<?php
/**
 * SBX Main class
 *
 * Loads all includes, theme constants, adds/removes filters, etc.
 *
 * @package SBX
 * @subpackage Functions
 * @since 2.4.5
 */
if ( ! class_exists('SBX') ) {
	class SBX {

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

			// Register admin scripts and styles
			add_action( 'admin_init', array( $this, 'admin_register_scripts_and_styles' ), 5 );

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
			define( 'SBX_VERSION',      '3.0.0' );
			define( 'SBX_OPTIONS',   'startbox' );
			define( 'THEME_PREFIX',    'sb' );
			define( 'THEME_NAME',      wp_get_theme() );

			// Define all our paths
			define( 'THEME_DIR',       get_template_directory() );
			define( 'THEME_URI',       get_template_directory_uri() );
			define( 'CHILD_THEME_DIR', get_stylesheet_directory() );
			define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );
			define( 'SBX_DIR',         trailingslashit( THEME_DIR ) . basename( dirname( __FILE__ ) ) );
			define( 'SBX_URI',         trailingslashit( THEME_URI ) . basename( dirname( __FILE__ ) ) );
			define( 'SBX_ADMIN',       trailingslashit( SBX_DIR ) . 'admin' );
			define( 'SBX_CLASSES',     trailingslashit( SBX_DIR ) . 'classes' );
			define( 'SBX_CSS',         trailingslashit( SBX_URI ) . 'css' );
			define( 'SBX_EXTENSIONS',  trailingslashit( SBX_DIR ) . 'extensions' );
			define( 'SBX_IMAGES',      trailingslashit( SBX_DIR ) . 'images' );
			define( 'SBX_JS',          trailingslashit( SBX_URI ) . 'js' );
			define( 'SBX_LANGUAGES',   trailingslashit( SBX_DIR ) . 'languages' );

		}

		/**
		 * Load core file requirements for SBX
		 *
		 * @since 3.0.0
		 */
		public function core() {
			require_once( SBX_EXTENSIONS . '/conditionals.php' );
			require_once( SBX_EXTENSIONS . '/template-tags.php' );
			require_once( SBX_EXTENSIONS . '/hooks.php' );
			require_once( SBX_EXTENSIONS . '/images.php' );
		}

		/**
		 * Load custom theme extensions, only if supported by the theme
		 *
		 * @since 3.0.0
		 */
		public function extensions() {
			require_if_theme_supports( 'sbx-breadcrumbs',     SBX_CLASSES . '/SBX_Breadcrumbs.php' );
			require_if_theme_supports( 'sbx-customizer',      SBX_CLASSES . '/SBX_Customizer.php' );
			require_if_theme_supports( 'sbx-layouts',         SBX_CLASSES . '/SBX_Layouts.php' );
			require_if_theme_supports( 'sbx-sidebars',        SBX_CLASSES . '/SBX_Sidebars.php' );
			require_if_theme_supports( 'sbx-custom-sidebars', SBX_CLASSES . '/SBX_Custom_Sidebars.php' );
			require_if_theme_supports( 'sbx-updates',         SBX_CLASSES . '/SBX_Updater.php' );
			require_if_theme_supports( 'sbx-shortcodes',      SBX_EXTENSIONS . '/shortcodes.php' );
			require_if_theme_supports( 'sbx-options',		  SBX_CLASSES . '/SBX_Options_API.php' );

			// Include all admin settings
			foreach ( glob( SBX_ADMIN . '/*.php') as $sb_admin ) {
				require_if_theme_supports( 'sbx-options', $sb_admin );
			}
		}

		/**
		 * Setup theme translations
		 *
		 * @since 3.0.0
		 */
		public function i18n() {

			// Translate, if applicable
			load_theme_textdomain( 'sbx', SBX_LANGUAGES );

		}

		/**
		 * Register the packaged scripts and styles
		 *
		 * @since 3.0.0
		 */
		public function register_scripts_and_styles() {

			// Register Default Scripts
			wp_register_script( 'sbx', SBX_JS . '/sbx.js', array( 'jquery' ), SBX_VERSION );
			wp_enqueue_script( 'sbx' );

			// Register Default Styles
			wp_register_style( 'sbx', SBX_CSS . '/sbx.css', null, SBX_VERSION );
			wp_register_style( 'default', CHILD_THEME_URI . '/style.css', null, SBX_VERSION );

			// Enqueue Default Styles
			if ( ! is_admin() ) {
				wp_enqueue_style( 'sbx' );
				wp_enqueue_style( 'default' );
			}

		}

		/**
		 * Register the packaged scripts and styles for the WP admin
		 *
		 * @since 3.0.0
		 */
		public function admin_register_scripts_and_styles() {

			wp_register_script( 'sb-admin', SBX_JS . '/admin.js', array( 'jquery' ), SBX_VERSION );
			wp_enqueue_script( 'sb-admin' );

		}
	}
}
$GLOBALS['startbox'] = new SBX;

// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)