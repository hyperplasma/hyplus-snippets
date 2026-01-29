<?php
/**
 * Plugin Name: HyGal画廊 - 极致视觉净化版
 * Description: 去除底部翻页器分割线，完善管理员提示逻辑，权重数值大者优先，新增10项/页选项。
 */

add_shortcode('hygal', 'hygal_minimalist_search_handler');

function hygal_minimalist_search_handler($atts) {
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));
    $is_admin = current_user_can('manage_options') ? 'true' : 'false';

    if (empty($tag_list)) return '<p style="text-align:center;">提示：请设置参数 [hygal tags="分类1,分类2"]</p>';

    ob_start();
    ?>
    <style>
        .hygal-component-container { margin: 20px 0; text-align: center; display: flex; flex-direction: column; }
        .hygal-filter-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; }
        .hygal-input { background: #ffffff !important; border: 1px solid #cbd5e0; border-radius: 6px; padding: 0 12px; font-size: 16px; font-weight: 600; color: #2d3a4b; height: 40px; outline: none; }
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; font-weight: 600; }
        
        .hygal-status-bar { display: none; grid-template-columns: 1fr auto 1fr; align-items: center; height: 36px; margin: 10px 0; padding: 0 5px; font-size: 14px; color: #475569; }
        
        .bar-bottom { margin-top: 15px; border-top: none !important; padding-top: 0 !important; }

        .status-left { text-align: left; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .status-center { font-weight: 600; min-width: 120px; display: flex; justify-content: center; }
        .hygal-pager { align-items: center; gap: 5px; display: none; }
        .pager-btn { cursor: pointer; padding: 0 8px; font-size: 18px; color: #43a5f5; user-select: none; }
        .pager-btn.disabled { opacity: 0.2; cursor: default; color: #94a3b8; }
        .status-right { text-align: right; display: flex; justify-content: flex-end; }
        .close-btn { cursor: pointer; color: #f87171; font-size: 26px; visibility: hidden; width: 24px; }

        #hygal-output { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; }
        #hygal-output.loading { height: 0; min-height: 0; overflow: hidden; opacity: 0; }

        .hygal-item { display: flex; flex-direction: column; background: #fff; border-radius: 4px; border: 1px solid #eef0f2; position: relative; }
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f7f8f9; }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        
        .hygal-title { 
            padding: 4px 2px !important; font-size: 13px !important; color: #666 !important; 
            text-align: center; line-height: 1.1 !important; word-wrap: break-word; 
            letter-spacing: -0.3px; cursor: default;
        }
        .is-admin .hygal-title { cursor: pointer; }

        .order-badge { 
            position: absolute; top: 2px; left: 2px; 
            background: rgba(0,0,0,0.35); color: #fff; 
            font-size: 8px; padding: 0 4px; border-radius: 2px; 
            pointer-events: none; z-index: 5; 
        }

        .hytool-version { margin-top: auto; padding-top: 25px; color: #ccc; font-size: 13px; text-align: right; pointer-events: none; }
    </style>

    <div class="hygal-component-container hyplus-unselectable <?php echo ($is_admin === 'true') ? 'is-admin' : ''; ?>">
        <div class="hyplus-nav-section">
            <div class="hygal-filter-container">
                <select id="f-category" class="hygal-input">
                    <?php foreach ($tag_list as $tag): ?>
                        <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="f-ppp" class="hygal-input">
                    <option value="60">60 项/页</option>
                    <option value="30">30 项/页</option>
                    <option value="10">10 项/页</option>
                </select>
                <select id="f-order" class="hygal-input">
                    <option value="DESC">最新优先</option>
                    <option value="ASC">最早优先</option>
                    <option value="RAND">随机排序</option>
                </select>
            </div>
            <div style="margin-top:15px; display:flex; justify-content:center;">
                <button id="btn-fetch" class="hyplus-nav-link hygal-btn-submit">展示图片</button>
            </div>
        </div>

        <div id="hygal-bar-top" class="hygal-status-bar">
            <div class="status-left info-text"></div>
            <div class="status-center">
                <div class="loading-text" style="display:none;">📡 正在获取...</div>
                <div class="hygal-pager">
                    <span class="pager-btn prev-btn">&lt;</span>
                    <span class="pager-text">1 / 1</span>
                    <span class="pager-btn next-btn">&gt;</span>
                </div>
            </div>
            <div class="status-right"><div class="close-btn">&times;</div></div>
        </div>

        <div id="hygal-output" class="loading"></div>

        <div id="hygal-bar-bottom" class="hygal-status-bar bar-bottom">
            <div class="status-left"></div>
            <div class="status-center">
                <div class="hygal-pager">
                    <span class="pager-btn prev-btn">&lt;</span>
                    <span class="pager-text">1 / 1</span>
                    <span class="pager-btn next-btn">&gt;</span>
                </div>
            </div>
            <div class="status-right"></div>
        </div>

        <div class="hytool-version">HyGal v0.5.3</div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const isAdmin = <?php echo $is_admin; ?>;
        let currentPage = 1, totalPages = 1;
        const $bars = $('.hygal-status-bar'), $output = $('#hygal-output'), $loadingText = $('.loading-text'),
              $pagers = $('.hygal-pager'), $pagerTexts = $('.pager-text'), $infoText = $('.info-text');

        function fetchImages(page = 1, isSwitching = false) {
            currentPage = page;
            $output.stop().animate({opacity: 0}, 80, function() { $(this).addClass('loading'); });
            if (isSwitching) $infoText.empty();
            $bars.css('display', 'grid');
            $pagers.hide();
            $('.close-btn').css('visibility', 'hidden');
            $loadingText.show();

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
                    $loadingText.hide();
                    if (res.success) {
                        const data = res.data;
                        $infoText.html('<b>' + $('#f-category').val() + '</b><span style="color:#64748b;margin-left:4px;">(' + data.total_items + ')</span>');
                        totalPages = data.total_pages || 1;
                        $pagerTexts.text(currentPage + ' / ' + totalPages);
                        $('.prev-btn').toggleClass('disabled', currentPage <= 1);
                        $('.next-btn').toggleClass('disabled', currentPage >= totalPages);
                        $pagers.css('display', 'flex');
                        $('.close-btn').css('visibility', 'visible');
                        $output.removeClass('loading').html(data.html).stop().css('opacity', 1).hide().fadeIn(80);
                        if(!isSwitching) {
                            $('html, body').animate({ scrollTop: $('#hygal-bar-top').offset().top - 80 }, 300);
                        }
                    }
                }
            });
        }

        $('#btn-fetch').on('click', () => fetchImages(1, true));
        $('.prev-btn').on('click', function() { if (currentPage > 1) fetchImages(currentPage - 1, false); });
        $('.next-btn').on('click', function() { if (currentPage < totalPages) fetchImages(currentPage + 1, false); });
        $('.close-btn').on('click', function() { $output.fadeOut(80, function() { $(this).empty().addClass('loading'); }); $bars.fadeOut(80); });

        if (isAdmin) {
            $output.on('click', '.hygal-title', function() {
                const $item = $(this).closest('.hygal-item');
                const imgId = $item.data('id');
                let rawOrder = $item.attr('data-raw-order'); 
                const displayOrder = (!rawOrder || rawOrder.trim() === '') ? '无' : rawOrder;
                
                const newOrder = prompt("设置权重评分（数值越大越靠前，留空取消权重）\n当前评分: " + displayOrder, (displayOrder === '无' ? '' : rawOrder));
                
                if (newOrder !== null) {
                    $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: 'POST',
                        data: { action: 'hygal_update_order', img_id: imgId, order_val: newOrder, _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>' },
                        success: function(res) { if(res.success) fetchImages(currentPage, false); }
                    });
                }
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端查询逻辑
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
add_action('wp_ajax_nopriv_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');

function hygal_ajax_fetch_minimal_handler() {
    check_ajax_referer('hygal_min_nonce');
    global $wpdb;

    $prefix = sanitize_text_field($_POST['prefix']);
    $ppp    = intval($_POST['ppp']);
    $paged  = intval($_POST['paged']);
    $order_type = $_POST['order'];
    $offset = ($paged - 1) * $ppp;

    $sql_where = $wpdb->prepare("
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} m_cat ON p.ID = m_cat.post_id AND m_cat.meta_key = '_hygal_category'
        LEFT JOIN {$wpdb->postmeta} m_ord ON p.ID = m_ord.post_id AND m_ord.meta_key = '_hygal_order'
        WHERE p.post_type = 'attachment' AND m_cat.meta_value = %s
    ", $prefix);

    if ($order_type === 'RAND') {
        $orderby = "ORDER BY RAND()";
    } else {
        $time_order = ($order_type === 'ASC') ? 'ASC' : 'DESC';
        $orderby = "ORDER BY 
            CASE WHEN m_ord.meta_value IS NULL OR m_ord.meta_value = '' THEN 1 ELSE 0 END ASC,
            CAST(m_ord.meta_value AS SIGNED) DESC, 
            p.post_date $time_order";
    }

    $sql = "SELECT p.ID, p.post_title, m_ord.meta_value as raw_order " . $sql_where . $orderby . $wpdb->prepare(" LIMIT %d, %d", $offset, $ppp);
    $total_items = $wpdb->get_var("SELECT COUNT(*) " . $sql_where);
    $results = $wpdb->get_results($sql);

    $logic_counter = $offset + 1;
    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $raw_order = $post->raw_order;
        $display_title = $post->post_title;
        if (strpos($display_title, $prefix . '-') === 0) $display_title = substr($display_title, strlen($prefix) + 1);
        
        $badge = ($raw_order !== '' && $raw_order !== null) ? '<div class="order-badge">#'.$logic_counter.'</div>' : '';
        if ($raw_order !== '' && $raw_order !== null) $logic_counter++;

        $html .= '<div class="hygal-item" data-id="'.$post->ID.'" data-raw-order="'.esc_attr($raw_order).'">
                    '.$badge.'
                    <div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy"></div>
                    <div class="hygal-title">'.esc_html($display_title).'</div>
                  </div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => (int)$total_items, 'total_pages' => ceil($total_items / $ppp)]);
}

add_action('wp_ajax_hygal_update_order', 'hygal_ajax_update_order_handler');
function hygal_ajax_update_order_handler() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) wp_send_json_error();
    $val = sanitize_text_field($_POST['order_val']);
    $img_id = intval($_POST['img_id']);
    if ($val === '') delete_post_meta($img_id, '_hygal_order');
    else update_post_meta($img_id, '_hygal_order', intval($val));
    wp_send_json_success();
}
?>