;(function($) {
$(document).ready(function(){

	// Add collapse toggles to postboxes
	postboxes.add_postbox_toggles( 'sb_admin' );

	// Collapse postboxes that should be closed
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	// Imagepicker / Uploader
	function sb_attach_imagepicker() {
		$('.imagepickerinput').each(function(index) {

			if( ! this.hasEventHander ) {
				var instance = this; // because this function has a strange structure and the "this" gets overwritten

				// Hide the Preview link if the input value is empty, show it as soon as there is a value
				$('.uploadinput', instance).change(function() {
					if ( ! $('.uploadinput', instance).val() ) {
						$('.previewlink', instance).hide();
					} else {
						$('.previewlink', instance).show();
						$('.previewlink', instance).attr( 'href', $('.uploadinput', instance).val() );
					}
				}).change();

				// Upload/Choose File Button (Media Library handler)
				$('.chooselink', instance).click(function(event) {
					event.preventDefault();
					wp.media.editor.open($(this));
					wp.media.editor.send.attachment = function(props, attachment){
						$('.uploadinput', instance).val(attachment.url);
						$('.uploadinput', instance).change();
					};
				});

				this.hasEventHander = true;
			}
		});
	} // sb_attach_imagepicker()
	sb_attach_imagepicker();

	// Attach the Colorpicker
	$('.colorpickerinput').each(function(index) {
		if( ! this.hasEventHander ) {

			var instance = this; // because this function has a strange structure and the this gets overwritten
			var val = $(".colorinput", instance).val(); // on attach value

			if(val.length < 4)
			{
				val = '#ffffff';
				$(".colorinput", instance).val(val);
			}

			$("span.colorselector span", instance).css('backgroundColor', val);

			function sb_validate_colorinput(inst1, inst2) {
				var value = $(inst1).val().replace('#',''); // strip hash
				if(value == 'transparent')
					return true;
				if(value.length != 6)
				{
					if(value.length == 3)
						value = value + value;
					else if(value.length === 0)
						value = 'ffffff';
				}
				value =  '#' + value; // add back hash
				$(inst1).val(value);
				$('.colorselector span', inst2).css('backgroundColor', value);
			}

			$(".colorselector", instance).ColorPicker({
				color: $(".colorinput", instance).val(),
				onShow: function (colpkr) {
					sb_validate_colorinput($(".colorinput", instance), instance);
					$(this).ColorPickerSetColor($(".colorinput", instance).val()); // incase they changed it by hand
					$(colpkr).fadeIn('fast');
					return false;
				},
				jQuery: function (colpkr) {
					$(colpkr).fadeOut('fast');
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('.colorselector span', instance).css('backgroundColor', '#' + hex);
					$('.colorinput', instance).val('#' + hex);
				}
			});

			// if they changed it by hand, validate the input
			$(".colorinput", instance).change(function() {
				sb_validate_colorinput(this, instance);
			});

			this.hasEventHander = true;

		}
	});

});
})(jQuery);