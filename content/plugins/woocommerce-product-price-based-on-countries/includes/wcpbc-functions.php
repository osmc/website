<?php
require_once 'third-party/vendor/autoload.php';
use GeoIp2\Database\Reader;

/**
 * WooCommerce Price Based Country Functions
 *
 * General functions available on both the front-end and admin.
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Retrun country IsoCode from IP
 *
 * @return String
 */
if ( ! function_exists('get_country_from_ip') ) {

	function get_country_from_ip( $ip ) {

		$isoCode = '';

		try {

			$reader = new Reader( WCPBC_GEOIP_DB );
			$record = $reader->country( $ip );
			$isoCode = $record->country->isoCode;	

		} catch (Exception $e) {
			
			error_log( $e->getMessage() );
		}

		return $isoCode;

	}
}

/**
 * Retrun country IsoCode from client IP
 *
 * @return String
 */

if ( ! function_exists('country_from_client_ip') ) {

	function country_from_client_ip() {		

		$debug_ip = get_option( 'wc_price_based_country_debug_ip' );
		$debug_enabled = get_option( 'wc_price_based_country_debug_mode' );

		if ( $debug_enabled == 'yes' && ! empty( $debug_ip ) ) {

			$client_ip = $debug_ip;

		} else {

			if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && $_SERVER['HTTP_CLIENT_IP'] ) {
			
				$client_ip = $_SERVER['HTTP_CLIENT_IP'];
				
			} elseif( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] ) {
				
				$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
			} else {
				
				$client_ip = $_SERVER['REMOTE_ADDR'];
			}			

		}	
		
		return get_country_from_ip( $client_ip );
	}
}

/**
 * Download GeoIp Database
 *
 *@return void
 */
if ( ! function_exists('wcpbc_donwload_geoipdb') ) {

	function wcpbc_donwload_geoipdb() {		

		$result = '';

		// We need the download_url() function, it should exists on virtually all installs of PHP, but if it doesn't for some reason, bail out.
		if( function_exists( 'download_url' ) ) {

			// This is the location of the file to download.
			$download_url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz';

			// Check to see if the subdirectory we're going to upload to exists, if not create it.
			if( !file_exists( WCPBC_UPLOAD_DIR ) ) {
				wp_mkdir_p( WCPBC_UPLOAD_DIR ); 			
			}
			
			$tmpFile = download_url( $download_url );
			
			if ( is_wp_error( $tmpFile ) ) {

				$result =  sprintf(__('Error downloading GeoIP database from: %s. %s', 'wc-price-based-country'), $download_url, $tmpFile->get_error_message() ) ;	

			} else {

				// Ungzip File
				$zh = gzopen( $tmpFile, 'rb' );
				$h = fopen( WCPBC_GEOIP_DB, 'wb' );

				// If we failed, through a message, otherwise proceed.
				if ( ! $zh ) {
					$result =  __('Downloaded file could not be opened for reading.', 'wc-price-based-country');

				} elseif (! $h ) {
					$result = sprintf(__('Database could not be written (%s).', 'wc-price-based-country'), $outFile);

				} else {
					
					while ( ($string = gzread($zh, 4096)) != false ){
						fwrite($h, $string, strlen($string));
					}

					gzclose($zh);
					fclose($h);				
				}

				unlink($tmpFile);
			}		

		} //function_exists( 'download_url' ) 

		return $result;
		
	}
}

add_action( 'wcpbc_update_geoip', 'wcpbc_donwload_geoipdb' );

function wcpbc_cron_schedules( $schedules ) {

	// Adds once 4 every 4 weeks to the existing schedules.			

	if( !array_key_exists( '4weeks', $schedules ) ) {
		$schedules['4weeks'] = array(
			'interval' => 2419200,
			'display' => __( 'Once Every 4 Weeks' )
		);
	}

	return $schedules;
}

add_filter( 'cron_schedules', 'wcpbc_cron_schedules' );

/**
 * Deactivate WC_Price_Based_Country delete scheduled event
 */
function wc_price_based_country_deactivate() {
	
	if ( wp_next_scheduled( 'wcpbc_update_geoip' ) ) {
		wp_clear_scheduled_hook( 'wcpbc_update_geoip' );	
	}	
}

register_deactivation_hook( WCPBC_FILE, 'wc_price_based_country_deactivate' );	

/**
 * Activate WC_Price_Based_Country add scheduled event
 */
function wc_price_based_country_activate() {

	if ( get_option( 'wc_price_based_country_update_geoip' ) && ! wp_next_scheduled( 'wcpbc_update_geoip' ) ) {

		wp_schedule_event( time(), '4weeks', 'wcpbc_update_geoip');					
	}	
}

register_activation_hook( WCPBC_FILE, 'wc_price_based_country_activate' );	


?>