<?php
/**
 * Settings for controlling various brand-related options like logo, favicon, etc.
 */
class sb_favicon_settings extends sb_settings {

	function sb_favicon_settings() {
		$this->name = __( 'Favicon', 'startbox' );
		$this->slug = 'sb_favicon_settings';
		$this->description = __( 'Upload a custom favicon.', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'core';
		$this->options = array(
			'favicon' => array(
					'type'		=> 'upload',
					'label'		=> __( 'Favicon', 'startbox' ),
					'desc'		=> sprintf( __( 'The %s is a small logo/icon that displays alongside your URL or in the page tab of most browsers.', 'startbox' ), '<a href="http://en.wikipedia.org/wiki/Favicon" target="_blank">favicon</a>' ),
					'default'	=> '/wp-content/themes/startbox/images/favicon.png',
					'help'		=> __( 'Specify a custom favicon for use in the navigation bar or browser tab for your site', 'startbox' )
				)
			);
			parent::__construct();
	}

	function favicon() {

		if ( sb_get_option( 'favicon' ) ) {
			echo '<link rel="icon" type="image/png" href="' . esc_url( sb_get_option( 'favicon' ) ) . '" />'."\n";
		} else {
			echo '<link rel="icon" type="image/png" href="' . SB_IMAGES . '/favicon.png" />'."\n";
		}
		
	}

	function hooks() {
		add_action( 'wp_head', array( $this, 'favicon' ) );
	}

}

sb_register_settings( 'sb_favicon_settings' );