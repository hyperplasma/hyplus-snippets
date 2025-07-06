<!-- Show the Last Updated and Pen Date in Article
 Description: This version currently needs JS code to append the last updated date and edit button to the article meta section.
 Code type: universal (html + js + php)
-->
<?php
add_action('wp_footer', function() {
	if (is_single()) {
		$lastModifiedDate = get_the_modified_date('Y年n月j日');
		$lastModifiedTime = get_the_modified_time('H:i');
		$post_id = get_the_ID();
		$edit_link = get_edit_post_link($post_id);

		// 创建更新信息HTML
		$update_info = sprintf(
			'<span class="updated-on">更新于 %s %s</span>&nbsp;',
			esc_html($lastModifiedDate),
			esc_html($lastModifiedTime)
		);

		// 创建编辑按钮HTML并处理shortcode
		$edit_button_shortcode = sprintf(
			'[um_show_content roles="administrator"]<a class="hyplus-unselectable" href="%s" target="_blank" title="编辑文章" style="text-decoration: none;">&nbsp;<span style="cursor: pointer;">🖊️</span></a>[/um_show_content]',
			esc_url($edit_link)
		);

		// 解析shortcode
		$edit_button = do_shortcode($edit_button_shortcode);

		// 组合完整的HTML
		$full_html = wp_json_encode($update_info . $edit_button);

		// 输出JavaScript
?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const entryMetaDivs = document.querySelectorAll('div.entry-meta');
		for (let div of entryMetaDivs) {
			if (!div.classList.contains('cat-links')) {
				div.insertAdjacentHTML('beforeend', <?php echo $full_html; ?>);
				break;
			}
		}
	});
</script>
<?php
	}
});
?>