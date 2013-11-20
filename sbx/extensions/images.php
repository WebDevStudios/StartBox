<?php
/**
 * StartBox Image Functions
 *
 * @package StartBox
 * @subpackage Functions
 */

if ( ! function_exists( 'sb_get_image_id' ) ) :
/**
 * Pull an attachment ID from a post, if one exists.
 *
 * @since    3.0.0
 * @global   WP_Post   $post      Post object.
 * @param    integer   $index     Optional. Index of which image to return from a post. Default is 0.
 * @return   integer   boolean    Returns image ID, or false if image with given index does not exist.
 */
function sbx_get_image_id( $index = 0 ) {

	global $post;

	$ids = array_keys(
		get_children(
			array(
				'post_parent'    => $post->ID,
				'post_type'	     => 'attachment',
				'post_mime_type' => 'image',
				'orderby'        => 'menu_order',
				'order'	         => 'ASC',
			)
		)
	);

	if ( isset( $ids[$index] ) )
		return $ids[$index];

	return false;

}
endif;

if ( ! function_exists( 'sbx_get_image' ) ) :
/**
 * Return an image pulled from the media gallery.
 *
 * Supported $args keys are:
 *
 *  - format   - string, default is 'html'
 *  - size     - string, default is 'full'
 *  - num      - integer, default is 0
 *  - attr     - string, default is ''
 *  - fallback - mixed, default is 'first-attached'
 *
 * @since    3.0.0
 * @uses     sb_get_image_id()  Pull an attachment ID from a post, if one exists.
 * @global   WP_Post  $post     Post object.
 * @param    array    string    $args Optional. Image query arguments. Default is empty array.
 * @return   string   boolean   Return image element HTML, URL of image, or false.
 */
function sbx_get_image( $args = array() ) {

	global $post;

	$defaults = apply_filters( 'sbx_get_image_default_args', array(
		'format'   => 'html',
		'size'     => 'full',
		'num'      => 0,
		'attr'     => '',
		'fallback' => 'first-attached'
	) );

	$args = wp_parse_args( $args, $defaults );

	// Check for post image
	if ( has_post_thumbnail() && ( 0 === $args['num'] ) ) {
		$id = get_post_thumbnail_id();
		$html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
		list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
	}

	// Else if first-attached, pull the first image attachment
	elseif ( 'first-attached' === $args['fallback'] ) {
		$id = sb_get_image_id( $args['num'] );
		$html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
		list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
	}

	// Else if fallback array exists
	elseif ( is_array( $args['fallback'] ) ) {
		$id   = 0;
		$html = $args['fallback']['html'];
		$url  = $args['fallback']['url'];
	}

	// Else, return false
	else {
		return false;
	}

	// Source path, relative to the root
	$src = str_replace( home_url(), '', $url );

	// Determine output
	if ( 'html' === mb_strtolower( $args['format'] ) )
		$output = $html;
	elseif ( 'url' === mb_strtolower( $args['format'] ) )
		$output = $url;
	else
		$output = $src;

	// Return false if $url is blank
	if ( empty( $url ) ) $output = false;

	// Return data, filtered
	return apply_filters( 'sbx_get_image', $output, $args, $id, $html, $url, $src );

}
endif;

if ( ! function_exists( 'sbx_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function sbx_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'sbx_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment" itemprop="thumbnailUrl">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/**
 * Returns an image URI for any given post ID or attachment ID.
 *
 * This function will intelligently select a photo to use for the post based
 * on its featured image setting. If no featured image is set it will attempt
 * to use the latest attached image. If no images can be found it will default
 * to a custom "no preview available" image.
 *
 * @since 1.5.0
 * @param  array  $args  An array of possible args to use for finding our image
 * @return string        The URL of our desired image
 */
function sbx_get_post_image( $args = array() ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $id;

	// Setup our defaults
	$defaults = array(
		'post_id'         => $id,
		'image_id'        => null,
		'use_attachments' => false,
		'nophoto_url'     => apply_filters( 'sbx_post_image_none', SBX_IMAGES . '/nophoto.jpg' )
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

	// If we have no attachment image, fallback to our 'sbx_post_image_none' image
	$post_image_uri = isset( $attachment[0] ) ? $attachment[0] : $args['nophoto_url'];

	// Finally, return the URI for our image
	return $post_image_uri;
}

/**
 * Generates an <img> tag for use as post thumbnail.
 *
 * Uses sbx_get_post_image_url to resize and crop the image to any specified
 * dimension on-the-fly, using entirely naitive WP functionality.
 *
 * @since  1.5.0
 * @param  sarray  $args  Specify additional attributes for the image tag
 * @return string         An <img> tag containing image path and all parameters
 */
function sbx_post_image( $args = array() ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $post;

	// Setup our defaults and merge them with the passed arguments
	$defaults = array(
		'post_id'         => $post->ID,
		'image_id'        => null,
		'use_attachments' => false,
		'width'           => 200,
		'height'          => 200,
		'crop'            => 1,
		'align'           => 't',
		'class'           => 'post-image',
		'alt'             => get_the_title(),
		'title'           => get_the_title(),
		'nophoto_url'     => apply_filters( 'sbx_post_image_none', SBX_IMAGES . '/nophoto.jpg' ),
		'hide_nophoto'    => false,
		'enabled'         => true,
		'echo'            => true,
	);
	$args = wp_parse_args( $args, apply_filters( 'sbx_post_image_settings', $defaults ) );

	// If post thumbnails are disabled, or we're electing to hide our "no photo" fallback image, bail here.
	if ( false == $args['enabled'] || ( $args['hide_nophoto'] && sbx_get_post_image( $args ) === $args['nophoto_url'] ) )
		return false;

	// Available filters to both bypass sbx_get_post_image_url() and override final output
	if ( ! is_string( $image = apply_filters( 'sb_pre_post_image', null, $args ) ) )
		$image = apply_filters( 'sbx_post_image', '<img src="' . sbx_get_post_image_url( $args ) . '" width="' . $args['width'] . '" height="' . $args['height'] . '" class="' . $args['class'] . '" alt="' . $args['alt'] . '" title="' . $args['title'] . '" />', $args );

	// Echo our image if applicable
	if ( $args['echo'] )
		echo $image;

	return $image;
}


/**
 * Grab the URL for an image to use for a given post and dimensions
 *
 * Uses sbx_get_post_image to intelligently determine which image to show.
 * Uses wp_get_image_editor, introduced in WP3.5, to resize and save a
 * new cropped image on-the-fly.
 *
 * @since  2.6.0
 *
 * @param  array  $args An array of all our sizing arguments
 * @return string       URI of our final image
 */
function sbx_get_post_image_url( $args = null ) {

	// Grab our global $post object, incase we aren't given an explicit post ID
	global $post;

	// Setup our default args
	$defaults = array(
		'post_id'           => $post->ID,
		'image_id'          => null,
		'use_attachments'   => apply_filters( 'sbx_post_image_use_attachments', false ),
		'width'             => absint( apply_filters( 'sbx_post_image_width', 200 ) ),
		'height'            => absint( apply_filters( 'sbx_post_image_height', 200 ) ),
		'crop'              => apply_filters( 'sbx_post_image_crop', true ),
		'align'             => apply_filters( 'sbx_post_image_align', 't' ),
		'override_existing' => apply_filters( 'sbx_post_image_override_existing', false )
	);
	$args = wp_parse_args($args, apply_filters( 'sbx_post_image_settings', $defaults ) );

	// Setup our image details
	$current_image  = sbx_get_post_image( $args );
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
				$crop_dimensions = sbx_image_crop_dimensions( $current_width, $current_height, $args['width'], $args['height'], $args['align'] );
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
	return apply_filters( 'sbx_post_image_url', $image_url, $args );

}

/**
 * Determine the starting coordinates and maximum dimensions of an image
 * given its original dimensions and a desired final size and alignment.
 *
 * Credit for calculating our alignment and ratios goes to TimThumb (http://code.google.com/p/timthumb/).
 *
 * @since  2.7.0
 *
 * @param  integer $original_width  The original width of the given image
 * @param  integer $original_height The original height of the given image
 * @param  integer $new_width       The desired width of the final image
 * @param  integer $new_height      The desired height of the final image
 * @param  string  $alignment       The alignment position for our cropped image (accepts: t, b, l, r, c, or any combination thereof)
 * @return array                    An associative array of our relevant data (start_x, start_y, max_width, max_height)
 */
function sbx_image_crop_dimensions( $original_width = 0, $original_height = 0, $new_width = 0, $new_height = 0, $alignment = '' ) {

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
