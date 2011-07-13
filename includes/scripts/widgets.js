;(function($) {
$(document).ready(function(){
	
	var $contentClass = $(".content-selector");
	var $pagesettings = $contentClass.parent().nextAll('.page-settings');
	var $postsettings = $contentClass.parent().nextAll('.post-settings');
	
	$contentClass.live("change", function() {
		$contentSelector = $(this);
		if( $contentSelector.val() === 'page' ) {
			$contentSelector.parent().nextAll('.post-settings').slideUp();
			$contentSelector.parent().nextAll('.page-settings').slideDown();
		} else {
			$contentSelector.parent().nextAll('.post-settings').slideDown();
			$contentSelector.parent().nextAll('.page-settings').slideUp();
		}
	});

	$('.advanced-settings').hide();
	$('.advanced-toggle:checked').parent().next('.advanced-settings').show();
	$('.advanced-toggle').live("change", function() {
	    $(this).parent().next('.advanced-settings')[
	        $(this).attr('checked') ? 'slideDown' : 'slideUp'
	    ]();
	});
	
	///// Ads Widget ////////////////////////////////////////////////////////////////////////
	
	// Add link to add another banner
	$('a.add').click(function() { 
		$('<li><p><label for="text">Link Text:</label> <input name="text" type="text" value="" class="text" /> <p><label for="url">Link URL:</label> <input name="url" type="text" value="" class="url" /></p> <p><label for="image">Image URL:</label> <input type="text" value="" class="ads" /></p> <a href="#nogo" class="remove">Remove</a></li>').appendTo($(this).prev('ul.ads')); // append (add) a new input to the form.
	});

	// Remove link to remove specific banner
	$('a.remove').live("click", function() { $(this).parent('li').remove(); });
	
});
})(jQuery);