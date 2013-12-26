# Functions: sb_get_option

## Description

Retrieve an option's value from the database.

## Usage

	<?php sb_get_option( $name ); ?>

## Parameters

* **$name**

    (string) (required) The name of the option to retrieve

	* Default: None 

## Returns

* **mixed**

	* The value of the option

## Examples

To retrieve the value of the "my-option" option:

	<?php sb_get_option( 'my-option' ); ?>

## Notes

## Change Log

Since: 2.4.4

## Source File

sb_get_option() is located in /startbox/includes/functions/custom.php

## Related

**[Options API](http://docs.wpstartbox.com/Options_API)**: [Adding Option Panels](http://docs.wpstartbox.com/Functions:sb_register_settings), [Removing Option Panels](http://docs.wpstartbox.com/Functions:sb_unregister_settings), [Creating Options](http://docs.wpstartbox.com/Options_API:Creating_Options), [Interacting With Your Options](http://docs.wpstartbox.com/Options_API#Interacting_With_Your_Options)