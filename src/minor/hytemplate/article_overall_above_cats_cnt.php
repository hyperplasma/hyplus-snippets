<?php
/**
 * Articles Overall - Display Category and Buttons above - PHP
 */
add_action('generate_before_entry_title', 'lh_single_cats_above_title');

// 统计预估阅读时间
function count_words_read_time () {
    global $post;
    $text_num = mb_strlen(preg_replace('/\s/','',html_entity_decode(strip_tags($post->post_content))),'UTF-8');
    $read_time = ceil($text_num/300);
    $output = '&nbsp;&nbsp;' . $text_num . '字&nbsp;&nbsp;' . $read_time  . '分钟';
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