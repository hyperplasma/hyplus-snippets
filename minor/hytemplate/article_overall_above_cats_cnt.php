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
    $output = '&nbsp;<span title="每个汉字或其他连续非空白字符算1个字">' . $text_num . '字</span>&nbsp;&nbsp;<span title="预估阅读时间（200字/分钟）">' . $read_time . '分钟</span>';
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
        
        // 获取系列页面ID
        $series_id = absint(get_post_meta($post->ID, 'hy_series_id', true));
        $series_html = '';
        
        if (!empty($series_id)) {
            // 验证该页面是否存在
            $series_post = get_post($series_id);
            if ($series_post && $series_post->post_status === 'publish') {
                $series_title = esc_attr($series_post->post_title);
                $series_link = esc_attr(get_permalink($series_id));
                $series_html = sprintf(
                    '[hysnip href="%s" title="%s" mode="link"]&nbsp;&nbsp;',
                    $series_link,
                    $series_title
                );
            }
        }

        ob_start();
        ?>
        <div class="post-buttons">
            <span class="entry-meta post-meta">
                <?php if (!empty($series_html)): ?>
                    <span>
                        <?php echo do_shortcode($series_html); ?>
                    </span>
                <?php endif; ?>
                <span style="color: green;">
                    <?php echo $counter_str; ?><span class="hyplus-unselectable"><?php echo $emoji ? '<a class="hyplus-scale" href="/user/akira37/"  style="display: inline-block;" title="受限内容">' . $emoji . '</a>' : ''; ?></span>
                </span>
            </span>
        </div>
        <?php
        echo ob_get_clean();
    }
}
?>