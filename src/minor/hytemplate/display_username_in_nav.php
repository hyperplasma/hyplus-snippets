<?php
/**
 * Display username in Nav bar php
 * Usage: 将导航栏中`{{username}}`替换为当前用户的用户名
 */
function yh_display_username_wp_menu($menu_items) {
    global $current_user;
    foreach ($menu_items as $menu_item) {
        if (strpos($menu_item->title, '{{username}}') !== false) {
            // Get username, otherwise set it to blank.
            if ($current_user->display_name) {
                $username = $current_user->display_name;
            } else {
                $username = '';
            }

            // Calculate the effective length considering Chinese characters as 1.5 length
            $effective_length = 0;
            for ($i = 0; $i < mb_strlen($username); $i++) {
                $char = mb_substr($username, $i, 1);
                // Check if the character is a Chinese character (using Unicode range)
                if (preg_match('/[\x{4e00}-\x{9fff}]/u', $char)) {
                    $effective_length += 1.5;
                } else {
                    $effective_length += 1;
                }
            }

            // Truncate username if effective length is >= 10
            if ($effective_length >= 10) {
                $truncated_username = '';
                $current_length = 0;

                for ($i = 0; $i < mb_strlen($username); $i++) {
                    $char = mb_substr($username, $i, 1);
                    if (preg_match('/[\x{4e00}-\x{9fff}]/u', $char)) {
                        $char_length = 1.5;
                    } else {
                        $char_length = 1;
                    }

                    if ($current_length + $char_length <= 7) {
                        $truncated_username .= $char;
                        $current_length += $char_length;
                    } else {
                        break;
                    }
                }

                $username = $truncated_username . '...';
            }

            $menu_item->title = str_replace('{{username}}', $username, $menu_item->title);
        }
    }
    return $menu_items;
}
add_filter('wp_nav_menu_objects', 'yh_display_username_wp_menu');