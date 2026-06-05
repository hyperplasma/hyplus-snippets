<?php
/**
 * 显示在线用户数量短代码 - 完全自包含实现
 * Description: 启用此snippet后即可完全工作，无需手动修改其他文件
 * Notes: 每天自动重置今日访问量，累计访问量持续增长，支持多处调用
 * Shortcode: [hyplus_user_counter]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hyplus_user_counter_start_session() {
    if ( session_status() === PHP_SESSION_NONE ) {
        @session_start();
    }
}
add_action( 'init', 'hyplus_user_counter_start_session', 1 );

function hyplus_user_counter_track_visit() {
    if ( is_admin() ) {
        return;
    }

    hyplus_user_counter_start_session();

    $date = date( 'Ymd' );
    $session_key = 'hyplus_user_counted_' . $date;

    if ( ! empty( $_SESSION[ $session_key ] ) ) {
        return;
    }

    $sitecount = wp_cache_get( 'site_count', 'hyplus_user_counter' );
    if ( ! is_array( $sitecount ) || empty( $sitecount['date'] ) || $sitecount['date'] !== $date ) {
        $sitecount = get_option( 'site_count' );
    }

    if ( ! is_array( $sitecount ) ) {
        $sitecount = array(
            'all'   => 0,
            'today' => 0,
            'date'  => $date,
        );
    }

    if ( ! isset( $sitecount['date'] ) || $sitecount['date'] !== $date ) {
        $sitecount['date']  = $date;
        $sitecount['today'] = 0;
    }

    $sitecount['all']   = isset( $sitecount['all'] ) ? intval( $sitecount['all'] ) + 1 : 1;
    $sitecount['today'] = isset( $sitecount['today'] ) ? intval( $sitecount['today'] ) + 1 : 1;

    update_option( 'site_count', $sitecount );
    wp_cache_set( 'site_count', $sitecount, 'hyplus_user_counter', HOUR_IN_SECONDS );
    $_SESSION[ $session_key ] = true;
}
add_action( 'init', 'hyplus_user_counter_track_visit', 5 );

function hyplus_user_counter_shortcode() {
    hyplus_user_counter_start_session();

    $date = date( 'Ymd' );
    $sitecount = wp_cache_get( 'site_count', 'hyplus_user_counter' );

    if ( ! is_array( $sitecount ) || empty( $sitecount['date'] ) || $sitecount['date'] !== $date ) {
        $sitecount = get_option( 'site_count' );
    }

    if ( ! is_array( $sitecount ) ) {
        $sitecount = array(
            'all'   => 0,
            'today' => 0,
            'date'  => $date,
        );
    }

    $all = absint( $sitecount['all'] );
    $today_count = absint( $sitecount['today'] );

    $output  = '<div style="text-align:center; margin: 20px 0;">';
    $output .= '<div style="text-align:center; padding: 10px 0">';
    $output .= '<span>您是今日第&nbsp;<span style="color: #fd7a5e;">' . $today_count . '</span>&nbsp;位访客</span>';
    $output .= '</div>';
    $output .= '<div style="display:flex; justify-content:space-between; max-width:220px; margin:0 auto; padding:10px 0;">';
    $output .= '<span>今日访问量</span><span style="color:#fd7a5e;">' . $today_count . '</span>';
    $output .= '</div>';
    $output .= '<div style="display:flex; justify-content:space-between; max-width:220px; margin:0 auto; padding:10px 0;">';
    $output .= '<span>累计访问量</span><span style="color:#fd7a5e;">' . $all . '</span>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}
add_shortcode( 'hyplus_user_counter', 'hyplus_user_counter_shortcode' );
