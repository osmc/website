<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC-Pre-Orders/Templates/Email
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Customer pre-ordered order email
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

$availability_date_text = ( ! empty( $availability_date ) ) ? sprintf( __( ' on %s.', 'wc-pre-orders' ), $availability_date ) : '.';

if ( WC_Pre_Orders_Order::order_will_be_charged_upon_release( $order ) ) :

	if ( WC_Pre_Orders_Order::order_has_payment_token( $order ) )
		echo sprintf( __( "Your pre-order has been received. You will be automatically charged for your order via your selected payment method when your pre-order is released%s Your order details are shown below for your reference.", 'wc-pre-orders' ), $availability_date_text ) . "\n\n";
	else
		echo sprintf( __( "Your pre-order has been received. You will be prompted for payment for your order when your pre-order is released%s Your order details are shown below for your reference.", 'wc-pre-orders' ), $availability_date_text ) . "\n\n";

else :

	echo sprintf( __( "Your pre-order has been received. You will be notified when your pre-order is released%s Your order details are shown below for your reference.", 'wc-pre-orders' ), $availability_date_text )  . "\n\n";

endif;

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, false, $plain_text );

echo sprintf( __( 'Order number: %s', 'wc-pre-orders'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order date: %s', 'wc-pre-orders'), date_i18n( woocommerce_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, false, $plain_text );

echo "\n" . $order->email_order_items_table( false, true, false, '', '', true );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, false, $plain_text );

echo __( 'Your details', 'wc-pre-orders' ) . "\n\n";

if ( $order->billing_email )
	echo __( 'Email:', 'wc-pre-orders' ); echo $order->billing_email. "\n";

if ( $order->billing_phone )
	echo __( 'Tel:', 'wc-pre-orders' ); ?> <?php echo $order->billing_phone. "\n";

woocommerce_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
