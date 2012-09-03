;(function($) {
$(document).ready(function(){

	// Add collapse toggles to postboxes
	postboxes.add_postbox_toggles( 'sb_admin' );

	// Collapse postboxes that should be closed
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	
	// Dynamically replace font-size units
	$("select.font-unit").change(function() {
		$('span.font-unit').text($(this).val());
	});
	
	var thumbnails = ".post_thumbnail_rss, .post_thumbnail_use_attachments, .post_thumbnail_align, .post_thumbnail_hide_nophoto, .post_thumbnail_width, .post_thumbnail_height, .post_thumbnail_default_image";
	$(thumbnails).hide();
	
	// Show/Hide Logo Options
	$("#startbox\\[logo-select\\]").change(function(){
		
		if ( $(this).val() == 'image' ) {
			$('.logo-align').slideDown(300);
			$('.logo-text').slideUp(300);
			$('.logo-image').slideDown(300);
		} else if ( $(this).val() == 'text' ) {
			$('.logo-align').slideDown(300);
			$('.logo-text').slideDown(300);
			$('.logo-image').slideUp(300);
		} else {
			$('.logo-align').slideUp(300);
			$('.logo-text').slideUp(300);
			$('.logo-image').slideUp(300);
		}
		
	});
	$("#startbox\\[logo-select\\]").change();

	// Show/Hide Thumbnail options
	$("#startbox\\[enable_post_thumbnails\\]").change(function(){
		
		if ( $(this).attr('checked') ) {
			$(thumbnails).slideDown(300);
			$("#startbox\\[post_thumbnail_hide_nophoto\\]").change();
		} else {
			$(thumbnails).slideUp(300);
		}
		
	});
	$("#startbox\\[enable_post_thumbnails\\]").change();
	
	// Show/Hide Default Thumbnail Image option
	$("#startbox\\[post_thumbnail_hide_nophoto\\]").change(function(){
		
		if ( $(this).attr('checked') ) {
			$('.post_thumbnail_default_image').slideUp(300);
		} else {
			$('.post_thumbnail_default_image').slideDown(300);
		}
		
	});
	$("#startbox\\[post_thumbnail_hide_nophoto\\]").change();
	
	// Dynamically show/hide primary extras
	
	// Show Primary Nav Social Extras, only if social is selected and primary nav isn't disabled
	var primary_social = ".primary_nav-social-extras";
	var primary = ".primary_nav-position, .primary_nav-depth, .primary_nav-extras, .primary_nav-enable-home, primary_social";
	$(primary).hide();
	
	// Show/Hide Primary nav social extras
	$("#startbox\\[primary_nav-extras\\]").change(function(){
		if ( $(this).val() == 'social' && $("#startbox\\[primary_nav\\]").val() != 'none' ) {
			$(primary_social).slideDown(300);
		} else {
			$(primary_social).slideUp(300);
		}
	});
	
	// Show/Hide Primary nav options
	$("#startbox\\[primary_nav\\]").change(function(){
		
		if ( $(this).val() != 'none' ) {
			$(primary).slideDown(300);
		} else {
			$(primary).slideUp(300);
		}
		$("#startbox\\[primary_nav-extras\\]").change(); // Trigger the social options to show/hide accordingly
		
	});
	$("#startbox\\[primary_nav\\]").change();
	
	// Show/Hide Secondary nav options
	var secondary_social = ".secondary_nav-social-extras";
	var secondary = ".secondary_nav-position, .secondary_nav-depth, .secondary_nav-extras, .secondary_nav-enable-home, secondary_social";
	$(secondary).hide();
	
	// Show/Hide Primary nav social extras
	$("#startbox\\[secondary_nav-extras\\]").change(function(){
		if ( $(this).val() == 'social' && $("#startbox\\[secondary_nav\\]").val() != 'none' ) {
			$(secondary_social).slideDown(300);
		} else {
			$(secondary_social).slideUp(300);
		}
	});
	
	$("#startbox\\[secondary_nav\\]").change(function(){
		
		if ( $(this).val() != 'none' ) {
			$(secondary).slideDown(300);
		} else {
			$(secondary).slideUp(300);
		}
		$("#startbox\\[secondary_nav-extras\\]").change(); // Trigger the social options to show/hide accordingly
		
	});
	$("#startbox\\[secondary_nav\\]").change();
	
	// Show/Hide Footer nav options
	var footer = ".footer_nav-position, .footer_nav-depth, .footer_nav-extras, .footer_nav-enable-home";
	$(footer).hide();
	$("#startbox\\[footer_nav\\]").change(function(){
		
		if ( $(this).val() != 'none' ) {
			$(footer).slideDown(300);
		} else {
			$(footer).slideUp(300);
		}
		
	});
	$("#startbox\\[footer_nav\\]").change();
	
	// Imagepicker / Uploader
	function sb_attach_imagepicker()
	{
		$('.imagepickerinput').each(function(index) {
			
			
			if(!this.hasEventHander)
			{
				var instance = this; // because this function has a strange structure and the this gets overwritten
			
				// Hide the Preview link if the input value is empty, show it as soon as there is a value
				if(!$('.uploadinput', instance).val())
				{
					$('.previewlink', instance).hide();
				}
				$('.uploadinput', instance).change(function() {
					if(!$('.uploadinput', instance).val())
					{
						$('.previewlink', instance).hide();
					} else {
						$('.previewlink', instance).show();
					}
				});
				
				// Preview
				$('.previewlink', instance).click(function() {
					$(this).colorbox({href: $('.uploadinput', instance).val(), maxWidth:"90%", maxHeight:"90%", opacity:.6});
				});
				
				// Choose
				$('.chooselink', instance).click(function() {
					$(this).colorbox({iframe:true, innerWidth:"675px", innerHeight:"90%", opacity:.6});
					
					// Handle Media Library
                    window.send_to_editor = function(html) {
                        imgurl = $('img',html).attr('src');
                        if(!imgurl)
                            imgurl = html; // Credit: leoj3n
                        $('.uploadinput', instance).val(imgurl);
                        $('.previewlink', instance).show().attr('href', imgurl);
                        $.fn.colorbox.close();
                    }
					
				});
				
				// Upload
				var fid = 'userfile'; // used to find file like $_FILES[fid]
				new AjaxUpload($('.uploadlink', instance), {
					action: ajaxurl,
					name: fid,
					data: { action: 'sb_action_handle_upload_ajax', _ajax_nonce: '<?php echo $NONCE; ?>', file_id: fid },
					responseType: 'json',
					onSubmit: function(file , ext){
						//if (ext && new RegExp('^(' + allowed.join('|') + ')$').test(ext)){
						if (ext && /^(jpg|png|jpeg|gif|ico)$/.test(ext)){							
							$('.uploadresult', instance).html('Uploading ' + file + '...');
						} else {
							// extension is not allowed
							$('.uploadresult', instance).html('<span class="error">Error: Only images are allowed.</span>');
							// cancel upload
							return false;				
						}
				
					},
					onComplete: function(file, response){
						if(response.error != '') {
							$('.uploadresult', instance).html(response.error); // show user the error
						} else {
							$('.uploadresult', instance).html('<a href="' + response.full + '" target="_blank" class="previewlink">' + file + '</a> has been uploaded!');
							$('.uploadinput', instance).val(response.full);
							$('.previewlink', instance).show().attr('href', response.full);
						}
					}
				});
				
				this.hasEventHander = true;
			}		
		});
	} // sb_attach_imagepicker()
	sb_attach_imagepicker();

	
	// Attach the Colorpicker
		$('.colorpickerinput').each(function(index) {
			if(!this.hasEventHander)
			{

				var instance = this; // because this function has a strange structure and the this gets overwritten
				var val = $(".colorinput", instance).val(); // on attach value

				if(val.length < 4)
				{
					val = '#ffffff';
					$(".colorinput", instance).val(val);	
				}

				$("span.colorselector span", instance).css('backgroundColor', val);

				function sb_validate_colorinput(inst1, inst2)
				{
					var value = $(inst1).val().replace('#',''); // strip hash
					if(value == 'transparent')
						return true;
					if(value.length != 6)
					{
						if(value.length == 3)
							value = value + value;
						else if(value.length == 0)
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