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
	add_theme_support(
		'sb-sidebars',
		array(
			array(
				'id'          => 'primary',
				'name'        => 'Primary Sidebar',
				'description' => __( 'This is the primary sidebar when using two- or three-column layouts.', 'startbox' ),
				'editable'    => 1
				),
			array(
				'id'          => 'secondary',
				'name'        => 'Secondary Sidebar',
				'description' => __( 'This is the secondary sidebar for three-column layouts.', 'startbox' ),
				'editable'    => 1
				),
			array(
				'id'          => 'home_featured',
				'name'        => 'Home Featured',
				'description' => __( 'These widgets will appear above the content on the homepage.', 'startbox' ),
				'editable'    => 0
				),
			array(
				'id'          => 'footer_widget_area_1',
				'name'        => 'Footer Aside 1',
				'description' => __( 'This is the first footer column. Use this before using any other footer columns.', 'startbox' ),
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_2',
				'name'        => 'Footer Aside 2',
				'description' => __( 'This is the second footer column. Only use this after using Footer Aside 1.', 'startbox' ),
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_3',
				'name'        => 'Footer Aside 3',
				'description' => __( 'This is the third footer column. Only use this after using Footer Aside 2.', 'startbox' ),
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_4',
				'name'        => 'Footer Aside 4',
				'description' => __( 'This is the last footer column. Only use this after using all other columns.', 'startbox' ),
				'editable'    => 1
				),
		)
	);
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