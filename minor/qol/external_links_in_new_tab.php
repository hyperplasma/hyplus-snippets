<?php
/**
 * External Links in New Tab PHP
 * 为所有外链和指定的内链添加新标签页打开属性
 * 包括文章内容和导航菜单中的链接
 */

// 定义需要在新标签页打开的特定链接路径（相对于网站根目录）
if (!defined('HYPERPLASMA_SPECIAL_LINK_PATHS')) {
    define('HYPERPLASMA_SPECIAL_LINK_PATHS', array(
        '/wp-admin/',
        '/wp-admin/edit.php',
        '/wp-admin/edit.php?post_type=page',
        '/wp-admin/admin.php?page=wpcode-snippet-manager&snippet_id=11647',
        '/wp-admin/plugins.php',
        ':27782/39933f96'
    ));
}

// 获取特定需要在新标签页打开的完整链接数组（使用缓存以提高性能）
function hyperplasma_get_special_internal_links() {
    // 尝试从缓存获取
    $cache_key = 'hyperplasma_special_links';
    $cached_data = wp_cache_get($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $site_domain = site_url();
    $special_link_paths = HYPERPLASMA_SPECIAL_LINK_PATHS;
    
    $special_internal_links = array();
    foreach ($special_link_paths as $path) {
        $special_internal_links[] = $site_domain . $path;
    }
    
    $data = array(
        'site_domain' => $site_domain,
        'links' => $special_internal_links
    );
    
    // 缓存结果（有效期 1 小时）
    wp_cache_set($cache_key, $data, '', HOUR_IN_SECONDS);
    
    return $data;
}

// 处理导航菜单项的函数
function hyperplasma_modify_menu_items($items) {
    $data = hyperplasma_get_special_internal_links();
    $site_domain = $data['site_domain'];
    $special_internal_links = $data['links'];

    foreach ($items as $item) {
        if (!empty($item->url)) {
            // 忽略以 / 或 # 开头的链接（必定是内部链接）
            if (preg_match('#^[/#]#', $item->url)) {
                continue;
            }
            // 检查是否是外部链接或特定的内部链接
            if (
                !preg_match('#^' . preg_quote($site_domain, '#') . '#i', $item->url) ||
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

    $data = hyperplasma_get_special_internal_links();
    $site_domain = $data['site_domain'];
    $special_internal_links = $data['links'];
    
    // 预编译正则表达式和转义的域名，避免在回调中重复处理
    $site_domain_escaped = preg_quote($site_domain, '#');

    // 使用正则表达式处理链接
    $pattern = '/<a([^>]*?)href=[\'"]([^\'"]+)[\'"]([^>]*?)>/i';
    return preg_replace_callback($pattern, function($matches) use ($site_domain, $site_domain_escaped, $special_internal_links) {
        $full_match = $matches[0];
        $url = $matches[2];

        // 忽略以 / 或 # 开头的链接（必定是内部链接）
        if (preg_match('#^[/#]#', $url)) {
            return $full_match;
        }

        // 检查是否是外部链接或特定的内部链接
        if (
            !preg_match('#^' . $site_domain_escaped . '#i', $url) ||
            in_array($url, $special_internal_links, true)
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