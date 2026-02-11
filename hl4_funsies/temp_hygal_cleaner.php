<?php
/**
 * Plugin Name: HyGal Cleaner Pro (定向前缀清洗工具)
 * Description: 仅针对指定的业务分类前缀进行清洗，防止误伤系统生成的图片标题。
 * Version: 1.1.0
 */

// 1. 添加管理菜单
add_action('admin_menu', 'hygal_cleaner_menu');
function hygal_cleaner_menu() {
    add_management_page(
        'HyGal 标题清洗', 
        'HyGal 标题清洗', 
        'manage_options', 
        'hygal-cleaner', 
        'hygal_cleaner_page_html'
    );
}

// 2. 界面部分
function hygal_cleaner_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>🧹 HyGal 高级定向清洗工具</h1>
        <p>此版本已加入<b>分类白名单</b>，仅处理你指定的分类前缀，安全等级：高。</p>
        
        <div style="background:#fff; padding:20px; border:1px solid #ccd0d4; border-radius:12px; max-width: 800px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3>白名单过滤规则：</h3>
            <div style="font-size: 13px; color: #666; background: #f9f9f9; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                匹配范围：二次白袜、朝潮、旅游、人工智能... 等共计 25 个特定分类。
            </div>
            
            <button id="btn-scan" class="button button-secondary">🔍 模拟安全扫描</button>
            <button id="btn-fix" class="button button-primary" style="margin-left:10px;">🛠️ 执行定向修复</button>
            <span id="cleaner-loading" style="display:none; margin-left:15px; font-weight:bold; color:#2271b1;">正在比对白名单...</span>
        </div>

        <div id="cleaner-results" style="margin-top:20px; max-width: 800px; display:none;">
            <h3 id="res-title">结果：</h3>
            <textarea id="res-log" style="width:100%; height:450px; font-family:monospace; background:#1e1e1e; color:#d4d4d4; padding:15px; border-radius:8px; line-height:1.5;" readonly></textarea>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        function runCleaner(mode) {
            if (mode === 'fix' && !confirm('确定要按白名单执行批量修改吗？建议先扫描确认。')) return;

            $('#cleaner-loading').show();
            $('#btn-scan, #btn-fix').prop('disabled', true);
            $('#cleaner-results').hide();

            $.post(ajaxurl, {
                action: 'hygal_cleaner_process_pro',
                mode: mode,
                _nonce: '<?php echo wp_create_nonce("hygal_cleaner_nonce_pro"); ?>'
            }, function(response) {
                $('#cleaner-loading').hide();
                $('#btn-scan, #btn-fix').prop('disabled', false);
                
                if (response.success) {
                    const data = response.data;
                    let log = "=== 定向清理报告 ===\n";
                    log += "符合分类标记的图片: " + data.total_scanned + " 张\n";
                    log += "匹配白名单需修改: " + data.affected_count + " 张\n";
                    log += "跳过(非业务前缀): " + (data.total_scanned - data.affected_count) + " 张\n\n";
                    log += "--- 处理明细 ---\n";
                    log += data.logs.join("\n");

                    $('#res-title').text(mode === 'scan' ? '🔍 模拟结果 (安全模式)' : '✅ 修复成功');
                    $('#res-log').val(log);
                    $('#cleaner-results').fadeIn();
                }
            });
        }
        $('#btn-scan').click(function() { runCleaner('scan'); });
        $('#btn-fix').click(function() { runCleaner('fix'); });
    });
    </script>
    <?php
}

// 3. 后端处理（带白名单逻辑）
add_action('wp_ajax_hygal_cleaner_process_pro', 'hygal_cleaner_ajax_handler_pro');
function hygal_cleaner_ajax_handler_pro() {
    check_ajax_referer('hygal_cleaner_nonce_pro', '_nonce');
    global $wpdb;
    
    $mode = $_POST['mode'];
    
    // 你提供的白名单列表
    $whitelist = [
        '二次白袜', '二次黑袜', '霞', '朝潮', '大潮', '满潮', '踩', '小鸡鸡', '小姐姐', 
        '三次白袜', '三次黑袜', '女孩立', '女孩蹲', '男孩立', '男孩蹲', '网络', 
        '家庭', '旅游', '学校', '社会', '图', 'Hyplus图', '数据结构', '人工智能', 'SM Map'
    ];

    $sql = "
        SELECT p.ID, p.post_title, m.meta_value as category
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
        WHERE p.post_type = 'attachment' 
        AND m.meta_key = '_hygal_category'
    ";
    
    $results = $wpdb->get_results($sql);
    $logs = [];
    $affected_count = 0;

    foreach ($results as $item) {
        $category = trim($item->category);
        $title = $item->post_title;
        
        // 关键逻辑 1：检查该图片的分类是否在白名单内
        if (!in_array($category, $whitelist)) {
            // 虽然带有分类标记，但该分类不在我们要清理的名单内（例如：'cropped'）
            continue; 
        }

        // 关键逻辑 2：检查标题是否真的以 "{分类}-" 开头
        $prefix_to_remove = $category . '-';
        if (mb_strpos($title, $prefix_to_remove) === 0) {
            $new_title = mb_substr($title, mb_strlen($prefix_to_remove));
            
            if (!empty(trim($new_title))) {
                $affected_count++;
                if ($mode === 'fix') {
                    $wpdb->update($wpdb->posts, ['post_title' => $new_title], ['ID' => $item->ID]);
                    $logs[] = "✅ [已修复] ID:{$item->ID} | {$title} -> {$new_title}";
                } else {
                    $logs[] = "🔍 [待修复] ID:{$item->ID} | {$title} -> {$new_title}";
                }
            }
        }
    }
    
    if (empty($logs)) $logs[] = "没有发现符合条件的标题，数据已经很干净了。";

    wp_send_json_success([
        'total_scanned' => count($results),
        'affected_count' => $affected_count,
        'logs' => $logs
    ]);
}