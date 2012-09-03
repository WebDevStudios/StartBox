<?php

/**
 * StartBox functions and definitions
 *
 * Sets up the theme and provides includes some default scripts
 *
 * For help with StartBox, visit http://docs.wpstartbox.com
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 * For more information about Child Themes see http://codex.wordpress.org/Theme_Development and http://codex.wordpress.org/Child_Themes
 *
 * @package StartBox
 * @link http://www.wpstartbox.com
 */

// Initialize StartBox, but only if a child theme hasn't already
if( !did_action( 'sb_init' ) ) { 
	require_once( get_template_directory() . '/includes/functions/startbox.php' );
	StartBox::init();
}

?>