<?php
/**
 * My Subscriptions
 */
?>

<?php if ( ! empty( $pre_orders ) ) : ?>
<h2>Pre-ordered OSMC products</h2>
	<table class="shop_table my_account_pre_orders my_account_orders">

		<thead>
			<tr>
				<th class="pre-order-order-number"><span class="nobr"><?php _e( 'Order', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-title"><span class="nobr"><?php _e( 'Product', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-status"><span class="nobr"><?php _e( 'Status', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-release-date"><span class="nobr"><?php _e( 'Release Date', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-actions"></th>
			</tr>
		</thead>

		<tbody>
		<?php foreach ( $pre_orders as $order ) : ?>
			<?php $item = WC_Pre_Orders_Order::get_pre_order_item( $order ); ?>
			<tr class="order">
				<td class="order-number" width="1%">
					<a href="<?php echo esc_url( add_query_arg( 'order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) ); ?>"><?php echo $order->get_order_number(); ?></a>
				</td>
				<td class="pre-order-title">
					<a href="<?php echo get_post_permalink( $item['product_id'] ); ?>">
						<?php echo $item['name']; ?>
					</a>
				</td>
				<td class="pre-order-status" style="text-align:left; white-space:nowrap;">
					<?php echo WC_Pre_Orders_Order::get_pre_order_status_to_display( $order ); ?>
				</td>
				<td class="pre-order-release-date">
					<?php echo WC_Pre_Orders_Product::get_localized_availability_date( $item['product_id'] ); ?>
				</td>
				<td class="pre-order-actions order-actions">
					<?php foreach( $actions[ $order->id ] as $key => $action ) : ?>
					<a href="<?php echo esc_url( $action['url'] ); ?>" class="button <?php echo sanitize_html_class( $key ) ?>"><?php echo esc_html( $action['name'] ); ?></a>
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>

	</table>

<?php endif;
