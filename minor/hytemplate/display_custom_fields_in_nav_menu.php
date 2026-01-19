<?php
/**
 * Display custom fields in Nav Menus php
 * Usage: 将导航栏中`{{username}}`替换为当前用户的用户名，`{{cnt}}`替换为分类的博文数量……
 */
function yh_display_custom_fields_wp_menu($menu_items) {
    global $current_user;
    $category_cache = array(); // Cache for category counts to avoid redundant queries
    
    foreach ($menu_items as $menu_item) {
        // Handle {{username}} replacement
        if (strpos($menu_item->title, '{{username}}') !== false) {
            $username = $current_user->display_name ?? '';
            
            if ($username) {
                // Combine length calculation and truncation in one pass
                $truncated_username = '';
                $current_length = 0;
                
                for ($i = 0; $i < mb_strlen($username); $i++) {
                    $char = mb_substr($username, $i, 1);
                    $char_length = preg_match('/[\x{4e00}-\x{9fff}]/u', $char) ? 1.5 : 1;
                    
                    if ($current_length + $char_length <= 7) {
                        $truncated_username .= $char;
                        $current_length += $char_length;
                    } else {
                        break;
                    }
                }
                
                $username = ($current_length > 7) ? $truncated_username . '...' : $username;
            }
            
            $menu_item->title = str_replace('{{username}}', $username, $menu_item->title);
        }

        // Handle {{cnt}} replacement for category post count
        if (strpos($menu_item->title, '{{cnt}}') !== false && $menu_item->object === 'category' && !empty($menu_item->object_id)) {
            $cat_id = $menu_item->object_id;
            
            // Use cache to avoid redundant database queries
            if (!isset($category_cache[$cat_id])) {
                $category = get_category($cat_id);
                $category_cache[$cat_id] = ($category && !is_wp_error($category)) ? $category->count : 0;
            }
            
            $menu_item->title = str_replace('{{cnt}}', '(' . $category_cache[$cat_id] . ')', $menu_item->title);
        }
    }
    return $menu_items;
}
add_filter('wp_nav_menu_objects', 'yh_display_custom_fields_wp_menu');
