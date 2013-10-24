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
require_once( get_template_directory() . '/sbx/startbox.php' );

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
				'id'          => 'header_widget_area',
				'name'        => 'Header Widget Area',
				'description' => __( 'Appears to the left of the logo area.', 'startbox' ),
				'class'       => 'header-widget-area',
				'editable'    => 1
				),
			array(
				'id'          => 'primary_widget_area',
				'name'        => 'Primary Widget Area',
				'description' => __( 'This is the primary widget area when using two- or three-column layouts.', 'startbox' ),
				'class'       => 'primary-widget-area',
				'editable'    => 1
				),
			array(
				'id'          => 'secondary_widget_area',
				'name'        => 'Secondary Widget Area',
				'description' => __( 'This is the secondary widget area for three-column layouts.', 'startbox' ),
				'class'       => 'secondary-widget-area',
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_1',
				'name'        => 'Footer - Left Widget Area',
				'description' => __( 'Appears on the left side of the footer.', 'startbox' ),
				'class'       => 'footer-widget-area-1',
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_2',
				'name'        => 'Footer - Center Widget Area',
				'description' => __( 'Appears in the center of the footer.', 'startbox' ),
				'class'       => 'footer-widget-area-2',
				'editable'    => 1
				),
			array(
				'id'          => 'footer_widget_area_3',
				'name'        => 'Footer - Right Widget Area',
				'description' => __( 'Appears on the right side of the footer.', 'startbox' ),
				'class'       => 'footer-widget-area-3',
				'editable'    => 1
				)
		)
	);
	add_theme_support( 'sb-custom-sidebars' );
	add_theme_support( 'sb-updates' );

	register_nav_menu( 'main-navigation',__( 'Main Navigation' ) );

	// Custom Post Editor Styles
    add_editor_style( 'editor-style.css' );

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
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 656; /* pixels */


/**
 * Add Google Font
 */
function sbx_google_webfonts() {
	$protocol = is_ssl() ? 'https' : 'http';
	$query_args = array(
		'family' => 'Open+Sans:400italic,700italic,400,700',
		'subset' => 'latin,latin-ext',
	);

	wp_enqueue_style( 'google-webfonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'sbx_google_webfonts' );


/**
 * Setup custom post types (Singular, Plural, Key, Slug, Menu Position)
 */
function sbx_post_types() {

	sbx_post_type( array( 'Books', 'Book', 'book', 'book' ), array( 'menu_position' => '1' ) );

}
add_action( 'init', 'sbx_post_types' );

/**
 * Setup taxonomies (Singlular, Plural, Key, Slug, Parent Key)
 */
function sbx_taxonomies() {

	sbx_taxonomy( 'Tag', 'Book Tags', 'book_tags', 'book-tags', array( 'book' ) );

}
add_action( 'init', 'sbx_taxonomies' );

/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/