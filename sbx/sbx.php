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
			require_if_theme_supports( 'sb-options',		 SB_CLASSES . '/SB_Options_API.php' );

			// Include all setting metaboxes
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/admin.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/favicon.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/feeds.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/header_scripts.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/footer_scripts.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/help.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/upgrade.php' );
			require_if_theme_supports( 'sb-customizer', SB_ADMIN .'/footer.php' );

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
			wp_register_script( 'sbx', SB_JS . '/sbx.js', array( 'jquery' ), SB_VERSION );
			wp_enqueue_script( 'sbx' );

			// Register Default Styles
			wp_register_style( 'sbx', SB_CSS . '/sbx.css', null, SB_VERSION );
			wp_register_style( 'default', CHILD_THEME_URI . '/style.css', null, SB_VERSION );

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

			wp_register_script( 'sb-admin', SB_JS . '/admin.js', array( 'jquery' ), SB_VERSION );
			wp_enqueue_script( 'sb-admin' );

		}
	}
}
$GLOBALS['startbox'] = new StartBox;

// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)