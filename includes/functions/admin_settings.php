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
 * Create an options panel and register new options
 *
 * This is the base settings class. When adding your own options panel to a child theme
 * you should extended this class and override the default settings. Note: Do NOT override
 * the __contstruct or _init methods.
 *
 * @since 2.4.2
 *
 * @uses sb_get_option()
 * @uses sb_add_option()
 * @uses add_meta_box()
 * @uses add_action()
 *
 * @param string $name Name of the options panel for use as metabox title
 * @param string $slug Nice-name of options panel, used for identifying when creating metabox
 * @param string $location The column in which to add the metabox, primary or secondary. Default is secondary
 * @param string $priority Priority for displaying the metabox, high, default or low. Default is default.
 * @param array $options The options to be added. See http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 * @param string $hide_ui_if_cannot Lowest capability a user must have in order to see this metabox
 */
class sb_settings {

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

	// This creates the metabox. Do not override this method.
	public function _init() {
		global $sb_admin, $sb_style;
		if ( empty( $this->hide_ui_if_cannot ) || current_user_can( $this->hide_ui_if_cannot ) ) {
			$this->page = ($this->page == 'sb_style') ? $sb_style : $sb_admin;
			add_meta_box( $this->slug, $this->name, array( $this, 'admin_form' ), $sb_admin, $this->location, $this->priority);
		}
	}

	// This makes errors more happy and less desctructive
	public function __call($method, $args) { wp_die( "Your new settings class, <b>" . $this->name . "</b>, is trying to call an unknown method: " . $method ); }

	// Create the options form to wrap inside metabox. Only override this in your own class if you want to create your own form and do a butt-ton of work.
	public function admin_form( $options ) {
		$options = ($options) ? $options : $this->options;

	    $output = '';
	    foreach ($options as $id => $settings) {

	    	// Assume an empty value for every possible setting
			$defaults = array(
				'label'		=> '',		// The label content
				'desc'		=> '',		// Additional descriptive text
				'class'		=> '',		// The option class
				'align'		=> '',		// The alignment for this input (left, right)
				'size'		=> '',		// The size of this input (small, default, large)
				'before'	=> '',		// Custom content to include before the input
				'after'		=> '',		// Custom costent to include after the input
				'options'	=> array(),	// String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
				'order_by'	=> '',		// For post options: the order to display the results
				'order'		=> '',		// For post options: the order to display the results
				'limit'		=> '', 		// For post and page options: how many results to retrieve
				'suggested'	=> '',		// For post options: the order to display the results
				'position'	=> array(),	// Array of possible menue positions
				'extras'	=> array(),	// Array of additional menu extras
			);

			// Parse the values we were given and extract them for individual use
			$r = wp_parse_args( $settings, $defaults );
			extract( $r, EXTR_OVERWRITE );

			// Grab our current setting value
			$value = sb_get_option( $id );

			// Loop through each option type and begin concatenate our form elements
			if ( 'divider' == $settings['type'] ) $output .= '<hr/>'."\n";
			elseif ( 'intro' == $settings['type'] ) $output .= sb_input::intro( array(
						'id'		=> $id,
						'label'		=> $label,
						'desc'		=> $desc
					) );
			elseif ( 'text' == $settings['type'] ) $output .= sb_input::text( array(
						'id'		=> $id,
						'class'		=> $class,
						'label'		=> $label,
						'value' 	=> $value,
						'desc'		=> $desc,
						'size' 		=> $size,
						'align'		=> $align,
						'before'	=> $before,
						'after'		=> $after
					) );
			elseif ( 'textarea' == $settings['type'] ) $output .= sb_input::textarea( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc
					) );
			elseif ( 'checkbox' == $settings['type'] ) $output .= sb_input::checkbox( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'align' 	=> $align
					) );
			elseif ( 'radio' == $settings['type'] ) $output .= sb_input::radio( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'options'	=> $options
					) );
			elseif ( 'select' == $settings['type'] ) $output .= sb_input::select( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'options'	=> $options,
						'size'		=> $size,
						'align'		=> $align,
						'order_by'	=> $order_by,
						'order'		=> $order,
						'limit'		=> $limit
					) );
			elseif ( 'enable_select' == $settings['type'] ) $output .= sb_input::enable_select( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'options'	=> $options,
						'size' 		=> $size,
						'align'		=> $align,
						'order_by'	=> $order_by,
						'order'		=> $order,
						'limit'		=> $limit
					) );
			elseif ( 'layout' == $settings['type'] ) $output .= sb_input::layout( array(
						'id' 		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'options'	=> $options
					) );
			elseif ( 'navigation' == $settings['type'] ) $output .= sb_input::navigation( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'size'		=> 'large',
						'align'		=> 'right',
						'position'	=> $position,
						'extras'	=> $extras
					) );
			elseif ( 'upload' == $settings['type'] ) $output .= sb_input::upload( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'suggested'	=> $suggested
					) );
			elseif ( 'logo' == $settings['type'] ) $output .= sb_input::logo( array(
						'id'		=> $id,
						'label'		=> $label,
						'desc'		=> $desc
					) );
			elseif ( 'wysiwyg' == $settings['type'] ) $output .= sb_input::wysiwyg( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc,
						'options'	=> $options
					) );
			elseif ( 'color' == $settings['type'] ) $output .= sb_input::color( array(
						'id'		=> $id,
						'label'		=> $label,
						'value'		=> $value,
						'desc'		=> $desc
					) );
			elseif ( 'background' == $settings['type'] ) $output .= sb_input::background( array(
						'id'		=> $id,
						'label'		=> $label,
						'desc'		=> $desc
				) );

		}

		// Finally, echo our output
	    echo $output;
	}

}

/**
 * StartBox Input Class
 *
 * Creates input fields for use in sb_settings classes. Currently used to produce the following:
 *
 * @since 2.4.4
 */
class sb_input {

	/**
	 * Helper function for outputting descriptive text for each option
	 *
	 * @param  string $desc The descriptive text
	 * @return string       The concatenated descriptive text
	 */
	public function descriptive_text( $desc ) {
		return '<br/><span class="desc"> ' . $desc . ' </span>'."\n";
	}

	/**
	 * Introduction setting
	 *
	 * @param  array $args The array of arguments for building this input
	 * @return string        The concatenated introduction output
	 */
	public function intro( $args ='' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',			// Unique ID for this option
			'label'		=> '',			// The content to display as the input label
			'desc'		=> '',			// Descriptive text
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output = '<h4 id="' . esc_attr( $id ) . '" class="' . esc_attr( $id ) . '">' . $label . '</h4>'."\n";
		$output .= '<p class="' . esc_attr( $id ) . '">' . $desc . '</p>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Text Input
	 *
	 * @param  array $args The array of arguments for building this input
	 * @return string      The concatenated text option output
	 */
	public function text( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',			// Unique ID for this option
			'class'		=> '',			// Optional CSS classes for input
			'label'		=> '',			// The content to display as the input label
			'value'		=> '',			// The option value
			'desc'		=> '',			// Descriptive text
			'size'		=> 'default',	// The size of the input (small, default, large; default: default)
			'align'		=> 'left',		// The alignment of the input (left, right; default: left)
			'before'	=> '', 			// Custom content to place before the input
			'after'		=> ''			// Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $args['id'] ) . '">';
		if ($label) $output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label> ';
		$output .= '<span class="' .esc_attr( $align ) . '">';
		$output .= $before;
		$output .= '<input type="text" value="' . esc_attr( $value ) . '" name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '" class="' . esc_attr( 'option-field-' . esc_attr( $size ) . ' ' . $class ) . '" />';
		$output .= $after;
		$output .= '</span>';
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= '</p>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Textarea Input
	 *
	 * @param  array $args  The array of arguments for building this input
	 * @return string       The concatenated textarea option output
	 */
	public function textarea( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',			// Unique ID for this option
			'label'		=> '',			// The content to display as the input label
			'value'		=> '',			// The option value
			'desc'		=> '',			// Descriptive text
			'before'	=> '<p class="' . esc_attr( $args['id'] ) . '">', // Custom content to place before the input
			'after'		=> '</p>'."\n"	// Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before;
		$output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . '</label><br/>'."\n";
		$output .= '<textarea name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '">' . esc_textarea( $value ) . '</textarea>'."\n";
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Checkbox Input
	 *
	 * @param  array $args  The array of arguments for building this input
	 * @return string       The concatenated checkbox option output
	 */
	public function checkbox( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',			// Unique ID for this option
			'label'		=> '',			// The content to display as the input label
			'value'		=> '',			// The option value
			'desc'		=> '',			// Descriptive text
			'align'		=> 'left',		// Alignment for input
			'before'	=> '<p class="' . esc_attr( $args['id'] ) . '">', // Custom content to place before the input
			'after'		=> '</p>'."\n"	// Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before ;
		$output .= '<label for="' . esc_attr( $sb_id ) . '" class="' . esc_attr( $align ) . '"><input type="checkbox" class="checkbox" id="' . esc_attr( $sb_id ) . '" name="' . esc_attr( $sb_id ) . '" value="true" ' . checked( $value, 'true', false ) . ' /> ' . $label . '</label>'."\n";
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Radio Input
	 *
	 * @param  array $args  The array of arguments for building this input
	 * @return string         The concatenated radio option output
	 */
	public function radio( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',		// Unique ID for this option
			'label'		=> '',		// The content to display as the input label
			'value'		=> '',		// The option value
			'desc'		=> '',		// Descriptive text
			'options'	=> array(),	// Array of radio options ('id' => 'value')
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $id ) . '">';
		$output .= $label . '<br/>'."\n";
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
	 * Layout Options
	 *
	 * @param  string $id     Unique ID for this option
	 * @param  string $label  The content to use as the input label
	 * @param  string $value  The option value
	 * @param  string $desc   The content to display as a small descriptive text
	 * @param  array $options An array of selectable radio options
	 * @return string         The concatenated layout option output
	 */
	public function layout( $args = '' ) {

		// If themes don't support layouts, don't return any layout options
		if ( !current_theme_supports('sb-layouts') || $args['options'] == '')
			return $output;

		// Setup our defaults
		$defaults = array(
			'id'		=> '',		// Unique ID for this option
			'label'		=> '',		// The content to display as the input label
			'value'		=> '',		// The option value
			'desc'		=> '',		// Descriptive text
			'options'	=> array(),	// Array of radio options ('id' => 'value')
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $id ) . '">';
		$output .= $label . '<br/>'."\n";
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		foreach ( $options as $layout => $option ) {
			$output .= '<div class="layout-container">';
			$output .= '<label for="' . esc_attr( $id ) . '-' . esc_attr( $layout ) . '">';
			$output .= '<input type="radio" id="' . esc_attr( $id ) . '-' . esc_attr( $layout ) . '" value="' . esc_attr( $layout ) . '" name="' . esc_attr( $sb_id ) . '" ' . checked( $value, $layout, false ) . ' />';
			$output .= '<img src="' . $option['img'] .'" alt="' . esc_attr( $option['label'] ) . '"  width="50" height="40" />';
			$output .= '</label>';
			$output .= '</div>';
		}
		$output .= '</p>'."\n";
		$output .= '<hr/>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Select Input
	 *
	 * @param  array $args An array of arguments
	 * @return string      The concatenated select option output
	 */
	public function select( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> 'option-select',	// The unique ID for this input
			'label'		=> 'Select',		// The content to use as the input label
			'value'		=> '',				// The option value
			'desc'		=> '',				// The content to display as a small descriptive text
			'options'	=> '',				// String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
			'size'		=> 'large',			// The size of this input (small, default, large; default: large)
			'align'		=> 'right',			// The alignment for this input (left, right; default: right)
			'order_by'	=> 'post_date',		// For post options: how posts should be ordered
			'order'		=> 'DESC',			// For post options: the order to display the results
			'limit'		=> 30, 				// For post and page options: how many results to retrieve
			'before'	=> '<p class="' . esc_attr( $args['id'] ) . '">', // Custom content to place before the input
			'after'		=> '</p>'."\n"	// Custom content to place after the input
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= $before;
		$output .= ($label) ? '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label> '."\n" : '';
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
			$output .= sb_dropdown_posts( array(
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
			$output .= '</select>'."\n";
		}
		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= $after;

		// Return our output
		return $output;
	}

	/**
	 * Enable Select Input - a select input with a checkbox input
	 *
	 * @param  array $args An array of arguments
	 * @return string      The concatenated select option output
	 */
	public function enable_select( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> 'option-select',	// The unique ID for this input
			'label'		=> 'Select',		// The content to use as the input label
			'value'		=> '',				// The option value
			'desc'		=> '',				// The content to display as a small descriptive text
			'options'	=> '',				// String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
			'size'		=> 'large',			// The size of this input (small, default, large; default: large)
			'align'		=> 'right',			// The alignment for this input (left, right; default: right)
			'before'	=> '<span class="right">',	// Custom content to include before the input
			'after'		=> '</span>',		// Custom costent to include after the input
			'order_by'	=> 'post_date',		// For post options: how posts should be ordered
			'order'		=> 'DESC',			// For post options: the order to display the results
			'limit'		=> 30 				// For post and page options: how many results to retrieve
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
	 * Navigation Option
	 *
	 * @param  array $args An array of arguments
	 * @return string           The concatenated navigation option output
	 */
	public function navigation( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> 'option-select',	// The unique ID for this input
			'label'		=> 'Select',		// The content to use as the input label
			'value'		=> '',				// The option value
			'desc'		=> '',				// The content to display as a small descriptive text
			'options'	=> '',				// String: pages, posts, categories: returns a dropdown for common WordPress content; Array: An array of selectable options
			'size'		=> 'large',			// The size of this input (small, default, large; default: large)
			'align'		=> 'right',			// The alignment for this input (left, right; default: right)
			'position'	=> '',				// An array of selectable position options
			'extras'	=> '',				// An array of selectable extra options
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$menu_opts = apply_filters( "sb_nav_types", array(
			'none' 		 => __( 'Disabled', 'startbox' ),
			'pages'		 => __( 'Pages', 'startbox' ),
			'categories' => __( 'Categories', 'startbox' )
		));
		$menus = get_terms('nav_menu');
		$output = '';

		// Concatenate our output
		$output .= '<p class="' . esc_attr( $id ) . '">'."\n";
		$output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label> '."\n";
		$output .= '<select id="' . esc_attr( $sb_id ) . '" name="' . esc_attr( $sb_id ) . '"class="option-select-' . esc_attr( $size ) . ' ' . esc_attr( $align ) . '">'."\n";
		foreach ( $menu_opts as $option_id => $option ) {
			if ($value == $option_id) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= '<option value="' . esc_attr( $option_id ) . '" ' . $select . '>' . $option . '</option>'."\n";
		}
		foreach ($menus as $menu ) {
			if ($value == $menu->term_id) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= '<option value="'. esc_attr( $menu->term_id ) .'" ' . $select . '>'. $menu->name .'</option>'."\n";
		}
		$output .= '</select>' . "\n";

		// Depth Options
		$depth = apply_filters( 'sb_nav_depth', array(
			'0' => 'Unlimited',
			'1' => '1',
			'2' => '2',
			'3' => '3'
		));
		$output .= sb_input::select( array( 'id' => $id . '-depth', 'label' => $label . ' ' . __( 'Depth', 'startbox' ), 'value' => sb_get_option( $id . '-depth' ), 'options' => $depth, 'size' => $size, 'align' => $align ) );

		// Position Options
		if ( !$position ) $position = apply_filters( "sb_{$id}_positions", array(
			'sb_before'		=> __( 'Top of Page', 'startbox' ),
			'sb_before_header'	=> __( 'Before Header', 'startbox' ),
			'sb_header'		=> __( 'Inside Header', 'startbox' ),
			'sb_after_header'	=> __( 'After Header', 'startbox' )
		));
		$output .= sb_input::select( array( 'id' => $id . '-position', 'label' => $label . ' ' . __( 'Position', 'startbox' ), 'value' => sb_get_option( $id . '-position' ), 'options' => $position, 'size' => $size, 'align' => $align ) );

		// Extras Options
		if ( $extras === true ) $extras = apply_filters( "sb_{$id}_extras", array(
			'disabled'	=> __( 'Disabled', 'startbox' ),
			'search'	=> __( 'Search Form', 'startbox' ),
			'social'	=> __( 'Social Links', 'startbox' )
		));
		if ($extras) $output .= sb_input::select( array( 'id' => $id . '-extras', 'label' => $label . ' ' . __( 'Extras', 'startbox' ), 'value' => sb_get_option( $id . '-extras' ), 'options' => $extras, 'size' => $size, 'align' => $align ) );

		// Add "Home" link to menu items
		$output .= sb_input::checkbox( array( 'id' => $id . '-enable-home', 'label' => sprintf( __( 'Add "Home" Link to %s', 'startbox'), $label ), 'value' => sb_get_option( $id . '-enable-home' ), 'align' => $align ) ) ;

		if ($desc) $output .= sb_input::descriptive_text( $desc );
		$output .= '</p>'."\n";

		// If we have no extras, we can stop here
		if (!$extras)
			return $output;

		// Social Extras Options
		$social_services = apply_filters( 'sb_nav_social_services', array(
			'rss'		=> __( 'Include RSS Feed', 'startbox' ),
			'twitter'	=> __( 'Twitter', 'startbox' ),
			'facebook'	=> __( 'Facebook', 'startbox' ),
			'youtube'	=> __( 'YouTube', 'startbox' ),
			'vimeo'		=> __( 'Vimeo', 'startbox' ),
			'flickr'	=> __( 'Flickr', 'startbox' ),
			'delicious'	=> __( 'del.icio.us', 'startbox' ),
			'linkedin'	=> __( 'LinkedIn', 'startbox' )
		));

		$output .= '<div class="' . esc_attr( $id ) . '-social-extras">';
		$output .= sb_input::intro( array( 'id' => $id . '-social-intro', 'label' => __( 'Social Links', 'startbox' ), 'desc' => __( 'Provide the full URL\'s (including http://) of whichever social profiles you would like to include in your navigation.', 'startbox' ) ) );
		foreach ($social_services as $service => $label) {
			$value = sb_get_option( $id . '-social-' . $service );
			if ($service == 'rss') {
				if ( sb_get_option( $id . '-social-rss' ) == 'true') { $checked = true; } else { $checked = false; }
				$output .= sb_input::checkbox( array( 'id' => $id . '-social-rss', 'label' => $label, 'value' => $checked, 'align' => $align ) );
			} else {
				$output .= sb_input::text( array( 'id' => $id . '-social-' . $service, 'class' => $id . '-social-' . $service, 'label' => $label, 'value' => $value, 'size' => 'medium', 'align' => 'right' ) );
			}
		}
		$output .= '</div>';

		// Return our output
		return $output;
	}

	/**
	 * Upload Input
	 *
	 * @param  array $args An array of arguments
	 * @return string      The concatenated upload option output
	 */
	public function upload( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',		// The unique ID for this input
			'label'		=> '',		// The content to use as the input label
			'value'		=> '',		// The option value
			'desc'		=> '',		// The content to display as a small descriptive text
			'suggested'	=> null,	// Comma-sepparated list of URLs (relative to the active theme's directory). Non-existant images will produce a warning.
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$sb_id = THEME_OPTIONS . '[' . esc_attr( $id ) . ']';
		$output = '';

		// Concatenate our output
		$output .= '<p class="imagepickerinput ' . esc_attr( $id ) . '">'."\n";
		$output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . ':</label> <input type="text" value="' . esc_attr( $value ) . '" name="' . esc_attr( $sb_id ) . '" id="' . esc_attr( $sb_id ) . '" class="uploadinput"/> ' ;
		$output .= '<a href="' . esc_attr( $value ) . '" class="previewlink button" title="' . esc_attr( $label ) . '">'.__('Preview','startbox').'</a>&nbsp;';
		if ( $suggested )
			$output .= '<a href="media-upload.php?type=image&amp;tab=suggested&amp;suggested=' . $suggested . '" class="chooselink button colorbox" title="' . __('Choose a previously uploaded file','startbox') . '">' . __('Media Library','startbox') . '</a>&nbsp;';
		else
			$output .= '<a href="media-upload.php?type=image&amp;tab=library" class="chooselink button colorbox" title="'.__('Choose a previously uploaded file','startbox').'">'.__('Media Library','startbox').'</a>&nbsp;';
		$output .= '<a href="#" class="uploadlink button" title="'.__('Upload a file','startbox').'">'.__('Upload','startbox').'</a><br/>';
		$output .= '<span class="desc"> ' . $desc . ' <span class="uploadresult"></span></span>'."\n";
		$output .= '</p>'."\n";

		// Return our output
		return $output;
	}

	/**
	 * Logo Options
	 *
	 * @param  array $args An array of arguments
	 * @return string        The concatenated logo option output
	 */
	public function logo( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',	// The unique ID for this input
			'label'		=> '',	// The content to use as the input label
			'desc'		=> '',	// The content to display as a small descriptive text
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output .= sb_input::intro( array(
			'id'		=> $id,
			'label'		=> __( 'Logo Settings', 'startbox' ),
			'desc'		=> $desc
			) );
		$output .= sb_input::select( array(
			'id'		=> $id . '-select',
			'label'		=> __( 'Logo Type', 'startbox' ),
			'align'		=> 'left',
			'value'		=> sb_get_option( $id . '-select' ),
			'options'	=> array(
				'image'		=> __( 'Image', 'startbox' ),
				'text'		=> __( 'Text', 'startbox' ),
				'disabled'	=> __( 'Disabled', 'startbox' )
				)
			) );
		$output .= sb_input::select( array(
			'id'		=> $id . '-align',
			'label'		=> __( 'Alignment', 'startbox' ),
			'align'		=> 'left',
			'value'		=> sb_get_option( $id . '-align' ),
			'options'	=> array(
				'left'		=> __( 'Left', 'startbox' ),
				'center'	=> __( 'Center', 'startbox' ),
				'right'		=> __( 'Right', 'startbox' )
				)
			) );
		$output .= sb_input::text( array(
			'id'		=> $id . '-text',
			'label'		=> __( 'Use This Text', 'startbox' ),
			'value'		=> sb_get_option( $id . '-text' ),
			'size'		=> 'medium',
			'align'		=> 'left'
			) );
		$output .= sb_input::upload( array(
			'id'		=> $id . '-image',
			'label'		=> __( 'Use This Image', 'startbox' ),
			'value'		=> sb_get_option( $id . '-image' ),
			) );

		// Return our output
		return $output;
	}

	/**
	 * WYSIWYG Options
	 *
	 * @param  array $args An array of arguments
	 * @return string      The concatenated WYSIWYG option output
	 */
	public function wysiwyg( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',		// Unique ID for this option
			'label'		=> '',		// The content to display as the input label
			'value'		=> '',		// The option value
			'desc'		=> '',		// Descriptive text
			'options'	=> array(	// Options specific to the wp_editor() function
        		'textarea_name'	=> THEME_OPTIONS . '[' . esc_attr( $args['id'] ) . ']',
	        	'media_buttons'	=> false,
    	    	'teeny'			=> true
        	)
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output .= '<p class=" . esc_attr( $id ) . ">'."\n";
        $output .= '<label for="' . esc_attr( $sb_id ) . '">' . $label . '</label><br/>'."\n";
        ob_start();
        wp_editor( $value, $id, $options );
		$output .= ob_get_clean();
        if ($desc) $output .= sb_input::descriptive_text( $desc );
        $output .= '</p>'."\n";

        // Return our output
        return $output;
    }

    /**
	 * Color Input
	 *
	 * @param  array $args An array of arguments
	 * @return string      The concatenated color option output
	 */
	public function color( $args = '' ) {

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

	/**
	 * Background Options
	 *
	 * @param  string $id    The unique Id for this input
	 * @param  string $label The content to use as the input label
	 * @param  string $desc  Small descriptive text
	 * @return string        The concatenated background option output
	 */
	public function background( $args = '' ) {

		// Setup our defaults
		$defaults = array(
			'id'		=> '',		// Unique ID for this option
			'label'		=> '',		// The content to display as the input label
			'desc'		=> '',		// Descriptive text
		);

		// Get our variables ready to go
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_OVERWRITE );
		$output = '';

		// Concatenate our output
		$output .= sb_input::intro( array(
			'id'		=> $id,
			'label'		=> $label,
			'desc'		=> $desc
			) );
		$output .= sb_input::upload( array(
			'id'		=> $id . '-image',
			'label'		=> 'Background Image',
			'value'		=> sb_get_option( $id . '-image' )
			) );
		$output .= sb_input::color( array(
			'id'		=> $id . '-color',
			'label'		=> 'Bacground Color',
			'value'		=> sb_get_option( $id . '-color' )
			) );
		$output .= sb_input::select( array(
			'id'		=> $id . '-horiz',
			'label'		=> 'Horizontal Alignment',
			'value'		=> sb_get_option( $id . '-horiz' ),
			'options'	=> array(
				'left'		=> 'Left',
				'center'	=> 'Center',
				'right'		=> 'Right'),
			'size'		=> 'medium',
			'align'		=> 'right'
			) );
		$output .= sb_input::select( array(
			'id'		=> $id . '-vert',
			'label'		=> 'Vertical Alignment',
			'value'		=> sb_get_option( $id . '-vert' ),
			'options'	=> array(
				'top'		=> 'Top',
				'middle'	=> 'Middle',
				'bottom'	=> 'Bottom'
				),
			'size'		=> 'medium',
			'align'		=> 'right'
			) );
		$output .= sb_input::select( array( 'id' => $id . '-repeat',
			'label'		=> 'Repeat',
			'value'		=> sb_get_option( $id . '-repeat' ),
			'options'	=> array(
				'no-repeat'	=> 'No Repeat',
				'repeat-x'	=> 'Tile Horizontally',
				'repeat-y'	=> 'Tile Vertically',
				'repeat'	=> 'Both'
				),
			'size'		=> 'medium',
			'align'		=> 'right'
			) );
		$output .= sb_input::checkbox( array(
			'id'		=> $id . '-fixed',
			'label'		=> 'Fixed Position',
			'value'		=> sb_get_option( $id . '-fixed' ),
			'align'		=> 'right'
			) );

		// Return our output
		return $output;
	}

}
$sb_input = new sb_input;

/**
 * SB Settings Factory
 *
 * The processor for adding/removing option panels with the Theme Options page.
 *
 * @since 2.4.2
 */
class sb_settings_factory {

	// Setup our variables
	public $settings = array();
	public $defaults = array(
		'sb_analytics_settings',
		'sb_layout_settings',
		'sb_content_settings',
		'sb_footer_settings',
		'sb_header_settings',
		'sb_settings_help',
		'sb_navigation_settings',
		'sb_seo_settings',
		'sb_upgrade_settings'
	);

	// Register a new options panel
	public function register($class_name) {
		$this->settings[$class_name] = & new $class_name();
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
 * Remove all default StartBox option panels
 *
 * @since 2.4.8
 */
function sb_remove_default_settings() {
	global $sb_settings_factory;
	add_action( 'admin_init', array($sb_settings_factory, 'unregister_all_defaults' ) );
}
if ( defined('SB_REMOVE_DEFAULT_SETTINGS') ) { sb_remove_default_settings(); }

/**
 * Sets the Default settings for all StartBox options
 *
 * @since 2.4.6
 */
function sb_set_default_options() {

	// Grab our various settings
	global $sb_settings_factory;
	$defaults = $current = get_option( THEME_OPTIONS );
	$settings = $sb_settings_factory->settings;

	// Loop through all settings panels
	foreach( $settings as $setting ){

		// Grab the options for the panel
		$options = $setting->options;

		// Loop through each option
		foreach( $options as $option_id => $option ) {

			// If the setting has a default, set it
			if ( isset( $option['default'] ) ) $defaults[$option_id] = $option['default'];

			// If we're working with navigation options specifically, we need to handle them differently
			if ( $option['type'] == 'navigation' ) {
				if ( isset($option['home_default']) )		$defaults[$option_id.'-enable-home']	= $option['home_default'];
				if ( isset($option['position_default']) )	$defaults[$option_id.'-position']		= $option['position_default'];
				if ( isset($option['depth_default']) )		$defaults[$option_id.'-depth']			= $option['depth_default'];
			}

			// And same is true with logo options
			if ( $option['type'] == 'logo' ) {
				$defaults['logo-image'] = IMAGES_URL . '/logo.png';
			}
		}
	}

	// If this was a reset, drop the reset value
	if ( isset( $current['reset'] ) && isset( $defaults['reset'] ) )
		$defaults['reset'] = false;

	// Save the options to the database, Allow child themes to filter what defaults are returned
	update_option( THEME_OPTIONS, apply_filters( 'sb_option_defaults', $defaults ) );

}
add_action( 'sb_install', 'sb_set_default_options' );

/**
 * Adds an option to the options db.
 *
 * @since 2.4.4
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name. Must be unique.
 * @param mixed $value Option Value.
 * @return bool True on success, false if the option already exists.
 */
function sb_add_option( $name, $value ) {
	$options = get_option( THEME_OPTIONS );
	if ( $options and !isset($options[$name]) ) {
		$options[$name] = $value;
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Updates an option to the options db.
 *
 * @since 2.4.4
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name. Must be unique.
 * @param mixed $value Option Value.
 * @return bool true|false
 */
function sb_update_option( $name, $value ) {
	$options = get_option( THEME_OPTIONS );
	if ( !isset($options[$name]) || $value != $options[$name] ) {
		$options[$name] = $value;
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Returns the value of an option from the db if it exists.
 *
 * @since 2.4.4
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name.
 * @return mixed Returns the option's value if it exists, false if it doesn't.
 */
function sb_get_option( $name ) {
	$options = get_option( THEME_OPTIONS );
	if ( is_array($options) && isset($options[$name]) ) {
		return $options[$name];
	} else {
		return false;
	}
}

/**
 * Deletes an option from the options db.
 *
 * @since 2.4.4
 * @link http://bit.ly/ptahoptions Thanks ptahdunbar!
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @param string $name Option Name. Must be unique.
 * @return bool true|false
 */
function sb_delete_option( $name ) {
	$options = get_option( THEME_OPTIONS );
	if ( $options[$name] ) {
		unset( $options[$name] );
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Add a new option to an existing metabox
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @since 2.4.9
 *
 * @param string $metabox the name of the metabox where the option will appear
 * @param string $option_name the name of the option to add
 * @param array $args the arguments to pass through the Options API
 *
 */
function sb_register_option( $metabox, $option_name, $args ) {
	global $sb_settings_factory;
	$sb_settings_factory->settings[$metabox]->options[$option_name] = $args ;
}
/**
 * Remove an existing option
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @since 2.4.9
 *
 * @param string $metabox the name of the metabox where the option exists
 * @param string $option the name of the option to remove
 * @param mixed $new_value Optional. Store a new, permanent value to the options table
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
 * Helper function for easily removing actions hooked in via classes
 *
 * @since 2.4.4
 *
 * @uses remove_action()
 *
 * @param string $tag Hook name
 * @param string $class_name Name of class where $function_to_remove resides
 * @param string $function_to_remove The function to remove
 * @param integer $priority Level of priority (default: 10)
 * @return bool True on success, false if the function does not exist.
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
 * Helper function for easily re-inserting actions hooked-in via classes
 *
 * @since 2.4.4
 *
 * @uses add_action()
 *
 * @param string $tag Hook name
 * @param string $class_name Name of class where $function_to_add resides
 * @param string $function_to_add The function to add
 * @param integer $priority Level of priority (default: 10)
 * @return bool True on success, false if the option already exists.
 */
function sb_add_action( $tag, $class_name, $function_to_add, $priority = 10 ) {
	if ($class_name) {
		global $sb_settings_factory;
		$function_to_add = array( $sb_settings_factory->settings[$class_name], $function_to_add);
	}
	return add_action( $tag, $function_to_add, $priority );
}

/**
 * Helper function for outputting valid CSS for background-type options
 *
 * @since 2.4.4
 *
 * @uses sb_get_option()
 *
 * @param string $option_name Option name
 * @return string Complete CSS for declaring short-hand background properties
 */
function sb_get_background_output( $option_name ) {
	$options = get_option( THEME_OPTIONS );
	$color = $options[ $option_name . '-color' ];
	$image = $options[ $option_name . '-image' ];
	$repeat = $options[ $option_name . '-repeat' ];
	$horiz = $options[ $option_name . '-horiz' ];
	$vert = $options[ $option_name . '-vert' ];
	$fixed = ($options[ $option_name . '-fixed' ]) ? ' fixed' : '';
	$url = ($image) ? "url('" . $image . "') " : ' ' ;

	$output = $color . ' ' . $url . $repeat . ' ' . $horiz . ' ' . $vert . $fixed;

	return $output;
}