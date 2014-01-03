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

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 656; /* pixels */

// Setup the environment and register support for various WP features.
function sb_setup_theme() {

	// Post Format support
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'link', 'quote', 'video' ) );

	// Post Thumbnail support
	add_theme_support( 'post-thumbnails' );

	// Include RSS feeds in <head>
	add_theme_support( 'automatic-feed-links' );

	// Register main nanivation
	register_nav_menu( 'main-navigation',__( 'Primary Navigation' ) );

	// Custom post editor styles
	add_editor_style( 'editor-style.css' );

	// Load SBX bootstrap
	require_once( get_template_directory() . '/sbx/sbx.php' );

	// Include relevant SBX Features
	add_theme_support( 'sbx-updates' );
	add_theme_support( 'sbx-customizer' );
	add_theme_support( 'sbx-layouts' );
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

	// Include customizer controls
	require_once( get_template_directory() . '/lib/customizer.php' );

}
add_action( 'after_setup_theme', 'sb_setup_theme' );

/**
 * In his grace, God has given us different gifts for doing certain things well.
 * So if God has given you the ability to prophesy, speak out with as much faith as
 * God has given you. If your gift is serving others, serve them well. If you are
 * a  teacher, teach well. If your gift is to encourage others, be encouraging. If
 * it is giving, give generously. If God has given you leadership ability, take the
 * responsibility seriously. And if you have a gift for showing kindness to others,
 * do it gladly. - Romans 12:6-8 (http://bit.ly/rom12nlt)
*/
