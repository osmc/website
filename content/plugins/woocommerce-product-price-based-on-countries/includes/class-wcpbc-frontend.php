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
 * @version		1.3.5
 * @author 		oscargare
 */
class WCPBC_Frontend {

	/**
	 * @var WCPBC_Customer $customer
	 */
	protected $customer = null;

	/**
	 * @var int $filter_widget_min_or_max;
	 */
	protected $filter_widget_min_or_max = 'min';

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

		add_filter( 'woocommerce_get_variation_sale_price', array( &$this, 'get_variation_sale_price' ), 10, 4 );		

		add_filter( 'woocommerce_variation_prices', array( &$this, 'get_variation_prices_array' ), 10, 3 );		
		
		// Price Filter
		add_filter( 'woocommerce_price_filter_results', array( &$this, 'price_filter_results' ), 10, 3 );

		add_filter( 'woocommerce_price_filter_widget_amount', array( &$this, 'price_filter_widget_amount' ) );

		//shortcode country selector
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
			WC()->customer->set_country( $_POST['wcpbc-manual-country'] );
		}

		$this->customer = new WCPBC_Customer();								

	}

	/**
	 * Add script to checkout page	 
	 */
	public function load_checkout_script( ) {

		if ( is_checkout() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( version_compare( WC()->version, '2.4', '<' ) ) {
				$version = '-2.3';
			} else {
				$version = '';
			}

			wp_enqueue_script( 'wc-price-based-country-checkout', WCPBC()->plugin_url() . 'assets/js/wcpbc-checkout' . $version . $suffix . '.js', array( 'wc-checkout', 'wc-cart-fragments' ), WC_VERSION, true );
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

	/**
	 * Get an array of all sale and regular prices from all variations.
	 * @since WooCommerce 2.4
	 * @param array() sale and regular prices for default location
	 * @param WC_Product_Variable 
	 * @param  bool Are prices for display? If so, taxes will be calculated.
	 * @return array()
	 */
	public function get_variation_prices_array( $prices_array, $product, $display ) {

		if ( $this->customer->group_key ) {

			$cache_key = 'var_prices_' . md5( json_encode( array(
				$product->id,
				$display ? WC_Tax::get_rates() : '',
				$this->customer->group_key,
				WC_Cache_Helper::get_transient_version( 'product' )
			) ) );

			if ( false === ( $prices_array = get_transient( $cache_key ) ) ) {				

				$prices            = array();
				$regular_prices    = array();
				$sale_prices       = array();
				$tax_display_mode  = get_option( 'woocommerce_tax_display_shop' );

				foreach ( $product->get_children( true ) as $variation_id ) {

					if ( $variation = $product->get_child( $variation_id ) ) {
							
						$price 			= $variation->get_price();
						$regular_price 	= $variation->get_regular_price();
						$sale_price 	= $variation->get_sale_price();

						// If sale price does not equal price, the product is not yet on sale
						if ( $price != $sale_price ) {
							$sale_price = $regular_price;
						}
						// If we are getting prices for display, we need to account for taxes
						if ( $display ) {
							$price         = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $price ) : $variation->get_price_excluding_tax( 1, $price );
							$regular_price = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $regular_price ) : $variation->get_price_excluding_tax( 1, $regular_price );
							$sale_price    = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $sale_price ) : $variation->get_price_excluding_tax( 1, $sale_price );
						}													

						$prices[ $variation_id ]         = $price;
						$regular_prices[ $variation_id ] = $regular_price;
						$sale_prices[ $variation_id ]    = $sale_price;

					}
					
				}

				asort( $prices );
				asort( $regular_prices );
				asort( $sale_prices );

				$prices_array  = array(
					'price'         => $prices,
					'regular_price' => $regular_prices,
					'sale_price'    => $sale_prices
				);

				set_transient( $cache_key, $prices_array, DAY_IN_SECONDS * 30 );				
			}

		}			

		return $prices_array;
	}

	/**
	 * Return matched produts where price between min and max
	 *
	 * @param array $matched_products_query
	 * @param int $min 
	 * @param int $max
	 * @return array
	 */
	public function price_filter_results( $matched_products_query, $min, $max ){

		global $wpdb;

		if ( $this->customer->group_key ) {
			
			$_price_method = '_' . $this->customer->group_key . '_price_method';
			$_price = '_' . $this->customer->group_key . '_price';

			$sql = $wpdb->prepare('SELECT DISTINCT ID, post_parent, post_type FROM %1$s 
					INNER JOIN %2$s wc_price ON ID = wc_price.post_id and wc_price.meta_key = "_price"
					LEFT JOIN %2$s price_method ON ID = price_method.post_id and price_method.meta_key = "%3$s"
					LEFT JOIN %2$s price ON ID = price.post_id and price.meta_key = "%4$s"
					WHERE post_type IN ( "product", "product_variation" )
					AND post_status = "publish"					
					AND IF(IFNULL(price_method.meta_value, "exchange_rate") = "exchange_rate", wc_price.meta_value * %5$s, price.meta_value) BETWEEN %6$d AND %7$d'
			, $wpdb->posts, $wpdb->postmeta, $_price_method, $_price, $this->customer->exchange_rate, $min, $max);

			$matched_products_query = $wpdb->get_results( $sql, OBJECT_K );			

		}

		return $matched_products_query;
	}

	/**
	 * Return de min and max value of price filter widget. Beta only works when have'nt any manually price greater or less that $min_or_max * exchage rate
	 *
	 * @param int $min_or_max
	 * @return array
	 */	
	public function price_filter_widget_amount( $min_or_max ) {

		if ( $this->customer->exchange_rate ) {

			$min_or_max = $min_or_max * $this->customer->exchange_rate;

			if ($this->filter_widget_min_or_max == 'min') {
				
				$min_or_max = floor($min_or_max);
				$this->filter_widget_min_or_max = 'max';

			} else {
				$min_or_max = ceil($min_or_max);
			}
		}

		return $min_or_max;
	}
		 
}

endif;

$wcpbc_frontend = new WCPBC_Frontend();

?>
