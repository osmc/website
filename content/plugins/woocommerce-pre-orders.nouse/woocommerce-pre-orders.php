<?php
/**
 * Plugin Name: WooCommerce Pre-Orders
 * Plugin URI: http://www.woothemes.com/products/pre-orders/
 * Description: Sell pre-orders for products in your WooCommerce store
 * Author: WooThemes
 * Author URI: http://www.woothemes.com
 * Version: 1.4.2
 * Text Domain: woocommerce-pre-orders
 * Domain Path: /languages/
 *
 * Copyright: (c) 2015 WooThemes
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Pre-Orders
 * @author    WooThemes
 * @category  Marketing
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'b2dc75e7d55e6f5bbfaccb59830f66b7', '178477' );

// Check if WooCommerce is active and deactivate extension if it's not
if ( ! is_woocommerce_active() ) {
	return;
}

/**
 * The WC_Pre_Orders global object
 *
 * @name $wc_pre_orders
 * @global WC_Pre_Orders $GLOBALS['wc_pre_orders']
 */
$GLOBALS['wc_pre_orders'] = new WC_Pre_Orders();

/**
 * Main Plugin Class
 *
 * @since 1.0
 */
class WC_Pre_Orders {

	/**
	 * Plugin version number
	 */
	const VERSION = '1.4.2';

	/**
	 * Plugin file path without trailing slash
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Plugin url without trailing slash
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * WC_Logger instance
	 *
	 * @var object
	 */
	private $logger;

	/**
	 * Setup main plugin class
	 *
	 * @since  1.0
	 * @return \WC_Pre_Orders
	 */
	public function __construct() {

		// load core classes
		$this->load_classes();

		// load classes that require WC to be loaded
		add_action( 'woocommerce_init', array( $this, 'init' ) );

		// load template functions
		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

		// add pre-order notification emails
		add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );

		// add 'pay later' payment gateway
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_pay_later_gateway' ) );

		// Hook up emails
		foreach ( array( 'wc_pre_order_status_new_to_active', 'wc_pre_order_status_completed', 'wc_pre_order_status_active_to_cancelled', 'wc_pre_orders_pre_order_date_changed' ) as $action ) {
			add_action( $action, array( $this, 'send_transactional_email' ), 10, 2 );
		}

		// Load translation files
		add_action( 'init', array( $this, 'load_translation' ) );

		// Un-schedule events on plugin deactivation
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Load core classes
	 *
	 * @since 1.0
	 */
	public function load_classes() {

		// load wp-cron hooks for scheduled events
		require( 'includes/class-wc-pre-orders-cron.php' );
		$this->cron = new WC_Pre_Orders_Cron();

		// load manager class to process pre-order actions
		require( 'includes/class-wc-pre-orders-manager.php' );
		$this->manager = new WC_Pre_Orders_Manager();

		// load product customizations / tweaks
		require( 'includes/class-wc-pre-orders-product.php' );
		$this->product = new WC_Pre_Orders_Product();

		// Load cart customizations / overrides
		require( 'includes/class-wc-pre-orders-cart.php' );
		$this->cart = new WC_Pre_Orders_Cart();

		// Load checkout customizations / overrides
		require( 'includes/class-wc-pre-orders-checkout.php' );
		$this->checkout = new WC_Pre_Orders_Checkout();

		// Load order hooks
		require( 'includes/class-wc-pre-orders-order.php' );
		$this->order = new WC_Pre_Orders_Order();
	}

	/**
	 * Load actions and filters that require WC to be loaded
	 *
	 * @since 1.0
	 */
	public function init() {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// Load admin.
			require( 'includes/admin/class-wc-pre-orders-admin.php' );

			// add a 'Configure' link to the plugin action links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_configure_plugin_link' ) );

		} else {

			// Watch for cancel URL action
			add_action( 'init', array( $this->manager, 'check_cancel_pre_order' ) );

			// add countdown shortcode
			add_shortcode( 'woocommerce_pre_order_countdown', array( $this, 'pre_order_countdown_shortcode' ) );

			// display pre-orders on a user's account page
			add_action( 'woocommerce_before_my_account', 'wc_pre_orders_my_account_pre_orders' );
		}
	}

	/**
	 * Function used to Init WooCommerce Pre-orders Template Functions - This makes them pluggable by plugins and themes.
	 *
	 * @since 1.0
	 */
	public function include_template_functions() {
		include( 'templates/woocommerce-pre-orders-template.php' );
	}

	/**
	 * Add the 'pay later' gateway, which replaces gateways that do not support pre-orders when the pre-order is charged
	 * upon release
	 *
	 * @since 1.0
	 */
	public function add_pay_later_gateway( $gateways ) {
		require_once( 'includes/gateways/class-wc-pre-orders-gateway-pay-later.php' );

		$gateways[] = 'WC_Pre_Orders_Gateway_Pay_Later';

		return $gateways;
	}

	/**
	 * Pre-order countdown shortcode
	 *
	 * @param array $atts associative array of shortcode parameters
	 *
	 * @return string shortcode content
	 */
	public function pre_order_countdown_shortcode( $atts ) {
		global $woocommerce;

		require( 'includes/shortcodes/class-wc-pre-orders-shortcode-countdown.php' );

		return $woocommerce->shortcode_wrapper( array( 'WC_Pre_Orders_Shortcode_Countdown', 'output' ), $atts, array( 'class' => 'woocommerce-pre-orders' ) );
	}

	/**
	 * Adds Pre-Order email classes
	 *
	 * @since 1.0
	 */
	public function add_email_classes( $email_classes ) {

		foreach ( array( 'new-pre-order', 'pre-order-available', 'pre-order-cancelled', 'pre-order-date-changed', 'pre-ordered' ) as $class_file_name ) {
			require_once( "includes/emails/class-wc-pre-orders-email-{$class_file_name}.php" );
		}

		$email_classes['WC_Pre_Orders_Email_New_Pre_Order']          = new WC_Pre_Orders_Email_New_Pre_Order();
		$email_classes['WC_Pre_Orders_Email_Pre_Ordered']            = new WC_Pre_Orders_Email_Pre_Ordered();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Date_Changed'] = new WC_Pre_Orders_Email_Pre_Order_Date_Changed();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Cancelled']    = new WC_Pre_Orders_Email_Pre_Order_Cancelled();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Available']    = new WC_Pre_Orders_Email_Pre_Order_Available();

		return $email_classes;
	}

	/**
	 * Sends transactional email by hooking into pre-order status changes
	 *
	 * @since 1.0
	 */
	public function send_transactional_email( $args = array(), $message = '' ) {
		global $woocommerce;

		$woocommerce->mailer();

		do_action( current_filter() . '_notification', $args, $message );
	}

	/**
	 * Remove terms and scheduled events on plugin deactivation
	 *
	 * @since 1.0
	 */
	public function deactivate() {

		// Remove scheduling function before removing scheduled hook, or else it will get re-added
		remove_action( 'init', array( $this->cron, 'add_scheduled_events') );

		// clear pre-order completion check event
		wp_clear_scheduled_hook( 'wc_pre_orders_completion_check' );
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 1.0
	 *
	 * @param array $actions associative array of action names to anchor tags
	 *
	 * @return array associative array of plugin action links
	 */
	public function add_configure_plugin_link( $actions ) {
		// Add the link to the front of the actions list
		return ( array_merge( array( 'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wc_pre_orders' ), __( 'Configure', 'wc-pre-orders' ) ) ),
			$actions )
		);
	}

	/**
	 * Load plugin text domain
	 *
	 * @since  1.0
	 */
	public function load_translation() {
		load_plugin_textdomain( 'wc-pre-orders', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Returns the plugin's path without a trailing slash
	 *
	 * @since  1.0
	 *
	 * @return string the plugin path
	 */
	public function get_plugin_path() {
		if ( $this->plugin_path ) {
			return $this->plugin_path;
		}

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Returns the plugin's url without a trailing slash
	 *
	 * @since  1.0
	 *
	 * @return string the plugin url
	 */
	public function get_plugin_url() {
		if ( $this->plugin_url ) {
			return $this->plugin_url;
		}

		return $this->plugin_url = plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
	}

	/**
	 * Log errors to WooCommerce log
	 *
	 * @since 1.0
	 *
	 * @param string $message message to log
	 */
	public function log( $message ) {
		global $woocommerce;

		if ( ! is_object( $this->logger ) ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->logger = new WC_Logger();
			} else {
				$this->logger = $woocommerce->logger();
			}
		}

		$this->logger->add( 'pre-orders', $message );
	}

	/**
	 * Get supported product types.
	 *
	 * @return array
	 */
	public static function get_supported_product_types() {
		$product_types = array(
			'simple',
			'variable',
			'composite',
			'bundle',
			'booking'
		);

		return apply_filters( 'wc_pre_orders_supported_product_types', $product_types );
	}
}
