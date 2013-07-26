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
if ( ! current_theme_supports( 'sb-customizer' ) ) return;

/**
 * Base class for supplimenting theme customizer
 *
 * @subpackage Classes
 * @since 3.0.0
 */
class SB_Customizer {

	/**
	 * Instantiation method
	 *
	 * @since 3.0.0
	 */
	function __construct() {

		// Register a customizer menu under Appearance
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Render all of our customizer settings and sections
		add_action( 'customize_register', array( $this, 'customize_register' ) );

	}

	/**
	 * Register an admin menu item for the theme customizer
	 *
	 * @since 3.0.0
	 */
	function admin_menu() {
	    add_theme_page( __( 'Customize', 'startbox' ), __( 'Customize', 'startbox' ), 'edit_theme_options', 'customize.php' );
	}

	/**
	 * Displays customizer options
	 *
	 * Creates customizer options based on the customizer array,
	 * usually set in the theme's functions.php file.
	 *
	 * @since 3.0.0
	 * @param object $wp_customize The WordPress theme customizer object.
	 */
	public function customize_register( $wp_customize = null ) {

		// Grab the currently registered sections
		$sections = $wp_customize->sections();

		// Pull back our registered settings
		$customizer_settings = apply_filters( 'sb_customizer_settings', array() );

		// If we have any settings, loop through and register each
		if ( is_array( $customizer_settings ) && ! empty( $customizer_settings ) ) {
			foreach ( $customizer_settings as $section_id => $section ) {

				// Add the customizer section, if it doesn't exist already
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
	 * Add a customizer section to the theme customizer
	 *
	 * @since 3.0.0
	 * @param object $wp_customize The WordPress theme customizer object.
	 * @param string $section_id   The unique name for this section
	 * @param array  $section      Section specific data used to add the section to the customizer.
	 */
	public function add_section( $wp_customize = null, $section_id = '', $section = array() ) {

		// Setup our section details with basic sanitization
		$section_details                   = array();
		$section_details['title']          = sanitize_text_field( $section['title'] );
		$section_details['description']    = ! empty( $section['description'] ) ? esc_html( $section['description'] ) : '';
		$section_details['priority']       = ! empty( $section['priority'] ) ? absint( $section['priority'] ) : 10;
		$section_details['capability']     = ! empty( $section['capability'] ) ? sanitize_text_field( $section['capability'] ) : 'edit_theme_options';
		$section_details['theme_supports'] = ! empty( $section['theme_supports'] ) ? sanitize_text_field( $section['theme_supports'] ) : null;

		// Adds settings section to theme customizer.
		$wp_customize->add_section( $section_id, $section_details );

	}

	/**
	 * Adds individual settings and controls to the theme customizer
	 *
	 * @since 3.0.0
	 * @param object $wp_customize    The WordPress theme customizer object
	 * @param string $section_id      The section in which this setting belongs
	 * @param array  $setting_details The setting details
	 */
	public function add_setting( $wp_customize = null, $section_id = '', $setting_details = array() ) {

		// Setup our setting details with basic sanitization
		$setting                         = array();
		$setting['default']              = ! empty( $setting_details['default'] ) ? $setting_details['default'] : null;
		$setting['capability']           = ! empty( $setting_details['capability'] ) ? $setting_details['capability'] : 'edit_theme_options';
		$setting['theme_supports']       = ! empty( $setting_details['theme_supports'] ) ? $setting_details['theme_supports'] : null;
		$setting['transport']            = ! empty( $setting_details['transport'] ) ? $setting_details['transport'] : 'refresh';
		$setting['sanitize_js_callback'] = ! empty( $setting_details['sanitize_js_callback'] ) ? $setting_details['sanitize_js_callback'] : null;

		// Setup the setting sanitization callback
		if ( ! empty( $setting_details['sanitize_callback'] ) ) {
			$setting['sanitize_callback'] = $setting_details['sanitize_callback'];
		} else {

			// If we don't specify what kind of data
			// is valid, use the setting's type
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
					$setting['sanitize_callback'] = array( new SB_Sanitization( $setting_details['choices'] ), 'sanitize_multiple_choice' );
					break;
				case 'checkbox':
					$setting['sanitize_callback'] = array( new SB_Sanitization(), 'sanitize_html' );
					break;
				case 'html':
					$setting['sanitize_callback'] = array( new SB_Sanitization(), 'sanitize_html' );
					break;
				case 'integer':
					$setting['sanitize_callback'] = array( new SB_Sanitization(), 'sanitize_integer' );
					break;
				case 'color':
					$setting['sanitize_callback'] = 'sanitize_hex_color';
					break;
				default:
					$setting['sanitize_callback'] = 'sanitize_text_field';
					break;
			}
		}

		// Adds setting to theme customizer.
		$wp_customize->add_setting( $setting_details['id'], $setting );

	}

	/**
	 * Add a controller for a given setting
	 *
	 * @since 3.0.0
	 * @param object $wp_customize    The WordPress theme customizer object
	 * @param string $section_id      The section in which this control belongs
	 * @param array  $setting_details The setting details
	 */
	public function add_control( $wp_customize = null, $section_id = '', $setting_details = array() ) {

		// Defines array to pass to add_control().
		$control = array();
		$control['section']  = sanitize_text_field( $section_id );
		$control['label']    = isset( $setting_details['label'] ) ? sanitize_text_field( $setting_details['label'] ) : '';
		$control['type']     = isset( $setting_details['type'] ) ? sanitize_text_field( $setting_details['type'] ) : 'text';
		$control['priority'] = isset( $setting_details['priority'] ) ? absint( $setting_details['priority'] ) : 10;

		// Set setting choices if they exist and this is a multiple choice setting
		if (
			isset( $setting_details['choices'] )
			&& is_array( $setting_details['choices'] )
			&& in_array( $setting_details['type'], array( 'select', 'radio' ) )
		) {
			$control['choices'] = $setting_details['choices'];
		}

		// Register the setting control
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
					$wp_customize->add_control( new SB_Customize_Textarea_Control( $wp_customize, $setting_details['id'], $control ) );
					break;
			}
		}
	}
}
$GLOBALS['startbox']->customizer = new SB_Customizer;

// Make sure WP_Customize_Control is available
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Register textarea controller for the theme customizer
	 *
	 * @subpackage Classes
	 * @link http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer
	 * @since 3.0.0
	 */
	class SB_Customize_Textarea_Control extends WP_Customize_Control {

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
class SB_Sanitization {

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
function sb_get_theme_mod( $setting = '', $default = '' ) {

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
