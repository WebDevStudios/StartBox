<?php
/**
 * StartBox Image Functions
 *
 * @package StartBox
 * @subpackage Functions
 */

/**
 * Returns an image URI for any given post ID or attachment ID.
 *
 * This function will intelligently select a photo to use for the post based
 * on its featured image setting. If no featured image is set it will attempt
 * to use the latest attached image. If no images can be found it will default
 * to a custom "no preview available" image.
 *
 * @since 1.5
 * @param  array  $args  An array of possible args to use for finding our image
 * @return string        The URL of our desired image
 */
function sb_get_post_image( $args = array(), $deprecated_post_id = null, $deprecated_use_attachments = null ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $id;

	// Back compat
	if ( !is_array( $args ) || $deprecated_post_id || $deprecated_use_attachments ) {

		// Throw a warning for anyone using the old format
		_deprecated_argument( __FUNCTION__, '2.6', 'Please pass all parameters in a single array.' );

		// Create an array of attributes from the deprecated fields
		$args = array(
			'image_id'        => absint( $args ),
			'post_id'         => absint( $deprecated_post_id ),
			'use_attachments' => $deprecated_use_attachments
		);
	}

	// Setup our defaults
	$defaults = array(
		'post_id'         => $id,
		'image_id'        => null,
		'use_attachments' => false,
		'nophoto_url'     => apply_filters( 'sb_post_image_none', SB_IMAGES . '/nophoto.jpg' )
	);
	$args = wp_parse_args( $args, $defaults );

	// If we have an explicit image ID, let's use it
	if ( $args['image_id'] ) {
		$attachment = wp_get_attachment_image_src( $args['image_id'], 'full' );

	// Otherwise, let's use our post's featured image
	} elseif ( has_post_thumbnail( $args['post_id'] ) ) {
		$attachment = wp_get_attachment_image_src( get_post_thumbnail_id( $args['post_id'] ), 'full' );

	// If we have no featured image, and we've elected to use ANY attached image, grab the newest image
	} elseif ( true == $args['use_attachments'] ) {
		$images = get_children( array(
			'post_parent'    => $args['post_id'],
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => 1
		) );
		foreach ( $images as $image ) { $attachment = wp_get_attachment_image_src( $image->ID, 'full' ); }
	}

	// If we have no attachment image, fallback to our 'sb_post_image_none' image
	$post_image_uri = isset( $attachment[0] ) ? $attachment[0] : $args['nophoto_url'];

	// Finally, return the URI for our image
	return $post_image_uri;
}

/**
 * Generates an <img> tag for use as post thumbnail.
 *
 * Uses sb_get_post_image_url to resize and crop the image to any specified
 * dimension on-the-fly, using entirely naitive WP functionality.
 *
 * @since  1.5
 * @param  sarray  $args  Specify additional attributes for the image tag
 * @return string         An <img> tag containing image path and all parameters
 */
function sb_post_image( $args = array(), $depricated_height = null, $depricated_align = null, $depricated_crop = null, $depricated_atts = array() ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $post;

	// Back compat
	if ( !is_array($args) || $depricated_height || $depricated_align || $depricated_crop || $depricated_atts ) {

		// Throw a warning for anyone using the old format
		_deprecated_argument( __FUNCTION__, '2.6', 'Please pass all parameters in a single array.' );

		// Create an array of attributes from the deprecated fields
		$args = array_merge( array(
			'width'  => absint( $args ),
			'height' => absint( $depricated_height ),
			'align'  => $depricated_align,
			'crop'   => $depricated_crop,
		), $depricated_atts );
	}

	// Setup our defaults and merge them with the passed arguments
	$defaults = array(
		'post_id'         => $post->ID,
		'image_id'        => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'           => apply_filters( 'sb_post_image_width', 200 ),
		'height'          => apply_filters( 'sb_post_image_height', 200 ),
		'crop'            => apply_filters( 'sb_post_image_crop', 1 ),
		'align'           => apply_filters( 'sb_post_image_align', 't' ),
		'class'           => apply_filters( 'sb_post_image_class', 'post-image' ),
		'alt'             => apply_filters( 'sb_post_image_alt', get_the_title() ),
		'title'           => apply_filters( 'sb_post_image_alt', get_the_title() ),
		'nophoto_url'     => apply_filters( 'sb_post_image_none', SB_IMAGES . '/nophoto.jpg' ),
		'hide_nophoto'    => apply_filters( 'sb_post_image_hide_nophoto', false ),
		'enabled'         => apply_filters( 'sb_post_image_enabled', true ),
		'echo'            => apply_filters( 'sb_post_image_echo', true )
	);
	$args = wp_parse_args( $args, apply_filters( 'sb_post_image_settings', $defaults ) );

	// If post thumbnails are disabled, or we're electing to hide our "no photo" fallback image, bail here.
	if ( false == $args['enabled'] || ( $args['hide_nophoto'] && sb_get_post_image( $args ) === $args['nophoto_url'] ) )
		return false;

	// Available filters to both bypass sb_get_post_image_url() and override final output
	if ( ! is_string( $image = apply_filters( 'sb_pre_post_image', null, $args ) ) )
		$image = apply_filters( 'sb_post_image', '<img src="' . sb_get_post_image_url( $args ) . '" width="' . $args['width'] . '" height="' . $args['height'] . '" class="' . $args['class'] . '" alt="' . $args['alt'] . '" title="' . $args['title'] . '" />', $args );

	// Echo our image if applicable
	if ( $args['echo'] )
		echo $image;

	return $image;
}


/**
 * Grab the URL for an image to use for a given post and dimensions
 *
 * Uses sb_get_post_image to intelligently determine which image to show.
 * Uses wp_get_image_editor, introduced in WP3.5, to resize and save a
 * new cropped image on-the-fly.
 *
 * @since  2.6
 * @param  array  $args  An array of all our sizing arguments
 * @return string        URI of our final image
 */
function sb_get_post_image_url( $args = null ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $post;

	// Setup our default args
	$defaults = array(
		'post_id'           => $post->ID,
		'image_id'          => null,
		'use_attachments'   => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'             => absint( apply_filters( 'sb_post_image_width', 200 ) ),
		'height'            => absint( apply_filters( 'sb_post_image_height', 200 ) ),
		'crop'              => apply_filters( 'sb_post_image_crop', true ),
		'align'             => apply_filters( 'sb_post_image_align', 't' ),
		'override_existing' => apply_filters( 'sb_post_image_override_existing', false )
	);
	$args = wp_parse_args($args, apply_filters( 'sb_post_image_settings', $defaults ) );

	// Setup our image details
	$current_image  = sb_get_post_image( $args );
	$current_size   = getimagesize( $current_image );
	$current_width  = $current_size[0];
	$current_height = $current_size[1];

	// Setup our location and filename details
	$uploads_dir    = wp_upload_dir( get_the_time( 'Y/m', $args['post_id'] ) );
	$image_info     = pathinfo( $current_image );
	$filename       = $image_info['filename'];
	$ext            = $image_info['extension'];
	$suffix         = $args['width'] . 'x' . $args['height'] . ( $args['crop'] ? '-' . $args['align'] : '' );
	$file           = trailingslashit( $uploads_dir['path'] ) . "{$filename}-{$suffix}.{$ext}";
	$image_url      = trailingslashit( $uploads_dir['url'] ) . "{$filename}-{$suffix}.{$ext}";

	// If we don't already have a file for our desired dimensions and alignment...
	if ( ! file_exists( $file ) || true == $args['override_existing'] ) {

		// Fire up the WP Image editor to generate our correctly sized image
		$image = wp_get_image_editor( $current_image );
		if ( ! is_wp_error( $image ) ) {

			// If crop is true, then we want to fill our new width and height
			if ( $args['crop'] ) {
				$crop_dimensions = sb_image_crop_dimensions( $current_width, $current_height, $args['width'], $args['height'], $args['align'] );
				$image->crop( $crop_dimensions['start_x'], $crop_dimensions['start_y'], $crop_dimensions['max_width'], $crop_dimensions['max_height'], $args['width'], $args['height'] );

			// Otherwise, just resize to a maximum height or width
			} else {
				$image->resize( $args['width'], $args['height'], false );
			}

			// Save our newly resized image
			$image->save( $file );

		}

	}

	// Finally, return the image URL for our correctly sized image
	return apply_filters( 'sb_post_image_url', $image_url, $args );

}

/**
 * Determine the starting coordinates and maximum dimensions of an image
 * given its original dimensions and a desired final size and alignment.
 *
 * Credit for calculating our alignment and ratios goes to TimThumb (http://code.google.com/p/timthumb/).
 *
 * @since  2.7
 * @param  integer $original_width  The original width of the given image
 * @param  integer $original_height The original height of the given image
 * @param  integer $new_width       The desired width of the final image
 * @param  integer $new_height      The desired height of the final image
 * @param  string  $alignment       The alignment position for our cropped image (accepts: t, b, l, r, c, or any combination thereof)
 * @return array                    An associative array of our relevant data (start_x, start_y, max_width, max_height)
 */
function sb_image_crop_dimensions( $original_width = 0, $original_height = 0, $new_width = 0, $new_height = 0, $alignment = '' ) {

	// Setup our Image Dimesnions
	$start_x      = $start_y = 0;
	$max_width    = absint( $original_width );
	$max_height   = absint( $original_height );
	$width_ratio  = absint( $original_width ) / absint( $new_width );
	$height_ratio = absint( $original_height ) / absint( $new_height );

	// calculate x or y coordinate and width or height of source
	if ($width_ratio > $height_ratio) {
		$max_width = round ($original_width / $width_ratio * $height_ratio);
		$start_x = round (($original_width - ($original_width / $width_ratio * $height_ratio)) / 2);
	} else if ($height_ratio > $width_ratio) {
		$max_height = round ($original_height / $height_ratio * $width_ratio);
		$start_y = round (($original_height - ($original_height / $height_ratio * $width_ratio)) / 2);
	}

	// Setup our starting coordinates based on our alignment
	if ( !empty( $alignment ) ) {
		if ( strstr($alignment, 't') )
			$start_y = 0;
		if ( strstr($alignment, 'b') )
			$start_y = $original_height - $max_height;
		if ( strstr($alignment, 'l') )
			$start_x = 0;
		if ( strstr($alignment, 'r') )
			$start_x = $original_width - $max_width;
	}

	// Return all our relevant data
	return array( 'start_x' => absint( $start_x ), 'start_y' => absint( $start_y ), 'max_width' => absint( $max_width ), 'max_height' => absint( $max_height ) );
}
