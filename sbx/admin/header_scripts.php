<?php

/**
 * These settings create an input for header script code that gets hooked into wp_head
 */
class sb_header_scripts_settings extends sb_settings {

	function sb_header_scripts_settings() {

		$this->name = __( 'Header Scripts', 'startbox' );
		$this->slug = 'sb_header_scripts_settings';
		$this->description = __( 'Allows you to include scripts in the header of your website', 'startbox' );
		$this->location = 'secondary';
		$this->priority = 'core';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'header_scripts' => array(
					'type'		=> 'textarea',
					'label'		=> __( 'Enter your header scripts below:', 'startbox' ),
					'sanitize'	=> false,
					'kses'		=> 'unfiltered_html',
					'help'		=> __( 'You can paste any code here that you would like to add to the &lt;head&gt; section of all your pages.', 'startbox' )
				)
		);

		parent::__construct();
		
	}


	function output() {

		if ( sb_get_option( 'header_scripts' ) ) {

			echo "\n\n".'<!-- BEGIN StartBox header scripts -->'."\n";
			echo sb_get_option( 'header_scripts' )."\n";
			echo '<!-- END StartBox header scripts -->'."\n";

		}

	}

	function hooks() {

		add_action('wp_head', array( $this, 'output' ) );

	}

}

sb_register_settings( 'sb_header_scripts_settings' );