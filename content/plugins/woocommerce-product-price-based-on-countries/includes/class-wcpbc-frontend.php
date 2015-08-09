<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCPBC_Frontend' ) ) :

require_once 'class-wcpbc-customer.php';	

/**
 * WCPBC_Frontend
 *
 * WooCommerce Price Based Country Front-End
 *
 * @class 		WCPBC_Frontend
 * @version		1.3.2
 * @author 		oscargare
 */
class WCPBC_Frontend {

	/**
	 * @var WCPBC_Customer $customer
	 */
	protected $customer = null;

	function __construct(){
		
		add_action( 'woocommerce_init', array(&$this, 'init') );		

		add_action( 'wp_enqueue_scripts', array( &$this, 'load_checkout_script' ) );

		add_action( 'woocommerce_checkout_update_order_review', array( &$this, 'checkout_country_update' ) );									

		add_action( 'wcpbc_manual_country_selector', array( &$this, 'country_select' ) );

		add_filter( 'woocommerce_currency',  array( &$this, 'currency' ) );

		add_filter('woocommerce_get_price', array( &$this, 'get_price' ), 10, 2 );

		add_filter( 'woocommerce_get_regular_price', array( &$this, 'get_regular_price') , 10, 2 );

		add_filter( 'woocommerce_get_sale_price', array( &$this, 'get_sale_price') , 10, 2 );								
						
		add_filter( 'woocommerce_get_variation_price', array( &$this, 'get_variation_price' ), 10, 4 );		

		add_filter( 'woocommerce_get_variation_regular_price', array( &$this, 'get_variation_regular_price' ), 10, 4 );	

		add_filter( 'woocommerce_get_sale_regular_price', array( &$this, 'get_variation_sale_price' ), 10, 4 );		
		
		add_shortcode( 'wcpbc_country_selector', array( &$this, 'country_select' ) );

	}		

	/**
	 * Instance WCPBC Customer after WooCommerce init	 
	 */
	public function init() {
		
		if ( ! isset( $_POST['wcpbc-manual-country'] ) && get_option('wc_price_based_country_test_mode', 'no') === 'yes' && $test_country = get_option('wc_price_based_country_test_country') ) {

			/* set test country */
			WC()->customer->set_country( $test_country );

			/* add test store message */
			add_action( 'wp_footer', array( &$this, 'test_store' ) );

		} elseif ( isset( $_POST['wcpbc-manual-country'] ) && $_POST['wcpbc-manual-country'] ) {			
			
			/* set customer WooCommerce customer country*/
			WC()->customer->set_country($_POST['wcpbc-manual-country']);
		}

		$this->customer = new WCPBC_Customer();								

	}

	/**
	 * Add script to checkout page	 
	 */
	public function load_checkout_script( ) {

		if ( is_checkout() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'wc-price-based-country-checkout', WCPBC()->plugin_url() . 'assets/js/wcpbc-checkout' . $suffix . '.js', array( 'wc-checkout', 'wc-cart-fragments' ), WC_VERSION, true );
		}

	}

	/**
	 * Update WCPBC Customer country when order review is update
	 */
	public function checkout_country_update( $post_data ) {			
		
		if ( isset( $_POST['country'] ) && ! in_array( $_POST['country'] , $this->customer->countries ) ) {
			
			$this->customer->set_country( $_POST['country'] );
						
		}
	}

	/**
	 * Output manual country select form
	 */
	public function country_select() {

		$all_countries = WC()->countries->get_countries();		
		$base_country = wc_get_base_location();			

		$countries[ $base_country['country'] ] = $all_countries[$base_country['country']];

		foreach ( WCPBC()->get_regions() as $region ) {
			
			foreach ( $region['countries'] as $country ) {

				if ( ! array_key_exists( $country, $countries ) ) {
					$countries[ $country ] = $all_countries[$country];					
				}
			}			
		}

		asort( $countries );
		
		$other_country = key( array_diff_key($all_countries, $countries ) );
		
		$countries[$other_country] = apply_filters( 'wcpbc_other_countries_text', __( 'Other countries' ) );	

		wc_get_template('country-selector.php', array( 'countries' => $countries ), 'woocommerce-product-price-based-on-countries/',  WCPBC()->plugin_path()  . '/templates/' );
	}

	/**
	 * Return test store message 
	 */
	public function test_store() {

		echo '<p class="demo_store">This is a demo store for testing purposes.</p>' ;
	}
	
	/**
	 * Return currency
	 * @return string currency
	 */
	public function currency( $currency ) {

		$wppbc_currency = $currency;
		
		if ( $this->customer->currency !== '' ) {
			
			$wppbc_currency = $this->customer->currency;
			
		}
		
		return $wppbc_currency;
	}		
	
	/**
	 * Returns the product's active price.
	 * @return string price
	 */
	public function get_price ( $price, $product, $price_type = '_price' ) {	
		
		$wcpbc_price = $price;
		
		if ( $this->customer->group_key ) {					
			
			$meta_key_preffix = '_' . $this->customer->group_key;

			if ( get_class( $product ) == 'WC_Product_Variation' ) {
				
				$post_id = $product->variation_id;	

				$meta_key_preffix .= '_variable';
				
			} else {
				$post_id = $product->id; 
			}
			
			$price_method = get_post_meta( $post_id, $meta_key_preffix . '_price_method', true ); 

			if ( $price_method === 'manual') {

				$wcpbc_price = get_post_meta( $post_id, $meta_key_preffix . $price_type, true );

			} elseif ( $this->customer->exchange_rate && !empty( $price ) ) {

					$wcpbc_price = ( $price * $this->customer->exchange_rate );							
			} 						
		}
			
		return $wcpbc_price;
	}

	/**
	 * Returns the product's regular price.
	 * @return string price
	 */
	public function get_regular_price ($price, $product) {			
		
		return $this->get_price( $price, $product, '_regular_price');
	}	
	
	/**
	 * Returns the product's sale price
	 * @return string price
	 */
	public function get_sale_price ( $price, $product ) {	
		
		return $this->get_price( $price, $product, '_sale_price');
	}
	
	/**
	 * Get the min or max variation active price.
	 * @param  string $min_or_max - min or max
	 * @param  boolean  $display Whether the value is going to be displayed
	 * @return string price
	 */		
	public function get_variation_price( $price, $product, $min_or_max, $display, $price_type = '_price' ) {		
		$wcpbc_price = $price;
		
		if ( $this->customer->group_key ) {

			$variation_id = get_post_meta( $product->id, '_' . $this->customer->group_key . '_' . $min_or_max . $price_type . '_variation_id', true );

			if ( $variation_id ) {

				$variation = $product->get_child( $variation_id );

				if ( $variation) {

					$price_function = 'get' . $price_type;

					$wcpbc_price = $variation->$price_function();

					if ( $display ) {
						$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
						$wcpbc_price      = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $wcpbc_price ) : $variation->get_price_excluding_tax( 1, $wcpbc_price );
					}
				}

			} elseif( $wcpbc_price && $this->customer->exchange_rate && $price_type !== '_price') {

				$wcpbc_price = $wcpbc_price * $this->customer->exchange_rate;
			}
		}		

		return $wcpbc_price;
	}	
	
	/**
	 * Get the min or max variation regular price.
	 * @param  string $min_or_max - min or max
	 * @param  boolean  $display Whether the value is going to be displayed
	 * @return string price
	 */
	public function get_variation_regular_price( $price, $product, $min_or_max, $display ) {		
		
		return $this->get_variation_price( $price, $product, $min_or_max, $display, '_regular_price' );
	}		

	/**
	 * Get the min or max variation sale price.
	 * @param  string $min_or_max - min or max
	 * @param  boolean  $display Whether the value is going to be displayed
	 * @return string price
	 */
	public function get_variation_sale_price( $price, $product, $min_or_max, $display ) {		
		
		return $this->get_variation_price( $price, $product, $min_or_max, $display, '_sale_price' );
	}
		 
}

endif;

$wcpbc_frontend = new WCPBC_Frontend();

?>
