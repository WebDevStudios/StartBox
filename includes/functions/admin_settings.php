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
 * @since StartBox 2.4.2
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
 */
class sb_settings {
	public $name = 'Settings Panel';	// Name for your options panel, displays as a title
	public $slug = 'settings_panel';	// Nice-name for your options panel
	public $page = 'sb_admin';			// Page for your settings panel: sb_admin or sb_style
	public $location = 'secondary';		// Column for your settings panel: primary or secondary
	public $priority = 'default';		// Priority for your settings panel: high, low, default
	public $options = array();			// A multi-dimensional array for populating the settings panel.
	
	// Create the options form to wrap inside metabox. Override this in your own class to create your own form
	public function form($options) {
		$options = ($options) ? $options : $this->options;
		
	    $output = '';
	    foreach ($options as $setting_id => $setting) {

	    	$value = sb_get_option( $setting_id );
			$label = ( isset( $setting['label'] ) ) ? $setting['label'] : '';
			$class = ( isset( $setting['class'] ) ) ? $setting['class'] : '';
			$align = ( isset( $setting['align'] ) ) ? $setting['align'] : '';
			$before = ( isset( $setting['before'] ) ) ? $setting['before'] : '';
			$after = ( isset( $setting['after'] ) ) ? $setting['after'] : '';
			$desc = ( isset( $setting['desc'] ) ) ? $setting['desc'] : '';
			$size = ( isset( $setting['size'] ) ) ? ' option-field-' . $setting['size'] : '' ;
			$position = ( isset( $setting['position'] ) ) ? $setting['position'] : '' ;
			$options = ( isset( $setting['options'] ) ) ? $setting['options'] : '';
			$order_by = ( isset( $setting['order_by'] ) ) ? $setting['order_by'] : '';
			$order = ( isset( $setting['order'] ) ) ? $setting['order'] : '';
			$limit = ( isset( $setting['limit'] ) ) ? $setting['limit'] : '';
			$suggested = ( isset( $setting['suggested'] ) ) ? $setting['suggested'] : '';
			$extras = ( isset( $setting['extras'] ) ) ? $setting['extras'] : '';
			
			if ($setting['type'] == 'intro') { $output .= sb_input::intro( $setting_id, $label, $desc ); }
			elseif ($setting['type'] == 'divider') { $output .= "\t" . '<hr/>'."\n"; }
			elseif ($setting['type'] == 'text') { $output .= '<p class="' . $setting_id . '">' . sb_input::text( $setting_id, $class, $label, $value, $desc, $size, $align, $before, $after ) . "</p>\n"; }
			elseif ($setting['type'] == 'textarea') { $output .= sb_input::textarea( $setting_id, $label, $value, $desc ); }
			elseif ($setting['type'] == 'checkbox') { $output .= sb_input::checkbox( $setting_id, $label, $value, $desc, $align ); }
			elseif ($setting['type'] == 'radio') { $output .= sb_input::radio( $setting_id, $label, $value, $desc, $options ); }
			elseif ($setting['type'] == 'select') { $output .= sb_input::select( array( 'id' => $setting_id, 'label' => $label, 'value' => $value, 'desc' => $desc, 'options' => $options, 'size' => $size, 'align' => $align, 'order_by' => $order_by, 'order' => $order, 'limit' => $limit ) ); }
			elseif ($setting['type'] == 'enable_select') { $output .= sb_input::enable_select( array( 'id' => $setting_id, 'label' => $label, 'value' => $value, 'desc' => $desc, 'options' => $options, 'size' => $size, 'align' => $align, 'order_by' => $order_by, 'order' => $order, 'limit' => $limit ) ); }
			elseif ($setting['type'] == 'color') { $output .= sb_input::color( $setting_id, $label, $value, $desc ); }
			elseif ($setting['type'] == 'upload') { $output .= sb_input::upload( $setting_id, $label, $value, $desc, $suggested ); }
			elseif ($setting['type'] == 'navigation') { $output .= sb_input::navigation( $setting_id, $label, $value, $desc, $size = 'large', $align ='right', $position, $extras ); }
			elseif ($setting['type'] == 'logo') { $output .= sb_input::logo( $setting_id, $label, $desc); }
			elseif ($setting['type'] == 'background') { $output .= sb_input::background( $setting_id, $label, $desc ); }
			elseif ($setting['type'] == 'font') { $output .= sb_input::font( $setting_id, $label, $desc ); }
			elseif ($setting['type'] == 'border') { $output .= sb_input::border( $setting_id, $label, $desc ); }
			elseif ($setting['type'] == 'wysiwyg' || $setting['type'] == 'tinymce') { $output .= sb_input::wysiwyg( $setting_id, $label, $value, $desc ); }
			elseif ($setting['type'] == 'layout') { $output .= sb_input::layout( $setting_id, $label, $value, $desc, $options ); }
			
		}
	    echo $output;
	}
	
	// Outputting settings as necessary. Note: you can add as many custom functions as you need.
	public function output() {}
	
	// For hooking all your functions elsewhere.
	// When referencing the function in add_action() use: array( $this, 'function_name' )
	public function hooks() {}
	
	// This makes errors more happy
	public function __call($method, $args) { wp_die( "Your new settings class, <b>" . $this->name . "</b>, is trying to call an unknown method: " . $method ); }
	
	// This creates the metabox. Do not override this method.
	public function _init() {
		global $sb_admin, $sb_style;
		$this->page = ($this->page == 'sb_style') ? $sb_style : $sb_admin;
		add_meta_box( $this->slug, $this->name, array( $this, 'form'), $sb_admin, $this->location, $this->priority);
	}
	
	// This makes everything work. Do not override this method.
	public function __construct() {
		add_action( 'admin_init', array( $this, '_init' ), 5 );
		add_action( 'init', array( $this, 'hooks' ), 9 );
	}

}

/**
 * StartBox Input Class
 *
 * Creates input fields for use in sb_settings classes. Currently used to produce the following:
 * 
 * sb_input::intro - produces <h4> heading ($label) and description ($desc)
 * sb_input::text - produces text input
 * sb_input::textarea - produces textarea input
 * sb_input::checkbox - produces checkbox
 * sb_input::radio - produces radio options ($options)
 * sb_input::select - produces select box options ($option)
 * sb_input::enable_select - produces select box options ($options) with a checkbox
 * sb_input::navigation - produces select box of navigation items (none, categories, pages, custom menus)
 * sb_input::color - produces a jQuery color selector
 * sb_input::upload - produces an AJAX uploader
 * sb_input::logo - internal, produces options for logo settings
 * sb_input::background - produces inputs for background (color, image, alignment, repeat, fixed)
 * sb_input::font - produces inputs for fonts (family, size, line height, unit, weight, style, decoration, transform)
 * sb_input::border - produces options for borders (color, top-, bottom-, left-, right- widths)
 *
 * @since StartBox 2.4.4
 */
class sb_input {
	
	public function intro( $id, $label, $desc ) {
		$output = '<h4 id="' . THEME_OPTIONS . '[' . $id . ']' . '" class="' . $id . '">' . $label . '</h4>'."\n";
		$output .= '<p class="' . $id . '">' . $desc . '</p>'."\n";
		return $output;
	}
	public function text( $id, $class, $label, $value, $desc, $size = 'default', $align = 'left', $before = null, $after = null ) {
		$output = "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . ':</label> <span class="' .$align . '">' . $before . '<input type="text" value="' . $value . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" id="' . THEME_OPTIONS . '[' . $id . ']' . '" class="option-field-' . $size . ' ' . $class . '" />' . $after . '</span>';
		if ($desc) { $output .= "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		return $output;
	}
	public function textarea( $id, $label, $value, $desc ) {
		$output = "\t" . '<p class="' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . '</label><br/>'."\n";
		$output .= "\t" . "\t" . '<textarea name="' . THEME_OPTIONS . '[' . $id . ']' . '" id="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $value . '</textarea>'."\n";
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function checkbox( $id, $label, $value, $desc, $align = 'left' ) {
		if ($value == 'true') { $checked = 'checked="checked"'; } else { $checked = ''; }
		$output = "\t" . '<p class="' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '" class="' . $align . '"><input type="checkbox" class="checkbox" id="' . THEME_OPTIONS . '[' . $id . ']' . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" value="true" ' . $checked . ' /> ' . $label . '</label><br/>'."\n";
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function radio( $id, $label, $value, $desc, $options ) {
		$output = "\t" . '<p class="' . $id . '">';
		$output .= "\t" . "\t" . $label . '<br/>'."\n";
		foreach ( $options as $option_id => $option ) {
			if ($value == $option_id) { $checked = 'checked'; } else { $checked = ''; }
			$output .= "\t" . "\t" . '<input type="radio" id="' . $id . '-' . $option_id . '" value="' . $option_id . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" ' . $checked . ' /> <label for="' . $id . '-' . $option_id . '">' . $option . '</label><br/>'."\n";
		}
		if ($desc) { $output .= "\t" . "\t" . '<span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function layout( $id, $label, $value, $desc, $options ) {
		// If themes don't support layouts, don't return any layout options
		if ( !current_theme_supports('sb-layouts') || $options == '')
			return $output;
			
		$output = "\t" . '<p class="' . $id . '">';
		$output .= "\t" . "\t" . $label . '<br/>'."\n";
		if ($desc) { $output .= "\t" . "\t" . '<span class="desc"> ' . $desc . ' </span>'."\n"; }
		foreach ( $options as $option_id => $option ) {
			$layout = $option_id;
			if ($value == $option_id) { $checked = 'checked'; } else { $checked = ''; }
			$output .= "\t" . "\t" . '<div class="layout-container"><label for="' . $id . '-' . $option_id . '"><input type="radio" id="' . $id . '-' . $option_id . '" value="' . $option_id . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" ' . $checked . ' /><img src="' . $option['img'] .'" alt="' . $option['label'] . '"  width="50" height="40" /></label></div>';
		}
		$output .= "\t" . '</p>'."\n";
		$output .= "\t" . '<hr/>'."\n";
		return $output;
	}
	public function select( $args = '' ) {
		$defaults = array(
			'id' => 'option-select',
			'label' => 'Select',
			'value' => '',
			'desc' => '',
			'options' => '',
			'size' => 'large',
			'align' => 'right',
			'order_by' => 'post_date',
			'order' => 'DESC',
			'limit' => 30
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		
		$output = "\t" . '<p class="' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . ':</label> '."\n";
		if ( $options == 'categories' ) {
			$output .= wp_dropdown_categories( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'id' => THEME_OPTIONS . '[' . $id . ']', 'class' => 'option-select-' . $size . ' ' . $align, 'show_option_none' => 'Select a Category', 'selected' => $value ) );
		} elseif ( $options == 'pages' ) {
			$output .= wp_dropdown_pages( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'show_option_none' => 'Select a Page', 'selected' => $value ) );
		} elseif ( $options == 'posts' ) {
			$output .= sb_dropdown_posts( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'id' => THEME_OPTIONS . '[' . $id . ']', 'class' => 'option-select-' . $size . ' ' . $align, 'show_option_none' => 'Select a Post', 'selected' => $value, 'order_by' => $order_by, 'order' => $order, 'limit' => $limit ) );
		} else {
			$output .= "\t" . "\t" . '<select id="' . THEME_OPTIONS . '[' . $id . ']' . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" class="option-select-' . $size . ' ' . $align . '">'."\n";
			foreach ( $options as $option_id => $option ) {
				if ($value == $option_id) { $select = 'selected="selected"'; } else { $select = ''; }
				$output .= "\t" . "\t" . "\t" . '<option value="' . $option_id . '" ' . $select . '>' . $option . '</option>'."\n";
			}
			$output .= "\t" . "\t" . '</select>'."\n";
		}
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function enable_select( $args = '' ) {
		$defaults = array(
			'id' => 'option-select',
			'label' => 'Select',
			'value' => '',
			'desc' => '',
			'options' => '',
			'size' => 'large',
			'align' => 'right',
			'order_by' => 'post_date',
			'order' => DESC,
			'limit' => 30
		);
		
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		
		if ( sb_get_option( $id . '-enabled' ) == 'true') { $checked = 'checked="checked"'; } else { $checked = ''; }
		$output = "\t" . '<p class="' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . '-enabled]' . '"><input type="checkbox" class="checkbox" id="' . THEME_OPTIONS . '[' . $id . '-enabled]' . '" name="' . THEME_OPTIONS . '[' . $id . '-enabled]' . '" value="true" ' . $checked . ' /> Enable</label>'."\n";
		$output .= '<span class="right">';
		if ($label) { $output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . ':</label> '."\n"; }
		if ( $options == 'categories' ) {
			$output .= wp_dropdown_categories( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'id' => THEME_OPTIONS . '[' . $id . ']', 'show_option_none' => 'Select a Category', 'selected' => $value ) );
		} elseif ( $options == 'pages' ) {
			$output .= wp_dropdown_pages( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'show_option_none' => 'Select a Page', 'selected' => $value ) );
		} elseif ( $options == 'posts' ) {
			$output .= sb_dropdown_posts( array( 'echo' => 0, 'name' => THEME_OPTIONS . '[' . $id . ']', 'id' => THEME_OPTIONS . '[' . $id . ']', 'class' => 'option-select-' . $size . ' ' . $align, 'show_option_none' => 'Select a Post', 'selected' => $value, 'order_by' => $order_by, 'order' => $order, 'limit' => $limit ) );
		} else {
			$output .= "\t" . "\t" . '<select id="' . THEME_OPTIONS . '[' . $id . ']' . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" class="option-select-' . $size . ' ' . $align . '">'."\n";
			foreach ( $options as $option_id => $option ) {
				if ($value == $option_id) { $select = 'selected="selected"'; } else { $select = ''; }
				$output .= "\t" . "\t" . "\t" . '<option value="' . $option_id . '" ' . $select . '>' . $option . '</option>'."\n";
			}
			$output .= "\t" . "\t" . '</select>'."\n";
		}
		$output .= '</span>';
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function navigation( $id, $label, $value, $desc, $size = 'large', $align = 'right', $position = null, $extras ) {
		$menu_opts = apply_filters( "sb_nav_types", array(
			'none' 		 => __( 'Disabled', 'startbox' ),
			'pages'		 => __( 'Pages', 'startbox' ),
			'categories' => __( 'Categories', 'startbox' )
		));
		$menus = get_terms('nav_menu');
		
		$output = "\t" . '<p class="' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . ':</label> '."\n";
		$output .= "\t" . "\t" . '<select id="' . THEME_OPTIONS . '[' . $id . ']' . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '"class="option-select-' . $size . ' ' . $align . '">'."\n";
		foreach ( $menu_opts as $option_id => $option ) {
			if ($value == $option_id) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= "\t" . "\t" . "\t" . '<option value="' . $option_id . '" ' . $select . '>' . $option . '</option>'."\n";
		}
		foreach ($menus as $menu ) {
			if ($value == $menu->term_id) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= "\t" . "\t" . "\t" . '<option value="'. $menu->term_id .'" ' . $select . '>'. $menu->name .'</option>'."\n";
		}
		$output .= "\t" . "\t" . '</select>' . "\n";
		
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
		if ( sb_get_option( $id . '-enable-home' ) == 'true') { $checked = true; } else { $checked = false; }
		$output .= sb_input::checkbox( $id . '-enable-home', sprintf( __( 'Add "Home" Link to %s', 'startbox'), $label ), $checked, null, $align );
		
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
		$output .= "\t" . '</p>'."\n";
		
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
		
		$output .= '<div class="' . $id . '-social-extras">';
		$output .= sb_input::intro( $id . '-social-intro', __( 'Social Links', 'startbox' ), __( 'Provide the full URL\'s (including http://) of whichever social profiles you would like to include in your navigation.', 'startbox' ) );
		foreach ($social_services as $service => $label) {
			$value = sb_get_option( $id . '-social-' . $service );
			if ($service == 'rss') {
				if ( sb_get_option( $id . '-social-rss' ) == 'true') { $checked = true; } else { $checked = false; }
				$output .= sb_input::checkbox( $id . '-social-rss', $label, $checked, null, $align );
			} else {
				$output .= '<p>' . sb_input::text( $id . '-social-' . $service, $id . '-social-' . $service, $label, $value, null, 'medium', 'right') . '</p>';
			}
		}
		$output .= '</div>';
		
		return $output;
	}
	public function color( $id, $label, $value, $desc ) {
		$output = '<p class="colorpickerinput ' . $id . '">';
		$output .= sb_input::text( $id, null, $label, sb_get_option( $id ), null, 'small', 'colorinput', '<span class="right">' );
		$output .= '<span class="colorselector"><span></span></span></span>'."\n";
		if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; } 
		$output .= '</p>';
		return $output;
	}
	public function upload( $id, $label, $value, $desc, $suggested = null ) {
		$output = "\t" . '<p class="imagepickerinput ' . $id . '">'."\n";
		$output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . ':</label> <input type="text" value="' . $value . '" name="' . THEME_OPTIONS . '[' . $id . ']' . '" id="' . THEME_OPTIONS . '[' . $id . ']' . '" class="uploadinput"/>';
		$output .= ' <a href="' . $value . '" class="previewlink button" title="' . $label . '">Preview</a>';
		if ( $suggested ) {
			// The URLs for the 'suggested' setting are relative to the active theme's directory. Non-existant images will produce a warning.
			$output .= '&nbsp;<a href="media-upload.php?type=image&amp;tab=suggested&amp;suggested=' . $suggested . '" class="chooselink button colorbox" title="Choose a previously uploaded file">Media Library</a>';
		} else {
			$output .= '&nbsp;<a href="media-upload.php?type=image&amp;tab=library" class="chooselink button colorbox" title="Choose a previously uploaded file">Media Library</a>';
		}
		$output .= '&nbsp;<a href="#" class="uploadlink button" title="Upload a file">Upload</a>';
		$output .= '';
		$output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' <span class="uploadresult"></span></span>'."\n";
		$output .= "\t" . '</p>'."\n";
		return $output;
	}
	public function logo($id, $label, $desc) {
		$output = sb_input::intro( $id, __( 'Logo Settings', 'startbox' ), $desc );
		$output .= sb_input::upload( $id . '-image', __( 'Logo Image', 'startbox' ), sb_get_option( $id . '-image' ), null );
		$output .= "<p>" . sb_input::text( $id . '-text', null, __( 'Or, use this text instead', 'startbox' ), sb_get_option( $id . '-text' ), null, 'medium' ) . "</p>\n";
		$output .= sb_input::select( array( 'id' => $id . '-align', 'label' => __( 'Logo Alignment', 'startbox' ), 'value' => sb_get_option( $id . '-align' ), 'options' => array( 'left' => __( 'Left', 'startbox' ), 'right' => __( 'Right', 'startbox' ), 'center' => __( 'Center', 'startbox' )), 'size' => 'default', 'align' => 'left' ) );
		$output .= sb_input::checkbox( $id . '-disabled', __( 'Disable Logo', 'startbox' ), sb_get_option( $id . '-disabled' ), null );
		return $output;
	}
	public function background( $id, $label, $desc ) {
		$output .= sb_input::intro( $id, $label, $desc );
		$output .= sb_input::upload( $id . '-image', 'Background Image', sb_get_option( $id . '-image' ), null );
		$output .= sb_input::color( $id . '-color', 'Bacground Color', sb_get_option( $id . '-color' ), null );
		$output .= sb_input::select( array( 'id' => $id . '-horiz', 'label' => 'Horizontal Alignment', 'value' => sb_get_option( $id . '-horiz' ), 'options' => array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right'), 'size' => 'medium', 'align' => 'right') );
		$output .= sb_input::select( array( 'id' => $id . '-vert', 'label' => 'Vertical Alignment', 'value' => sb_get_option( $id . '-vert' ), 'options' => array( 'top' => 'Top', 'middle' => 'Middle', 'bottom' => 'Bottom'), 'size' => 'medium', 'align' => 'right' ) );
		$output .= sb_input::select( array( 'id' => $id . '-repeat', 'label' => 'Repeat', 'value' => sb_get_option( $id . '-repeat' ), 'options' => array( 'no-repeat' => 'No Repeat', 'repeat-x' => 'Tile Horizontally', 'repeat-y' => 'Tile Vertically', 'repeat' => 'Both'), 'size' => 'medium', 'align' => 'right' ) );
		$output .= sb_input::checkbox( $id . '-fixed', 'Fixed Position', sb_get_option( $id . '-fixed' ), $setting['desc'], 'right' );
		return $output;
	}
	public function font( $id, $label, $desc ) {
		$output = sb_input::intro( $id, $label, $desc );
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-family', null, 'Font Family', sb_get_option( $id . '-family' ), 'Enter an individual font name a comma-separated font stack (e.g. Georgia,Times,"Times New Roman",serif).', 'large', 'right' ) . "</p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-size', null, 'Font Size', sb_get_option( $id . '-size' ), null, 'small', null, '<span class="right">' ) . " <span class='font-unit'>" . sb_get_option( $id . '-unit' ) . "</span></span></p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-line-height', null, 'Line Height', sb_get_option( $id . '-line-height'), null, 'small', null, '<span class="right">' ) . " <span class='font-unit'>" . sb_get_option( $id . '-unit' ) . "</span></span></p>\n";
		$output .= sb_input::select( array( 'id' => $id . '-unit', 'label' => 'Unit of measurement', 'value' => sb_get_option( $id . '-unit' ), 'options' => array('px' => 'px','pt' => 'pt','em' => 'em','%' => '%'), 'size' => 'medium', 'align' => 'right font-unit' ) );
		$output .= sb_input::color( $id . '-color', 'Font Color', sb_get_option( $id . '-color' ), null );
		$output .= sb_input::select( array( 'id' => $id . '-style', 'label' => 'Font Style', 'value' => sb_get_option( $id . '-style' ), 'options' => array('normal' => 'Normal', 'italic' => 'Italic'), 'size' => 'medium', 'align' => 'right' ) );
		$output .= sb_input::select( array( 'id' => $id . '-weight', 'label' => 'Font Weight', 'value' => sb_get_option( $id . '-weight' ), 'options' => array('normal' => 'Normal', 'bold' => 'Bold'), 'size' => 'medium', 'align' => 'right' ) );
		$output .= sb_input::select( array( 'id' => $id . '-decoration', 'label' => 'Text Decoration', 'value' => sb_get_option( $id . '-decoration' ), 'options' => array( 'none' => 'None', 'underline' => 'Underline', 'overline' => 'Overline', 'line-through' => 'Line Through'), 'size' => 'medium', 'align' => 'right' ) );
		$output .= sb_input::select( array( 'id' => $id . '-transform', 'label' => 'Text Transform', 'value' => sb_get_option( $id . '-transform' ), 'options' => array( 'none' => 'None', 'capitalize' => 'Capitalize', 'uppercase' => 'UPPERCASE', 'lowercase' => 'lowercase'), 'size' => 'medium', 'align' => 'right' ) );
		return $output;
	}
	public function border( $id, $label, $desc ) {
		$output = sb_input::intro( $id, $label, $desc );
		$output .= sb_input::color( $id . '-color', 'Border Color', sb_get_option( $id . '-color' ), null );
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-top', null, 'Border Top Width', sb_get_option( $id . '-top' ), null, 'small', null, '<span class="right">' ) . " px</p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-bottom', null, 'Border Bottom Width', sb_get_option( $id . '-bottom' ), null, 'small', null, '<span class="right">' ) . " px</span></p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-left', null, 'Border Left Width', sb_get_option( $id . '-left' ), null, 'small', null, '<span class="right">' ) . " px</span></p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-right', null, 'Border Right Width', sb_get_option( $id . '-right' ), null, 'small', null, '<span class="right">' ) . " px</span></p>\n";
		$output .= '<p class="' . $id . '">' . sb_input::text( $id . '-radius', null, 'Border Radius', sb_get_option( $id . '-radius' ), null, 'small', null, '<span class="right">' ) . " px</span></p>\n";
		$output .= "\t" . "\t" . '<p><span class="desc">Note: the Border Radius property does not apply to Internet Explorer users.</p>'."\n"; 
		return $output;
	}
	public function wysiwyg( $id, $label, $value, $desc ) {
    	$output = "\t" . '<p class=" . $id . ">'."\n";
        $output .= "\t" . "\t" . '<label for="' . THEME_OPTIONS . '[' . $id . ']' . '">' . $label . '</label><br/>'."\n";
        $info = THEME_OPTIONS . '[' . $id . ']';
		ob_start();
        the_editor( $value, $info );
		$output .= ob_get_clean();
        if ($desc) { $output .= "\t" . "\t" . '<br/><span class="desc"> ' . $desc . ' </span>'."\n"; }
        $output .= "\t" . '</p>'."\n";
        return $output;
    }
}
$sb_input = new sb_input;

/**
 * SB Settings Factory
 *
 * The processor for adding/removing option panels with the Theme Options page.
 *
 * @since StartBox 2.4.2
 */
class sb_settings_factory {
	public $settings = array();
	public $defaults = array( 'sb_analytics_settings', 'sb_content_settings', 'sb_feedburner_settings', 'sb_footer_settings', 'sb_header_settings', 'sb_settings_help', 'sb_pushup_settings', 'sb_seo_settings', 'sb_upgrade_settings' );

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
 * @since StartBox 2.4.8
 */
function sb_remove_default_settings() {
	global $sb_settings_factory;
	add_action( 'admin_init', array($sb_settings_factory, 'unregister_all_defaults' ) );
}
if ( defined('SB_REMOVE_DEFAULT_SETTINGS') ) { sb_remove_default_settings(); }

/**
 * Sets the Default settings for all StartBox options
 *
 * @since StartBox 2.4.6
 */
function sb_set_default_options() {
	// Loop through all theme options and set the defaults
	global $sb_settings_factory;
	$defaults = $theme_options = get_option( THEME_OPTIONS );
	$settings = $sb_settings_factory->settings;

	foreach($settings as $setting){
		$options = $setting->options;
		foreach( $options as $option_id => $option ) {
			if ( isset( $option['default'] ) ) $defaults[$option_id] = $option['default'];
			if ( $option['type'] == 'navigation' ) {
				if ( isset($option['home_default']) ) $defaults[$option_id.'-enable-home'] = $option['home_default'];
				if ( isset($option['position_default']) ) $defaults[$option_id.'-position'] = $option['position_default'];
				if ( isset($option['depth_default']) ) $defaults[$option_id.'-depth'] = $option['depth_default'];
			}
		}
	}
	
	// Set the default logo
	$defaults['logo-image'] = IMAGES_URL . '/logo.png';

	// Unset the reset variable
	$defaults['reset'] = null;

	// Save the options to the database, Allow child themes to filter what defaults are returned
	update_option( THEME_OPTIONS, apply_filters( 'sb_option_defaults', $defaults ) );
}
add_action( 'sb_install', 'sb_set_default_options' );

/**
 * Adds an option to the options db.
 *
 * @since StartBox 2.4.4
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
 * @since StartBox 2.4.4
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
 * @since StartBox 2.4.4
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
	if ( isset($options[$name]) ) {
		return $options[$name];
	} else {
		return false;
	}
}

/**
 * Deletes an option from the options db.
 *
 * @since StartBox 2.4.4
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
 * Helper function for easily removing actions hooked in via classes
 *
 * @since StartBox 2.4.4
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
 * @since StartBox 2.4.4
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
 * Helper function for outputting valid CSS for background-type optionss
 *
 * @since StartBox 2.4.4
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

/**
 * StartBox Handle Upload AJAX
 *
 * Necessary functions for handling the media uploads gracefully.
 * Currently only supports images.
 *
 * @uses sb_handle_upload
 * @since StartBox 2.4.4
 */
function sb_handle_upload_ajax()
{
	// check_ajax_referer('sb'); // security
	$thumb = $full = array();
	$error = '';
	if( !isset($_REQUEST['file_id']) )
		$error = htmlentities( sb_error(7, array(), false) ); // no $file_id found, error out (with no html formatting)
	if($error == '')
	{
		$id = sb_handle_upload($_REQUEST['file_id']);
		if(is_numeric($id))
		{
			$thumb = wp_get_attachment_image_src($id, 'thumbnail');
			$full = wp_get_attachment_image_src($id, 'full');
			if($thumb[0] == '' || $full[0] == '')
				$error = 'Error: Could not retrieve uploaded image.';
		}
		else
		{
			$error = $id;
		}
	}
	die(json_encode( array('thumb' => $thumb[0], 'full' => $full[0], 'error' => $error) ));
}
add_action('wp_ajax_sb_action_handle_upload_ajax', 'sb_handle_upload_ajax');

/**
 * StartBox Upload Handler
 * 
 * @param integer $file_id the ID of the media to be uploaded
 *
 * @since StartBox 2.4.4
 */
function sb_handle_upload($file_id = '')
{	
	if(empty($_FILES))
		return 'Error: No file received.';

	return media_handle_upload($file_id, 0, array(), array('test_form' => false)); // returns attachment id
}

// Custom Media Tab: Suggested Files -- Credit: Joel Kuczmarski
function sb_filter_media_upload_tabs($_default_tabs) {
    if( isset( $_GET['suggested'] ) && $_GET['suggested'] != '')
        $_default_tabs['suggested'] = __( 'Suggested', 'startbox' );

    return $_default_tabs;
}
add_filter('media_upload_tabs', 'sb_filter_media_upload_tabs');

function sb_media_upload_suggested() {
    $errors = array();

    if(!empty($_POST))
    {
        $return = media_upload_form_handler();

        if(is_string($return))
            return $return;
        if(is_array($return))
            $errors = $return;
    }

    return wp_iframe( 'sb_media_upload_suggested_form', $errors );
}
add_action('media_upload_suggested', 'sb_media_upload_suggested');

function sb_media_upload_suggested_form($errors) {
    global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types, $images;

    media_upload_header();

    $images = explode(', ', $_GET['suggested']);

?>
    <script type="text/javascript">
    function doSend(url) {
        var win = window.dialogArguments || opener || parent || top;
        window.parent.send_to_editor(url);
        return false;
    }
    </script>

    <div style="margin:1em;">
        <h3 class="media-title">Use media files suggested by your theme</h3>
        <div id="media-items">
<?php
    $missing = array(); // to store all missing files for later error output
    foreach($images as $index => $image) :
        global $blog_id;
        $replace = explode('/', get_blog_details($blog_id)->path); // necessary for timthumb to play nice with WordPress Multisite
		if( is_subdomain_install() )
			$theme_uri = THEME_URI;
		else
			$theme_uri = str_replace($replace[2] . '/', '', THEME_URI);
        $fullimage = $theme_uri . '/' . $image;

        if(!@getimagesize($fullimage)) // doing this funky-ness b.c. file_exists doesn't want to work for these URLs...
        {
            array_push($missing, $image);
            continue;
        }
?>
            <div id="media-item-<?php echo $index; ?>" class="media-item">
                <div style="float:right; width:30%; text-align:center; padding-top:35px;"><input type="button" value="Insert into Post" class="button" onclick="doSend('<?php echo $fullimage; ?>')"></div>
                <div style="width:70%; height:100px; overflow:hidden;">
                    <img src="<?php echo SCRIPTS_URL; ?>/timthumb.php?src=<?php echo $fullimage; ?>&amp;h=100&amp;zc=1&amp;cropfrom=middleleft&amp;q=100" alt="" height="100" />
                </div>
                <div style="clear:both;"></div>
            </div>
<?php
	endforeach;

    // error output:
    if(count($missing) > 0) : ?>
        <div class="media-item">
            <div style="padding:1em;">
                Warning! The following items are missing from the current theme's directory:
                <ul style="list-style:inside circle; margin-top:1em;">
                <?php foreach($missing as $index => $url)
                        echo '<li style="padding-left:1em;">' . $url . '</li>'; ?>
                </ul>
            </div>
        </div>
<?php endif; ?>
        </div>
    </div>
<?php
}

/**
 * Adds contextual help for all StartBox Options
 *
 * @since StartBox 2.4.9
 */
function sb_admin_help() {
	global $sb_settings_factory;
	$defaults = $theme_options = get_option( THEME_OPTIONS );
	$settings = $sb_settings_factory->settings;
	
	$output = '<p>Below you will find contextual help for all the theme options on this page.</p>';

	foreach($settings as $setting){
		if ( isset($setting->description) ) {
			$output .= '<div style="width:45%; margin-right:5%; position:relative; float:left;">';
			$output .= '<h4 style="margin:1em 0 0; display:block;">' . $setting->name . '</h4>';
			$output .= '<p style="margin:0 0 1em;">' . $setting->description . '</p>';
			
			$options = $setting->options;
			foreach( $options as $option_id => $option ) {
				if ( isset( $option['help'] ) ) $output .= $option['help'];
			}
			
			$output .= '</div>';
		}
	}
	
	$output .= '<p style="clear:both;">' . sprintf( __( 'For more information, try the %sTheme Documentation%s or %sSupport Forum%s', 'startbox' ), '<a href="' . apply_filters( 'sb_theme_docs', 'http://docs.wpstartbox.com' ) . '" target="_blank">', '</a>',  '<a href="' . apply_filters( 'sb_theme_support', 'http://wpstartbox.com/support/forum' ) . '" target="_blank" >', '</a>' ) . '</p>';
	
	add_contextual_help( 'appearance_page_sb_admin', $output ); 
}
add_action( 'admin_init', 'sb_admin_help' );

/**
 * Add a new option to an existing metabox
 *
 * @link http://docs.wpstartbox.com/child-themes/theme-options/ Using Theme Options
 *
 * @since StartBox 2.4.9
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
 * @since StartBox 2.4.9
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

?>