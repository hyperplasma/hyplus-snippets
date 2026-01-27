<?php
/**
 * Plugin Name: WebP Ultra Converter Pro (Minimal Tags)
 * Description: 极简文件名命名，本地预览，自定义 Title/尺寸，生成无 alt 的 img 标签。
 */

add_action('admin_menu', function() {
    add_management_page('WebP 上传器', 'WebP 上传器', 'manage_options', 'webp-uploader-minimal', 'webp_min_render_page');
});

function webp_min_render_page() {
    ?>
    <style>
        .webp-pro-container { max-width: 800px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        #drop-zone { border: 2px dashed #cbd5e0; padding: 30px; text-align: center; color: #4a5568; cursor: pointer; border-radius: 10px; background: #f8fafc; min-height: 150px; display: flex; align-items: center; justify-content: center; position: relative; }
        #drop-zone.hover { border-color: #3182ce; background: #ebf8ff; }
        #local-preview { max-height: 200px; max-width: 100%; border-radius: 4px; display: none; }
        .step-section { margin-top: 20px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; display: none; }
        .input-group { margin-bottom: 12px; }
        .input-group label { display: block; font-size: 13px; margin-bottom: 4px; color: #718096; }
        .input-group input { width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; }
        .copy-box { background: #2d3748; color: #edf2f7; padding: 12px; border-radius: 6px; position: relative; font-family: 'Consolas', monospace; font-size: 12px; margin-top: 10px; word-break: break-all; min-height: 40px; }
        .copy-btn { position: absolute; right: 8px; top: 8px; background: #3182ce; color: white; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #3182ce; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: bold; }
        #loading { display:none; text-align: center; color: #3182ce; margin: 10px 0; }
    </style>

    <div class="wrap">
        <div class="webp-pro-container">
            <h2>WebP 极速转换 Pro</h2>
            
            <div id="drop-zone">
                <div id="drop-hint">拖拽、粘贴或点击选择图片</div>
                <img id="local-preview" src="">
                <input type="file" id="file-input" style="display:none" accept="image/*">
            </div>

            <div id="step-upload" class="step-section">
                <div class="input-group">
                    <label>后台记录标题 (不填则用时间戳)</label>
                    <input type="text" id="img-title-input">
                </div>
                <button id="upload-btn" class="btn-primary">上传并转换</button>
            </div>

            <div id="loading">🚀 处理中...</div>

            <div id="step-html" class="step-section">
                <div style="display: flex; gap: 10px;">
                    <div class="input-group" style="flex:2;">
                        <label>HTML Title</label>
                        <input type="text" id="html-title-opt">
                    </div>
                    <div class="input-group" style="flex:1;">
                        <label>宽度</label>
                        <input type="text" id="html-width-opt">
                    </div>
                    <div class="input-group" style="flex:1;">
                        <label>高度</label>
                        <input type="text" id="html-height-opt">
                    </div>
                </div>
                <div class="copy-box">
                    <code id="final-code"></code>
                    <button class="copy-btn" id="do-copy">复制标签</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentBlob = null;
    let uploadedUrl = "";
    
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const localPreview = document.getElementById('local-preview');
    const dropHint = document.getElementById('drop-hint');

    function showPreview(file) {
        if (!file) return;
        currentBlob = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            localPreview.src = e.target.result;
            localPreview.style.display = 'block';
            dropHint.style.display = 'none';
            document.getElementById('step-upload').style.display = 'block';
            document.getElementById('step-html').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    dropZone.onclick = () => fileInput.click();
    dropZone.ondragover = (e) => { e.preventDefault(); dropZone.classList.add('hover'); };
    dropZone.ondragleave = () => dropZone.classList.remove('hover');
    dropZone.ondrop = (e) => { e.preventDefault(); dropZone.classList.remove('hover'); showPreview(e.dataTransfer.files[0]); };
    document.onpaste = (e) => {
        const items = e.clipboardData.items;
        for (let i = 0; i < items.length; i++) { if (items[i].type.indexOf("image") !== -1) showPreview(items[i].getAsFile()); }
    };
    fileInput.onchange = (e) => showPreview(e.target.files[0]);

    document.getElementById('upload-btn').onclick = function() {
        const formData = new FormData();
        formData.append('action', 'webp_min_upload');
        formData.append('_ajax_nonce', '<?php echo wp_create_nonce("webp_min_nonce"); ?>');
        formData.append('webp_file', currentBlob);
        formData.append('img_title', document.getElementById('img-title-input').value);

        jQuery('#loading').show();
        this.disabled = true;

        jQuery.ajax({
            url: ajaxurl, type: 'POST', data: formData, processData: false, contentType: false,
            success: function(res) {
                jQuery('#loading').hide();
                document.getElementById('upload-btn').disabled = false;
                if (res.success) {
                    uploadedUrl = res.data.url;
                    document.getElementById('step-html').style.display = 'block';
                    document.getElementById('html-title-opt').value = res.data.title;
                    generateTag();
                }
            }
        });
    };

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
            this.innerText = "OK!";
            setTimeout(() => this.innerText = old, 1000);
        });
    };
    </script>
    <?php
}

add_action('wp_ajax_webp_min_upload', function() {
    check_ajax_referer('webp_min_nonce');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    @ini_set('memory_limit', '512M');
    $tmp_file = $_FILES['webp_file']['tmp_name'];
    $pure_ts = date('YmdHis') . rand(100, 999);
    $img_title = !empty($_POST['img_title']) ? sanitize_text_field($_POST['img_title']) : $pure_ts;

    $info = @getimagesize($tmp_file);
    $target_webp = $tmp_file . '.webp';
    $converted = false;

    if (function_exists('imagewebp')) {
        $img = null;
        if ($info['mime'] == 'image/jpeg') $img = @imagecreatefromjpeg($tmp_file);
        elseif ($info['mime'] == 'image/png') {
            $img = @imagecreatefrompng($tmp_file);
            if($img){ imagepalettetotruecolor($img); imagealphablending($img, true); imagesavealpha($img, true); }
        }
        if ($img && imagewebp($img, $target_webp, 80)) { $converted = true; imagedestroy($img); }
    }

    $final_path = $converted ? $target_webp : $tmp_file;
    $final_name = $pure_ts . ($converted ? '.webp' : image_type_to_extension($info[2]));

    $no_thumbs = function($s){ return []; };
    add_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);
    add_filter('big_image_size_threshold', '__return_false', 999);
    
    $id = media_handle_sideload(['name' => $final_name, 'tmp_name' => $final_path], 0);
    if (!is_wp_error($id)) wp_update_post(['ID' => $id, 'post_title' => $img_title]);

    if ($converted) @unlink($tmp_file);
    remove_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);

    wp_send_json_success(['url' => wp_get_attachment_url($id), 'title' => $img_title]);
});