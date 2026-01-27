<?php
/**
 * Plugin Name: 高级WebP上传器
 * Description: 全宽布局，支持持久化前缀标识，实时显示压缩率数据，内存优化。
 */

add_action('admin_menu', function() {
    add_management_page('高级WebP上传器', '高级WebP上传器', 'manage_options', 'webp-uploader-pro', 'webp_pro_render_page');
});

function webp_pro_render_page() {
    ?>
    <style>
        .webp-slim-container { margin: 10px 20px 0 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        
        /* 上传框固定高度 */
        #drop-zone { border: 2px dashed #cbd5e0; height: 80px; text-align: center; color: #4a5568; cursor: pointer; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #drop-zone.hover { border-color: #2271b1; background: #ebf8ff; }
        #local-preview { height: 60px; width: auto; max-width: 150px; object-fit: contain; border-radius: 4px; display: none; }
        
        /* 状态统计条 */
        #stats-bar { display: none; margin: 15px 0; padding: 10px 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 13px; color: #166534; }
        .stats-item { margin-right: 15px; font-weight: 600; }
        .stats-highlight { color: #15803d; text-decoration: underline; }

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
            <h1>高级WebP上传器</h1>
            
            <div id="drop-zone">
                <img id="local-preview" src="">
                <div id="drop-hint">点击、拖拽或粘贴图片</div>
                <input type="file" id="file-input" style="display:none" accept="image/*">
            </div>

            <div id="stats-bar">
                <span>✅ 上传成功！</span>
                <span class="stats-item">原大小: <span id="size-old" class="stats-highlight"></span></span>
                <span class="stats-item">压缩后: <span id="size-new" class="stats-highlight"></span></span>
                <span class="stats-item">节省率: <span id="size-ratio" class="stats-highlight"></span></span>
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
                        <label>请设置标签 (HTML Title)</label>
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
    
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function executeUpload() {
        if (!currentBlob || jQuery('#upload-btn').prop('disabled')) return;
        const formData = new FormData();
        formData.append('action', 'webp_pro_upload');
        formData.append('_ajax_nonce', '<?php echo wp_create_nonce("webp_pro_nonce"); ?>');
        formData.append('webp_file', currentBlob);
        formData.append('img_title', document.getElementById('img-title-input').value);
        formData.append('img_prefix', document.getElementById('img-prefix-input').value);

        jQuery('#loading').show();
        jQuery('#upload-btn').prop('disabled', true);
        jQuery('#stats-bar').hide();

        jQuery.ajax({
            url: ajaxurl, type: 'POST', data: formData, processData: false, contentType: false,
            success: function(res) {
                jQuery('#loading').hide();
                jQuery('#upload-btn').prop('disabled', false);
                if (res.success) {
                    uploadedUrl = res.data.url;
                    
                    // 显示统计数据
                    document.getElementById('size-old').innerText = formatBytes(res.data.old_size);
                    document.getElementById('size-new').innerText = formatBytes(res.data.new_size);
                    document.getElementById('size-ratio').innerText = res.data.ratio + '%';
                    jQuery('#stats-bar').fadeIn();

                    document.getElementById('step-html').style.display = 'block';
                    document.getElementById('html-title-opt').value = res.data.raw_title;
                    document.getElementById('img-title-input').value = ""; 
                    generateTag();
                } else { alert(res.data); }
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
            jQuery('#stats-bar').hide();
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

add_action('wp_ajax_webp_pro_upload', function() {
    check_ajax_referer('webp_pro_nonce');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    @ini_set('memory_limit', '512M'); 

    $tmp_file = $_FILES['webp_file']['tmp_name'];
    $old_size = filesize($tmp_file); // 记录原始大小
    
    $pure_ts = date('YmdHis') . rand(100, 999);
    $raw_title = sanitize_text_field($_POST['img_title']);
    $prefix = sanitize_text_field($_POST['img_prefix']);

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
    $new_size = filesize($final_path); // 记录压缩后大小
    $ratio = round((1 - ($new_size / $old_size)) * 100, 2); // 计算比例

    $final_name = $pure_ts . ($converted ? '.webp' : image_type_to_extension($info[2]));

    $no_thumbs = function($s){ return []; };
    add_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);
    add_filter('big_image_size_threshold', '__return_false', 999);
    
    $id = media_handle_sideload(['name' => $final_name, 'tmp_name' => $final_path], 0);
    if (!is_wp_error($id)) wp_update_post(['ID' => $id, 'post_title' => $final_wp_title]);

    if ($converted && file_exists($tmp_file)) @unlink($tmp_file);
    remove_filter('intermediate_image_sizes_advanced', $no_thumbs, 999);

    if (is_wp_error($id)) wp_send_json_error($id->get_error_message());
    
    wp_send_json_success([
        'url' => wp_get_attachment_url($id), 
        'raw_title' => $raw_title,
        'old_size' => $old_size,
        'new_size' => $new_size,
        'ratio' => $ratio
    ]);
});