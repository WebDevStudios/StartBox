<?php
/**
 * StartBox Hooks and Filters
 *
 * A collection of many of StartBox's default hooks, as well as some default filtration.
 *
 * @package StartBox
 * @subpackage Functions
 */

////////////////////////////////////////////////// Body, Post and Comment class filters //////////////////////////////////////////////////

// Filter body_class to include user browser, category, and date classes
function sbx_body_classes($classes) {
	global $wp_query;

	// Determine user's browser and adds appropriate class
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
	if($is_lynx) $classes[]       = 'lynx';
	elseif($is_gecko) $classes[]  = 'gecko';
	elseif($is_opera) $classes[]  = 'opera';
	elseif($is_NS4) $classes[]    = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[]     = 'ie';
	elseif($is_iphone) $classes[] = 'iphone';
	else $classes[]               = 'unknown';

	// Include user's IE version for version-specific hacking. Credit: http://wordpress.org/extend/plugins/krusty-msie-body-classes/
	if( preg_match( '/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version ) ){

		// add a class with the major version number
		$classes[] = 'ie' . $browser_version[1];

		// add an ie-lt9 class to match MSIE 8 and older
		if ( 9 > $browser_version[1] )
			$classes[] = 'ie-lt9';

		// add an ie-lt8 and ie-old class to match MSIE 7 and older
		if ( 8 > $browser_version[1] ) {
			$classes[] = 'ie-lt8';
			$classes[] = 'ie-old';
		}

	}

	// Adds category classes for each category on single posts
	if ( $cats = get_the_category() )
		foreach ( $cats as $cat )
			$classes[] = 's-category-' . $cat->slug;

	// Applies the time- and date-based classes
    sbx_date_classes( time(), $classes, $p = null );

	// Adds classes for the month, day, and hour when the post was published
	if ( is_single() )
		sbx_date_classes( mysql2date( 'U', $wp_query->post->post_date ), $classes, 's-' );

	// Adds post and page slug class, prefixed by 'post-' or 'page-', respectively
	if ( is_single() )
    	$classes[] = 'post-' . $wp_query->post->post_name;
	elseif( is_page() )
		$classes[] = 'page-' . $wp_query->post->post_name;

	// Gutters
	$classes[] = 'gutters';

	// return the $classes array
	return $classes;
}
add_filter('body_class','sbx_body_classes');


// Filter post_class to include an author class
function sbx_post_classes($classes) {
	// Author for the post queried
	$classes[] = 'author-' . sanitize_title_with_dashes( strtolower( get_the_author() ) );

	// return the $classes array
	return $classes;
}
add_filter('post_class','sbx_post_classes');


// Generates time- and date-based classes relative to GMT (UTC)
function sbx_date_classes($t, &$classes, $p) {
	$t = $t + ( get_option('gmt_offset') * 3600 );
	$classes[] = $p . 'y' . gmdate( 'Y', $t ); // Year
	$classes[] = $p . 'm' . gmdate( 'm', $t ); // Month
	$classes[] = $p . 'd' . gmdate( 'd', $t ); // Day
	$classes[] = $p . 'h' . gmdate( 'H', $t ); // Hour
}


////////////////////////////////////////////////// Items To Hook into Header //////////////////////////////////////////////////

// Header Wrap
function sbx_header_wrap() {
	if ( !did_action( 'sb_header') )
		echo '<div id="header_wrap">'."\n";
	else
		echo '</div><!-- #header_wrap -->'."\n";
}
add_action( 'before_header', 'sbx_header_wrap', 999 );
add_action( 'after_header', 'sbx_header_wrap', 9 );

/**
 * Filter the site title to be more dynamic.
 *
 */
function sbx_default_title( $title, $sep, $seplocation) {
	$site_name = get_bloginfo('name');
	$sep = ' | ';

	if ( is_home() || is_front_page() ) { $title = get_bloginfo('description'); }
	elseif ( is_singular() || is_page() ) { $title = single_post_title( '', false ); }
	elseif ( is_search() ) { $title = sprintf( __('Search Results for: %s', 'startbox'), esc_html(stripslashes(get_search_query())) ); }
    elseif ( is_category() ) { $title = sprintf( __('Category Archives: %s', 'startbox'), single_cat_title( '', false )); }
    elseif ( is_tag() ) { $title = sprintf( __('Tag Archives: %s', 'startbox'), sbx_tag_query() ); }
	elseif ( is_404() ) { $title = __( 'Not Found', 'startbox' ); }
	else { $title = get_bloginfo('description'); }

	// Appends current page number (if on page 2 or greater)
    if ( get_query_var('paged') ) { $title .= $sep . sprintf( __( 'Page %s', 'startbox' ), get_query_var('paged') ); }

	if ( is_home() || is_front_page() ) {
		$title = array(
			'site_name' => $site_name,
			'separator' => $sep,
			'title'     => $title
		);
	} else {
		$title = array(
			'title'     => $title,
			'separator' => $sep,
			'site_name' => $site_name
		);
	}

    // Filters should return an array
    $title = apply_filters('sb_doctitle', $title);

	if( is_array( $title ) )
		$title = implode('', $title);

	return $title;
}
add_filter( 'wp_title', 'sbx_default_title', 9, 3 );

// Filter the RSS title to return nothing, otherwise RSS shows dupilicate title
add_filter( 'wp_title_rss', create_function( '$a', 'return "";' ) );

// Insert #top anchor at beginning of page
function sbx_topofpage() {
	echo '<a name="top"></a>'."\n";
}
add_action( 'before', 'sbx_topofpage', 1);

// Insert skip-to-content link for screen reader users
function sb_skip_to_content() {
	echo '<a href="#content" title="Skip to content" class="skip-to-content">' . __( 'Skip to content', 'startbox' ) . '</a>'."\n";
}

// Insert Yoast Breadcrumbs if Active
function sbx_breadcrumb_output() {
	if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb('<div id="yoastbreadcrumb">','</div>'); }
}
add_action( 'before_content', 'sbx_breadcrumb_output', 15 );


////////////////////////////////////////////////// Items To Hook into home page //////////////////////////////////////////////////

// Add additional hooks to the front page
function sbx_front_page_hooks() {
	// Only include these hooks on the front page,
	// and when NOT using a custom template
	if ( is_front_page() && ! is_page_template() ) {
		do_action( 'before_featured' );
		do_action( 'featured' );
		do_action( 'after_featured' );
	}
}
add_action( 'before_content', 'sbx_front_page_hooks' );

// Add a featured widget area to sb_featrued on the home page
function sbx_home_featured_sidebar() {
	sbx_do_sidebar( 'featured_aside', 'home_featured', 'featured-aside' );
}
add_action( 'featured','sbx_home_featured_sidebar');

////////////////////////////////////////////////// Items To Hook into content areas //////////////////////////////////////////////////

// Filter the page title based on the template (credit: Ian Stewart)
function sbx_default_page_title() {
	global $post;
	$container = apply_filters( 'sb_page_title_container', 'h1' );

	$content = '<' . $container . ' class="page-title">';
	if (is_attachment()) {
		$content .= '<a href="';
		$content .= get_permalink($post->post_parent);
		$content .= '" rev="attachment"><span class="meta-nav">&laquo; </span>';
		$content .= get_the_title($post->post_parent);
		$content .= '</a>';
	} elseif ( is_singular() ) {
		$content .= get_the_title();
	} elseif (is_author()) {
		$content .= '<span>';
		$content .= __('Author Archives: ', 'startbox');
		$content .= '</span> ';
		$content .= get_the_author();
	} elseif (is_category()) {
		$content .= '<span>';
		$content .= __('Category Archives:', 'startbox');
		$content .= '</span> ';
		$content .= single_cat_title('', FALSE);
	} elseif (is_search()) {
		$content .= '<span>';
		$content .= __('Search Results for:', 'startbox');
		$content .= '</span> <span id="search-terms">';
		$content .= esc_html(stripslashes($_GET['s']), true);
		$content .= '</span>';
	} elseif (is_tag()) {
		$content .= '<span>' . __('Tag Archives:', 'startbox') . '</span> ' . sbx_tag_query();
	} elseif (is_tax()) {
		$term = get_term_by('slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$content .= '<span>' . __('Archives:', 'startbox') . '</span> ' . $term->name;
	} elseif (is_day()) {
		$content .= sprintf(__('<span>Daily Archives:</span> %s', 'startbox'), get_the_time(get_option('date_format')));
	} elseif (is_month()) {
		$content .= sprintf(__('<span>Monthly Archives:</span> %s', 'startbox'), get_the_time('F Y'));
	} elseif (is_year()) {
		$content .= sprintf(__('<span>Yearly Archives:</span> %s', 'startbox'), get_the_time('Y'));
	} elseif (is_404()) {
		$content .= __('404 - File Not Found', 'startbox');
	} elseif (is_post_type_archive()) {
		$content .= '<span>' . __('Content Archives:', 'startbox') . '</span> ' . post_type_archive_title( '', false );
	} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
		$content .= __('Blog Archives', 'startbox');
	}
	$content .= '</' . $container . '>';
	$content .= "\n";

	echo apply_filters( 'sbx_default_page_title', $content, $container, $post );
}
add_action( 'page_title', 'sbx_default_page_title' );

// Hook archive meta after page title for archive pages
function sbx_archive_meta() {
	if ( ( is_category() || is_tag() || is_tax() ) && term_description() != '' ) {
		$content = '<div class="archive-meta">';
		$content .= apply_filters( 'sbx_archive_meta', term_description() );
		$content .= '</div>';
		echo $content;
	}
}
add_action( 'page_title', 'sbx_archive_meta' );

// Add content filters for the description/meta content
add_filter( 'archive_meta', 'wptexturize' );
add_filter( 'archive_meta', 'convert_smilies' );
add_filter( 'archive_meta', 'convert_chars' );
add_filter( 'archive_meta', 'wpautop' );

// Default 404 Page
function sbx_404_content() {
	echo '<p>' . __('Sorry, but we were unable to find what you were looking for. Try searching or browsing our content below.', 'startbox' ) . '</p>';
	get_template_part( 'searchform' );
	echo '<br/>';
	sb_sitemap();
}
add_action( '404', 'sbx_404_content' );

// Dynamically create hook for the very first post in a loop
function sbx_before_first_post() { global $firstpost; if ( !isset( $firstpost ) ) { do_action( 'before_first_post' ); } } // Just before the post
function sbx_after_first_post() { global $firstpost; if ( !isset( $firstpost ) ) { do_action( 'after_first_post' ); $firstpost = 1; } } // Just after the post
add_action( 'before_post', 'sbx_before_first_post' );
add_action( 'after_post', 'sbx_after_first_post' );

////////////////////////////////////////////////// Items To Hook into Footer //////////////////////////////////////////////////

// Include our footer widgets
function sbx_footer_widgets() {
	get_sidebar('footer');
}
add_action( 'footer_widgets', 'sbx_footer_widgets' );

// Auto-hide the address bar in mobile Safari (iPhone)
function sbx_iphone() { echo '<script type="text/javascript">window.scrollTo(0, 1);</script>'; }
add_action( 'after','sbx_iphone');

// Add left/right footer hooks
function sbx_footer_left_right() {
	if ( has_action( 'sb_footer_left' ) ) { echo '<div id="footer_left" class="left">'; do_action( 'footer_left' ); echo '</div><!-- #footer_left -->'; }
	if ( has_action( 'sb_footer_right' ) ) { echo '<div id="footer_right" class="right">'; do_action( 'footer_right' ); echo '</div><!-- #footer_right -->'; }
}
add_action( 'footer', 'sbx_footer_left_right', 15 );