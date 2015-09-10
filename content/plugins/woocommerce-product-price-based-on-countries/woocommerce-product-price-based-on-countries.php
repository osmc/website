<?php

/*
 Plugin Name: WooCommerce Product Price Based on Countries
 Plugin URI: https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
 Description: Sets products prices based on country of your site's visitor.
 Author: Oscar Gare
 Version: 1.3.5
 Author URI: google.com/+OscarGarciaArenas
 License: GPLv2
*/

 /*
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :	


if ( ! class_exists( 'WC_Product_Price_Based_Country' ) ) :

/**
 * Main WC Product Price Based Country Class
 *
 * @class WC_Product_Price_Based_Country
 * @version	1.3.5
 */
class WC_Product_Price_Based_Country {

	/**
	 * @var string
	 */
	public $version = '1.3.5';

	/**
	 * @var The single instance of the class		 
	 */
	protected static $_instance = null;

	/**
	 * @var $regions
	 */
	protected $regions = null;

	/**
	 * Main WC_Product_Price_Based_Country Instance
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *	 
	 * @static
	 * @see WCPBC()
	 * @return Product Price Based Country - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WC_Product_Price_Based_Country Constructor.
	 */
	public function __construct() {						

		$this->includes();

		register_activation_hook( __FILE__, array( 'WCPBC_Install', 'install' ) );	
	}

	
	/**
	 * Get regions
	 *@return array
	*/
	public function get_regions(){

		if ( is_null( $this->regions ) ) {

			$this->regions = get_option( 'wc_price_based_country_regions', array() );			
		}		

		return $this->regions;
	}	

	/**
	 * What type of request is this?
	 * string $type frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {

		$is_ajax = defined('DOING_AJAX') && DOING_AJAX;

		switch ( $type ) {
			case 'admin' :							
				$ajax_allow_actions = array( 'woocommerce_add_variation', 'woocommerce_load_variations', 'woocommerce_save_variations' );
				return ( is_admin() && !$is_ajax ) || ( is_admin() && $is_ajax && isset( $_POST['action'] ) && in_array( $_POST['action'], $ajax_allow_actions ) );
			
			case 'frontend' :
				return ! $this->is_request('bot') && ( ! is_admin() || ( is_admin() && $is_ajax ) ) && ! defined( 'DOING_CRON' );

			case 'bot':
				$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
				return preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent );
		}
	}

	/**
	 * Include required files used in admin and on the frontend.
	 */
	public function includes() {		

		if ( $this->is_request( 'admin') ) {
			include_once 'includes/class-wcpbc-install.php';	
			include_once 'includes/class-wcpbc-admin.php';	

		} elseif ( $this->is_request( 'frontend') ) {

			require_once 'includes/class-wcpbc-frontend.php';						
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {		
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	
	public function plugin_path(){
		return plugin_dir_path( __FILE__ );
	}

}	//End Class

/**
 * Returns the main instance of WC_Product_Price_Based_Country to prevent the need to use globals.
 *
 * @since  1.3.0
 * @return WC_Product_Price_Based_Country
 */
function WCPBC() {
	return WC_Product_Price_Based_Country::instance();
}

$wc_product_price_based_country = WCPBC();

endif; // ! class_exists( 'WC_Product_Price_Based_Country' )
	
	
else :
	
	add_action( 'admin_init', 'oga_wppbc_deactivate' );
	
	function oga_wppbc_deactivate () {
		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		
	}
	
   add_action( 'admin_notices', 'oga_wppbc_no_woocommerce_admin_notice' );
   
   function oga_wppbc_no_woocommerce_admin_notice () {
	   	?>
	   	<div class="updated">
	   		<p><strong>WooCommerce Product Price Based on Countries</strong> has been deactivated because <a href="http://woothemes.com/">Woocommerce plugin</a> is required</p>
	   	</div>
	   	<?php
    }	
      	
   
endif;



?>