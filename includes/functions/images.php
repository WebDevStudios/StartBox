<?php
/**
 * StartBox Image Functions
 *
 * @package StartBox
 * @subpackage Functions
 */

/**
 * Return URL of an image based on Post ID
 *
 * First checks for featured image, then checks for any attached image, finally defaults to IMAGES_URL/nophoto.jpg
 *
 * @since 1.5
 * @uses has_post_thumbnail
 * @uses get_post_thumbnail_id
 * @uses wp_get_attachment_image_src
 *
 */
function sb_get_post_image( $args = array(), $deprecated_post_id = null, $deprecated_use_attachments = null, $deprecated_url = null ) {
	global $id, $blog_id;

	// If $args isn't an array, we're working with deprecated settings
	if ( !is_array( $args )  ) {
		// Throw a warning for anyone using the old format
		_deprecated_argument( __FUNCTION__, '2.6', 'Please pass all attributes as a single array instead.' );
		// Create an array of attributes from the deprecated fields
		$args = array(
			'image_id'			=> $args, // Note: this should be an integer,
			'post_id'			=> $deprecated_post_id,
			'use_attachments'	=> $deprecated_use_attachments,
			'url'				=> $deprecated_url
		);
	}

	$defaults = array(
		'image_id'			=> null,
		'post_id'			=> $id,
		'use_attachments'	=> false,
		'url'				=> null
	);
	extract( wp_parse_args( $args, $defaults ) );

	// if a URL is specified, return that
	if ($url)
		return $url;

	// if image_id is specified, use that
	elseif ( $image_id )
		$attachment = wp_get_attachment_image_src( $image_id, 'full' );

	// if not, let's use the post's featured image
	elseif ( has_post_thumbnail( $post_id) )
		$attachment = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

	// otherwise, and only if we want to, just use the last image attached to the post
	elseif ( true == $use_attachments ) {
		$images = get_children(array(
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'numberposts' => 1,
			'post_mime_type' => 'image'));
		foreach( $images as $image ) { $attachment = wp_get_attachment_image_src( $image->ID, 'full' ); }
	}

	// If there is no image, use the default image (available filter: sb_post_image_none)
	$post_image_uri = ( isset( $attachment[0] ) ) ? $attachment[0] : apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' );

	// If no image, return now
	if ( $post_image_uri == apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' ) )
		return $post_image_uri;

	// If MU/MS install, we need to dig a little deeper and link through the blogs.dir
	if ( 'IS_MU' ) {
		$imageParts = explode( '/files/', $post_image_uri );
		if ( isset( $imageParts[1] ) ) {
			$post_image_uri = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
		}
	}

	return $post_image_uri;
}

/**
 * Generates cropped thumbnail of any dimension placed in an <img> tag
 *
 * @since  1.5
 * @uses   sb_get_post_image_url
 * @param  string|array   $args         Specify additional attributes for the image tag
 * @return string                       An <img> tag containing image path and all parameters
 */
function sb_post_image( $args = array(), $depricated_height = null, $depricated_align = null, $depricated_zoom = null, $depricated_atts = array() ) {

	// Grab our global $post object
	global $post;

	// Check to see if $args value is an array or if the depricated args are used
	if ( !is_array($args) || !empty( $depricated_height ) || !empty( $depricated_align ) || !empty( $depricated_zoom ) || !empty( $depricated_atts ) ) {
		// Throw a warning for anyone using the old format
		_deprecated_argument( __FUNCTION__, '2.6', 'Please pass all attributes as a single array instead.' );
		// Create an array of attributes from the deprecated fields
		$args = array_merge( array(
			'width'		=> $args,
			'height'	=> $depricated_height,
			'align'		=> $depricated_align,
			'zoom'		=> $depricated_zoom,
		), $depricated_atts);
	}

	// Setup our defaults and merge them with the passed arguments
	$defaults = array(
		'post_id' 			=> $post->ID,
		'image_id'			=> null,
		'image_url'			=> null,
		'use_attachments'	=> apply_filters( 'sb_post_image_use_attachments', false ),
		'width'				=> apply_filters( 'sb_post_image_width', 200 ),
		'height'			=> apply_filters( 'sb_post_image_height', 200 ),
		'align'				=> apply_filters( 'sb_post_image_align', 't' ),
		'zoom'				=> apply_filters( 'sb_post_image_zoom', 1 ),
		'quality'			=> apply_filters( 'sb_post_image_quality', 100 ),
		'class'				=> apply_filters( 'sb_post_image_class', 'post-image' ),
		'alt'				=> apply_filters( 'sb_post_image_alt', get_the_title() ),
		'title' 			=> apply_filters( 'sb_post_image_title', get_the_title() ),
		'nophoto_url'		=> apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' ),
		'hide_nophoto'		=> apply_filters( 'sb_post_image_hide_nophoto', false ),
		'enabled'			=> apply_filters( 'sb_post_image_enabled', true ),
		'echo'				=> apply_filters( 'sb_post_image_echo', true )
	);
	extract( $args = wp_parse_args($args, apply_filters( 'sb_post_image_settings', $defaults ) ) );

	// If thumbnails are disabled, or we're hiding thumbnails when no preview is available, stop here.
	if ( false == $enabled || ( $hide_nophoto && sb_get_post_image( $args ) === $nophoto_url ) )
		return false;

	// String together the output
	$output = '<img src="' . sb_get_post_image_url( $args ) . '" ';
	foreach ( $args as $name => $value ) {
		if ( in_array( $name, array( 'post_id', 'image_id', 'image_url', 'use_attachments', 'align', 'zoom', 'quality', 'nophoto_url', 'hide_nophoto', 'echo', 'enabled' ) ) )
			continue;
		$output .= $name . '="' . esc_attr( $value ) . '" ';
	}
	$output .= '/>';

	// Echo output if applicable
	if ($echo)
		echo $output;

	return $output;
}

/**
 * Generates URL for image cropping
 *
 * @since  2.5
 * @uses   sb_get_post_image
 * @param  mixed   $args         Specify an array or pass the arguments straight through
 * @return string                URI containing image path and all parameters
 */
function sb_post_image_url( $args = array() ) {

	// This is a legacy function that didn't actually echo by default.
	// So, we need to check if our intent is to echo to maintain backwards compatibility.
	if ( $args['echo'] ) echo sb_get_post_image_url( $args );

	// Return our post image URL
	return sb_get_post_image_url( $args );
}

function sb_get_post_image_url( $args = null ) {

	// Grab our global $post object
	global $post;

	// Setup our default args
	$defaults = array(
		'post_id' 			=> $post->ID,
		'image_id'			=> null,
		'image_url'			=> null,
		'use_attachments'	=> apply_filters( 'sb_post_image_use_attachments', false ),
		'width'				=> apply_filters( 'sb_post_image_width', 200 ),
		'height'			=> apply_filters( 'sb_post_image_height', 200 ),
		'align'				=> apply_filters( 'sb_post_image_align', 't' ),
		'zoom'				=> apply_filters( 'sb_post_image_zoom', 1 ),
		'quality'			=> apply_filters( 'sb_post_image_quality', 100 ),
	);
	extract( $args = wp_parse_args($args, apply_filters( 'sb_post_image_settings', $defaults ) ) );

	// Combine all our options into the proper URI string
	$output = esc_url( SCRIPTS_URL . '/timthumb.php?src=' . sb_get_post_image( $args ) . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;a=' . $align . '&amp;zc=' . $zoom . '&amp;q=' . $quality );

	// Return the string
	return $output;
}