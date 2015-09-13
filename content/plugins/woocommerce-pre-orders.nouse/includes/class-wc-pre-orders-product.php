<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC-Pre-Orders/Product
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Pre-Orders Product class
 *
 * Customizes the functionality and display of simple/variable products to support Pre-Orders
 *
 * @since 1.0
 */
class WC_Pre_Orders_Product {


	/**
	 * Adds needed hooks / filters
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add an optional pre-order product message after single product price on the single product page
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_pre_order_product_message' ), 11 );

		// add an optional pre-order product message before the 'add to cart' button on the product shop loop page
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_pre_order_product_message' ), 11 );

		// change the add to cart button text on product shop loop page and single product page
		add_filter( 'add_to_cart_text',          array( $this, 'modify_add_to_cart_button_text' ) );
		add_filter( 'variable_add_to_cart_text', array( $this, 'modify_add_to_cart_button_text' ) );
		add_filter( 'single_add_to_cart_text',   array( $this, 'modify_add_to_cart_button_text' ) );

		// 2.1 Filters
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'modify_add_to_cart_button_text' ), 10 , 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'modify_add_to_cart_button_text' ), 10 , 2 );

		// automatically cancel a pre-order when product is trashed
		add_action( 'wp_trash_post', array( $this, 'maybe_cancel_pre_order_product_trashed' ) );
	}


	/**
	 * Add a customizable message to product's on the shop loop page and / or on the single product page immediately
	 * after the price
	 *
	 * @since 1.0
	 */
	public function add_pre_order_product_message() {
		global $product;

		// only modify products with pre-orders enabled
		if ( ! $this->product_can_be_pre_ordered( $product ) ) {
			return;
		}

		// get custom message
		if ( is_shop() ) {
			$message = get_option( 'wc_pre_orders_shop_loop_product_message' );
		} else {
			$message = get_option( 'wc_pre_orders_single_product_message' );
		}

		// bail if none available
		if ( ! $message ) {
			return;
		}

		// add localized availability date if needed
		$message = str_replace( '{availability_date}', $this->get_localized_availability_date( $product ), $message );

		// add localized availability time if needed
		$message = str_replace( '{availability_time}', $this->get_localized_availability_time( $product ), $message );

		$message = apply_filters( 'wc_pre_orders_product_message', $message, $product );

		echo wp_kses_post( $message );
	}


	/**
	 * Modifies the add to cart button text on product loop page & single product page
	 *
	 * @since 1.0
	 * @param string $default_text default add to cart button text
	 * @return string
	 */
	public function modify_add_to_cart_button_text( $default_text ) {
		global $product;

		// only modify products with pre-orders enabled
		if ( ! $this->product_can_be_pre_ordered( $product ) ) {
			return $default_text;
		}

		// get custom text if set
		$text = get_option( 'wc_pre_orders_add_to_cart_button_text' );

		if ( $text ) {
			return $text;
		} else {
			return $default_text;
		}
	}


	/**
	 * Checks if a given product can be pre-ordered by verifying pre-orders are enabled for it
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if product can be pre-ordered, false otherwise
	 */
	public static function product_can_be_pre_ordered( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return is_object( $product ) && 'yes' === $product->wc_pre_orders_enabled;
	}


	/**
	 * Checks if a given product has active pre-orders
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if product can be pre-ordered, false otherwise
	 */
	public static function product_has_active_pre_orders( $product ) {
		global $wpdb;

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		$order_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT items.order_id AS id
			FROM {$wpdb->prefix}woocommerce_order_items AS items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_meta ON items.order_item_id = item_meta.order_item_id
			LEFT JOIN {$wpdb->postmeta} AS post_meta ON items.order_id = post_meta.post_id
			WHERE
				items.order_item_type = 'line_item' AND
				item_meta.meta_key = '_product_id' AND
				item_meta.meta_value = '%s' AND
				post_meta.meta_key = '_wc_pre_orders_status' AND
				post_meta.meta_value = 'active'
			", $product->id
			)
		);

		return ( ! empty( $order_ids ) );
	}

	/**
	 * Checks if a given pre-order-enabled product is charged upon release
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if pre-order is charged upon release, false otherwise
	 */
	public static function product_is_charged_upon_release( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return 'upon_release' === $product->wc_pre_orders_when_to_charge;
	}


	/**
	 * Checks if a given pre-order-enabled product is charged upfront
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if pre-order is charged upfront, false otherwise
	 */
	public static function product_is_charged_upfront( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return 'upfront' === $product->wc_pre_orders_when_to_charge;
	}


	/**
	 * Checks if a given product has a pre-order fee enabled
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if product has a pre-order fee enabled, false otherwise
	 */
	public static function has_pre_order_fee( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return $product->wc_pre_orders_fee > 0;
	}


	/**
	 * Gets the pre-order fee for a given product
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return string the pre-order fee amount
	 */
	public static function get_pre_order_fee( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return $product->wc_pre_orders_fee;
	}


	/**
	 * Gets the tax status of a pre-order fee by checking the tax status of the product
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if the pre-order fee is taxable, false otherwise
	 */
	public static function get_pre_order_fee_tax_status( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		return 'taxable' === $product->tax_status;
	}


	/**
	 * Gets the availability date of the product localized to the site's date format
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @param string $none_text optional text to return if there is no availability datetime set
	 * @return string the formatted availability date
	 */
	public static function get_localized_availability_date( $product, $none_text = '' ) {

		if ( '' === $none_text ) {
			$none_text = __( 'Soon', 'wc-pre-orders' );
		}

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		$timestamp = self::get_localized_availability_datetime_timestamp( $product );

		if ( ! $timestamp ) {
			return $none_text;
		}

		return apply_filters( 'wc_pre_orders_localized_availability_date', date_i18n( woocommerce_date_format(), $timestamp ), $product, $none_text );
	}


	/**
	 * Gets the availability time of the product formatted according to the site's time format and timezone
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return string the formatted availability time
	 */
	public static function get_localized_availability_time( $product ) {

		$timestamp = self::get_localized_availability_datetime_timestamp( $product );

		$localized_time = date( get_option( 'time_format' ), $timestamp );

		return apply_filters( 'wc_pre_orders_localized_availability_time', $localized_time, $timestamp );
	}


	/**
	 * Gets the availability timestamp of the product localized to the configured
	 * timezone
	 *
	 * @param WC_Product|int $product the product object or post identifier
	 * @return int the timestamp, localized to the current timezone
	 */
	public static function get_localized_availability_datetime_timestamp( $product ) {

		if ( ! is_object( $product ) ) {
			$product = get_product( $product );
		}

		if ( ! $product || ! $timestamp = $product->wc_pre_orders_availability_datetime ) {
			return 0;
		}

		try {

			// get datetime object from unix timestamp
			$datetime = new DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

			// set the timezone to the site timezone
			$datetime->setTimezone( new DateTimeZone( self::get_wp_timezone_string() ) );

			// return the unix timestamp adjusted to reflect the site's timezone
			return $timestamp + $datetime->getOffset();

		} catch ( Exception $e ) {
			global $wc_pre_orders;

			// log error
			$wc_pre_orders->log( $e->getMessage() );
			return 0;
		}
	}


	/**
	 * Returns the timezone string for a site, even if it's set to a UTC offset
	 *
	 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
	 *
	 * @since 1.0
	 * @return string valid PHP timezone string
	 */
	public static function get_wp_timezone_string() {

		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}

		// get UTC offset, if it isn't set then return UTC
		if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
			return 'UTC';
		}

		// adjust UTC offset from hours to seconds
		$utc_offset *= 3600;

		// attempt to guess the timezone string from the UTC offset
		$timezone = timezone_name_from_abbr( '', $utc_offset );

		// last try, guess timezone string manually
		if ( false === $timezone ) {

			$is_dst = date( 'I' );

			foreach ( timezone_abbreviations_list() as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
						return $city['timezone_id'];
					}
				}
			}
		}

		// fallback to UTC
		return 'UTC';
	}

	/**
	 * Maybe cancel pre order when product is trashed
	 *
	 * @param int $product_id Product ID
	 * @return void
	 */
	public function maybe_cancel_pre_order_product_trashed( $product_id ) {
		global $wpdb;

		$orders = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT order_items.order_id
				FROM {$wpdb->prefix}woocommerce_order_items AS order_items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
					ON order_itemmeta.order_item_id = order_items.order_item_id
				WHERE order_itemmeta.meta_key = '_product_id'
				AND order_itemmeta.meta_value = %d
			", $product_id )
		);

		foreach ( $orders as $order_data ) {
			$order = new WC_Order( $order_data->order_id );

			if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) && WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
				WC_Pre_Orders_Order::update_pre_order_status( $order, 'cancelled' );
			}
		}
	}


} // end \WC_Pre_Orders_Product class
