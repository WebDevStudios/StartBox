<?php
/**
 * Creates Theme Options page and enqueues all necessary scripts
 *
 * @since 2.2.8
 */
function sb_admin_init() {

	// Setup our global admin variable
	global $sb_admin;

	// Create our settings page and add it to the menu
	$sb_admin = add_theme_page( THEME_NAME." Options", __('Theme Options', 'startbox'), 'edit_theme_options', 'sb_admin', 'sb_admin_page' );

	// Register our custom settings field
	register_setting( 'sb_admin', THEME_OPTIONS, 'sb_sanitize');

	// Load in our custom JS and help content
	add_action( 'load-' . $sb_admin, 'sb_admin_load' );
	add_action( 'load-' . $sb_admin, 'sb_admin_help');

}
add_action( 'admin_menu', 'sb_admin_init' );

/**
 * Add a menu item for Theme Options to the admin bar
 *
 * @since 2.4
 */
function sb_admin_bar_init() {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array( 'id' => 'theme-options', 'parent' => 'appearance', 'title' => __('Theme Options', 'startbox'), 'href' => admin_url( 'themes.php?page=sb_admin' ) ) );
}
add_action( 'wp_before_admin_bar_render', 'sb_admin_bar_init' );

/**
 * Adds contextual help for all StartBox Options
 *
 * @since 2.5.5
 */
function sb_admin_help() {
    global $sb_admin, $wp_version;

	// Make sure we're on at least WP3.3
	if ( version_compare( $wp_version, '3.3', '>=' ) ) {
	    $screen = get_current_screen();

	    // Don't add help tab if screen is not sb_admin
	    if ( $screen->id != $sb_admin ) return;

		// Grab our theme options
		global $sb_settings_factory;
		$defaults = $theme_options = get_option( THEME_OPTIONS );
		$settings = $sb_settings_factory->settings;

		// Add our generic helper text no matter what
		$screen->add_help_tab( array(
			'id'		=> 'sb_need_help',
			'title'		=> __( 'Additional Resources', 'startbox' ),
			'content'	=> __( '<h3>Additional Resources</h3>', 'startbox' ) . '<p>' . sprintf( __( 'For more information, try the %s or %s.', 'startbox' ), '<a href="' . apply_filters( 'sb_theme_docs', 'http://docs.wpstartbox.com' ) . '" target="_blank">' . __( 'Theme Documentation', 'startbox') . '</a>',  '<a href="' . apply_filters( 'sb_theme_support', 'http://wpstartbox.com/support/' ) . '" target="_blank" >' . __( 'Support Forum', 'startbox' ) . '</a>' ) . '</p>'
		) );

		// Loop through each option panel
		foreach ( $settings as $setting ) {

			// Only include options panels that have a description set
			if ( isset($setting->description) ) {
				$output = '';
				$output .= '<h3>' . $setting->name . '</h3>';
				$output .= '<p>' . $setting->description . '</p>';

				// loop through each individual option to find help text, include it in output if found
				$options = $setting->options;
				foreach( $options as $option_id => $option ) {
					if ( isset( $option['help'] ) )
						$output .= '<p style="padding:8px 0; margin:0; border-top:1px solid #eee;"><strong>' .  rtrim( $option['label'], ':' ) . '</strong> &ndash; ' . $option['help'] . '</p>';
				}

				// Add the help tab
			    $screen->add_help_tab( array(
			        'id'		=> $setting->slug,
			        'title'		=> $setting->name,
			        'content'	=> $output,
			    ) );
			} // end if isset
		} // end foreach
	} // end if version compare

}

function sb_admin_load() {

	global $sb_admin;

	add_screen_option( 'layout_columns', array('max' => 2, 'default' => 2) );

	// Load the scripts for handling metaboxes
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');

	// Load StartBox-specific scripts and styles
	wp_enqueue_script( 'colorbox' );
	wp_enqueue_script( 'jquery-ajaxuploader', SCRIPTS_URL . '/jquery.ajaxupload.js' );
	wp_enqueue_script( 'jquery-colorpicker', SCRIPTS_URL . '/colorpicker/js/colorpicker.js' );
	wp_enqueue_script( 'sb-admin', SCRIPTS_URL . '/admin.js', array('jquery-colorpicker') );
	wp_enqueue_style( 'colorpicker', SCRIPTS_URL . '/colorpicker/css/colorpicker.css' );
	wp_enqueue_style( 'sb-admin', STYLES_URL . '/admin.css' );
	wp_enqueue_style( 'colorbox' );

	// Load scripts for TinyMCE (Credit: Lee Doel)
	if ( user_can_richedit() ){
		wp_enqueue_script('editor');
		wp_enqueue_script('media-upload');
		wp_enqueue_style( 'thickbox' );
	}

	// Reset our theme options back to default
	if ( sb_get_option('reset') ) { sb_set_default_options(); wp_redirect( admin_url( 'themes.php?page=sb_admin&reset=true' ) ); }
}

/**
 * Adds the Screen Options tab with 2 columns. Credit: http://www.code-styling.de/english/how-to-use-wordpress-metaboxes-at-own-plugins
 */
function sb_screen_options($columns, $screen) {
	global $sb_admin;
	if ($screen == $sb_admin) {
		$columns[$sb_admin] = 2;
	}
	return $columns;
}
// add_filter('screen_layout_columns', 'sb_screen_options', 10, 2);



/**
 * Callback for StartBox Theme Options Page layout
 *
 * @since 2.2.8
 */
function sb_admin_page() { global $sb_admin; ?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>

    <h2><?php echo THEME_NAME; ?> <?php _e( 'Options', 'startbox'); ?></h2>

	<?php
		if ( isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == true ) {
			echo '<div id="message" class="updated fade"><p>' . THEME_NAME . ' ' . __( 'Options Updated.', 'startbox') . '</p></div>';
		}
	?>

    <div id="poststuff" class="metabox-holder<?php global $screen_layout_columns; echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
    	<form method="post" enctype="multipart/form-data" action="options.php" id="sb_options">
		<?php
			// Include Save/Reset buttons in header
			sb_admin_buttons();

			// Make metaboxes work proper
			wp_nonce_field( 'sb-admin-metaboxes' );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

			// Make settings usable
			settings_fields( 'sb_admin' );
		?>
        <div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
            	<?php do_meta_boxes( $sb_admin, 'primary', null ); ?>
			</div>
        </div>  <!-- postbox-container -->

        <div id="side-info-column" class="inner-sidebar">
        	<?php do_meta_boxes( $sb_admin, 'secondary', null ); ?>
        </div>  <!-- postbox-container -->

		<?php sb_admin_buttons(); ?>
        </form>
    </div>  <!-- metabox-holder -->
</div> <!-- wrap -->
<?php
}

// Include the Admin Buttons
function sb_admin_buttons() { ?>
	<div style="position:relative;clear:both; margin-bottom:20px">
		<input type="submit" name="Submit" value="<?php _e('Save All Settings', 'startbox') ?>" class="button-primary" />
		<input type="submit" name="<?php echo esc_attr( THEME_OPTIONS . '[reset]' ); ?>" value="<?php _e('Reset All Settings', 'startbox') ?>" class="button" onclick="if(confirm('Reset All Theme Settings?')) return true; else return false;" />
	</div>
<?php }

// Perform some basic sanitization to our options on save
function sb_sanitize($inputs) {
	global $sb_settings_factory;
	$settings = $sb_settings_factory->settings;

	foreach ( $settings as $setting ) {
		$options = $setting->options;
		foreach ( $options as $option_id => $option ) {

			// Forcefully set unchecked checkboxes to false so they retain a vailue when unchecked
			if ($option['type'] == 'checkbox' && !isset($inputs[$option_id])) {
				$inputs[$option_id] = false;
			}
			// Basic KSES sanitization
			if ( isset( $option['kses'] ) ) {
				if ( true === $option['kses'] || ( is_string( $option['kses'] ) && !current_user_can( $option['kses'] ) ) )
					$inputs[$option_id] = wp_kses_post( $inputs[$option_id] );
			}
			// Sanitize untrusted textual inputs. Defaults to true. Set 'sanitize' => false for no satitization, or use 'sanitize' => array( 'allowed_html' => '', 'allowed_protocols' => '' ) to allow specific tags.
			if ( ( $option['type'] == 'text' || $option['type'] == 'textarea') && ( isset($option['sanitize']) && $option['sanitize'] != false ) ) {
				$inputs[$option_id] = wp_kses( $inputs[$option_id], $option['sanitize']['allowed_html'], ( empty ( $option['sanitize']['allowed_protocols']) ? array() : $option['sanitize']['allowed_protocols'] ) );
			}

		}
	}
	return $inputs;
}