<?php
/**
 * SBX Image Functions
 *
 * @package SBX
 * @subpackage Extensions
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! function_exists( 'sbx_get_attached_image_id' ) ) :
/**
 * Get image ID for a given post attached at a given index.
 *
 * @since  1.0.0
 *
 * @param  integer  $post_id Post ID.
 * @param  integer  $index   Specific attached image to return (default: 0, first image).
 * @return int|bool          Attachment ID if found, otherwise false.
 */
function sbx_get_attached_image_id( $post_id = 0, $index = 0 ) {
	global $post;

	// If no $post_id set, use current post
	if ( empty( $post_id ) )
		$post_id = $post->ID;

	$ids = array_keys(
		get_children(
			array(
				'post_parent'    => $post_id,
				'post_type'	     => 'attachment',
				'post_mime_type' => 'image',
				'orderby'        => 'menu_order',
				'order'	         => 'ASC',
			)
		)
	);

	if ( isset( $ids[ $index ] ) )
		return $ids[ $index ];

	return false;

}
endif;

if ( ! function_exists( 'sbx_get_image' ) ) :
/**
 * Return the desired output for any image attachment.
 *
 * This function intelligently looks for the current post (if none specified)
 * and looks first for a featured image, then any attached image, then a custom
 * fallback. Alternatively, a specific image ID can be set to bypass the above.
 *
 * This function can return either the relative source to an image, its full
 * URL, or a complete <img> tag, based on 'output' parameter.
 *
 * @since  1.0.0
 *
 * @param  array|string  $args {
 *     Image output args.
 *
 *     @type integer 'post_id'          Specific post ID to fetch. Default: current post ID.
 *     @type integer 'image_id'         Specific image ID to fetch. Default: 0.
 *     @type string  'output'           Output format. Accepts: 'html', 'url', 'path'. Default: 'html'.
 *     @type mixed   'size'             Attachment size. Accepts size name (e.g. 'thumbnail') or dimension array. Default: 'full'.
 *     @type mixed   'attr'             Query string or array of additional <img> tag attributes.
 *     @type mixed   'fallback'         Custom fallback parameters. Accepts false, 'use_attachments', or array( 'html' => '', 'url' => '' ). Default: 'use_attachments'.
 *     @type integer 'attachment_index' Sepcific attached image index to retrieve. Default: 0.
 * }
 * @return string boolean Return image element HTML, URL of image, or false.
 */
function sbx_get_image( $args = array() ) {
	global $post;

	// Parse filterable defaults
	$defaults = apply_filters( 'sbx_get_image_defaults', array(
		'post_id'          => $post->ID,
		'image_id'         => 0,
		'output'           => 'html',
		'size'             => 'full',
		'attr'             => '',
		'fallback'         => 'use_attachments',
		'attachment_index' => 0,
	) );
	$args = wp_parse_args( $args, $defaults );

	// If given an explicit image ID, use that
	// Otherwise, try to pull back the featured image
	// Finally, attempt to pull back any attached image
	if ( ! empty( $args['image_id'] ) ) {
		$id = absint( $args['image_id'] );
	} elseif ( has_post_thumbnail( $args['post_id'] ) && ( 0 === $args['attachment_index'] ) ) {
		$id = get_post_thumbnail_id( $args['post_id'] );
	} elseif ( 'use_attachments' === $args['fallback'] ) {
		$id = sbx_get_attached_image_id( $args['post_id'], $args['attachment_index'] );
	}

	// If an image ID was found, retrieve image details
	if ( ! empty( $id ) ) {
		$html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
		list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );

	// Otherwise, attempt to use fallback array
	} elseif ( is_array( $args['fallback'] ) ) {
		$id   = 0;
		$html = $args['fallback']['html'];
		$url  = $args['fallback']['url'];
	}

	// If no image was found, set output to false
	if ( empty( $url ) ) {
		$output = false;

	// Otherwise, setup the correct output
	} else {

		// Source path, relative to the root
		$src = str_replace( home_url(), '', $url );

		// Determine output
		if ( 'html' === mb_strtolower( $args['output'] ) ) {
			$output = $html;
		} elseif ( 'url' === mb_strtolower( $args['output'] ) ) {
			$output = $url;
		} else {
			$output = $src;
		}

	}

	// Return data, filtered
	return apply_filters( 'sbx_get_image', $output, $args, $id, $html, $url, $src );

}
endif;

if ( ! function_exists( 'sbx_image' ) ) :
/**
 * Output contents of sbx_get_image().
 *
 * @since 1.0.0
 *
 * @param array  $args Image output args.
 */
function sbx_image( $args = array() ) {
	echo sbx_get_image( $args );
}
endif;

if ( ! function_exists( 'sbx_the_attached_image' ) ) :
/**
 * Output attached image and link to the next attached image.
 *
 * Grabs all images attached to the same parent to find the
 * next adjacent image (or first image, if viewing the very
 * last image). If image is alone, link points to itself.
 *
 * @since 1.0.0
 */
function sbx_the_attached_image() {

	// Setup base variables
	$post                = get_post();
	$attachment_size     = apply_filters( 'sbx_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	// Get all images attached to the same parent post
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

	// If more than one image was found
	if ( count( $attachment_ids ) > 1 ) {

		// Get the next image ID
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// If found, get the URL for the next image
		if ( ! empty( $next_id ) ) {
			$next_attachment_url = get_attachment_link( $next_id );

		// Otherwise, the the URL for the very first image
		} else {
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
		}
	}

	// Generate output
	$output = sprintf(
		'<a href="%1$s" title="%2$s" rel="attachment" itemprop="thumbnailUrl">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);

	echo apply_filters( 'sbx_the_attached_image', $output, wp_get_attachment_image( $post->ID, $attachment_size ), esc_url( $next_attachment_url ), $post );
}
endif;
