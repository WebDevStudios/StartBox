<?php
/**
 * SBX Upgrade Engine
 *
 * Settings for controlling automatic upgrades.
 *
 * @package SBX
 * @subpackage Options
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class sb_upgrade_settings extends SB_Settings {

	function sb_upgrade_settings() {
		$this->name = __( 'SBX Information', 'sbx' );
		$this->slug = 'sb_upgrade_settings';
		$this->location = 'primary';
		$this->priority = 'high';
		$this->options = array(
			'sb_version_info' => array(
				'type'	=> 'intro',
				'desc'	=> sprintf( __( 'SBX Version: %s', 'sbx' ), SBX_VERSION )
			)
		);

		parent::__construct();

	}

}

// Only register this panel if the theme supports upgrades
if ( current_theme_supports( 'sb-updates' ) ) {
	sb_register_settings( 'sb_upgrade_settings' );
}
