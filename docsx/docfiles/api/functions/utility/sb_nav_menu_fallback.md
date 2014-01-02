/*
Title: sb_nav_menu_fallback
Description: Parameters and examples of the sb_nav_menu_fallback function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 01-02-14
 */

# sb_nav_menu_fallback

## Description

Creates a fallback function for [wp_nav_menu()](http://codex.wordpress.org/Function_Reference/wp_nav_menu) when no menu has been created and assigned yet.

## Usage

	<?php sb_nav_menu($args); ?>

### Default Usage

	<?php $args = array(
		'depth'         => 0,
		'sort_column'   => 'menu_order, post_title',
		'menu_id'       => 'page_menu',
		'menu_class'    => 'menu',
		'include'       => '',
		'exclude'       => '',
		'echo'          => true,
		'show_home'     => true,
		'link_before'   => '',
		'link_after'    => ''
	); ?>

## Parameters

* **depth**

	(integer) (optional) how many levels of the hierarchy are to be included where 0 means all

	* Default: 0

* **sort_column**

	(string) (optional) which columns from the database to sort by

	* Default: 'menu_order, post_title'

* **menu_id**

	(string) (optional) The ID that is applied to the ul element which forms the menu

	* Default: None

* **menu_class**

	(string) (optional) CSS class to use for the ul element which forms the menu

	* Default: None

* **include**

	(string) (optional) Comma-separated list of IDs to include

	* Default: ''

* **exclude**

	(string) (optional) Comma-separated list of IDs to exclude

	* Default: ''

* **echo**

	(boolean) (optional) Whether to echo the menu or return it.

	* Default: true

* **show_home**

	(boolean) (optional) Whether or not to prepend a link to the homepage to the menu.

	* Default: true

* **link_before**

	(string) (optional) Output text before the link text

	* Default: ''

* **link_after**

	(string) (optional) Output text after the link text

	* Default: ''

## Examples
