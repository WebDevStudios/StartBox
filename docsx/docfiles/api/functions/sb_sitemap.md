# Functions: sb_sitemap

## Description

Function for producing a sitemap.

## Usage

	<?php sb_sitemap( $args ); ?>

## Default Usage

	<?php $args = array(
		'show_pages'		=> true,
		'show_categories'	=> true,
		'show_posts'		=> true,
		'exclude_pages'		=> '',
		'exclude_categories'	=> '',
		'class'			=> 'sitemap',
		'container_class'	=> '',
		'header_container'	=> 'h3',
		'subheader_container'	=> 'h4',
		'echo'			=> true
	); ?>

By default, this usage shows:

* An unordered list of pages
* An unordered list of categories
* An unordered list of posts
* No excluded pages
* No excluded categories
* All unordered lists have a CSS class of "sitemap"
* All list containers have a CSS class of "sitemap-container"
* The complete output has a header wrapped in an
* Each list has a header wrapped in an
* The resulting HTML output is echoed

## Parameters

* **$args**

	(array) (required) All of the arguments the function parses.

	* Default: None

## Arguments

* **$show_pages**

	(boolean) (optional) Whether or not to include pages in output.

	* Default: true

* **$show_categories**

	(boolean) (optional) Whether or not to include categories in output.

	* Default: true

* **$show_posts**

	(boolean) (optional) Whether or not to include posts in output.

	* Default: true

* **$exclude_pages**

	(string) (optional) A comma-separated list of Page IDs to be excluded from the list (example: '3,7,31').

	* Default: None

* **$exclude_categories**

	(string) (optional) A comma-separated list of Category IDs to be excluded from the list (example: '3,7,31').

	* Default: None

* **$class**

	(string) (optional) The CSS class for each unordered list.

	* Default: sitemap

* **$container_class**

	(string) (optional) The CSS class for the containing element for each section.

	* Default: sitemap-container

* **$header_container**

	(string) (optional) The html element that wraps each header at the beginning of the output.

	* Default: h3

* **$subhead_container**

	(string) (optional) The html element that wraps each section subhead.

	* Default: h4

* **$echo**

	(boolean) (optional) Whether to echo (1, true) or return (0, false) the resulting HTML.

	* Default: true

## Examples

	<?php sb_sitemap() ?>

## Notes

Available Filter: 'sb_sitemap_defaults' for passing new defaults.

## Change Log

Since: 2.4.9

## Source File

sb_sitemap() is located in /startbox/includes/functions/custom.php

## Related
See also: [sb_add_action()](http://docs.wpstartbox.com/Custom_Functions:sb_add_action), [sb_remove_action()](http://docs.wpstartbox.com/Custom_Functions:sb_remove_action), [WordPress Plugin API](http://codex.wordpress.org/Plugin_API).