<?php
/**
 * Plugin Name: HyperPlasma Redundant Domain URL Cleaner Pro
 * Description: 扫描并清理 wp_posts (含 Markdown) 及 wp_term_taxonomy 中的绝对域名。
 */

// 1. 在“工具”菜单下添加子菜单
add_action('admin_menu', 'hp_cleaner_add_tool_menu');
function hp_cleaner_add_tool_menu() {
    add_management_page(
        '域名链接清理工具',
        '域名链接清理',
        'manage_options',
        'hp-url-cleaner',
        'hp_cleaner_render_page'
    );
}

// 2. 渲染后台页面 HTML
function hp_cleaner_render_page() {
    ?>
    <div class="wrap">
        <h1>域名链接清理工具 <span style="font-size: 0.5em; color: #666;">v2.0</span></h1>
        <p>此工具将扫描并移除站点中的 <code>https://www.hyperplasma.top</code> 前缀。</p>
        
        <div style="background: #fff; border-left: 4px solid #d63638; padding: 12px; margin-bottom: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <strong>🚨 终极警告：</strong> 此操作将直接修改数据库！执行前请务必<strong>全库备份</strong>。
        </div>

        <div class="card" style="max-width: 600px; margin-bottom: 20px; padding: 15px;">
            <h2>1. 博文内容清理 (Posts)</h2>
            <p>涉及字段：<code>post_content</code> (HTML) 和 <code>post_content_filtered</code> (Markdown)。</p>
            <button id="start-clean-posts" class="button button-primary">开始清理博文</button>
            <div id="posts-progress-container" style="margin-top: 15px; display:none; background: #eee; border: 1px solid #ccc;">
                <div id="posts-progress-bar" style="width: 0%; height: 20px; background: #0073aa; transition: width 0.3s;"></div>
            </div>
            <p id="posts-status"></p>
        </div>

        <div class="card" style="max-width: 600px; margin-bottom: 20px; padding: 15px;">
            <h2>2. 分类/标签描述清理 (Taxonomy)</h2>
            <p>涉及字段：<code>wp_term_taxonomy</code> 表中的 <code>description</code> 字段。</p>
            <button id="start-clean-tax" class="button button-secondary">开始清理分类描述</button>
            <div id="tax-progress-container" style="margin-top: 15px; display:none; background: #eee; border: 1px solid #ccc;">
                <div id="tax-progress-bar" style="width: 0%; height: 20px; background: #46b450; transition: width 0.3s;"></div>
            </div>
            <p id="tax-status"></p>
        </div>

        <div id="log" style="margin-top: 20px; max-height: 250px; overflow-y: auto; background: #333; color: #adff2f; padding: 15px; font-family: monospace; border-radius: 4px; font-size: 12px;">
            > 等待操作...
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var targetDomain = 'https://www.hyperplasma.top';

        function log(msg) {
            $('#log').prepend('> ' + msg + '<br>');
        }

        // --- 处理博文逻辑 ---
        $('#start-clean-posts').on('click', function() {
            if(!confirm('确定清理博文中的域名吗？')) return;
            $(this).prop('disabled', true);
            $('#posts-progress-container').show();
            runAjax('hp_clean_posts_action', 0, 20, '#posts-progress-bar', '#posts-status', '#start-clean-posts');
        });

        // --- 处理分类描述逻辑 ---
        $('#start-clean-tax').on('click', function() {
            if(!confirm('确定清理分类/标签描述中的域名吗？')) return;
            $(this).prop('disabled', true);
            $('#tax-progress-container').show();
            runAjax('hp_clean_tax_action', 0, 20, '#tax-progress-bar', '#tax-status', '#start-clean-tax');
        });

        function runAjax(action, offset, batchSize, barId, statusId, btnId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: action,
                    offset: offset,
                    batch_size: batchSize,
                    nonce: '<?php echo wp_create_nonce("hp_cleaner_nonce"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        var total = response.data.total;
                        var currentOffset = offset + batchSize;
                        var progress = (currentOffset / total) * 100;
                        if(progress > 100) progress = 100;
                        
                        $(barId).css('width', progress + '%');
                        $(statusId).text('进度: ' + Math.round(progress) + '% (' + (currentOffset > total ? total : currentOffset) + '/' + total + ')');
                        
                        if (currentOffset < total) {
                            runAjax(action, currentOffset, batchSize, barId, statusId, btnId);
                        } else {
                            $(statusId).text('✅ 处理完成！');
                            log('任务完成：' + action);
                        }
                    } else {
                        log('错误: ' + response.data);
                    }
                }
            });
        }
    });
    </script>
    <?php
}

// 3. AJAX 处理逻辑：博文清理
add_action('wp_ajax_hp_clean_posts_action', 'hp_clean_posts_ajax_handler');
function hp_clean_posts_ajax_handler() {
    check_ajax_referer('hp_cleaner_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'posts';
    $target = 'https://www.hyperplasma.top';

    $total = $wpdb->get_var("SELECT COUNT(ID) FROM $table WHERE post_status != 'auto-draft'");
    $offset = intval($_POST['offset']);
    $batch_size = intval($_POST['batch_size']);

    $posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content, post_content_filtered FROM $table WHERE post_status != 'auto-draft' LIMIT %d, %d", $offset, $batch_size));

    foreach ($posts as $post) {
        $up_content = str_replace($target, '', $post->post_content);
        $up_filtered = str_replace($target, '', $post->post_content_filtered);

        if ($up_content !== $post->post_content || $up_filtered !== $post->post_content_filtered) {
            $wpdb->update($table, array('post_content' => $up_content, 'post_content_filtered' => $up_filtered), array('ID' => $post->ID));
        }
    }
    wp_send_json_success(array('total' => (int)$total));
}

// 4. AJAX 处理逻辑：分类描述清理
add_action('wp_ajax_hp_clean_tax_action', 'hp_clean_tax_ajax_handler');
function hp_clean_tax_ajax_handler() {
    check_ajax_referer('hp_cleaner_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'term_taxonomy';
    $target = 'https://www.hyperplasma.top';

    $total = $wpdb->get_var("SELECT COUNT(term_taxonomy_id) FROM $table");
    $offset = intval($_POST['offset']);
    $batch_size = intval($_POST['batch_size']);

    $terms = $wpdb->get_results($wpdb->prepare("SELECT term_taxonomy_id, description FROM $table LIMIT %d, %d", $offset, $batch_size));

    foreach ($terms as $term) {
        $up_desc = str_replace($target, '', $term->description);
        if ($up_desc !== $term->description) {
            $wpdb->update($table, array('description' => $up_desc), array('term_taxonomy_id' => $term->term_taxonomy_id));
        }
    }
    wp_send_json_success(array('total' => (int)$total));
}