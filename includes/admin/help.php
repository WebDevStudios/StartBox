<?php
	class sb_settings_help extends sb_settings {
		
		function sb_settings_help() {
			$this->name = __( 'Need Help?', 'startbox' );
			$this->slug = 'sb_settings_help';
			$this->location = 'secondary';
			$this->priority = 'high';
			parent::__construct();
		}

		function form() {
			echo '<p>' . sprintf( __( 'Struggling with some of the theme options or settings? Have a look at the comprehensive %1$stheme documentation%2$s.', 'startbox' ), '<a href="http://docs.wpstartbox.com" target="_blank">', '</a>' ) . '</p>';
		}

	}
	
	sb_register_settings('sb_settings_help');

?>