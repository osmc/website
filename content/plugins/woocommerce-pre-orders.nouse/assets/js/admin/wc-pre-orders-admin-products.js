/* global wc_pre_orders_admin_products_params */
jQuery( document ).ready( function( $ ) {
	'use strict';

	//
	$( '#woocommerce-product-data #product-type' ).on( 'change', function() {
		var current   = $( this ).val(),
			tab       = $( '.wc_pre_orders_tab' );

		if ( -1 !== $.inArray( current, wc_pre_orders_admin_products_params.product_types ) ) {
			tab.show();
		} else {
			tab.hide();
		}

	}).change();
});
