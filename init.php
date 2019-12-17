<?php
/*
Copyright (C) License Management Schiavon Maikol

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
/**
 * Plugin Name:       License Management
 * Plugin URI:        http://www.licensemanagement.it
 * Description:       This plugin is solution for handle all license for vehicles of all customers.
 * Management enable complete dashboard for handle to the best all information, such as customer, license plate and permissions. 
 * All of this can do user administrator while the customer show his information by portal. 
 * The first thing to do is insert all information related customer, and after create user Wordpress for customer and communicate to them his username and password for execute login.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Maikol Schiavon
 * Author URI:        http://www.licensemanagement.it
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       license-management
 * Domain Path:       /languages
 */

// error_reporting(E_ALL);
// ini_set("display_errors",true);

// date_default_timezone_set('UTC');

function license_management_install() {

    global $wpdb;

	$table_name_license = $wpdb->prefix . "license_management_license";
	$table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
	$table_name_service = $wpdb->prefix . "license_management_service";
	$table_name_changelog = $wpdb->prefix . "license_management_changelog";
	$table_name_service_rel = $wpdb->prefix . "license_management_service_rel";

	$charset_collate = $wpdb->get_charset_collate();
	
    $sql_license = "CREATE TABLE $table_name_license (
            id int NOT NULL AUTO_INCREMENT,
			license_plate varchar(50) CHARACTER SET utf8 NOT NULL,
			enterpriseid int NOT NULL,
			messages varchar(255) CHARACTER SET utf8,
			license_status varchar(50) CHARACTER SET utf8 NOT NULL,
			createdtime TIMESTAMP,
			modifiedtime TIMESTAMP,
			deletedtime TIMESTAMP,
			deleted int(1),
            PRIMARY KEY (`id`)
		  ) $charset_collate ";

	$sql_enterprise = "CREATE TABLE $table_name_enterprise (
            id int NOT NULL AUTO_INCREMENT,
			business_name varchar(150) CHARACTER SET utf8 NOT NULL,
			userid int NOT NULL,
			createdtime TIMESTAMP,
			modifiedtime TIMESTAMP,
			deletedtime TIMESTAMP,
			deleted int(1),
            PRIMARY KEY (`id`)
		  ) $charset_collate ";

	$sql_service = "CREATE TABLE $table_name_service (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`service_name` varchar(150) CHARACTER SET utf8 NOT NULL,
			`createdtime` timestamp NULL DEFAULT NULL,
			`modifiedtime` timestamp NULL DEFAULT NULL,
			`deletedtime` timestamp NULL DEFAULT NULL,
			`deleted` int(1) DEFAULT NULL,
			`licenseid` int(11) NOT NULL,
			`service_status` varchar(50) CHARACTER SET utf8 NOT NULL,
			PRIMARY KEY (`id`) 
			) $charset_collate ";

	$sql_chagelog = "CREATE TABLE $table_name_changelog (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`entity` varchar(50) CHARACTER SET utf8 NOT NULL,
			`entityid` int(11) NOT NULL,
			`createdtime` datetime NOT NULL,
			`changelog` longtext NOT NULL,
			`action` varchar(50) NOT NULL,
			PRIMARY KEY (`id`)
	) $charset_collate ";

	$sql_service_rel = "CREATE TABLE $table_name_service_rel (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`serviceid` int(11) DEFAULT NULL,
		`licenseid` int(11) NOT NULL,
		`service_status` int(1) NOT NULL,
		`date_end` date DEFAULT NULL,
		`messages` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`createduserid` int(19) DEFAULT NULL,
		`createdtime` timestamp NULL DEFAULT NULL,
		`modifiedtime` timestamp NULL DEFAULT NULL,
		`deletedtime` timestamp NULL DEFAULT NULL,
		`deleted` int(1) DEFAULT NULL,
		PRIMARY KEY (`id`)
	) $charset_collate ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql_license);
	dbDelta($sql_enterprise);
	dbDelta($sql_service);
	dbDelta($sql_chagelog);
	dbDelta($sql_service_rel);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'license_management_install');

//menu items
add_action('admin_menu','license_management_modifymenu');
function license_management_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('License Plate Management', //page title
	'License Plate Management', //menu title
	'manage_options', //capabilities
	'license_management_list', //menu slug
	'license_management_list' //function
	);
	
	//this is a submenu
	add_submenu_page('license_management_list', //parent slug
	__('LBL_ADD_ENTERPRISE','license-management'),  //page title
	__('LBL_ADD_ENTERPRISE','license-management'), //menu title
	'manage_options', //capability
	'license_management_enterprise', //menu slug
	'license_management_enterprise'); //function

	//this is a submenu
	add_submenu_page('license_management_list', //parent slug
	__('LBL_CREATE_LICENSE','license-management'),  //page title
	__('LBL_CREATE_LICENSE','license-management'), //menu title
	'manage_options', //capability
	'license_management_license', //menu slug
	'license_management_license'); //function

	//this is a submenu
	add_submenu_page('license_management_list', //parent slug
	__('LBL_CREATE_SERVICE','license-management'),  //page title
	__('LBL_CREATE_SERVICE','license-management'), //menu title
	'manage_options', //capability
	'license_management_service', //menu slug
	'license_management_service'); //function
}

register_activation_hook( __FILE__, 'license_management_insert_page' );

function license_management_insert_page(){
    // Create post object
    $my_post = array(
      'post_title'    => 'Customer Portal',
      'post_content'  => '[license-management-view]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );

    // Insert the post into the database
    wp_insert_post( $my_post, '' );
}

function license_management_require_lib(){
	define('LICENSE_MANAGEMENT_URL', plugins_url( 'license-management', 'license-management' ));

	wp_enqueue_style( 'timeline', LICENSE_MANAGEMENT_URL .'/css/timeline.css',false,'1.0','all');
	wp_enqueue_style( 'dashboard-css', LICENSE_MANAGEMENT_URL .'/css/dashboard.css',false,'1.0','all');
	wp_enqueue_style( 'customerportal', LICENSE_MANAGEMENT_URL .'/css/customerportal.css',false,'1.0','all');

	wp_enqueue_style( 'bootstrap-license', LICENSE_MANAGEMENT_URL .'/lib/bootstrap/css/bootstrap.min.css',false,'1.0','all');
	wp_enqueue_style( 'fontawesome', LICENSE_MANAGEMENT_URL .'/lib/fontawesome-free-5.3.1/css/all.css',false,'1.0','all');

	wp_enqueue_script( 'bootstrap-license-js', LICENSE_MANAGEMENT_URL .'/lib/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '20180909', true );
	wp_enqueue_script( 'bootstrap-popper-license-js', LICENSE_MANAGEMENT_URL .'/lib/bootstrap/js/Popper.js', array( 'jquery' ), '20180909', true );

	// Chart.js
	wp_enqueue_script( 'chart-js', LICENSE_MANAGEMENT_URL .'/lib/Chart.js/Chart.js', array(), true );
	wp_enqueue_script( 'license-management-chart', LICENSE_MANAGEMENT_URL .'/js/license-management-chart.js', array( 'jquery' ), '20181006', true );

	wp_enqueue_script( 'dashboard-js', LICENSE_MANAGEMENT_URL .'/js/license-management-dashboard.js', array(), true );

	wp_enqueue_script( 'license-management-entity', LICENSE_MANAGEMENT_URL .'/js/license-management-entity.js', array(), true );

	// fullcalendar
	wp_enqueue_style( 'calendar', LICENSE_MANAGEMENT_URL .'/css/calendar.css',false,'1.0','all');
	wp_enqueue_style( 'fullcalendar', LICENSE_MANAGEMENT_URL .'/lib/fullcalendar/fullcalendar.min.css',false,'1.0','all');
	wp_enqueue_style( 'fullcalendar-print', LICENSE_MANAGEMENT_URL .'/lib/fullcalendar/fullcalendar.print.min.css',false,'1.0','all');
	// wp_enqueue_script( 'calendar-js', LICENSE_MANAGEMENT_URL .'/lib/fullcalendar/lib/fullcalendar_moment.min.js', array(), true );
	// wp_enqueue_script( 'fullcalendar-js', LICENSE_MANAGEMENT_URL .'/lib/fullcalendar/fullcalendar.min.js', array(), true );
	wp_enqueue_script( 'fullcalendar-inizialize-js', LICENSE_MANAGEMENT_URL .'/js/license-management-calendar-inizialize.js', array(), true );
	wp_enqueue_script( 'calendar-js', LICENSE_MANAGEMENT_URL .'/js/license-management-calendar.js', array(), true );
}

function license_management_replace_content ($content){
	license_management_require_lib();
	$content = str_replace ("[license-management-view]", license_management_get_control_content_html(), $content);
	return $content;
}
add_filter ( 'the_content', 'license_management_replace_content');

function license_management_get_control_content_html(){
	$id = "";
	$status = $action = "";
	if(isset($_REQUEST["status"]) && !empty($_REQUEST["status"]) && is_numeric($_REQUEST["status"])){
		$status = intval($_REQUEST["status"]);
	}

	if(isset($_REQUEST["id"]) && !empty($_REQUEST["id"]) && is_numeric($_REQUEST["id"])){
		$id = intval($_REQUEST["id"]);
	}

	if(isset($_REQUEST["action"]) && !empty($_REQUEST["action"]) && !is_numeric($_REQUEST["action"])){
		$action = $_REQUEST["action"];
	}
	
	if($action == "detail"){
		return license_management_get_content_html_detail($id,$status);
	}else{
		return license_management_get_content_html_list($status);
	}
}
add_action( 'wp_enqueue_scripts', 'license_management_get_control_content_html' );

define('LICENSE_MANAGEMENT_ROOTDIR', plugin_dir_path(__FILE__));

require_once(LICENSE_MANAGEMENT_ROOTDIR . 'utils/license-management-utils.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-list.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-enterprise.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-license.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-service.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-portal.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'utils/license-management-dashboard-utils.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'utils/license-management-entity.php');
require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-calendar.php');
//require_once(LICENSE_MANAGEMENT_ROOTDIR . 'license-management-dashboard.php');

if(isset($_REQUEST["page"]) && ($_REQUEST["page"] == 'license_management_list' || $_REQUEST["page"] == 'license_management_service' || $_REQUEST["page"] == 'license_management_enterprise' || $_REQUEST["page"] == 'license_management_license') ){
	license_management_require_lib();
}

add_action( 'wp_ajax_license_management_request', 'license_management_ajax' );

function license_management_ajax(){
	$action_create = array("create_service","add_messages","service_messages","edit_service_lbl","update_service","create_service_lbl");

	if(isset($_REQUEST["collapse"])){
		if($_REQUEST["collapse"] == "collapse1" || $_REQUEST["collapse"] == "collapseEnterpriseCreate"){
			license_management_enterprise();
		}elseif($_REQUEST["collapse"] == "collapse2" || $_REQUEST["collapse"] == "collapseLicenseCreate"){
			license_management_license();
		}elseif($_REQUEST["collapse"] == "collapse3"){
			license_management_service();
		}
	}elseif(isset($_REQUEST["create"]) && in_array($_REQUEST["create"],$action_create)){
		$service = new license_management_service();

		if($_REQUEST["create"] == "create_service"){		
			echo $service->create_service($_REQUEST);
		}elseif($_REQUEST["create"] == "add_messages"){
			echo $service->add_messages($_REQUEST);
		}elseif($_REQUEST["create"] == "service_messages"){
			echo $service->create_messages($_REQUEST);
		}elseif($_REQUEST["create"] == "edit_service_lbl"){
			echo $service->quick_service_html($_REQUEST,'edit');
		}elseif($_REQUEST["create"] == "create_service_lbl"){
			echo $service->quick_service_html($_REQUEST,'create');
		}elseif($_REQUEST["create"] == "update_service"){
			echo $service->update_service($_REQUEST);			
		}else{
			license_management_service();
		}
	}elseif(isset($_REQUEST["create"]) && ($_REQUEST["create"] == "enterprise")){
		$enterprise = new license_management_enterprise();
		echo $enterprise->create('',$_REQUEST);
	}elseif(isset($_REQUEST["create"]) && ($_REQUEST["create"] == "license")){
		$license = new license_management_license();
		echo $license->create('',$_REQUEST);
	}elseif( isset($_REQUEST["entity"]) && isset($_REQUEST["export_csv"]) ){		// Download csv
		echo license_management_export_csv($_REQUEST["entity"]);
		/*	Debug
		$filename = $file."_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $csv_output;*/
	}elseif( isset($_REQUEST["mode"]) && ( $_REQUEST["mode"] == "messages_tmp" || $_REQUEST["mode"] == "del_messages_tmp") ){
		license_management_messages();
	}elseif( isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "tab"){
		$directory = "tab/";
		$entity = $_REQUEST["entity"];
		switch ($entity) {
			case 'customers':
				require_once $directory."license-management-customers-tab.php";
				license_management_customers_tab();
				break;
			case 'industries':
				require_once $directory."license-management-enterprise-tab.php";
				license_management_enterprise_tab();
				break;
			case 'services':
				require_once $directory."license-management-service-tab.php";
				license_management_service_tab();
				break;
			case 'license':
				require_once $directory.'license-management-license-tab.php';
				license_management_license_tab();
				break;
			case 'messages':
				require_once $directory.'license-management-messages-tab.php';
				license_management_messages_tab();
				break;
		}
	}
	wp_die();
}

load_plugin_textdomain('license-management', FALSE, basename(dirname(__FILE__)) . '/languages/');

add_action( 'wp_enqueue_scripts', 'license_management_get_translation_for_js' );

wp_enqueue_script('cs_functions_js',true);
wp_localize_script('cs_functions_js', 'langvars', license_management_get_translation_for_js());