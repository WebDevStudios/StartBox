<?php

/**
 * StartBox - Main class
 *
 * Loads all includes, theme constants, adds/removes filters, etc.
 *
 * @package StartBox
 * @subpackage Functions
 * @since 2.4.5
 */

class StartBox {

	// Initialize StartBox -- Available action: sb_init
	public function init() {

		// Grab and define our variables and constants
		global $blog_id;

		// If we're on on 3.4, use the handy wp_get_theme function
		if ( function_exists('wp_get_theme') ) {
			$startbox		= wp_get_theme( 'startbox' );
			$current_theme	= wp_get_theme();
			$sb_version		= $startbox->version;
			$theme_version	= $current_theme->version;
		// Otherwise, stick with the old way of doing things
		} else {
			$startbox		= get_theme_data( get_template_directory() . '/style.css' );
			$theme_data		= get_theme_data( get_stylesheet_directory() . '/style.css' );
			$sb_version		= $sb_data['version'];
			$theme_version	= $theme_data['version'];
			$current_theme	= $theme_data['Name'];
		}

		// Setup our theme constants
		define( 'THEME_NAME',		$current_theme );
		define( 'THEME_VERSION',	$theme_version );
		define( 'THEME_OPTIONS',	'startbox' );
		define( 'THEME_PREFIX',		'sb_' );
		define( 'SB_VERSION',		$sb_version );
		define( 'THEME_PATH',		get_stylesheet_directory() );
		define( 'THEME_URI',		get_stylesheet_directory_uri() );
		define( 'SB_PATH',			get_template_directory() );
		define( 'INCLUDES_PATH',	get_template_directory() . '/includes' );
		define( 'INCLUDES_URL',		get_template_directory_uri() . '/includes' );
		define( 'ADMIN_PATH',		INCLUDES_PATH . '/admin' );
		define( 'FUNCTIONS_PATH',	INCLUDES_PATH . '/functions' );
		define( 'EXTENSIONS_PATH',	INCLUDES_PATH . '/extensions' );
		define( 'SCRIPTS_URL',		INCLUDES_URL . '/scripts' );
		define( 'WIDGETS_PATH',		INCLUDES_PATH . '/widgets' );
		define( 'STYLES_URL',		INCLUDES_URL . '/styles' );
		define( 'IMAGES_URL',		get_template_directory_uri() . '/images' );

		// Translate, if applicable
		load_theme_textdomain( 'startbox', INCLUDES_PATH . '/languages' );

		// Register all our stock functionality
		require_once( FUNCTIONS_PATH . '/admin_settings.php' );	// Admin Functions
		require_once( FUNCTIONS_PATH . '/custom.php' );			// Custom Functions
		require_once( FUNCTIONS_PATH . '/conditionals.php' );	// Conditional Functions
		require_once( FUNCTIONS_PATH . '/images.php' );			// Image Functions
		require_once( FUNCTIONS_PATH . '/menus.php' );			// Menu Functions
		require_once( FUNCTIONS_PATH . '/depricated.php' );		// Deprecated Functions
		require_once( FUNCTIONS_PATH . '/hooks.php' );			// Hooks
		require_once( FUNCTIONS_PATH . '/sidebars.php' );		// Sidebars
		require_once( FUNCTIONS_PATH . '/comment_format.php' );	// Comment Structure

		// Register our scripts and styles
		add_action( 'init', array( 'StartBox', 'register_scripts_and_styles' ), 1 );

		// Setup our theme environment
		add_action( 'after_setup_theme', array( 'StartBox', 'environment' ), 5 );
		add_action( 'after_setup_theme', array( 'StartBox', 'sb_includes' ), 15 );

		// Included hook for other things to do during initialization
		do_action('sb_init');

		// If no version information exists in the database, run our installer
		if ( ! get_option( 'startbox_version' ) )
			add_action( 'after_setup_theme', array( 'StartBox', 'install' ), 20 );

		// Add child theme defaults if child theme is activated for the first time (Credit: Joel Kuczmarski)
		if ( ! get_option( 'sb_child_install' ) && SB_PATH != THEME_PATH )
			add_action( 'after_setup_theme', array( 'StartBox', 'child_install' ), 25 );

		// If we already have a version set, and it's older than current, update!
		if ( version_compare( get_option( 'startbox_version' ), SB_VERSION, '<') )
			StartBox::perform_upgrade();

		// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)

	}

	// Register all of the included scripts and styles
	public function register_scripts_and_styles() {

		// Register Default Scripts
		wp_register_script( 'startbox',		SCRIPTS_URL . '/startbox.js', array('jquery', 'colorbox', 'md5', 'smoothScroll') );
		wp_register_script( 'colorbox',		SCRIPTS_URL . '/jquery.colorbox.min.js', array('jquery'), NULL );
		wp_register_script( 'md5',			SCRIPTS_URL . '/jquery.md5.js', array('jquery') );
		wp_register_script( 'hovercards',	( is_ssl() ? 'https://secure' : 'http://s' ) . '.gravatar.com/js/gprofiles.js?u', array('jquery') ); // Gravatar Hovercards
		wp_register_script( 'smoothScroll',	SCRIPTS_URL . '/jquery.smooth-scroll.min.js', array('jquery'), '1.4');

		// Register Default Styles
		wp_register_style( 'startbox',		STYLES_URL . '/startbox.css' );
		wp_register_style( 'layouts',		STYLES_URL . '/layouts.css' );
		wp_register_style( 'colorbox',		STYLES_URL . '/colorbox.css', null, SB_VERSION, 'screen' );
		wp_register_style( 'comments',		STYLES_URL . '/comments.css' );
		wp_register_style( 'reset',			STYLES_URL . '/reset.css' );
		wp_register_style( 'images',		STYLES_URL . '/images.css' );
		wp_register_style( 'shortcodes',	STYLES_URL . '/shortcodes.css' );
		wp_register_style( 'typography',	STYLES_URL . '/typography.css' );
		wp_register_style( 'print',			STYLES_URL . '/print.css', null, SB_VERSION, 'print' );
	}

	// Setup the environment and register support for various WP features.
	public function environment() {

		// Add theme support for various WP-specific features
		register_nav_menus( array( 'primary' => __( 'Primary Navigation', 'startbox' ), 'secondary' => __( 'Secondary Navigation', 'startbox' ) ) );  // Enables custom menus in the Appearance menu, since WP3.0
		add_theme_support( 'automatic-feed-links' );	// Adds default posts and comments RSS feeds, since WP3.0
		add_theme_support( 'post-thumbnails' );			// Enables post thumbnails in the write screens, since WP2.9
		set_post_thumbnail_size( 200, 200, true );		// Sets the default thumbnail size to 200x200
		add_editor_style( array(						// This sets up the content editor style to match the front-end design
			'/includes/styles/typography.css',			// Basic Typography
			'/includes/styles/editor.css'				// Content-specific styles (adapted from startbox.css)
		) );

		// Add theme support for StartBox-specific features
		add_theme_support( 'sb-updates' );				// StartBox Updates Manager
		add_theme_support( 'sb-options' );				// StartBox Options API
		add_theme_support( 'sb-sidebars' );				// StartBox Easy Sidebars
		add_theme_support( 'sb-shortcodes' );			// StartBox Shortcodes
		add_theme_support( 'sb-slideshows' );			// StartBox Slideshows
		// add_theme_support( 'sb-theme-customizer' );	// StartBox Theme Customizer Settings

		// Add theme support for StartBox Layouts, redefine this list of available layouts using the filter 'sb_layouts_defaults'
		$sb_default_layouts = array(
			'one-col' 			=> array( 'label' => '1 Column (no sidebars)', 			'img' => IMAGES_URL . '/layouts/one-col.png' ),
			'two-col-left' 		=> array( 'label' => '2 Columns, sidebar on left', 		'img' => IMAGES_URL . '/layouts/two-col-left.png' ),
			'two-col-right' 	=> array( 'label' => '2 Columns, sidebar on right', 	'img' => IMAGES_URL . '/layouts/two-col-right.png' ),
			'three-col-left' 	=> array( 'label' => '3 Columns, sidebar on left', 		'img' => IMAGES_URL . '/layouts/three-col-left.png' ),
			'three-col-right' 	=> array( 'label' => '3 Columns, sidebar on right', 	'img' => IMAGES_URL . '/layouts/three-col-right.png' ),
			'three-col-both'	=> array( 'label' => '3 Columns, sidebar on each side',	'img' => IMAGES_URL . '/layouts/three-col-both.png' )
			);

		add_theme_support( 'sb-layouts', apply_filters( 'sb_layouts_defaults', $sb_default_layouts) ); 				// Theme Layouts
		add_theme_support( 'sb-layouts-home', apply_filters( 'sb_layouts_defaults_home', $sb_default_layouts ) );	// Theme Layouts (homepage)

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

		require_if_theme_supports( 'sb-updates',			FUNCTIONS_PATH . '/upgrade.php' );									// Update Manager
		require_if_theme_supports( 'sb-sidebars',			EXTENSIONS_PATH . '/sidebars.php' );								// Sidebar manager
		require_if_theme_supports( 'sb-shortcodes',			EXTENSIONS_PATH . '/shortcodes.php' );								// Shortcodes
		require_if_theme_supports( 'sb-slideshows',			EXTENSIONS_PATH . '/startbox-slideshows/startbox-slideshows.php' );	// Slideshows
		require_if_theme_supports( 'sb-layouts',			EXTENSIONS_PATH . '/layouts.php' );									// Theme Layouts
		require_if_theme_supports( 'sb-theme-customizer',	EXTENSIONS_PATH . '/theme-customizer.php' );				 		// Theme Customizer settings (in development)
		foreach ( glob( ADMIN_PATH . '/*.php') as $sb_admin ) { require_if_theme_supports( 'sb-options', $sb_admin ); }			// Theme Options
		foreach ( glob( WIDGETS_PATH . '/*.php') as $sb_widget ) { require_once( $sb_widget ); }								// Widgets

	}

	// Setup default scripts and styles
	public function sb_default_scripts() {
		if ( is_admin() ) { return; }
		if ( is_singular() ) {
			wp_enqueue_style( 'print' );
			wp_enqueue_script( 'comment-reply' );
		}
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
		add_option( 'startbox_version', SB_VERSION );

		// Included hook for other things to do during install
		do_action( 'sb_install' );

	}

	// Install a Child Theme for the first time -- Available hook: sb_child_install
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

	// Uninstall StartBox -- Available hook: sb_uninstall -- Note: this doesn't actually get called anywhere
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

	// Upgrade StartBox core -- Available hook: sb_upgrade
	public function perform_upgrade() {

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
			} else {
				$theme_settings['home_layout'] = 'two-col-right';
			}

			if ( isset( $theme_settings['layout'] ) ) {
				if ( $theme_settings['layout'] == '1cr' ) { $new_settings['layout'] = 'one-col'; }
				elseif ( $theme_settings['layout'] == '2cl' ) { $new_settings['layout'] = 'two-col-left'; }
				elseif ( $theme_settings['layout'] == '2cr' ) { $new_settings['layout'] = 'two-col-right'; }
				elseif ( $theme_settings['layout'] == '3cl' ) { $new_settings['layout'] = 'three-col-left'; }
				elseif ( $theme_settings['layout'] == '3cr' ) { $new_settings['layout'] = 'three-col-right'; }
				elseif ( $theme_settings['layout'] == '3cb' ) { $new_settings['layout'] = 'three-col-both'; }
			} else {
				$theme_settings['layout'] = 'two-col-right';
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
			$theme_settings['layout'] = isset( $theme_settings['layout']) ? $theme_settings['layout'] : '';
			$new_settings = array( 'post_layout' => $theme_settings['layout'] );
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

			// Update our old logo settings based on old preference
			$logo_disabled = sb_get_option('logo-disabled');
			$logo_text     = sb_get_option('logo-text');
			$logo_image    = sb_get_option('logo-image');
			if ( $logo_disabled )
				sb_update_option( 'logo-select', 'disabled' );
			elseif ( ! empty( $logo_text ) )
				sb_update_option( 'logo-select', 'text' );
			else
				sb_update_option( 'logo-select', 'image' );

			// If we have existing copyright information in the site
			if ( sb_get_option('enable_copyright') ) {

				// Grab our legacy footer text settings
				$enable_copyright	= sb_get_option('enable_copyright');
				$copyright_year		= sb_get_option('copyright_year') ? sb_get_option('copyright_year') : date('Y');
				$enable_wp_credit	= sb_get_option('enable_wp_credit');
				$enable_sb_credit	= sb_get_option('enable_sb_credit');
				$old_footer_text	= sb_get_option('footer_text');

				// Build new footer text content
				// Roughly: [copyright year="2012"] [site_link].<br/>Proudly powered by [WordPress] and [StartBox].
				$new_footer_text = '';
				if ( $enable_copyright ) { $new_footer_text .= '[copyright year="' . $copyright_year . '"] [site_link].'; }
				if ( $enable_copyright && ( $enable_wp_credit || $enable_sb_credit ) ) { $new_footer_text .= '<br/>'; }
				if ( $enable_wp_credit || $enable_sb_credit ) {
					$new_footer_text .= 'Proudly powered by ';
					if ( $enable_wp_credit ) { $new_footer_text .= '[WordPress]'; }
					if ( $enable_wp_credit && $enable_sb_credit ) { $new_footer_text .= ' and '; }
					if ( $enable_sb_credit ) { $new_footer_text .= '[StartBox]'; }
					$new_footer_text .= '.';
				}
				if ( $old_footer_text ) { $new_footer_text .= '<br/>' . $old_footer_text; }

				// Update our new footer text option
				sb_update_option( 'footer_text', $new_footer_text);

				// Finally, delete our old footer options
				sb_delete_option('enable_copyright');
				sb_delete_option('copyright_year');
				sb_delete_option('enable_wp_credit');
				sb_delete_option('enable_sb_credit');
				sb_delete_option('enable_designer_credit');
				sb_delete_option('site_name');
				sb_delete_option('site_url');
				sb_delete_option('footer_text');

			}

			// Update our working version to 2.6
			update_option( 'startbox_version', '2.6' );

		}

		// Upgrade to 2.7
		if ( version_compare( get_option('startbox_version'), '2.7', '<') ) {

			// Update our active widgets to properly handle the name change for primary and secondary widget areas
			$registered_sidebars = get_option( 'sidebars_widgets' );
			if ( isset( $registered_sidebars['primary_widget_area'] ) ) {
				$registered_sidebars['primary'] = $registered_sidebars['primary_widget_area'];
				unset( $registered_sidebars['primary_widget_area'] );
			}
			if ( isset( $registered_sidebars['secondary_widget_area'] ) ) {
				$registered_sidebars['secondary'] = $registered_sidebars['secondary_widget_area'];
				unset( $registered_sidebars['secondary_widget_area'] );
			}
			update_option( 'sidebars_widgets', $registered_sidebars );

			// Update our custom sidebars to handle the name change for primary and secondary widget areas
			global $wpdb;
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'primary' ), array( 'meta_value' => 'primary_widget_area' ) );
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'secondary' ), array( 'meta_value' => 'secondary_widget_area' ) );

			// Update our theme version
			update_option( 'startbox_version', '2.7' );

		}

		// Included hook for other things to do during upgrade
		do_action( 'sb_upgrade' );

	}

}