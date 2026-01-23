<?php
/**
 * 显示在线用户数量短代码 - 完全自包含实现
 * Description: 启用此snippet后即可完全工作，无需手动修改其他文件
 * Notes: 每天自动重置今日访问量，累计访问量持续增长，支持多处调用
 * Shortcode: [wpcode id="2514"]
 */

// 防止多次定义函数
if ( !function_exists('wb_site_count_user') ) {
    // 统计全站总访问量/今日总访问量/当前是第几个访客
    function wb_site_count_user(){
        // 防止重复执行
        if ( did_action('wb_site_count_user_done') ) {
            return;
        }
        
        if ( !session_id() ) {
            @session_start();  // 使用 @ 抑制已启动的警告
        }
        
        $date = date('ymd', time());
        
        // 检查该访客是否已计数过
        if(!isset($_SESSION['wb_counted_'.$date])){        
            // 先从缓存获取，没有再从数据库获取
            $count = wp_cache_get('site_count');
            if ( $count === false ) {
                $count = get_option('site_count');
                if ( $count && is_array($count) ) {
                    wp_cache_set('site_count', $count, '', 3600); // 缓存1小时
                }
            }
            
            // 初始化数据结构
            if( !$count || !is_array($count) ){
                $newcount = array(
                    'all' => 1,      // 累计访问量
                    'date' => $date,
                    'today' => 1     // 今日访问量
                );
            } else {
                // 检查是否是新的一天，如果是则重置今日计数
                $is_new_day = ($count['date'] !== $date);
                $newcount = array(
                    'all' => ($count['all'] + 1),              // 累计访问量 +1
                    'date' => $date,
                    'today' => $is_new_day ? 1 : ($count['today'] + 1)  // 新一天则为1，否则 +1
                );
            }
            
            update_option( 'site_count', $newcount );
            wp_cache_set('site_count', $newcount, '', 3600); // 更新缓存
            $_SESSION['wb_counted_'.$date] = true;  // 标记该访客已计数
        }
        
        do_action('wb_site_count_user_done');
    }
    
    // 注册 init hook，启用访问计数（使用 has_action 检测，防止重复注册）
    if ( ! has_action( 'init', 'wb_site_count_user' ) ) {
        add_action('init', 'wb_site_count_user');
    }
}

// 短代码展示访问统计 - 只在未执行过 hook 的情况下执行函数
if ( !did_action('init') ) {
    // 如果在 init 之前被调用，手动触发计数
    wb_site_count_user();
}

// 安全的 session 启动
if ( !session_id() ) {
    @session_start();
}

// 从缓存获取统计数据
$sitecount = wp_cache_get('site_count');
if ( $sitecount === false ) {
    $sitecount = get_option('site_count');
    if ( $sitecount && is_array($sitecount) ) {
        wp_cache_set('site_count', $sitecount, '', 3600);
    }
}

// 确保数据有效
if ( !$sitecount || !is_array($sitecount) ) {
    $sitecount = array(
        'all' => 0,
        'today' => 0
    );
}

$date = date('ymd', time());
$today_count = isset($_SESSION['wb_counted_'.$date]) ? $sitecount['today'] : ($sitecount['today'] + 1);

// 只有在未缓冲时才开始缓冲
if ( ob_get_level() === 0 ) {
    ob_start();
    $ob_started = true;
} else {
    $ob_started = false;
}
?>
<div style="text-align:center; margin: 20px 0;">
	<div style="text-align: center; padding: 10px 0">
        <span>您是今日第&nbsp;<span style="color: #fd7a5e;"><?php echo absint($today_count); ?></span>&nbsp;位访客</span>
    </div>
    
    <div style="display: flex; justify-content: space-between; max-width: 220px; margin: 0 auto; padding: 10px 0;">
        <span>今日访问量</span>
        <span style="color: #fd7a5e;"><?php echo absint($sitecount['today']); ?></span>
    </div>
	
	<div style="display: flex; justify-content: space-between; max-width: 220px; margin: 0 auto; padding: 10px 0;">
        <span>累计访问量</span>
        <span style="color: #fd7a5e;"><?php echo absint($sitecount['all']); ?></span>
    </div>
</div>
<?php
if ( $ob_started ) {
    ob_end_flush();
}
?>