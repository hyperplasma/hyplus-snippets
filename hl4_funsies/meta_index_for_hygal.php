<?php
/**
 * 插件功能：Meta Index for HyGal - 智能同步助手 (v2.0)
 * 功能：扫描媒体库，根据标题前缀同步构建或清理 _hygal_category 索引字段。
 */

// 1. 注册后台菜单
add_action('admin_menu', function() {
    add_management_page('HyGal 索引助手', 'HyGal 索引助手', 'manage_options', 'hygal-indexer', 'hygal_indexer_page');
});

// 2. 页面渲染
function hygal_indexer_page() {
    ?>
    <div class="wrap">
        <h1>🛠️ HyGal 媒体库索引同步工具</h1>
        <p>本工具将扫描所有附件标题：</p>
        <ul style="list-style-type: disc; margin-left: 20px;">
            <li>符合 <code>前缀-标题</code> 格式：<strong>更新或建立</strong> 索引。</li>
            <li>不含 <code>-</code> 连字符：<strong>自动删除</strong> 现有索引（清理失效数据）。</li>
        </ul>
        
        <div id="indexer-box" style="background:#fff; padding:20px; border:1px solid #ccd0d4; border-radius:8px; max-width:600px;">
            <div id="indexer-status">
                <p>准备就绪。点击下方按钮开始全量同步...</p>
            </div>
            
            <div style="margin-top:20px;">
                <button id="start-indexing" class="button button-primary button-large">开始全量同步索引</button>
            </div>

            <div id="progress-container" style="margin-top:20px; display:none;">
                <div style="background:#eee; height:20px; border-radius:10px; overflow:hidden;">
                    <div id="progress-bar" style="background:#43a5f5; width:0%; height:100%; transition: width 0.3s;"></div>
                </div>
                <p id="progress-text" style="text-align:center; font-weight:600; margin-top:10px;"></p>
            </div>
        </div>

        <div id="indexer-log" style="margin-top:20px; background:#f0f0f1; padding:15px; height:250px; overflow-y:auto; font-family:monospace; font-size:12px; border:1px solid #ccd0d4; line-height: 1.6;">
            > 等待操作...
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        let isProcessing = false;

        $('#start-indexing').on('click', function() {
            if(isProcessing) return;
            if(!confirm('确定要同步整个媒体库索引吗？\n不含连字符的标题将会被移除索引字段。')) return;

            const $btn = $(this);
            const $log = $('#indexer-log');
            const $progress = $('#progress-container');
            const $bar = $('#progress-bar');
            const $pText = $('#progress-text');

            isProcessing = true;
            $btn.prop('disabled', true).text('同步处理中...');
            $progress.show();
            $log.html('> 任务启动，正在计算附件总量...');

            function processBatch(offset) {
                $.post(ajaxurl, {
                    action: 'hygal_do_indexing',
                    offset: offset,
                    nonce: '<?php echo wp_create_nonce("hygal_indexer_nonce"); ?>'
                }, function(res) {
                    if(res.success) {
                        const data = res.data;
                        $log.append('<br>> 处理批次: ' + data.current + '/' + data.total);
                        $log.scrollTop($log[0].scrollHeight);
                        
                        let percent = (data.current / data.total) * 100;
                        $bar.css('width', percent + '%');
                        $pText.text(Math.round(percent) + '% (' + data.current + ' / ' + data.total + ')');

                        if(!data.finished) {
                            processBatch(data.next_offset);
                        } else {
                            $log.append('<br><strong>> ✅ 全量同步完成！索引已与标题保持一致。</strong>');
                            $btn.text('全量同步完成').addClass('button-disabled');
                            isProcessing = false;
                        }
                    } else {
                        $log.append('<br>> ❌ 发生错误: ' + res.data);
                        $btn.prop('disabled', false).text('重新尝试');
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

// 3. AJAX 分批逻辑
add_action('wp_ajax_hygal_do_indexing', function() {
    check_ajax_referer('hygal_indexer_nonce', 'nonce');
    
    global $wpdb;
    $batch_size = 150; // 适当增加批次大小，提高效率
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

    // 获取附件总数
    $total = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'attachment'");
    
    // 查询当前批次的 ID 和标题
    $attachments = $wpdb->get_results($wpdb->prepare(
        "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'attachment' LIMIT %d OFFSET %d",
        $batch_size, $offset
    ));

    $processed_count = 0;
    foreach ($attachments as $at) {
        $title = $at->post_title;
        $dash_pos = strpos($title, '-');
        
        if ($dash_pos !== false) {
            // 模式 A: 提取前缀并更新/建立索引
            $prefix = trim(substr($title, 0, $dash_pos));
            if (!empty($prefix)) {
                update_post_meta($at->ID, '_hygal_category', $prefix);
            } else {
                // 如果是 "-标题" 这种异常格式，清理索引
                delete_post_meta($at->ID, '_hygal_category');
            }
        } else {
            // 模式 B: 标题中没有连字符，主动清理可能存在的旧索引
            delete_post_meta($at->ID, '_hygal_category');
        }
        $processed_count++;
    }

    $current_pos = $offset + $processed_count;
    wp_send_json_success([
        'total' => (int)$total,
        'current' => $current_pos,
        'next_offset' => $current_pos,
        'finished' => ($current_pos >= $total)
    ]);
});