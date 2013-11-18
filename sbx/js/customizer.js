/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
( function( $ ) {

	// Loop through each customizer setting and bind a callback
	// Note: sb_customizer is defined by wp_localize_script()
		// setting.js_callback = the JS function to call
		// setting.control     = the customizer setting ID
		// setting.selector    = the element to target on-page
	$.each( sb_customizer, function( i, setting ) {
		// If callback exists, bind it to customizer changes
		if ( 'function' == typeof setting.js_callback ) {
			wp.customize( setting.control, function( value ) {
				value.bind( function( to ) {
					setting.js_callback( setting.selector, to );
				} );
			} );
		}
	});


	function sbx_change_text( element, value ) {
		$( element ).text( value );
	}

	function sbx_change_text_color( element, value ) {
		$( element ).css( 'color', value );
	}

	function sbx_change_background_color( element, value ) {
		$( element ).css( 'background-color', value );
	}

	function sbx_change_border_color( element, value ) {
		$( element ).css( 'border-color', value );
	}

} )( jQuery );
