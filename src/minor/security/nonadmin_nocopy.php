<?php
/**
 * Non-admin No Copy php
 * Current status: fluid
 */
add_action('wp_head', function () {
    // 检查用户是否为管理员
    if (!current_user_can('administrator')) {
        echo '<style>article{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}</style>';
    }
});