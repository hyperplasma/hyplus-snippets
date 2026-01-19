<?php
/**
 * Hide wp_login Admin - 隐藏WordPress默认登陆页面
 * 
 * 功能说明：
 * 1. 将 wp-login.php 访问重定向到自定义登录页面 /login
 * 2. 通过修改登陆错误提示和隐藏登陆页面链接来增强安全性
 * 
 * Current status: active
 */

// ========== 隐藏 wp-login.php 页面 ==========
// 任何用户（包括已登录用户）直接访问 wp-login.php 时，重定向到自定义登录页面
add_action( 'login_init', function() {
    // 获取当前页面URL
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    
    // 允许的登录流程（这些不会被重定向）
    $allowed_paths = array(
        'action=logout',
        'action=lostpassword',
        'action=resetpass',
        'action=register',
        'action=postpass',  // 博文密码验证
        'checkemail=confirm', // 邮箱确认
        'checkemail=registered', // 注册确认
    );
    
    // 检查是否是允许的页面
    $is_allowed = false;
    foreach ( $allowed_paths as $path ) {
        if ( strpos( $request_uri, $path ) !== false ) {
            $is_allowed = true;
            break;
        }
    }
    
    // 如果不是允许的页面，重定向到自定义登录页面
    if ( ! $is_allowed ) {
        wp_redirect( home_url( '/login' ) );
        exit;
    }
}, 10 );


// ========== 登陆错误提示隐藏 ==========
// 隐藏登陆错误信息（防止用户信息泄露）
add_filter( 'login_errors', function( $error ) {
    // 只显示通用错误提示
    return '<strong>错误：</strong>登录失败，请检查用户名和密码。';
}, 10, 1 );


// ========== 隐藏登陆相关的wp-json端点 ==========
// 禁用REST API登陆相关端点，防止API扫描
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_not_authenticated', '未认证' );
    }
    return $result;
}, 10, 1 );


// ========== 替换WordPress登陆URL ==========
// 将所有指向 wp-login.php 的链接重定向到自定义登录页面
add_filter( 'login_url', function( $login_url, $redirect, $force_reauth ) {
    return home_url( '/login' );
}, 10, 3 );


// ========== 防止通过wp-login.php?action=register进行注册 ==========
add_action( 'login_init', function() {
    if ( ! is_user_logged_in() && isset( $_GET['action'] ) && $_GET['action'] === 'register' ) {
        wp_redirect( home_url( '/login' ) );
        exit;
    }
}, 5 );
?>
