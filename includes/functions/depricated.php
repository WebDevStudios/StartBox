<?php
/**
 * StartBox Deprecated Functions
 *
 * These are functions that have either been replaced or removed.
 *
 * @package StartBox
 * @subpackage Functions
 */

// Legacy action hook helper functions
function sb_title() { _deprecated_function( __FUNCTION__, '2.6.0', 'wp_title' ); wp_title(); } // The site title
function sb_before() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before\' )' ); do_action('sb_before'); } // the very first thing inside <body>
function sb_before_header() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_header\' )' ); do_action('sb_before_header'); } // inside div#wrap, before div#header
function sb_header() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_header\' )' ); do_action('sb_header'); } // inside div#header, before any content
function sb_after_header() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_header\' )' ); do_action('sb_after_header'); } // inside div#wrap, after div#header
function sb_before_container() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_container\' )' ); do_action('sb_before_container'); } // inside div#container_wrap, before div#container
function sb_before_featured() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_featured\' )' ); do_action('sb_before_featured'); } // Located just after sb_before_content
function sb_featured() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_featured\' )' ); do_action('sb_featured'); } // Located just after sb_before_featured
function sb_after_featured() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_featured\' )' ); do_action('sb_after_featured'); } // Located just after sb_featured
function sb_home() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_home\' )' ); do_action('sb_home'); } // Located just after sb_after_featured
function sb_before_content() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_content\' )' ); do_action('sb_before_content'); } // Just before the content
function sb_page_title() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_page_title\' )' ); do_action('sb_page_title'); } // The Page Title, appears immediately after sb_before_content
function sb_after_content() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_content\' )' ); do_action('sb_after_content');} // Just after the content
function sb_404() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_404\' )' ); do_action('sb_404'); } // Inside div.post, only on 404 page
function sb_before_post() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_post\' )' ); do_action('sb_before_post'); } // Before div.post
function sb_before_post_content() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_post_content\' )' ); do_action('sb_before_post_content'); } // Inside div.post, after .entry-header, before .entry-content
function sb_post_header() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_post_header\' )' ); do_action('sb_post_header' ); } // Inside div.entry-meta
function sb_post_footer() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_post_footer\' )' ); do_action('sb_post_footer' ); } // Inside div.entry-footer
function sb_after_post_content() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_post_content\' )' ); do_action('sb_after_post_content'); } // Inside div.post, after .entry-content, before .entry-footer
function sb_after_post() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_post\' )' ); do_action('sb_after_post'); } // Inside div.post, after .entry-content, before .entry-footer
function sb_between_primary_and_secondary_widgets() { _deprecated_function( __FUNCTION__, '2.5', 'do_action( \'sb_after_primary_aside_widgets\' )' ); do_action('sb_between_primary_and_secondary_widgets');}
function sb_no_widgets() { _deprecated_function( __FUNCTION__, '2.6.0', 'specific widget location hooks' ); }
function sb_before_footer_widgets() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_footer_widgets\' )' ); do_action('sb_before_footer_widgets'); } // inside div#footer, before div#footer_sidebar
function sb_after_footer_widgets() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_footer_widgets\' )' ); do_action('sb_after_footer_widgets'); } // inside div#footer, after div#footer_sidebar
function sb_between_footer_widgets() { _deprecated_function( __FUNCTION__, '2.6.0', 'other footer widget hooks' ); }
function sb_after_container() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_container\' )' ); do_action('sb_after_container'); } // inside div#container_wrap, after div#container
function sb_between_content_and_footer() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_between_content_and_footer\' )' ); do_action('sb_between_content_and_footer'); } // after div#wrap, before div#footer_wrap
function sb_before_footer() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_before_footer\' )' ); do_action('sb_before_footer'); } // inside div#footer_wrap, before div#footer
function sb_footer() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_footer\' )' ); do_action('sb_footer'); } // inside div#footer after div#footer_sidebar
function sb_after_footer() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after_footer\' )' ); do_action('sb_after_footer'); } // inside div#footer_wrap, after div#footer
function sb_after() { _deprecated_function( __FUNCTION__, '2.6.0', 'do_action( \'sb_after\' )' ); do_action('sb_after'); } // the very last thing before </body>

// Legacy post image function
function sb_post_image_url( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.7.0', 'sb_get_post_image_url()' );
	return sb_get_post_image_url( $args );
}

/**
 * Deprecated. Registering Tag Cloud widget.
 *
 * @deprecated 2.7.2
 *
 * @see sb_load_widgets()
 */
function load_widget_sb_tagcloud_widget() {
	_deprecated_function( __FUNCTION__, '2.7.2', 'sb_load_widgets' );
}

/**
 * Deprecated. Registering Social widget.
 *
 * @deprecated 2.7.2
 *
 * @see sb_load_widgets()
 */
function sb_social_load_widget() {
	_deprecated_function( __FUNCTION__, '2.7.2', 'sb_load_widgets' );
}

/**
 * Deprecated. Register Search widget.
 *
 * @deprecated 2.7.2
 *
 * @see sb_load_widgets()
 */
function load_widget_sb_search_widget() {
	_deprecated_function( __FUNCTION__, '2.7.2', 'sb_load_widgets' );
}

/**
 * Deprecated. Register Featured Content widget.
 *
 * @deprecated 2.7.2
 *
 * @see sb_load_widgets()
 */
function load_widget_sb_featured_content() {
	_deprecated_function( __FUNCTION__, '2.7.2', 'sb_load_widgets' );
}
