<?php

// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Post meta
$regions = array_keys( get_option( 'wc_price_based_country_regions', array() ) );

foreach ( $regions as $region_key ) {

	foreach ( array( '_price', '_regular_price', '_sale_price', '_price_method' ) as $price_type ) {

		foreach ( array('', '_variable') as $variable) {

			$meta_key = '_' . $region_key . $variable . $price_type;

			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $meta_key ) );			
		}

		if ( $price_type !== '_price_method') {

			foreach ( array('_min', '_max' ) as $min_or_max ) {

				$meta_key = '_' . $region_key . $min_or_max . $price_type . '_variation_id';

				$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $meta_key ) );			
			}		
		}		
	}	
}

$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_price_based_country_%'");

?>
