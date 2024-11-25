<?php
//Exit if file called directly
if (! defined( 'ABSPATH' )) { 
    exit; 
}


function pm_store_locator_onActivation(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $create_table_query = "
    CREATE TABLE IF NOT EXISTS `wp_stores` (
        id BIGINT NOT NULL AUTO_INCREMENT,
        store_name VARCHAR(100) NOT NULL,
        store_address VARCHAR(255) NOT NULL,
        city VARCHAR(100) NOT NULL,
        state VARCHAR(100) NOT NULL,
        pincode INT(10) NOT NULL,
        phone BIGINT NOT NULL,
        email VARCHAR(100) NOT NULL,
        open_timing TIME NOT NULL,
        close_timing TIME NOT NULL,
        image TEXT NOT NULL,
        google_map TEXT NOT NULL,
		store_url TEXT NOT NULL,
        created DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_table_query );
}