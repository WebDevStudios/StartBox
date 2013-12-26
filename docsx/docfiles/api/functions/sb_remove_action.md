# Functions: sb_remove_action

## Description

This function removes a function attached to a specified action hook. This method can be used to remove default functions attached to a specific action hook and possibly replace them with a substitute.

**Important:** To remove a hook, the $function_to_remove and $priority arguments must match when the hook was added. No warning will be given on removal failure.

## Usage

	<?php sb_remove_action( $tag, $metabox_name, $function_to_remove, $priority ); ?>

## Parameters

* **$tag**

	(string) (required) The name of the action you wish to hook onto. (See Plugin API/Action Reference for a list of action hooks)

	* Default: None

* **$metabox_name**

	(string) (required) The name of the class where the $function_to_add resides.

	* Default: None

* **$function_to_remove**

	(callback) (required) The name of the function you wish to be called. Note: any of the syntaxes explained in the PHP documentation for the 'callback' type are valid.

	* Default: None

* **$priority**

	(int) (optional) How important your function is. Alter this to make your function be called before or after other functions. The default is 10, so (for example) setting it to 5 would make it run earlier and setting it to 12 would make it run later.

	* Default: 10

## Return

(boolean) Whether the function is removed.
* True - The function was successfully removed.
* False - The function could not be removed.

## Examples

### Simple Hook

Reposition the Analytics code to hook into header:

	function child_reposition_analytics()  {
		sb_remove_action( 'wp_footer', 'sb_analytics_settings', 'output' );
		sb_add_action( 'wp_head', 'sb_analytics_settings', 'output' );
	}
	add_action('template_redirect', 'child_reposition_analytics');

## Notes

You only need to use sb_remove_action() in place of remove_action() IF you are interacting with a function hooked via theme options. To determine the name of an option's containing metabox, look in /startbox/includes/admin/ for the option panel containing the option you need to alter. The classname will be the metabox name (hint: this is typically sb_SOMETHING_settings, where SOMETHING may be header, footer, analytics, etc)

## Change Log

Since: 2.4.4

## Source File

sb_remove_action() is located in /startbox/includes/functions/custom.php

## Related
See also: [sb_add_action()](http://docs.wpstartbox.com/Custom_Functions:sb_add_action), [sb_remove_action()](http://docs.wpstartbox.com/Custom_Functions:sb_remove_action), [WordPress Plugin API](http://codex.wordpress.org/Plugin_API)