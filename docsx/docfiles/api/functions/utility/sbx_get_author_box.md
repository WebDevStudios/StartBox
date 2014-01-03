/*
Title: sbx_get_author_box
Description: Parameters and examples of the sbx_get_author_box function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# sbx_get_author_box

## Description

Returns html markup for a semantic data rich author box sbx_get_author_box() function.

## Usage

	<?php $authorbox = sbx_get_author_box( $args ); ?>

or

	<?php echo sbx_get_author_box( $args ); ?>

## Parameters

* **gravatar_size**

	(integer) (optional) Pixel size for the gravatar dimensions

	* Default: 96

* **title**

	(string) (optional) Title to use with the author box

	* Default: __( 'About', 'startbox' )

* **name**

	(string) (optional) Name to use with the author

	* Default: get_the_author_meta( 'display_name' )

* **email**

	(string) (optional) Email to use with the author

	* Default: get_the_author_meta( 'email' )

* **description**

	(string) (optional) Description to use with the author

	* Default: get_the_author_meta( 'description' )

* **user_id**

	(integer) (optional) User ID to display

	* Default: get_the_author_meta( 'ID' )

## Examples

Changes the gravatar size and title text

	$args = array(
		'gravatar_size' => 192,
		'title' => 'About Me',
	);
	echo sbx_get_author_box( $args );
