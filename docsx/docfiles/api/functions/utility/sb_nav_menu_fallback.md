/*
Title: sb_nav_menu_fallback
Description: Parameters and examples of the sb_nav_menu_fallback function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 12-31-13
 */

> FIX ME

# Functions: sb_nav_menu_fallback

## Description

Helper function for rendering a menu. Created to easily handle menus selected via the Options API

## Usage

	<?php sb_nav_menu($args); ?>

### Default Usage

	<?php $args = array(
		'type'			=> 'pages',
		'class'			=> 'nav',
		'show_home'		=> 1,
		'echo'			=> false,
		'container'		=> 'div',
		'container_id'	=> '',
		'menu_class'	=> '',
		'menu_id'		=> '',
		'before'		=> '',
		'after'			=> '',
		'link_before'	=> '',
		'link_after'	=> '',
		'depth'			=> 0,
		'fallback_cb'	=> 'sb_nav_menu_fallback',
		'extras'		=> '',
		'walker'		=> ''
	); ?>

## Parameters

* **$type**

	(string) (optional) The type of menu that is desired; accepts pages, categories, menu id, menu slug, menu name

	* Default: pages

* **$class**

	(string) (optional) the class that is applied to the container

	* Default: nav

* **$show_home**

	(boolean) (optional) Whether or not to prepend a link to the homepage to the menu.

	* Default: 1 (true)

* **$echo**

	(boolean) (optional) Whether to echo the menu or return it. For returning menu use '0'

	* Default: 1 (true)

* **$container**

	(string) (optional) Whether to wrap the ul, and what to wrap it with. Allowed tags are div and nav. Use false for no container e.g. container => false

	* Default: div

* **$container_id**

	(string) (optional) The ID that is applied to the container

	* Default: None

* **$menu_class**

	(string) (optional) CSS class to use for the ul element which forms the menu

	* Default: None

* **$menu_id**

	(string) (optional) The ID that is applied to the ul element which forms the menu

	* Default: None

* **$before**

	(string) (optional) Output text before the &lt;a&gt; of the link

	* Default: None

* **$after**

	(string) (optional) Output text after the &lt;a&gt; of the link

	* Default: None

* **$link_before**

	(string) (optional) Output text before the link text

	* Default: None

* **$link_after**

	(string) (optional) Output text after the link text

	* Default: None

* **$depth**

	(integer) (optional) how many levels of the hierarchy are to be included where 0 means all

	* Default: 0

* **$fallback_cb**

	(string) (optional) If the menu doesn't exist, the fallback function to use. Set to false for no fallback.

	* Default: wp_page_menu

* **$extras**

	(array) (optional) Extra includes to append to the menu. Accepts "search" and "social.

	* Default: None

* **$walker**

	(object) (optional) Custom walker object to use (Note: You must pass an actual object to use, not a string)

	* Default: new Walker_Nav_Menu

## Examples

### Wrap Each Nav Item

If you want to wrap each of your menu items in a span tag, you could use the following code:

	function my_nav_defaults($defaults) {
		$defaults['link_before'] = '<span>';
		$defaults['link_after'] = '</span>';
		return $defaults
	}
	add_filter( 'sb_nav_menu_defaults', 'my_nav_menu_defaults' );

## Notes

This function uses a custom callback name sb_nav_menu_fallback so that we can properly use a single function to handle pages, categories and custom menus. Additionally, a custom filter is also applied so that all active menus, whether they be pages, categories, or a custom menu, will use the same ".current-menu-item" and ".current-menu-ancestor" classes for the active menu items. The first and last menu items also inherit a ".first" and ".last" class, respectively.

Available filters:

* sb_nav_menu_defaults
* sb_{$menu_id}_menu

Because this function invokes wp_nav_menu, any menus produced via sb_nav_menu have access to the same filters and functionality of wp_nav_menu.

## Change Log

Since: 2.4.8

## Source File

sb_nav_menu() is located in /startbox/includes/functions/custom.php
