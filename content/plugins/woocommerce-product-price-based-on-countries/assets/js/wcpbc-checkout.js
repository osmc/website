jQuery( function( $ ) {
	
	var orderReviewHtlm = $('#order_review').html();

	$( document ).ajaxComplete(function( event, xhr, settings ) {	

		if ( settings.data.indexOf('woocommerce_update_order_review') > 1 && orderReviewHtlm !== $('#order_review').html() ){

			$.ajax( $fragment_refresh );	

			orderReviewHtlm = $('#order_review').html();
		}		
	});
	
});