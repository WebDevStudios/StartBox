# Functions:sb_delete_option

## Description

Delete an option from the database.

## Usage

	<?php sb_add_option( $name ); ?>

## Parameters

* **$name**

    (string) (required) The name of the option to remove

	* Default: None 

## Returns

**boolean**

True on success, false if the option already exists.

## Examples

To remove the "my-option" option:

	<?php sb_delete_option( 'my-option' ); ?>

## Notes

## Change Log

Since: 2.4.4

## Source File

sb_delete_option() is located in /startbox/includes/functions/custom.php

## Related

**[Options API](http://docs.wpstartbox.com/Options_API)**: [Adding Option Panels](http://docs.wpstartbox.com/Functions:sb_register_settings), [Removing Option Panels](http://docs.wpstartbox.com/Functions:sb_unregister_settings), [Creating Options](http://docs.wpstartbox.com/Options_API:Creating_Options), [Interacting With Your Options](http://docs.wpstartbox.com/Options_API#Interacting_With_Your_Options)