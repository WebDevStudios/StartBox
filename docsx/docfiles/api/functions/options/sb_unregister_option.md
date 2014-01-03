/*
Title: sb_unregister_option
Description: Parameters and examples of the sb_unregister_option function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# sb_unregister_option

## Description

Remove a single option from a metabox on the Theme Options Page.

## Usage

	<?php sb_unregister_option( $metabox, $option_name, $new_value ); ?>

## Parameters

* **metabox**

	(string) (required) The name of the metabox where the option will appear.

	* Default: None

* **option_name**

	(string) (required) The name of the option to add.

	* Default: None

* **new_value**

	(mixed) (required) A new value to save to the database after removing the option.

	* Default: None

## Examples

	<?php
	// Remove the "Enable Admin Links" option from the Footer Settings metabox and set it's value to false.
	function child_remove_options() {
		sb_unregister_option( 'sb_footer_settings', 'enable_admin', false );
	}
	add_action('init', 'child_remove_options');
	?>

## Notes

## Change Log

Since: 2.4.9

## Source File

sb_unregister_option() is located in /startbox/includes/functions/admin_settings.php

## Related

**[Options API](http://docs.wpstartbox.com/Options_API)**: [Adding Option Panels](http://docs.wpstartbox.com/Functions:sb_register_settings), [Removing Option Panels](http://docs.wpstartbox.com/Functions:sb_unregister_settings), [Creating Options](http://docs.wpstartbox.com/Options_API:Creating_Options), [Interacting With Your Options](http://docs.wpstartbox.com/Options_API#Interacting_With_Your_Options)
