<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC-Pre-Orders/Admin
 * @author    WooThemes
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre-Orders Admin Orders class.
 */
class WC_Pre_Orders_Admin_Orders {

	/**
	 * Initialize the admin order actions.
	 */
	public function __construct() {
		// Add pre-order emails to list of available emails to resend.
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'maybe_allow_resend_of_pre_order_emails' ) );

		// Hook to make sure pre order is properly set up when added through admin.
		add_action( 'save_post', array( $this, 'check_manual_order_for_pre_order_products' ), 10, 1 );
	}

	/**
	 * Add pre-order emails to the list of order emails that can be resent, based on the pre-order status.
	 *
	 * @param  array $available_emails Simple array of WC_Email class IDs that can be resent.
	 *
	 * @return array
	 */
	public function maybe_allow_resend_of_pre_order_emails( $available_emails ) {
		global $theorder;

		if ( WC_Pre_Orders_Order::order_contains_pre_order( $theorder ) ) {

			$available_emails[] = 'wc_pre_orders_pre_ordered';

			$pre_order_status = WC_Pre_Orders_Order::get_pre_order_status( $theorder );

			if ( 'cancelled' === $pre_order_status ) {
				$available_emails[] = 'wc_pre_orders_pre_order_cancelled';
			}

			if ( 'completed' === $pre_order_status ) {
				$available_emails[] = 'wc_pre_orders_pre_order_available';
			}
		}

		return $available_emails;
	}

	/**
	 * Marks the order as being a pre order if it contains pre order products in
	 * case an order gets added manually from the administration panel.
	 *
	 * @param int $order_id ID of the newly saved order.
	 */
	public function check_manual_order_for_pre_order_products( $order_id ) {
		// Make sure we are in the administration panel and we're saving an order.
		if ( ! is_admin() || ! isset( $_POST['post_type'] ) || 'shop_order' != $_POST['post_type'] ) {
			return;
		}

		$order = new WC_Order( $order_id );

		// Check if the order hasn't been processed already.
		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
			return;
		}

		// Order has not been processed yet (or doesn't contain pre orders).
		$contains_pre_orders = false;

		foreach ( $order->get_items() as $item ) {
			if ( 'line_item' == $item['type'] ) {
				$product = get_product( $item['item_meta']['_product_id'][0] );

				if ( 'yes' == $product->wc_pre_orders_enabled ) {
					// Set correct flags for this order, making it a pre order
					update_post_meta( $order_id, '_wc_pre_orders_is_pre_order', 1 );
					update_post_meta( $order_id, '_wc_pre_orders_when_charged', $product->wc_pre_orders_when_to_charge );
					return;
				}
			}
		}
	}
}

new WC_Pre_Orders_Admin_Orders();
