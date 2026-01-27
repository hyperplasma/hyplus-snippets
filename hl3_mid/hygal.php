<?php
/**
 * Plugin Name: HyGal画廊 - 响应式单行版
 * Description: 将设置项合并至同一行，支持自动换行的响应式布局，纯白圆角输入框。
 * Shortcode: [hygal tags="霞,虹,雾,hyplus"]
 */

add_shortcode('hygal', 'hygal_minimalist_search_handler');

function hygal_minimalist_search_handler($atts) {
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));

    if (empty($tag_list)) {
        return '<p style="text-align:center;">提示：请设置分类参数，例如 [hygal tags="分类1,分类2"]</p>';
    }

    ob_start();
    ?>
    <style>
        .hygal-component-container { margin: 20px 0; text-align: center; }
        
        /* 合并后的单行容器：支持换行，间距对齐 */
        .hygal-filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;
            color: #2d3a4b;
            font-size: 16px;
            font-weight: 600;
        }

        /* 内部小组，用于让“显示 [ ] 项”在换行时保持不散开 */
        .hygal-filter-unit {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* 纯白圆角输入控件 */
        .hygal-input {
            background: #ffffff !important;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 16px;
            font-weight: 600;
            color: #2d3a4b;
            height: 40px;
            outline: none;
            transition: border-color 0.2s;
            vertical-align: middle;
        }
        .hygal-input:focus { border-color: #43a5f5; }

        .input-select { min-width: 130px; cursor: pointer; }
        .input-limit { width: 95px; text-align: center; }

        .hygal-btn-row { margin-top: 15px; display: flex; justify-content: center; }

        .hygal-btn-submit {
            height: 40px;
            padding: 0 45px !important;
            font-size: 16px !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #c4e0f7;
            border-radius: 6px;
        }

        /* 画廊网格 */
        .hygal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin-top: 25px;
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
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f7f8f9; }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        .hygal-title { padding: 6px 4px; font-size: 10px; color: #444; text-align: center; line-height: 1.3; word-wrap: break-word; }

        #hygal-loader-msg { display: none; font-size: 14px; color: #175082; margin: 15px 0; }
    </style>

    <div class="hygal-component-container">
        <div class="hyplus-nav-section">
            <div class="hygal-filter-container">
                <select id="f-category" class="hygal-input input-select">
                    <option value="">选择分类</option>
                    <?php foreach ($tag_list as $tag): ?>
                        <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="hygal-filter-unit">
                    <span>显示</span>
                    <input type="number" id="f-limit" class="hygal-input input-limit" placeholder="全部" min="1">
                    <span>项</span>
                </div>
                
                <select id="f-order" class="hygal-input input-select">
                    <option value="DESC">最新在前</option>
                    <option value="ASC">最早在前</option>
                    <option value="RAND">随机抽选</option>
                </select>
            </div>

            <div class="hygal-btn-row">
                <button id="btn-fetch" class="hyplus-nav-link hygal-btn-submit">展示图片</button>
            </div>
        </div>

        <div id="hygal-loader-msg">📡 正在搜索...</div>
        <div id="hygal-output" class="hygal-grid"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#btn-fetch').on('click', function() {
            const category = $('#f-category').val();
            if (!category) { alert('请先选择一个分类'); return; }

            const $btn = $(this);
            const $output = $('#hygal-output');
            const $loader = $('#hygal-loader-msg');

            $btn.prop('disabled', true).css('opacity', '0.6');
            $loader.show();
            $output.fadeOut(100);

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'hygal_fetch_minimal',
                    prefix: category,
                    limit: $('#f-limit').val(),
                    order: $('#f-order').val(),
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                },
                success: function(res) {
                    $loader.hide();
                    $btn.prop('disabled', false).css('opacity', '1');
                    if (res.success) {
                        $output.html(res.data).fadeIn(150);
                    } else {
                        $output.html('<p style="grid-column:1/-1; padding:40px; color:#999;">暂无相关内容</p>').fadeIn(150);
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
 * 后端查询逻辑 - 保持不变
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
add_action('wp_ajax_nopriv_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');

function hygal_ajax_fetch_minimal_handler() {
    check_ajax_referer('hygal_min_nonce');
    global $wpdb;

    $prefix = sanitize_text_field($_POST['prefix']);
    $limit  = !empty($_POST['limit']) ? intval($_POST['limit']) : -1;
    $order  = sanitize_text_field($_POST['order']);

    $sql = "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit'";
    $args = [];

    $sql .= " AND post_title LIKE %s";
    $args[] = $wpdb->esc_like($prefix . '-') . '%';

    if ($order === 'RAND') {
        $sql .= " ORDER BY RAND()";
    } else {
        $sql .= " ORDER BY post_date " . ($order === 'ASC' ? 'ASC' : 'DESC');
    }

    if ($limit > 0) {
        $sql .= " LIMIT %d";
        $args[] = $limit;
    }

    $results = $wpdb->get_results($wpdb->prepare($sql, $args));

    if (empty($results)) { wp_send_json_error(); }

    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
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
?>