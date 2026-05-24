<?php
/**
 * Hide the update notice in the admin area, as if we all should!
 */
add_action( 'admin_head', function () {
	remove_action( 'admin_notices', 'update_nag', 3 );
}, 1 );
?>