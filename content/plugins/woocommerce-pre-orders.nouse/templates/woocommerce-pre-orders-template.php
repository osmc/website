<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC-Pre-Orders/Templates
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Pluggable template functions
 *
 * @since 1.0
 */

if ( ! function_exists( 'wc_pre_orders_my_account_pre_orders' ) ) {

	/**
	 * Output WooCommerce Pre-orders "My Pre-Orders" table in the user's My Account
	 * page
	 *
	 * @access public
	 */
	function wc_pre_orders_my_account_pre_orders() {
		global $wc_pre_orders;

		$pre_orders = WC_Pre_Orders_Manager::get_users_pre_orders();
		$actions = array();

		// determine the available actions (Cancel)
		foreach ( $pre_orders as $order ) {
			$_actions = array();

			if ( WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
				$_actions['cancel'] = array(
					'url'  => WC_Pre_Orders_Manager::get_users_change_status_link( 'cancelled', $order ),
					'name' => __( 'Cancel', 'wc-pre-orders' )
				);
			}

			$actions[ $order->id ] = $_actions;
		}

		// Load the template
		woocommerce_get_template(
			'myaccount/my-pre-orders.php',
			array(
				'pre_orders' => $pre_orders,
				'actions'    => $actions,
			),
			'',
			$wc_pre_orders->get_plugin_path() . '/templates/'
		);
	}
}
