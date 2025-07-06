<?php
/**
 * Random Post Redirect PHP - 随机文章跳转功能
 * 访问`/random`跳转到一篇随机文章
 */
add_action( 'template_redirect', 'hyperplasma_non_sequential_random_redirect' );

function hyperplasma_non_sequential_random_redirect() {
    if ( '/random' !== parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ) {
        return;
    }

    // 方法1：使用WP原生函数（最简单可靠）
    $random_post = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'orderby'        => 'rand',
        'posts_per_page' => 1,
        'fields'         => 'ids' // 只获取ID提升性能
    ) );

    // 方法2：优化的SQL查询（需要正确定义$wpdb）
    if ( empty( $random_post ) ) {
        global $wpdb;
        $random_post = array( $wpdb->get_var(
            "SELECT ID FROM {$wpdb->posts} 
            WHERE post_type = 'post' 
            AND post_status = 'publish'
            ORDER BY RAND() 
            LIMIT 1"
        ) );
    }

    // 方法3：备用方案 - 从最新100篇文章中随机
    if ( empty( $random_post[0] ) ) {
        $recent_posts = wp_get_recent_posts( array(
            'numberposts' => 100,
            'post_status' => 'publish',
            'fields'      => 'ids'
        ) );
        
        if ( ! empty( $recent_posts ) ) {
            shuffle( $recent_posts );
            $random_post = array( $recent_posts[0] );
        }
    }

    // 执行跳转
    if ( ! empty( $random_post[0] ) ) {
        wp_redirect( get_permalink( $random_post[0] ), 302 );
        exit;
    }
    
    // 所有方法都失败时跳转首页
    wp_redirect( home_url(), 302 );
    exit;
}