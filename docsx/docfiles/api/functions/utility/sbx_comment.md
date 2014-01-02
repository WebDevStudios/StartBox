/*
Title: sbx_comment
Description: Parameters and examples of the sbx_comment function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 01-02-14
 */

# sbx_comment

> Editor's note: Pending possible changes with arguments options and overriding.

## Description

Display template for comments and pingbacks.

This function is used as a callback for wp_list_comments() in the comments.php template file.

To override and modify, you'll want to consider defining your own sbx_comment function.

## Usage

	<?php sbx_comment(); ?>

## Parameters

* **comment**

	(object) (optional) Comment object

	* Default: null

* **args**

	(array) (optional) Array of arguments to use with formatting output

	* Default: array()

		Available parameters for $args array

		* add_below
			* Used for the JavaScript addComment.moveForm() method parameters.
		* depth
			* Threaded comment depth. Provided by the primary parameter in the function.
		* before
			* the html or text to add before the reply link
		* after
			* the html or text to add after the reply link
		* has_children

		* avatar_size

* **depth**

	(integer) (optional) Threaded comment depth

	* Default: array()

## Examples
