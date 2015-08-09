jQuery( document ).ready(function( $ ) {
	// Check if form is submitted and override
	$( '.woothemes-updater-wrap #activate-products' ).submit(function( event ) {
		var license_keys_object = [];
		$('input[name^="license_keys["]').each(function( i, item ) {
			if ( $( this ).val().length > 0 ) {
				var license_object = { name: $( this ).attr("name").replace( 'license_keys[', '' ).replace( ']', '' ), key: $( this ).val() };
				license_keys_object.push( license_object );
			}
		});
		if ( license_keys_object.length > 0 ) {
			$( 'div.error.fade' ).remove();
			$( '#activate-products table.licenses' ).css({ opacity: 0.2 });
			$( '#activate-products' ).attr( 'disabled','disabled' );
			var submit_data = {
				action: 'woothemes_activate_license_keys',
				license_data: license_keys_object,
				security: WTHelper.activate_license_nonce
			};
			$.post( WTHelper.ajax_url, submit_data, function( data ) {
				var json_data = $.parseJSON( data );
				// Check if activation was successfull and reload page to show new activation
				if ( 'true' == json_data.success ) {
					window.location.href = json_data.url;
				}

				// If not sucessfull, show error messages.
				$( '.woothemes-updater-wrap .nav-tab-wrapper' ).after( json_data.message );
				$( '#activate-products table.licenses' ).css({ opacity: 1 });
				$( '#activate-products' ).removeAttr( 'disabled' );
				$('html, body').animate({
					scrollTop: $( '.woothemes-updater-wrap .nav-tab-wrapper' ).offset().top
				}, 2000);
			});
		}
		event.preventDefault();
	});
});