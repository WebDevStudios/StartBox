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
	global $id, $blog_id;

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
		'nophoto_url'     => apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' )
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
function sb_post_image( $args = array(), $depricated_height = null, $depricated_align = null, $depricated_zoom = null, $depricated_atts = array() ) {

	// Grab our global $post object
	global $post;

	// Back compat
	if ( !is_array($args) || $depricated_height || $depricated_align || $depricated_zoom || $depricated_atts ) {

		// Throw a warning for anyone using the old format
		_deprecated_argument( __FUNCTION__, '2.6', 'Please pass all parameters in a single array.' );

		// Create an array of attributes from the deprecated fields
		$args = array_merge( array(
			'width'  => absint( $args ),
			'height' => absint( $depricated_height ),
			'align'  => $depricated_align,
			'zoom'   => $depricated_zoom,
		), $depricated_atts );
	}

	// Setup our defaults and merge them with the passed arguments
	$defaults = array(
		'post_id'         => $post->ID,
		'image_id'        => null,
		'image_url'       => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'           => apply_filters( 'sb_post_image_width', 200 ),
		'height'          => apply_filters( 'sb_post_image_height', 200 ),
		'align'           => apply_filters( 'sb_post_image_align', 't' ),
		'zoom'            => apply_filters( 'sb_post_image_zoom', 1 ),
		'quality'         => apply_filters( 'sb_post_image_quality', 100 ),
		'class'           => apply_filters( 'sb_post_image_class', 'post-image' ),
		'alt'             => apply_filters( 'sb_post_image_alt', get_the_title() ),
		'nophoto_url'     => apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' ),
		'hide_nophoto'    => apply_filters( 'sb_post_image_hide_nophoto', false ),
		'enabled'         => apply_filters( 'sb_post_image_enabled', true ),
		'echo'            => apply_filters( 'sb_post_image_echo', true )
	);
	$args = wp_parse_args( $args, apply_filters( 'sb_post_image_settings', $defaults ) );

	// If post thumbnails are disabled, or we're electing to hide our "no photo" fallback image, bail here.
	if ( false == $args['enabled'] || ( $args['hide_nophoto'] && sb_get_post_image( $args ) === $args['nophoto_url'] ) )
		return false;

	// Otherwise, setup our image tag
	$image = '<img src="' . sb_get_post_image_url( $args ) . '" width="' . $args['width'] . '" height="' . $args['height'] . '" class="' . $args['class'] . '" alt="' . $args['alt'] . '" />';

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

	// Grab our global $post object
	global $post;

	// Setup our default args
	$defaults = array(
		'post_id'         => $post->ID,
		'image_id'        => null,
		'image_url'       => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'           => absint( apply_filters( 'sb_post_image_width', 200 ) ),
		'height'          => absint( apply_filters( 'sb_post_image_height', 200 ) ),
		'align'           => apply_filters( 'sb_post_image_align', 't' ),
		'zoom'            => apply_filters( 'sb_post_image_zoom', 1 )
	);
	$args = wp_parse_args($args, apply_filters( 'sb_post_image_settings', $defaults ) );

	// Fire up the WP Image editor to generate our correctly sized image
	$image = wp_get_image_editor( sb_get_post_image( $args ) );
	if ( ! is_wp_error( $image ) ) {

		// Resize our image based on our args
		// @TODO: setup handler for simple crop alignment
		$image->resize( $args['width'], $args['height'], $args['zoom'] );

		// Save the file to the uploads dir, but only if it doesn't already exist
		$uploads_dir = wp_upload_dir( get_the_time( 'Y/m', $args['post_id'] ) );
		$filename    = $image->generate_filename( null, $uploads_dir['path'] );
		if ( ! file_exists( $filename ) )
			$image->save( $filename );

		// Build our image URL from the uploads dir and our saved filename
		$image_url = trailingslashit( $uploads_dir['url'] ) . wp_basename( $filename );

		// Return our final image
		return $image_url;

	} else {

		// If we make it here, there was a problem with our image, so return the original file
		return sb_get_post_image( $args );
	}
}
