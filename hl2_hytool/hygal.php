<?php
/**
 * Plugin Name: HyGal 极致画廊 (Dual Pager Edition)
 * Description: 集成上传、管理、上下双翻页组件、批量下载功能。修复了顶部翻页丢失问题。
 * Version: 1.5.0.1
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
        .hyupload-container { margin: 0 0 0 0; text-align: center; font-family: -apple-system, sans-serif; }
        #hyupload-drop-zone { border: 2px dashed var(--hyplus-border-color-light2); min-height: 100px; border-radius: 12px; background: var(--hyplus-bg-settings); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; padding: 15px; padding-top: 25px; position: relative; }
        #hyupload-drop-zone:hover, #hyupload-drop-zone.hover { border-color: var(--hyplus-primary-link-color); background: var(--hyplus-bg-button-light); }
        #hyupload-preview-img { max-height: 80px; border-radius: 6px; display: none; margin-right: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .hyupload-hint { color: #64748b; font-size: 15px; font-weight: 500; pointer-events: none; }
        #hyupload-stats { display: none; padding: 12px; margin-top: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; color: #166534; }
        .hyupload-stat-tag { font-weight: 700; color: #15803d; text-decoration: underline; margin: 0 4px; }
        .hyupload-row { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; margin-top: 15px; }
        .hyupload-input { background: var(--hyplus-bg-search-input) !important; border: 1px solid var(--hyplus-border-color-light2); border-radius: 6px; padding: 0 12px; font-size: 15px; font-weight: 600; color: var(--hyplus-text-heading); height: 40px; outline: none; }
        #hyupload-prefix { min-width: 100px; cursor: pointer; }
        #hyupload-title { flex: 1; min-width: 180px; }
        .hyupload-btn-submit { height: 40px; padding: 0 35px !important; cursor: pointer; font-weight: 600; }
        #hyupload-loading { display: none; color: #2271b1; font-weight: bold; margin-top: 15px; }

        /* 画廊主体 */
        .hygal-component-container { margin: 20px 0; text-align: center; display: flex; flex-direction: column; }
        .hyplus-unselectable { -webkit-user-select: none; user-select: none; }
        .hygal-filter-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; }
        .hygal-input { background: var(--hyplus-bg-search-input) !important; border: 1px solid var(--hyplus-border-color-light2); border-radius: 6px; padding: 0 12px; font-size: 16px; font-weight: 600; color: var(--hyplus-text-heading); height: 40px; outline: none; }
        .hygal-btn-submit { height: 40px; padding: 0 45px !important; font-size: 16px !important; cursor: pointer; font-weight: 600; }
        
        /* 顶部状态栏布局 */
        .hygal-status-bar { display: none; grid-template-columns: 1fr auto 1fr; align-items: center; height: 40px; margin: 10px 0; padding: 0 10px; font-size: 14px; color: #475569; }
        .status-left { text-align: left; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .status-center { display: flex; justify-content: center; align-items: center; }
        .status-right { text-align: right; display: flex; justify-content: flex-end; align-items: center; min-width: 80px; gap: 10px; }

        /* 翻页器样式 (共用) */
        .hygal-pager { align-items: center; gap: 2px; display: none; } 
        .pager-btn { cursor: pointer; padding: 0 6px; font-size: 20px; color: var(--hyplus-primary-link-color); font-weight: bold; line-height: 1; } 
        .pager-btn.disabled { opacity: 0.5; cursor: default; color: #94a3b8; }
        .pager-text { cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: background 0.2s; font-weight: 600; }
        .pager-text:hover { background: var(--hyplus-bg-button-light); color: var(--hyplus-primary-link-color); }
        
        /* 底部翻页器专用位置 */
        .footer-pager-wrap { margin: 15px 0; display: flex; justify-content: center; }

        /* 功能按钮 */
        .close-btn, .dl-batch-btn { 
            cursor: pointer; line-height: 1; visibility: hidden; 
            transition: transform 0.2s; padding: 2px; font-family: Arial, sans-serif; font-weight: bold; 
        }
        .close-btn { color: var(--hyplus-btn-close-control) !important; font-size: 28px !important; }
        .dl-batch-btn { color: #43a5f5 !important; font-size: 16px !important; }
        .close-btn:hover, .dl-batch-btn:hover { transform: scale(1.2); }

        /* 内容区 */
        #hygal-output { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; margin-top: 5px; }
        #hygal-output.loading { height: 0; min-height: 0; overflow: hidden; opacity: 0; }
        .hygal-item { display: flex; flex-direction: column; background: var(--hyplus-bg-search-input); border-radius: 4px; border: 1px solid var(--hyplus-border-color-light); position: relative; overflow: hidden; }
        .hygal-img-wrapper { width: 100%; aspect-ratio: 1/1; overflow: hidden; background: var(--hyplus-bg-settings); }
        .hygal-img-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; margin: 0 !important; }
        .hygal-title { padding: 5px 2px !important; font-size: 12px !important; color: #666 !important; text-align: center; line-height: 1.2 !important; word-wrap: break-word; cursor: default; }
        .is-admin .hygal-title { cursor: pointer; }
        .hygal-item.has-order .hygal-title { background-color: #e7fafd !important; color: #00626b !important; }
        .hytool-version { margin-top: -1.5em; color: var(--hyplus-border-color-neutral); font-size: 13px; text-align: right; pointer-events: none; }

        /* 弹窗 */
        #hygal-admin-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999; justify-content: center; align-items: center; background: rgba(0,0,0,0.25); }
        .hygal-modal-content { background: var(--hyplus-bg-search-input); padding: 25px; border: 1px solid var(--hyplus-border-color-neutral); border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 4px 4px 10px 0 rgba(0, 0, 0, 0.5); text-align: left; position: relative; }
        .hygal-modal-label { display: block; font-size: 13px; color: #666; font-weight: 600; }
        .hygal-modal-input { width: 100%; margin-top: 6px; margin-bottom: 12px; padding: 8px; border: 1px solid var(--hyplus-border-color-neutral); border-radius: 8px; font-size: 14px; }
        .hygal-modal-btns { margin-top: 16px; display: flex; gap: 10px; }
        .hygal-btn { flex: 1; padding: 10px; cursor: pointer; font-weight: 600; }
        .hygal-modal-meta { font-size: 12px; color: #999; margin-top: -8px; margin-bottom: 5px; text-align: right; font-family: monospace; }
        .copy-link-btn { color: var(--hyplus-primary-link-color); cursor: pointer; transition: opacity 0.2s; }
        .copy-link-btn:hover { opacity: 0.7; }
        .hygal-btn-delete { position: absolute; top: 8px; right: 12px; color: #ff4d4f; font-size: 24px; font-weight: bold; line-height: 1; cursor: pointer; opacity: 0; transition: opacity 0.2s, transform 0.2s; z-index: 10; padding: 5px; }
        .hygal-btn-delete:hover { opacity: 1; transform: scale(1.1); }
        .hygal-no-scroll { overflow: hidden !important; width: 100%; }
    </style>

    <?php if ($is_admin_manage === 'true'): ?>
    <div id="hygal-admin-modal" class="hyplus-unselectable">
        <div class="hygal-modal-content">
            <div id="hygal-delete-trigger" class="hygal-btn-delete" title="删除此图片">🗑️</div>
            <label class="hygal-modal-label">权重评分（数值越大越靠前）</label>
            <input type="number" id="mod-order" class="hygal-modal-input" placeholder="无">
            <label class="hygal-modal-label">分类</label>
            <select id="mod-prefix" class="hygal-modal-input">
                <?php foreach ($tag_list as $tag): ?>
                    <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                <?php endforeach; ?>
            </select>
            <label class="hygal-modal-label">标题</label>
            <input type="text" id="mod-title" class="hygal-modal-input">
            <div id="mod-meta" class="hygal-modal-meta">大小: - <br>上传日期: -<br>复制路径: <a href="#" data-type="absolute" class="copy-link-btn">绝对</a> | <a href="#" data-type="relative" class="copy-link-btn">相对</a></div>
            <div class="hygal-modal-btns">
                <button class="hyplus-nav-link hygal-btn hygal-btn-cancel" onclick="closeHyModal()">取消</button>
                <button class="hyplus-nav-link hygal-btn hygal-btn-save" id="hygal-save-trigger">保存修改</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="hygal-merged-wrapper">
        <div class="hygal-component-container hyplus-unselectable <?php echo ($is_admin_manage === 'true') ? 'is-admin' : ''; ?>">
            <div class="hyplus-nav-section">
                <?php if ($can_upload): ?>
                <div class="hyupload-container" style="margin-bottom: 20px;">
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
                            <input type="text" id="hyupload-title" class="hyupload-input" placeholder="输入描述标题...">
                            <button id="hyupload-upload-btn" class="hyplus-nav-link hyupload-btn-submit">转换并上传</button>
                        </div>
                    </div>
                    <div id="hyupload-loading" class="hyplus-unselectable">🚀 正在处理 WebP 转换并存储索引...</div>
                </div>
                <?php endif; ?>
                
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

            <div class="hytool-version">HyGal v1.5.1</div>
        </div>
    </div>

    <script>
    function closeHyModal() { 
        jQuery('#hygal-admin-modal').hide(); 
        jQuery('body').removeClass('hygal-no-scroll');
    }

    // 点击弹窗背景关闭
    jQuery(document).on('click', '#hygal-admin-modal', function(e) {
        if (e.target === this) {
            closeHyModal();
        }
    });

    jQuery(document).ready(function($) {
        const isAdmin = <?php echo $is_admin_manage; ?>;
        let currentPage = 1, totalPages = 1, currentTargetId = null, currentImageUrl = null, isFetching = false;

        function fetchImages(page = 1, isSwitching = false) {
            isFetching = true;
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
                    $('.info-text').html('共 <b>' + data.total_items + '</b> 项');
                    totalPages = data.total_pages || 1;
                    
                    // 同步更新上下两个翻页器
                    $('.pager-text').text(currentPage + ' / ' + totalPages);
                    $('.prev-btn').toggleClass('disabled', currentPage <= 1);
                    $('.next-btn').toggleClass('disabled', currentPage >= totalPages);
                    
                    $('.hygal-pager').css('display', 'flex'); 
                    $('.close-btn, .dl-batch-btn').css('visibility', 'visible');
                    $('#hygal-output').removeClass('loading').html(data.html).stop().css('opacity', 1).hide().fadeIn(80, function() {
                        isFetching = false;
                    });
                    
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
            $('#hygal-output').fadeOut(100, function() {
                $(this).empty().addClass('loading').show();
                $('.hygal-status-bar, .hygal-pager').hide();
            });
        });

        $('#btn-fetch').on('click', () => {
            if (!isFetching) {
                fetchImages(1, true);
            }
        });

        // 管理逻辑
        if (isAdmin) {
            // ESC 快捷键关闭编辑面板
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#hygal-admin-modal').css('display') !== 'none') {
                    closeHyModal();
                }
            });

            $('#hygal-output').on('click', '.hygal-title', function() {
                const $item = $(this).closest('.hygal-item');
                currentTargetId = $item.data('id');
                currentImageUrl = $item.find('img').attr('src');
                $('#mod-order').val($item.attr('data-raw-order'));
                $('#mod-prefix').val($item.attr('data-current-prefix'));
                $('#mod-title').val($(this).text());
                $('#mod-meta').html('大小: ' + $item.attr('data-size') + '<br>上传日期: ' + $item.attr('data-date') + '<br>复制链接：<a href="#" data-type="absolute" class="copy-link-btn">绝对</a> | <a href="#" data-type="relative" class="copy-link-btn">相对</a>');
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
            // 复制链接功能
            $(document).on('click', '.copy-link-btn', function(e) {
                e.preventDefault();
                if (!currentImageUrl) {
                    alert('❌ 无法获取图片链接');
                    return;
                }
                
                let linkToCopy = currentImageUrl;
                const linkType = $(this).attr('data-type');
                
                if (linkType === 'relative') {
                    // 转换为相对路径
                    const url = new URL(linkToCopy, window.location.origin);
                    linkToCopy = url.pathname;
                }
                
                // 使用现代 Clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(linkToCopy).then(() => {
                        alert('✅ ' + (linkType === 'absolute' ? '绝对' : '相对') + '链接已复制到剪切板');
                    }).catch(() => {
                        alert('❌ 复制失败，请重试');
                    });
                } else {
                    // 备选方案：使用旧方法
                    const $temp = $('<textarea>').val(linkToCopy).appendTo('body').select();
                    try {
                        document.execCommand('copy');
                        alert('✅ ' + (linkType === 'absolute' ? '绝对' : '相对') + '链接已复制到剪切板');
                    } catch (err) {
                        alert('❌ 复制失败，请重试');
                    }
                    $temp.remove();
                }
            });
            
            $('#hygal-delete-trigger').on('click', function() {
                if(!currentTargetId) return;
                
                // 生成随机验证码
                const chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnopqrstuvwxyz';
                let verifyCode = '';
                for (let i = 0; i < 4; i++) {
                    verifyCode += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                const userInput = prompt('⚠️ 警告：您正在请求永久删除此图片！\n此操作不可逆，文件将从服务器彻底移除。\n\n请在下方输入验证码：' + verifyCode);
                
                if (userInput === verifyCode) {
                    if (confirm('✅ 验证通过。\n\n最后确认：真的要删除这张图片吗？')) {
                        const $delBtn = $(this);
                        $delBtn.css('opacity', '0.5').css('pointer-events', 'none');
                        
                        $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                            action: 'hygal_delete_asset',
                            img_id: currentTargetId,
                            _ajax_nonce: '<?php echo wp_create_nonce("hygal_min_nonce"); ?>'
                        }, function(res) {
                            if(res.success) {
                                alert('图片已成功删除。');
                                closeHyModal();
                                fetchImages(currentPage, false);
                            } else {
                                alert('删除失败：' + (res.data || '未知错误'));
                            }
                            $delBtn.css('opacity', '').css('pointer-events', '');
                        });
                    }
                } else if (userInput !== null) {
                    alert('❌ 验证码错误，取消删除。');
                }
            });
        }

        // 上传逻辑
        if ($('#hyupload-drop-zone').length) {
            let currentBlob = null;
            
            function formatBytes(b) {
                if (b < 1024) return b + ' B';
                if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
                return (b / 1048576).toFixed(1) + ' MB';
            }
            
            function performUpload() {
                if (!currentBlob || $('#hyupload-upload-btn').prop('disabled')) return;
                
                // 使用 FileReader 将 Blob 转换为 Base64（避免 PHP $_FILES 的临时目录问题）
                const reader = new FileReader();
                reader.onload = function(e) {
                    const base64Data = e.target.result;
                    const fd = new FormData();
                    fd.append('action', 'hyu_webp_upload');
                    fd.append('_nonce', '<?php echo wp_create_nonce("hyu_upload_nonce"); ?>');
                    fd.append('file_base64', base64Data);
                    fd.append('file_name', currentBlob.name || ('upload_' + Date.now() + '.jpg'));
                    fd.append('title', $('#hyupload-title').val());
                    fd.append('prefix', $('#f-category').val());
                    
                    $('#hyupload-loading').show(); 
                    $('#hyupload-upload-btn').prop('disabled', true);
                    
                    $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                        type: 'POST', 
                        data: fd, 
                        processData: false, 
                        contentType: false,
                        success: function(res) {
                            $('#hyupload-loading').hide(); 
                            $('#hyupload-upload-btn').prop('disabled', false);
                            if (res.success) { 
                                $('#hyupload-old').text(formatBytes(res.data.old_size));
                                $('#hyupload-new').text(formatBytes(res.data.new_size));
                                $('#hyupload-ratio').text(res.data.ratio + '%'); 
                                $('#hyupload-stats').fadeIn(); 
                                currentBlob = null; 
                                $('#hyupload-preview-img').hide(); 
                                $('#hyupload-drop-text').show(); 
                                $('#hyupload-controls').hide(); 
                                $('#hyupload-title').val("");
                            } else {
                                const errMsg = (typeof res.data === 'object' && res.data.message) ? res.data.message : (res.data || '未知错误');
                                alert('失败: ' + errMsg);
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#hyupload-loading').hide(); 
                            $('#hyupload-upload-btn').prop('disabled', false);
                            console.error('上传错误详情:', xhr.responseText, status, error);
                            alert('上传失败: 网络错误或服务器响应异常。\n\n请检查：\n1. 网络连接\n2. 服务器日志\n3. 文件大小（最大50MB）\n\n详情请打开浏览器控制台查看');
                        }
                    });
                };
                reader.readAsDataURL(currentBlob);
            }
            function handleImageFile(file) {
                if (!file || !file.type.startsWith('image/')) return;
                currentBlob = file;
                const r = new FileReader(); 
                r.onload = (e) => { 
                    $('#hyupload-preview-img').attr('src', e.target.result).show(); 
                    $('#hyupload-drop-text').hide(); 
                    $('#hyupload-controls').fadeIn(); 
                    $('#hyupload-stats').hide();
                    setTimeout(() => $('#hyupload-title').focus(), 200);
                };
                r.readAsDataURL(file);
            }
            $('#hyupload-drop-zone').on('click', () => $('#hyupload-file-input')[0].click());
            $('#hyupload-file-input').on('change', function() {
                handleImageFile(this.files[0]);
            });
            // 粘贴事件处理 - 在 document 级别监听，避免 div 无焦点问题
            $(document).on('paste', function(e) {
                const target = e.target;
                // 如果目标是输入框或文本区域，不拦截粘贴
                if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA') {
                    return;
                }
                const items = e.originalEvent.clipboardData.items;
                for (let item of items) {
                    if (item.type.startsWith('image/')) {
                        handleImageFile(item.getAsFile());
                        e.preventDefault();
                        break;
                    }
                }
            });
            // 拖拽事件处理
            $('#hyupload-drop-zone').on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('hover');
            }).on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('hover');
            }).on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('hover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleImageFile(files[0]);
                }
            });
            $('#hyupload-upload-btn').on('click', performUpload);
            // 上传框支持Enter提交
            $('#hyupload-title').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    performUpload();
                }
            });
        }

        // 编辑弹窗支持Enter提交
        $('#mod-title').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#hygal-save-trigger').click();
            }
        });
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
    $order_type = $_POST['order'];

    // 优化：一次查询获取所有需要的元数据，包括attached_file，减少函数调用
    $all_results = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, p.post_title, p.post_date, p.guid, pm_file.meta_value as attached_file, m_ord.meta_value as raw_order
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} m_cat ON p.ID = m_cat.post_id AND m_cat.meta_key = '_hygal_category'
        LEFT JOIN {$wpdb->postmeta} m_ord ON p.ID = m_ord.post_id AND m_ord.meta_key = '_hygal_order'
        LEFT JOIN {$wpdb->postmeta} pm_file ON p.ID = pm_file.post_id AND pm_file.meta_key = '_wp_attached_file'
        WHERE p.post_type = 'attachment' AND m_cat.meta_value = %s
    ", $prefix));
    
    $total_items = count($all_results);

    // 应用层RAND()排序 - 避免数据库RAND()低效问题
    if ($order_type === 'RAND') {
        shuffle($all_results);
    } else {
        // 非随机排序时应用排序逻辑
        usort($all_results, function($a, $b) use ($order_type) {
            // 权重排序：有权重的排前面
            $a_has_order = ($a->raw_order !== '' && $a->raw_order !== null) ? 1 : 0;
            $b_has_order = ($b->raw_order !== '' && $b->raw_order !== null) ? 1 : 0;
            if ($a_has_order !== $b_has_order) {
                return $b_has_order - $a_has_order;
            }
            
            // 权重值排序
            if ($a_has_order && $b_has_order) {
                $cmp = intval($b->raw_order) - intval($a->raw_order);
                if ($cmp !== 0) return $cmp;
            }
            
            // 日期排序
            $date_a = strtotime($a->post_date);
            $date_b = strtotime($b->post_date);
            return $order_type === 'ASC' ? $date_a - $date_b : $date_b - $date_a;
        });
    }

    // 分页处理
    $offset = ($paged - 1) * $ppp;
    $results = array_slice($all_results, $offset, $ppp);

    $upload_dir = wp_upload_dir();
    $base_url = $upload_dir['baseurl'];
    
    $html = '';
    foreach ($results as $post) {
        // 优化：直接使用已查询的guid和attached_file数据，避免多次WordPress函数调用
        $url = !empty($post->guid) ? $post->guid : $base_url . '/';
        
        // 获取文件大小：先尝试使用attached_file构建路径
        $size_str = '未知';
        if (!empty($post->attached_file)) {
            $full_path = $upload_dir['basedir'] . '/' . $post->attached_file;
            if (file_exists($full_path)) {
                $size_str = size_format(filesize($full_path));
            }
        }
        
        $date_str = wp_date('Y-m-d', strtotime($post->post_date));
        $has_order = ($post->raw_order !== '' && $post->raw_order !== null) ? 'has-order' : '';
        $html .= '<div class="hygal-item '.$has_order.'" data-id="'.$post->ID.'" data-raw-order="'.esc_attr($post->raw_order).'" data-current-prefix="'.esc_attr($prefix).'" data-size="'.$size_str.'" data-date="'.$date_str.'"><div class="hygal-img-wrapper"><img src="'.esc_url($url).'" loading="lazy"></div><div class="hygal-title">'.esc_html($post->post_title).'</div></div>';
    }
    wp_send_json_success(['html' => $html, 'total_items' => (int)$total_items, 'total_pages' => ceil($total_items / $ppp)]);
}

add_action('wp_ajax_hygal_update_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => '权限不足']);
        return;
    }
    $id = intval($_POST['img_id']);
    if (!$id) {
        wp_send_json_error(['message' => '无效ID']);
        return;
    }
    
    // 权重字段：有值则更新，空值则删除（清除权重）
    $order_val = sanitize_text_field($_POST['order_val']);
    if ($order_val !== '') {
        update_post_meta($id, '_hygal_order', $order_val);
    } else {
        delete_post_meta($id, '_hygal_order');
    }
    
    update_post_meta($id, '_hygal_category', sanitize_text_field($_POST['new_prefix']));
    wp_update_post(['ID' => $id, 'post_title' => sanitize_text_field($_POST['new_pure_title'])]);
    wp_send_json_success();
});

add_action('wp_ajax_hygal_delete_asset', function() {
    check_ajax_referer('hygal_min_nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => '权限不足']);
        return;
    }
    $img_id = intval($_POST['img_id']);
    if (!$img_id) {
        wp_send_json_error(['message' => '无效ID']);
        return;
    }
    
    if (wp_delete_attachment($img_id, true)) {
        wp_send_json_success(['message' => '删除成功']);
    } else {
        wp_send_json_error(['message' => '删除失败，可能文件不存在或权限问题']);
    }
});

add_action('wp_ajax_hyu_webp_upload', function() {
    check_ajax_referer('hyu_upload_nonce', '_nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => '无权操作']);
        return;
    }
    
    // 从 Base64 数据接收（不再使用 $_FILES，避免系统临时目录问题）
    if (empty($_POST['file_base64'])) {
        wp_send_json_error(['message' => '❌ 未收到文件数据']);
        return;
    }
    
    $base64_data = sanitize_text_field($_POST['file_base64']);
    $file_name = sanitize_file_name($_POST['file_name'] ?? 'upload_' . time() . '.jpg');
    
    // 解析 Base64 数据
    if (strpos($base64_data, ',') !== false) {
        list($type, $base64_data) = explode(',', $base64_data);
    }
    $binary_data = @base64_decode($base64_data);
    
    if (empty($binary_data)) {
        wp_send_json_error(['message' => '❌ Base64 数据无效']);
        return;
    }
    
    // 文件大小限制 (最大 50MB)
    $max_size = 50 * 1024 * 1024;
    if (strlen($binary_data) > $max_size) {
        wp_send_json_error(['message' => '文件过大，请压缩后上传（最大50MB，当前：' . round(strlen($binary_data) / 1024 / 1024, 1) . 'MB）']);
        return;
    }
    
    if (strlen($binary_data) == 0) {
        wp_send_json_error(['message' => '❌ 文件为空。请检查是否选择了正确的图片文件']);
        return;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    
    @ini_set('memory_limit', '512M');
    
    // 使用 WordPress 上传目录作为工作目录
    $upload_dir = wp_upload_dir();
    $work_dir = $upload_dir['basedir'] . '/hygal-temp';
    
    // 确保工作目录存在
    if (!is_dir($work_dir)) {
        if (!@mkdir($work_dir, 0755, true)) {
            wp_send_json_error(['message' => '❌ 无法创建工作目录。请检查 WordPress 上传文件夹权限']);
            return;
        }
    }
    
    if (!is_writable($work_dir)) {
        wp_send_json_error(['message' => '❌ 工作目录不可写。请检查权限']);
        return;
    }
    
    $prefix = sanitize_text_field($_POST['prefix']);
    $raw_title = sanitize_text_field($_POST['title']);
    $ts = date('YmdHis');
    $wp_title = !empty($raw_title) ? $raw_title : $ts;
    
    // 将 Base64 数据保存为临时文件
    $local_tmp = $work_dir . '/' . uniqid('hygal_') . '_upload_' . md5($ts) . '_temp.jpg';
    if (file_put_contents($local_tmp, $binary_data) === false) {
        wp_send_json_error(['message' => '❌ 无法保存上传的数据。请检查磁盘空间和权限']);
        return;
    }
    
    $old_size = strlen($binary_data);
    $target = $local_tmp . '.webp';
    $success = false;
    
    // 验证文件是否为有效图片
    $info = @getimagesize($local_tmp);
    if (!$info) {
        @unlink($local_tmp);
        wp_send_json_error(['message' => '❌ 图像处理失败，请检查图片格式']);
        return;
    }
    
    // 方法1：尝试使用 Imagick (更高效)
    if (extension_loaded('imagick')) {
        try {
            $imagick = new Imagick($local_tmp);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(80);
            $imagick->writeImage($target);
            $imagick->destroy();
            $success = true;
        } catch (Exception $e) {
            // 如果 Imagick 失败，尝试 GD 库
        }
    }
    
    // 方法2：使用 GD 库 (备选)
    if (!$success) {
        $img = null;
        if ($info['mime'] == 'image/jpeg') {
            $img = @imagecreatefromjpeg($local_tmp);
        } elseif ($info['mime'] == 'image/png') {
            $img = @imagecreatefrompng($local_tmp);
        } elseif ($info['mime'] == 'image/gif') {
            $img = @imagecreatefromgif($local_tmp);
        } elseif ($info['mime'] == 'image/webp') {
            $img = @imagecreatefromwebp($local_tmp);
        }
        
        if ($img === false) {
            @unlink($local_tmp);
            wp_send_json_error(['message' => '❌ 图像处理失败，请检查图片格式']);
            return;
        }
        
        // PNG特殊处理
        if ($info['mime'] == 'image/png') {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }
        
        if (!imagewebp($img, $target, 80)) {
            imagedestroy($img);
            @unlink($local_tmp);
            wp_send_json_error(['message' => 'WebP转换失败，请重试']);
            return;
        }
        imagedestroy($img);
        $success = true;
    }
    
    // 清理原始临时文件
    if (file_exists($local_tmp)) {
        @unlink($local_tmp);
    }
    
    if (!$success || !file_exists($target)) {
        wp_send_json_error(['message' => '文件生成失败']);
        return;
    }
    
    $new_size = filesize($target);
    $ratio = ($old_size > 0) ? round((1 - ($new_size / $old_size)) * 100, 1) : 0;
    
    add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 999);
    add_filter('big_image_size_threshold', '__return_false', 999);
    
    $id = media_handle_sideload(['name' => $ts . '.webp', 'tmp_name' => $target], 0);
    
    // 立即清理 WebP 文件
    if (file_exists($target)) {
        @unlink($target);
    }
    
    // 立即清理所有临时文件
    $files = @glob($work_dir . '/hygal_*');
    if (is_array($files)) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
    
    if (is_wp_error($id)) {
        wp_send_json_error(['message' => '上传到媒体库失败：' . $id->get_error_message()]);
        return;
    }
    
    wp_update_post(['ID' => $id, 'post_title' => $wp_title]);
    update_post_meta($id, '_hygal_category', $prefix);
    
    wp_send_json_success([
        'old_size' => $old_size,
        'new_size' => $new_size,
        'ratio' => $ratio
    ]);
});
?>