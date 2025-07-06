<?php
/**
 * Counters of Pages and Apps PHP
 */
add_action('wp_footer', function() {
    ?>
<script>function shareArticle(r,e){navigator.share?navigator.share({title:e,url:r}).then((()=>console.log("分享成功"))).catch((r=>console.error("分享失败",r))):alert("您的浏览器不支持此分享功能")}</script>
    <?php
});

// 注册短代码
add_shortcode('site_content_counts', 'display_site_content_counts');

// 显示文章和页面数
function display_site_content_counts() {
	// 获取文章数
	$post_count = wp_count_posts('post')->publish;
	// 获取页面数
	$app_count = wp_count_posts('page')->publish;

	// 输出结果
	ob_start();
	// CSS位于main.css中（搜索`HY-from`前缀）
?>
<div class=site-content-counts><span>Posts:&nbsp;<span class=site-content-counter><?php echo esc_html($post_count); ?></span></span>&nbsp;&nbsp;<span>Apps:&nbsp;<span class=site-content-counter><?php echo esc_html($app_count) . "+"; ?></span></span></div>
<?php
	// 返回缓冲内容
	return ob_get_clean();
}
