<?php
/**
 * Plugin Name: WebP Ultra Converter Pro (Prefix Support)
 * Description: 全宽布局，支持持久化前缀标识，Enter 键上传，内存优化。
 */

add_action('admin_menu', function() {
    add_management_page('WebP 上传器', 'WebP 上传器', 'manage_options', 'webp-uploader-prefix', 'webp_prefix_render_page');
});

function webp_prefix_render_page() {
    ?>
    <style>
        .webp-slim-container { margin: 10px 20px 0 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        #drop-zone { border: 2px dashed #cbd5e0; height: 80px; text-align: center; color: #4a5568; cursor: pointer; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #drop-zone.hover { border-color: #2271b1; background: #ebf8ff; }
        #local-preview { height: 60px; width: auto; max-width: 150px; object-fit: contain; border-radius: 4px; display: none; }
        .step-section { margin-top: 15px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; display: none; }
        .input-row { display: flex; gap: 12px; align-items: flex-end; margin-bottom: 10px; }
        .input-group { flex: 1; }
        .input-group label { display: block; font-size: 12px; margin-bottom: 4px; color: #64748b; font-weight: 600; }
        .input-group input { width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; }
        .copy-box { background: #1e293b; color: #f1f5f9; padding: 15px; border-radius: 4px; font-family: 'Consolas', monospace; font-size: 12px; margin-top: 10px; word-break: break-all; }
        .btn-center-wrapper { display: flex; justify-content: center; margin-top: 15px; }
        .copy-btn { background: #3b82f6; color: white; border: none; padding: 10px 30px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; }
        #loading { display:none; color: #2271b1; font-weight: bold; margin: 10px 0; }
        .btn-primary { background: #2271b1; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold; height: 34px; }
    </style>

    <div class="wrap">
        <div class="webp-slim-container">
            <h1>WebP 极速上传</h1>
            
            <div id="drop-zone">
                <img id="local-preview" src="">
                <div id="drop-hint">点击、拖拽或粘贴图片</div>
                <input type="file" id="file-input" style="display:none" accept="image/*">
            </div>

            <div id="step-upload" class="step-section">
                <div class="input-row">
                    <div class="input-group" style="flex: 2;">
                        <label>图片标题</label>
                        <input type="text" id="img-title-input" placeholder="具体描述（上传后自动清空）">
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>前缀标识</label>
                        <input type="text" id="img-prefix-input" placeholder="例如：霞（不会清空）">
                    </div>
                    <button id="upload-btn" class="btn-primary">上传转换</button>
                </div>
            </div>

            <div id="loading">✨ 处理中...</div>

            <div id="step-html" class="step-section">
                <div class="input-row">
                    <div class="input-group">
                        <label>上传成功，请设置标签 (HTML Title)</label>
                        <input type="text" id="html-title-opt">
                    </div>
                    <div class="input-group" style="max-width: 80px;">
                        <label>宽度</label>
                        <input type="text" id="html-width-opt">
                    </div>
                    <div class="input-group" style="max-width: 80px;">
                        <label>高度</label>
                        <input type="text" id="html-height-opt">
                    </div>
                </div>
                <div class="copy-box"><code id="final-code"></code></div>
                <div class="btn-center-wrapper"><button class="copy-btn" id="do-copy">复制 img 标签</button></div>
            </div>
        </div>
    </div>

    <script>
    let currentBlob = null;
    let uploadedUrl = "";
    
    function executeUpload() {
        if (!currentBlob || jQuery('#upload-btn').prop('disabled')) return;
        const formData = new FormData();
        formData.append('action', 'webp_prefix_upload');
        formData.append('_ajax_nonce', '<?php echo wp_create_nonce("webp_prefix_nonce"); ?>');
        formData.append('webp_file', currentBlob);
        formData.append('img_title', document.getElementById('img-title-input').value);
        formData.append('img_prefix', document.getElementById('img-prefix-input').value);

        jQuery('#loading').show();
        jQuery('#upload-btn').prop('disabled', true);

        jQuery.ajax({
            url: ajaxurl, type: 'POST', data: formData, processData: false, contentType: false,
            success: function(res) {
                jQuery('#loading').hide();
                jQuery('#upload-btn').prop('disabled', false);
                if (res.success) {
                    uploadedUrl = res.data.url;
                    document.getElementById('step-html').style.display = 'block';
                    // HTML Title 仅显示用户填写的具体标题部分（或自动生成的部分）
                    document.getElementById('html-title-opt').value = res.data.raw_title;
                    document.getElementById('img-title-input').value = ""; 
                    generateTag();
                } else { alert(res.data); }
            },
            error: function() {
                jQuery('#loading').hide();
                jQuery('#upload-btn').prop('disabled', false);
                alert("上传失败，请检查内存或文件格式。");
            }
        });
    }

    function showPreview(file) {
        if (!file) return;
        currentBlob = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('local-preview').src = e.target.result;
            document.getElementById('local-preview').style.display = 'block';
            document.getElementById('drop-hint').style.display = 'none';
            document.getElementById('step-upload').style.display = 'block';
            document.getElementById('step-html').style.display = 'none';
            setTimeout(() => document.getElementById('img-title-input').focus(), 100);
        };
        reader.readAsDataURL(file);
    }

    const dz = document.getElementById('drop-zone');
    dz.onclick = () => document.getElementById('file-input').click();
    document.getElementById('file-input').onchange = (e) => showPreview(e.target.files[0]);
    dz.ondragover = (e) => { e.preventDefault(); dz.classList.add('hover'); };
    dz.ondragleave = () => dz.classList.remove('hover');
    dz.ondrop = (e) => { e.preventDefault(); dz.classList.remove('hover'); showPreview(e.dataTransfer.files[0]); };
    document.onpaste = (e) => {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) { if (items[i].type.indexOf("image") !== -1) showPreview(items[i].getAsFile()); }
    };

    document.getElementById('upload-btn').onclick = executeUpload;
    document.getElementById('img-title-input').onkeydown = function(e) { if (e.key === 'Enter') { e.preventDefault(); executeUpload(); } };
    document.getElementById('img-prefix-input').onkeydown = function(e) { if (e.key === 'Enter') { e.preventDefault(); executeUpload(); } };

    ['html-title-opt', 'html-width-opt', 'html-height-opt'].forEach(id => {
        document.getElementById(id).oninput = generateTag;
    });

    function generateTag() {
        const title = document.getElementById('html-title-opt').value;
        const w = document.getElementById('html-width-opt').value;
        const h = document.getElementById('html-height-opt').value;
        let tag = `<img src="${uploadedUrl}"`;
        if (title) tag += ` title="${title}"`;
        if (w) tag += ` width="${w}"`;
        if (h) tag += ` height="${h}"`;
        tag += `>`;
        document.getElementById('final-code').innerText = tag;
    }

    document.getElementById('do-copy').onclick = function() {
        navigator.clipboard.writeText(document.getElementById('final-code').innerText).then(() => {
            const old = this.innerText;
            this.innerText = "已成功复制到剪贴板！";
            this.style.background = "#10b981";
            setTimeout(() => { this.innerText = old; this.style.background = "#3b82f6"; }, 1000);
        });
    };
    </script>
    <?php
}

add_action('wp_ajax_webp_prefix_upload', function() {
    check_ajax_referer('webp_prefix_nonce');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    @ini_set('memory_limit', '512M'); 

    $tmp_file = $_FILES['webp_file']['tmp_name'];
    $pure_ts = date('YmdHis') . rand(100, 999);
    
    $raw_title = sanitize_text_field($_POST['img_title']);
    $prefix = sanitize_text_field($_POST['img_prefix']);

    // 构建媒体库最终标题
    if (!empty($prefix) && !empty($raw_title)) {
        $final_wp_title = $prefix . '-' . $raw_title;
    } elseif (!empty($prefix)) {
        $final_wp_title = $prefix . '-' . $pure_ts;
    } else {
        $final_wp_title = !empty($raw_title) ? $raw_title : $pure_ts;
    }

    $info = @getimagesize($tmp_file);
    $target_webp = $tmp_file . '.webp';
    $converted = false;

    if (function_exists('imagewebp')) {
        $img = null;
        if ($info['mime'] == 'image/jpeg') $img = @imagecreatefromjpeg($tmp_file);
        elseif ($info['mime'] == 'image/png') $img = @imagecreatefrompng($tmp_file);
        if ($img) {
            if ($info['mime'] == 'image/png') { imagepalettetotruecolor($img); imagealphablending($img, true); imagesavealpha($img, true); }
            if (@imagewebp($img, $target_webp, 80)) $converted = true;
            imagedestroy($img);
        }
    }

    $final_path = $converted ? $target_webp : $tmp_file;
    $final_name = $pure_ts . ($converted ? '.webp' : image_type_to_extension($info[2]));

    $no_thumbs = function($s){ return []; };
    add_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);
    add_filter('big_image_size_threshold', '__return_false', 999);
    
    $id = media_handle_sideload(['name' => $final_name, 'tmp_name' => $final_path], 0);
    if (!is_wp_error($id)) wp_update_post(['ID' => $id, 'post_title' => $final_wp_title]);

    if ($converted && file_exists($tmp_file)) @unlink($tmp_file);
    remove_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);

    if (is_wp_error($id)) wp_send_json_error($id->get_error_message());
    
    // 返回 raw_title 供 HTML 标签使用，确保标签里不带前缀
    wp_send_json_success([
        'url' => wp_get_attachment_url($id), 
        'raw_title' => !empty($raw_title) ? $raw_title : ""
    ]);
});