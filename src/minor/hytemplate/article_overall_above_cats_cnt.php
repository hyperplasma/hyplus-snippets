<?php
/**
 * Articles Overall - Display Category and Buttons above - PHP
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
        $categories_list = get_the_category_list(_x(' | ', 'Used between list items, there is a space after the comma.', 'generatepress'));
        if ($categories_list) {
            $counter_str = count_words_read_time();

            ob_start();
            ?>
            <div class="post-buttons">
                <span class="entry-meta cat-links">
                    <span class="screen-reader-text"><?php echo _x('Categories', 'Used before category names.', 'generatepress'); ?> </span>
                    <?php echo $categories_list; ?>
                    <span style="color: green;">
                        <?php echo $counter_str; ?>
                    </span>
                </span>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
?>