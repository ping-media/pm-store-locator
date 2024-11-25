<?php
/**
 * Plugin Name: PM Store Locator
 * Plugin URI: https://wordpressbrain.com/
 * Description: This is a custom plugin for IsaanWorld store locator
 * Version: 2.0
 * Author: Deep P. Goyal
 * Author URI: https://wpexpertdeep.com
 */

if (! defined( 'ABSPATH' )) {
	exit;
}

//COURSEPREFIX

function pm_store_locator_plugin_styles_scripts() {
    wp_register_style('dataTable-css', plugin_dir_url(__FILE__).'assets/css/jquery.dataTables.css');
    wp_enqueue_style('dataTable-css');
    wp_register_script( 'dataTable-js', plugin_dir_url(__FILE__).'assets/js/jquery.dataTables.js');
    wp_enqueue_script('dataTable-js');
}
add_action('admin_enqueue_scripts', 'pm_store_locator_plugin_styles_scripts');

function course_certificate_include_bs_datatables() {
	wp_enqueue_script('jquery');
    wp_enqueue_style( 'datepicker-css', plugin_dir_url(__FILE__).'assets/css/jquery-ui.css' );
    //wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-datepicker' );//, plugin_dir_url(__FILE__).'assets/js/datepicker.js' );
	wp_enqueue_script( 'admin-bs', plugin_dir_url(__FILE__).'assets/js/bootstrap.min.js' );
    wp_enqueue_style( 'admin-css', plugin_dir_url(__FILE__).'assets/css/bootstrap.min.css' );
}

if( isset($_GET['page']) && $_GET['page'] == 'pm-stores' ){
	add_action('admin_enqueue_scripts', 'course_certificate_include_bs_datatables');
}


add_action('wp_enqueue_scripts', 'pm_store_locator_frontend_styles_scripts');
function pm_store_locator_frontend_styles_scripts(){
	wp_enqueue_script('PM-ajax-script', plugin_dir_url(__FILE__) . 'inc/js/plugin.js?cache=' . microtime(), array('jquery'), null, true);
    wp_localize_script('PM-ajax-script', 'PM_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}

function course_certificate_include_bootsrap(){ ?>
	<style type="text/css">
		.cf-search {
		    width: 700px;
		    margin: 50px auto !important;
		    background: #f7f8fd;
		    border: 3px solid #eceefb;
		    padding: 30px;
		    border-radius: 10px;
		}
		.cf-search form {
		    display: inline-flex;
    		width: 100%;
		}
		.cf-field {
			display: inline-block !important;
		    border: 1px solid #000 !important;
		    margin-bottom: 0px !important;
		    width: 90%;
		    padding-left: 16px;
		    height: 47px;
		}
		.cf-btn {
			display: inline-block;
			border: none;
		    height: 47px !important;
		    width: 200px;
		    background: #000 !important;
		    color: #fff !important;
	        min-height: 47px;
    		border-radius: 0 !important;
		}
		.success {
			color: #155724;
		    background-color: #d4edda;
		    position: relative;
		    padding: .75rem 1.25rem;
		    margin-bottom: 1rem;
		    border: 1px solid #c3e6cb;
		    border-radius: .25rem;
		}
		.danger {
		    color: #721c24;
		    background-color: #f8d7da;
	        position: relative;
		    padding: .75rem 1.25rem;
		    margin-bottom: 1rem;
		    border: 1px solid #f5c6cb;
		    border-radius: .25rem;
		}

		@media screen and ( max-width: 768px ){
			.cf-search{ width: 90%; }
		}
		@media screen and ( max-width: 480px ){
			.cf-search form { display: initial; }
			.cf-field, .cf-btn {
				display: block !important;
				width: 100%;
			}
		}
	</style>
<?php }
add_action('wp_head', 'course_certificate_include_bootsrap');

if ( is_admin() ) {

	// Include dependencies
	require_once plugin_dir_path( __file__ ).'install.php';
	require_once plugin_dir_path( __file__ ).'uninstall.php';
	require_once plugin_dir_path( __file__ ).'inc/core-functions.php';
	require_once plugin_dir_path( __file__ ).'admin/admin-menu.php'; 
	require_once plugin_dir_path( __file__ ).'admin/settings-page.php';
}

register_activation_hook( __FILE__, 'pm_store_locator_onActivation' );
register_deactivation_hook( __FILE__, 'pm_store_locator_onDeactivation' );

// Search certificate
function course_certificate_certificate_search_form(){ 
	$output = '';
	$output .= '<style type="text/css">
		.cf-btn:hover {
			background: #000 !important;
		    color: #fff !important;
		}
		.rs-heading {
			text-align: center;
		}
		.search-table {
		    border-spacing: 0 !important;
		    border-top: none !important;
		    border-right: none !important;
		    border-left: none !important;
	        min-width: 100%;
	        border-bottom: 1px solid #ddd;
		}
		.search-table thead {
			background-color: transparent;
		}
		.search-table thead tr th {
			background-color: #000 !important;
			color: #fff !important;
			text-transform: uppercase;
			text-align: center;
		    padding: 15px 0px;
		}
		.search-table tbody tr td {
			border-right: 1px solid #ddd;
			padding: 14px 10px;
		}
		.br-0 {
			border-right: none !important
		}
		body {
			overflow-x: hidden;
		}
		.btlr-10{ border-top-left-radius: 10px; }
		.btrr-10{ border-top-right-radius: 10px; }
		.bl-1{ border-left: 1px solid #ddd; }
	</style>
		<div class="cf-search">
		<form method="POST">
			<input type="text" required class="cf-field" placeholder="Enter Pincode Code" name="pincode">
			<input type="submit" class="cf-btn" value="Search" name="code_data">
		</form>
	</div>
	<div class="container">';
	if( isset($_POST['pincode']) ){
		$pincode = sanitize_text_field($_POST['pincode']);
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT * FROM wp_stores where pincode = '$pincode'"); 
		if( !empty($rows) ){
		$output .= '<h1 class="rs-heading">Search Result</h1>
		</strong>
	</div>
        <table class="search-table" style="width:100%">
        	<thead>
                <tr>
                    <th class="btlr-10">Student Name</th>
                    <th>Course</th>
                    <th>Certification No</th>
                    <th class="br-0 btrr-10">Award Date</th>
                </tr>
            </thead>
            <tbody>';
			foreach ( $rows as $data ){
            	$output .= '<tr>
            		<td class="bl-1">'.$data->store_name.'</td>
            		<td>'.$data->store_address.'</td>
            		<td>'.$data->pincode.'</td>
            		<td>'.$data->city.'</td>
            	</tr>';
            }
           	$output .= ' </tbody>
        </table>';
   		}else{
   			echo '<div class="danger">No result found against this code <strong>'.$pincode.'</strong></div>';
   		} 
    } 
	$output .= '</div>';
	return $output;
}

add_shortcode( 'get_store_search_form' , 'course_certificate_certificate_search_form' );

add_shortcode( 'store_filter' , 'store_filter_func' );
function store_filter_func(){
	ob_start();
	require_once plugin_dir_path( __file__ ).'inc/store-filter.php';
	return ob_get_clean();
}