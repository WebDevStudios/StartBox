;(function($) {
$(document).ready(function(){
	
	// Initialize colorbox
	$(".colorbox").colorbox({maxWidth: "90%", maxHeight: "90%", opacity:.6});
	$(".ext").colorbox({iframe:true, innerWidth:"90%", innerHeight:"90%", opacity:.6});
	$(".colorbox-inline").colorbox({maxWidth: "90%", maxHeight: "90%", opacity:.6, inline:true})

	// Hide all elements with the class of .hideme
	$('.hideme').hide();

	// Slider Toggle -- an anchor tag with class '.toggle' will expand it's href target.
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
	
	// Autofill input box with a default value (via http://www.joesak.com/2008/11/19/a-jquery-function-to-auto-fill-input-fields-and-clear-them-on-click/)
	function autoFill(id, v){
		$(id).css({ color: "#b2adad" }).attr({ value: v }).focus(function(){
			if($(this).val()==v){
				$(this).val("").css({ color: "#333" });
			}
		}).blur(function(){
			if($(this).val()==""){
				$(this).css({ color: "#aaa" }).val(v);
			}
		});

	}
	
	// Autofill several default fields
	autoFill($(".searchtext"), $(".searchtext").attr("title")); // Search field
	autoFill($("#author"), "Your Name"); // Comment Author Name
	autoFill($("#email"), "Your E-mail"); // Comment Author E-mail
	autoFill($("#url"), "Your Website (Optional)"); // Comment Author Website
	
	// Add Smooth Scrolling to all links, except those with a class of "noscroll"
	$('a').smoothScroll({exclude: ['.noscroll']});
	
	// Dynamically replace default "Your Name" with user's name (textchange function courtesy of ZURB: http://www.zurb.com/playground/jquery-text-change-custom-event)
	(function(a){a.event.special.textchange={setup:function(){a(this).bind("keyup.textchange",a.event.special.textchange.handler);a(this).bind("cut.textchange paste.textchange input.textchange",a.event.special.textchange.delayedHandler)},teardown:function(){a(this).unbind(".textchange")},handler:function(){a.event.special.textchange.triggerIfChanged(a(this))},delayedHandler:function(){var b=a(this);setTimeout(function(){a.event.special.textchange.triggerIfChanged(b)},25)},triggerIfChanged:function(b){var c=
	b.attr("contenteditable")?b.html():b.val();if(c!==b.data("lastValue")){b.trigger("textchange",b.data("lastValue"));b.data("lastValue",c)}}};a.event.special.hastext={setup:function(){a(this).bind("textchange",a.event.special.hastext.handler)},teardown:function(){a(this).unbind("textchange",a.event.special.hastext.handler)},handler:function(b,c){if((c===""||c===undefined)&&c!==a(this).val())a(this).trigger("hastext")}};a.event.special.notext={setup:function(){a(this).bind("textchange",a.event.special.notext.handler)},
	teardown:function(){a(this).unbind("textchange",a.event.special.notext.handler)},handler:function(b,c){a(this).val()===""&&a(this).val()!==c&&a(this).trigger("notext")}}})(jQuery);
	
	$('#author').bind('textchange', function (event, previousText) {
	  $('#authorname').text($(this).val());
	});
	
	// Adds URL Encoding and Decoding to JQuery (needed for setting gravatar default)
	$.extend({URLEncode:function(c){var o='';var x=0;c=c.toString();var r=/(^[a-zA-Z0-9_.]*)/;
	  while(x<c.length){var m=r.exec(c.substr(x));
	    if(m!=null && m.length>1 && m[1]!=''){o+=m[1];x+=m[1].length;
	    }else{if(c[x]==' ')o+='+';else{var d=c.charCodeAt(x);var h=d.toString(16);
	    o+='%'+(h.length<2?'0':'')+h.toUpperCase();}x++;}}return o;},
	URLDecode:function(s){var o=s;var binVal,t;var r=/(%[^%]{2})/;
	  while((m=r.exec(o))!=null && m.length>1 && m[1]!=''){b=parseInt(m[1].substr(1),16);
	  t=String.fromCharCode(b);o=o.replace(m[1],t);}return o;}
	});
	
	// Dynamically replace default gravatar with user's gravatar
	var gravatar_default = $.URLEncode("http://"+document.location.hostname+"/wp-content/themes/startbox/images/comments/gravatar.png");
	var gravatar_size = 60;
	$("#email").change(function() {
		$('#commentform .avatar').attr("src","http://www.gravatar.com/avatar/"+$.md5($('#email').val())+"?s="+gravatar_size+"&default="+gravatar_default);
	});
	

});
})(jQuery);