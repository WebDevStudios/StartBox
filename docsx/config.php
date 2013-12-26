<?php

/*
// Override any of the default settings below:

$config['site_title'] = 'Pico';			// Site title
$config['base_url'] = ''; 				// Override base URL (e.g. http://example.com)
$config['theme'] = 'default'; 			// Set the theme (defaults to "default")
$config['date_format'] = 'jS M Y';		// Set the PHP date format
$config['twig_config'] = array(			// Twig settings
	'cache' => false,					// To enable Twig caching change this to CACHE_DIR
	'autoescape' => false,				// Autoescape Twig vars
	'debug' => false					// Enable Twig debug
);
$config['pages_order_by'] = 'alpha';	// Order pages by "alpha" or "date"
$config['pages_order'] = 'asc';			// Order pages "asc" or "desc"
$config['excerpt_length'] = 50;			// The pages excerpt length (in words)

// To add a custom config setting:

$config['custom_setting'] = 'Hello'; 	// Can be accessed by {{ config.custom_setting }} in a theme

*/
$config['theme'] = 'startbox';
$config['site_title'] = 'StartBox';
$config['twig_config'] = array(			// Twig settings
	'cache' => false,					// To enable Twig caching change this to CACHE_DIR
	'autoescape' => false,				// Autoescape Twig vars
);
//Set our top level navigation URLs. Provide in the order that you wish for them to appear.
$config['topnav'] = array(
	'getting_started',
	'extensions',
	'api',
	'resources',
	'troubleshooting'
);
$config['topnav_exclude'] = array(
	'extension_hooks',
	'shortcodes',
	'tutorials'
);
//Messy, but gets rid of links for the time being.
$config['mcb_toc_top_txt'] = '';
$config['mcb_toc_caption'] = 'Table of contents';

//Custom header meta data.
$config['custom_meta_values'] = array(
	'modified_date' => 'Modified Date'
);
