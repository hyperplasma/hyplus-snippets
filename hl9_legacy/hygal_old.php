<?php
/**
 * Plugin Name: HyGal画廊 (Old) - 极致视觉净化版
 * Description: 空间优化版：压缩翻页控件间距，恢复完整加载文案，支持修改权重/分类/标题，Esc/外部关闭。
 * Current status: unused (combined to new HyGal)
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
        /* 基础容器 */
        .hygal-component-container { margin: 20px 0; text-align: center; display: flex; flex-direction: column; }
        .hyplus-unselectable { -webkit-user-select: none; user-select: none; }
        
        /* 筛选器 */
        .hygal-filter-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; }
        .hygal-input { background: #ffffff !important; border: 1px solid #cbd5e0; border-radius: 6px; padding: 0 12px; font-size: 16px; font-weight: 600; color: #2d3a4b; height: 40px; outline: none; }
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; font-weight: 600; }
        
        /* 状态条 */
        .hygal-status-bar { display: none; grid-template-columns: 1fr auto 1fr; align-items: center; height: 40px; margin: 10px 0; padding: 0 10px; font-size: 14px; color: #475569; }
        .bar-bottom { margin-top: 15px; border-top: none !important; padding-top: 0 !important; }
        
        /* 左侧：分类信息 */
        .status-left { text-align: left; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; padding-right: 5px; }
        
        /* 中间：翻页控件瘦身，为左侧留空间 */
        .status-center { font-weight: 600; min-width: 80px; display: flex; justify-content: center; }
        .hygal-pager { align-items: center; gap: 2px; display: none; } 
        .pager-btn { cursor: pointer; padding: 0 4px; font-size: 18px; color: #43a5f5; } 
        .pager-btn.disabled { opacity: 0.2; cursor: default; color: #94a3b8; }
        /* .pager-text { font-size: 13px; } */

        /* 右侧：关闭按钮 */
        .status-right { text-align: right; display: flex; justify-content: flex-end; align-items: center; min-width: 30px; }
        .close-btn { 
            cursor: pointer; color: #ef4444 !important; font-size: 28px !important; 
            line-height: 1; visibility: hidden; transition: transform 0.2s; 
            padding: 2px; font-family: Arial, sans-serif; font-weight: bold;
        }
        .close-btn:hover { transform: scale(1.2); color: #b91c1c !important; }

        /* 网格输出 */
        #hygal-output { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; }
        #hygal-output.loading { height: 0; min-height: 0; overflow: hidden; opacity: 0; }
        .hygal-item { display: flex; flex-direction: column; background: #fff; border-radius: 4px; border: 1px solid #eef0f2; position: relative; overflow: hidden; }
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f7f8f9; }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        .hygal-title { padding: 5px 2px !important; font-size: 12px !important; color: #666 !important; text-align: center; line-height: 1.2 !important; word-wrap: break-word; letter-spacing: -0.3px; cursor: default; transition: background 0.2s; }
        .is-admin .hygal-title { cursor: pointer; }
        .hygal-item.has-order .hygal-title { background-color: #f4fbfc !important; color: #00626b !important; }
        .hytool-version { margin-top: auto; padding-top: 25px; color: #ccc; font-size: 13px; text-align: right; pointer-events: none; }

        /* 管理员弹窗 UI */
        #hygal-admin-modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: 99999; justify-content: center; align-items: center;
            background: rgba(0,0,0,0.25);
        }
        .hy-modal-content {
            background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 12px; width: 90%; max-width: 400px;
            box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5); text-align: left;
        }
        .hy-modal-label { display: block; font-size: 13px; color: #666; font-weight: 600; }
        .hy-modal-input { width: 100%; margin-top: 6px; margin-bottom: 12px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .hy-modal-btns { margin-top: 6px; display: flex; gap: 10px; }
        .hy-btn { flex: 1; padding: 10px; cursor: pointer; font-weight: 600; }
        .hygal-no-scroll { overflow: hidden !important; width: 100%; }
    </style>

    <div id="hygal-admin-modal" class="hyplus-unselectable">
        <div class="hy-modal-content">
            <label class="hy-modal-label">权重评分 (数值越大越靠前)</label>
            <input type="number" id="mod-order" class="hy-modal-input" placeholder="无">
            
            <label class="hy-modal-label">所属分类</label>
            <select id="mod-prefix" class="hy-modal-input">
                <?php foreach ($tag_list as $tag): ?>
                    <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                <?php endforeach; ?>
            </select>
            
            <label class="hy-modal-label">标题</label>
            <input type="text" id="mod-title" class="hy-modal-input">
            
            <div class="hy-modal-btns">
                <button class="hyplus-nav-link hy-btn hy-btn-cancel" onclick="closeHyModal()">取消</button>
                <button class="hyplus-nav-link hy-btn hy-btn-save" id="hy-save-trigger">保存修改</button>
            </div>
        </div>
    </div>

    <div class="hygal-component-container hyplus-unselectable <?php echo ($is_admin === 'true') ? 'is-admin' : ''; ?>">
        <div class="hyplus-nav-section">
            <div class="hygal-filter-container">
                <select id="f-category" class="hygal-input">
                    <?php foreach ($tag_list as $tag): ?>
                        <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="f-ppp" class="hygal-input"><option value="10">10项/页</option><option value="30">30项/页</option><option value="60">60项/页</option><option value="1">1项/页</option></select>
                <select id="f-order" class="hygal-input"><option value="DESC">最新优先</option><option value="ASC">最早优先</option><option value="RAND">随机排序</option></select>
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
                    <span class="pager-btn prev-btn">&lt;</span><span class="pager-text">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                </div>
            </div>
            <div class="status-right"><div class="close-btn">&times;</div></div>
        </div>

        <div id="hygal-output" class="loading"></div>

        <div id="hygal-bar-bottom" class="hygal-status-bar bar-bottom">
            <div class="status-left"></div>
            <div class="status-center">
                <div class="hygal-pager">
                    <span class="pager-btn prev-btn">&lt;</span><span class="pager-text">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                </div>
            </div>
            <div class="status-right"></div>
        </div>
        <div class="hytool-version">HyGal v0.6.5</div>
    </div>

    <script>
    function closeHyModal() { 
        jQuery('#hygal-admin-modal').css('display', 'none'); 
        jQuery('body').removeClass('hygal-no-scroll');
    }

    jQuery(document).ready(function($) {
        const isAdmin = <?php echo $is_admin; ?>;
        let currentPage = 1, totalPages = 1, currentTargetId = null;

        function fetchImages(page = 1, isSwitching = false) {
            currentPage = page;
            $('#hygal-output').stop().animate({opacity: 0}, 80, function() { $(this).addClass('loading'); });
            $('.hygal-status-bar').css('display', 'grid');
            $('.loading-text').show(); $('.hygal-pager').hide();
            $('.close-btn').css('visibility', 'hidden');

            $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                action: 'hygal_fetch_minimal',
                prefix: $('#f-category').val(),
                ppp: $('#f-ppp').val(),
                paged: currentPage,
                order: $('#f-order').val(),
                _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
            }, function(res) {
                $('.loading-text').hide();
                if (res.success) {
                    const data = res.data;
                    $('.info-text').html('<b>' + $('#f-category').val() + '</b><span style="color:#64748b;margin-left:4px;">(' + data.total_items + ')</span>');
                    totalPages = data.total_pages || 1;
                    $('.pager-text').text(currentPage + ' / ' + totalPages);
                    $('.prev-btn').toggleClass('disabled', currentPage <= 1);
                    $('.next-btn').toggleClass('disabled', currentPage >= totalPages);
                    $('.hygal-pager').css('display', 'flex'); 
                    $('.close-btn').css('visibility', 'visible');
                    $('#hygal-output').removeClass('loading').html(data.html).stop().css('opacity', 1).hide().fadeIn(80);
                    if(!isSwitching) $('html, body').animate({ scrollTop: $('#hygal-bar-top').offset().top - 80 }, 300);
                }
            });
        }

        $('#btn-fetch').on('click', () => fetchImages(1, true));
        $('.prev-btn').on('click', () => currentPage > 1 && fetchImages(currentPage - 1));
        $('.next-btn').on('click', () => currentPage < totalPages && fetchImages(currentPage + 1));
        $('.close-btn').on('click', function() { 
            $('#hygal-output').fadeOut(80, function() { $(this).empty().addClass('loading'); }); 
            $('.hygal-status-bar').fadeOut(80); 
        });

        if (isAdmin) {
            $('#hygal-output').on('click', '.hygal-title', function() {
                const $item = $(this).closest('.hygal-item');
                currentTargetId = $item.data('id');
                $('#mod-order').val($item.attr('data-raw-order'));
                $('#mod-prefix').val($item.attr('data-current-prefix'));
                $('#mod-title').val($(this).text());
                $('#hygal-admin-modal').css('display', 'flex');
                $('body').addClass('hygal-no-scroll');
            });

            $('#hygal-admin-modal').on('click', function(e) { if (e.target === this) closeHyModal(); });
            $(document).on('keydown', function(e) { if (e.key === "Escape" && $('#hygal-admin-modal').is(':visible')) closeHyModal(); });

            const submitAssetUpdate = () => {
                const btn = $('#hy-save-trigger');
                if (btn.prop('disabled')) return;
                btn.prop('disabled', true).text('同步中...');
                $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                    action: 'hygal_update_asset',
                    img_id: currentTargetId,
                    order_val: $('#mod-order').val(),
                    new_prefix: $('#mod-prefix').val(),
                    new_pure_title: $('#mod-title').val(),
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('保存修改');
                    if(res.success) { closeHyModal(); fetchImages(currentPage, false); }
                });
            };
            $('#hy-save-trigger').on('click', submitAssetUpdate);
            $('.hy-modal-input').on('keypress', function(e) { if (e.which === 13) submitAssetUpdate(); });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端逻辑：获取列表 (此处逻辑保持不变)
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
add_action('wp_ajax_nopriv_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');

function hygal_ajax_fetch_minimal_handler() {
    check_ajax_referer('hygal_min_nonce');
    global $wpdb;
    $prefix = sanitize_text_field($_POST['prefix']);
    $ppp = intval($_POST['ppp']);
    $paged = intval($_POST['paged']);
    $offset = ($paged - 1) * $ppp;
    $order_type = $_POST['order'];

    $sql_where = $wpdb->prepare("
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} m_cat ON p.ID = m_cat.post_id AND m_cat.meta_key = '_hygal_category'
        LEFT JOIN {$wpdb->postmeta} m_ord ON p.ID = m_ord.post_id AND m_ord.meta_key = '_hygal_order'
        WHERE p.post_type = 'attachment' AND m_cat.meta_value = %s
    ", $prefix);

    $orderby = ($order_type === 'RAND') ? "ORDER BY RAND()" : "ORDER BY CASE WHEN m_ord.meta_value IS NULL OR m_ord.meta_value = '' THEN 1 ELSE 0 END ASC, CAST(m_ord.meta_value AS SIGNED) DESC, p.post_date ".($order_type==='ASC'?'ASC':'DESC');

    $results = $wpdb->get_results("SELECT p.ID, p.post_title, m_ord.meta_value as raw_order " . $sql_where . $orderby . $wpdb->prepare(" LIMIT %d, %d", $offset, $ppp));
    $total_items = $wpdb->get_var("SELECT COUNT(*) " . $sql_where);

    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
        if (strpos($display_title, $prefix . '-') === 0) $display_title = substr($display_title, strlen($prefix) + 1);
        $has_order_class = ($post->raw_order !== '' && $post->raw_order !== null) ? 'has-order' : '';

        $html .= '<div class="hygal-item '.$has_order_class.'" data-id="'.$post->ID.'" data-raw-order="'.esc_attr($post->raw_order).'" data-current-prefix="'.esc_attr($prefix).'">
                    <div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy"></div>
                    <div class="hygal-title">'.esc_html($display_title).'</div>
                  </div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => (int)$total_items, 'total_pages' => ceil($total_items / $ppp)]);
}

/**
 * 后端逻辑：同步更新
 */
add_action('wp_ajax_hygal_update_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('权限不足');
    $img_id = intval($_POST['img_id']);
    $order_val = sanitize_text_field($_POST['order_val']);
    $new_prefix = sanitize_text_field($_POST['new_prefix']);
    $new_pure_title = sanitize_text_field($_POST['new_pure_title']);
    if ($order_val === '') delete_post_meta($img_id, '_hygal_order');
    else update_post_meta($img_id, '_hygal_order', intval($order_val));
    $full_new_title = $new_prefix . '-' . $new_pure_title;
    wp_update_post(['ID' => $img_id, 'post_title' => $full_new_title]);
    update_post_meta($img_id, '_hygal_category', $new_prefix);
    wp_send_json_success();
});
?>