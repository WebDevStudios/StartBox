<?php

/**
 * StartBox Options API
 *
 * The Options API is what allows the Theme Options page to be easily extended and altered.
 * This file contains the necessary helper classes for building your own Theme Options.
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @package StartBox
 * @subpackage Options
 */

/**
 * Create an options panel and register new options.
 *
 * This is the base settings class. When adding your own options panel to a child theme
 * you should extended this class and override the default settings. Note: Do NOT override
 * the __contstruct or _init methods.
 *
 * @since 1.0.0
 *
 * @uses sb_get_option()
 * @uses sb_add_option()
 * @uses add_meta_box()
 * @uses add_action()
 *
 * @param string $name               Name of the options panel for use as metabox title.
 * @param string $slug               Nice-name of options panel, used for identifying when creating metabox.
 * @param string $location           The column in which to add the metabox, primary or secondary. Default is secondary.
 * @param string $priority           Priority for displaying the metabox, high, default or low. Default is default.
 * @param array  $options            The options to be added.
 * @param string $hide_ui_if_cannot  Lowest capability a user must have in order to see this metabox.
 */
class SB_Settings {

	// Setup our variables
	public $name = 'Settings Panel';	// Name for your options panel, displays as a title
	public $slug = 'settings_panel';	// Nice-name for your options panel
	public $page = 'sb_admin';			// Page for your settings panel: sb_admin or sb_style
	public $location = 'secondary';		// Column for your settings panel: primary or secondary
	public $priority = 'default';		// Priority for your settings panel: high, low, default
	public $options = array();			// A multi-dimensional array for populating the settings panel.
	public $hide_ui_if_cannot = NULL;	// Lowest capability a user must have in order to see this metabox

	// Outputting settings as necessary. Note: you can add as many custom functions as you need.
	public function output() {}

	// For hooking all your functions elsewhere. Note: When referencing the function in add_action() use: array( &$this, 'function_name' )
	public function hooks() {}

	// This makes everything work. Do not override this method.
	public function __construct() {
		if (is_network_admin()) { return; } // skip the rest if it's a network admin, no need to continue
		add_action( 'admin_init', array( $this, '_init' ), 5 );
		add_action( 'init', array( $this, 'hooks' ), 9 );
	}

	// This hooks the metabox method. Do not override this method.
	public function _init() {

		global $sb_admin, $sb_style;
		$this->page = ($this->page == 'sb_style') ? $sb_style : $sb_admin;

		add_action( 'load-'. $this->page, array( $this, '_metaboxes' ) );

	}

	// This creates the metabox. Do not override this method.
	public function _metaboxes() {

		if ( empty( $this->hide_ui_if_cannot ) || current_user_can( $this->hide_ui_if_cannot ) )
			add_meta_box( $this->slug, $this->name, array( $this, 'admin_form' ), $this->page, $this->location, $this->priority);

	}

	// This makes errors more happy and less desctructive
	public function __call($method, $args) { wp_die( "Your new settings class, <b>" . $this->name . "</b>, is trying to call an unknown method: " . $method ); }

	// Create the options form to wrap inside metabox. Only override this in your own class if you want to create your own form and do a butt-ton of work.
	public function admin_form( $options ) {

		// Get options from object if not passed directly
		$options = ( ! empty( $options ) && is_array( $options ) )
			? $options
			: $this->options;

		// Initialize output
		$output = '';

		// Output each option
		foreach ( $options as $option_id => $option ) {

			// Setup option defaults
			$defaults = array(
				'id'    => $option_id, // Option ID.
				'value' => sb_get_option( $option_id ), // Option value.
			);

			// Parse option values against defaults
			$option = wp_parse_args( $option, $defaults );

			// Add option markup to output
			switch ( $option['type'] ) {
				case 'intro' :
				case 'text' :
				case 'textarea' :
				case 'checkbox' :
				case 'radio' :
				case 'select' :
				case 'enable_select' :
				case 'upload' :
				case 'wysiwyg' :
				case 'color' :
					$output .= SB_Input::$option['type']( $option );
					break;
				case 'divider' :
					$output .= "<hr/>\n";
					break;
				default :
					break;
			}

		}

		// Finally, echo our output
		echo $output;
	}

}

/**
 * StartBox Input Class.
 *
 * Generates markup for various option input types.
 *
 * @since 1.0.0
 */
class SB_Input {

	/**
	 * Helper function for outputting descriptive text for each option.
	 *
	 * @param  string $desc The descriptive text.
	 * @return string       The concatenated descriptive text.
	 */
	public static function descriptive_text( $desc ) {
		return '<span class="description"> ' . $desc . ' </span>'."\n";
	}

	/**
	 * Introduction setting.
	 *
	 * @param  array  $args  The array of arguments for building this input.
	 * @return string        The concatenated introduction output.
	 */
	public static function intro( $args ='' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',  // Unique ID for this option
			'label'		=> '',  // The content to display as the input label
			'desc'		=> '',  // Descriptive text
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output = '<h4>' . $label . '</h4>'."\n";
		$output .= '<span class="description">' . $desc . '</span>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Text Input.
	 *
	 * @param  array  $args The array of arguments for building this input.
	 * @return string       The concatenated text option output.
	 */
	public static function text( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',          // Unique ID for this option
			'class'		=> '',          // Optional CSS classes for input
			'label'		=> '',          // The content to display as the input label
			'value'		=> '',          // The option value
			'desc'		=> '',          // Descriptive text
			'size'		=> 'default',   // The size of the input (small, default, large; default: default)
			'align'		=> '',          // The alignment of the input (left, right; default: left)
			'before'	=> '',          // Custom content to place before the input
			'after'		=> ''           // Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		if ($label) $output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label>';
		$output .= $before;
		$output .= '<p class="' . esc_attr( $args['id'] ) . ' ' . esc_attr( $align ) . '"><input type="text" value="' . esc_attr( $value ) . '" name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '" class="' . esc_attr( 'option-field-' . esc_attr( $size ) . ' ' . $class ) . '" /></p>';
		$output .= $after;
		if ($desc) $output .= sb_input::descriptive_text( $desc );

		// Return our output
		return $output;
	}

	/**
	 * Textarea Input.
	 *
	 * @param  array  $args  The array of arguments for building this input.
	 * @return string        The concatenated textarea option output.
	 */
	public static function textarea( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',      // Unique ID for this option
			'label'		=> '',      // The content to display as the input label
			'value'		=> '',      // The option value
			'desc'		=> '',      // Descriptive text
			'before'	=> '',      // Custom content to place before the input
			'after'		=> ''."\n"  // Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before;
		$output .= '<p class="' . esc_attr( $args['id'] ) . '"><label for="' . esc_attr( $sb_id ) . '">' . $label . '</label></p>'."\n";
		$output .= '<textarea name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '">' . esc_textarea( $value ) . '</textarea>'."\n";
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Checkbox Input.
	 *
	 * @param  array  $args  The array of arguments for building this input.
	 * @return string        The concatenated checkbox option output.
	 */
	public static function checkbox( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',      // Unique ID for this option
			'label'		=> '',      // The content to display as the input label
			'value'		=> '',      // The option value
			'desc'		=> '',      // Descriptive text
			'align'		=> '',      // Alignment for input
			'before'	=> '',      // Custom content to place before the input
			'after'		=> ''."\n"  // Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before ;
		$output .= '<p class="' . esc_attr( $args['id'] ) . ' ' . esc_attr( $align ) . '">';
		$output .= '<label for="' . esc_attr( $sb_id ) . '" class="' . esc_attr( $align ) . '">';
		$output .= '<input type="checkbox" class="checkbox" id="' . esc_attr( $sb_id ) . '" name="' . esc_attr( $sb_id ) . '" value="true" ' . checked( $value, 'true', false ) . ' /> ';
		$output .= $label;
		$output .= '</label>'."\n";
		$output .= '</p>';
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Radio Input.
	 *
	 * @param  array  $args   The array of arguments for building this input.
	 * @return string         The concatenated radio option output.
	 */
	public static function radio( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',      // Unique ID for this option
			'label'		=> '',      // The content to display as the input label
			'value'		=> '',      // The option value
			'desc'		=> '',      // Descriptive text
			'options'	=> array(), // Array of radio options ('id' => 'value')
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $args['id'] ) . ' ' . esc_attr( $align ) . '">';
		$output .= $label . '<br>'."\n";
		foreach ( $options as $option_id => $option ) {
			$output .= '<input type="radio" id="' . esc_attr( $id ) . '-' . esc_attr( $option_id ) . '" value="' . esc_attr( $option_id ) . '" name="' . esc_attr( $sb_id ) . '" ' . checked( $value, $option_id, false ) . ' />';
			$output .= '<label for="' . esc_attr( $id ) . '-' . esc_attr( $option_id ) . '">' . $option . '</label><br/>'."\n";
		}
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= '</p>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Select Input.
	 *
	 * @param  array  $args  An array of arguments.
	 * @return string        The concatenated select option output.
	 */
	public static function select( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> 'option-select', // The unique ID for this input
			'label'		=> 'Select',        // The content to use as the input label
			'value'		=> '',              // The option value
			'desc'		=> '',              // The content to display as a small descriptive text
			'options'	=> '',              // String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
			'size'		=> 'large',         // The size of this input (small, default, large; default: large)
			'align'		=> 'right',         // The alignment for this input (left, right; default: right)
			'order_by'	=> 'post_date',     // For post options: how posts should be ordered
			'order'		=> 'DESC',          // For post options: the order to display the results
			'limit'		=> 30,              // For post and page options: how many results to retrieve
			'before'	=> '',              // Custom content to place before the input
			'after'		=> ''."\n"          // Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before;
		$output .= ($label) ? '<p class="' . esc_attr( $args['id'] ) . ' ' . esc_attr( $align ) . '"><label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label> '."\n" : '';
		if ( 'categories' == $options )
			$output .= wp_dropdown_categories( array(
				'echo'		=> 0,
				'name'		=> $sb_id,
				'id'		=> $sb_id,
				'class'		=> 'option-select-' . esc_attr( $size ) . ' ' . $align,
				'selected'	=> $value,
				'show_option_none' => 'Select a Category'
			) );
		elseif ( 'pages' == $options )
			$output .= wp_dropdown_pages( array(
				'echo'		=> 0,
				'name'		=> $sb_id,
				'id'		=> $sb_id,
				'selected'	=> $value,
				'show_option_none' => 'Select a Page'
			) );
		elseif ( 'posts' == $options )
			$output .= sbx_dropdown_posts( array(
				'echo'		=> 0,
				'name'		=> $sb_id,
				'id'		=> $sb_id,
				'class'		=> 'option-select-' . esc_attr( $size ) . ' ' . $align,
				'selected'	=> $value,
				'order_by'	=> $order_by,
				'order'		=> $order,
				'limit'		=> $limit,
				'show_option_none' => 'Select a Post'
			) );
		elseif ( $options ) {
			$output .= '<select id="' . esc_attr( $sb_id ) . '" name="' . esc_attr( $sb_id ) . '" class="option-select-' . esc_attr( $size ) . ' ' . esc_attr( $align ) . '">'."\n";
			foreach ( $options as $option_id => $option ) {
				$output .= '<option value="' . esc_attr( $option_id ) . '" ' . selected( $value, $option_id, false ) . '>' . $option . '</option>'."\n";
			}
			$output .= '</select></p>'."\n";
		}
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= '';
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Enable Select Input - a select input with a checkbox input.
	 *
	 * @param  array  $args  An array of arguments.
	 * @return string        The concatenated select option output.
	 */
	public static function enable_select( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> 'option-select',         // The unique ID for this input
			'label'		=> 'Select',                // The content to use as the input label
			'value'		=> '',                      // The option value
			'desc'		=> '',                      // The content to display as a small descriptive text
			'options'	=> '',                      // String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
			'size'		=> 'large',                 // The size of this input (small, default, large; default: large)
			'align'		=> 'right',                 // The alignment for this input (left, right; default: right)
			'before'	=> '<span class="right">',	// Custom content to include before the input
			'after'		=> '</span>',               // Custom costent to include after the input
			'order_by'	=> 'post_date',             // For post options: how posts should be ordered
			'order'		=> 'DESC',                  // For post options: the order to display the results
			'limit'		=> 30                       // For post and page options: how many results to retrieve
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output .= sb_input::checkbox( array(
			'id'	=> $id . '-enabled',
			'value'	=> sb_get_option( $id . '-enabled' ),
			'label'	=> 'Enable',
			'align'	=> 'left',
			'after' => ' '
			));
		$output .= sb_input::select( $r );
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= '</p>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Upload Input.
	 *
	 * @param  array  $args  An array of arguments.
	 * @return string        The concatenated upload option output.
	 */
	public static function upload( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'    => '', // The unique ID for this input
			'label' => '', // The content to use as the input label
			'value' => '', // The option value
			'desc'  => ''  // The content to display as a small descriptive text
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = SBX_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Load up the dependinces for WP's media browser, if they're not already loaded
		wp_enqueue_media();

		// Concatenate our output
		$output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label>';
		$output .= '<p class="imagepickerinput ' . esc_attr( $args['id'] ) . '">';
		$output .= '<input type="text" value="' . esc_attr( $value ) . '" name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '" class="uploadinput"/><br>' ;
		$output .= '<a class="previewlink button" href="' . esc_attr( $value ) . '" target="_blank">' . __( 'Preview', 'startbox' ) . '</a>&nbsp;';
		$output .= '<a class="chooselink button" href="#">' . __( 'Upload/Choose File', 'startbox' ) . '</a>';
		$output .= '</p>';
		$output .= '<p><span class="description"> ' . $desc . ' <span class="uploadresult"></span></span></p>'."\n";
		$output .= ''."\n";

		// Return our output
		return $output;
	}

	/**
	 * WYSIWYG Options.
	 *
	 * @param  array  $args  An array of arguments.
	 * @return string        The concatenated WYSIWYG option output.
	 */
	public static function wysiwyg( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',      // Unique ID for this option
			'label'		=> '',      // The content to display as the input label
			'value'		=> '',      // The option value
			'desc'		=> '',      // Descriptive text
			'options'	=> array(   // Options specific to the wp_editor() function
				'textarea_name'	=> SBX_OPTIONS . '[' . esc_attr( $args['id'] ) . ']',
				'media_buttons'	=> false,
				'teeny'			=> true
			)
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $args['id'] ) . ' ' . esc_attr( $align ) . '"><label for="' . esc_attr( $sb_id ) . '">' . $label . '</label></p>'."\n";
		ob_start();
		wp_editor( $value, $id, $options );
		$output .= ob_get_clean();
		if ($desc) $output .= sb_input::descriptive_text( $desc );

		// Return our output
		return $output;
	}

	/**
	 * Color Input.
	 *
	 * @param  array  $args  An array of arguments.
	 * @return string        The concatenated color option output.
	 */
	public static function color( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',
			'label'		=> '',
			'value'		=> '',
			'desc'		=> '',
			'class'		=> 'colorinput',
			'size'		=> 'small',
			'align'		=> 'right',
			'before'	=> '<span class="colorpickerinput">',
			'after'		=> '<span class="colorselector"><span></span></span></span>'
		);

		// Get our variables ready to go
		$args = wp_parse_args( $args, $defaults );

		// Return our output
		return sb_input::text( $args );
	}

}
$sb_input = new sb_input;

/**
 * SB Settings Factory.
 *
 * The processor for adding/removing option panels with the Theme Options page.
 *
 * @since 1.0.0
 */
class sb_settings_factory {

	// Setup our variables
	public $settings = array();
	public $defaults = array(
		'sb_analytics_settings',
		'sb_layout_settings',
		'sb_content_settings',
		'sb_footer_settings',
		'sb_favicon_settings',
		'sb_settings_help',
		'sb_navigation_settings',
		'sb_seo_settings',
		'sb_upgrade_settings'
	);

	// Register a new options panel
	public function register($class_name) {
		$this->settings[$class_name] = new $class_name();
	}

	// Unregister an options panel
	public function unregister($class_name) {
		if ( isset($this->settings[$class_name]) ) {
			global $sb_admin;
			remove_meta_box( $this->settings[$class_name]->slug, $sb_admin, $this->settings[$class_name]->location );
			unset($this->settings[$class_name]);
		}
	}

	// Unregister ALL option panels
	public function unregister_all_defaults($defaults) {
		$defaults = $this->defaults;
		foreach( $defaults as $default ) {
			sb_unregister_settings( $default );
		}
	}
}
global $sb_settings_factory;
$sb_settings_factory = new sb_settings_factory;

// Registers Settings
function sb_register_settings($class_name) {
	global $sb_settings_factory;
	$sb_settings_factory->register($class_name);
}

// Unregisters Settings - Run within a function hooked to admin_init to unregister default settings.
function sb_unregister_settings($class_name) {
	global $sb_settings_factory;
	$sb_settings_factory->unregister($class_name);
}

/**
 * Remove all default StartBox option panels.
 *
 * @since 1.0.0
 */
function sb_remove_default_settings() {
	global $sb_settings_factory;
	add_action( 'admin_init', array($sb_settings_factory, 'unregister_all_defaults' ) );
}
if ( defined('SB_REMOVE_DEFAULT_SETTINGS') ) { sb_remove_default_settings(); }

/**
 * Sets the Default settings for all StartBox options.
 *
 * @since 1.0.0
 */
function sb_set_default_options() {

	// Grab our various settings
	global $sb_settings_factory;
	$defaults = $current = get_option( SBX_OPTIONS );
	$settings = $sb_settings_factory->settings;

	// Loop through all settings panels
	foreach( $settings as $setting ){

		// Grab the options for the panel
		$options = $setting->options;

		// Loop through each option
		foreach( $options as $option_id => $option ) {

			// If the setting has a default, set it
			if ( isset( $option['default'] ) )
				$defaults[ $option_id ] = $option['default'];
		}
	}

	// If this was a reset, drop the reset value
	if ( isset( $current['reset'] ) && isset( $defaults['reset'] ) )
		$defaults['reset'] = false;

	// Save the options to the database, Allow child themes to filter what defaults are returned
	update_option( SBX_OPTIONS, apply_filters( 'sb_option_defaults', $defaults ) );

}
add_action( 'sb_install', 'sb_set_default_options' );

/**
 * Adds an option to the options db.
 *
 * @since 1.0.0
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name. Must be unique.
 * @param mixed  $value Option Value.
 * @return bool  True on success, false if the option already exists.
 */
function sb_add_option( $name, $value ) {
	$options = get_option( SBX_OPTIONS );
	if ( $options and !isset($options[$name]) ) {
		$options[$name] = $value;
		return update_option( SBX_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Updates an option to the options db.
 *
 * @since 1.0.0
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name  Option Name. Must be unique.
 * @param mixed  $value Option Value.
 * @return bool  true|false
 */
function sb_update_option( $name, $value ) {
	$options = get_option( SBX_OPTIONS );
	if ( !isset($options[$name]) || $value != $options[$name] ) {
		$options[$name] = $value;
		return update_option( SBX_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Returns the value of an option from the db if it exists.
 *
 * @since 1.0.0
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name.
 * @return mixed Returns the option's value if it exists, false if it doesn't.
 */
function sb_get_option( $name ) {
	$options = get_option( SBX_OPTIONS );
	if ( is_array($options) && isset($options[$name]) ) {
		return $options[$name];
	} else {
		return false;
	}
}

/**
 * Deletes an option from the options db.
 *
 * @since 1.0.0
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name. Must be unique.
 * @return bool  true|false
 */
function sb_delete_option( $name ) {
	$options = get_option( SBX_OPTIONS );
	if ( $options[$name] ) {
		unset( $options[$name] );
		return update_option( SBX_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Add a new option to an existing metabox.
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @since 1.0.0
 *
 * @param string $metabox the name of the metabox where the option will appear.
 * @param string $option_name the name of the option to add.
 * @param array  $args the arguments to pass through the Options API.
 *
 */
function sb_register_option( $metabox, $option_name, $args ) {
	global $sb_settings_factory;
	$sb_settings_factory->settings[$metabox]->options[$option_name] = $args ;
}
/**
 * Remove an existing option.
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @since 2.4.9
 *
 * @param string $metabox the name of the metabox where the option exists.
 * @param string $option the name of the option to remove.
 * @param mixed  $new_value Optional. Store a new, permanent value to the options table.
 *
 * @uses sb_update_option()
 *
 */
function sb_unregister_option( $metabox, $option, $new_value = '') {
	global $sb_settings_factory;

	// Remove the option if it exsits
	if ( isset($sb_settings_factory->settings[$metabox]->options[$option]) )
		unset( $sb_settings_factory->settings[$metabox]->options[$option] );

	// If we're setting a new, permanant value
	if ($new_value)
		sb_update_option( $option, $new_value);
}

/**
 * Helper function for easily removing actions hooked in via classes.
 *
 * @since 1.0.0
 *
 * @uses remove_action()
 *
 * @param string  $tag Hook name.
 * @param string  $class_name Name of class where $function_to_remove resides.
 * @param string  $function_to_remove The function to remove.
 * @param integer $priority Level of priority (default: 10).
 * @return bool   True on success, false if the function does not exist.
 */
//
function sb_remove_action( $tag, $class_name, $function_to_remove, $priority = 10 ) {
	if ($class_name) {
		global $sb_settings_factory;
		$function_to_remove = array( $sb_settings_factory->settings[$class_name], $function_to_remove);
	}
	return remove_action( $tag, $function_to_remove, $priority );
}

/**
 * Helper function for easily re-inserting actions hooked-in via classes.
 *
 * @since 1.0.0
 *
 * @uses add_action()
 *
 * @param string  $tag Hook name.
 * @param string  $class_name Name of class where $function_to_add resides.
 * @param string  $function_to_add The function to add.
 * @param integer $priority Level of priority (default: 10).
 * @return bool   True on success, false if the option already exists.
 */
function sb_add_action( $tag, $class_name, $function_to_add, $priority = 10 ) {
	if ($class_name) {
		global $sb_settings_factory;
		$function_to_add = array( $sb_settings_factory->settings[$class_name], $function_to_add);
	}
	return add_action( $tag, $function_to_add, $priority );
}
