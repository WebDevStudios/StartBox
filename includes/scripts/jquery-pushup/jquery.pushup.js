/* 
 * Original Copyright
 * 
 * Pushup
 * Copyright (c) 2008 Nick Stakenburg (www.nickstakenburg.com)
 *
 * License: MIT-style license.
 * Website: http://www.pushuptheweb.com
 *
 */

/* 
 * Modified for jQuery by Stuart Loxton (www.stuartloxton.com)
*/

jQuery.pushup = {
	Version: '1.0.0',
	options: {
		appearDelay: 0.5,
		fadeDelay: 6,
		images: 'images/',
		message: 'Important browser update available',
		reminder: {
			hours: 6,
			message: 'Remind me again in #{hours}'
		}
	},
	updateLinks: {
		IE: 'http://www.microsoft.com/windows/downloads/ie/',
		Firefox: 'http://www.getfirefox.com',
		Safari: 'http://www.apple.com/safari/download/',
		Opera: 'http://www.opera.com/download/'
	},
	browsVer: {
		Firefox: (navigator.userAgent.indexOf('Firefox') > -1) ? parseFloat(navigator.userAgent.match(/Firefox[\/\s](\d+)/)[1]) : false,
		IE: (jQuery.browser.msie) ? parseFloat(jQuery.browser.version) : false,
		Safari: (jQuery.browser.safari) ? parseFloat(jQuery.browser.version) : false,
		Opera: (jQuery.browser.opera) ? parseFloat(jQuery.browser.version) : false
	},
	browsers: {
		Firefox: 3,
		IE: 8,
		Opera: 9,
		Safari: 6
	},
	init: function() {
		jQuery.each(jQuery.pushup.browsVer, function(x, y) {
			if(y && y < jQuery.pushup.browsers[x]) {
				if (!jQuery.pushup.options.ignoreReminder && jQuery.pushup.cookiesEnabled && Cookie.get('_pushupBlocked')) { return; } else {
					time = (jQuery.pushup.options.appearDelay != undefined) ? jQuery.pushup.options.appearDelay * 1000 : 0;
					setTimeout('jQuery.pushup.show(jQuery.pushup.browserUsed)', time);
				}
			}
		});
	},
	show: function() {
		browser = typeof arguments[0] == 'string' ?
		arguments[0] : jQuery.pushup.browserUsed || 'IE';
		elm = document.createElement('div');
		elm.style.display = 'none';
		elm.id = 'pushup';
		jQuery('body').prepend(elm);
		icon = jQuery(document.createElement('div')).addClass('pushup_icon');
		message = jQuery(document.createElement('span')).addClass('pushup_message');
		messagelink = jQuery(document.createElement('a')).addClass('pushup_messageLink').attr('target', '_blank').append(icon).append(message);
		jQuery('#pushup').append(messagelink);
		jQuery('.pushup_message').html(jQuery.pushup.options.message);
		
		var hours = jQuery.pushup.options.reminder.hours;
		if (hours && jQuery.pushup.cookiesEnabled) {
			var H = hours + ' hour' + (hours > 1 ? 's' : ''),
			message = jQuery.pushup.options.reminder.message.replace('#{hours}', H);
			hourelem = jQuery(document.createElement('a')).attr('href', '#').addClass('pushup_reminder').html(message);
			jQuery('#pushup').append(hourelem);
			jQuery('.pushup_reminder').click(function() {
				jQuery.pushup.setReminder(jQuery.pushup.options.reminder.hours);
				jQuery.pushup.hide();
				return false;
			});
		}
		if(/^http\:\/\//.test(jQuery.pushup.options.images) || /^\//.test(jQuery.pushup.options.images)) {
			imgSrc = jQuery.pushup.options.images;
		} else {
			jQuery('script[src]').each(function(x, y) {
				if(/jquery\.pushup/.test(jQuery(y).attr('src'))) {
					srcFol =  jQuery(y).attr('src').replace('jquery.pushup.js', '');
					imgSrc = srcFol + jQuery.pushup.options.images;
				}
			});
		}
		styles = (jQuery.pushup.browsVer.IE < 7 && jQuery.pushup.browsVer.IE) ? {
			filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' +
		      imgSrc+browser.toLowerCase()  + '.png\'\', sizingMethod=\'crop\')'
		} : {
			background: 'url('+imgSrc+browser.toLowerCase()+'.png) no-repeat top left'
		}
		jQuery('.pushup_icon').css(styles);
		jQuery('#pushup').fadeIn('slow');
		if(jQuery.pushup.options.fadeDelay != undefined) {
			time = jQuery.pushup.options.fadeDelay * 1000;
			setTimeout('jQuery.pushup.hide()', time);
		}
	},
	hide: function() { jQuery('#pushup').fadeOut('slow'); },
	setReminder: function(hours) {
		Cookie.set('_pushupBlocked', 'blocked', { duration: 1 / 24 * hours })
	},
	resetReminder: function() { Cookie.remove('_pushupBlocked') }
	
}
jQuery.each(jQuery.pushup.browsVer, function(x,y) {
	if(y) {
		jQuery.pushup.browserUsed = x;
	}
})

// Based on the work of Peter-Paul Koch - http://www.quirksmode.org
var Cookie = {
  set: function(name, value) {
    var expires = '', options = arguments[2] || {};
    if (options.duration) {
      var date = new Date();
      date.setTime(date.getTime() + options.duration * 1000 * 60 * 60 * 24);
      value += '; expires=' + date.toGMTString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
  },

  remove: function(name) { this.set(name, '', -1) },

  get: function(name) {
    var cookies = document.cookie.split(';'), nameEQ = name + "=";
    for (var i = 0, l = cookies.length; i < l; i++) {
      var c = cookies[i];
      while (c.charAt(0) == ' ')
        c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0)
        return c.substring(nameEQ.length, c.length);
    }
    return null;
  }
};
jQuery.pushup.cookiesEnabled = (function(test) {
  if (Cookie.get(test)) return true;
  Cookie.set(test, 'test', { duration: 15 });
  return Cookie.get(test);
})('_pushupCookiesEnabled');
jQuery(function() {
	jQuery.pushup.init();
});
