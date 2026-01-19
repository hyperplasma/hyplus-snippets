<?php
/**
 * Custom Protected Title excerpt PHP
 */
/* 修改密码保护标题 */
add_filter('protected_title_format', function($title) {
	// $lock = "\u{1F510}";
	// $emoji = json_decode('"' . $lock . '"');
	// return $emoji . "%s" . $emoji;
	return "%s";
});

/* 移除密码保护文章摘要 */
add_filter('the_excerpt', function($excerpt) {
	if (post_password_required()) {
		$lock = "\u{1F510}";
		$emoji = json_decode('"' . $lock . '"');
		return $emoji . '此内容受密码保护。如需查阅，请先输入此内容的临时保护密码' . $emoji;
	}
	return $excerpt;
});

add_filter('the_password_form', function($form) {
	// 替换表单提示语
	$form = str_replace(
		'此内容受密码保护。如需查看请在下方输入访问密码：',
		'此内容受密码保护。如需查阅，请在下列字段中输入此内容的临时保护密码。密码随时可能更改，详情请联系<a href="https://www.hyperplasma.top/user/akira37/">Hyplus管理员</a>。',
		$form
	);
	// 替换输入框前的标签
	$form = str_replace(
		'密码：',
		'保护密码：',
		$form
	);
	return $form;
});