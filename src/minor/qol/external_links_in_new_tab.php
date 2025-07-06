<?php
/**
 * External Links in New Tab PHP
 * 为所有外链和指定的内链添加新标签页打开属性
 * 包括文章内容和导航菜单中的链接
 */

// 处理导航菜单项的函数
function hyperplasma_modify_menu_items($items) {
    // 定义需要在新标签页打开的特定内部链接
    $special_internal_links = array(
        'https://www.hyperplasma.top/wp-admin/',
        'https://www.hyperplasma.top/wp-admin/edit.php',
        'https://www.hyperplasma.top/wp-admin/edit.php?post_type=page',
        'https://www.hyperplasma.top/wp-admin/plugins.php',
        'https://www.hyperplasma.top:27782/39933f96'
    );

    foreach ($items as $item) {
        if (!empty($item->url)) {
            // 检查是否是外部链接或特定的内部链接
            if (
                preg_match('#^https?://(?!www\.hyperplasma\.top)#i', $item->url) ||
                in_array($item->url, $special_internal_links)
            ) {
                // 添加 target="_blank"
                $item->target = '_blank';

                // 添加或更新 rel 属性
                $rel_values = array_filter(explode(' ', $item->xfn));
                $rel_values[] = 'noopener';
                $rel_values[] = 'external';
                $item->xfn = implode(' ', array_unique(array_filter($rel_values)));
            }
        }
    }
    return $items;
}

// 处理文章内容中链接的函数
function hyperplasma_modify_content_links($content) {
    // 如果内容为空，直接返回
    if (empty($content)) {
        return $content;
    }

    // 定义需要在新标签页打开的特定内部链接
    $special_internal_links = array(
        'https://www.hyperplasma.top/wp-admin/',
        'https://www.hyperplasma.top/wp-admin/edit.php',
        'https://www.hyperplasma.top/wp-admin/edit.php?post_type=page',
        'https://www.hyperplasma.top/wp-admin/plugins.php',
        'https://www.hyperplasma.top:27782/39933f96'
    );

    // 使用正则表达式处理链接
    $pattern = '/<a([^>]*?)href=[\'"]([^\'"]+)[\'"]([^>]*?)>/i';
    return preg_replace_callback($pattern, function($matches) use ($special_internal_links) {
        $full_match = $matches[0];
        $attr_before = $matches[1];
        $url = $matches[2];
        $attr_after = $matches[3];

        // 检查是否是外部链接或特定的内部链接
        if (
            preg_match('#^https?://(?!www\.hyperplasma\.top)#i', $url) ||
            in_array($url, $special_internal_links)
        ) {
            // 确保不重复添加属性
            if (strpos($full_match, 'target=') === false) {
                $full_match = str_replace('>', ' target="_blank">', $full_match);
            }
            if (strpos($full_match, 'rel=') === false) {
                $full_match = str_replace('>', ' rel="noopener external">', $full_match);
            }
        }

        return $full_match;
    }, $content);
}

// 添加到WordPress过滤器
add_filter('wp_nav_menu_objects', 'hyperplasma_modify_menu_items', 10, 1);
add_filter('the_content', 'hyperplasma_modify_content_links', 999);
add_filter('widget_text', 'hyperplasma_modify_content_links', 999);
add_filter('term_description', 'hyperplasma_modify_content_links', 999);
add_filter('the_excerpt', 'hyperplasma_modify_content_links', 999);