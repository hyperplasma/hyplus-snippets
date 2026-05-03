<?php
/**
 * Articles Overall - Display cnt and sth else above - PHP
 * 系列链接功能：如果文章设置了 hy_series_id，显示 hysnip 短代码链接
 */
add_action('generate_before_entry_title', 'lh_single_cats_above_title');

// 统计预估阅读时间
function count_words_read_time() {
    global $post;
    $text = html_entity_decode($post->post_content);
    
    // 按照 Typora 的方式计数：汉字1个算1个词，连续的非空白字符（字母、数字、符号等）也算1个词
    $chinese_chars = preg_match_all('/[\x{4E00}-\x{9FFF}]/u', $text);
    
    // 移除汉字后，统计连续的非空白字符序列
    $text_without_chinese = preg_replace('/[\x{4E00}-\x{9FFF}]/u', '', $text);
    $other_words = preg_match_all('/[^\s]+/u', $text_without_chinese);
    
    $text_num = $chinese_chars + $other_words;
    
    $read_time = $text_num > 0 ? ceil($text_num / 200) : 0;
    $output = '<span title="每个汉字或其他连续非空白字符算1个字">' . $text_num . '字</span>&nbsp;<span title="预估阅读时间（200字/分钟）">' . $read_time . '分钟</span>';
    return $output;
}

function lh_single_cats_above_title() {
    if (is_single()) {
        global $post;
        
        $counter_str = count_words_read_time();
        $emoji = '';
        
        // 检查是否为密码保护的文章
        if (!empty($post->post_password)) {
            $emoji .= '🔐';
        }
        
        // 获取系列页面ID（可能有多个，以逗号分隔）
        $series_ids_raw = get_post_meta($post->ID, 'hy_series_id', true);
        $series_html = '';
        
        if (!empty($series_ids_raw)) {
            // 检测是否为纯数字（单个ID的常见情况）
            if (ctype_digit((string)$series_ids_raw)) {
                // 快速路径：单个ID
                $series_post = get_post((int)$series_ids_raw);
                if ($series_post && $series_post->post_status === 'publish') {
                    $series_html = sprintf(
                        "[hysnip href='%s' title='%s' mode='link' async='1']",
                        get_permalink($series_post->ID),
                        $series_post->post_title
                    );
                }
            } else {
                // 完整路径：多个ID
                $series_snippets = array();
                foreach (array_map('trim', explode(',', $series_ids_raw)) as $series_id) {
                    $series_id = (int)$series_id;
                    if ($series_id <= 0) continue;
                    
                    $series_post = get_post($series_id);
                    if ($series_post && $series_post->post_status === 'publish') {
                        $series_snippets[] = sprintf(
                            "[hysnip href='%s' title='%s' mode='link' async='1']",
                            get_permalink($series_post->ID),
                            $series_post->post_title
                        );
                    }
                }
                
                $series_html = implode(' ', $series_snippets);
            }
        }

        ?>
        <div class="post-buttons">
            <span class="entry-meta post-meta">
                <?php if (!empty($series_html)): ?>
                    <span>
                        <?php echo do_shortcode($series_html); ?>
                    </span>
                <?php endif; ?>
                <span style="color: green;">
                    <?php echo $counter_str; ?><span class="hyplus-unselectable"><?php echo $emoji ? '&nbsp;<a class="hyplus-scale" href="/user/akira37/"  style="display: inline-block;" title="受限内容">' . $emoji . '</a>' : ''; ?></span>
                </span>
            </span>
        </div>
        <?php
    }
}
?>