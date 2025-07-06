<?php
/**
 * Show Post Counts in Nav bar
 * Code type: PHP
 */
// 获取分类或标签下的文章数量
function get_category_post_count($cat_id) {
    $category = get_category($cat_id);
    return $category->category_count;
}

// 在导航菜单项后追加文章数量
function add_post_count_to_menu_item($title,$item, $args) {
    // 检查是否是主菜单
    if ($args->theme_location == 'primary') {
        // 检查菜单项是否有分类ID设置
        if (get_post_meta($item->ID, '_menu_item_object_id', true)) {
            $category_id = get_post_meta($item->ID, '_menu_item_object_id', true);
            $post_count = get_category_post_count($category_id);
            // 当且仅当有文章时才追加显示文章数量
            if ($post_count > 0) {
                $title .= ' (' .$post_count . ')';
            }
        }
    }
    return $title;
}

// 添加过滤器钩子
add_filter('nav_menu_item_title', 'add_post_count_to_menu_item', 10, 3);


/**
 * Show Search Result Count and Mobile Search Form - 只在前台搜索结果页生效
 * Code type: PHP
 */
add_action('template_redirect', function () {
    if (is_search()) {
        ob_start(function ($content) {
            global $wp_query;
            // 匹配常见的搜索标题 h1/h2/h3，兼容绝大多数主题
            $pattern = '/(<h1[^>]*class="[^"]*page-title[^"]*"[^>]*>.*?<\/h1>)/is';
            if (preg_match($pattern, $content, $matches)) {
                $count = intval($wp_query->found_posts);
                // 条数显示
                $count_html = '<div style="text-align:center;font-size:20px;color:#333;margin-top:8px;font-weight:600;">共' . $count . '条</div>';

                // 移动端搜索框（仅在 max-width:768px 时显示），圆角31px，无按钮，占满宽度，placeholder为Hyplus Search...
                // 相关CSS在hypluscss.css中（搜索`HY-from`）
				$search_form = '
                <form class="mobile-search-bar" role="search" method="get" action="' . esc_url(home_url('/')) . '" style="display:none;text-align:center;">
                    <input type="search" name="s" value="' . esc_attr(get_search_query()) . '" placeholder="Hyplus Search..." autocomplete="off" />
                </form>
                ';

                // 在标题后插入统计条数和移动端搜索框
                $replacement = $matches[1] . $count_html . $search_form;
                $content = preg_replace($pattern, $replacement, $content, 1);
            }
            return $content;
        });
    }
});