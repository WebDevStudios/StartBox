;(function($) {
$(document).ready(function(){

// We only want these styles applied when javascript is enabled
	$('div#thumbs').css({'width' : '285px', 'float' : 'left', 'margin-left' : '12px'});
	$('div.content').css('display', 'block');

	// Initially set opacity on thumbs and add
	// additional styling for hover effect on thumbs
	var onMouseOutOpacity = 0.5;
	$('#thumbs ul.thumbs li').css('opacity', onMouseOutOpacity)
		.hover(
			function () {
				$(this).not('.selected').fadeTo('fast', 1.0);
			}, 
			function () {
				$(this).not('.selected').fadeTo('fast', onMouseOutOpacity);
			}
		);
		
	var gallery = $('#gallery').galleriffic('#thumbs', {
		delay:                  4000,
		numThumbs:              12,
		preloadAhead:           10,
		enableTopPager:         false,
		enableBottomPager:      true,
		imageContainerSel:      '#slideshow',
		controlsContainerSel:   '#controls',
		captionContainerSel:    '#caption',
		loadingContainerSel:    '#loading',
		renderSSControls:       true,
		renderNavControls:      true,
		playLinkText:           'Play Slideshow',
		pauseLinkText:          'Pause Slideshow',
		prevLinkText:           '&lsaquo; Previous Photo',
		nextLinkText:           'Next Photo &rsaquo;',
		nextPageLinkText:       'Next &rsaquo;',
		prevPageLinkText:       '&lsaquo; Prev',
		enableHistory:          true,
		autoStart:              false,
		onChange:               function(prevIndex, nextIndex) {
			$('#thumbs ul.thumbs').children()
				.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
				.eq(nextIndex).fadeTo('fast', 1.0);
		},
		onTransitionOut:        function(callback) {
			$('#caption').fadeOut('fast');
			$('#slideshow').fadeOut('fast', callback);
		},
		onTransitionIn:         function() {
			$('#slideshow, #caption').fadeIn('fast');
		},
		onPageTransitionOut:    function(callback) {
			$('#thumbs ul.thumbs').fadeOut('fast', callback);
		},
		onPageTransitionIn:     function() {
			$('#thumbs ul.thumbs').fadeIn('fast');
		}
	});
	
});
})(jQuery);