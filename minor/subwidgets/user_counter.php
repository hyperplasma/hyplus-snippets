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

function hyplus_user_counter_get_client_ip() {
    $ip = '';
    $server_keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' );

    foreach ( $server_keys as $key ) {
        if ( empty( $_SERVER[ $key ] ) ) {
            continue;
        }

        $value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
        if ( $value !== '' ) {
            $ip = trim( current( preg_split( '/,/', $value ) ) );
            break;
        }
    }

    if ( $ip === '' ) {
        return '';
    }

    return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

function hyplus_user_counter_get_user_agent() {
    if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
        return 'unknown';
    }

    return substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 512 );
}

function hyplus_user_counter_get_fingerprint( $ip, $date, $user_agent ) {
    $salt = defined( 'AUTH_KEY' ) && AUTH_KEY ? AUTH_KEY : 'hyplus-user-counter';
    return hash( 'sha256', $ip . '|' . $date . '|' . $user_agent . '|' . $salt );
}

function hyplus_user_counter_get_sitecount_data( $date = null ) {
    $date = $date ? $date : date( 'Ymd' );

    $sitecount = wp_cache_get( 'site_count', 'hyplus_user_counter' );
    if ( ! is_array( $sitecount ) || empty( $sitecount['date'] ) || $sitecount['date'] !== $date ) {
        $sitecount = get_option( 'site_count' );
    }

    if ( ! is_array( $sitecount ) ) {
        $sitecount = array(
            'all'       => 0,
            'today'     => 0,
            'date'      => $date,
            'log_cache' => array(),
        );
    }

    $sitecount['all']   = isset( $sitecount['all'] ) ? absint( $sitecount['all'] ) : 0;
    $sitecount['today'] = isset( $sitecount['today'] ) ? absint( $sitecount['today'] ) : 0;

    if ( ! isset( $sitecount['date'] ) || $sitecount['date'] !== $date ) {
        $sitecount['date']  = $date;
        $sitecount['today'] = 0;
        $sitecount['log_cache'] = array();
    }

    if ( ! isset( $sitecount['log_cache'] ) || ! is_array( $sitecount['log_cache'] ) ) {
        $sitecount['log_cache'] = array();
    }

    if ( ! isset( $sitecount['log_cache'][ $date ] ) || ! is_array( $sitecount['log_cache'][ $date ] ) ) {
        $sitecount['log_cache'][ $date ] = array();
    }

    return $sitecount;
}

function hyplus_user_counter_track_visit() {
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    $ip = hyplus_user_counter_get_client_ip();
    if ( $ip === '' ) {
        return;
    }

    $date = date( 'Ymd' );
    $user_agent = hyplus_user_counter_get_user_agent();
    $sitecount = hyplus_user_counter_get_sitecount_data( $date );
    $fingerprint = hyplus_user_counter_get_fingerprint( $ip, $date, $user_agent );

    if ( isset( $sitecount['log_cache'][ $date ][ $fingerprint ] ) ) {
        return;
    }

    $sitecount['all']   = intval( $sitecount['all'] ) + 1;
    $sitecount['today'] = intval( $sitecount['today'] ) + 1;
    $sitecount['log_cache'][ $date ][ $fingerprint ] = array(
        'ip'   => $ip,
        'ua'   => $user_agent,
        'time' => time(),
    );

    update_option( 'site_count', $sitecount );
    wp_cache_set( 'site_count', $sitecount, 'hyplus_user_counter', HOUR_IN_SECONDS );
}
add_action( 'init', 'hyplus_user_counter_track_visit', 5 );

function hyplus_user_counter_shortcode() {
    $date = date( 'Ymd' );
    $sitecount = hyplus_user_counter_get_sitecount_data( $date );

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
