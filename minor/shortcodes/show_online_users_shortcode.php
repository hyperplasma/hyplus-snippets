<?php
/**
 * 显示在线用户数量（Snippet中仅注册短代码，函数实现位于function.php）
 * 注：每天早上8点重置每日访问量
 * Shortcode: [wpcode id="2514"]
 */
wb_echo_site_count();

/*
 * See function.php, backup here
 * 以下内容位于function.php，每次更新后记得手动重新粘贴。
 */
// show visitor cnt
// 统计全站总访问量/今日总访问量/当前是第几个访客
function wb_site_count_user(){
    $addnum =1;  //每个访客增加的访问数
    session_start();
    $date = date('ymd',time());
    if(!isset($_SESSION['wb_'.$date]) && !$_SESSION['wb_'.$date]){        
        $count = get_option('site_count');
        if(!$count || !is_array($count)){
            $newcount = array(
                'all' => 83760, //自定义初始访问数
                'date' => $date,
                'today' => $addnum
            );
            update_option( 'site_count', $newcount );
        } else {
            $newcount = array(
                'all' => ($count['all']+$addnum),
                'date' => $date,
                'today' => ($count['date'] == $date) ? ($count['today']+$addnum) : $addnum
            );
            update_option( 'site_count', $newcount );
        }
        $_SESSION['wb_'.$date] = $newcount['today'];
    }
    return;
}
add_action('init', 'wb_site_count_user');

// 输出访问统计
function wb_echo_site_count(){
    session_start();
    $sitecount = get_option('site_count');    
    $date = date('ymd', time());
    
    // *
    echo '<div style="text-align:center; margin: 20px 0;">'; // 添加居中样式
	
	echo '<div style="text-align: center; padding: 10px 0">';
    echo '<span>您是今日第 <span style="color: #fd7a5e;">'.absint($_SESSION['wb_'.$date]).'</span> 位访客</span>';
    echo '</div>';
    
    echo '<div style="display: flex; justify-content: space-between; max-width: 220px; margin: 0 auto; padding: 10px 0;">';
    echo '<span>今日访问量</span>';
    echo '<span style="color: #fd7a5e;">'.absint($sitecount['today']).'</span>';
    echo '</div>';
	
	echo '<div style="display: flex; justify-content: space-between; max-width: 220px; margin: 0 auto; padding: 10px 0;">';
    echo '<span>累计访问量</span>';
    echo '<span style="color: #fd7a5e;">'.absint($sitecount['all']).'</span>';
    echo '</div>';

    echo '</div>';
}