<?php
/* HyNav: Series Nav Page, Ultimate Button popup addon (Desc is moved here for easier editing in Code Snippets)
 * Description: similar to HyNav
 * Code type: PHP/HTML (no need to compress the codes)
 * Special Permissions: No Formatting
 * Shortcode: [series_nav_panel_render]
 */
add_shortcode('series_nav_panel_render', 'series_nav_panel_render');

function series_nav_panel_render() {
    ob_start();
    ?>
<div class="hyplus-nav-container hyplus-unselectable"  style="grid-template-columns: 1fr;">
	<div class="hyplus-nav-section">
		<h3>常规</h3>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				[hysnip id="20627"][hysnip id="20613"][hysnip id="20633"]
			</div>
			<div class="hyplus-nav-group">
				[hysnip id="20663"][hysnip id="20637"][hysnip id="20641"]
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<h3>系统开发综合</h3>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				[hysnip id="20651"][hysnip id="20650"][hysnip id="20652"][hysnip id="20654"][hysnip id="20661"]
			</div>
			<div class="hyplus-nav-group">
				[hysnip id="20851"][hysnip id="20667"][hysnip id="20668"]
			</div>
		</div>
	</div>
	
	<div class="hyplus-nav-section">
		<h3>大数据·人工智能</h3>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				[hysnip id="20672"][hysnip id="20674"]
			</div>
			<div class="hyplus-nav-group">
				[hysnip id="20679"][hysnip id="20677"][hysnip id="20678"][hysnip id="20682"]
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<h3>其他HyPress系列</h3>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<?php if (current_user_can('administrator')): ?>
				[hysnip id="/"][hysnip id="/"]
				<?php else: ?>
				<a class="hyplus-nav-link" href="https://www.hyperplasma.top/welcome/">返回古腾堡欢迎页</a>
				<?php endif; ?>
			</div>
			<div class="hyplus-nav-group">
				[hysnip id="20691"]
			</div>
		</div>
	</div>
</div>
    <?php
    return do_shortcode(ob_get_clean());
}
?>