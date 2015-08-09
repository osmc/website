<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WCPBC_Install Class
 *
 * Installation related functions and actions.
 *
 * @author 		oscargare 
 * @version     1.3.2
 */

class WCPBC_Install {
	
	/**
	 * Hooks.
	 */
	public static function init() {		
		
		add_action( 'admin_init', array( __CLASS__, 'update_actions' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'check_version' ) );				
	}

	/**
	 * Get install version
	 */
	public static function get_install_version() {

		$install_version = get_option( 'wc_price_based_country_version', null );

		if ( is_null( $install_version ) && get_option('_oga_wppbc_countries_groups') ) {
			$install_version = '1.3.1';
		}

		return $install_version;
	}	

	/**
	 * Install function 
	 */ 
	public static function install(){

		// Queue upgrades
		$current_version = self::get_install_version();
		
		if ( version_compare( WCPBC()->version, $current_version, '<' ) && null !== $current_version ) {
			add_action( 'admin_notices', array( __CLASS__, 'update_notice' ) );
		} else {
			delete_option( 'wc_price_based_country_version' );
			add_option( 'wc_price_based_country_version', WCPBC()->version );
		}
	}

	/**
	 * check_version function.
	 */
	public static function check_version() {
				
		if (  ! defined( 'IFRAME_REQUEST' ) && self::get_install_version() !== WCPBC()->version ) {
			add_action( 'admin_notices', array( __CLASS__, 'update_notice' ) );

		} else {
			self::check_default_customer_address();
		}
	}

	/**
	 * check woocommerce default customer address
	 */
	public static function check_default_customer_address() {

		global $pagenow;		
		
		if ( ! ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && (  ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' ) ) ) ) {

			$default_customer_address = get_option('woocommerce_default_customer_address');

			if ( $default_customer_address !== 'geolocation' && $default_customer_address !== 'geolocation_ajax' ){

				add_action( 'admin_notices', array( __CLASS__, 'geolocation_notice' ) );	
			}
		}
	}

	/**
	 * Update db admin notice
	 */	
	public static function update_notice() {
		?>
		<div class="updated woocommerce-message wc-connect">
			<p><?php _e( '<strong>WooCommerce Price Based Country Database Update Required</strong> &#8211; We just need to update your install to the latest version', 'wc-price-based-country' ); ?></p>
			<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_wc_price_based_country', 'true', admin_url( 'admin.php?page=wc-settings&tab=price_based_country' ) ) ); ?>" class="wc-update-now button-primary"><?php _e( 'Run the updater', 'woocommerce' ); ?></a></p>
		</div>
		<script type="text/javascript">
			jQuery('.wc-update-now').click('click', function(){
				var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'woocommerce' ); ?>' );
				return answer;
			});
		</script>
		<?php
	}

	/**
	 * Geolocation address notice
	 */	
	public static function geolocation_notice() {
		?>
		<div class="updated woocommerce-message wc-connect">
			<p><?php _e( '<strong>WooCommerce Price Based Country</strong> required Geolocation Address to determine the customers default address. Go WooCommerce settings page and set <strong>Default Customer Address</strong> to <em>Geolocate Address</em>.', 'wc-price-based-country' ); ?></p>			
			<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings' ) ); ?>" class="button-primary"><?php _e( 'Go WooCommerce General Setting', 'woocommerce' ); ?></a></p>
		</div>		
		<?php
	}

	/**
	 * Handle updates
	 */
	public static function update_actions() {

		if ( ! empty( $_GET['do_update_wc_price_based_country'] ) ) {

			$install_version = self::get_install_version();
			$db_updates         = array(
				'1.3.2' => 'updates/wcpbc-update-1.3.2.php'
			);

			foreach ( $db_updates as $version => $updater ) {
				if ( version_compare( $install_version, $version, '<' ) ) {
					include( $updater );				
				}
			}

			update_option( 'wc_price_based_country_version', WCPBC()->version  );		
		}

		if ( isset($_GET['wc_price_based_country_donate_hide']) && $_GET['wc_price_based_country_donate_hide'] ) {
			update_option('wc_price_based_country_hide_ads', 'yes');
		}

	}
}

WCPBC_Install::init();