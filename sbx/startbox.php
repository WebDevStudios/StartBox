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
if ( ! class_exists('StartBox') ) {
	class StartBox {

		/**
		 * Initialization constructor for SBX
		 *
		 * @since 3.0.0
		 */
		public function __construct() {

			// Hook in all the different parts of our engine
			add_action( 'init', array( $this, 'constants' ), 1 );
			add_action( 'init', array( $this, 'core' ), 2 );
			add_action( 'init', array( $this, 'extensions' ), 3 );
			add_action( 'init', array( $this, 'i18n' ), 4 );

			// Register our scripts and styles
			add_action( 'init', array( $this, 'register_scripts_and_styles' ), 5 );

			// Available action for other processes to fire during init
			do_action( 'sb_init', $this );

		}

		/**
		 * Register the constants used throughout SBX
		 *
		 * @since 3.0.0
		 */
		public function constants() {

			// Setup version and option constants
			define( 'SB_VERSION',      '3.0.0' );
			define( 'THEME_OPTIONS',   'startbox' );
			define( 'THEME_PREFIX',    'sb' );
			define( 'THEME_NAME',      wp_get_theme() );

			// Define all our paths
			define( 'THEME_DIR',       get_template_directory() );
			define( 'THEME_URI',       get_template_directory_uri() );
			define( 'CHILD_THEME_DIR', get_stylesheet_directory() );
			define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );
			define( 'SB_DIR',          trailingslashit( THEME_DIR ) . basename( dirname( __FILE__ ) ) );
			define( 'SB_URI',          trailingslashit( THEME_URI ) . basename( dirname( __FILE__ ) ) );
			define( 'SB_ADMIN',        trailingslashit( SB_DIR ) . 'admin' );
			define( 'SB_CLASSES',      trailingslashit( SB_DIR ) . 'classes' );
			define( 'SB_CSS',          trailingslashit( SB_URI ) . 'css' );
			define( 'SB_EXTENSIONS',   trailingslashit( SB_DIR ) . 'extensions' );
			define( 'SB_IMAGES',       trailingslashit( SB_DIR ) . 'images' );
			define( 'SB_JS',           trailingslashit( SB_URI ) . 'js' );
			define( 'SB_LANGUAGES',    trailingslashit( SB_DIR ) . 'languages' );

		}

		/**
		 * Load core file requirements for SBX
		 *
		 * @since 3.0.0
		 */
		public function core() {
			require_once( SB_EXTENSIONS . '/conditionals.php' );
			require_once( SB_EXTENSIONS . '/custom.php' );
			require_once( SB_EXTENSIONS . '/hooks.php' );
			require_once( SB_EXTENSIONS . '/images.php' );
			require_once( SB_EXTENSIONS . '/post-types-taxonomies.php' );
			require_once( SB_EXTENSIONS . '/template-tags.php' );
		}

		/**
		 * Load custom theme extensions, only if supported by the theme
		 *
		 * @since 3.0.0
		 */
		public function extensions() {
			require_if_theme_supports( 'sb-breadcrumbs',     SB_CLASSES . '/SB_Breadcrumbs.php' );
			require_if_theme_supports( 'sb-customizer',      SB_CLASSES . '/SB_Customizer.php' );
			require_if_theme_supports( 'sb-layouts',         SB_CLASSES . '/SB_Layouts.php' );
			require_if_theme_supports( 'sb-sidebars',        SB_CLASSES . '/SB_Sidebars.php' );
			require_if_theme_supports( 'sb-custom-sidebars', SB_CLASSES . '/SB_Custom_Sidebars.php' );
			require_if_theme_supports( 'sb-updates',         SB_CLASSES . '/SB_Updater.php' );
			require_if_theme_supports( 'sb-shortcodes',      SB_EXTENSIONS . '/shortcodes.php' );
			require_if_theme_supports( 'sb-options',		SB_CLASSES . '/SB_Options_API.php' );

			// Include all customization panels
			foreach ( glob( SB_ADMIN . '/*.php') as $sb_admin )
			 	require_if_theme_supports( 'sb-customizer', $sb_admin );

		}

		/**
		 * Setup theme translations
		 *
		 * @since 3.0.0
		 */
		public function i18n() {

			// Translate, if applicable
			load_theme_textdomain( 'sbx', SB_LANGUAGES );

		}

		/**
		 * Register the packaged scripts and styles
		 *
		 * @since 3.0.0
		 */
		public function register_scripts_and_styles() {

			// Register Default Scripts
			wp_register_script( 'colorbox',     SB_JS . '/jquery.colorbox.min.js', array( 'jquery' ), SB_VERSION );
			wp_register_script( 'smoothScroll', SB_JS . '/jquery.smooth-scroll.min.js', array( 'jquery' ), SB_VERSION );
			wp_register_script( 'startbox',     SB_JS . '/startbox.js', array( 'jquery' ), SB_VERSION );
			wp_enqueue_script( 'startbox' );

			// Register Default Styles
			wp_register_style( 'colorbox',      SB_CSS . '/colorbox.css', null, SB_VERSION, 'screen' );
			wp_register_style( 'images',        SB_CSS . '/images.css', null, SB_VERSION );
			wp_register_style( 'layouts',       SB_CSS . '/layouts.css', null, SB_VERSION );
			wp_register_style( 'print',         SB_CSS . '/print.css', null, SB_VERSION, 'print' );
			wp_register_style( 'reset',         SB_CSS . '/reset.css', null, SB_VERSION );
			wp_register_style( 'shortcodes',    SB_CSS . '/shortcodes.css', null, SB_VERSION );
			wp_register_style( 'typography',    SB_CSS . '/typography.css', null, SB_VERSION );
		}
	}
}
$GLOBALS['startbox'] = new StartBox;

// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)