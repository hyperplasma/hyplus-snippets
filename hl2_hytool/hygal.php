<?php
/**
 * Plugin Name: HyGal画廊 - 交互最终稳定版
 * Description: 修复分页控件失效问题，保持 Grid 绝对居中与视觉稳定。
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
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; display: inline-flex; align-items: center; font-weight: 600; }
        
        /* 状态栏：三列等宽布局确保绝对居中 */
        #hygal-status-bar { 
            display: none; 
            grid-template-columns: 1fr auto 1fr; 
            align-items: center;
            height: 36px; 
            margin: 10px 0;
            padding: 0 5px;
            font-size: 14px;
            color: #475569;
        }

        .status-left { text-align: left; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .status-left b { color: #1e293b; font-weight: 700; }
        .status-count { color: #64748b; margin-left: 4px; font-weight: 600; }

        .status-center { font-weight: 600; min-width: 120px; display: flex; justify-content: center; align-items: center; position: relative; }
        
        /* 分页控件样式 */
        #hygal-pager { align-items: center; gap: 12px; display: none; }
        .pager-btn { cursor: pointer; padding: 0 8px; font-size: 18px; color: #43a5f5; user-select: none; }
        .pager-btn.disabled { opacity: 0.2; cursor: default; color: #94a3b8; }

        .status-right { text-align: right; display: flex; justify-content: flex-end; }
        #hygal-close-btn { cursor: pointer; color: #f87171; font-size: 26px; line-height: 1; width: 24px; visibility: hidden; }

        #hygal-output { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; transition: opacity 0.1s; }
        #hygal-output.loading { height: 0; min-height: 0; overflow: hidden; }

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
                <select id="f-ppp" class="hygal-input input-select hyplus-unselectable">
                    <option value="60">每页 60 项</option>
                    <option value="30">每页 30 项</option>
                </select>
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
            <div class="status-left" id="hygal-info"></div>
            
            <div class="status-center">
                <div id="hygal-loading-text" style="display:none;">📡 正在获取...</div>
                <div id="hygal-pager">
                    <span class="pager-btn" id="pager-prev">&lt;</span>
                    <span id="pager-text">1 / 1</span>
                    <span class="pager-btn" id="pager-next">&gt;</span>
                </div>
            </div>

            <div class="status-right">
                <div id="hygal-close-btn" title="清空内容">&times;</div>
            </div>
        </div>

        <div id="hygal-output" class="hygal-grid hyplus-unselectable loading"></div>
        <div class="hytool-version hyplus-unselectable">HyGal v0.3.7</div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        let currentPage = 1;
        let totalPages = 1;

        const $btn = $('#btn-fetch');
        const $output = $('#hygal-output');
        const $statusBar = $('#hygal-status-bar');
        const $info = $('#hygal-info');
        const $loadingText = $('#hygal-loading-text');
        const $pager = $('#hygal-pager');
        const $closeBtn = $('#hygal-close-btn');

        function fetchImages(page = 1, isSwitching = false) {
            currentPage = page;
            $btn.prop('disabled', true).css('opacity', '0.6');
            
            // 1. 隐藏内容区域
            $output.stop().animate({opacity: 0}, 80, function() { $(this).addClass('loading'); });
            
            // 2. 状态栏显示逻辑
            if (isSwitching) { $info.empty(); } // 换分类才清空左边
            
            $statusBar.css('display', 'grid');
            $closeBtn.css('visibility', 'hidden'); // 隐藏红叉
            
            $pager.hide(); // 隐藏分页控件
            $loadingText.text('📡 正在获取...').css('color', '').show(); // 显示正在获取

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'hygal_fetch_minimal',
                    prefix: $('#f-category').val(),
                    ppp: $('#f-ppp').val(),
                    paged: currentPage,
                    order: $('#f-order').val(),
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                },
                success: function(res) {
                    $btn.prop('disabled', false).css('opacity', '1');
                    $loadingText.hide();
                    
                    if (res.success) {
                        const data = res.data;
                        const catName = $('#f-category').val();
                        
                        // 更新左侧
                        $info.html('<b>' + catName + '</b><span class="status-count">(' + data.total_items + ')</span>');
                        
                        // 更新分页控件状态
                        totalPages = data.total_pages || 1;
                        $('#pager-text').text(currentPage + ' / ' + totalPages);
                        $('#pager-prev').toggleClass('disabled', currentPage <= 1);
                        $('#pager-next').toggleClass('disabled', currentPage >= totalPages);
                        
                        $pager.css('display', 'flex');
                        $closeBtn.css('visibility', 'visible');
                        $output.removeClass('loading').html(data.html).stop().css('opacity', 1).hide().fadeIn(80);
                    } else {
                        $loadingText.text('❌ 未找到内容').css('color', '#ef4444').show();
                        $closeBtn.css('visibility', 'visible');
                        $output.empty().addClass('loading');
                    }
                }
            });
        }

        // 按钮点击事件（只需绑定一次）
        $btn.on('click', () => fetchImages(1, true));
        
        $('#pager-prev').on('click', function() { 
            if (currentPage > 1 && !$(this).hasClass('disabled')) fetchImages(currentPage - 1, false); 
        });
        
        $('#pager-next').on('click', function() { 
            if (currentPage < totalPages && !$(this).hasClass('disabled')) fetchImages(currentPage + 1, false); 
        });
        
        $closeBtn.on('click', function() {
            $output.stop().fadeOut(80, function() { $(this).empty().addClass('loading'); });
            $statusBar.fadeOut(80);
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端查询保持不变
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
add_action('wp_ajax_nopriv_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
function hygal_ajax_fetch_minimal_handler() {
    check_ajax_referer('hygal_min_nonce');
    $prefix = sanitize_text_field($_POST['prefix']);
    $ppp    = intval($_POST['ppp']);
    $paged  = intval($_POST['paged']);
    $order  = sanitize_text_field($_POST['order']);

    $query_args = [
        'post_type' => 'attachment', 'post_status' => 'inherit',
        'posts_per_page' => $ppp, 'paged' => $paged,
        'meta_key' => '_hygal_category', 'meta_value' => $prefix, 'meta_compare' => '=',
    ];
    if ($order === 'RAND') { $query_args['orderby'] = 'rand'; } 
    else { $query_args['orderby'] = 'date'; $query_args['order'] = $order; }

    $query = new WP_Query($query_args);
    if (!$query->have_posts()) { wp_send_json_error(); }

    $total_items = $query->found_posts;
    $total_pages = ceil($total_items / $ppp);
    $html = '';
    foreach ($query->posts as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
        $prefix_str = $prefix . '-';
        if (strpos($display_title, $prefix_str) === 0) { $display_title = substr($display_title, strlen($prefix_str)); }
        $html .= '<div class="hygal-item"><div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy" title="'.esc_attr($display_title).'"></div><div class="hygal-title">'.esc_html($display_title).'</div></div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => $total_items, 'total_pages' => $total_pages]);
}
?>