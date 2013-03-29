<?php
/**
 * Registers a settings metabox to direct users to the help tab
 */
class sb_settings_help extends sb_settings {

	function sb_settings_help() {
		$this->name = __( 'Need Help?', 'startbox' );
		$this->slug = 'sb_settings_help';
		$this->location = 'secondary';
		$this->priority = 'high';
		parent::__construct();
	}

	function admin_form() {
		echo '<p>' . __( 'Struggling with some of the theme options or settings? Click on the "Help" tab above.', 'startbox' ) . '</p>';
		echo '<p>' . sprintf( __( 'You can also visit the StartBox <a href="%s" target="_blank">support forum</a>', 'startbox' ), 'http://wpstartbox.com/support/' ) . '</p>';
	}

}

sb_register_settings('sb_settings_help');