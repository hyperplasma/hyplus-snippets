<?php
/**
 * Disable Comments Auto-scroll after Submitting
 * Code type: PHP
 * Current status: unused
 */
function disable_comment_anchor_scroll() {
    ?>
    <script type="text/javascript">
        // 确保在文档加载完毕后执行
        document.addEventListener('DOMContentLoaded', function() {
            // 移除所有hashchange事件监听器
            if (window.addEventListener) {
                window.removeEventListener('hashchange', window.onhashchange, false);
            } else if (window.attachEvent) { // 兼容旧版IE
                window.detachEvent('onhashchange', window.onhashchange);
            }

            // 如果存在hash，并且hash以'#comment-'开头，则移除hash
            if (window.location.hash && window.location.hash.startsWith('#comment-')) {
                history.pushState('', document.title, window.location.pathname + window.location.search);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'disable_comment_anchor_scroll');
