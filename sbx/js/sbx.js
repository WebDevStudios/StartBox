jQuery(document).ready(function($) {

	// Initialize colorbox, if available
	if ( jQuery.isFunction(jQuery.fn.colorbox) ) {
		$(".colorbox").colorbox({ maxWidth: "90%", maxHeight: "90%", opacity:0.6} );
		$(".ext").colorbox({ iframe:true, innerWidth:"90%", innerHeight:"90%", opacity:0.6} );
		$(".colorbox-inline").colorbox({ maxWidth: "90%", maxHeight: "90%", opacity:0.6, inline:true });
	}

	// Add Smooth Scrolling to all links,
	// except those with a class of "noscroll"
	if ( jQuery.isFunction(jQuery.fn.smoothScroll) ) {
		$('a').smoothScroll({exclude: ['.noscroll']});
	}

	// Hide all elements with the class of .hideme
	$('.hideme').hide();

	// Slider Toggle -- an anchor tag with class
	// '.toggle' will expand it's href target.
	$(".toggle")
		.addClass("noscroll")
		.each( function() { $($(this).attr('href')).hide(); } )
		.toggle(
		function () {
			$(this).text($(this).text().replace("More", "Less"));
			$($(this).attr('href')).slideDown(300);
		},
		function () {
			$(this).text($(this).text().replace("Less", "More"));
			$($(this).attr('href')).slideUp(300);
		}
	);

});
