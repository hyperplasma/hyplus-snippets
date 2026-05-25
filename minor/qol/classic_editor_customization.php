<?php
/**
 * Classic Editor Customization - 后台经典编辑器各种自定义功能
 * Code type: PHP
 */

// 添加"内容信息"元框
add_action('add_meta_boxes', 'hyplus_add_content_info_metabox');

function hyplus_add_content_info_metabox() {
    add_meta_box(
        'hyplus_content_info',
        '内容信息',
        'hyplus_render_content_info_metabox',
        array('post', 'page'),
        'side',
        'high'
    );
}

function hyplus_render_content_info_metabox($post) {
    if (empty($post->ID)) {
        echo '<p>发布后将显示文章ID</p>';
        return;
    }
    
    echo '<p><strong>文章ID:</strong> ' . esc_html($post->ID) . '</p>';
}
?>