<?php
/**
 * Show Search Result Count and Mobile Search Form 
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