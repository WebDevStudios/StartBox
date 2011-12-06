<?php 
/**
 * StartBox Custom Functions
 *
 * Lots of misc functions. These should probably be reviewed and moved to more specific files.
 *
 * @package StartBox
 * @subpackage Functions
 */

// Add filters for the description/meta content in archive.php
add_filter( 'archive_meta', 'wptexturize' );
add_filter( 'archive_meta', 'convert_smilies' );
add_filter( 'archive_meta', 'convert_chars' );
add_filter( 'archive_meta', 'wpautop' );


// Filter body_class to include user browser, category, and date classes
function sb_body_classes($classes) {
	global $wp_query;
	
	// Determine user browser
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	elseif($is_iphone) $classes[] = 'iphone';
	else $classes[] = 'unknown';
	
	// Determine IE version, specifically. Credit: http://wordpress.org/extend/plugins/krusty-msie-body-classes/
	if(preg_match('/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version)){
		// add a class with the major version number
		$classes[] = 'ie' . $browser_version[1];
		
		// add a class with the major and minor version number, if it's MSIE 5.5
		if('5' == $browser_version[1] && isset($browser_version[2]) && '5' == $browser_version[2])
			$classes[] = 'ie' . strtolower(str_replace('.', '-', strtolower($browser_version[0])));
		
		// add an ie-old and ie-lt7 classes to match MSIE 6 and older
		if (7 > $browser_version[1])
			$classes[] = 'ie-old';
			$classes[] = 'ie-lt7';
		
		// add an ie-lt8 class to match MSIE 7 and older
		if (8 > $browser_version[1])
			$classes[] = 'ie-lt8';
		
		// add an ie-lt9 class to match MSIE 8 and older
		if (9 > $browser_version[1])
			$classes[] = 'ie-lt9';
	}
	
	// Adds category classes for each category on single posts
	if ( $cats = get_the_category() )
		foreach ( $cats as $cat )
			$classes[] = 's-category-' . $cat->slug;
			
	// Applies the time- and date-based classes
    sb_date_classes( time(), $classes, $p = null );

	// Adds classes for the month, day, and hour when the post was published
	if ( is_single() )
		sb_date_classes( mysql2date( 'U', $wp_query->post->post_date ), $classes, 's-' );
	
	// Adds post and page slug class, prefixed by 'post-' or 'page-', respectively
	if ( is_single() )
    	$classes[] = 'post-' . $wp_query->post->post_name;
	elseif( is_page() )
		$classes[] = 'page-' . $wp_query->post->post_name;
		
	// return the $classes array
	return $classes;
}
add_filter('body_class','sb_body_classes');


// Filter post_class to include an author class
function sb_post_classes($classes) {
	// Author for the post queried
	$classes[] = 'author-' . sanitize_title_with_dashes( strtolower( get_the_author() ) );
	
	// return the $classes array
	return $classes;
}
add_filter('post_class','sb_post_classes');


// Generates time- and date-based classes relative to GMT (UTC)
function sb_date_classes($t, &$classes, $p) {
	$t = $t + ( get_option('gmt_offset') * 3600 );
	$classes[] = $p . 'y' . gmdate( 'Y', $t ); // Year
	$classes[] = $p . 'm' . gmdate( 'm', $t ); // Month
	$classes[] = $p . 'd' . gmdate( 'd', $t ); // Day
	$classes[] = $p . 'h' . gmdate( 'H', $t ); // Hour
}

/**
 * For category lists on category archives: Returns other categories except the current one (redundant)
 *
 * @since Unknown
 *
 * @param string $glue The connecting element between category names.
*/
function sb_cat_lists($glue) {
	$current_cat = single_cat_title( '', false );
	$separator = "\n";
	$cats = explode( $separator, get_the_category_list($separator) );
	foreach ( $cats as $i => $str ) {
		if ( strstr( $str, ">$current_cat<" ) ) {
			unset($cats[$i]);
			break;
		}
	}
	if ( empty($cats) )
		return false;

	return trim(join( $glue, $cats ));
}

/**
 * For tag lists on tag archives: Returns other tags except the current one (redundant)
 *
 * @since Unknown
 *
 * @param string $glue The connecting element between category names.
*/
function sb_tag_lists($glue) {
	$current_tag = single_tag_title( '', '',  false );
	$separator = "\n";
	$tags = explode( $separator, get_the_tag_list( "", "$separator", "" ) );
	foreach ( $tags as $i => $str ) {
		if ( strstr( $str, ">$current_tag<" ) ) {
			unset($tags[$i]);
			break;
		}
	}
	if ( empty($tags) )
		return false;

	return trim(join( $glue, $tags ));
}



/**
 * Tests if any of a post's assigned categories are descendants of specified categories
 *
 * @since Unknown
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 */
function sb_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

/**
 * Tests to see if a specific page template is active.
 *
 * @since Unknown
 *
 * @param string $pagetemplate is the template filename
*/
function sb_is_pagetemplate_active($pagetemplate = '') {
	global $wpdb;
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '_wp_page_template' AND meta_value = %s", $pagetemplate ) ) ) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Tests to see if current page has a parent.
 *
 * @since 2.4.9
 *
 * @param integer $page_id the page ID to test
 * @param integer $parent_id (optional) check if page is child of specific parent
*/
function sb_is_child_page( $parent_id = null, $page_id = null ) {
	global $post;
	$pid = ($page_id) ? $page_id : $post->ID;
	
	if ( is_page($pid) && $post->post_parent ) { // Verify we're working with a page and it has a parent
		if ( isset( $parent_id ) && !in_array( $parent_id, get_post_ancestors($pid) ) ) { return false; }// If the specified parent_id is not an ancestor of the current page, return false
		else { return true; } // Otherwise, it has a parent and the specified parent id match. Return true.
	} else {
		return false; // if it's not a page or has no parent, return false.
	}

}

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
	
	$output = '<img class="' . $class . $nophoto . '" src="' . SCRIPTS_URL . '/timthumb.php?src=' . sb_get_post_image( $image_id, $post_id, $use_attachments, $image_url ) . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;a=' . $align . '&amp;zc=' . $zoom . '&amp;q=100"';
	foreach ( $attr as $name => $value ) {
		$exlcude = null;
		if ( in_array( $name, array( 'post_id', 'image_id', 'align', 'class', 'crop', 'zoom', 'echo', 'image_url', 'use_attachments', 'hide_nophoto', 'enabled' ) ) ) continue;
		$output .= " $name=" . '"' . $value . '"';
	}
	$output .= ' />';
	
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


/** 
 * Display Relative Timestamps
 *
 * This plugin is based on code from Dunstan Orchard's Blog. Pluginiffied by Michael Heilemann:
 * @link http://www.1976design.com/blog/archive/2004/07/23/redesign-time-presentation/
 *
 * Usage:
 * For posts: echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . ' ago';
 * For comments: echo time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time()) . ' ago';
 *
 * @since StartBox 2.4.6
 * @param integer $older_date The original date in question
 * @param integer $newer_date Specify a known date to determine elapsed time. Will use current time if false Default: false 
 * @return string Time since 
*/

function sb_time_since($older_date, $newer_date = false) {

	// array of time period chunks
	$chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'minute'),
	);
	
	// Newer Date (false to use current time)
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	return $output;
}

	
/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @since StartBox 2.4.7
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function sb_dropdown_posts($args = '') {

	$defaults = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'order_by' => 'post_date',
		'order' => 'DESC',
		'limit' => 30,
		'selected' => 0,
		'echo' => 1,
		'name' => '',
		'id' => '',
		'class' => 'postlist',
		'show_option_none' => true,
		'option_none_value' => 'Select a Post'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Query the Posts
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$limit = ( $limit ) ? ' LIMIT '.absint( $limit ) : '';
	$id = esc_attr($id);
	$name = esc_attr($name);
	$output = '';
	$order_by = sanitize_sql_orderby( $order_by . ' ' . $order );
	
	$post_list = (array)$wpdb->get_results(
		$wpdb->prepare("
		SELECT ID, post_title, post_date
		FROM $wpdb->posts
		WHERE post_type = %s
		AND post_status = %s
		ORDER BY {$order_by}
		{$limit}
	", $post_type, $post_status ) );
	
	$output .= "\t" . "\t" . '<select style="width:100%;" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '">'."\n";
	if ( !empty($post_list) ) {
		if ( $show_option_none ) $output .= "\t" . "\t" . "\t" . '<option value="">' . $option_none_value . '</option>';
		foreach ($post_list as $posts) {
			if ($selected == $posts->ID) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= "\t" . "\t" . "\t" . '<option value="' . $posts->ID . '"' . $select . '>' . $posts->post_title . '</option>';
		}
	} else {
		$output .= "\t" . "\t" . "\t" . '<option value="">Nothing to Display</option>';
	}
	$output .= '</select>';

	$output = apply_filters('wp_dropdown_posts', $output);

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Create a nice multi-tag title
 *
 * Credits: Ian Stewart and Martin Kopischke for providing this code
 *
 * @since StartBox 2.4.7
 */
function sb_tag_query() {
	$nice_tag_query = get_query_var('tag'); // tags in current query
	$nice_tag_query = str_replace(' ', '+', $nice_tag_query); // get_query_var returns ' ' for AND, replace by +
	$tag_slugs = preg_split('%[,+]%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of tag slugs
	$tag_ops = preg_split('%[^,+]*%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of operators

	$tag_ops_counter = 0;
	$nice_tag_query = '';

	foreach ($tag_slugs as $tag_slug) { 
		$tag = get_term_by('slug', $tag_slug ,'post_tag');
		// prettify tag operator, if any
		if ( isset( $tag_ops[$tag_ops_counter] ) &&  $tag_ops[$tag_ops_counter] == ',') {
			$tag_ops[$tag_ops_counter] = ', ';
		} elseif ( isset( $tag_ops[$tag_ops_counter] ) && $tag_ops[$tag_ops_counter] == '+') {
			$tag_ops[$tag_ops_counter] = ' + ';
		} else {
			$tag_ops[$tag_ops_counter] = '';
		}
		// concatenate display name and prettified operators
		$nice_tag_query = $nice_tag_query . $tag->name . $tag_ops[$tag_ops_counter];
		$tag_ops_counter += 1;
	}
	 return $nice_tag_query;
}

/**
 * Helper function for building a menu based on user selection with the Options API
 *
 * @since StartBox 2.4.8
 *
 * @uses wp_page_menu()
 * @uses wp_nav_menu()
 * @uses wp_list_categories()
 *
 * @param array|string $args Optional. Override default arguments. type = type of menu, class = class of containing element, show_home will show/hide home link
 */
if ( !function_exists( 'sb_nav_menu' ) ) {
	function sb_nav_menu( $args = '' ) {
		$defaults = array(
				'type'			=> 'pages',
				'class'			=> 'nav',
				'show_home'		=> 1,
				'echo'			=> false,
				'container'		=> 'div', 
				'container_id'	=> '', 
				'menu_class'	=> '', 
				'menu_id'		=> '',
				'before'		=> '',
				'after'			=> '',
				'link_before'	=> '',
				'link_after'	=> '',
				'depth'			=> 0,
				'fallback_cb'	=> 'sb_nav_menu_fallback',
				'extras'		=> '',
				'walker'		=> ''
			);
		$r = wp_parse_args( $args, apply_filters( "sb_nav_menu_defaults", $defaults ) );
		extract( $r, EXTR_SKIP );

		if ( $type == 'none' || $type == '' ) 
			return;
		
		$output = wp_nav_menu( array( 
			'menu' 				=> $type,
			'container'			=> $container, 
			'container_class'	=> $class, 
			'container_id'		=> $container_id, 
			'menu_class'		=> $menu_class, 
			'menu_id'			=> $menu_id,
			'before'			=> $before,
			'after'				=> $after,
			'link_before'		=> $link_before,
			'link_after'		=> $link_after,
			'depth'				=> $depth,
			'show_home'			=> $show_home,
			'fallback_cb'		=> $fallback_cb,
			'extras'			=> $extras,
			'walker'			=> $walker,
			'echo'				=> false ) );
	
		$nav_menu = apply_filters( "sb_{$menu_id}_menu", $output );

		if ($echo)
			echo $nav_menu;
		else
			return $nav_menu;
	}
}

/**
 * Fallback function for building menus in the event no custom menus exist -- copied mostly from wp_nav_menu()
 *
 * @since StartBox 2.4.9
*/
if ( !function_exists('sb_nav_menu_fallback') ) {
	function sb_nav_menu_fallback( $args = array() ) {
		$args = apply_filters( 'wp_nav_menu_args', $args );
		$args = (object) $args;
	
		$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
		$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
	
		$nav_menu = $items = '';
		$nav_menu .= '<'. $args->container . $id . $class . '>';
		$nav_menu .= '<ul id="' . $args->menu_id . '">';
		$nav_menu .= apply_filters( 'wp_nav_menu_items', $items, $args );
		$nav_menu .= '</ul>';
		$nav_menu .= '</' . $args->container . '>';
		$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

		if ( $args->echo )
			echo $nav_menu;
		else
			return $nav_menu;
	}
}

/**
 * Filter for replacing wp_nav_menu_items with either pages or categories
 *
 * @since StartBox 2.4.9
 *
 */
function sb_nav_menu_items($items, $args ) {
	extract( wp_parse_args( $args ) );

	// Include Link to homepage based on user selection
	$is_home = ( is_front_page() ) ? ' current-menu-item' : '' ;
	$home = ( $show_home ) ? '<li class="menu-item menu-item-home' . $is_home . '"><a href="' . home_url('/') . '">Home</a></li>' : '' ;
	
	// Change menu contents based on user selection
	if ( $menu == 'pages' ) {
		$exclude = (get_option('show_on_front') == 'page') ? get_option('page_on_front') : '';
		$items = $home . wp_list_pages('title_li=&exclude=' . $exclude . '&depth=' . $depth . '&echo=0');
		if( $page = strripos( $items, 'current_page_item') ) { $items = substr_replace( $items, ' current-menu-item', $page+17, 0 ); }
		if( $page_parent = strripos( $items, 'current_page_ancestor') ) { $items = substr_replace( $items, ' current-menu-ancestor', $page_parent+21, 0 ); }
	} elseif ( $menu == 'categories' ) {
		$items = $home . wp_list_categories('title_li=&depth=' . $depth . '&echo=0');
		if( $cat = strripos( $items, 'current-cat') ) { $items = substr_replace( $items, ' current-menu-item', $cat+11, 0 ); }
		if( $cat_parent = strripos( $items, 'current-cat-parent') ) { $items = substr_replace( $items, ' current-menu-ancestor', $cat_parent+18, 0 ); }
	} else {
		$items = $home . $items;
	}
	
	// Adds .first and .last classes to respective menu items
    if( $first = strpos( $items, 'class=' ) ) { $items = substr_replace( $items, 'first ', $first+7, 0 ); }
    if( $last = strripos( $items, 'class=') ) { $items = substr_replace( $items, 'last ', $last+7, 0 ); }
	
	// Add extras
	if ( $extras == 'search' ) {
		$items .= '<li class="menu-item menu-item-type-search">';
		$items .= '<form class="searchform" method="get" action="' . home_url() . '">';
		$items .= '<input name="s" type="text" class="searchtext" value="" title="' . apply_filters( 'sb_search_text', 'Type your search and press Enter.' ) . '" size="10" tabindex="1" />';
		$items .= '<input type="submit" class="searchbutton button" value="Search" tabindex="2" />';
		$items .= '</form>';
		$items .= '</li>';
	} elseif ( $extras == 'social' ) {
		$options = get_option(THEME_OPTIONS);
		$rss = (isset($options[$menu_id . '-social-rss'])) ? $options[$menu_id . '-social-rss'] : '';
		$services = array(
			'rss'		=> $rss,
			'twitter'	=> $options[$menu_id . '-social-twitter'],
			'facebook'	=> $options[$menu_id . '-social-facebook'],
			'youtube'	=> $options[$menu_id . '-social-youtube'],
			'vimeo'		=> $options[$menu_id . '-social-vimeo'],
			'flickr'	=> $options[$menu_id . '-social-flickr'],
			'delicious'	=> $options[$menu_id . '-social-delicious'],
			'linkedin'	=> $options[$menu_id . '-social-linkedin'],
		);
		$icon_url = apply_filters( 'sb_nav_social_images_url', IMAGES_URL.'/social/' );
		$icon_size = apply_filters( 'sb_nav_social_images_size', 24 );
		
		foreach ($services as $service => $url) {
			$text = apply_filters( "sb_social_{$service}", sprintf( __( 'Connect on %s', 'startbox'), $service ) );
			
			if ( $service == 'rss' && isset($url) && $url === true ) {
				$rss_text = apply_filters( 'sb_social_rss', __( 'Subscribe via RSS', 'startbox') );
				$items .= '<li class="menu-item menu-item-type-social menu-item-' . $service . '">';
				$items .= '<a href="' . get_bloginfo('rss2_url') . '" target="_blank" title="' . $rss_text . '">';
				$items .= '<img src="' . $icon_url . $service . '.png" width="' . $icon_size . 'px" height="' . $icon_size . 'px" alt="' . $rss_text . '" />';
				$items .= '<span>RSS Feed</span>';
				$items .= '</a></li>';
			} elseif (isset($url) && $url != '') {
				$items .= '<li class="menu-item menu-item-type-social menu-item-' . $service . '">';
				$items .= '<a href="' . $url . '" target="_blank" title="' . $text . '">';
				$items .= '<img src="' . $icon_url . $service . '.png" width="' . $icon_size . 'px" height="' . $icon_size . 'px" alt="' . $text . '" />';
				$items .= '<span>' . $text . '</span>';
				$items .= '</a></li>';
			}
		}
	}

    return $items;
}
add_filter( 'wp_nav_menu_items', 'sb_nav_menu_items', 10, 2 );


/**
 * Function for producing a sitemap.
 *
 * @since StartBox 2.4.9
 *
 * @uses apply_filters() to pass new 'sb_sitemap_defaults' 
 * @uses wp_list_pages()
 * @uses wp_list_categories()
 *
 * @param array $args array of all configurable options
 *
 */
function sb_sitemap( $args = '' ) {
	global $wp_query, $post;
	$cached_query = $wp_query;
	$cached_post = $post;
	$output = '';
	
	$defaults = array(
		'show_pages'		=> true,
		'show_categories'	=> true,
		'show_posts'		=> true,
		'show_cpts'			=> true,
		'exclude_pages'		=> '',
		'exclude_categories' => '',
		'exclude_post_types' => apply_filters( 'sb_sitemap_exclude_post_types', array('attachment', 'revision', 'nav_menu_item', 'slideshow', 'page', 'post') ),
		'class'				=> 'sitemap',
		'container_class'	=> 'sitemap-container',
		'header_container'	=> 'h3',
		'subheader_container' => 'h4',
		'echo'				=> true
	);
	$r = wp_parse_args( $args, apply_filters( 'sb_sitemap_defaults', $defaults ) );
	extract( $r, EXTR_SKIP );
	
	if ( $show_pages ) {
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-page">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Pages', 'startbox' ) . '</' . $header_container . '>' . "\n";
		$output .= "\t" . '<ul id="pagelist" class="' . $class . '">' . "\n";
		$output .= "\t\t" . wp_list_pages('title_li=&exclude=' . $exclude_pages . '&depth=0&echo=0') . "\n";
		$output .= "\t" . '</ul>' . "\n";
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-page -->' . "\n";
	} if ($show_cpts) {
		$post_types = get_post_types( array('public'=>true),'objects'); 
		foreach ( $post_types as $cpt ) {
			if ( !in_array( $cpt->name, $exclude_post_types ) ) {
        		$posts = new WP_query( array(
					'posts_per_page' => 500,
					'post_type'	=> $cpt->name
					) );
				if ( $posts->have_posts() ) {
					$output .= '<div class="' . $container_class . ' ' . $container_class . '-cpt ' . $container_class . '-' . $cpt->name . '">' . "\n";
					$output .= "\t" . '<' . $header_container . '>' . $cpt->label . '</' . $header_container . '>' . "\n";
					$output .= "\t" . '<ul id="cptlist" class="' . $class . '">' . "\n";
					while ( $posts->have_posts()) : $posts->the_post();
						$output .= "\t\t" . '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . get_comments_number() . ')</li>' . "\n";
					endwhile;
					$output .= "\t" . '</ul>' . "\n";
					$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-cpt ' . $container_class . '-' . $cpt->name . ' -->' . "\n";
				}
			}
		}
	} if ( $show_categories ) {
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-category">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Categories', 'startbox' ) . '</' . $header_container . '>' . "\n";
		$output .= "\t" . '<ul id="catlist" class="' . $class . '">' . "\n";
		$output .= "\t\t" . wp_list_categories('title_li=&exclude=' . $exclude_categories . '&depth=0&echo=0') . "\n";
		$output .= "\t" . '</ul>' . "\n";
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-category -->' . "\n";
	} if ( $show_posts ) {
		
        $categories = get_categories( 'exclude=' . $exclude_categories );
		
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-post">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Posts by Category', 'startbox' ) . '</' . $header_container . '>' . "\n";
		foreach ( $categories as $cat ) {
        	query_posts( 'cat=' . $cat->cat_ID );
			if ( have_posts() ) {
	            $output .= "\t" . '<' . $subheader_container . '>' . $cat->cat_name . '</' . $subheader_container . '>' . "\n";
	            $output .= "\t" . '<ul id="postlist" class="' . $class . '">' . "\n";
				while (have_posts()) : the_post();
					$output .= "\t\t" . '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . get_comments_number() . ')</li>' . "\n";
				endwhile;
				$output .= "\t" . '</ul>' . "\n";
			}
		}
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-post -->' . "\n";
	}
	
	$wp_query = $cached_query;
	$post = $cached_post;
	
	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Function for retrieving taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'get_taxonomy_term_type' ) ) {
	function get_taxonomy_term_type($taxonomy,$term_id) {
		return get_option("_term_type_{$taxonomy}_{$term->term_id}");
	}
}

/**
 * Function for updating taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 * @param mixed $value the new value
 *
 */
if ( !function_exists( 'update_taxonomy_term_type' ) ) {
	function update_taxonomy_term_type($taxonomy,$term_id,$value) {
		update_option("_term_type_{$taxonomy}_{$term_id}",$value);
	}
}

/**
 * Function for deleting taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'delete_taxonomy_term_type' ) ) {
	function delete_taxonomy_term_type($taxonomy,$term_id ) {
		delete_option("_term_type_{$taxonomy}_{$term_id}");
	}
}


/**
 * Utility: Verify a given post type
 *
 * @since StartBox 2.5
 * @param string $type the post type to verify against
 */
function sb_verify_post_type( $type ) {
	global $post_type;
	
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $type || isset( $post_type ) && $post_type == $type)
		return true;
	else
		return false;
}

?>