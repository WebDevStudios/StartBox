<?php
/**
 * SBX Common Actions and Filters
 *
 * @package SBX
 * @subpackage Extensions
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Add content filters for the description/meta content
add_filter( 'archive_meta', 'wptexturize' );
add_filter( 'archive_meta', 'convert_smilies' );
add_filter( 'archive_meta', 'convert_chars' );
add_filter( 'archive_meta', 'wpautop' );

// Enable Shortcodes in widget areas
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Add helpful CSS classes to the <body> tag
 * on singular content views.
 *
 * @since  1.0.0
 *
 * @param  array $classes Body classes.
 * @return array          Modified body classes.
 */
function sbx_body_classes( $classes = array() ) {
	global $wp_query;

	// Bail here if not viewing a singular object
	if ( ! is_singular() )
		return $classes;

	// Add classes for the month, day, and hour of publication
	$classes = sbx_date_classes( mysql2date( 'U', $wp_query->post->post_date ), $classes, 's-' );

	// Add class for the content's slug
	$classes[] = 'slug-' . $wp_query->post->post_name;

	// For posts, add a class for each category
	if ( is_single() && $cats = get_the_category() ) {
		foreach ( $cats as $cat ) {
			$classes[] = 's-category-' . $cat->slug;
		}
	}

	// return the $classes array
	return $classes;

}
add_filter( 'body_class','sbx_body_classes' );

/**
 * Add helpful classes to the post container.
 *
 * @since  1.0.0
 *
 * @param  array  $classes Post classes.
 * @return array           Modified post classes.
 */
function sbx_post_classes( $classes = array() ) {

	// Add classes for the month, day, and hour of publication
	$classes = sbx_date_classes( get_the_time( 'U' ), $classes, 's-' );

	// Add class for the post author
	$classes[] = 'author-' . sanitize_title_with_dashes( strtolower( get_the_author() ) );

	return $classes;
}
add_filter( 'post_class', 'sbx_post_classes' );

/**
 * Filter wp_title to provide a more well-rounded site title.
 *
 * @since  1.0.0
 *
 * @param  string $title Default title for current view.
 * @param  string $sep   Optional separator.
 * @return string        Filtered title.
 */
function sbx_default_site_title( $title, $sep ) {
	global $page, $paged;

	// Get the site name
	$site_name = get_bloginfo('name');

	// Return only the content title for feeds
	if ( is_feed() ) {
		return $title;
	}

	// Get the most relevant title based on content being viewed
	if ( is_singular() ) {
		$title = single_post_title( '', false );
	} elseif ( is_category() ) {
		$title = sprintf( __( 'Category Archives: %s', 'sbx' ), single_term_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag Archives: %s', 'sbx' ), single_term_title( '', false ) );
	} elseif ( is_tax() ) {
		$taxonomy = get_query_var( 'taxonomy' );
		$title = sprintf( __( '%1$s Archives: %2$s', 'sbx' ), $taxonomy->singular_label, single_term_title( '', false ) );
	} elseif ( is_author() ) {
		the_post();
		$author = get_the_author();
		rewind_posts();
		$title = sprintf( __( 'Author Archives: %s', 'sbx' ), esc_html( $author ) );
	} elseif ( is_search() ) {
		$title = sprintf( __( 'Search Results for: %s', 'sbx' ), esc_html( stripslashes( get_search_query() ) ) );
	} elseif ( is_404() ) {
		$title = __( 'Not Found', 'sbx' );
	} else {
		$title = get_bloginfo( 'description' );
	}

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		$title .= " $sep " . sprintf( __( 'Page %s', 'sbx' ), max( $paged, $page ) );
	}

	// Handle the home/blog pages differently than everything else
	if ( is_home() || is_front_page() ) {
		$new_title = array(
			'site_name' => $site_name,
			'separator' => $sep,
			'title'     => $title
		);
	} else {
		$new_title = array(
			'title'     => $title,
			'separator' => $sep,
			'site_name' => $site_name
		);
	}

	return implode( ' ', $new_title );

}
add_filter( 'wp_title', 'sbx_default_site_title', 9, 2 );

/**
 * Add a "Page Template" column to the 'Page' dashboard.
 *
 * This column shows the page template in use by the given page.
 *
 * Credit: @tommcfarlin
 * @link http://tommcfarlin.com/view-page-templates/
 *
 * @since  1.0.0
 * @param  array $page_columns Table columns.
 * @return array               Modified table columns.
 */
function sbx_add_template_column( $page_columns ) {
	$page_columns['template'] = __( 'Page Template', 'sbx' );
	return $page_columns;
}
add_filter( 'manage_edit-page_columns', 'sbx_add_template_column' );

/**
 * Output friendly name of template used by page.
 *
 * Sanely falls back to "Default" if no template is in use.
 *
 * Credit: @tommcfarlin
 * @link http://tommcfarlin.com/view-page-templates/
 *
 * @since 1.0.0
 * @param array $page_columns Column being rendered.
 */
function sbx_add_template_data( $column_name ) {
	global $post;

	// Only output data for the custom "template" column
	if ( 'template' !== $column_name ) {
		return;
	}

	// First, the get name of the template
	$template_name = get_post_meta( $post->ID, '_wp_page_template', true );
	$parent_theme_template = get_template_directory() . '/' . $template_name;
	$child_theme_tempalte = get_stylesheet_directory() . '/' . $template_name;

	// Output "Default" if template not set or non-existant
	if ( 0 == strlen( trim( $template_name ) ) || ( ! file_exists( $parent_theme_template ) || ! file_exists( $child_theme_template ) ) ) {
		_e( 'Default', 'sbx' );

	// Otherwise, output the friendly template name
	} elseif ( file_exists( $parent_theme_template ) ) {
		echo get_file_description( $parent_theme_template );
	} elseif ( file_exists( $child_theme_template ) ) {
		echo get_file_description( $child_theme_template );
	}

}
add_action( 'manage_page_posts_custom_column', 'sbx_add_template_data' );

/**
 * Auto-hide the address bar in mobile Safari (iPhone)
 *
 * @since  1.0.0
 */
function sbx_hide_iphone_addressbar() {
	echo '<script type="text/javascript">window.scrollTo(0, 1);</script>';
}
add_action( 'wp_footer','sbx_hide_iphone_addressbar' );

/**
 * Dynamically add a "before_first_post" hook to first post in a loop.
 *
 * @since 1.0.0
 */
function sbx_before_first_post() {
	if ( ! did_action('before_first_post') ) {
		do_action( 'before_first_post' );
	}
}
add_action( 'before_post', 'sbx_before_first_post' );

/**
 * Dynamically add a "before_first_post" hook to first post in a loop.
 *
 * @since 1.0.0
 */
function sbx_after_first_post() {
	if ( ! did_action('after_first_post') ) {
		do_action( 'after_first_post' );
	}
}
add_action( 'after_post', 'sbx_after_first_post' );

/**
 * Forever eliminate "Startbox" from the planet
 * (or at least the little bit we can influence).
 *
 * Violating our coding standards for a good function name.
 * Adapted from capital_P_dangit().
 *
 * @since 1.0.0
 */
function capital_B_dangit( $text ) {

	// Simple replacement for titles
	if ( 'the_title' === current_filter() )
		return str_replace( 'Startbox', 'StartBox', $text );

	// Still here? Use the more judicious replacement
	static $dblq = false;
	if ( false === $dblq ) {
		$dblq = _x( '&#8220;', 'opening curly quote' );
	}

	return str_replace(
		array( ' Startbox', '&#8216;Startbox', $dblq . 'Startbox', '>Startbox', '(Startbox' ),
		array( ' StartBox', '&#8216;StartBox', $dblq . 'StartBox', '>StartBox', '(StartBox' ),
		$text
	);

}
add_filter( 'the_content', 'capital_B_dangit', 11 );
add_filter( 'the_title', 'capital_B_dangit', 11 );
add_filter( 'comment_text', 'capital_B_dangit', 31 );

/**
 * Wrap an embedded video with a container for simpler styling.
 *
 * @since  1.0.0
 *
 * @param  string $output HTML Markup.
 * @param  string $url    oEmbed URL.
 * @return string         Potentially modified HTML markup.
 */
function sbx_oembed_video_wrapper( $output, $url ) {

	// Setup list of providers to filter
	$video_providers = array(
		'youtu\.?be',
		'vimeo',
		'hulu',
		'viddler',
		'wordpress\.tv',
		'funnyordie',
		'slideshare',
		'dailymotion',
	);

	// If oembed is from a provider, wrap it
	if ( preg_match( '/' . implode( '|', $video_providers ) . '/', $url ) ) {
		$output = '<div class="video-wrapper">' . $output . '</div>';
	}

	// Return output
	return $output;

}
add_filter( 'embed_oembed_html', 'sbx_oembed_video_wrapper', 10, 2 );
