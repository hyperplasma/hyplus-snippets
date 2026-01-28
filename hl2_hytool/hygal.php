<?php
/**
 * Plugin Name: HyGal画廊 - 视觉稳定版 (纯净版)
 * Description: 基于 _hygal_category 索引的高速查询，移除耗时显示，优化界面纯净度。
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
        .hygal-component-container { margin: 20px 0; text-align: center; display: flex; flex-direction: column; }
        .hygal-filter-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; color: #2d3a4b; font-size: 16px; font-weight: 600; }
        .hygal-input { background: #ffffff !important; border: 1px solid #cbd5e0; border-radius: 6px; padding: 0 12px; font-size: 16px; font-weight: 600; color: #2d3a4b; height: 40px; outline: none; transition: border-color 0.2s; }
        .hygal-input:focus { border-color: #43a5f5; }
        .input-select { min-width: 130px; cursor: pointer; }
        .input-limit { width: 95px; text-align: center; }
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; display: inline-flex; align-items: center; font-weight: 600; }
        .hygal-btn-submit:hover { background: #f0f9ff; }

        /* 状态栏布局：回归极简 */
        #hygal-status-bar { 
            display: none; 
            align-items: center;
            justify-content: space-between;
            height: 30px; 
            margin: 10px 0;
            padding: 0 5px;
            font-size: 14px;
            color: #64748b;
        }
        #hygal-status-text { flex: 1; text-align: center; font-weight: 600; }
        
        #hygal-close-btn { 
            cursor: pointer; color: #f87171; font-size: 24px; line-height: 1; width: 24px; transition: color 0.1s; 
        }
        #hygal-close-btn:hover { color: #ef4444; }
        .status-left-spacer { width: 24px; }

        #hygal-output {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; transition: opacity 0.1s;
        }
        .hygal-item { display: flex; flex-direction: column; background: #fff; border-radius: 4px; overflow: hidden; border: 1px solid #eef0f2; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f7f8f9; }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        .hygal-title { padding: 6px 4px; font-size: 10px; color: #444; text-align: center; line-height: 1.3; word-wrap: break-word; }

        .hytool-version { margin-top: auto; padding-top: 20px; color: #ccc; font-size: 14px; text-align: right; width: 100%; pointer-events: none; }
    </style>

    <div class="hygal-component-container">
        <div class="hyplus-nav-section">
            <div class="hygal-filter-container">
                <select id="f-category" class="hygal-input input-select hyplus-unselectable">
                    <?php foreach ($tag_list as $tag): ?>
                        <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="hyplus-unselectable">显示</span>
                    <input type="number" id="f-limit" class="hygal-input input-limit" placeholder="全部" min="1">
                    <span class="hyplus-unselectable">项</span>
                </div>
                <select id="f-order" class="hygal-input input-select hyplus-unselectable">
                    <option value="DESC">最新在前</option>
                    <option value="ASC">最早在前</option>
                    <option value="RAND">随机抽选</option>
                </select>
            </div>
            <div style="margin-top:15px; display:flex; justify-content:center;">
                <button id="btn-fetch" class="hyplus-nav-link hygal-btn-submit hyplus-unselectable">展示图片</button>
            </div>
        </div>

        <div id="hygal-status-bar" class="hyplus-unselectable">
            <div class="status-left-spacer"></div>
            <div id="hygal-status-text">📡 正在检索...</div>
            <div id="hygal-close-btn" title="清空内容">&times;</div>
        </div>

        <div id="hygal-output" class="hygal-grid hyplus-unselectable"></div>
        <div class="hytool-version hyplus-unselectable">HyGal v0.2.2</div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const $btn = $('#btn-fetch');
        const $output = $('#hygal-output');
        const $statusBar = $('#hygal-status-bar');
        const $statusText = $('#hygal-status-text');
        const $closeBtn = $('#hygal-close-btn');

        $btn.on('click', function() {
            $btn.prop('disabled', true).css('opacity', '0.6');
            $output.stop().animate({opacity: 0}, 80);
            
            $statusText.text('📡 正在检索...');
            $closeBtn.hide(); 
            $statusBar.css('display', 'flex').hide().fadeIn(80);

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'hygal_fetch_minimal',
                    prefix: $('#f-category').val(),
                    limit: $('#f-limit').val(),
                    order: $('#f-order').val(),
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                },
                success: function(res) {
                    $btn.prop('disabled', false).css('opacity', '1');
                    if (res.success) {
                        $statusText.text('✨ 共 ' + res.data.count + ' 项');
                        $closeBtn.show(); 
                        $output.html(res.data.html).stop().css('opacity', 1).hide().fadeIn(80);
                    } else {
                        $statusText.text('❌ 未找到内容');
                        $closeBtn.show();
                        $output.empty().css('opacity', 1);
                    }
                }
            });
        });

        $closeBtn.on('click', function() {
            $output.stop().fadeOut(80, function() { $(this).empty(); });
            $statusBar.stop().fadeOut(80);
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端逻辑：Meta Query 高速查询 (生产优化版)
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
add_action('wp_ajax_nopriv_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');

function hygal_ajax_fetch_minimal_handler() {
    check_ajax_referer('hygal_min_nonce');
    
    $prefix = sanitize_text_field($_POST['prefix']);
    $limit  = !empty($_POST['limit']) ? intval($_POST['limit']) : -1;
    $order  = sanitize_text_field($_POST['order']);

    $query_args = [
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => $limit,
        'meta_key'       => '_hygal_category',
        'meta_value'     => $prefix,
        'meta_compare'   => '=',
        'no_found_rows'  => false, 
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    ];

    if ($order === 'RAND') {
        $query_args['orderby'] = 'rand';
    } else {
        $query_args['orderby'] = 'date';
        $query_args['order']   = $order;
    }

    $query = new WP_Query($query_args);
    
    if (!$query->have_posts()) {
        wp_send_json_error();
    }

    $count = $query->found_posts;
    $html = '';
    
    foreach ($query->posts as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
        $prefix_str = $prefix . '-';
        if (strpos($display_title, $prefix_str) === 0) {
            $display_title = substr($display_title, strlen($prefix_str));
        }

        $html .= '<div class="hygal-item">';
        $html .= '<div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy" title="'.esc_attr($display_title).'"></div>';
        $html .= '<div class="hygal-title">'.esc_html($display_title).'</div>';
        $html .= '</div>';
    }

    wp_send_json_success(['html' => $html, 'count' => $count]);
}
?>