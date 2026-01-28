<?php
/**
 * Plugin Name: HyUploader WebP - 分类联动版
 * Description: 前缀改为下拉选择，支持通过短代码 tags 参数定义分类，支持回车键快捷上传。默认 tag 为“图”。
 * Shortcode: [hyuploader_webp tags="霞,虹,雾,hyplus"]
 */

add_shortcode('hyuploader_webp', 'hy_uploader_webp_shortcode');

function hy_uploader_webp_shortcode($atts) {
    // 安全校验
    if (!current_user_can('upload_files')) {
        return '<p style="text-align:center; color:#999; padding:20px;">🔒 权限不足，请登录后操作。</p>';
    }

    // 解析短代码参数
    $atts = shortcode_atts(['tags' => ''], $atts);
    $tag_list = array_filter(array_map('trim', explode(',', $atts['tags'])));

    // 逻辑修复：如果没传 tags，默认赋值为“图”
    if (empty($tag_list)) {
        $tag_list = ['图'];
    }

    ob_start();
    ?>
    <style>
        .hyu-container { margin: 20px 0; text-align: center; font-family: -apple-system, sans-serif; }
        
        /* 1. 拖拽/点击预览区 */
        #hyu-drop-zone { 
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
            margin-bottom: 15px;
            position: relative;
        }
        #hyu-drop-zone:hover { border-color: #43a5f5; background: #f0f9ff; }
        #hyu-drop-zone.hover { border-color: #43a5f5; background: #f0f9ff; }
        
        #hyu-preview-img { max-height: 80px; border-radius: 6px; display: none; margin-right: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .hyu-hint { color: #64748b; font-size: 15px; font-weight: 500; pointer-events: none; }

        /* 2. 统计条 */
        #hyu-stats { display: none; margin-bottom: 15px; padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; color: #166534; }
        .hyu-stat-tag { font-weight: 700; color: #15803d; text-decoration: underline; margin: 0 4px; }

        /* 3. 输入框与控制行 */
        .hyu-row { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; margin-bottom: 15px; }
        
        .hyu-input {
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
        .hyu-input:focus { border-color: #43a5f5; }
        
        #hyu-prefix { min-width: 100px; cursor: pointer; }
        #hyu-title { flex: 1; min-width: 180px; }

        /* 4. 按钮 */
        .hyu-btn-submit {
            height: 40px;
            padding: 0 35px !important;
            cursor: pointer;
            font-weight: 600;
        }
        
        #hyu-loading { display: none; color: #2271b1; font-weight: bold; margin: 10px 0; }
        
        .hytool-version {
            margin-top: 10px;
            color: #aaa;
            font-size: 15px;
            font-family: inherit;
            user-select: none;
            letter-spacing: 1px;
            background: transparent;
            z-index: 2;
            text-align: right;
            width: 100%;
        }
    </style>

    <div class="hyu-container">
        <div class="hyplus-nav-section" style="padding: 20px;">
            <div id="hyu-drop-zone">
                <img id="hyu-preview-img" src="">
                <div id="hyu-drop-text" class="hyu-hint">点击、拖拽或粘贴图片到此处</div>
                <input type="file" id="hyu-file-input" style="display:none" accept="image/*">
            </div>

            <div id="hyu-stats" class="hyplus-unselectable">
                <span>✅ 已成功存入媒体库！</span>
                <span>原大小: <span id="hyu-old" class="hyu-stat-tag"></span></span>
                <span>压缩后: <span id="hyu-new" class="hyu-stat-tag"></span></span>
                <span>节省: <span id="hyu-ratio" class="hyu-stat-tag"></span></span>
            </div>

            <div id="hyu-controls" class="hyplus-unselectable" style="display:none;">
                <div class="hyu-row">
                    <select id="hyu-prefix" class="hyu-input">
                        <?php foreach ($tag_list as $tag): ?>
                            <option value="<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" id="hyu-title" class="hyu-input" placeholder="输入描述标题...">
                    <button id="hyu-upload-btn" class="hyplus-nav-link hyu-btn-submit">转换并上传</button>
                </div>
            </div>

            <div id="hyu-loading" class="hyplus-unselectable">🚀 正在处理 WebP 转换并存储...</div>
        </div>
        <div class="hytool-version hyplus-unselectable">HyUploader WebP v0.1.8</div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        let currentBlob = null;

        function formatBytes(b) {
            if (b < 1024) return b + ' B';
            if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
            return (b / 1048576).toFixed(1) + ' MB';
        }

        function performUpload() {
            if (!currentBlob || $('#hyu-upload-btn').prop('disabled')) return;

            const fd = new FormData();
            fd.append('action', 'hyu_webp_upload');
            fd.append('_nonce', '<?php echo wp_create_nonce("hyu_upload_nonce"); ?>');
            fd.append('file', currentBlob);
            fd.append('title', $('#hyu-title').val());
            fd.append('prefix', $('#hyu-prefix').val());

            $('#hyu-loading').show();
            $('#hyu-upload-btn').prop('disabled', true).css('opacity', '0.6');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#hyu-loading').hide();
                    $('#hyu-upload-btn').prop('disabled', false).css('opacity', '1');
                    if (res.success) {
                        $('#hyu-old').text(formatBytes(res.data.old_size));
                        $('#hyu-new').text(formatBytes(res.data.new_size));
                        $('#hyu-ratio').text(res.data.ratio + '%');
                        $('#hyu-stats').fadeIn();
                        
                        $('#hyu-title').val(""); 
                        $('#hyu-preview-img').hide();
                        $('#hyu-drop-text').show();
                        $('#hyu-controls').hide();
                        currentBlob = null;
                    } else { alert('失败: ' + res.data); }
                }
            });
        }

        $('#hyu-drop-zone').on('click', () => $('#hyu-file-input').trigger('click'));
        $('#hyu-preview-img').on('click', (e) => { e.stopPropagation(); $('#hyu-file-input').trigger('click'); });

        function showPreview(file) {
            if (!file || !file.type.startsWith('image/')) return;
            currentBlob = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                $('#hyu-preview-img').attr('src', e.target.result).show();
                $('#hyu-drop-text').hide();
                $('#hyu-controls').fadeIn();
                $('#hyu-stats').hide();
                setTimeout(() => $('#hyu-title').focus(), 200);
            };
            reader.readAsDataURL(file);
        }

        $('#hyu-file-input').on('change', function() { showPreview(this.files[0]); });
        
        $('#hyu-drop-zone').on('dragover', function(e){ e.preventDefault(); $(this).addClass('hover'); });
        $('#hyu-drop-zone').on('dragleave', function(){ $(this).removeClass('hover'); });
        $('#hyu-drop-zone').on('drop', function(e){
            e.preventDefault();
            $(this).removeClass('hover');
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) showPreview(files[0]);
        });

        $(document).on('paste', (e) => {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let item of items) { if (item.type.indexOf("image") !== -1) showPreview(item.getAsFile()); }
        });

        $('#hyu-upload-btn').on('click', function(e) { e.preventDefault(); performUpload(); });
        $('#hyu-title').on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); performUpload(); } });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 后端处理逻辑
 */
add_action('wp_ajax_hyu_webp_upload', 'hy_uploader_webp_ajax_handler');

function hy_uploader_webp_ajax_handler() {
    check_ajax_referer('hyu_upload_nonce', '_nonce');
    if (!current_user_can('upload_files')) wp_send_json_error('无权操作');

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    @ini_set('memory_limit', '512M');
    if (!isset($_FILES['file'])) wp_send_json_error('没有接收到文件');

    $file = $_FILES['file'];
    $tmp_path = $file['tmp_name'];
    $old_size = filesize($tmp_path);
    
    $prefix = sanitize_text_field($_POST['prefix']);
    $raw_title = sanitize_text_field($_POST['title']);
    $ts = date('YmdHis');
    
    if (!empty($prefix) && !empty($raw_title)) {
        $wp_title = $prefix . '-' . $raw_title;
    } elseif (!empty($prefix)) {
        $wp_title = $prefix . '-' . $ts;
    } else {
        $wp_title = !empty($raw_title) ? $raw_title : $ts;
    }

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
    }

    if ($is_converted && file_exists($tmp_path)) @unlink($tmp_path);
    if (is_wp_error($attach_id)) wp_send_json_error($attach_id->get_error_message());

    wp_send_json_success([
        'old_size' => $old_size,
        'new_size' => $new_size,
        'ratio' => $ratio
    ]);
}
?>