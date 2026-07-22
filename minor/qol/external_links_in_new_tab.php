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
        // '/wp-admin/edit.php',
        // '/wp-admin/edit.php?post_type=page',
        // '/wp-admin/admin.php?page=wpcode-snippet-manager&snippet_id=11647',
        // '/wp-admin/plugins.php',
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
    
    $site_domain_escaped = preg_quote($site_domain, '~');
    
    $data = array(
        'site_domain' => $site_domain,
        'site_domain_escaped' => $site_domain_escaped,
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
    $site_domain_escaped = $data['site_domain_escaped'];
    $special_internal_links = $data['links'];

    foreach ($items as $item) {
        if (!empty($item->url) && is_string($item->url)) {
            // 新增：若链接不以 http 开头，完全不做处理
            if (!preg_match('/^https?:/i', $item->url)) {
                continue;
            }

            // // 忽略以 / 或 # 开头的链接（必定是内部链接）
            // if (preg_match('/^[/#]/', $item->url)) {
            //     continue;
            // }
            
            // 检查是否是外部链接或特殊内部链接
            $is_external = !preg_match('~^' . $site_domain_escaped . '~i', $item->url);
            $is_special_link = in_array($item->url, $special_internal_links, true);
            if ($is_external || $is_special_link) {
                // 添加 target="_blank"
                $item->target = '_blank';

                // 添加或更新 rel 属性
                $rel_values = array_filter(explode(' ', is_string($item->xfn) ? $item->xfn : ''));
                $rel_values[] = 'noopener';
                $rel_values[] = 'external';
                $item->xfn = implode(' ', array_unique(array_filter($rel_values)));
            }
        }
    }
    return $items;
}

function hyperplasma_append_external_link_marker($link_text) {
    // if (strpos($link_text, '⧉') !== false) {
    //     return $link_text;
    // }

    return $link_text . '<sup>⧉</sup>';
}

// 处理文章内容中链接的函数
function hyperplasma_modify_content_links($content) {
    // 如果内容为空，或没有链接，直接返回
    if (empty($content) || stripos($content, '<a') === false || (stripos($content, 'http://') === false && stripos($content, 'https://') === false)) {
        return $content;
    }

    $data = hyperplasma_get_special_internal_links();
    $site_domain = $data['site_domain'];
    $site_domain_escaped = $data['site_domain_escaped'];
    $special_internal_links = $data['links'];
    
    // 预缓存特殊路径以提高性能（避免在每个链接中重复访问常量）
    $special_paths = HYPERPLASMA_SPECIAL_LINK_PATHS;

    // 使用正则表达式处理链接
    // 匹配完整的 <a>...</a> 结构，以便在链接文字末尾添加外部链接符号
    $pattern = '/(<a\b[^>]*?href=[\'\"]([^\'\"]*?)[\'\"]([^>]*)>)(.*?)(<\/a>)/is';
    
    return preg_replace_callback($pattern, function($matches) use ($site_domain, $site_domain_escaped, $special_internal_links, $special_paths) {
        $opening_tag = $matches[1];
        $url = $matches[2];
        $attr_part = $matches[3];
        $link_text = $matches[4];
        $closing_tag = $matches[5];

        // 若链接不以http开头，完全不做处理
        if (!preg_match('/^https?:/i', $url)) {
            return $matches[0];
        }

        // // 如果包含 class="hyplus-nav-link"，则不修改
        // if ((is_category() || is_tax()) && preg_match('/class=["\'][^"\']*hyplus-nav-link[^"\']*["\']/i', $full_tag)) {
        //     return $full_tag;
        // }

        // // 忽略以 / 或 # 开头的链接（必定是内部链接）- 这些链接不应该添加target属性
        // if (preg_match('/^[/#]/', $url)) {
        //     return $full_tag;
        // }

        // 检查是否是外部链接或特定的内部链接
        $is_external = !preg_match('~^' . $site_domain_escaped . '~i', $url);
        $is_special_link = false;
        
        // 检查是否在特殊链接列表中
        if (in_array($url, $special_internal_links, true)) {
            $is_special_link = true;
        } else {
            // 检查是否是相对路径的特殊链接（使用预缓存的 $special_paths）
            foreach ($special_paths as $path) {
                if (stripos($url, $path) === 0) {
                    $is_special_link = true;
                    break;
                }
            }
        }
        
        $new_opening_tag = $opening_tag;
        $new_link_text = $link_text;

        if ($is_external || $is_special_link) {
            if ($is_external && strpos($opening_tag, 'class=') === false) {
                $new_link_text = hyperplasma_append_external_link_marker($link_text);
            }

            // 确保不重复添加属性
            if (strpos($new_opening_tag, 'target=') === false) {
                $new_opening_tag = preg_replace('/>$/', ' target="_blank">', $new_opening_tag);
            }
            if (strpos($new_opening_tag, 'rel=') === false) {
                $new_opening_tag = preg_replace('/>$/', ' rel="noopener external">', $new_opening_tag);
            }
        }

        return $new_opening_tag . $new_link_text . $closing_tag;
    }, $content);
}

// 添加到WordPress过滤器
add_filter('wp_nav_menu_objects', 'hyperplasma_modify_menu_items', 10, 1);
add_filter('the_content', 'hyperplasma_modify_content_links', 999);
add_filter('widget_text', 'hyperplasma_modify_content_links', 999);
add_filter('term_description', 'hyperplasma_modify_content_links', 999);
add_filter('the_excerpt', 'hyperplasma_modify_content_links', 999);