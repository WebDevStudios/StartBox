<?php

/**
 * StartBox - Main class
 *
 * Loads all includes, theme constants, adds/removes filters, etc.
 *
 * @package StartBox
 * @subpackage Functions
 * @since StartBox 2.4.5
 */

class StartBox {
	
	// Initialize StartBox -- Available action: sb_init
	public function init() {
		
		// Grab and define our variables and constants
		global $blog_id;
		$sb_data = get_theme_data( get_template_directory() . '/style.css' );
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		define( 'THEME_NAME', $theme_data['Name'] );
		define( 'THEME_VERSION', $theme_data['Version'] );
		define( 'THEME_OPTIONS', 'startbox' );
		define( 'THEME_PREFIX', 'sb_' );
		define( 'SB_VERSION', $sb_data['Version'] );
		define( 'IS_MU', (isset($blog_id) && $blog_id > 0) ? true : false );
		define( 'THEME_PATH', get_stylesheet_directory() );
		define( 'THEME_URI', get_stylesheet_directory_uri() );
		define( 'SB_PATH', get_template_directory() );
		define( 'INCLUDES_PATH', TEMPLATEPATH . '/includes' );
		define( 'INCLUDES_URL', get_template_directory_uri() . '/includes' );
		define( 'ADMIN_PATH', INCLUDES_PATH . '/admin' );
		define( 'FUNCTIONS_PATH', INCLUDES_PATH . '/functions' );
		define( 'PLUGINS_PATH', INCLUDES_PATH . '/plugins' );
		define( 'SCRIPTS_URL', INCLUDES_URL . '/scripts' );
		define( 'WIDGETS_PATH', INCLUDES_PATH . '/widgets' );
		define( 'STYLES_URL', INCLUDES_URL . '/styles' );
		define( 'IMAGES_PATH', get_template_directory() . '/images' );
		define( 'IMAGES_URL', get_template_directory_uri() . '/images' );

		// Translate, if applicable
		load_theme_textdomain( 'startbox', INCLUDES_PATH . '/languages' );
		
		// Register functions, hooks, shortcodes, sidebars and comments
		require_once( FUNCTIONS_PATH . '/admin_settings.php' );	// Admin Functions
		require_once( FUNCTIONS_PATH . '/custom.php' );			// Custom Functions
		require_once( FUNCTIONS_PATH . '/depricated.php' );		// Deprecated Functions
		require_once( FUNCTIONS_PATH . '/hooks.php' );			// Hooks
		require_once( FUNCTIONS_PATH . '/shortcodes.php' );		// Shortcodes
		require_once( FUNCTIONS_PATH . '/sidebars.php' );		// Sidebars
		require_once( FUNCTIONS_PATH . '/comment_format.php' );	// Comment Structure
		
		// Register scripts and styles
		add_action( 'init', array( 'StartBox', 'register_scripts_and_styles' ), 1 );
		
		// Add child theme defaults if child theme is activated for the first time (Credit: Joel Kuczmarski)
		if ( !get_option('sb_child_install') && SB_PATH != THEME_PATH ) { add_action( 'after_setup_theme', array( 'StartBox', 'child_install' ) ); }
		
		// Included hook for other things to do during initialization
		do_action('sb_init');
		
		// Setup the environment
		add_action( 'after_setup_theme', array('StartBox', 'environment'), 5 );
		add_action( 'after_setup_theme', array('StartBox', 'sb_includes'), 15 );
		
		// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)
		
	}
	
	// Register all of the included scripts and styles
	public function register_scripts_and_styles() {
		// Register Default Scripts
		wp_register_script( 'startbox', SCRIPTS_URL . '/startbox.js', array('jquery', 'colorbox', 'md5', 'smoothScroll') );
		wp_register_script( 'pushup', SCRIPTS_URL . '/jquery-pushup/jquery.pushup.js', array('jquery'), NULL );
		wp_register_script( 'colorbox', SCRIPTS_URL . '/jquery.colorbox.min.js', array('jquery'), NULL );
		wp_register_script( 'md5', SCRIPTS_URL . '/jquery.md5.js', array('jquery') );
		wp_register_script( 'galleriffic', SCRIPTS_URL . '/jquery.galleriffic.min.js', array('jquery') );
		wp_register_script( 'galleries', SCRIPTS_URL . '/galleries.js', array('galleriffic') );
		wp_register_script( 'hovercards', ( is_ssl() ? 'https://secure' : 'http://s' ) . '.gravatar.com/js/gprofiles.js?u', array('jquery') ); // Gravatar Hovercards
		wp_register_script( 'nivoslider',  SCRIPTS_URL . '/jquery-nivo/jquery.nivo.slider.js', array('jquery'), '1.0');
		wp_register_script( 'smoothScroll', SCRIPTS_URL . '/jquery.smooth-scroll.min.js', array('jquery'), '1.4');

		// Register Default Styles
		wp_register_style( 'startbox', STYLES_URL . '/startbox.css' );
		wp_register_style( 'layouts', STYLES_URL . '/layouts.css' );
		wp_register_style( 'pushup', SCRIPTS_URL . '/jquery-pushup/pushup.css', null, null, 'screen' );
		wp_register_style( 'colorbox', STYLES_URL . '/colorbox.css', null, null, 'screen' );
		wp_register_style( 'comments', STYLES_URL . '/comments.css' );
		wp_register_style( 'reset', STYLES_URL . '/reset.css' );
		wp_register_style( 'images', STYLES_URL . '/images.css' );
		wp_register_style( 'shortcodes', STYLES_URL . '/shortcodes.css' );
		wp_register_style( 'typography', STYLES_URL . '/typography.css' );
		wp_register_style( 'print', STYLES_URL . '/print.css', null, null, 'print' );
		wp_register_style( 'nivo_slider', SCRIPTS_URL . '/jquery-nivo/css/nivo-slider.css', null, null, 'screen');
		wp_register_style( 'nivo_custom', SCRIPTS_URL . '/jquery-nivo/css/custom-nivo-slider.css', array('nivo_slider'), null, 'screen');
	}
	
	// Setup the environment and register support for various WP features.
	public function environment() {
		
		// Add theme support for various WP-specific features
		register_nav_menus( array( 'primary' => __( 'Primary Navigation', 'StartBox' ), 'secondary' => __( 'Secondary Navigation', 'StartBox' ) ) );  // Enables custom menus in the Appearance menu, since WP3.0
		add_theme_support( 'automatic-feed-links' ); // Adds default posts and comments RSS feeds, since WP3.0
		add_theme_support( 'post-thumbnails' ); // Enables post thumbnails in the write screens, since WP2.9
		set_post_thumbnail_size( 200, 200, true ); // Sets the default thumbnail size to 200x200
		add_editor_style( array(  // This sets up the content editor style to match the front-end design
			'/includes/styles/reset.css', // Global Reset
			'/includes/styles/typography.css', // Basic Typography
			'/includes/styles/editor.css' // Content-specific styles (adapted from startbox.css)
		) );
		
		// Add theme support for StartBox-specific features
		add_theme_support( 'sb-updates' ); // StartBox Updates Manager
		add_theme_support( 'sb-options' ); // StartBox Options API
		add_theme_support( 'sb-slideshows' ); // StartBox Slideshows
		add_theme_support( 'sb-sidebars' ); // StartBox Easy Sidebars
		
		// Add theme support for StartBox Layouts, redefine this list using the filter 'sb_layouts_defaults'
		$sb_default_layouts = array(
			'one-col' 			=> array( 'label' => '1 Column (no sidebars)', 			'img' => IMAGES_URL . '/layouts/one-col.png' ),
			'two-col-left' 		=> array( 'label' => '2 Columns, sidebar on left', 		'img' => IMAGES_URL . '/layouts/two-col-left.png' ),
			'two-col-right' 	=> array( 'label' => '2 Columns, sidebar on right', 	'img' => IMAGES_URL . '/layouts/two-col-right.png' ),
			'three-col-left' 	=> array( 'label' => '3 Columns, sidebar on left', 		'img' => IMAGES_URL . '/layouts/three-col-left.png' ),
			'three-col-right' 	=> array( 'label' => '3 Columns, sidebar on right', 	'img' => IMAGES_URL . '/layouts/three-col-right.png' ),
			'three-col-both'	=> array( 'label' => '3 Columns, sidebar on each side',	'img' => IMAGES_URL . '/layouts/three-col-both.png' )
			);
		
		add_theme_support( 'sb-layouts', apply_filters( 'sb_layouts_defaults', $sb_default_layouts) ); // Theme Layouts
		add_theme_support( 'sb-layouts-home', apply_filters( 'sb_layouts_defaults_home', $sb_default_layouts ) ); // Theme Layouts (homepage)
		
		
		// If theme has been switched, unset child defaults
		add_action( 'switch_theme', array( 'StartBox', 'child_uninstall' ) );
		
		// Load default Scripts and Styles
		add_action( 'wp_enqueue_scripts', array( 'StartBox', 'sb_default_scripts'), 12 );
		
		// Set the content width based on the theme's layout for resizing large images.
		global $content_width;
		$layout = sb_get_option('layout');
		if ($layout == 'one-col') { $content_width = 940; }
		elseif ( $layout == 'three-col-left' || $layout == 'three-col-right' || $layout == 'three-col-both' ) { $content_width = 540; }
		else { $content_width = 640; }
		
	}
	
	// Include all Widgets, Plugins and Theme Options
	public function sb_includes() {
		
		require_if_theme_supports( 'sb-galleriffic', PLUGINS_PATH .  '/galleriffic.php' );							// Galleriffic Slideshows (not supported yet)
		require_if_theme_supports( 'sb-slideshows', PLUGINS_PATH .  '/slideshows.php' );  							// Slideshows Post Type
		require_if_theme_supports( 'sb-sidebars', PLUGINS_PATH .  '/sidebars.php' );	  							// Sidebar manager
		require_if_theme_supports( 'sb-layouts', FUNCTIONS_PATH .  '/layouts.php' );	  							// Theme Layouts
		foreach ( glob( WIDGETS_PATH . '/*.php') as $widget ) { require_once( $widget ); }							// Widgets
		foreach ( glob( ADMIN_PATH . '/*.php') as $admin ) { require_if_theme_supports( 'sb-options', $admin ); }	// Theme Options
		require_if_theme_supports( 'sb-updates', FUNCTIONS_PATH .  '/upgrade.php' );								// Update Manager
				
		// Check installed version, upgrade if needed (Credit: K2, http://getk2.com)
		$sb_version = get_option( 'startbox_version' );
		
		if ( $sb_version === false )
			StartBox::install(); // no version set, let's install SB for the first time!
		elseif ( version_compare($sb_version, SB_VERSION, '<') )
			StartBox::upgrade($sb_version); // version is out-dated, let's upgrade!
		
	}
	
	// Setup default scripts and styles
	public function sb_default_scripts() {
		if (is_admin()) { return; }
		if ( is_singular() ) wp_enqueue_style( 'print' );
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
		wp_enqueue_style( 'shortcodes' );
		wp_enqueue_style( 'layouts' );
		wp_enqueue_script( 'hovercards' );
		wp_enqueue_script( 'startbox' );
	}
	
	// Install StartBox for the first time -- Available hook: sb_install
	public function install() {

		// Setup the main theme options and store them to a variable
		add_option( THEME_OPTIONS );
		
		// Set the current StartBox version
		add_option( 'startbox_version', 'SB_VERSION' );

		// Included hook for other things to do during install
		do_action( 'sb_install' );

	}
	
	// Upgrade StartBox -- Available hook: sb_upgrade
	public function upgrade() {
		
		// Make sure we're not on the current version
		if ( version_compare( get_option('startbox_version'), SB_VERSION, '>=' ) )
			return;
			
		// Upgrade to 2.4.8
		if ( version_compare( get_option('startbox_version'), '2.4.8', '<' ) ) {

			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'post_thumbnail_width' => 200,
				'post_thumbnail_height' => 200
			);
			
			// Update column layouts properly
			if ( isset( $theme_settings['home_layout'] ) ) {
				if ( $theme_settings['home_layout'] == '1cr' ) { $new_settings['home_layout'] = 'one-col'; }
				elseif ( $theme_settings['home_layout'] == '2cl' ) { $new_settings['home_layout'] = 'two-col-left'; }
				elseif ( $theme_settings['home_layout'] == '2cr' ) { $new_settings['home_layout'] = 'two-col-right'; }
				elseif ( $theme_settings['home_layout'] == '3cl' ) { $new_settings['home_layout'] = 'three-col-left'; }
				elseif ( $theme_settings['home_layout'] == '3cr' ) { $new_settings['home_layout'] = 'three-col-right'; }
				elseif ( $theme_settings['home_layout'] == '3cb' ) { $new_settings['home_layout'] = 'three-col-both'; }
			}
			
			if ( isset( $theme_settings['layout'] ) ) {
				if ( $theme_settings['layout'] == '1cr' ) { $new_settings['layout'] = 'one-col'; }
				elseif ( $theme_settings['layout'] == '2cl' ) { $new_settings['layout'] = 'two-col-left'; }
				elseif ( $theme_settings['layout'] == '2cr' ) { $new_settings['layout'] = 'two-col-right'; }
				elseif ( $theme_settings['layout'] == '3cl' ) { $new_settings['layout'] = 'three-col-left'; }
				elseif ( $theme_settings['layout'] == '3cr' ) { $new_settings['layout'] = 'three-col-right'; }
				elseif ( $theme_settings['layout'] == '3cb' ) { $new_settings['layout'] = 'three-col-both'; }
			}
			
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.8' );
		}
		
		// Upgrade to 2.4.9
		if ( version_compare( get_option('startbox_version'), '2.4.9', '<') ) {
			
			$theme_settings = get_option( THEME_OPTIONS );
			
			if (!isset($theme_settings['nav_after_header'])) $theme_settings['nav_after_header'] = 'pages';
			if (!isset($theme_settings['nav_after_header_home'])) $theme_settings['nav_after_header_home'] = true;
			if (!isset($theme_settings['nav_before_header'])) $theme_settings['nav_before_header'] = 'disabled';
			if (!isset($theme_settings['nav_before_header_home'])) $theme_settings['nav_before_header_home'] = false;
			
			$new_settings = array(
				'enable_updates'			=> true,
				'primary_nav'				=> $theme_settings['nav_after_header'],
				'prymary_nav-enable-home'	=> $theme_settings['nav_after_header_home'],
				'primary_nav-position'		=> 'sb_after_header',
				'primary_nav-depth'			=> '0',
				'secondary_nav'				=> $theme_settings['nav_before_header'],
				'secondary_nav-enable-home'	=> $theme_settings['nav_before_header_home'],
				'secondary_nav-position' 	=> 'sb_before_header',
				'secondary_nav-depth'		=> '0',
				'site_url'					=> home_url(),
				'site_name'					=> get_bloginfo('name')
			);
			
			unset($theme_settings['nav_after_header']);
			unset($theme_settings['nav_after_header_home']);
			unset($theme_settings['nav_before_header']);
			unset($theme_settings['nav_before_header_home']);
			
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.9' );
		}
		
		// Upgrade to 2.4.9.2
		if ( version_compare( get_option('startbox_version'), '2.4.9.2', '<') ) {
			
			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'post_thumbnail_rss'	=> true
			);
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.9.2' );
		}
		
		// Upgrade to 2.5
		if ( version_compare( get_option('startbox_version'), '2.5', '<') ) {
			
			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'enable_post_thumbnails'			=> true,
				'post_thumbnail_use_attachments'	=> true,
				'post_thumbnail_hide_nophoto'		=> false,
				'post_thumbnail_align'				=> 'tc',
				'post_thumbnail_default_image'		=> IMAGES_URL . '/nophoto.jpg'
			);
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.5' );
		}
		
		// Upgrade to 2.5.6
		if ( version_compare( get_option('startbox_version'), '2.5.6', '<') ) {
			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'post_layout' => $theme_settings['layout'],
			);
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.5.6' );
		}
		
		// Upgrade to 2.6
		if ( version_compare( get_option('startbox_version'), '2.6', '<') ) {
			
			// Replace the Full Width page template with the one-column layout
			global $wpdb;
			$where = array(
				'meta_key' => '_wp_page_template',
				'meta_value' => 'page-fullwidth.php' );
			$new_values = array(
				'meta_key' => '_wp_page_template', 'meta_value' => '',
				'meta_key' => '_sb_layout', 'meta_value' => 'one-col' );
			$wpdb->update( $wpdb->postmeta, $new_values, $where );
			
			// $theme_settings = get_option( THEME_OPTIONS );
			// $new_settings = array();
			// $new_settings = wp_parse_args($new_settings, $theme_settings);
			// update_option( THEME_OPTIONS, $new_settings);
			// update_option( 'startbox_version', '2.6' );
		}
		
		// Upgrade to 2.6.1
		// if ( version_compare( get_option('startbox_version'), '2.6.1', '<') ) {
		// 	
		// 	$theme_settings = get_option( THEME_OPTIONS );
		// 	$new_settings = array();
		// 	$new_settings = wp_parse_args($new_settings, $theme_settings);
		// 	update_option( THEME_OPTIONS, $new_settings);
		// 	update_option( 'startbox_version', '2.6.1' );
		// }
		
		// Included hook for other things to do during upgrade
		do_action( 'sb_upgrade' );

	}
	
	// Install Child Theme for the first time -- Available hook: sb_child_install
	public function child_install() {
		
		// Grab all our settings variables
		global $sb_settings_factory;
		$defaults = get_option( THEME_OPTIONS );
		$settings = $sb_settings_factory->settings;
		$exclude = $sb_settings_factory->defaults;
		
		// Loop through all child setting defaults and store them to an array
		foreach($settings as $setting) {
			if ( !in_array( $setting->slug, $exclude ) ) {
				$options = $setting->options;
				foreach( $options as $option_id => $option ) {
					if ( isset( $option['default'] ) ) $defaults[$option_id] = $option['default'];
				}
			}
		}

		// Save the options to the database
		update_option( THEME_OPTIONS, apply_filters( 'sb_child_option_defaults', $defaults ) );
		
		// Included hook for other things to do durich Child theme install
		do_action('sb_child_install');

		// Store an option that the child theme has been installed
	    add_option('sb_child_install', true);
	}
	
	// Uninstall StartBox -- Available hook: sb_uninstall
	public function uninstall() {
		
		// Delete options stored to the database
		delete_option( THEME_OPTIONS );
		delete_option( 'startbox_version' );
		delete_option( 'sb_child_install' );
		
		// Included hook for other things to do during uninstall
		do_action( 'sb_uninstall' );
	}
	
	// Child Uninstall
	public function child_uninstall() { delete_option( 'sb_child_install' ); }

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

?>