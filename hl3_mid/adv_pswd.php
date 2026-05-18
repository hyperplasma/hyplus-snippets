<?php
/**
 * Hyplus Advanced Post Password access control.
 * Description:
 * 	- Allows admins to bypass password-protected posts/pages.
 * 	- Remembers successful passwords per protected post/page so users do not need to re-enter repeatedly.
 * Code Type: PHP
 */

// 规范化 URL 用于 cookie 名称一致性
function hyplus_normalize_url_for_cookie( $url ) {
    // 移除查询字符串和锚点
    $url = strtok( $url, '?' );
    // 转换为小写
    $url = strtolower( $url );
    // 移除末尾斜杠（对于主页除外）
    $url = untrailingslashit( $url );
    return $url;
}

add_filter('post_password_required', 'hyplus_adv_post_password_required', 10, 2);
add_action('wp_loaded', 'hyplus_adv_post_password_submit');

function hyplus_adv_post_password_submit()
{
    if ( empty($_POST) || ! isset($_POST['post_password']) ) {
        return;
    }

    $referer = wp_get_raw_referer();
    if ( ! $referer ) {
        return;
    }

    $request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );
    $referer = hyplus_normalize_url_for_cookie( $referer );
    
    // 只在来自受保护页面的合法提交时才设置 cookie
    if ( empty( $referer ) ) {
        return;
    }

    if ( headers_sent() ) {
        return;
    }

    require_once ABSPATH . WPINC . '/class-phpass.php';

    $secure   = 'https' === parse_url( $referer, PHP_URL_SCHEME );
    $expire   = apply_filters( 'post_password_expires', time() + 365 * DAY_IN_SECONDS );
    $password = wp_unslash( $_POST['post_password'] );
    $hasher   = new PasswordHash( 8, true );

    $cookie_name = 'hyplus-post-password_' . md5( $referer ) . '_' . COOKIEHASH;
    setcookie( $cookie_name, $hasher->HashPassword( $password ), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );
}

function hyplus_adv_post_password_required( $required, $post )
{
    $post = get_post( $post );
    if ( ! $post instanceof WP_Post ) {
        return $required;
    }

    if ( empty( $post->post_password ) ) {
        return false;
    }

    // 管理员绕过密码
    if ( current_user_can( 'administrator' ) || current_user_can( 'manage_options' ) ) {
        return false;
    }

    if ( ! $required ) {
        return false;
    }

    // 规范化永久链接用于 cookie 检查
    $post_url = hyplus_normalize_url_for_cookie( get_permalink( $post ) );
    $cookie_name = 'hyplus-post-password_' . md5( $post_url ) . '_' . COOKIEHASH;
    
    if ( empty( $_COOKIE[ $cookie_name ] ) ) {
        return true;
    }

    require_once ABSPATH . WPINC . '/class-phpass.php';

    $hash = wp_unslash( $_COOKIE[ $cookie_name ] );
    if ( 0 !== strpos( $hash, '$P$B' ) ) {
        return true;
    }

    $hasher = new PasswordHash( 8, true );
    $valid  = $hasher->CheckPassword( $post->post_password, $hash );

    return $valid ? false : true;
}
