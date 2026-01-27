<?php
/**
 * Plugin Name: Universal Image Migrator Ultra (Extreme Speed)
 * Description: 极速迁移版。大幅缩短扫描间隔，具备内存保护和全量日志。
 * Code Type: PHP
 * Tips: toggle off when unused
 */

add_action('admin_menu', function() {
    add_management_page('图片本地化', '图片本地化 (极速版)', 'manage_options', 'ueim-extreme', 'ueim_extreme_render_page');
});

function ueim_extreme_render_page() {
    ?>
    <style>
        #ueim-console { background: #1e1e1e; color: #d4d4d4; padding: 10px; font-family: 'Consolas', monospace; height: 500px; overflow-y: auto; border-radius: 4px; margin-top: 20px; font-size: 11px; line-height: 1.4; border: 1px solid #333; }
        .log-item { margin-bottom: 2px; border-bottom: 1px solid #2d2d2d; white-space: nowrap; }
        .log-success { color: #4ec9b0; font-weight: bold; }
        .log-error { color: #f44747; font-weight: bold; }
        .log-warn { color: #dcdcaa; }
        .log-retry { color: #ce9178; }
        .log-skip { color: #6a9955; opacity: 0.5; }
        #ueim-progress-container { background: #333; border-radius: 5px; height: 12px; margin: 15px 0; overflow: hidden; display:none; }
        #ueim-progress-bar { background: #007bff; height: 100%; width: 0%; transition: width 0.1s linear; }
        .ueim-btn-group { display: flex; gap: 10px; margin-top: 15px; }
    </style>

    <div class="wrap">
        <h1>Universal Image Migrator (Extreme Speed)</h1>
        
        <div class="ueim-btn-group">
            <button id="start-btn" class="button button-primary button-hero">极速开始</button>
            <button id="stop-btn" class="button button-hero" style="display:none; background:#d63638; color:#fff; border:none;">停止</button>
        </div>

        <div id="ueim-progress-container"><div id="ueim-progress-bar"></div></div>
        <div id="status-text" style="font-size:14px; margin-top:5px;">就绪</div>

        <div id="ueim-console"><div class="log-item">极速版已就绪。扫描间隔已设为 50ms。</div></div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        let postIds = [];
        let currentIndex = 0;
        let isRunning = false;
        let shouldStop = false;
        let retryCount = 0;
        const MAX_RETRIES = 2; 

        $('#start-btn').on('click', function() {
            if(isRunning) return;
            isRunning = true; shouldStop = false;
            $(this).prop('disabled', true).text('狂飙中...');
            $('#stop-btn').show();
            
            $.post(ajaxurl, { action: 'ueim_get_ids_v8', _ajax_nonce: '<?php echo wp_create_nonce("ueim_v8"); ?>' }, function(res) {
                if(res.success) {
                    postIds = res.data;
                    $('#ueim-progress-container').show();
                    log('🚀 列表获取成功，总计 ' + postIds.length + ' 篇，开始极速扫描...', 'log-warn');
                    processLoop();
                }
            });
        });

        $('#stop-btn').on('click', function() { shouldStop = true; log('🛑 收到停止信号...', 'log-error'); });

        function processLoop() {
            if (shouldStop || currentIndex >= postIds.length) { finish(); return; }

            let postId = postIds[currentIndex];
            $('#status-text').text('🚀 正在处理: ' + (currentIndex + 1) + ' / ' + postIds.length);

            $.ajax({
                url: ajaxurl, type: 'POST', timeout: 60000,
                data: { action: 'ueim_process_v8', post_id: postId, _ajax_nonce: '<?php echo wp_create_nonce("ueim_v8"); ?>' },
                success: function(res) {
                    if(res.success) {
                        if(res.data.modified) {
                            log('✅ ID:' + postId + ' 迁移成功: ' + res.data.msg, 'log-success');
                        } else {
                            log('⏭️ ID:' + postId + ' 已本地化/无外链', 'log-skip');
                        }
                        retryCount = 0; currentIndex++; updateBar();
                        // 极速核心：仅延迟 50ms 立即执行下一篇
                        setTimeout(processLoop, 50); 
                    } else {
                        currentIndex++; updateBar(); processLoop();
                    }
                },
                error: function() {
                    if (retryCount < MAX_RETRIES) {
                        retryCount++;
                        log('🔄 ID:' + postId + ' 网络抖动，重试...', 'log-retry');
                        setTimeout(processLoop, 1000); 
                    } else {
                        log('❌ ID:' + postId + ' 跳过', 'log-error');
                        retryCount = 0; currentIndex++; updateBar(); processLoop();
                    }
                }
            });
        }

        function updateBar() { $('#ueim-progress-bar').css('width', (currentIndex / postIds.length * 100) + '%'); }

        function finish() {
            isRunning = false; $('#start-btn').prop('disabled', false).text('继续扫描'); $('#stop-btn').hide();
            log('🏁 任务结束。', 'log-warn');
        }

        function log(msg, cls) {
            let $c = $('#ueim-console');
            $c.append('<div class="log-item ' + cls + '">[' + new Date().toLocaleTimeString() + '] ' + msg + '</div>');
            // 极速模式下，为了性能，每 5 条日志才滚动一次底端，或者手动滚动
            if(currentIndex % 5 === 0) $c.scrollTop($c[0].scrollHeight);
        }
    });
    </script>
    <?php
}

// 3. 后端逻辑
add_action('wp_ajax_ueim_get_ids_v8', function() {
    check_ajax_referer('ueim_v8');
    wp_send_json_success(get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>-1,'fields'=>'ids']));
});

add_action('wp_ajax_ueim_process_v8', function() {
    check_ajax_referer('ueim_v8');
    @ini_set('memory_limit', '512M'); 
    $post_id = intval($_POST['post_id']);
    wp_cache_flush();
    $post = get_post($post_id);
    $site_url = home_url();

    preg_match_all('/!\[.*?\]\(\s*(https?:\/\/[^\s"\)]+)/i', $post->post_content_filtered, $m1);
    preg_match_all('/<img[^>]+src=[\'"](https?:\/\/[^\'"]+)[\'"]/i', $post->post_content, $m2);
    
    $urls = array_filter(array_unique(array_merge((array)$m1[1], (array)$m2[1])), function($u) use ($site_url) {
        return (strpos($u, 'http') === 0 && strpos($u, $site_url) === false && strpos($u, 'data:image') === false);
    });

    if (empty($urls)) wp_send_json_success(['modified'=>false]);

    $reps = [];
    foreach ($urls as $u) {
        $local = ueim_down_shielded_v8($u, $post_id);
        if (!is_wp_error($local)) $reps[$u] = $local;
    }

    if (!empty($reps)) {
        $new_md = str_replace(array_keys($reps), array_values($reps), $post->post_content_filtered);
        $new_html = str_replace(array_keys($reps), array_values($reps), $post->post_content);
        global $wpdb;
        $wpdb->update($wpdb->posts, ['post_content'=>$new_html, 'post_content_filtered'=>$new_md], ['ID'=>$post_id]);
        wp_send_json_success(['modified'=>true, 'msg'=>count($reps)."张"]);
    }
    wp_send_json_success(['modified'=>false]);
});

function ueim_down_shielded_v8($url, $post_id) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $tmp = download_url($url, 20); // 极速版缩短下载等待为20s
    if (is_wp_error($tmp)) return $tmp;

    $info = @getimagesize($tmp);
    if (!$info) { @unlink($tmp); return new WP_Error('err','NoImg'); }

    $mem_limit = (int)ini_get('memory_limit') * 1024 * 1024;
    $est_mem = $info[0] * $info[1] * 6; 
    $target_webp = $tmp . '.webp';
    $converted = false;

    if (function_exists('imagewebp') && (memory_get_usage() + $est_mem < $mem_limit)) {
        $img = null;
        if ($info['mime'] == 'image/jpeg') $img = @imagecreatefromjpeg($tmp);
        elseif ($info['mime'] == 'image/png') {
            $img = @imagecreatefrompng($tmp);
            if($img){ imagepalettetotruecolor($img); imagealphablending($img,true); imagesavealpha($img,true); }
        }
        if ($img) {
            if (@imagewebp($img, $target_webp, 80)) $converted = true;
            imagedestroy($img);
            unset($img);
        }
    }

    $path = $converted ? $target_webp : $tmp;
    $name = $converted ? (pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME).'.webp') : basename(parse_url($url, PHP_URL_PATH));
    if ($converted) @unlink($tmp);

    // 严禁生成缩略图
    $no_t = function($s){ return []; };
    add_filter('intermediate_image_sizes_advanced', $no_t, 999);
    add_filter('big_image_size_threshold', '__return_false', 999);
    add_filter('image_make_intermediate_size', '__return_false', 999);

    $id = media_handle_sideload(['name'=>$name, 'tmp_name'=>$path], $post_id);

    remove_filter('intermediate_image_sizes_advanced', $no_t, 999);
    if (is_wp_error($id)) { @unlink($path); return $id; }
    return wp_get_attachment_url($id);
}