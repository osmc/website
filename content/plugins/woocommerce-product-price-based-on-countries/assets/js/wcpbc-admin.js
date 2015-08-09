jQuery( function( $ ) {

	$('body').on( 'keyup', '.wcpbc_sale_price[type=text]', function(){
		
		var sale_price_field = $(this);			
		var regular_price_field = $('#' + sale_price_field.attr('id').replace('_sale','_regular') ) ;		

		var sale_price    = parseFloat( accounting.unformat( sale_price_field.val(), woocommerce_admin.mon_decimal_point ) );
		var regular_price = parseFloat( accounting.unformat( regular_price_field.val(), woocommerce_admin.mon_decimal_point ) );		

		if( sale_price >= regular_price ) {
			if ( $(this).parent().find('.wc_error_tip').size() === 0 ) {
				var offset = $(this).position();
				$(this).after( '<div class="wc_error_tip">' + woocommerce_admin.i18_sale_less_than_regular_error + '</div>' );
				$('.wc_error_tip')
					.css('left', offset.left + $(this).width() - ( $(this).width() / 2 ) - ( $('.wc_error_tip').width() / 2 ) )
					.css('top', offset.top + $(this).height() )
					.fadeIn('100');
			}
		} else {
			$('.wc_error_tip').fadeOut('100', function(){ $(this).remove(); } );
		}
		return this;
	});

	$('body').on( 'change', '.wcpbc_sale_price[type=text]', function(){			

		var sale_price_field = $(this);				
		var regular_price_field = $('#' + sale_price_field.attr('id').replace('_sale','_regular') ) ;		

		var sale_price    = parseFloat( accounting.unformat( sale_price_field.val(), woocommerce_admin.mon_decimal_point ) );
		var regular_price = parseFloat( accounting.unformat( regular_price_field.val(), woocommerce_admin.mon_decimal_point ) );

		var sale_price    = parseFloat( accounting.unformat( sale_price_field.val(), woocommerce_admin.mon_decimal_point ) );
		var regular_price = parseFloat( accounting.unformat( regular_price_field.val(), woocommerce_admin.mon_decimal_point ) );

		if( sale_price >= regular_price ) {
			sale_price_field.val( regular_price_field.val() );
		} else {
			$('.wc_error_tip').fadeOut('100', function(){ $(this).remove(); } );
		}
		return this;			

	});


	$('body').on( 'click', '.wcpbc_price_method[type="radio"]', function(){

		var parent_class = '.' + $(this).attr('name') + '_field';					

		parent_class = parent_class.replace('[', '_');
		parent_class = parent_class.replace(']', '');
		
		$(this).parents(parent_class).next().toggle( $(this).val() == 'manual');
		return this;
	});

	$('#wc_price_based_country_test_mode').on('change', function() {
   		if ($(this).is(':checked')) {
   			$('#wc_price_based_country_test_country').closest('tr').show();
   		} else {
   			$('#wc_price_based_country_test_country').closest('tr').hide();
   		}
   	}).change();

});
