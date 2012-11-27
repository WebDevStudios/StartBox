<?php

/**
 * StartBox Shortcodes
 *
 * This file contains all the definitions for the default StartBox shortcodes.
 *
 * @package StartBox
 * @subpackage Shortcodes
 */

add_shortcode( 'sidebar', 'sb_sidebar_shortcode' );
add_shortcode( 'sitemap', 'sb_get_sitemap' );

add_shortcode( 'button', 'sb_button' );
add_shortcode( 'box', 'sb_box' );
add_shortcode( 'hr', 'sb_divider' );
add_shortcode( 'divider', 'sb_divider' );
add_shortcode( 'rtt', 'sb_rtt' );
add_shortcode( 'toggle', 'sb_toggle' );

add_shortcode( 'title', 'sb_entry_title' );
add_shortcode( 'author_bio', 'sb_author_bio' );
add_shortcode( 'author', 'sb_entry_author' );
add_shortcode( 'categories', 'sb_entry_categories' );
add_shortcode( 'comments', 'sb_entry_comments' );
add_shortcode( 'date', 'sb_entry_date' );
add_shortcode( 'tags', 'sb_entry_tags' );
add_shortcode( 'time', 'sb_entry_time' );
add_shortcode( 'edit', 'sb_entry_edit' );
add_shortcode( 'more', 'sb_readmore' );

add_shortcode( 'twitter', 'sb_twitter' );
add_shortcode( 'facebook', 'sb_facebook' );
add_shortcode( 'stumble', 'sb_stumble' );

add_shortcode( 'protected', 'sb_protected' );
add_shortcode( 'expires', 'sb_expires' );
add_shortcode( 'show_after', 'sb_show_after' );

add_shortcode( 'one_half', 'sb_one_half');
add_shortcode( 'one_third', 'sb_one_third');
add_shortcode( 'two_thirds', 'sb_two_thirds');
add_shortcode( 'one_fourth', 'sb_one_fourth');
add_shortcode( 'two_fourths', 'sb_one_half');
add_shortcode( 'three_fourths', 'sb_three_fourths');
add_shortcode( 'one_fifth', 'sb_one_fifth');
add_shortcode( 'two_fifths', 'sb_two_fifths');
add_shortcode( 'three_fifths', 'sb_three_fifths');
add_shortcode( 'four_fifths', 'sb_four_fifths');
add_shortcode( 'one_sixth', 'sb_one_sixth');
add_shortcode( 'two_sixths', 'sb_one_third');
add_shortcode( 'three_sixths', 'sb_one_half');
add_shortcode( 'four_sixths', 'sb_two_thirds');
add_shortcode( 'five_sixths', 'sb_five_sixths');

add_shortcode( 'one_half_last', 'sb_one_half_last');
add_shortcode( 'one_third_last', 'sb_one_third_last');
add_shortcode( 'two_thirds_last', 'sb_two_thirds_last');
add_shortcode( 'one_fourth_last', 'sb_one_fourth_last');
add_shortcode( 'two_fourths_last', 'sb_one_half_last');
add_shortcode( 'three_fourths_last', 'sb_three_fourths_last');
add_shortcode( 'one_fifth_last', 'sb_one_fifth_last');
add_shortcode( 'two_fifths_last', 'sb_two_fifths_last');
add_shortcode( 'three_fifths_last', 'sb_three_fifths_last');
add_shortcode( 'four_fifths_last', 'sb_four_fifths_last');
add_shortcode( 'one_sixth_last', 'sb_one_sixth_last');
add_shortcode( 'two_sixths_last', 'sb_one_third_last');
add_shortcode( 'three_sixths_last', 'sb_one_half_last');
add_shortcode( 'four_sixths_last', 'sb_two_thirds_last');
add_shortcode( 'five_sixths_last', 'sb_five_sixths_last');

add_shortcode( 'copyright', 'sb_copyright' );
add_shortcode( 'site_link', 'sb_site_link' );
add_shortcode( 'WordPress', 'sb_wp_link' );
add_shortcode( 'StartBox', 'sb_footer_link' );

/**
 * Enable Shortcodes in widget areas
 *
 * @since 2.4.8
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Increase backtrack limit (see http://core.trac.wordpress.org/ticket/8553)
 *
 * @since 2.4.8
 */
@ini_set('pcre.backtrack_limit', 500000);

/**
 * Shortcode to display Return To Top link
 *
 * @since 2.4.3
 */
function sb_rtt() {
	$link = '<a href="#top" class="rtt cb" title="Return to top of page">' . apply_filters( 'sb_rtt_text', __( 'Return to Top', 'startbox' ) ) . '</a>';
	return $link;
}

/**
 * Shortcode to display a sidebar virtually anywhere.
 *
 * @since 2.5
 */
function sb_sidebar_shortcode ( $atts ) {
	extract ( shortcode_atts ( array (
		'location'	=> null,
		'id' 		=> null,
		'classes'	=> null
	), $atts ) );

	if ( is_null ( $id ) ) return null;
	if ( is_null ( $location ) ) $location = 'shortcode-'.$id; // prevents multiple shortcodes from using the same ID

	ob_start();
	sb_do_sidebar( $location , $id, $classes );
	return ob_get_clean();
}

/**
 * Displays an Edit link for admins
 *
 * @since 2.4.6
 */
function sb_entry_edit() {
	if ( current_user_can('edit_posts') )
		return '<span class="meta-sep">|</span> <span class="edit-link"><a href="' . get_edit_post_link() . '">' . __('Edit', 'startbox') . '</a></span>';
}

/**
 * Displays the current post date, if time since is installed, it will use that instead.
 * Formatted for hAtom microformat.
 *
 * @since 2.4.6
 *
 * @uses sb_time_since
 *
 */
function sb_entry_date( $atts ) {
	global $post;
	extract ( shortcode_atts ( array (
		'format' => get_option('date_format'),
		'relative' => false
	), $atts ) );

	if ( true == $relative )
		return '<span class="published entry-date">' . sb_time_since( abs( strtotime( $post->post_date_gmt . " GMT" ) ), time() ) . ' ago</span>';
	else
		return '<span class="published entry-date">' . get_the_time( $format ) . '</span>';
}

/**
 * Displays the current post time
 *
 * @since 2.4.6
 */
function sb_entry_time() {
	return '<span class="entry-time">' . get_the_time( get_option('time_format') ) . '</span>';
}

/**
 * Displays the current post categories
 *
 * @since 2.4.6
 *
 * @uses get_the_category_list
 *
 */
function sb_entry_categories() {
	return '<span class="entry-categories">' . get_the_category_list(', ') . '</span>';
}

/**
 * Displays a Read More link
 *
 * @since 2.4.9
 *
 * @uses get_permalink
 *
 */
function sb_readmore() {
	return '<a href="' . get_permalink() . '" title="' . sprintf(__("Continue Reading %s", "startbox"), esc_html(get_the_title(), 1)) . '" rel="nofollow" class="more-link">' . apply_filters( "sb_read_more", "Read &amp; Discuss &raquo;" ) . '</a>';
}

/**
 * Displays the current post title.
 *
 * @since 2.5.4
 *
 */
function sb_entry_title() {
	return get_the_title();
}

/**
 * Displays the current post author.
 * Formatted for hAtom microformat.
 *
 * @since 2.4.6
 *
 */
function sb_entry_author() {

	$output = '<span class="vcard author entry-author">';
	$output .= '<a href="' . get_author_posts_url( get_the_author_meta('ID') ) . '" class="url fn" title="' . sprintf( __('View all posts by %s', 'startbox'), esc_attr( get_the_author() ) ) .'">';
	$output .= get_the_author();
	$output .= '</a>';
	$output .= '</span>';

	return $output;
}


/**
 * Displays the current post tags or blank if none.
 *
 * @since 2.4.6
 *
 */
function sb_entry_tags() {
	if ( $tags = get_the_tag_list( '<span>' . __('Tagged: ','startbox') . '</span>', ', ' ) )
		return '<span class="entry-tags">' . $tags . '</span>';
}


/**
 * Displays the number of comments in current post as a link to the comments, wrapped in a <span>.
 *
 * @since 2.4.6
 *
 */
function sb_entry_comments() {
	ob_start();
	comments_popup_link(__('No Comments', 'startbox'), __('1 Comment', 'startbox'), __('% Comments', 'startbox'));
	return '<span class="entry-comments">' . ob_get_clean() . '</span>';
}

/**
 * Shortcode to create a content box
 *
 * @since 2.4.8
 */
function sb_author_bio( $atts, $content = null ) {

	$output = '';
	$output .= '<div id="entry-author-info">';
	$output .= '<h2>' . sprintf( esc_attr__( 'About %s', 'startbox' ), get_the_author() ) . '</h2>';
	$output .= '<div id="author-avatar">' . get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'sb_author_article_gavatar_size', 60 ) ) . '</div><!-- #author-avatar -->';
	$output .= '<div id="author-description">' . get_the_author_meta( 'description' ) . '<div id="author-link">';
	$output .= '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ). '">' . sprintf( __( 'View all posts by %s &raquo;', 'startbox' ), get_the_author() ) . '</a>';
	$output .= '</div><!-- #author-link	-->';
	$output .= '</div><!-- #author-description -->';
	$output .= '</div><!-- #entry-author-info -->';

	return $output;
}

/**
 * Shortcode to create a content box
 *
 * @since 2.4.7
 */
function sb_box( $atts, $content = null ) {
	extract( shortcode_atts( array( 'type' => 'info', 'style' => false ), $atts ) );
	if ($style == false) { $style = $type; }
	return '<div class="box ' . $style . '">' . do_shortcode($content) . '</div>';
}

/**
 * Shortcode to create an anchor tag with a class of button
 *
 * Optional arguments:
 * link: URI
 * size: small, normal, large, xl
 * color: specify a CSS colorname or HEX value
 * border: border color (e.g. blue or #0000FF)
 * text: specify a CSS colorname or HEX value
 * icon: info, alert, check, download, note (coming soon)
 * class: specify custom classes
 *
 * @since 2.4.8
 *
 */

function sb_button( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'size' => '',
   		'icon' => '',
		'class' => '',
   		'color' => '',
   		'border' => '',
   		'text' => '',
   		'link' => '#nogo'), $atts)
	);

	// Set custom classes
	$class_out = $style = '';
	if ( $size ) $class_out .= ' ' . $size;
	if ( $icon ) $class_out .= ' ' . $icon;
	if ( $class ) $class_out .= ' ' . $class;

	// Set color
	if (
		$color == 'red' OR
		$color == 'orange' OR
		$color == 'yellow' OR
		$color == 'green' OR
		$color == 'blue' OR
		$color == 'purple' OR
		$color == 'dark' OR
		$color == 'light'
	) {
		$class_out .= ' ' . $color;
	} else {
	   	if (!$border) $border = $color;
		if (!$text) $text = '#FFF';
		$style = 'style="background:' . $color . '; color:' . $text . '; border-color:' . $border . ';"';
	}

   	$output = '<a href="' . $link . '" class="button' . $class_out . '" ' . $style . '>' . $content . '</a>';
   	return $output;
}

/**
 * Shortcodes to create columns
 *
 * @since 2.4.8
 *
 */
function sb_one_half( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_half' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_one_third( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_third' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_two_thirds( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column two_thirds' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_one_fourth( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_fourth' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_three_fourths( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column three_fourths' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_one_fifth( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_fifth' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_two_fifths( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column two_fifths' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_three_fifths( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column three_fifths' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_four_fifths( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column four_fifths' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_one_sixth( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_sixth' . $class . '">' . do_shortcode( $content ) . '</div>';
}
function sb_five_sixths( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column five_sixths' . $class . '">' . do_shortcode( $content ) . '</div>';
}

// Adds class 'last' for last column in a row. There has to be a better solution than this!
function sb_one_half_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_half last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_one_third_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_third last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_two_thirds_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column two_thirds last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_one_fourth_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_fourth last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_three_fourths_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column three_fourths last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_one_fifth_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_fifth last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_two_fifths_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column two_fifths last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_three_fifths_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column three_fifths last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_four_fifths_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column four_fifths last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_one_sixth_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column one_sixth last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}
function sb_five_sixths_last( $atts, $content = null ) {
	extract( shortcode_atts( array(	'class' => '' ), $atts) );
	if ($class) $class = ' ' . $class;
	return '<div class="column five_sixths last' . $class . '">' . do_shortcode( $content ) . '</div><div class="cb"></div>';
}

/**
 * Shortcodes for styling lists
 *
 * @since 2.4.8
 *
 */
function sb_check_list( $atts, $content = null ) {
	return str_replace( '<ul>', '<ul class="check_list">', do_shortcode( $content ) );
}

function sb_arrow_list( $atts, $content = null ) {
	return str_replace( '<ul>', '<ul class="arrow_list">', do_shortcode( $content ) );
}

/**
 * Shortcode for creating a divider
 *
 * @since 2.4.8
 *
 */
function sb_divider( $atts, $content = null ) {
	extract( shortcode_atts( array( 'show_top' => false, 'align' => 'center' ), $atts ) );

	$top = ( $show_top ) ? do_shortcode( '[rtt]' ) : '' ;
	return '<div class="hr divider" style="text-align:' . $align . ';">' . $top . '</div>';
}

/**
 * Shortcode for creating a jQuery toggle link
 *
 * @since 2.4.8
 *
 */
function sb_toggle( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => 'Show More',
		'class' => '',
		'id' => '',
		'container' => 'div',
		'container_class' => '',
		'position' => 'before' ), $atts )
	);

	STATIC $i = 1;
	if (!$id) { $id = 'toggle-' . $i; $i++; }

	$output = '';
	if ($position == 'before') { $output .= '<a href="#' . $id . '" class="toggle ' . esc_attr( $class ) . '">' . $title . '</a>'; }
	$output .= '<' . $container . ' id="' . esc_attr( $id ) . '" class="toggled ' . esc_attr( $container_class ) . '">' . do_shortcode( $content ) . '</' . $container . '>';
	if ($position != 'before') { $output .= '<a href="#' . esc_attr( $id ) . '" class="toggle ' . esc_attr( $class ) . '">' . $title . '</a>'; }
	return $output;
}

/**
 * Twitter button
 *
 * @since 2.4.8
 * @link http://twitter.com/goodies/tweetbutton
 */
function sb_twitter( $atts, $content = null ) {
   	extract(shortcode_atts(array(
		'url' => '',
   		'style' => 'horizontal',
		'source' => '',
		'text' => '',
		'related' => '',
		'lang' => '',
		'float' => 'left'), $atts)
	);

	$output = '';
	if ( $url ) { $output .= ' data-url="'.esc_attr( $url ).'"'; }
	if ( $source ) { $output .= ' data-via="'.esc_attr( $source ).'"'; }
	if ( $text ) { $output .= ' data-text="'.esc_attr( $text ).'"'; }
	if ( $related ) { $output .= ' data-related="'.esc_attr( $related ).'"'; }
	if ( $lang ) { $output .= ' data-lang="'.esc_attr( $lang ).'"'; }
	$output = '<div class="twitter ' . esc_attr( $float ) . '"><a href="http://twitter.com/share" class="twitter-share-button"'.$output.' data-count="'.esc_attr( $style ).'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';

	return $output;
}

/**
 * Facebook Like button
 *
 * @since 2.4.8
 * @link http://developers.facebook.com/docs/reference/plugins/like
 */
function sb_facebook( $atts, $content = null ) {
	global $post;
   	extract(shortcode_atts(array(
		'float'		=> 'left',
		'width' 	=> 450,
		'action' 	=> 'like',
		'send'		=> 'true',
		'faces'		=> 'false',
		'colorscheme' => 'light',
		'font'		=> 'arial',
		'url'		=> urlencode(get_permalink($post->ID)),
		'style' 	=> 'standard'
	), $atts));

	if ( $style == "button" ) { $style = "button_count"; }
	elseif ( $style == "box" ) { $style = "box_count"; }
	else { $style = "standard";	}

	return '<div id="fb-root" class="facebook ' . esc_attr( $float ) . '"></div><script src="http://connect.facebook.net/en_US/all.js#appId=251140598259252&amp;xfbml=1"></script><fb:like href="' . esc_url( $url ) . '" send="' . esc_attr( $send ) . '" width="' . esc_attr( $width ) . '" show_faces="' . esc_attr( $faces ) . '" action="' . esc_attr( $action ) . '" colorscheme="' . esc_attr( $colorscheme ) . '" layout="' . esc_attr( $style ) . '" font="' . esc_attr( $font ) . '"  class="facebook ' . esc_attr( $float ) . '"></fb:like>';
}

/**
 * Stumble Upon button
 *
 * @since 2.4.8
 * @link http://www.stumbleupon.com/badges/
 */
function sb_stumble( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'link' => '',
		'style' => 'box',
		'float' => 'left'), $atts)
	);

	if ( $style == 'compact' ) { $s = 1; }
	elseif ($style == 'box' ) { $s = 5; }
	elseif ($style == 'icon' ) { $s = 4; }
	else { $s = $style; }
	if ( $link ) { $link = ' &r=' . $link; }

	return '<div class="stumble ' . esc_attr( $float ) . '"><script src="http://www.stumbleupon.com/hostedbadge.php?s=' . $s . $link . '"></script></div>';
}


/**
 * Protect member-only content
 *
 * @since 2.4.8
 *
 */
function sb_protected( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'show_login' => 'false',
		'class' => ''
		), $atts )
	);

	if ( is_user_logged_in() ) { return do_shortcode( $content ); }
	else {
		$output = '<div class="protected ' . $class . '">';
		$output .= apply_filters( 'sb_protected_text', __( 'Sorry, you must be logged in to view this content.', 'startbox' ) );
		if ($show_login == 'true') { $output .= wp_login_form( array( 'echo' => 0 ) ); }
		$output .= '</div>';

		return apply_filters( 'sb_protected', $output );
	}
}

/**
 * Hide content after specific expiration date
 *
 * @since 2.6
 */
function sb_expires( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'date' => '',
		'expired_message' => '',
		), $atts )
	);

	$today = time();
	$expiration = strtotime($date);

	if ( $today >= $expiration )
		return do_shortcode( $expired_message );
	else
		return do_shortcode( $content );
}

/**
 * Show content after specific teaser date
 *
 * @since 2.6
 */
function sb_show_after( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'date' => '',
		'teaser' => '',
		), $atts )
	);

	$today = time();
	$teaser_date = strtotime($date);

	if ( $today < $teaser_date )
		return do_shortcode( $teaser );
	else
		return do_shortcode( $content );
}

/**
 * Function for producing a sitemap.
 *
 * @since 2.4.9
 *
 * @uses apply_filters() to pass new 'sb_sitemap_defaults'
 * @uses wp_list_pages()
 * @uses wp_list_categories()
 *
 * @param array $args array of all configurable options
 *
 */
function sb_sitemap( $args = '' ) {
	echo sb_get_sitemap( $args );
}
function sb_get_sitemap( $args = '' ) {
	global $wp_query, $post;
	$cached_query = $wp_query;
	$cached_post = $post;
	$output = '';

	$defaults = array(
		'show_pages'		=> true,	// Include Pages in output
		'show_categories'	=> true,	// Include Categories in output
		'show_posts'		=> true,	// Include Posts (sorted by category) in output
		'show_cpts'			=> true,	// Include Custom Post Types in output
		'exclude_pages'		=> '',		// Comma-separated list of pages to exclude
		'exclude_categories' => '',		// Comma-separated list of categories to exclude
		'exclude_post_types' => apply_filters( 'sb_sitemap_exclude_post_types', array('attachment', 'revision', 'nav_menu_item', 'slideshow', 'page', 'post') ), // Array of post-types to exclude
		'class'				=> 'sitemap',// Custom class(es) to use in ul elements
		'container_class'	=> 'sitemap-container', // Custom class(es) to use in div wrappers
		'header_container'	=> 'h3',	// Element type to use for wrapping primary headings
		'subheader_container' => 'h4'	// Element type to use for wrapping secondary headings
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

	return $output;
}

/**
 * Shortcode to insert link to current site
 *
 * @since 2.6
 */
function sb_site_link( $atts ) {
	extract( shortcode_atts( array(
		'url' => site_url(),
		'target' => '_blank',
		'text' => get_bloginfo('title')
		), $atts ) );

	return '<a href="' . $url . '" target="' . $target . '">' . $text . '</a>';
}

/**
 * Shortcode to insert WordPress link
 *
 * @since 2.6
 */
function sb_wp_link( $atts ) {
	extract( shortcode_atts( array( 'target' => '_blank' ), $atts ) );

	return '<a href="http://wordpress.org/" target="' . $target . '">WordPress</a>';
}

/**
 * Shortcode to insert StartBox link
 *
 * @since 2.6
 */
function sb_footer_link( $atts ) {
	extract( shortcode_atts( array( 'target' => '_blank', 'affiliate_link' => '' ), $atts ) );

	return '<a href="http://wpstartbox.com/" target="' . $target . '">StartBox</a>';
}

/**
 * Shortcode to insert copyright date(s)
 *
 * @since 2.6
 */
function sb_copyright( $atts ) {
	extract( shortcode_atts( array( 'year' => date('Y') ), $atts ) );

	$current_year = date('Y');
	if ( $year == $current_year )
		return '&copy;' . $current_year;
	else
		return '&copy;' . $year . '-' . $current_year;

}