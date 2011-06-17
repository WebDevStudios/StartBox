<?php
	class sb_upgrade_settings extends sb_settings {

		function sb_upgrade_settings() {
			$this->name = __( 'Version Information', 'startbox');
			$this->slug = 'sb_upgrade_settings';
			$this->description = __( 'Enable automatic upgrades by checking one box and providing your StartBox license key.', 'startbox' );
			$this->location = 'secondary';
			$this->priority = 'high';
			$this->options = array(
				'sb_version_info' => array(
					'type'	=> 'intro',
					'desc'	=> sprintf(__('StartBox Version: %s', 'startbox'), get_option('startbox_version') )
				),
				'enable_updates' => array(
						'type'		=> 'checkbox',
						'label'		=> __('Enable Automatic Updates', 'startbox'),
						'default'	=> true
				),
				'sb_license' => array(
					'type'	=> 'text',
					'label'	=> 'StartBox License',
					'desc'	=> __( 'Enter your license to enable automatic updates.', 'startbox' )
				)
			);
			parent::__construct();
		}

	}
	
	if ( current_theme_supports('sb-updates') ) sb_register_settings('sb_upgrade_settings');
	
?>