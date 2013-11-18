<?php
/**
 * StartBox Theme Customizer Settings
 *
 * Base extensions for the theme customizer introduced in WP3.4
 * Many props to Alex Mansfield (@alexmansfield) for his work
 * with Theme Foundation and Theme Toolkit.
 *
 * @link http://themefoundation.com/wordpress-theme-customizer/
 *
 * @package StartBox
 * @subpackage Customizer
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports the customizer, skip the rest if not
if ( ! current_theme_supports( 'sbx-customizer' ) ) return;

/**
 * Base class for extending the theme customizer.
 *
 * @subpackage Classes
 * @since 3.0.0
 */
class SBX_Customizer {

	/**
	 * Instantiation method.
	 *
	 * @since 3.0.0
	 */
	function __construct() {

		// Register a customizer menu under Appearance
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Render all of our customizer settings and sections
		add_action( 'customize_register', array( $this, 'customize_register' ) );

		// Bind JS handlers so customizer preview reloads asynchronously.
		add_action( 'customize_preview_init', array( $this, 'customize_preview_int' ) );

	}

	/**
	 * Register menu item for theme customizer.
	 *
	 * @since 3.0.0
	 */
	function admin_menu() {
	    add_theme_page( __( 'Customize', 'startbox' ), __( 'Customize', 'startbox' ), 'edit_theme_options', 'customize.php' );
	}

	/**
	 * Display customizer options.
	 *
	 * Creates customizer options based on the customizer array,
	 * usually set in the theme's functions.php file.
	 *
	 * @since 3.0.0
	 *
	 * @param object $wp_customize Theme customizer object.
	 */
	public function customize_register( $wp_customize = null ) {

		// Grab the currently registered sections
		$sections = $wp_customize->sections();

		// Pull back all registered settings
		$customizer_settings = apply_filters( 'sb_customizer_settings', array() );

		// Loop through each registered setting
		if ( is_array( $customizer_settings ) && ! empty( $customizer_settings ) ) {
			foreach ( $customizer_settings as $section_id => $section ) {

				// If customizer section doesn't exist, add it
				if ( ! isset( $sections[$section_id] ) )
					$this->add_section( $wp_customize, $section_id, $section );

				// Add the section settings
				foreach ( $section['settings'] as $setting ) {
					$this->add_setting( $wp_customize, $section_id, $setting );
					$this->add_control( $wp_customize, $section_id, $setting );
				}

			}
		}

	}

	/**
	 * Add a single customizer section.
	 *
	 * @since 3.0.0
	 *
	 * @param object $wp_customize Theme customizer object.
	 * @param string $section_id   Unique section name.
	 * @param array  $section      Additional section data.
	 */
	public function add_section( $wp_customize = null, $section_id = '', $section = array() ) {

		// Sanitize section details
		$section_details                   = array();
		$section_details['title']          = sanitize_text_field( $section['title'] );
		$section_details['description']    = ! empty( $section['description'] ) ? esc_html( $section['description'] ) : '';
		$section_details['priority']       = ! empty( $section['priority'] ) ? absint( $section['priority'] ) : 10;
		$section_details['capability']     = ! empty( $section['capability'] ) ? sanitize_text_field( $section['capability'] ) : 'edit_theme_options';
		$section_details['theme_supports'] = ! empty( $section['theme_supports'] ) ? sanitize_text_field( $section['theme_supports'] ) : null;

		// Add this section to the theme customizer
		$wp_customize->add_section( $section_id, $section_details );

	}

	/**
	 * Add a single setting to the customizer.
	 *
	 * @since 3.0.0
	 *
	 * @param object $wp_customize    Theme customizer object.
	 * @param string $section_id      Parent section ID.
	 * @param array  $setting_details Additional setting details.
	 */
	public function add_setting( $wp_customize = null, $section_id = '', $setting_details = array() ) {

		// Sanitize setting details
		$setting                         = array();
		$setting['default']              = ! empty( $setting_details['default'] ) ? $setting_details['default'] : null;
		$setting['capability']           = ! empty( $setting_details['capability'] ) ? $setting_details['capability'] : 'edit_theme_options';
		$setting['theme_supports']       = ! empty( $setting_details['theme_supports'] ) ? $setting_details['theme_supports'] : null;
		$setting['transport']            = ! empty( $setting_details['transport'] ) ? $setting_details['transport'] : 'refresh';
		$setting['sanitize_js_callback'] = ! empty( $setting_details['sanitize_js_callback'] ) ? $setting_details['sanitize_js_callback'] : null;

		// Setup setting sanitization
		if ( ! empty( $setting_details['sanitize_callback'] ) ) {
			$setting['sanitize_callback'] = $setting_details['sanitize_callback'];

		// If sanitize callback was unspecified, select a
		// good sanatization callback based on data type
		} else {

			// If valid data type is empty, use setting type
			if ( empty( $setting_details['valid'] ) )
				$setting_details['valid'] = $setting_details['type'];

			switch ( $setting_details['valid'] ) {
				case 'text':
					$setting['sanitize_callback'] = 'sanitize_text_field';
					break;
				case 'url':
					$setting['sanitize_callback'] = 'esc_url_raw';
					break;
				case 'email':
					$setting['sanitize_callback'] = 'sanitize_email';
					break;
				case 'radio':
				case 'select':
					$setting['sanitize_callback'] = array( new SBX_Sanitization( $setting_details['choices'] ), 'sanitize_multiple_choice' );
					break;
				case 'checkbox':
					$setting['sanitize_callback'] = array( new SBX_Sanitization(), 'sanitize_html' );
					break;
				case 'html':
					$setting['sanitize_callback'] = array( new SBX_Sanitization(), 'sanitize_html' );
					break;
				case 'integer':
					$setting['sanitize_callback'] = array( new SBX_Sanitization(), 'sanitize_integer' );
					break;
				case 'color':
					$setting['sanitize_callback'] = 'sanitize_hex_color';
					break;
				default:
					$setting['sanitize_callback'] = 'sanitize_text_field';
					break;
			}
		}

		// Add this setting to the theme customizer
		$wp_customize->add_setting( $setting_details['id'], $setting );

	}

	/**
	 * Add a controller for a given setting.
	 *
	 * @since 3.0.0
	 *
	 * @param object $wp_customize    Theme customizer object.
	 * @param string $section_id      Parent section ID.
	 * @param array  $setting_details Additional setting details.
	 */
	public function add_control( $wp_customize = null, $section_id = '', $setting_details = array() ) {

		// Sanitize control details
		$control = array();
		$control['section']  = sanitize_text_field( $section_id );
		$control['label']    = isset( $setting_details['label'] ) ? sanitize_text_field( $setting_details['label'] ) : '';
		$control['type']     = isset( $setting_details['type'] ) ? sanitize_text_field( $setting_details['type'] ) : 'text';
		$control['priority'] = isset( $setting_details['priority'] ) ? absint( $setting_details['priority'] ) : 10;

		// If dealing with a multiple-choice setting, register all choices
		if (
			isset( $setting_details['choices'] )
			&& is_array( $setting_details['choices'] )
			&& in_array( $setting_details['type'], array( 'select', 'radio' ) )
		) {
			$control['choices'] = $setting_details['choices'];
		}

		// Add this controller to the theme customizer
		if ( in_array( $control['type'], array( 'text', 'checkbox', 'radio', 'select', 'dropdown-pages' ) ) ) {
			$wp_customize->add_control( $setting_details['id'], $control );
		} else {
			switch ( $setting_details['type'] ) {
				case 'color':
					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_details['id'], $control ) );
					break;
				case 'upload':
					$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, $setting_details['id'], $control ) );
					break;
				case 'image':
					$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $setting_details['id'], $control ) );
					break;
				case 'textarea':
					$wp_customize->add_control( new SBX_Customize_Textarea_Control( $wp_customize, $setting_details['id'], $control ) );
					break;
			}
		}

	}

	/**
	 * Enqueue Customizer JS.
	 *
	 * @since  3.0.0
	 */
	function customize_preview_int() {
		wp_enqueue_script( 'sb_customizer', SBX_JS . '/customizer.js', array( 'customize-preview' ), SBX_VERSION, true );
		wp_localize_script( 'sb_customizer', 'sb_customizer', $this->get_settings_js() );
	}

	/**
	 * Build array of JS-specific options.
	 *
	 * @since  3.0.0
	 *
	 * @return array Settings data for customizer javascript.
	 */
	function get_settings_js() {

		// Initialize JS settings array
		$js_settings = array();

		// Pull back all registered settings
		$customizer_settings = apply_filters( 'sb_customizer_settings', array() );

		// Extract the JS-specific data
		foreach ( $customizer_settings as $section ) {
			foreach ( $section['settings'] as $setting ) {
				// Only include a setting if it has a js_callback and css_selector
				if ( isset( $setting['js_callback'] ) && isset( $setting['css_selector'] ) ) {
					$js_settings[ $setting['id'] ]['control']     = $setting['id'];
					$js_settings[ $setting['id'] ]['js_callback'] = $setting['js_callback'];
					$js_settings[ $setting['id'] ]['selector']    = $setting['css_selector'];
				}
			}
		}

		// Return all JS settings
		return $js_settings;
	}

}
$GLOBALS['startbox']->customizer = new SBX_Customizer;

// Make sure WP_Customize_Control is available
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Register textarea controller for the theme customizer
	 *
	 * @subpackage Classes
	 * @link http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer
	 * @since 3.0.0
	 */
	class SBX_Customize_Textarea_Control extends WP_Customize_Control {

		/**
		 * @access public
		 * @since 3.0.0
		 * @var string The type of form element being generated.
		 */
		public $type = 'textarea';

		/**
		 * Overrides the render_content() function in the parent class
		 *
		 * @since 3.0.0
		 */
		public function render_content() {
			?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
				</label>
			<?php
		}
	}
}

/**
 * Base class for handling input sanitization
 *
 * @subpackage Classes
 * @since 3.0.0
 */
class SBX_Sanitization {

	/**
	 * Allowed inputs
	 *
	 * @since 3.0.0
	 * @var array
	 */
	private $valid;

	/**
	 * Instantiation method, sets up valid inputs
	 *
	 * @since 3.0.0
	 * @param array $valid An array of valid inputs
	 */
	function __construct( $valid = array() ) {
		$this->valid = is_array( $valid ) ? $valid : array();
	}

	/**
	 * Sanitizes multiple choice inputs
	 *
	 * Sanitizes select and radio inputs based on the valid
	 * "options" array set in the original options array
	 *
	 * @since  3.0.0
	 * @param  string $input Input data to be sanitized
	 * @param  array  $valid Array of allowed values.
	 * @return string        Valid option from $valid array matching $input, otherwise null.
	 */
	public function sanitize_multiple_choice( $input = '' ) {

		if ( array_key_exists( $input, $this->valid ) )
			return $input;
		else
			return null;

	}

	/**
	 * Sanitizes checkbox (boolean) inputs
	 *
	 * @since  3.0.0
	 * @param  string $input Input data to be sanitized
	 * @return string        Returns the $valid string if equal to $input, otherwise null.
	 */
	public function sanitize_checkbox( $input = '' ) {
		return ( ! empty( $input ) ) ? 'true' : 'false';
	}

	/**
	 * Sanitizes HTML input
	 *
	 * @since  3.0.0
	 * @param  string $input Input data to be sanitized
	 * @return string        Sanitized HTML
	 */
	public function sanitize_html( $input = '' ) {
		return wp_kses_post( force_balance_tags( $input ) );
	}

	/**
	 * Sanitizes integer input
	 *
	 * @since  3.0.0
	 * @param  string $input Input data to be sanitized
	 * @return integer       Returns the $valid string after sanitization.
	 */
	public function sanitize_integer( $input = '' ) {
		return is_numeric( $input ) ? intval( $input ) : absint( $input );
	}

}

/**
 * Helper for get_theme_mod() to automatically retrieve defaults
 *
 * Looks first for the setting, then for the passed default,
 * finally for a default set in sb_customizer_settings
 *
 * @since  3.0.0
 * @param  string $setting The setting to retrieve
 * @param  string $default A specific default to use if no data exists
 * @return string          The setting data, or a default
 */
function sbx_get_theme_mod( $setting = '', $default = '' ) {

	// Attempt to grab the setting from the DB
	$output = get_theme_mod( $setting, $default );

	// If we have no output, attempt to pull back the default from sb_customizer_settings
	if ( empty( $output ) ) {

		// Pull back our customizer settings array
		$customizer_settings = apply_filters( 'sb_customizer_settings', array() );

		// Get only the settings from our array,
		// and loop through until we find this setting
		$setting_fields = wp_list_pluck( $customizer_settings, 'settings' );
		foreach ( $setting_fields as $section ) {
			foreach ( $section as $field ) {
				if ( $setting == $field['id'] ) {
					$output = $field['default'];
				}
			}
		}

	}

	return $output;
}
