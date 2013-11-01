<?php
/**
 * Settings for controlling automatic upgrades
 */
class sb_upgrade_settings extends SB_Settings {

	function sb_upgrade_settings() {
		$this->name = __( 'SBX Information', 'startbox' );
		$this->slug = 'sb_upgrade_settings';
		$this->location = 'primary';
		$this->priority = 'high';
		$this->options = array(
			'sb_version_info' => array(
				'type'	=> 'intro',
				'desc'	=> sprintf( __( 'StartBox Version: %s', 'startbox' ), SB_VERSION )
			)
		);

		parent::__construct();

	}

}

// Only register this panel if the theme supports upgrades
if ( current_theme_supports( 'sb-updates' ) )
	sb_register_settings( 'sb_upgrade_settings' );