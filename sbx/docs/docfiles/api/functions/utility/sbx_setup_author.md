/*
Title: sbx_setup_author
Description: Parameters and examples of the sbx_setup_author function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 01-02-2014
 */

# sbx_setup_author

## Description

Sets up the authordata global when viewing an author archive

## Usage

	<?php sbx_author_box(); ?>

## Parameters

none

## Examples

Changes the gravatar size and title text

	function sbx_setup_author() {
		global $wp_query;

		if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
			$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
		}
	}
	add_action( 'wp', 'sbx_setup_author' );

## Note

You likely will never need to call this function directly, as it already gets hooked onto the wp action.
