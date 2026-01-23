<?php
/**
 * 显示在线用户数量短代码 - 完全自包含实现
 * Description: 启用此snippet后即可完全工作，无需手动修改其他文件
 * Notes: 每早8点刷新今日访问量数据
 * Shortcode: [wpcode id="2514"]
 */

// 初始访问计数常量
define( 'WB_SITE_COUNT_INIT', apply_filters( 'wb_site_count_init', 83760 ) );

// 统计全站总访问量/今日总访问量/当前是第几个访客
function wb_site_count_user(){
    // 防止重复执行
    if ( did_action('wb_site_count_user_done') ) {
        return;
    }
    
    $addnum = 1;  // 每个访客增加的访问数
    
    if ( !session_id() ) {
        session_start();
    }
    
    $date = date('ymd', time());
    
    // 检查该访客是否已计数过
    if(!isset($_SESSION['wb_'.$date]) || !$_SESSION['wb_'.$date]){        
        // 先从缓存获取，没有再从数据库获取
        $count = wp_cache_get('site_count');
        if ( $count === false ) {
            $count = get_option('site_count');
            if ( $count && is_array($count) ) {
                wp_cache_set('site_count', $count, '', 3600); // 缓存1小时
            }
        }
        
        if(!$count || !is_array($count)){
            $newcount = array(
                'all' => WB_SITE_COUNT_INIT,
                'date' => $date,
                'today' => $addnum
            );
        } else {
            $newcount = array(
                'all' => ($count['all'] + $addnum),
                'date' => $date,
                'today' => ($count['date'] == $date) ? ($count['today'] + $addnum) : $addnum
            );
        }
        
        update_option( 'site_count', $newcount );
        wp_cache_set('site_count', $newcount, '', 3600); // 更新缓存
        $_SESSION['wb_'.$date] = $newcount['today'];
    }
    
    do_action('wb_site_count_user_done');
}

// 注册 init hook，启用访问计数（使用 has_action 检测，防止重复注册）
if ( ! has_action( 'init', 'wb_site_count_user' ) ) {
    add_action('init', 'wb_site_count_user');
}

// 短代码展示访问统计
if ( !session_id() ) {
    session_start();
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
        'all' => WB_SITE_COUNT_INIT,
        'today' => 0
    );
}

$date = date('ymd', time());
$today_count = isset($_SESSION['wb_'.$date]) ? absint($_SESSION['wb_'.$date]) : 1;

ob_start();
?>
<div style="text-align:center; margin: 20px 0;">
	<div style="text-align: center; padding: 10px 0">
        <span>您是今日第 <span style="color: #fd7a5e;"><?php echo $today_count; ?></span> 位访客</span>
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
ob_end_flush();
?>