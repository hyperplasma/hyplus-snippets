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
            $print_js = 'javascript:window.print();';

            $share_icon = '📤';
            $share_url = get_permalink();
            $post_title = get_the_title();

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
                    <span class="hyplus-unselectable" style="float: right; display: inline-block; margin-left: 10px;">
                        <!-- 复制按钮 -->
                        <a href="#" onclick="window.copyIdLink(this, <?php echo $post_id; ?>); return false;" title="复制朴素链接（ID：<?php echo $post_id; ?>)" style="font-family:Apple Color Emoji,Segoe UI Emoji,NotoColorEmoji,Segoe UI Symbol,Android Emoji,sans-serif;">📋</a>&nbsp;
                        <!-- 分享按钮 -->
                        <a href="#" onclick="window.shareArticle('<?php echo esc_js($share_url); ?>', '<?php echo esc_js($post_title); ?>'); return false;" title="分享文章"><?php echo $share_icon; ?></a>&nbsp;
                        <!-- 打印按钮 -->
                        <a href="<?php echo esc_js($print_js); ?>" title="打印文章（建议先在Hyplus设置隐藏必要元素）" onclick="window.print(); return false;"><?php echo $print_icon; ?></a>&nbsp;
                    </span>
                </span>
            </div>
            <script>
            window.shareArticle = function(r, e) {
                if (navigator.share) {
                    navigator.share({ title: e, url: r })
                        .then(() => console.log('分享成功'))
                        .catch(err => console.error('分享失败', err));
                } else {
                    alert('您的浏览器不支持此分享功能');
                }
            };
            window.copyIdLink = function(el, postId) {
                var url = 'https://www.hyperplasma.top/?p=' + postId;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url);
                } else {
                    var input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                }
                var oldHtml = el.innerHTML;
                el.innerHTML = '<span style="color:#4CAF50;font-family:Apple Color Emoji,Segoe UI Emoji,NotoColorEmoji,Segoe UI Symbol,Android Emoji,sans-serif;">✔︎</span>';
                setTimeout(function(){el.innerHTML = oldHtml;}, 1500);
            };
            </script>
            <?php
            echo ob_get_clean();
        }
    }
}
?>