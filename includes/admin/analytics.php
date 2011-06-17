<?php
	class sb_analytics_settings extends sb_settings {

		function sb_analytics_settings() {
			$this->name = __( 'Google Analytics', 'startbox');
			$this->slug = 'sb_analytics_settings';
			$this->description = __( 'Paste in your tracking codes for your preferred site statistics software (like Google Analytics).', 'startbox' );
			$this->location = 'secondary';
			$this->priority = 'core';
			$this->options = array(
				'analytics' => array(
						'type'	=> 'textarea',
						'label'	=> sprintf( __('Enter your %s code below:'), '<a href="http://google.com/analytics" target="_blank">analytics</a>' ),
						'sanitize'	=> false
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
	
?>