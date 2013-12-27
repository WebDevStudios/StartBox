<?php
/**
 * SBX Bootstrap
 *
 * @package SBX
 * @subpackage Core
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * SBX Main class.
 *
 * This does all the magic of making everything available.
 *
 * @package SBX
 * @subpackage Classes
 * @since 1.0.0
 */
if ( ! class_exists('SBX') ) {
	class SBX {

		/**
		 * SBX Version
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public static $version = '1.0.0';

		/**
		 * SBX Directory
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public static $sbx_dir = '';

		/**
		 * SBX Directory URI
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public static $sbx_uri = '';

		/**
		 * SBX Options Prefix
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public static $options_prefix = 'sbx_';

		/**
		 * SBX Initialization.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Setup static vars
			self::$sbx_dir = trailingslashit( get_template_directory() ) . basename( dirname( __FILE__ ) );
			self::$sbx_uri = trailingslashit( get_template_directory_uri() ) . basename( dirname( __FILE__ ) );

			// Hook in the SBX engine
			add_action( 'init', array( $this, 'core' ), 1 );
			add_action( 'init', array( $this, 'extensions' ), 2 );
			add_action( 'init', array( $this, 'i18n' ), 3 );

			// Register bundled scripts and styles
			add_action( 'init', array( $this, 'front_end_scripts' ), 4 );
			add_action( 'admin_init', array( $this, 'admin_scripts' ), 4 );

			// Allow other processes to attach to SBX init
			do_action( 'sb_init', $this );

		} /* __construct() */

		/**
		 * Load core file requirements for SBX.
		 *
		 * @since 1.0.0
		 */
		public function core() {
			require_once( SBX::$sbx_dir . '/core/conditionals.php' );
			require_once( SBX::$sbx_dir . '/core/hooks.php' );
			require_once( SBX::$sbx_dir . '/core/images.php' );
			require_once( SBX::$sbx_dir . '/core/shortcodes.php' );
			require_once( SBX::$sbx_dir . '/core/utility.php' );
		} /* core() */

		/**
		 * Load custom theme extensions.
		 *
		 * Only extensions supported by the theme will be loaded.
		 *
		 * @since 1.0.0
		 */
		public function extensions() {
			require_if_theme_supports( 'sbx-customizer', SBX::$sbx_dir . '/classes/SBX_Customizer.php' );
			require_if_theme_supports( 'sbx-layouts',    SBX::$sbx_dir . '/classes/SBX_Layouts.php' );
			require_if_theme_supports( 'sbx-plugins',    SBX::$sbx_dir . '/classes/TGM_Plugin_Activation.php' );
			require_if_theme_supports( 'sbx-sidebars',   SBX::$sbx_dir . '/classes/SBX_Sidebars.php' );
			require_if_theme_supports( 'sbx-updates',    SBX::$sbx_dir . '/classes/SBX_Updater.php' );

			// Include all SBX Options files
			require_if_theme_supports( 'sbx-options',    SBX::$sbx_dir . '/classes/SBX_Options_API.php' );
			foreach ( glob( SBX::$sbx_dir . '/admin/*.php') as $sb_admin ) {
				require_if_theme_supports( 'sbx-options', $sb_admin );
			}
		} /* extensions() */

		/**
		 * Setup theme translations.
		 *
		 * @since 1.0.0
		 */
		public function i18n() {
			load_theme_textdomain( 'sbx', SBX::$sbx_dir . '/languages' );
		} /* i18n() */

		/**
		 * Register the packaged front-end scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function front_end_scripts() {
			wp_register_script( 'sbx-js', SBX::$sbx_uri . '/js/sbx.js', array( 'jquery' ), SBX::$version );
			wp_register_style( 'sbx-css', SBX::$sbx_uri . '/css/sbx.css', null, SBX::$version );
			wp_register_style( 'default', get_stylesheet_uri(), null, SBX::$version );

			// Enqueue for front-end only
			if ( ! is_admin() ) {
				wp_enqueue_script( 'sbx-js' );
				wp_enqueue_style( 'sbx' );
				wp_enqueue_style( 'default' );
			}

		} /* front_end_scripts() */

		/**
		 * Register the packaged admin scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function admin_scripts() {
			wp_register_script( 'sbx-admin', SBX::$sbx_uri . '/js/admin.js', array( 'jquery' ), SBX::$version );
			wp_enqueue_script( 'sbx-admin' );
		} /* admin_scripts() */
	}
}
$GLOBALS['sbx'] = new SBX;

// "God opposes the proud, but gives grace to the humble." - James 4:6b (ESV)
