/*
Title: sbx_date_classes
Description: Parameters and examples of the sbx_date_classes function
Author: Michael Beckwith
Date: 01-02-14
Last Edited: 01-02-14
 */

# sbx_date_classes

## Description

Returns `gmdate()` formatted class attributes to be included in `body_class` and `post_class` filters. Adds classes for "Year", "Month", "Day", and "Hour"

## Usage

	<?php sbx_date_classes(); ?>

## Parameters

* **timestamp**

	(integer) (optional) Timestamp

	* Default: 0

* **classes**

	(array) (optional) Original classes array

	* Default: array()

* **prefix**

	(string) (optional) String to prefix classes with

	* Default: ''

## Examples

	function my_post_classes( $classes = array() ) {

		// Add classes for the month, day, and hour of publication
		$classes = sbx_date_classes( get_the_time( 'U' ), $classes, 's-' );

		return $classes;
	}
	add_filter( 'post_class', 'my_post_classes' );
