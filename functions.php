<?php

/**
 * StartBox functions and definitions
 *
 * Sets up the theme and provides includes some default scripts
 *
 * For help with StartBox, visit http://docs.wpstartbox.com
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 * For more information about Child Themes see http://codex.wordpress.org/Theme_Development and http://codex.wordpress.org/Child_Themes
 *
 * @package StartBox
 * @link http://www.wpstartbox.com
 */

// Initialize StartBox, but only if a child theme hasn't already
require_once( get_template_directory() . '/startbox/startbox.php' );

// Setup the environment and register support for various WP features.
function startbox_setup_theme() {

	// StartBox Core Features
	add_theme_support( 'sb-breadcrumbs' );
	add_theme_support( 'sb-customizer' );
	add_theme_support( 'sb-layouts' );
	add_theme_support( 'sb-shortcodes' );
	add_theme_support( 'sb-sidebars' );
	add_theme_support( 'sb-updates' );

	// Custom Post Editor Styles
	add_editor_style( array(
		'/includes/styles/typography.css',
		'/includes/styles/editor.css'
	) );

	// Post Format support
	add_theme_support(
		'post-formats',
		array( 'aside', 'gallery', 'image', 'link', 'quote', 'video' )
	);

	// Post Thumbnail support
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 200, 200, true );

}
add_action( 'after_setup_theme', 'startbox_setup_theme' );

/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/