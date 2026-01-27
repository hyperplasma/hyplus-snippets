<?php
/**
 * Plugin Name: 高级WebP上传器 - HyGal 动态组件
 * Description: 动态画廊。直接调用全局 HyNav 样式，白底下拉菜单，极速异步渲染。
 * Usage: [hygal tags="霞,虹,雾"]
 */

add_shortcode('hygal', 'hygal_hynav_integration_handler');

function hygal_hynav_integration_handler($atts) {
    // 1. 解析短代码参数
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));

    if (empty($tag_list)) {
        return '<p style="text-align:center;">提示：请设置分类参数，例如 [hygal tags="分类1,分类2"]</p>';
    }

    ob_start();
    ?>
    <style>
        /* 仅保留画廊布局逻辑，视觉样式调用全局 CSS */
        .hygal-component-container {
            margin: 20px 0;
            text-align: center;
        }

        /* 下拉菜单：普通白底样式，增大字号 */
        .hygal-native-select {
            background: #ffffff;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 16px;
            font-weight: 600;
            color: #2d3a4b;
            margin: 0 5px;
            cursor: pointer;
            height: 38px;
            vertical-align: middle;
        }

        /* 画廊网格布局 */
        .hygal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .hygal-item {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #eef0f2;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .hygal-img-wrapper {
            width: 100%;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #f7f8f9;
        }
        .hygal-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            margin: 0 !important;
        }
        .hygal-title {
            padding: 6px 4px;
            font-size: 10px;
            color: #444;
            text-align: center;
            line-height: 1.3;
            word-wrap: break-word;
        }

        #hygal-loader-msg { display: none; font-size: 14px; color: #175082; margin: 10px 0; }
        
        /* 强制让按钮在组件内对齐 */
        .hygal-btn-fix {
            /* border-radius: 6px !important; */
            height: 38px;
            padding: 0 20px !important;
            font-size: 16px !important;
            cursor: pointer;
            /* border: none; */
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }
    </style>

    <div class="hygal-component-container">
        <div class="hyplus-nav-section">
            <div class="hyplus-nav-group" style="border:none; padding:0; margin:0;">
                <select id="sel-category" class="hygal-native-select">
                    <option value="">选择分类</option>
                    <?php foreach ($tag_list as $tag): ?>
                        <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="sel-limit" class="hygal-native-select">
                    <option value="12">显示12张</option>
                    <option value="24">显示24张</option>
                    <option value="48">显示48张</option>
                    <option value="-1">显示全部</option>
                </select>

                <button id="btn-fetch" class="hyplus-nav-link hygal-btn-fix">展示图片</button>
            </div>
        </div>

        <div id="hygal-loader-msg">⚡ 正在调取数据...</div>

        <div id="hygal-output" class="hygal-grid"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#btn-fetch').on('click', function() {
            const category = $('#sel-category').val();
            const limit = $('#sel-limit').val();
            
            if (!category) {
                alert('请先选择一个分类');
                return;
            }

            const $btn = $(this);
            const $loader = $('#hygal-loader-msg');
            const $output = $('#hygal-output');

            $btn.prop('disabled', true).css('opacity', '0.6');
            $loader.show();
            // 快速淡出旧内容
            $output.fadeOut(150);

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'hygal_fetch_action',
                    prefix: category,
                    limit: limit,
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_fast_nonce"); ?>'
                },
                success: function(res) {
                    $loader.hide();
                    $btn.prop('disabled', false).css('opacity', '1');
                    if (res.success) {
                        // 快速淡入新内容 (200ms)
                        $output.html(res.data).fadeIn(200);
                    } else {
                        $output.html('<p style="grid-column:1/-1; padding:30px; color:#999;">暂无匹配记录</p>').fadeIn(200);
                    }
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端搜索逻辑
 */
add_action('wp_ajax_hygal_fetch_action', 'hygal_ajax_fetch_handler');
add_action('wp_ajax_nopriv_hygal_fetch_action', 'hygal_ajax_fetch_handler');

function hygal_ajax_fetch_handler() {
    check_ajax_referer('hygal_fast_nonce');

    $prefix = sanitize_text_field($_POST['prefix']);
    $limit = intval($_POST['limit']);

    global $wpdb;
    $prefix_like = $wpdb->esc_like($prefix . '-') . '%';
    
    $query = "SELECT ID, post_title FROM {$wpdb->posts} 
              WHERE post_type = 'attachment' 
              AND post_title LIKE %s 
              ORDER BY post_date DESC";
    
    if ($limit !== -1) { $query .= " LIMIT " . $limit; }

    $results = $wpdb->get_results($wpdb->prepare($query, $prefix_like));

    if (empty($results)) { wp_send_json_error(); }

    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
        // 抹除前缀只留内容
        if (strpos($display_title, $prefix . '-') === 0) {
            $display_title = substr($display_title, strlen($prefix . '-'));
        }

        $html .= '<div class="hygal-item">';
        $html .= '  <div class="hygal-img-wrapper">';
        $html .= '    <img src="' . esc_url($url) . '" loading="lazy" title="' . esc_attr($display_title) . '">';
        $html .= '  </div>';
        $html .= '  <div class="hygal-title">' . esc_html($display_title) . '</div>';
        $html .= '</div>';
    }

    wp_send_json_success($html);
}