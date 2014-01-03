/*
Title: sb_unregister_settings
Description: Parameters and examples of the sb_unregister_settings function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# sb_unregister_settings

## Description

Remove a single metabox from the Theme Options page. You can use sb_remove_default_settings() to remove all default metaboxes.

## Usage

	<?php sb_unregister_settings( $class_name ); ?>

## Parameters

* **class_name**

	(integer) (required) The name of the new metabox to remove.

	* Default: None

## Examples

To unregister a default metabox, such as the Footer Settings, wrap sb_unregister_setting('class_name') inside a function and hook it to admin_init. Like so:

	<?php
	// Unregister an options panel
	function yourtheme_custom_options() {
		sb_unregister_settings( 'sb_footer_settings' );
	}
	add_action( 'admin_init', 'yourtheme_custom_options' );
	?>

> Note: This function must be hooked into admin_init in order to fire correctly.

### Default Option Panels

Below is a list of the default StartBox option panels sorted alphabetically by class name.

* sb_analytics_settings
* sb_content_settings
* sb_feedburner_settings
* sb_footer_settings
* sb_header_settings
* sb_navigation_settings
* sb_pushup_settings
* sb_seo_settings
* sb_settings_help
* sb_upgrade_settings

## Notes

Use sb_remove_default_settings() to remove all of the default panels at once.

## Change Log

Since: 2.4.2

## Source File

sb_unregister_settings() is located in /startbox/includes/functions/admin_settings.php
