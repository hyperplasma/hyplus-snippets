<?php
/**
 * Feature Image Under Entry Header
 * Description: 在单篇文章页面，将特色图片显示在 entry-header 下方并居中
 */

// 隐藏默认的特色图片显示（提升性能）
add_action( 'after_setup_theme','gp_remove_featured_page_header' );  
if (!function_exists('gp_remove_featured_page_header')) {
	function gp_remove_featured_page_header() { 
    	remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	}
}

// 记录当前文章ID
add_action('the_post', function($post) {
    global $hyplus_featured_img_post_id;
    $hyplus_featured_img_post_id = $post->ID;
});

// 在 entry-header 后输出特色图片（兼容大多数主题）
add_action('generate_after_entry_header', function() {
    global $hyplus_featured_img_post_id;
    if (!is_single() || empty($hyplus_featured_img_post_id)) return;
    if (!has_post_thumbnail($hyplus_featured_img_post_id)) return;
    echo '<div class="hypost-featured-image" style="text-align:center;margin-top:20px;">';
    echo get_the_post_thumbnail($hyplus_featured_img_post_id, 'full', [
        'style' => 'display:inline-block;max-width:100%;height:auto;'
    ]);
    echo '</div>';
});