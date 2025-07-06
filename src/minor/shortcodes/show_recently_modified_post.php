<?php
/**
 * Show Recently Modified Posts Shortcode PHP
 * 显示最近修改的文章短代码
 * 使用方法：[recently_modified_posts posts_per_page="6" show_modified_date="false"]
 * - posts_per_page: 显示的文章数量，默认6
 * - show_modified_date: 是否显示修改日期，默认不显示（false），可设置为true显示
 * 
 * Code type: PHP
 * Shortcode: [recently_modified_posts]
 */
function recently_modified_posts_shortcode($atts) {
    // 解析短代码参数，默认显示6篇文章，不显示修改日期
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 6,
            'show_modified_date' => 'false'
        ),
        $atts,
        'recently_modified_posts'
    );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby'        => 'modified',
        'order'          => 'DESC'
    );
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        $output = '<ul>';
        while ( $query->have_posts() ) {
            $query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            if ( strtolower($atts['show_modified_date']) === 'true' ) {
                $modified_date = get_the_modified_date('Y年m月d日 H:i');
                $output .= ' <br><span style="font-size: smaller;">更新于&nbsp;' . $modified_date . '</span>';
            }
            $output .= '</li>';
        }
        $output .= '</ul>';
        wp_reset_postdata();
        return $output;
    } else {
        return '<p>没有找到最近修改的文章。</p>';
    }
}
add_shortcode( 'recently_modified_posts', 'recently_modified_posts_shortcode' );
    