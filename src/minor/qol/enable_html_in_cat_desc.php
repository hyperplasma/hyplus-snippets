<?php
/**
 * Enable HTML in Category Descriptions PHP
 * 启用分类描述中的HTML代码支持，并保持换行（故请谨慎换行）
 */

// 移除默认的过滤器
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');

// 添加简单的换行处理
function hyperplasma_preserve_linebreaks($description) {
    // 将连续两个换行符转换为段落
    $description = preg_replace('/\n\n+/', '</p><p>', $description);
    // 将单个换行符转换为<br>
    $description = preg_replace('/\n/', '<br>', $description);
    // 包裹在段落标签中
    $description = '<p>' . $description . '</p>';
    // 修复空段落
    $description = str_replace('<p></p>', '<p>&nbsp;</p>', $description);
    return $description;
}
add_filter('term_description', 'hyperplasma_preserve_linebreaks', 1);