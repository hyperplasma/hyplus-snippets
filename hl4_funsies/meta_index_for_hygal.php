<?php
/**
 * Plugin Name: HyGal 媒体库灾后重建 (v5.0 - 零缩略图版)
 * Description: 1. 支持指定月份扫描；2. 找回丢失图片；3. 强制禁止生成任何缩略图以节省空间。
 * Version: 5.0.0
 */

add_action('admin_menu', function() {
    add_management_page('HyGal 重建 Pro', 'HyGal 重建 Pro', 'manage_options', 'hygal-recovery', 'hygal_recovery_page');
});

function hygal_recovery_page() {
    $current_month = date('Y/m');
    ?>
    <div class="wrap">
        <h1>🏗️ HyGal 媒体库灾后重建 Pro</h1>
        
        <div class="notice notice-info" style="margin-top: 15px; border-left-color: #722ed1;">
            <p><strong>🚀 性能模式已开启：</strong> 此工具在导入时会<strong>完全禁用</strong> WordPress 默认缩略图生成。仅保留原图记录和基础元数据（尺寸/类型）。</p>
        </div>

        <div id="recovery-box" style="background:#fff; padding:25px; border-radius:12px; border:1px solid #ccd0d4; max-width:700px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <div id="step-config" style="margin-bottom: 20px;">
                <h3>第一步：设定扫描范围</h3>
                <p>请输入 <code>wp-content/uploads/</code> 下的子目录（留空则扫描全量，建议按月扫描）：</p>
                <input type="text" id="scan-path" value="<?php echo $current_month; ?>" placeholder="例如: 2026/02" style="width:200px; font-family:monospace; padding:8px; border-radius:4px; border:1px solid #ddd;">
                <button id="btn-scan" class="button button-secondary" style="height:38px; margin-left:10px;">🔍 开始定向扫描</button>
            </div>

            <div id="step-import" style="display:none; margin-top:20px; border-top:2px dashed #eee; padding-top:20px;">
                <h3>第二步：执行轻量化导入</h3>
                <p id="scan-stat" style="font-weight:bold; color:#722ed1; font-size:16px;"></p>
                <p style="color:#666; font-size:12px;">※ 导入过程中将不会生成任何 thumbnail, medium, large 比例的图片文件。</p>
                <button id="btn-import" class="button button-primary button-large" style="background:#722ed1; border-color:#722ed1;">立即找回并建立索引</button>
            </div>

            <div id="progress-area" style="margin-top:25px; display:none;">
                <div style="background:#f0f0f1; height:12px; border-radius:6px; overflow:hidden;">
                    <div id="prog-bar" style="background:#722ed1; width:0%; height:100%; transition:0.3s;"></div>
                </div>
                <p id="prog-text" style="text-align:center; margin-top:10px; font-weight:bold;"></p>
            </div>
        </div>

        <div id="log-window" style="margin-top:20px; background:#1e1e1e; color:#a9b7c6; padding:15px; height:300px; overflow-y:auto; font-family:monospace; font-size:12px; border-radius:8px; line-height:1.6; border: 1px solid #333;">
            > 系统就绪。建议先从最近的月份开始扫描...
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        let missingFiles = [];
        const $log = $('#log-window');

        // 1. 扫描逻辑
        $('#btn-scan').click(function() {
            const subPath = $('#scan-path').val().trim();
            const $btn = $(this);
            
            $btn.prop('disabled', true).text('正在穿梭文件系统...');
            $log.append('<br>> 正在扫描目录: uploads/' + (subPath || 'ROOT'));

            $.post(ajaxurl, { 
                action: 'hygal_scan_v5', 
                sub_path: subPath,
                nonce: '<?php echo wp_create_nonce("hygal_rec_v5"); ?>' 
            }, function(res) {
                if(res.success) {
                    missingFiles = res.data.missing;
                    $log.append('<br>> 扫描完成！在目标目录下发现 ' + missingFiles.length + ' 个数据库缺失文件。');
                    if(missingFiles.length > 0) {
                        $('#scan-stat').text('待找回图片：' + missingFiles.length + ' 张');
                        $('#step-import').fadeIn();
                    } else {
                        $log.append('<br>> 该目录下所有文件均已在媒体库中，无需操作。');
                    }
                } else {
                    alert('扫描出错：' + res.data);
                }
                $btn.text('重新扫描').prop('disabled', false);
            });
        });

        // 2. 导入逻辑
        $('#btn-import').click(function() {
            if(!confirm('将以“零缩略图”模式导入，确定继续？')) return;
            $(this).prop('disabled', true);
            $('#progress-area').show();
            processImport(0);
        });

        function processImport(index) {
            if(index >= missingFiles.length) {
                $log.append('<br><span style="color:#52c41a"><strong>> ✅ 重建任务圆满完成！已自动设为“图”分类。</strong></span>');
                $('#prog-text').text('处理完成！');
                return;
            }

            $.post(ajaxurl, {
                action: 'hygal_import_v5',
                file_path: missingFiles[index],
                nonce: '<?php echo wp_create_nonce("hygal_rec_v5"); ?>'
            }, function(res) {
                let percent = ((index + 1) / missingFiles.length) * 100;
                $('#prog-bar').css('width', percent + '%');
                $('#prog-text').text('正在恢复 (' + (index + 1) + '/' + missingFiles.length + ')');
                
                if(res.success) {
                    $log.append('<br><span style="color:#52c41a">+ [成功]</span> ' + res.data.file);
                } else {
                    $log.append('<br><span style="color:#ff4d4f">- [跳过/错误]</span> ' + missingFiles[index]);
                }
                $log.scrollTop($log[0].scrollHeight);
                processImport(index + 1);
            });
        }
    });
    </script>
    <?php
}

// --- 后端逻辑 ---

// A. 定向扫描
add_action('wp_ajax_hygal_scan_v5', function() {
    check_ajax_referer('hygal_rec_v5', 'nonce');
    global $wpdb;

    $sub_path = isset($_POST['sub_path']) ? trim($_POST['sub_path'], '/') : '';
    $upload_dir = wp_upload_dir();
    $target_dir = empty($sub_path) ? $upload_dir['basedir'] : $upload_dir['basedir'] . '/' . $sub_path;

    if (!is_dir($target_dir)) {
        wp_send_json_error('目录不存在：' . $sub_path);
    }

    $missing = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target_dir));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // 获取数据库中已有的路径
    $existing_files = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file'");

    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            // 将绝对路径转为相对于 uploads 的路径
            $rel = str_replace($upload_dir['basedir'] . '/', '', $file->getPathname());
            // 如果不在数据库中，且不是缩略图（排除像 -150x150.jpg 这种文件）
            if (!in_array($rel, $existing_files) && !preg_match('/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i', $rel)) {
                $missing[] = $rel;
            }
        }
    }
    wp_send_json_success(['missing' => $missing]);
});

// B. 导入并禁用缩略图
add_action('wp_ajax_hygal_import_v5', function() {
    check_ajax_referer('hygal_rec_v5', 'nonce');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $rel_path = $_POST['file_path'];
    $upload_dir = wp_upload_dir();
    $abs_path = $upload_dir['basedir'] . '/' . $rel_path;

    if (!file_exists($abs_path)) wp_send_json_error('文件丢失');

    // 核心：禁用所有缩略图尺寸
    add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 999);
    add_filter('fallback_intermediate_image_sizes', '__return_empty_array', 999);

    $file_name = pathinfo($abs_path, PATHINFO_FILENAME);
    $attachment = array(
        'guid'           => $upload_dir['baseurl'] . '/' . $rel_path,
        'post_mime_type' => wp_check_filetype($abs_path)['type'],
        'post_title'     => $file_name,
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $abs_path);
    
    if (!is_wp_error($attach_id)) {
        // 生成元数据（此时由于 filter 作用，只解析尺寸，不产生文件）
        $attach_data = wp_generate_attachment_metadata($attach_id, $abs_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        // 同步分类
        update_post_meta($attach_id, '_hygal_category', '图');

        wp_send_json_success(['file' => $rel_path]);
    }

    wp_send_json_error('写入失败');
});