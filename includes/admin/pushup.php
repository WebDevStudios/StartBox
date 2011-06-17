<?php
	class sb_pushup_settings extends sb_settings {
		
		public function sb_pushup_settings() {
			$this->name = __( 'Save the Web', 'startbox');
			$this->slug = 'sb_pushup_settings';
			$this->location = 'secondary';
			$this->priority = 'low';
			$this->options = array(
					'intro' => array(
							'type'		=> 'intro',
							'desc'		=>	sprintf( __( 'Help save the internet by letting users of older browsers know that it\'s time for an upgrade. %sSee an example &raquo;%s', 'startbox'), '<a href="http://pushuptheweb.com/" title="Push Up The Web" target="_blank">', '</a>')
						),
					'enable_pushup' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Save the web!', 'startbox' ),
							'default'	=> 'true'
						)
				);
			parent::__construct();
		}

		public function sb_pushup_output() {
			if ( sb_get_option( 'enable_pushup' ) ) {
				wp_enqueue_script( 'pushup' );
				wp_enqueue_style( 'pushup' );
			}
		}
		public function hooks() {
			add_action( 'template_redirect', array($this, 'sb_pushup_output') );
		}

	}

	sb_register_settings('sb_pushup_settings');	

?>