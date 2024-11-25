<?php
//Exit if file called directly
if (! defined( 'ABSPATH' )) {
	exit;
}


// add top-level administrative menu
function pm_store_locator_admin_menu() {
	
	/* 
		add_menu_page(
			string   $page_title, 
			string   $menu_title, 
			string   $capability, 
			string   $menu_slug, 
			callable $function = '', 
			string   $icon_url = '', 
			int      $position = null 
		)
	*/
	
	add_menu_page(
		'Stores',
		'Stores',
		'manage_options',
		'pm-stores',
		'course_certificate_admin_certificate_ui',
		'dashicons-store',
		null
	);
	
}
add_action( 'admin_menu', 'pm_store_locator_admin_menu' );