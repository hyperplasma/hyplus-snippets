<?php
/**
 * Hide Version Footer PHP
 * 隐藏WordPress版本号
 * Current status: merged into footer_beian_text.php
 */ 
//隐藏版本号
function wpbeginner_remove_version() {
	return '';
}
add_filter('the_generator', 'wpbeginner_remove_version');