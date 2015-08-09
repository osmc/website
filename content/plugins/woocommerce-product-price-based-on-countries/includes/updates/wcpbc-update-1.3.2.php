<?php
/**
 * Update WCPBC to 1.3.2
 *
 * @author 		OscarGare
 * @category 	Admin
 * @version     1.3.2
 */

if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

global $wpdb;

$regions = get_option( '_oga_wppbc_countries_groups' );

if ( ! $regions ) {
	$regions = array();
}

foreach ( $regions as $region_key => $region ) {
	
	/* set exchange_rate to 1 if is empty */
	if ( empty( $regions[$region_key]['exchange_rate']) ) {
		$regions[$region_key]['exchange_rate'] = 1;
	}		

	unset($regions[$region_key]['empty_price_method']);

	foreach ( array('','_variable') as $variable) {
		
		$pre_meta_key = '_' . $region_key . $variable;	

		/* create _price_method postmeta */
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", ( $variable ? 'product_variation' : 'product' ) ) );

		foreach ( $rows as $row ) {
			
			$_price = get_post_meta($row->ID, $pre_meta_key . '_price', true );

			update_post_meta( $row->ID, $pre_meta_key . '_price_method', ( empty($_price) ? 'exchange_rate' : 'manual' ) );
		}

		/* create _regular_price postmeta */
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_value !='' AND meta_key = %s", $pre_meta_key . '_price' ) );

		foreach ( $rows as $row ) {			
			update_post_meta( $row->post_id, $pre_meta_key . '_regular_price', $row->meta_value );
		}

		/* update _price with sale price */
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_value !='' AND meta_key = %s", $pre_meta_key . '_sale_price' ) );

		foreach ( $rows as $row ) {			
			update_post_meta( $row->post_id, $pre_meta_key . '_price', $row->meta_value );
		}
		
	}
	
}

/* rename options regions */
delete_option('_oga_wppbc_countries_groups');

add_option('wc_price_based_country_regions', $regions);

/* sync variable products */
require_once( WC()->plugin_path() . '/includes/class-wc-product-variable.php' );

$rows = $wpdb->get_results( $wpdb->prepare( "SELECT distinct post_parent FROM $wpdb->posts where post_type = %s", 'product_variation' ) );

foreach ( $rows as $row ) {
	WC_Product_Variable::sync( $row->post_parent );
}

/* rename and update test options */
add_option('wc_price_based_country_test_mode', get_option('wc_price_based_country_debug_mode') );
delete_option('wc_price_based_country_debug_mode');

$test_ip = get_option('wc_price_based_country_debug_ip');
if ($test_ip) {	
	$country = WC_Geolocation::geolocate_ip($test_ip);
	add_option('wc_price_based_country_test_country', $country['country'] );	
}

delete_option('wc_price_based_country_debug_ip');

/* unschedule geoip donwload */
if ( wp_next_scheduled( 'wcpbc_update_geoip' ) ) {
	wp_clear_scheduled_hook( 'wcpbc_update_geoip' );	
}

delete_option('wc_price_based_country_update_geoip');

// Delete de options older options
delete_option( '_oga_wppbc_apiurl' );
delete_option ( '_oga_wppbc_api_country_field' );


// Delete geoip db
$geoip_db_dir = wp_upload_dir();
$geoip_db_dir = $geoip_db_dir['basedir'] . '/wc_price_based_country';

if ( file_exists( $geoip_db_dir .'/GeoLite2-Country.mmdb' ) )  {
	unlink( $geoip_db_dir .'/GeoLite2-Country.mmdb' );
	rmdir( $geoip_db_dir );
}	
