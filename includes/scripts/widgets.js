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
	
});
})(jQuery);