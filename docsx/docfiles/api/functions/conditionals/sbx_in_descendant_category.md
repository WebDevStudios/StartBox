/*
Title: sb_in_descendant_category
Description: Parameters and examples of the sb_in_descendant_category function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 12-31-13
 */

# Functions: sb_in_descendant_category

## Description

Tests if the current post (or any specified post) is assigned to any descendants of the specified categories. Works similarly to WP's in_category() conditional.

## Usage

	<?php sb_in_descendant_category($cats, $_post) ?>

## Parameters

* **$category**

	(mixed) (required) One or more categories specified by ID (integer), name or slug (string), or an array of these

	* Default: None

* **$_post**

	(mixed) (optional) The post (integer ID or object). Defaults to the current post in the Loop or the post in the main query.

	* Default: None

## Return

(boolean) Whether the post is assigned to any of the specified categories.

## Examples

### Testing the current post within the Loop

sb_in_descendant_category() is often used to take different actions within the Loop depending on the current post's category, e.g.

	<?php
	if ( sb_in_descendent_category( 'blowfish' )) {
		// They have spinities...
	} elseif ( sb_in_descendent_category( array( 'Tropical Birds', 'small-mammals', 12 ) )) {
		// They are warm-blooded...
	} else {
		// & c.
	}
	?>

## Notes

## Change Log

Since: Unknown

##Source File

sb_in_descendant_category() is located in /startbox/includes/functions/custom.php
