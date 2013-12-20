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
	add_theme_support( 'sbx-breadcrumbs' );
	add_theme_support( 'sbx-customizer' );
	add_theme_support( 'sbx-shortcodes' );
	add_theme_support( 'sbx-options' );
	add_theme_support( 'sbx-sidebars',
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
	add_theme_support( 'sbx-custom-sidebars' );
	add_theme_support( 'sbx-updates' );
	add_theme_support( 'sbx-plugins' );
	add_theme_support( 'automatic-feed-links' );

	// Register main nanivation
	register_nav_menu( 'main-navigation',__( 'Primary Navigation' ) );

	// Custom post editor styles
    add_editor_style( 'editor-style.css' );

	// Post Format support
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'link', 'quote', 'video' ) );

	// Post Thumbnail support
	add_theme_support( 'post-thumbnails' );

	// Include customizer settings
	add_filter( 'sb_customizer_settings', 'startbox_customizer_settings' );

	// Include required plugins for this theme
	add_action( 'sbx_register_plugins', 'startbox_theme_required_plugins' );

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
		'title'       => 'Branding',
		'description' => 'Upload a favicon, touch icon (iOS), and a tile icon (Windows 8).',
		'priority'    => 1,
		'settings'    => array(
			array(
				'id'                => $prefix . 'favicon',
				'label'             => 'Favicon (32x32 .ico)',
				'type'              => 'upload',
				'priority'          => 10,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'touch_icon',
				'label'             => 'Touch Icon (152x152 .png)',
				'type'              => 'image',
				'priority'          => 20,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'tile_icon',
				'label'             => 'Tile Icon (144x144 .png)',
				'type'              => 'image',
				'priority'          => 30,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'tile_bg',
				'label'             => 'Tile Icon Background',
				'type'              => 'color',
				'default'           => '#fff',
				'priority'          => 40,
				'sanitize_callback' => 'esc_url',
				),
		)
	);
	$sections['color_settings'] = array(
		'title'       => 'Theme Colors',
		'description' => 'Customize theme colors.',
		'priority'    => 200,
		'settings'    => array(
			array(
				'id'                => $prefix . 'override_colors',
				'label'             => 'Use custom colors below',
				'type'              => 'checkbox',
				'priority'          => 10,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'primary_text_color',
				'label'             => 'Primary Text',
				'type'              => 'color',
				'default'           => '#111111',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'secondary_text_color',
				'label'             => 'Secondary Text',
				'type'              => 'color',
				'default'           => '#888888',
				'priority'          => 31,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'nav_footer_bg_color',
				'label'             => 'Nav and Footer Background',
				'type'              => 'color',
				'default'           => '#111111',
				'priority'          => 32,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'border_hover_color',
				'label'             => 'Borders, Hovers, and Highlights',
				'type'              => 'color',
				'default'           => '#999999',
				'priority'          => 33,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'url_color',
				'label'             => 'Links',
				'type'              => 'color',
				'default'           => '#777777',
				'priority'          => 34,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'content_bg_color',
				'label'             => 'Content and Sidebar Background',
				'type'              => 'color',
				'default'           => '#ffffff',
				'priority'          => 35,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'site_bg_color',
				'label'             => 'Site Background',
				'type'              => 'color',
				'default'           => '#f0f0f0',
				'priority'          => 36,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
		)
	);
	$sections['content_settings'] = array(
		'title'       => 'Post Content and Meta',
		'description' => 'Customize the post content area.',
		'priority'    => 300,
		'settings'    => array(
			array(
				'id'                => $prefix . 'post_header_meta',
				'label'             => 'Post Header Meta',
				'type'              => 'textarea',
				'default'           => 'Published by [author] on [date] at [time] in [categories]',
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.entry-header .entry-meta',
				),
			array(
				'id'                => $prefix . 'post_footer_meta',
				'label'             => 'Post Footer Meta',
				'type'              => 'textarea',
				'default'           => '[categories] [tags] [edit]',
				'priority'          => 20,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.entry-footer .entry-meta',
				),
			array(
				'id'                => $prefix . 'show_author_box',
				'label'             => 'Display Author Box',
				'type'              => 'checkbox',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
		)
	);
	$sections['footer_settings'] = array(
		'title'       => 'Footer',
		'description' => 'Customize the credits area of the Footer.',
		'priority'    => 400,
		'settings'    => array(
			array(
				'id'                => $prefix . 'rtt_link',
				'label'             => 'Return to Top Link',
				'type'              => 'checkbox',
				'priority'          => 10,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'credits',
				'label'             => 'Site Credits',
				'type'              => 'textarea',
				'default'           => '[copyright year="2013"] <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a>. Proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://wpstartbox.com">StartBox</a>.',
				'priority'          => 30,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.site-info .credits',
				),
		)
	);
	$sections['example_settings'] = array(
		'title'       => 'Example Settings',
		'description' => 'Section description...',
		'priority'    => 999,
		'settings'    => array(
			array(
				'id'                => $prefix . 'text',
				'label'             => 'Text',
				'type'              => 'text',
				'default'           => 'Default content',
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'textarea',
				'label'             => 'Textarea',
				'type'              => 'textarea',
				'default'           => 'Some sample content...',
				'priority'          => 20,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'checkbox',
				'label'             => 'Checkbox',
				'type'              => 'checkbox',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'radio_buttons',
				'label'             => 'Radio Buttons',
				'type'              => 'radio',
				'default'           => 'left',
				'choices'       => array(
					'left'      => 'Left',
					'right'     => 'Right',
					'center'    => 'Center',
					),
				'priority'          => 40,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'select_list',
				'label'             => 'Select list',
				'type'              => 'select',
				'default'           => 'two',
				'choices'     => array(
					'one'     => 'Option 1',
					'two'     => 'Option 2',
					'three'   => 'Option 3',
					),
				'priority'          => 50,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'page',
				'label'             => 'Page',
				'type'              => 'dropdown-pages',
				'priority'          => 60,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'color',
				'label'             => 'Color',
				'type'              => 'color',
				'default'           => '#f70',
				'priority'          => 70,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'upload',
				'label'             => 'Upload',
				'type'              => 'upload',
				'priority'          => 80,
				'sanitize_callback' => 'esc_url',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'image',
				'label'             => 'Image',
				'type'              => 'image',
				'priority'          => 90,
				'sanitize_callback' => 'esc_url',
				'js_callback'       => '',
				'css_selector'      => '',
				),
		)
	);

	return $sections;
}

/**
 * Registers required plugins for this theme
 *
 * Allows you to easily require or recommend
 * plugins for your WordPress themes
 *
 * @since 3.0.0
 */
function startbox_theme_required_plugins() {

	$plugins = array(

		// This is an example of how to include a plugin from the WordPress Plugin Repository
		array(
			'name' 		=> 'Custom Post Type UI',
			'slug' 		=> 'custom-post-type-ui',
			'required' 	=> false,
		),

	);

	sbx_register_theme_plugins( $plugins );

}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 656; /* pixels */


/**
 * Add Google Font
 */
function startbox_google_webfonts() {

	$protocol = is_ssl() ? 'https' : 'http';
	$query_args = array(
		'family' => 'Open+Sans:300italic,700italic,300,700',
		'subset' => 'latin,latin-ext',
	);

	wp_enqueue_style( 'open-sans', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );

}
add_action( 'wp_enqueue_scripts', 'startbox_google_webfonts' );

/**
 * Theme color override
 */
function startbox_color_override() {

	// Check for override setting
	$override = sbx_get_theme_mod( 'sb_override_colors' );

	// If enabled, print CSS
	if ( $override ) { ?>
<style type="text/css" media="screen">
	body,
	.site-header,
	.site-inner,
	.site-footer {
		background-color: <?php echo sbx_get_theme_mod( 'sb_site_bg_color' ); ?>;
	}
	body {
		color: <?php echo sbx_get_theme_mod( 'sb_primary_text_color' ); ?>;
	}
	a,
	a:active,
	a:visited {
		color:  <?php echo sbx_get_theme_mod( 'sb_url_color' ); ?>;
	}
	a:hover {
		color:  <?php echo sbx_get_theme_mod( 'sb_border_hover_color' ); ?>;
	}
	.main-navigation,
	.footer-widgets {
		background-color: <?php echo sbx_get_theme_mod( 'sb_nav_footer_bg_color' ); ?>;
	}
	.entry-meta {
		color: <?php echo sbx_get_theme_mod( 'sb_secondary_text_color' ); ?>;
		border-color:  <?php echo sbx_get_theme_mod( 'sb_border_hover_color' ); ?>;
	}
	.post,
	.site-inner .page,
	.primary-widget-area .widget {
		background-color: <?php echo sbx_get_theme_mod( 'sb_content_bg_color' ); ?>;
	}
</style>

<?php } }
add_action( 'wp_head', 'startbox_color_override', 999 );

/**
 * Conditionally add the author box after single posts.
 *
 * @since  3.0.0
 */
function sb_do_author_box() {

	// Output if theme setting warrants it
	if ( is_single() && get_theme_mod( 'sb_show_author_box' ) ) {
		sbx_author_box();
	}

}
add_action( 'entry_after', 'sb_do_author_box', 10 );

/**
 * Add additional custom body classes.
 *
 * @since  3.0.0
 *
 * @param  array $classes Body CSS classes.
 * @return array          Modified CSS classes.
 */
function sb_custom_body_classes( $classes ) {
	$classes[] = 'gutters';
	return $classes;
}
add_filter( 'body_class', 'sb_custom_body_classes' );

/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/
