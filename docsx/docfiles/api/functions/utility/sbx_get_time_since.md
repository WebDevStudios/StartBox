/*
Title: sbx_get_time_since
Description: Parameters and examples of the sbx_get_time_since function
Author: Michael Beckwith
Date: 01-02-2014
Last Edited: 01-02-2014
 */

# sbx_get_time_since

## Description

Returns a relative timestamp aka "X minutes ago"

## Usage

	<?php echo sbx_get_time_since(); ?>

## Parameters

* **older_date**

	(integer) (optional) Older timestamp

	* Default: 0

* **newer_date**

	(integer) (optional) more recent timestamp

	* Default: 0

## Examples

	//Grab the unix timestamp of our post date.
	echo sbx_get_time_since( get_the_time( 'U' ) )
