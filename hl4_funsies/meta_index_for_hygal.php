<?php
/**
 * 插件功能：HyGal 索引构建助手
 * 功能：扫描现有媒体库，根据标题前缀建立 Meta 索引
 */

// 1. 注册后台菜单
add_action('admin_menu', function() {
    add_management_page('HyGal 索引助手', 'HyGal 索引助手', 'manage_options', 'hygal-indexer', 'hygal_indexer_page');
});

// 2. 页面渲染
function hygal_indexer_page() {
    ?>
    <div class="wrap">
        <h1>🛠️ HyGal 媒体库索引助手</h1>
        <p>本工具将扫描所有附件标题，识别 <code>前缀-标题</code> 格式，并将其存入 <code>_hygal_category</code> 索引字段。</p>
        
        <div id="indexer-box" style="background:#fff; padding:20px; border:1px solid #ccd0d4; border-radius:8px; max-width:600px;">
            <div id="indexer-status">
                <p>点击下方按钮开始分析媒体库...</p>
            </div>
            
            <div style="margin-top:20px;">
                <button id="start-indexing" class="button button-primary button-large">开始同步索引</button>
            </div>

            <div id="progress-container" style="margin-top:20px; display:none;">
                <div style="background:#eee; height:20px; border-radius:10px; overflow:hidden;">
                    <div id="progress-bar" style="background:#43a5f5; width:0%; height:100%; transition: width 0.3s;"></div>
                </div>
                <p id="progress-text" style="text-align:center; font-weight:600;"></p>
            </div>
        </div>

        <div id="indexer-log" style="margin-top:20px; background:#f0f0f1; padding:15px; height:200px; overflow-y:auto; font-family:monospace; font-size:12px; border:1px solid #ccd0d4;">
            > 等待操作...
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        let isProcessing = false;

        $('#start-indexing').on('click', function() {
            if(isProcessing) return;
            if(!confirm('确定要扫描整个媒体库吗？这可能需要一点时间。')) return;

            const $btn = $(this);
            const $log = $('#indexer-log');
            const $progress = $('#progress-container');
            const $bar = $('#progress-bar');
            const $pText = $('#progress-text');

            isProcessing = true;
            $btn.prop('disabled', true).text('正在处理...');
            $progress.show();
            $log.append('<br>> 正在获取附件总量...');

            function processBatch(offset) {
                $.post(ajaxurl, {
                    action: 'hygal_do_indexing',
                    offset: offset,
                    nonce: '<?php echo wp_create_nonce("hygal_indexer_nonce"); ?>'
                }, function(res) {
                    if(res.success) {
                        const data = res.data;
                        $log.append('<br>> 处理进度: ' + data.current + '/' + data.total);
                        $log.scrollTop($log[0].scrollHeight);
                        
                        let percent = (data.current / data.total) * 100;
                        $bar.css('width', percent + '%');
                        $pText.text(Math.round(percent) + '% (' + data.current + ' / ' + data.total + ')');

                        if(!data.finished) {
                            processBatch(data.next_offset);
                        } else {
                            $log.append('<br>> ✅ 索引构建完成！');
                            $btn.text('同步完成').addClass('button-disabled');
                            isProcessing = false;
                        }
                    } else {
                        $log.append('<br>> ❌ 错误: ' + res.data);
                        isProcessing = false;
                    }
                });
            }

            processBatch(0);
        });
    });
    </script>
    <?php
}

// 3. AJAX 处理 (采用分批处理模式，防止超时)
add_action('wp_ajax_hygal_do_indexing', function() {
    check_ajax_referer('hygal_indexer_nonce', 'nonce');
    
    global $wpdb;
    $batch_size = 100; // 每批处理100张图
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

    // 获取总数（仅第一次时有用，但在 AJAX 响应中一直返回）
    $total = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'attachment'");
    
    // 获取当前批次
    $attachments = $wpdb->get_results($wpdb->prepare(
        "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'attachment' LIMIT %d OFFSET %d",
        $batch_size, $offset
    ));

    $processed_count = 0;
    foreach ($attachments as $at) {
        $title = $at->post_title;
        // 查找连字符位置
        $dash_pos = strpos($title, '-');
        
        if ($dash_pos !== false) {
            // 提取前缀并去除两端空格
            $prefix = trim(substr($title, 0, $dash_pos));
            if (!empty($prefix)) {
                // 更新或创建索引字段
                update_post_meta($at->ID, '_hygal_category', $prefix);
            }
        }
        $processed_count++;
    }

    $current_pos = $offset + $processed_count;
    wp_send_json_success([
        'total' => $total,
        'current' => $current_pos,
        'next_offset' => $current_pos,
        'finished' => ($current_pos >= $total)
    ]);
});