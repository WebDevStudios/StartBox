<?php

/**
 * These settings create an input for analytics tracking code that gets hooked into wp_head
 */
class sb_analytics_settings extends sb_settings {

	function sb_analytics_settings() {
		$this->name = __( 'Google Analytics', 'startbox');
		$this->slug = 'sb_analytics_settings';
		$this->description = __( 'Allows you to include the tracking codes for your preferred site statistics software (like Google Analytics, Woopra, etc).', 'startbox' );
		$this->location = 'secondary';
		$this->priority = 'core';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'analytics' => array(
					'type'		=> 'textarea',
					'label'		=> sprintf( __('Enter your %s code below:', 'startbox'), '<a href="http://google.com/analytics" target="_blank">analytics</a>' ),
					'sanitize'	=> false,
					'kses'		=> 'unfiltered_html',
					'help'		=> __( 'Though intended specifically for analytics code, you can actually paste any code here that you would like to add to the &lt;head&gt; section of all your pages.', 'startbox' )
				)
		);
		parent::__construct();
	}


	function output() {
		if ( sb_get_option( 'analytics' ) ) {
			echo "\n\n".'<!-- BEGIN Analytics-->'."\n";
			echo sb_get_option( 'analytics' )."\n";
			echo '<!-- END Analytics-->'."\n";
		}
	}

	function hooks() {
		add_action('wp_head', array( $this, 'output' ) );
	}

}

sb_register_settings('sb_analytics_settings');