# Functions: sb_get_post_image

## Description

Returns URI of featured image for given post or attachment id. Can optionally fallback to any image attachment if no featured image is specified. Defaults to /startbox/images/nophoto.png if neither are found.

## Usage

	<?php sb_get_post_image($image_id, $post_id, $use_attachments, $url); ?>

## Parameters

* **$image_id**

	(integer) (optional) Return a specific image URI based on its attachment ID

	* Default: null

* **$post_id**

	(integer) (optional) Return a post's featured image based on the post ID. Will default to current post's ID if left null

	* Default: null

* **$use_attachments**

	(boolean) (optional) Fallback to any attached image if no featured or specified image is found

	* Default: false

* **$url**

	(string) (optional) Returns exactly whatever string is entered.

	* Default: null

## Examples

In it's simplest form, this function can be used to output the URL of a post's featured image.

	<?php echo sb_get_post_image(); ?>

Because we are passing no parameters, the function will use the current post's ID and return the URI of the featured image. If no featured image is found it will return the URI for the "No Preview Available" image.

## Notes

Available filter: sb_post_image_none to replace the default "No Preview Available" thumbnail.

## Change Log

Since: 1.5

## Source File

sb_get_post_image() is located in /startbox/includes/functions/custom.php