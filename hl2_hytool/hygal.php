<?php
/**
 * Plugin Name: HyGal 极致画廊 (Dual Pager Edition)
 * Description: 集成上传、管理、上下双翻页组件、批量下载功能。修复了顶部翻页丢失问题。
 * Version: 1.4.2
 */

add_shortcode('hygal', 'hygal_unified_handler');

function hygal_unified_handler($atts) {
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));
    
    if (empty($tag_list)) { $tag_list_for_upload = ['图']; } 
    else { $tag_list_for_upload = $tag_list; }

    $can_upload = current_user_can('upload_files');
    $is_admin_manage = current_user_can('manage_options') ? 'true' : 'false';

    if (empty($tag_list) && $is_admin_manage === 'false') {
        return '<p style="text-align:center;color:#666;">提示：请设置参数 [hygal tags="分类1,分类2"]</p>';
    }

    ob_start();
    ?>
    <style>
        /* 基础与上传样式 */
        .hyupload-container { margin: 0 0 20px 0; text-align: center; font-family: -apple-system, sans-serif; }
        #hyupload-drop-zone { border: 2px dashed #cbd5e0; min-height: 100px; border-radius: 12px; background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; padding: 15px; padding-top: 25px; position: relative; }
        #hyupload-drop-zone:hover, #hyupload-drop-zone.hover { border-color: #43a5f5; background: #f0f9ff; }
        #hyupload-preview-img { max-height: 80px; border-radius: 6px; display: none; margin-right: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .hyupload-hint { color: #64748b; font-size: 15px; font-weight: 500; pointer-events: none; }
        #hyupload-stats { display: none; padding: 12px; margin-top: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; color: #166534; }
        .hyupload-stat-tag { font-weight: 700; color: #15803d; text-decoration: underline; margin: 0 4px; }
        .hyupload-row { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; margin-top: 15px; }
        .hyupload-input { background: #ffffff !important; border: 1px solid #cbd5e0; border-radius: 6px; padding: 0 12px; font-size: 15px; font-weight: 600; color: #2d3a4b; height: 40px; outline: none; }
        #hyupload-prefix { min-width: 100px; cursor: pointer; }
        #hyupload-title { flex: 1; min-width: 180px; }
        .hyupload-btn-submit { height: 40px; padding: 0 35px !important; cursor: pointer; font-weight: 600; }
        #hyupload-loading { display: none; color: #2271b1; font-weight: bold; margin-top: 15px; }

        /* 画廊主体 */
        .hygal-component-container { margin: 20px 0; text-align: center; display: flex; flex-direction: column; }
        .hyplus-unselectable { -webkit-user-select: none; user-select: none; }
        .hygal-filter-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; }
        .hygal-input { background: #ffffff !important; border: 1px solid #cbd5e0; border-radius: 6px; padding: 0 12px; font-size: 16px; font-weight: 600; color: #2d3a4b; height: 40px; outline: none; }
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; font-weight: 600; }
        
        /* 顶部状态栏布局 */
        .hygal-status-bar { display: none; grid-template-columns: 1fr auto 1fr; align-items: center; height: 40px; margin: 10px 0; padding: 0 10px; font-size: 14px; color: #475569; }
        .status-left { text-align: left; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .status-center { display: flex; justify-content: center; align-items: center; }
        .status-right { text-align: right; display: flex; justify-content: flex-end; align-items: center; min-width: 80px; gap: 10px; }

        /* 翻页器样式 (共用) */
        .hygal-pager { align-items: center; gap: 2px; display: none; } 
        .pager-btn { cursor: pointer; padding: 0 6px; font-size: 20px; color: #43a5f5; font-weight: bold; line-height: 1; } 
        .pager-btn.disabled { opacity: 0.2; cursor: default; color: #94a3b8; }
        .pager-text { cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: background 0.2s; font-weight: 600; }
        .pager-text:hover { background: #f1f5f9; color: #43a5f5; }
        
        /* 底部翻页器专用位置 */
        .footer-pager-wrap { margin: 15px 0; display: flex; justify-content: center; }

        /* 功能按钮 */
        .close-btn, .dl-batch-btn { 
            cursor: pointer; line-height: 1; visibility: hidden; 
            transition: transform 0.2s; padding: 2px; font-family: Arial, sans-serif; font-weight: bold; 
        }
        .close-btn { color: #ef4444 !important; font-size: 28px !important; }
        .dl-batch-btn { color: #43a5f5 !important; font-size: 16px !important; }
        .close-btn:hover, .dl-batch-btn:hover { transform: scale(1.2); }

        /* 内容区 */
        #hygal-output { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; }
        #hygal-output.loading { height: 0; min-height: 0; overflow: hidden; opacity: 0; }
        .hygal-item { display: flex; flex-direction: column; background: #fff; border-radius: 4px; border: 1px solid #eef0f2; position: relative; overflow: hidden; }
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f7f8f9; }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        .hygal-title { padding: 5px 2px !important; font-size: 12px !important; color: #666 !important; text-align: center; line-height: 1.2 !important; word-wrap: break-word; cursor: default; }
        .is-admin .hygal-title { cursor: pointer; }
        .hygal-item.has-order .hygal-title { background-color: #f4fbfc !important; color: #00626b !important; }
        .hytool-version { margin-top: -1.5em; color: #ccc; font-size: 13px; text-align: right; pointer-events: none; }

        /* 弹窗 */
        #hygal-admin-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999; justify-content: center; align-items: center; background: rgba(0,0,0,0.25); }
        .hygal-modal-content { background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5); text-align: left; position: relative; }
        .hygal-modal-label { display: block; font-size: 13px; color: #666; font-weight: 600; }
        .hygal-modal-input { width: 100%; margin-top: 6px; margin-bottom: 12px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .hygal-modal-btns { margin-top: 20px; display: flex; gap: 10px; }
        .hygal-btn { flex: 1; padding: 10px; cursor: pointer; font-weight: 600; }
        .hygal-modal-meta { font-size: 12px; color: #999; margin-top: -8px; margin-bottom: 5px; text-align: right; font-family: monospace; }
        .hygal-btn-delete { position: absolute; top: 8px; right: 12px; color: #ff4d4f; font-size: 24px; font-weight: bold; line-height: 1; cursor: pointer; opacity: 0.2; transition: opacity 0.2s, transform 0.2s; z-index: 10; padding: 5px; }
        .hygal-btn-delete:hover { opacity: 1; transform: scale(1.1); }
        .hygal-no-scroll { overflow: hidden !important; width: 100%; }
    </style>

    <div id="hygal-admin-modal" class="hyplus-unselectable">
        <div class="hygal-modal-content">
            <div id="hygal-delete-trigger" class="hygal-btn-delete" title="删除此图片">🗑️</div>
            <label class="hygal-modal-label">权重评分（数值越大越靠前）</label>
            <input type="number" id="mod-order" class="hygal-modal-input" placeholder="无">
            <label class="hygal-modal-label">修改分类</label>
            <select id="mod-prefix" class="hygal-modal-input">
                <?php foreach ($tag_list as $tag): ?>
                    <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                <?php endforeach; ?>
            </select>
            <label class="hygal-modal-label">标题</label>
            <input type="text" id="mod-title" class="hygal-modal-input">
            <div id="mod-meta" class="hygal-modal-meta">大小: - <br>上传日期: -</div>
            <div class="hygal-modal-btns">
                <button class="hyplus-nav-link hygal-btn hygal-btn-cancel" onclick="closeHyModal()">取消</button>
                <button class="hyplus-nav-link hygal-btn hygal-btn-save" id="hygal-save-trigger">保存修改</button>
            </div>
        </div>
    </div>

    <div class="hygal-merged-wrapper">
        <?php if ($can_upload): ?>
        <div class="hyupload-container">
            <div class="hyplus-nav-section" style="padding: 20px;">
                <div id="hyupload-drop-zone">
                    <img id="hyupload-preview-img" src="">
                    <div id="hyupload-drop-text" class="hyupload-hint">点击、拖拽或粘贴图片到此处上传</div>
                    <input type="file" id="hyupload-file-input" style="display:none" accept="image/*">
                </div>
                <div id="hyupload-stats" class="hyplus-unselectable">
                    <span>✅ 已同步至媒体库！</span>
                    <span>节省: <span id="hyupload-ratio" class="hyupload-stat-tag"></span></span>
                </div>
                <div id="hyupload-controls" class="hyplus-unselectable" style="display:none;">
                    <div class="hyupload-row">
                        <select id="hyupload-prefix" class="hyupload-input">
                            <?php foreach ($tag_list_for_upload as $tag): ?>
                                <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="hyupload-title" class="hyupload-input" placeholder="输入描述标题...">
                        <button id="hyupload-upload-btn" class="hyplus-nav-link hyupload-btn-submit">转换并上传</button>
                    </div>
                </div>
                <div id="hyupload-loading" class="hyplus-unselectable">🚀 正在处理 WebP...</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="hygal-component-container hyplus-unselectable <?php echo ($is_admin_manage === 'true') ? 'is-admin' : ''; ?>">
            <div class="hyplus-nav-section">
                <div class="hygal-filter-container">
                    <select id="f-category" class="hygal-input">
                        <?php foreach ($tag_list as $tag): ?>
                            <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="f-ppp" class="hygal-input"><option value="10">10项/页</option><option value="30">30项/页</option><option value="60">60项/页</option></select>
                    <select id="f-order" class="hygal-input"><option value="DESC">最新优先</option><option value="ASC">最早优先</option><option value="RAND">随机排序</option></select>
                </div>
                <div style="margin-top:15px; display:flex; justify-content:center;">
                    <button id="btn-fetch" class="hyplus-nav-link hygal-btn-submit">展示图片</button>
                </div>
            </div>

            <div id="hygal-bar-top" class="hygal-status-bar">
                <div class="status-left info-text"></div>
                <div class="status-center">
                    <div class="loading-text" style="display:none;">📡 获取中...</div>
                    <div class="hygal-pager top-pager">
                        <span class="pager-btn prev-btn">&lt;</span><span class="pager-text" title="点击跳转页码">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                    </div>
                </div>
                <div class="status-right">
                    <div class="dl-batch-btn" title="批量下载当前页图片">📥</div>
                    <div class="close-btn" title="关闭画廊">&times;</div>
                </div>
            </div>

            <div id="hygal-output" class="loading"></div>

            <div class="footer-pager-wrap">
                <div class="hygal-pager footer-pager">
                    <span class="pager-btn prev-btn">&lt;</span><span class="pager-text" title="点击跳转页码">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                </div>
            </div>

            <div class="hytool-version">HyGal v1.4.2</div>
        </div>
    </div>

    <script>
    function closeHyModal() { 
        jQuery('#hygal-admin-modal').hide(); 
        jQuery('body').removeClass('hygal-no-scroll');
    }

    jQuery(document).ready(function($) {
        const isAdmin = <?php echo $is_admin_manage; ?>;
        let currentPage = 1, totalPages = 1, currentTargetId = null;

        function fetchImages(page = 1, isSwitching = false) {
            currentPage = page;
            $('#hygal-output').stop().animate({opacity: 0}, 80, function() { $(this).addClass('loading'); });
            $('.hygal-status-bar').css('display', 'grid');
            $('.loading-text').show(); 
            $('.hygal-pager').hide();
            $('.close-btn, .dl-batch-btn').css('visibility', 'hidden');

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
                    $('.info-text').html('<b>' + $('#f-category').val() + '</b> (' + data.total_items + ')');
                    totalPages = data.total_pages || 1;
                    
                    // 同步更新上下两个翻页器
                    $('.pager-text').text(currentPage + ' / ' + totalPages);
                    $('.prev-btn').toggleClass('disabled', currentPage <= 1);
                    $('.next-btn').toggleClass('disabled', currentPage >= totalPages);
                    
                    $('.hygal-pager').css('display', 'flex'); 
                    $('.close-btn, .dl-batch-btn').css('visibility', 'visible');
                    $('#hygal-output').removeClass('loading').html(data.html).stop().css('opacity', 1).hide().fadeIn(80);
                    
                    if(!isSwitching) $('html, body').animate({ scrollTop: $('#hygal-bar-top').offset().top - 80 }, 300);
                }
            });
        }

        // 批量下载逻辑
        $('.dl-batch-btn').on('click', function() {
            const $items = $('#hygal-output .hygal-item');
            if ($items.length === 0) return;
            if (!confirm('🚀 确定要批量下载当前页的 ' + $items.length + ' 张图片吗？')) return;

            $items.each(function(index) {
                const imgUrl = $(this).find('img').attr('src');
                const imgTitle = $(this).find('.hygal-title').text().trim() || 'image_' + index;
                setTimeout(() => {
                    const link = document.createElement('a');
                    link.href = imgUrl;
                    link.download = imgTitle;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }, index * 300);
            });
        });

        // 通用翻页交互逻辑 (上下共用)
        $(document).on('click', '.pager-text', function() {
            if (totalPages <= 1) return;
            const targetPage = prompt('🚀 跳转到第几页？ (1 - ' + totalPages + ')', currentPage);
            if (targetPage) {
                const p = parseInt(targetPage);
                if (p >= 1 && p <= totalPages) fetchImages(p);
            }
        });

        $(document).on('click', '.prev-btn', function() {
            if(!$(this).hasClass('disabled')) fetchImages(currentPage - 1);
        });

        $(document).on('click', '.next-btn', function() {
            if(!$(this).hasClass('disabled')) fetchImages(currentPage + 1);
        });

        // 修复关闭逻辑：带淡出动画
        $('.close-btn').on('click', function() { 
            $('#hygal-output').fadeOut(250, function() {
                $(this).empty().addClass('loading').show();
                $('.hygal-status-bar, .hygal-pager').hide();
            });
        });

        $('#btn-fetch').on('click', () => fetchImages(1, true));

        // 管理逻辑
        if (isAdmin) {
            $('#hygal-output').on('click', '.hygal-title', function() {
                const $item = $(this).closest('.hygal-item');
                currentTargetId = $item.data('id');
                $('#mod-order').val($item.attr('data-raw-order'));
                $('#mod-prefix').val($item.attr('data-current-prefix'));
                $('#mod-title').val($(this).text());
                $('#mod-meta').html('大小: ' + $item.attr('data-size') + '<br>日期: ' + $item.attr('data-date'));
                $('#hygal-admin-modal').css('display', 'flex');
                $('body').addClass('hygal-no-scroll');
            });
            $('#hygal-save-trigger').on('click', function() {
                const btn = $(this); btn.prop('disabled', true).text('同步中...');
                $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                    action: 'hygal_update_asset',
                    img_id: currentTargetId,
                    order_val: $('#mod-order').val(),
                    new_prefix: $('#mod-prefix').val(),
                    new_pure_title: $('#mod-title').val(),
                    _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                }, function() { btn.prop('disabled', false).text('保存修改'); closeHyModal(); fetchImages(currentPage, false); });
            });
            $('#hygal-delete-trigger').on('click', function() {
                if (confirm('⚠️ 确定永久删除？')) {
                    $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                        action: 'hygal_delete_asset',
                        img_id: currentTargetId,
                        _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                    }, function() { closeHyModal(); fetchImages(currentPage, false); });
                }
            });
        }

        // 上传逻辑
        if ($('#hyupload-drop-zone').length) {
            let currentBlob = null;
            function performUpload() {
                if (!currentBlob || $('#hyupload-upload-btn').prop('disabled')) return;
                const fd = new FormData();
                fd.append('action', 'hyu_webp_upload');
                fd.append('_nonce', '<?php echo wp_create_nonce("hyu_upload_nonce"); ?>');
                fd.append('file', currentBlob);
                fd.append('title', $('#hyupload-title').val());
                fd.append('prefix', $('#hyupload-prefix').val());
                $('#hyupload-loading').show(); $('#hyupload-upload-btn').prop('disabled', true);
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>', type: 'POST', data: fd, processData: false, contentType: false,
                    success: function(res) {
                        $('#hyupload-loading').hide(); $('#hyupload-upload-btn').prop('disabled', false);
                        if (res.success) { 
                            $('#hyupload-ratio').text(res.data.ratio + '%'); 
                            $('#hyupload-stats').fadeIn(); currentBlob = null; 
                            $('#hyupload-preview-img').hide(); $('#hyupload-drop-text').show(); 
                            $('#hyupload-controls').hide(); 
                        }
                    }
                });
            }
            $('#hyupload-drop-zone').on('click', () => $('#hyupload-file-input')[0].click());
            $('#hyupload-file-input').on('change', function() {
                const file = this.files[0]; currentBlob = file;
                const r = new FileReader(); r.onload = (e) => { $('#hyupload-preview-img').attr('src', e.target.result).show(); $('#hyupload-drop-text').hide(); $('#hyupload-controls').fadeIn(); };
                r.readAsDataURL(file);
            });
            $('#hyupload-upload-btn').on('click', performUpload);
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端逻辑 (不变)
 */
add_action('wp_ajax_hygal_fetch_minimal', 'hygal_ajax_fetch_minimal_handler');
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

    $results = $wpdb->get_results("SELECT p.ID, p.post_title, p.post_date, m_ord.meta_value as raw_order " . $sql_where . $orderby . $wpdb->prepare(" LIMIT %d, %d", $offset, $ppp));
    $total_items = $wpdb->get_var("SELECT COUNT(*) " . $sql_where);

    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $file_path = get_attached_file($post->ID);
        $size_str = file_exists($file_path) ? size_format(filesize($file_path)) : '未知';
        $date_str = get_the_date('Y-m-d', $post->ID);
        $has_order = ($post->raw_order !== '' && $post->raw_order !== null) ? 'has-order' : '';
        $html .= '<div class="hygal-item '.$has_order.'" data-id="'.$post->ID.'" data-raw-order="'.esc_attr($post->raw_order).'" data-current-prefix="'.esc_attr($prefix).'" data-size="'.$size_str.'" data-date="'.$date_str.'"><div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy"></div><div class="hygal-title">'.esc_html($post->post_title).'</div></div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => (int)$total_items, 'total_pages' => ceil($total_items / $ppp)]);
}

add_action('wp_ajax_hygal_update_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) return;
    $id = intval($_POST['img_id']);
    update_post_meta($id, '_hygal_order', sanitize_text_field($_POST['order_val']));
    update_post_meta($id, '_hygal_category', sanitize_text_field($_POST['new_prefix']));
    wp_update_post(['ID' => $id, 'post_title' => sanitize_text_field($_POST['new_pure_title'])]);
    wp_send_json_success();
});

add_action('wp_ajax_hygal_delete_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (current_user_can('manage_options')) wp_delete_attachment(intval($_POST['img_id']), true);
    wp_send_json_success();
});

add_action('wp_ajax_hyu_webp_upload', function() {
    check_ajax_referer('hyu_upload_nonce', '_nonce');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    $file = $_FILES['file'];
    $prefix = $_POST['prefix'];
    $title = $_POST['title'] ?: date('YmdHis');
    
    $tmp = $file['tmp_name'];
    $img = @imagecreatefromstring(file_get_contents($tmp));
    $target = $tmp . '.webp';
    imagewebp($img, $target, 80);
    imagedestroy($img);

    add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 999);
    $id = media_handle_sideload(['name' => $title.'.webp', 'tmp_name' => $target], 0);
    update_post_meta($id, '_hygal_category', $prefix);
    wp_update_post(['ID' => $id, 'post_title' => $title]);
    wp_send_json_success(['ratio' => 80]);
});
?>