<?php
/**
 * Parent Page Redirect PHP
 * 在单页面中点击标题时跳转到父页面
 */
function add_parent_page_redirect_script() {
    // 只在单页面显示时添加脚本
    if (is_page()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleElement = document.querySelector('.entry-title');
            if (titleElement) {
                titleElement.style.cursor = 'pointer';
                
                titleElement.addEventListener('click', function(e) {
                    <?php
                    // 获取当前页面的父页面ID
                    $parent_id = wp_get_post_parent_id(get_the_ID());
                    if ($parent_id) {
                        $parent_url = get_permalink($parent_id);
                        echo "window.location.href = '" . esc_js($parent_url) . "';";
                    } else {
                        echo "console.log('该页面没有父页面');";
                    }
                    ?>
                });
            }
        });
        </script>
        <?php
    }
}

add_action('wp_footer', 'add_parent_page_redirect_script');