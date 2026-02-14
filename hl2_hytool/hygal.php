<?php
/**
 * Plugin Name: HyGal 极致画廊 (Jump-to-Page Edition)
 * Description: 集成 HyUploader WebP 上传与 HyGal 空间优化版画廊。新增点击页码直接跳转功能。
 * Version: 1.3.0
 */

add_shortcode('hygal', 'hygal_unified_handler');

function hygal_unified_handler($atts) {
    // 1. 参数解析与权限判断
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));
    
    if (empty($tag_list)) {
        $tag_list_for_upload = ['图']; 
    } else {
        $tag_list_for_upload = $tag_list;
    }

    $can_upload = current_user_can('upload_files');
    $is_admin_manage = current_user_can('manage_options') ? 'true' : 'false';

    if (empty($tag_list) && $is_admin_manage === 'false') {
        return '<p style="text-align:center;color:#666;">提示：请设置参数 [hygal tags="分类1,分类2"]</p>';
    }

    ob_start();
    ?>
    <style>
        /* =========================================
           PART 1: HyUploader WebP Styles (前缀 hyupload-)
           ========================================= */
        .hyupload-container { margin: 0 0 20px 0; text-align: center; font-family: -apple-system, sans-serif; }
        
        #hyupload-drop-zone { 
            border: 2px dashed #cbd5e0; 
            min-height: 100px; 
            border-radius: 12px; 
            background: #f8fafc; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            transition: all 0.2s;
            padding: 15px;
            padding-top: 25px;
            position: relative;
        }
        #hyupload-drop-zone:hover { border-color: #43a5f5; background: #f0f9ff; }
        #hyupload-drop-zone.hover { border-color: #43a5f5; background: #f0f9ff; }
        
        #hyupload-preview-img { max-height: 80px; border-radius: 6px; display: none; margin-right: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .hyupload-hint { color: #64748b; font-size: 15px; font-weight: 500; pointer-events: none; }

        #hyupload-stats { display: none; padding: 12px; margin-top: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; color: #166534; }
        .hyupload-stat-tag { font-weight: 700; color: #15803d; text-decoration: underline; margin: 0 4px; }

        .hyupload-row { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; margin-top: 15px; }
        
        .hyupload-input {
            background: #ffffff !important;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 15px;
            font-weight: 600;
            color: #2d3a4b;
            height: 40px;
            outline: none;
            transition: border-color 0.2s;
        }
        .hyupload-input:focus { border-color: #43a5f5; }
        
        #hyupload-prefix { min-width: 100px; cursor: pointer; }
        #hyupload-title { flex: 1; min-width: 180px; }

        .hyupload-btn-submit { height: 40px; padding: 0 35px !important; cursor: pointer; font-weight: 600; }
        
        #hyupload-loading { display: none; color: #2271b1; font-weight: bold; margin: 0; margin-top: 15px; }

        /* =========================================
           PART 2: HyGal Styles (前缀 hygal-)
           ========================================= */
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
        
        /* 中间：翻页控件 */
        .status-center { font-weight: 600; min-width: 80px; display: flex; justify-content: center; }
        .hygal-pager { align-items: center; gap: 2px; display: none; } 
        .pager-btn { cursor: pointer; padding: 0 4px; font-size: 18px; color: #43a5f5; transition: transform 0.1s; } 
        .pager-btn:active { transform: scale(1.2); }
        .pager-btn.disabled { opacity: 0.2; cursor: default; color: #94a3b8; }
        
        /* 页码跳转样式 */
        .pager-text { cursor: pointer; padding: 2px 6px; border-radius: 4px; transition: background 0.2s; }
        .pager-text:hover { background: #f1f5f9; color: #43a5f5; }

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
        .hytool-version { margin-top: 0.5em; color: #ccc; font-size: 13px; text-align: right; pointer-events: none; }

        /* 管理员弹窗 UI */
        #hygal-admin-modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: 99999; justify-content: center; align-items: center;
            background: rgba(0,0,0,0.25);
        }
        .hygal-modal-content {
            background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 12px; width: 90%; max-width: 400px;
            box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5); text-align: left;
            position: relative;
        }
        .hygal-modal-label { display: block; font-size: 13px; color: #666; font-weight: 600; }
        .hygal-modal-input { width: 100%; margin-top: 6px; margin-bottom: 12px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .hygal-modal-btns { margin-top: 20px; display: flex; gap: 10px; }
        .hygal-btn { flex: 1; padding: 10px; cursor: pointer; font-weight: 600; }
        
        .hygal-modal-meta { font-size: 12px; color: #999; margin-top: -8px; margin-bottom: 5px; text-align: right; font-family: monospace; }

        .hygal-btn-delete {
            position: absolute; top: 8px; right: 12px;
            color: #ff4d4f; font-size: 24px; font-weight: bold;
            line-height: 1; cursor: pointer;
            opacity: 0; transition: opacity 0.2s, transform 0.2s;
            z-index: 10; padding: 5px;
        }
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
                    <span>✅ 已同步至媒体库并建立索引！</span>
                    <span>原大小: <span id="hyupload-old" class="hyupload-stat-tag"></span></span>
                    <span>压缩后: <span id="hyupload-new" class="hyupload-stat-tag"></span></span>
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
                <div id="hyupload-loading" class="hyplus-unselectable">🚀 正在处理 WebP 转换并存储索引...</div>
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
                        <span class="pager-btn prev-btn">&lt;</span><span class="pager-text" title="点击跳转页码">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                    </div>
                </div>
                <div class="status-right"><div class="close-btn">&times;</div></div>
            </div>

            <div id="hygal-output" class="loading"></div>

            <div id="hygal-bar-bottom" class="hygal-status-bar bar-bottom">
                <div class="status-left"></div>
                <div class="status-center">
                    <div class="hygal-pager">
                        <span class="pager-btn prev-btn">&lt;</span><span class="pager-text" title="点击跳转页码">1 / 1</span><span class="pager-btn next-btn">&gt;</span>
                    </div>
                </div>
                <div class="status-right"></div>
            </div>
            <div class="hytool-version">HyGal v1.3.0</div>
        </div>

    </div>

    <script>
    function closeHyModal() { 
        jQuery('#hygal-admin-modal').css('display', 'none'); 
        jQuery('body').removeClass('hygal-no-scroll');
    }

    jQuery(document).ready(function($) {
        
        // --- 逻辑块 A: HyGal Viewer 逻辑 ---
        const isAdmin = <?php echo $is_admin_manage; ?>;
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

        // 跳转页码逻辑
        $('.pager-text').on('click', function() {
            if (totalPages <= 1) return;
            const targetPage = prompt('🚀 跳转到第几页？ (范围: 1 - ' + totalPages + ')', currentPage);
            if (targetPage !== null) {
                const pageNum = parseInt(targetPage);
                if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages) {
                    if (pageNum !== currentPage) {
                        fetchImages(pageNum);
                    }
                } else {
                    alert('❌ 输入无效，请输入 1 到 ' + totalPages + ' 之间的数字。');
                }
            }
        });

        $('#btn-fetch').on('click', () => fetchImages(1, true));
        $('.prev-btn').on('click', function() { if(!$(this).hasClass('disabled')) fetchImages(currentPage - 1); });
        $('.next-btn').on('click', function() { if(!$(this).hasClass('disabled')) fetchImages(currentPage + 1); });
        
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
                const metaText = '大小: ' + $item.attr('data-size') + '<br>上传日期: ' + $item.attr('data-date');
                $('#mod-meta').html(metaText);
                $('#hygal-admin-modal').css('display', 'flex');
                $('body').addClass('hygal-no-scroll');
            });

            $('#hygal-admin-modal').on('click', function(e) { if (e.target === this) closeHyModal(); });
            $(document).on('keydown', function(e) { if (e.key === "Escape" && $('#hygal-admin-modal').is(':visible')) closeHyModal(); });

            const submitAssetUpdate = () => {
                const btn = $('#hygal-save-trigger');
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
            $('#hygal-save-trigger').on('click', submitAssetUpdate);
            $('.hygal-modal-input').on('keypress', function(e) { if (e.which === 13) submitAssetUpdate(); });

            function generateCode() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                let result = '';
                for (let i = 0; i < 4; i++) result += chars.charAt(Math.floor(Math.random() * chars.length));
                return result;
            }

            $('#hygal-delete-trigger').on('click', function() {
                if(!currentTargetId) return;
                const verifyCode = generateCode();
                const userInput = prompt('⚠️ 删除警告：\n文件将从服务器彻底移除，不可恢复！\n\n验证码：' + verifyCode);
                if (userInput === verifyCode) {
                    const $delBtn = $(this);
                    $delBtn.css('opacity', '0.5').css('pointer-events', 'none');
                    $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                        action: 'hygal_delete_asset',
                        img_id: currentTargetId,
                        _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                    }, function(res) {
                        if(res.success) { alert('已删除'); closeHyModal(); fetchImages(currentPage, false); }
                        $delBtn.css('opacity', '').css('pointer-events', '');
                    });
                } else if (userInput !== null) { alert('验证码错误'); }
            });
        }

        // --- 逻辑块 B: HyUploader 逻辑 ---
        if ($('#hyupload-drop-zone').length) {
            let currentBlob = null;
            function formatBytes(b) {
                if (b < 1024) return b + ' B';
                if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
                return (b / 1048576).toFixed(1) + ' MB';
            }
            function performUpload() {
                if (!currentBlob || $('#hyupload-upload-btn').prop('disabled')) return;
                const fd = new FormData();
                fd.append('action', 'hyu_webp_upload');
                fd.append('_nonce', '<?php echo wp_create_nonce("hyu_upload_nonce"); ?>');
                fd.append('file', currentBlob);
                fd.append('title', $('#hyupload-title').val());
                fd.append('prefix', $('#hyupload-prefix').val());
                $('#hyupload-loading').show();
                $('#hyupload-upload-btn').prop('disabled', true).css('opacity', '0.6');

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST', data: fd, processData: false, contentType: false,
                    success: function(res) {
                        $('#hyupload-loading').hide();
                        $('#hyupload-upload-btn').prop('disabled', false).css('opacity', '1');
                        if (res.success) {
                            $('#hyupload-old').text(formatBytes(res.data.old_size));
                            $('#hyupload-new').text(formatBytes(res.data.new_size));
                            $('#hyupload-ratio').text(res.data.ratio + '%');
                            $('#hyupload-stats').fadeIn();
                            $('#hyupload-title').val(""); 
                            $('#hyupload-preview-img').hide();
                            $('#hyupload-drop-text').show();
                            $('#hyupload-controls').hide();
                            currentBlob = null;
                        } else { alert('失败: ' + res.data); }
                    }
                });
            }
            $('#hyupload-drop-zone').on('click', () => $('#hyupload-file-input')[0].click());
            function showPreview(file) {
                if (!file || !file.type.startsWith('image/')) return;
                currentBlob = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    $('#hyupload-preview-img').attr('src', e.target.result).show();
                    $('#hyupload-drop-text').hide();
                    $('#hyupload-controls').fadeIn();
                    $('#hyupload-stats').hide();
                    setTimeout(() => $('#hyupload-title').focus(), 200);
                };
                reader.readAsDataURL(file);
            }
            $('#hyupload-file-input').on('change', function() { showPreview(this.files[0]); });
            $('#hyupload-drop-zone').on('dragover', function(e){ e.preventDefault(); $(this).addClass('hover'); });
            $('#hyupload-drop-zone').on('dragleave', function(){ $(this).removeClass('hover'); });
            $('#hyupload-drop-zone').on('drop', function(e){ e.preventDefault(); $(this).removeClass('hover'); const files = e.originalEvent.dataTransfer.files; if (files.length > 0) showPreview(files[0]); });
            $(document).on('paste', (e) => { const items = (e.clipboardData || e.originalEvent.clipboardData).items; for (let item of items) { if (item.type.indexOf("image") !== -1) showPreview(item.getAsFile()); } });
            $('#hyupload-upload-btn').on('click', function(e) { e.preventDefault(); performUpload(); });
            $('#hyupload-title').on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); performUpload(); } });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * =======================================================
 * 后端逻辑 PART A: HyGal Viewer Fetch & Update & Delete
 * =======================================================
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

    $results = $wpdb->get_results("SELECT p.ID, p.post_title, p.post_date, m_ord.meta_value as raw_order " . $sql_where . $orderby . $wpdb->prepare(" LIMIT %d, %d", $offset, $ppp));
    $total_items = $wpdb->get_var("SELECT COUNT(*) " . $sql_where);

    $html = '';
    foreach ($results as $post) {
        $url = wp_get_attachment_url($post->ID);
        $display_title = $post->post_title;
        $has_order_class = ($post->raw_order !== '' && $post->raw_order !== null) ? 'has-order' : '';
        $file_path = get_attached_file($post->ID);
        $size_str = file_exists($file_path) ? size_format(filesize($file_path)) : '未知';
        $date_str = get_the_date('Y年m月d日', $post->ID);

        $html .= '<div class="hygal-item '.$has_order_class.'" 
                       data-id="'.$post->ID.'" 
                       data-raw-order="'.esc_attr($post->raw_order).'" 
                       data-current-prefix="'.esc_attr($prefix).'"
                       data-size="'.esc_attr($size_str).'"
                       data-date="'.esc_attr($date_str).'">
                    <div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy"></div>
                    <div class="hygal-title">'.esc_html($display_title).'</div>
                  </div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => (int)$total_items, 'total_pages' => ceil($total_items / $ppp)]);
}

add_action('wp_ajax_hygal_update_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('权限不足');
    $img_id = intval($_POST['img_id']);
    $order_val = sanitize_text_field($_POST['order_val']);
    $new_prefix = sanitize_text_field($_POST['new_prefix']);
    $new_title = sanitize_text_field($_POST['new_pure_title']);

    if ($order_val === '') delete_post_meta($img_id, '_hygal_order');
    else update_post_meta($img_id, '_hygal_order', intval($order_val));
    
    wp_update_post(['ID' => $img_id, 'post_title' => $new_title]);
    update_post_meta($img_id, '_hygal_category', $new_prefix);
    wp_send_json_success();
});

add_action('wp_ajax_hygal_delete_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('权限不足');
    $img_id = intval($_POST['img_id']);
    if (!$img_id) wp_send_json_error('无效 ID');
    $result = wp_delete_attachment($img_id, true);
    if ($result) wp_send_json_success('删除成功');
    else wp_send_json_error('删除失败');
});

/**
 * 后端逻辑 PART B: HyUploader
 */
add_action('wp_ajax_hyu_webp_upload', 'hy_uploader_webp_ajax_handler_merged');
function hy_uploader_webp_ajax_handler_merged() {
    check_ajax_referer('hyu_upload_nonce', '_nonce');
    if (!current_user_can('upload_files')) wp_send_json_error('无权操作');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    @ini_set('memory_limit', '512M');
    if (!isset($_FILES['file'])) wp_send_json_error('无文件');

    $file = $_FILES['file'];
    $tmp_path = $file['tmp_name'];
    $old_size = filesize($tmp_path);
    $prefix = sanitize_text_field($_POST['prefix']);
    $raw_title = sanitize_text_field($_POST['title']);
    $ts = date('YmdHis');
    $wp_title = !empty($raw_title) ? $raw_title : $ts;

    $info = @getimagesize($tmp_path);
    $target_webp = $tmp_path . '.webp';
    $is_converted = false;
    if (function_exists('imagewebp')) {
        $img = null;
        if ($info['mime'] == 'image/jpeg') $img = @imagecreatefromjpeg($tmp_path);
        elseif ($info['mime'] == 'image/png') $img = @imagecreatefrompng($tmp_path);
        if ($img) {
            if ($info['mime'] == 'image/png') {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            if (@imagewebp($img, $target_webp, 80)) $is_converted = true;
            imagedestroy($img);
        }
    }

    $final_file_path = $is_converted ? $target_webp : $tmp_path;
    $new_size = filesize($final_file_path);
    $ratio = ($old_size > 0) ? round((1 - ($new_size / $old_size)) * 100, 1) : 0;
    $final_name = $ts . ($is_converted ? '.webp' : image_type_to_extension($info[2]));

    add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 999);
    add_filter('big_image_size_threshold', '__return_false', 999);

    $attach_id = media_handle_sideload(['name' => $final_name, 'tmp_name' => $final_file_path], 0);
    if (!is_wp_error($attach_id)) {
        wp_update_post(['ID' => $attach_id, 'post_title' => $wp_title]);
        if (!empty($prefix)) update_post_meta($attach_id, '_hygal_category', $prefix);
    }
    if ($is_converted && file_exists($tmp_path)) @unlink($tmp_path);
    if (is_wp_error($attach_id)) wp_send_json_error($attach_id->get_error_message());

    wp_send_json_success(['old_size' => $old_size, 'new_size' => $new_size, 'ratio' => $ratio]);
}
?>