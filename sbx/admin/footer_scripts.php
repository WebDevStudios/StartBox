<?php
/**
 * SBX Footer Scripts
 *
 * Creates an input for footer script code that gets hooked into wp_footert.
 *
 * @package SBX
 * @subpackage Admin
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class sb_footer_scripts_settings extends SB_Settings {

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function sb_footer_scripts_settings() {

		$this->name = __( 'Footer Scripts', 'sbx' );
		$this->slug = 'sb_footer_scripts_settings';
		$this->description = __( 'Allows you to include scripts in the footer of your website (like jQuery, Woopra, etc).', 'sbx' );
		$this->location = 'primary';
		$this->priority = 'low';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'footer_scripts' => array(
					'type'		=> 'textarea',
					'label'		=> __( 'Enter your footer scripts below:', 'sbx' ),
					'desc'      => __( 'Allows you to include scripts in the footer of your website (like jQuery, Woopra, etc).', 'sbx' ),
					'sanitize'	=> false,
					'kses'		=> 'unfiltered_html',
					'help'		=> __( 'You can paste any code here that you would like to add to the &lt;footer&gt; section of all your pages.', 'sbx' )
				)
		);

		parent::__construct();

	}


	/**
	 * Output
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function output() {

		if ( sb_get_option( 'footer_scripts' ) ) {

			echo "\n\n".'<!-- BEGIN SBX footer scripts -->'."\n";
			echo sb_get_option( 'footer_scripts' )."\n";
			echo '<!-- END SBX footer scripts -->'."\n";

		}

	}

	/**
	 * Hooks
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function hooks() {
		add_action( 'wp_footer', array( $this, 'output' ) );
	}

}

sb_register_settings( 'sb_footer_scripts_settings' );
