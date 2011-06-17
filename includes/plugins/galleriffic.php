<?php
/**
 * Plugin Name: Galleriffic Gallery
 * Plugin URI: http://rzen.net
 * Description: Replace the default gallery feature with a the Gallerific jQuery gallery plugin, and valid code!
 * Version: 0.0.1
 * Author: Brian Richards
 * Author URI: http://rzen.net
 *
 * Borrowing most of the logic from Justin Tadlock's Cleaner Gallery plugin,
 * I was able to mesh together a solution for using the Galleriffic jQuery script.
 *
 * @internal Much of this code has been adopted from Justin Tadlock's Cleaner Gallery plugin
 * @author Justin Tadlock
 * @link http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

// Check to see if current theme supports this feature
if( !current_theme_supports('sb-galleriffic') ) return;


// Add Scripts To Head On Front End Only
function sb_galleries_template_redirect() {
	wp_enqueue_script('galleries');
}
add_action('template_redirect', 'sb_galleries_template_redirect');


/*
* We're going to filter the default gallery shortcode.
* So, we're adding our own function here.
*/
add_filter( 'post_gallery', 'galleriffic_gallery', 10, 2 );

function galleriffic_gallery() {
	global $post;

	/* Orderby */
	if ( isset( $attr['orderby'] ) ) :
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	endif;

	/*
	* Extract default gallery settings
	*/
	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	), $attr));

	/*
	* Make sure $id is an integer
	*/
	$id = intval( $id );
	
	/*
	* Get image attachments
	* If none, return
	*/
	$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	if ( empty( $attachments ) )
		return '';

	/*
	* If is feed, leave the default WP settings
	* We're only worried about on-site presentation
	*/
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	$i = 0;

	/*
	* Remove the style output in the middle of the freakin' page.
	* This needs to be added to the header.
	* The width applied through CSS but limits it a bit.
	*/

	/*
	* Open the gallery <div>
	*/
	$output .= '<div id="gallery-wrap" class="gallery-wrap">'."\n";
	$output .= '<div id="gallery-'.$id.'" class="content gallery gallery-'.$id.'">'."\n";
		$output .= '<div id="loading" class="loader"></div>'."\n";
		$output .= '<div id="slideshow" class="slideshow"></div>'."\n";
		$output .= '<div id="controls" class="controls"></div>'."\n";
		$output .= '<div id="caption" class="embox"></div>'."\n";
	$output .= '</div><!--#gallery-'.$id.'-->'."\n";
	$output .= '<div id="thumbs" class="navigation">'."\n";
	$output .= '<ul class="thumbs noscript">'."\n";
	/*
	* Loop through each attachment
	*/
	foreach ( $attachments as $id => $attachment ) :

		/*
		* Get the caption and title
		*/
		$caption = esc_html( $attachment->post_excerpt, 1 );
		$title = esc_html( $attachment->post_title, 1 );
		$link = wp_get_attachment_image_src( $id, 'large' );
		$img = wp_get_attachment_image_src( $id, $size );
		
		/*
		* Open each gallery item
		*/
		$output .= "\n\t\t\t\t\t<li class='gallery-item'>";
			$output .= '<a class="thumb" href="' .  wp_get_attachment_url( $id ) . '" title="' . $title . '">';
				$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';
			$output .= '</a>';

		/*
		* If image caption is set
		*/
		if ( $caption ) :
			$output .= "\n\t\t\t\t\t\t<div class='caption'>";
				$output .= $caption;
			$output .= "</div>";
		endif;

		/*
		* Close individual gallery item
		*/
		$output .= "\n\t\t\t\t\t</li>";

	endforeach;

	/*
	* Close gallery and return it
	*/

		$output .= '</ul><!--.thumbs-->'."\n";
		$output .= '</div><!--#thumbs-->'."\n";
		$output .= '</div><!--#gallery-wrap-->'."\n";
		$output .= '<div class="cb"></div>'."\n";

	/*
	* Return out very nice, valid XHTML gallery.
	*/
	return $output;

}
?>