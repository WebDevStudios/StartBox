# Functions: sb_update_option

## Description

Update an existing option in the database.

## Usage

	<?php sb_update_option( $name, $value ); ?>

## Parameters

* **$name**

	(string) (required) The name of the option to update

	* Default: None

* **$value**

	(mixed) (required) The new value of the option being updated

	* Default: None

## Returns

(boolean) True on success, false if the option already exists.

## Examples

To update the option named "my-option" with a new value of "14":

	<?php sb_update_option( 'my-option', '14' ); ?>

## Notes

## Change Log

Since: 2.4.4

## Source File

sb_update_option() is located in /startbox/includes/functions/custom.php