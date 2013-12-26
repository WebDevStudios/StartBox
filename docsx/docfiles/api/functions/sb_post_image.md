# Functions:sb_post_image

## Description

Generates cropped thumbnail of any dimension placed in a properly formatted <img> tag.

## Usage

	<?php sb_post_image( $w, $h, $a, $zc, $atts); ?>

## Default Usage

	<?php $atts = array(
		'post_id' => null,
		'image_id' => null,
		'image_url' => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'	=> apply_filters( 'sb_post_image_width', 200 ),
		'height'=> apply_filters( 'sb_post_image_height', 200 ),
		'align'	=> apply_filters( 'sb_post_image_align', 't' ),
		'zoom'	=> apply_filters( 'sb_post_image_zoom', 1 ),
		'class'	=> apply_filters( 'sb_post_image_class', 'post-image' . $nophoto ),
		'alt'	=> apply_filters( 'sb_post_image_alt', get_the_title() ),
		'echo'	=> apply_filters( 'sb_post_image_echo', true )
	); ?>

By default, this usage creates an <img> tag with the following:

* A thumbnail pulled from the post's featured image
* Width of 200px
* Height of 200px
* Aligned to top-center
* Zooming and cropping (upscaling) any small images
* Class of "post-image" (and "nophoto" if no featured image is available)
* Alt text containing the post title 

## Parameters

These paramaters are passed directly through the function:

* **$w**

    (integer) (optional) The desired image width.

	* Default: 200 

* **$h**

    (integer) (optional) The desired image height.

	* Default: 200 

* **$a**

    (string) (optional) Align (Crop Location) - where the crop should center. Accepts: c, t, tr, tl, b, br, bl, l, r.

	* Default: topcenter 

* **$z**

    (boolean) (1) optional

	* Default: None 

* **$atts**

    (array) (optional) An array of additional attributes for the <img> tag.

	* Default: None 

## Arguments

These arguments are passed through the $atts parameter as an array:

* **post_id**

    (integer) (optional) Get featured image from a specific post ID.
	
	* Default: null 

* **image_id**

    (integer) (optional) Get a specific image based on it's attachment ID.

	* Default: null 

* **image_url**

    (string) (optional) Get a specific image from a specified URL.

	* Default: null 

* **use_attachments**

    (boolean) (optional) Fallback to any attached image if no featured image specified.

	* Default: false 

* **width**

    (integer) (optional) Fallback for the $w attribute above.

	* Default: 200 

* **height**

    (integer) (optional) Fallback for the $h attribute above.

	* Default: 200 

* **align**

    (string) (optional) Fallback for the $a attribute above.

	* Default: t 

* **zoom**

    (boolean) (optional) Fallback for the $z attribute above.

	* Default: 1 

* **class**

    (string) (optional) The CSS class(es) of the image tag.

	* Default: post-image 

* **alt**

    (string) (optional) The Alt text of the image tag.

	* Default: the_title() 

* **echo**

    (boolean) (optional) Echo the resulting <img> string (true) or return it (false)

	* Default: true 

In addition to the above, you can also pass any other attributes through the $atts array and they will be added to the <img> tag like any other traditional HTML attribute.

## Examples

###Create a thumbnail that is 640x200

The following example will produce a thumbnail that is 640px wide by 200px tall, cropped to the upper-left corner. It will also upscale and crop any photos that are smaller to fit this dimension.

	<?php sb_post_image( 640, 200, 'tl', 1 ); ?>

### Set a New Default Width & Height

First, allow me to point out that there are two options specifically for this purpose in the Content Settings metabox on the Theme Options page.

If you would still like to do it programmatically, however, here's how:

	// Globally override the default width & height for sb_post_image()
	function my_image_settings($defaults) {
	        $defaults[width] = 250 // New width
	        $defaults[height] = 100 // New height
	        return $defaults;
	}
	add_filter( 'sb_post_image_settings', 'my_image_settings' );

### Changing the alignment (crop location)

You can either change this by passing a value through the $c parameter, or globally through a filter. The parameter accepts the following 9 arguments:

* c : position in the center (this is the default)
* t : align top
* tr : align top right
* tl : align top left
* b : align bottom
* br : align bottom right
* bl : align bottom left
* l : align left
* r : align right

&nbsp;

	// Globally override the default crop location for sb_post_image()
	function my_image_settings($defaults) {
	        $defaults[align] = tl; // top left
	        return $defaults;
	}
	add_filter( 'sb_post_image_settings', 'my_image_settings' );

## Notes

All of the defaults in this function can be globally overridden using the filters you see being passed in the usage defaults above. Additionally, you can filter ALL of the defaults in a single function by using the sb_post_image_settings filter (see above example).

## Change Log

Since: 1.5

## Source File

sb_post_image() is located in /startbox/includes/functions/custom.php 