# Functions: sb_add_option

## Description

Add an option to the database

## Usage

	<?php sb_add_option( $name, $value ); ?>

## Parameters

* **$name**

	(string) (required) The name of the option to add

	* Default: None 

* **$value**

	(mixed) (required) The initial value of the option being added

	* Default: None 

## Returns

(boolean) True on success, false if the option already exists.

## Examples

To add a new option named "my-option" with a value of "12":

	<?php sb_add_option( 'my-option', '12' ); ?>

## Notes

## Change Log

Since: 2.4.4

## Source File

sb_add_option() is located in /startbox/includes/functions/custom.php

## Related

**[Options API](http://docs.wpstartbox.com/Options_API)**: [Adding Option Panels](http://docs.wpstartbox.com/Functions:sb_register_settings), [Removing Option Panels](http://docs.wpstartbox.com/Functions:sb_unregister_settings), [Creating Options](http://docs.wpstartbox.com/Options_API:Creating_Options), [Interacting With Your Options](http://docs.wpstartbox.com/Options_API#Interacting_With_Your_Options)