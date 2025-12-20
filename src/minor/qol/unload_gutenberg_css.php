<?php
/**
 * Name: Unload Gutenberg CSS (Except Homepage)
 * Description: 仅在主页加载古腾堡块编辑器样式，其他页面不加载
 * Code type: PHP
 */

function hyplus_unload_gutenberg_css() {
    // 仅在非主页/主院页面移除古腾堡样式
    if ( ! is_home() && ! is_front_page() ) {
        // 移除古腾堡块库样式
        wp_dequeue_style( 'wp-block-library' );
        
        // 移除古腾堡块库主题样式
        wp_dequeue_style( 'wp-block-library-theme' );
        
        // 移除古腾堡排版主题样式
        wp_dequeue_style( 'wp-block-editor' );
        
        // 移除古腾堡嵌入样式
        wp_dequeue_style( 'wp-embed' );
    }
}

add_action( 'wp_enqueue_scripts', 'hyplus_unload_gutenberg_css', 20 );
