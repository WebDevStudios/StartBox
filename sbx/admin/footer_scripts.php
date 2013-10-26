<?php

/**
 * These settings create an input for footer script code that gets hooked into wp_footer
 */
class sb_footer_scripts_settings extends sb_settings {

	function sb_footer_scripts_settings() {
		
		$this->name = __( 'Footer Scripts', 'startbox' );
		$this->slug = 'sb_footer_scripts_settings';
		$this->description = __( 'Allows you to include scripts in the footer of your website (like jQuery, Woopra, etc).', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'low';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'footer_scripts' => array(
					'type'		=> 'textarea',
					'label'		=> __( 'Enter your footer scripts below:', 'startbox' ),
					'desc'      => __( 'Allows you to include scripts in the footer of your website (like jQuery, Woopra, etc).', 'startbox' ),
					'sanitize'	=> false,
					'kses'		=> 'unfiltered_html',
					'help'		=> __( 'You can paste any code here that you would like to add to the &lt;footer&gt; section of all your pages.', 'startbox' )
				)
		);

		parent::__construct();

	}


	function output() {

		if ( sb_get_option( 'footer_scripts' ) ) {

			echo "\n\n".'<!-- BEGIN StartBox footer scripts -->'."\n";
			echo sb_get_option( 'footer_scripts' )."\n";
			echo '<!-- END StartBox footer scripts -->'."\n";

		}

	}

	function hooks() {

		add_action( 'wp_footer', array( $this, 'output' ) );

	}

}

sb_register_settings( 'sb_footer_scripts_settings' );