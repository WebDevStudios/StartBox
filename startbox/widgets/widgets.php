<?php
/**
 * StartBox Widget Loader
 *
 * @package StartBox
 * @subpackage Widgets
 * @author  WebDev Studios
 * @link    http://wpstartbox.com/
 * @license GPL-2.0+
 */

/**
 * Register widgets for use with StartBox themes.
 *
 * See individual widget class files for further documentation.
 *
 * @since 2.7.2
 */
function sb_load_widgets() {
	register_widget( 'SB_Widget_Featured_Content' );
	register_widget( 'SB_Widget_Search' );
	register_widget( 'SB_Widget_Social' );
	register_widget( 'SB_Widget_Tag_Cloud' );

	// Ideally we'd replace the native Search and Tag Cloud widgets, by setting their id_base to be 'search' and
	// 'tag_cloud'. However, doing that now would cause a BC-breakage as widget settings are saved under a key that
	// uses the id_base in it's name. As such, we simply have to unregister the default widgets for now.
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Tag_Cloud' );
}
add_action( 'widgets_init', 'sb_load_widgets' );

/**
 * Enqueue JavaScript file for Featured Content widget.
 *
 * @since Unknown
 */
function load_featured_widget_js() {
	wp_enqueue_script( 'sb-widgets', SCRIPTS_URL . '/widgets.js', array( 'jquery' ), THEME_VERSION );
}
add_action( 'sidebar_admin_setup', 'load_featured_widget_js' );
