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
            $post_id = get_the_ID();
            $print_icon = '🖨';
            $print_js = 'javascript:window.print();';  // 替换原有链接为JS打印指令

            $share_icon = '📤';
            $share_url = get_permalink();
            $post_title = get_the_title();

            $counter_str = count_words_read_time();

            printf(
                '<div class="post-buttons"><span class="entry-meta cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x('Categories', 'Used before category names.', 'generatepress'),
              $categories_list . 
                '<span style="color: green;">' . $counter_str . '</span>' . 
                '<span class="hyplus-unselectable" style="float: right; display: inline-block; margin-left: 10px;">' .
                '<a href="#" onclick="shareArticle(\'' . esc_js($share_url) . '\', \'' . esc_js($post_title) . '\'); return false;" title="分享文章">' . $share_icon . '</a>&nbsp;' .
                '<a href="' . esc_js($print_js) . '" title="打印文章（建议先在Hyplus设置隐藏必要元素）" onclick="window.print(); return false;">' . $print_icon . '</a>&nbsp;' .
                '</span></div>'
            );
        }
    }
}
