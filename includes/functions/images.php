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
 * @since StartBox 1.5
 *
 * @uses has_post_thumbnail
 * @uses get_post_thumbnail_id
 * @uses wp_get_attachment_image_src
 *
 */
function sb_get_post_image($image_id = null, $post_id = null, $use_attachments = false, $url = null) {
	global $id,$blog_id;
	$post_id = ( $post_id == null ) ? $id : $post_id;
	$attachment = array();
	
	// if a URL is specified, use that
	if ($url)
		return $url;

	// if image_id is specified, use that
	elseif ($image_id)
		$attachment = wp_get_attachment_image_src( $image_id, 'full' );
		
	// if not, let's use the post's featured image
	elseif ( has_post_thumbnail( $post_id) )
		$attachment = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
		
	// otherwise, and only if we want to, just use the last image attached to the post
	elseif ( $use_attachments == true ) {
		$images = get_children(array(
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'numberposts' => 1,
			'post_mime_type' => 'image'));
		foreach($images as $image) { $attachment = wp_get_attachment_image_src( $image->ID, 'full' ); } 
	}
	
	// If there is no image, use the default image (available filter: sb_post_image_none)
	$post_image_uri = (isset($attachment[0])) ? $attachment[0] : apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' );
	
	// If no image, return now
	if ( $post_image_uri == apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' ) )
		return $post_image_uri;
	
	// If MU/MS install, we need to dig a little deeper and link through the blogs.dir
	if ('IS_MU') {
		$imageParts = explode('/files/', $post_image_uri);
		if (isset($imageParts[1])) {
			$post_image_uri = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
		}
	}
	
	return $post_image_uri;
}

/**
 * Generates cropped thumbnail of any dimension placed in an <img> tag
 * 
 * @since StartBox 1.5
 *
 * @uses sb_get_post_image
 *
 * @param integer $w specify image width in pixels. Default: 200
 * @param integer $h specify image height in pixels. Default: 200
 * @param string $a crop alignment, values: tl, t, tr, l, c, r, bl, b, br. Default: t
 * @param boolean $z zoom and cropped to fill (true) or stretched to fit (false). Default: true
 * @param string|array $atts specify additional attributes for the image tag
 *
 * @return string <img> tag containing image path and all parameters
 */
function sb_post_image($w = null, $h = null, $a = null, $zc = 1, $attr = null) {
	
	$defaults = array(
		'post_id' => null,
		'image_id' => null,
		'image_url' => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'	=> apply_filters( 'sb_post_image_width', 200 ),
		'height'=> apply_filters( 'sb_post_image_height', 200 ),
		'align'	=> apply_filters( 'sb_post_image_align', 't' ),
		'zoom'	=> apply_filters( 'sb_post_image_zoom', 1 ),
		'class'	=> apply_filters( 'sb_post_image_class', 'post-image' ),
		'alt'	=> apply_filters( 'sb_post_image_alt', get_the_title() ),
		'title' => apply_filters( 'sb_post_image_title', get_the_title() ),
		'hide_nophoto' => apply_filters( 'sb_post_image_hide_nophoto', false ),
		'enabled' => apply_filters( 'sb_post_image_enabled', true ),
		'echo'	=> apply_filters( 'sb_post_image_echo', true )
	);
	extract( $attr = wp_parse_args($attr, apply_filters( 'sb_post_image_settings', $defaults ) ) );
	
	$nophoto = ( sb_get_post_image($image_id, $post_id, $use_attachments, $image_url) === apply_filters( 'sb_post_image_none', IMAGES_URL . '/nophoto.jpg' ) ) ? ' nophoto' : '';
	
	// If we're hiding thumbnails when no preview is available, or thumbnails are disabled, stop here.
	if ( ( $hide_nophoto && $nophoto != '' ) || $enabled == false )
		return false;
	
	$attr['width'] = $width = ($w != null) ? $w : $width;
	$attr['height'] = $height = ($h != null) ? $h : $height;
	$attr['align'] = $align = ($a != null) ? $a : $align;
	$attr['zoom'] = $zoom = ($zc != null) ? $zc : $zoom;
	
	$output = '<img class="' . $class . $nophoto . '" src="' . esc_url( SCRIPTS_URL . '/timthumb.php?src=' . sb_get_post_image( $image_id, $post_id, $use_attachments, $image_url ) . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;a=' . $align . '&amp;zc=' . $zoom . '&amp;q=100' ) . '" ';
	foreach ( $attr as $name => $value ) {
		$exlcude = null;
		if ( in_array( $name, array( 'post_id', 'image_id', 'align', 'class', 'crop', 'zoom', 'echo', 'image_url', 'use_attachments', 'hide_nophoto', 'enabled' ) ) ) continue;
		$output .= $name . '="' . esc_attr( $value ) . '" ';
	}
	$output .= '/>';
	
	if ($echo)
		echo $output;

	return $output;
}

/**
 * Generates URL for image cropping
 * 
 * @since StartBox 2.5
 *
 * @uses sb_get_post_image
 *
 * @param mixed $args specify an array or pass the arguments straight through
 *
 * @return string URI containing image path and all parameters
 */
function sb_post_image_url( $args = null ) {
	
	$defaults = array(
		'post_id' => null,
		'image_id' => null,
		'image_url' => null,
		'use_attachments' => apply_filters( 'sb_post_image_use_attachments', false ),
		'width'	=> apply_filters( 'sb_post_image_width', 200 ),
		'height'=> apply_filters( 'sb_post_image_height', 200 ),
		'align'	=> apply_filters( 'sb_post_image_align', 't' ),
		'zoom'	=> apply_filters( 'sb_post_image_zoom', 1 ),
		'quality' => apply_filters( 'sb_post_image_quality', 100 ),
		'echo'	=> apply_filters( 'sb_post_image_echo', true )
	);
	extract( $args = wp_parse_args($args, apply_filters( 'sb_post_image_settings', $defaults ) ) );
	
	// Combine all our options into the proper URI string
	$output = SCRIPTS_URL . '/timthumb.php?src=' . sb_get_post_image( $image_id, $post_id, $use_attachments, $image_url ) . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;a=' . $align . '&amp;zc=' . $zoom . '&amp;q=' . $quality;

	// Echo the output if echo is true
	if ($echo)
		echo $output;

	// Return the string
	return $output;
}

?>