<!-- Show the Last Updated and Pen Date in Article
 Description: This version currently needs JS code to append the last updated date and edit button to the article meta section.
 Code type: universal (html + js + php)
-->
<?php
add_action('wp_footer', function() {
    if (is_single()) {
        $lastModifiedDate = get_the_modified_date('Y年n月j日');
        $lastModifiedTime = get_the_modified_time('H:i');
        $post_id = get_the_ID();
        $edit_link = get_edit_post_link($post_id);

        // 创建更新信息HTML
        $update_info = sprintf(
            '<span class="updated-on" style="display: inline;">更新于 %s %s</span><span class="hyplus-unselectable">&nbsp;</span>',
            esc_html($lastModifiedDate),
            esc_html($lastModifiedTime)
        );

        // 获取文章ID
        $post_id_attr = esc_attr($post_id);

        // 创建按钮HTML
        $buttons_html = sprintf(
            '<span class="hyplus-unselectable" style="display: inline-block;">&nbsp;</span>' .
            '<span class="hyplus-scale" style="display: inline-block;"><a href="#" onclick="window.copyHysnipCode(this, %s); return false;" title="复制非弹窗式HySnip短代码（ID：%s）" style="text-decoration: none;" class="hyplus-unselectable">📋</a></span>' .
            '<span class="hyplus-unselectable" style="display: inline-block;">&nbsp;&nbsp;</span>' .
            '<span class="hyplus-scale" style="display: inline-block;"><a href="#" onclick="window.shareArticle(\'%s\', \'%s\'); return false;" title="分享文章" style="text-decoration: none;" class="hyplus-unselectable">📤</a></span>' .
            '<span class="hyplus-unselectable" style="display: inline-block;">&nbsp;&nbsp;</span>' .
            '<span class="hyplus-scale" style="display: inline-block;"><a href="javascript:window.print();" title="打印文章（建议先在Hyplus设置隐藏必要元素）" onclick="window.print(); return false;" style="text-decoration: none;" class="hyplus-unselectable">🖨</a></span>',
            $post_id_attr,
            $post_id_attr,
            esc_url(get_permalink($post_id)),
            esc_js(get_the_title($post_id))
        );

        // 创建编辑按钮HTML并处理shortcode
        $edit_button_shortcode = sprintf(
            '[um_show_content roles="administrator"]<span class="hyplus-unselectable" style="display: inline-block;">&nbsp;&nbsp;</span><span class="hyplus-scale" style="display: inline-block;"><a class="hyplus-unselectable" href="%s" target="_blank" title="编辑文章" style="text-decoration: none;"><span style="cursor: pointer;" data-postid="%s">🖊️</span></a></span>[/um_show_content]',
            esc_url($edit_link),
            $post_id_attr
        );

        // 解析shortcode
        $edit_button = do_shortcode($edit_button_shortcode);

        // 组合完整的HTML
        $full_html = wp_json_encode($update_info . $buttons_html . $edit_button);

        // 输出JavaScript
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const entryMetaDivs = document.querySelectorAll('div.entry-meta');
        for (let div of entryMetaDivs) {
            if (!div.classList.contains('cat-links')) {
                div.style.display = 'block';
                div.insertAdjacentHTML('beforeend', <?php echo $full_html; ?>);

                // 悬浮显示文章ID
                const penIcon = div.querySelector('span[data-postid]');
                if (penIcon) {
                    penIcon.setAttribute('title', '编辑文章');
                }
                break;
            }
        }
    });

    // 添加复制和分享功能
    window.shareArticle = function(r, e) {
        if (navigator.share) {
            navigator.share({ title: e, url: r })
                .then(() => console.log('分享成功'))
                .catch(err => console.error('分享失败', err));
        } else {
            alert('您的浏览器不支持此分享功能');
        }
    };
    
    window.copyHysnipCode = function(el, postId) {
        var code = '[hysnip id="' + postId + '" mode="none"]';
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(function() {
                alert('复制成功！ID：' + postId);
            });
        } else {
            var input = document.createElement('input');
            input.value = code;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('复制成功！ID：' + postId);
        }
    };
</script>
<?php
    }
});
?>