<!-- Show the Pen Date in Taxonomy Description
 Description: This version currently needs JS code to append the edit button to the taxonomy description.
 Code type: universal (html + js + php)
-->
<?php
// 分类页面插入编辑按钮，仅管理员可见
add_action('wp_footer', function() {
    if (is_category() || is_tax()) {
        $term = get_queried_object();
        if ($term && isset($term->term_id) && isset($term->taxonomy)) {
            $edit_link = get_edit_term_link($term->term_id, $term->taxonomy);
            if ($edit_link) {
                $edit_button_shortcode = sprintf(
                    '[um_show_content roles="administrator"]&nbsp;<a class="hyplus-unselectable" href="%s" target="_blank" title="编辑分类" style="text-decoration: none;"><span style="cursor: pointer;">🖊️</span></a>[/um_show_content]',
                    esc_url($edit_link)
                );
                $edit_button = do_shortcode($edit_button_shortcode);
                $edit_button_json = wp_json_encode($edit_button);
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var description = document.querySelector('.taxonomy-description p');
        if (description) {
            // 插入编辑按钮到第一个p标签最后，前面加空格
            description.insertAdjacentHTML('beforeend', <?php echo $edit_button_json; ?>);
        }
    });
</script>
<?php
            }
        }
    }
});
?>
