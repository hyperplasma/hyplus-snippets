<?php
/**
 * Non-admin No Copy php
 */
add_action('wp_head', function () {
    // 检查用户是否已登录且为管理员
    if (!is_user_logged_in() || !current_user_can('administrator')) {
        echo '<style>article{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}</style>';
    }
});