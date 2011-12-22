<?php
class sb_upgrade_settings extends sb_settings {

	function sb_upgrade_settings() {
		$this->name = __( 'Version Information', 'startbox');
		$this->slug = 'sb_upgrade_settings';
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
			)
		);
		parent::__construct();
	}

}

if ( current_theme_supports('sb-updates') ) sb_register_settings('sb_upgrade_settings');
?>