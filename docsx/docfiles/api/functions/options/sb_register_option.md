/*
Title: sb_register_option
Description: Parameters and examples of the sb_register_option function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 12-31-13
 */

# Functions: sb_register_option

## Description

Add a new option to an existing metabox on the Theme Options Page. To remove an option, see sb_unregister_option().

## Usage

	<?php sb_register_option( $metabox, $option_name, $args ); ?>

## Parameters

* **$metabox**

	(string) (required) The name of the metabox where the option will appear.

	* Default: None

* **$option_name**

	(string) (required) The name of the option to add.

	* Default: None

* **$args**

	(array) (required) The option's arguments to pass through the Options API.

	* Default: None

## Examples

	<?php
	// Add a single option from an options panel
	function child_add_options() {
		sb_register_option( 'sb_footer_settings', 'my_new_option', array(
			'type' => 'text',
			'label' => 'A New Textbox',
			'default' => 'My Starting Value',
			'desc' => 'A short description.' ) );
	}
	add_action('init', 'child_add_options');
	?>

## Notes
## Change Log

Since: 2.4.9
## Source File

sb_register_option() is located in /startbox/includes/functions/admin_settings.php

## Related

**[Options API](http://docs.wpstartbox.com/Options_API)**: [Adding Option Panels](http://docs.wpstartbox.com/Functions:sb_register_settings), [Removing Option Panels](http://docs.wpstartbox.com/Functions:sb_unregister_settings), [Creating Options](http://docs.wpstartbox.com/Options_API:Creating_Options), [Interacting With Your Options](http://docs.wpstartbox.com/Options_API#Interacting_With_Your_Options)
