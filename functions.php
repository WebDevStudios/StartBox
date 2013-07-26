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
	add_theme_support( 'sb-custom-sidebars' );
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

	add_filter( 'sb_customizer_settings', 'sb_sample_customizer_settings' );

}
add_action( 'after_setup_theme', 'startbox_setup_theme' );

function sb_sample_customizer_settings( $sections = array() ) {

	// Defines $prefix for setting IDs. Optional.
	$prefix = 'sb_';

	$sections['title_tagline']['settings'][] = array(
		'id'      => $prefix . 'header_text',
		'label'   => 'Additional Header Text',
		'type'    => 'text',
		'default' => 'Some content'
	);

	// Defines theme cusotmizer sections and settings
	$sections['example_settings'] = array(
		'title'       => 'Example Settings',
		'description' => 'Section description...',
		'priority'    => 200,
		'settings'    => array(
			array(
				'id'      => $prefix . 'text',
				'label'   => 'Text',
				'type'    => 'text',
				'default' => 'Default content',
				'priority' => 10
			),
			array(
				'id'      => $prefix . 'textarea',
				'label'   => 'Textarea',
				'type'    => 'textarea',
				'default' => 'Some sample content...',
				'priority' => 20
			),
			array(
				'id'    => $prefix . 'checkbox',
				'label' => 'Checkbox',
				'type'  => 'checkbox',
				'priority' => 30
			),
			array(
				'id'      => $prefix . 'radio_buttons',
				'label'   => 'Radio Buttons',
				'type'    => 'radio',
				'default' => 'left',
				'choices' => array(
					'left'   => 'Left',
					'right'  => 'Right',
					'center' => 'Center',
				),
				'priority' => 40
			),
			array(
				'id'      => $prefix . 'select_list',
				'label'   => 'Select list',
				'type'    => 'select',
				'default' => 'two',
				'choices' => array(
					'one'   => 'Option 1',
					'two'   => 'Option 2',
					'three' => 'Option 3',
				),
				'priority' => 50
			),
			array(
				'id'      => $prefix . 'page',
				'label'   => 'Page',
				'type'    => 'dropdown-pages',
				'priority' => 60
			),
			array(
				'id'      => $prefix . 'color',
				'label'   => 'Color',
				'type'    => 'color',
				'default' => '#f70',
				'priority' => 70
			),
			array(
				'id'      => $prefix . 'upload',
				'label'   => 'Upload',
				'type'    => 'upload',
				'priority' => 80
			),
			array(
				'id'      => $prefix . 'image',
				'label'   => 'Image',
				'type'    => 'image',
				'priority' => 90
			),
		)
	);

	return $sections;
}

/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/