<?php 
/**
 * Creates Theme Options page and enqueues all necessary scripts
 *
 * @since 2.2.8
 */

function sb_admin_init() {
	global $sb_admin;
	$sb_admin = add_theme_page( THEME_NAME." Options", __('Theme Options', 'startbox'), 'edit_theme_options', 'sb_admin', 'sb_admin_page' );
	register_setting( 'sb_admin', THEME_OPTIONS, 'sb_sanitize');
	add_action( 'load-' . $sb_admin, 'sb_admin_load' );
}
add_action( 'admin_menu', 'sb_admin_init' );

function sb_admin_bar_init() {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'title' => __('Theme Options', 'startbox'), 'href' => admin_url( 'themes.php?page=sb_admin' ) ) );
}
add_action( 'wp_before_admin_bar_render', 'sb_admin_bar_init' );


function sb_admin_load() {
	global $sb_admin;
	
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
        add_action('admin_head', 'wp_tiny_mce');
    }
	
	if ( sb_get_option('reset') ) { sb_set_default_options(); wp_redirect( admin_url( 'admin.php?page=sb_admin&reset=true' ) ); }
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
add_filter('screen_layout_columns', 'sb_screen_options', 10, 2);


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
		elseif ( isset($_REQUEST['reset']) && $_REQUEST['reset'] == true ) {
			echo '<div id="message" class="updated fade"><p>' . THEME_NAME . ' ' . __( 'Options Reset.', 'startbox') . '</p></div>';
		}
		if ( isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] == 'true') {
			echo '<div id="message" class="updated fade"><p>' . sprintf( __('StartBox upgraded to version %s!', 'startbox'), get_option('startbox_version') ) . '</p></div>';
		}
	?>
	
    <div id="poststuff" class="metabox-holder<?php global $screen_layout_columns; echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
    	<form method="post" enctype="multipart/form-data" action="options.php" id="sb_options">
		<?php
			// Include Save/Reset buttons in header
			sb_admin_buttons();
			
			// Make metaboxes work proper
			wp_nonce_field('sb-admin-metaboxes');
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
			
			// Make settings usoable
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
		<input type="submit" name="<?php echo THEME_OPTIONS . '[reset]'; ?>" value="<?php _e('Reset All Settings', 'startbox') ?>" class="button" onclick="if(confirm('Reset All Theme Settings?')) return true; else return false;" />
	</div>
<?php }

// This callback's sole responsibility is to set checkboxes to "false" if unchecked
function sb_sanitize($inputs) {
	global $sb_settings_factory;
	$settings = $sb_settings_factory->settings;

	foreach ( $settings as $setting ) {
		$options = $setting->options;
		foreach ( $options as $option_id => $option ) {
			
			// Set unchecked checkboxes to false, otherwise their value is unset and the default gets set on every save
			if ($option['type'] == 'checkbox' && !isset($inputs[$option_id])) {
				$inputs[$option_id] = false;
			}
			
			// Sanitize untrusted textual inputs. Defaults to true. Set 'sanitize' => false for no satitization, or use 'sanitize' => array( 'allowed_html' => '', 'allowed_protocols' => '' ) to allow specific tags.
			if ( ( $option['type'] == 'text' || $option['type'] == 'textarea') && ( isset($option['sanitize']) && $option['sanitize'] != false ) ) {
				$inputs[$option_id] = wp_kses( $inputs[$option_id], $option['sanitize']['allowed_html'], ( empty ( $option['sanitize']['allowed_protocols']) ? array() : $option['sanitize']['allowed_protocols'] ) );
			}
			
		}
	}
	return $inputs;
}

?>