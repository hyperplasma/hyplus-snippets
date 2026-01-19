<?php
/**
 * Non-admin No Copy (currently only check if logged in) php
 * Current status: fluid
 */
add_action('wp_head', function () {
    // 检查用户是否已经登陆
    // if (!current_user_can('administrator')) {
    if (!is_user_logged_in()) {
        echo '<style>article{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}</style>';
    }
});