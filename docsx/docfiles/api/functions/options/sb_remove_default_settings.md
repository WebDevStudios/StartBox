/*
Title: sb_remove_default_settings
Description: Parameters and examples of the sb_remove_default_settings function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# sb_remove_default_settings

## Description

Remove all of the default metaboxes StartBox adds to the [[Options API|Theme Options] page.
Usage

	<?php sb_remove_default_settings(); ?>

## Parameters

This function accepts no parameters.

## Examples

	<?php sb_remove_default_settings(); ?>

## Notes

You can also invoke this function defining the constant SB_REMOVE_DEFAULT_SETTINGS as true. i.e.

	define('SB_REMOVE_DEFAULT_SETTINGS', true);

## Change Log

Since: 2.4.8

## Source File

sb_remove_default_settings() is located in /startbox/includes/functions/admin_settings.php
