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
require_once( get_template_directory() . '/sbx/sbx.php' );

// Setup the environment and register support for various WP features.
function startbox_setup_theme() {

	// StartBox Core Features
	add_theme_support( 'sb-breadcrumbs' );
	add_theme_support( 'sb-customizer' );
	add_theme_support( 'sb-shortcodes' );
	add_theme_support( 'sb-options' );
	add_theme_support( 'sb-sidebars',
		array(
			array(
				'id'          => 'primary_widget_area',
				'name'        => __( 'Primary Widget Area', 'startbox' ),
				'description' => __( 'This is the primary widget area when using two- or three-column layouts.', 'startbox' ),
				),
			array(
				'id'          => 'secondary_widget_area',
				'name'        => __( 'Secondary Widget Area', 'startbox' ),
				'description' => __( 'This is the secondary widget area for three-column layouts.', 'startbox' ),
				),
			array(
				'id'          => 'header_widget_area',
				'name'        => __( 'Header Widget Area', 'startbox' ),
				'description' => __( 'Appears to the right of the logo area.', 'startbox' ),
				),
			array(
				'id'          => 'footer_widget_area_1',
				'name'        => __( 'Footer - Left Widget Area', 'startbox' ),
				'description' => __( 'Appears on the left side of the footer.', 'startbox' ),
				),
			array(
				'id'          => 'footer_widget_area_2',
				'name'        => __( 'Footer - Center Widget Area', 'startbox' ),
				'description' => __( 'Appears in the center of the footer.', 'startbox' ),
				),
			array(
				'id'          => 'footer_widget_area_3',
				'name'        => __( 'Footer - Right Widget Area', 'startbox' ),
				'description' => __( 'Appears on the right side of the footer.', 'startbox' ),
				),
		)
	);
	add_theme_support( 'sb-custom-sidebars' );
	add_theme_support( 'sb-updates' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-header' );
	add_theme_support( 'custom-background' );

	register_nav_menu( 'main-navigation',__( 'Primary Navigation' ) );

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

	add_filter( 'sb_customizer_settings', 'startbox_customizer_settings' );

}
add_action( 'after_setup_theme', 'startbox_setup_theme' );

/**
 * Hook into WordPress theme customizer
 * @link( /wp-admin/customize.php, link)
 */
function startbox_customizer_settings( $sections = array() ) {

	// Defines $prefix for setting IDs. Optional.
	$prefix = 'sb_';

	// Defines theme cusotmizer sections and settings
	$sections['branding_settings'] = array(
		'title'       => 'Branding Settings',
		'description' => 'Upload a favicon, touch icon (iOS), and a tile icon (Windows 8).',
		'priority'    => 1,
		'settings'    => array(
			array(
				'id'       => $prefix . 'favicon',
				'label'    => 'Favicon (32x32 .ico)',
				'type'     => 'image',
				'priority' => 10,
				),
			array(
				'id'       => $prefix . 'touch_icon',
				'label'    => 'Touch Icon (152x152 .png)',
				'type'     => 'image',
				'priority' => 20,
				),
			array(
				'id'       => $prefix . 'tile_icon',
				'label'    => 'Tile Icon (144x144 .png)',
				'type'     => 'image',
				'priority' => 30,
				),
			array(
				'id'       => $prefix . 'tile_bg',
				'label'    => 'Tile Icon Background',
				'type'     => 'color',
				'default'  => '#fff',
				'priority' => 40,
				),
		)
	);

	// Defines theme cusotmizer sections and settings
	$sections['content_settings'] = array(
		'title'       => 'Post Content Settings',
		'description' => 'Customize the post content area.',
		'priority'    => 200,
		'settings'    => array(
			array(
				'id'           => $prefix . 'post_header_meta',
				'label'        => 'Post Header Meta',
				'type'         => 'textarea',
				'default'      => 'Published by [author] on [date] at [time] in [categories]',
				'priority'     => 10,
				'js_callback'  => 'sbx_change_text',
				'css_selector' => '.entry-header .entry-meta',
				),
			array(
				'id'           => $prefix . 'post_footer_meta',
				'label'        => 'Post Footer Meta',
				'type'         => 'textarea',
				'default'      => 'Categories: [categories], Tags: [tags] [edit]',
				'priority'     => 20,
				'js_callback'  => 'sbx_change_text',
				'css_selector' => '.entry-footer .entry-meta',
				),
			array(
				'id'           => $prefix . 'show_author_box',
				'label'        => 'Display Author Box',
				'type'         => 'checkbox',
				'priority'     => 30,
				'js_callback'  => '',
				'css_selector' => '',
				),
		)
	);

	// Defines theme cusotmizer sections and settings
	$sections['footer_settings'] = array(
		'title'       => 'Footer Settings',
		'description' => 'Customize the credits area of the Footer.',
		'priority'    => 300,
		'settings'    => array(
			array(
				'id'           => $prefix . 'rtt_link',
				'label'        => 'Return to Top Link',
				'type'         => 'checkbox',
				'priority'     => 10,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'credits',
				'label'        => 'Site Credits',
				'type'         => 'textarea',
				'default'      => '[copyright year="2013"] [site_link]. Proudly powered by [WordPress] and [StartBox].',
				'priority'     => 30,
				'js_callback'  => 'sbx_change_text',
				'css_selector' => '.site-info .credits',
				),
		)
	);

	// Defines theme cusotmizer sections and settings
	$sections['example_settings'] = array(
		'title'       => 'Example Settings',
		'description' => 'Section description...',
		'priority'    => 999,
		'settings'    => array(
			array(
				'id'           => $prefix . 'text',
				'label'        => 'Text',
				'type'         => 'text',
				'default'      => 'Default content',
				'priority'     => 10,
				'js_callback'  => 'sbx_change_text',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'textarea',
				'label'        => 'Textarea',
				'type'         => 'textarea',
				'default'      => 'Some sample content...',
				'priority'     => 20,
				'js_callback'  => 'sbx_change_text',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'checkbox',
				'label'        => 'Checkbox',
				'type'         => 'checkbox',
				'priority'     => 30,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'radio_buttons',
				'label'        => 'Radio Buttons',
				'type'         => 'radio',
				'default'      => 'left',
				'choices'      => array(
					'left'   => 'Left',
					'right'  => 'Right',
					'center' => 'Center',
					),
				'priority'     => 40,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'          => $prefix . 'select_list',
				'label'       => 'Select list',
				'type'        => 'select',
				'default'     => 'two',
				'choices'     => array(
					'one'   => 'Option 1',
					'two'   => 'Option 2',
					'three' => 'Option 3',
					),
				'priority'     => 50,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'page',
				'label'        => 'Page',
				'type'         => 'dropdown-pages',
				'priority'     => 60,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'color',
				'label'        => 'Color',
				'type'         => 'color',
				'default'      => '#f70',
				'priority'     => 70,
				'js_callback'  => 'sbx_change_text_color',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'upload',
				'label'        => 'Upload',
				'type'         => 'upload',
				'priority'     => 80,
				'js_callback'  => '',
				'css_selector' => '',
				),
			array(
				'id'           => $prefix . 'image',
				'label'        => 'Image',
				'type'         => 'image',
				'priority'     => 90,
				'js_callback'  => '',
				'css_selector' => '',
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

	wp_enqueue_style( 'open-sans', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );

}
add_action( 'wp_enqueue_scripts', 'sbx_google_webfonts' );


/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/