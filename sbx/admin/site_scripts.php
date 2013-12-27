<?php
/**
 * SBX Header Scripts
 *
 * Creates an input for header script code that gets hooked into wp_head.
 *
 * @package SBX
 * @subpackage Admin
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class sbx_site_script_settings extends SB_Settings {

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function sbx_site_script_settings() {

		$this->name              = __( 'Site Scripts', 'sbx' );
		$this->slug              = 'sbx_site_script_settings';
		$this->description       = __( 'Include custom scripts in the header and/or footer of your website (e.g. Google Analytics).', 'sbx' );
		$this->location          = 'primary';
		$this->priority          = 'low';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options           = array(
			'header_scripts' => array(
					'type'     => 'textarea',
					'label'    => __( 'Header Scripts:', 'sbx' ),
					'desc'     => sprintf( __( 'Scripts placed here will appear in the %s portion of your site.', 'sbx' ), '<code>&lt;head></code>' ),
					'sanitize' => false,
					'kses'     => 'unfiltered_html',
					'help'     => __( 'Code included here will appear in the &lt;head&gt; section of all your pages.', 'sbx' )
				),
			'footer_scripts' => array(
					'type'     => 'textarea',
					'label'    => __( 'Footer Scripts:', 'sbx' ),
					'desc'     => sprintf( __( 'Scripts placed here will appear just before the closing %s tag of your site.', 'sbx' ), '<code>&lt;/body></code>' ),
					'sanitize' => false,
					'kses'     => 'unfiltered_html',
					'help'     => __( 'Code included here will appear just before the closing &lt;body&gt; section of all your pages.', 'sbx' )
				),
		);

		parent::__construct();

	}

	/**
	 * Header Script Output.
	 *
	 * @since 1.0.0
	 */
	function header_output() {
		if ( sb_get_option( 'header_scripts' ) ) {
			echo "\n\n<!-- BEGIN SBX header scripts -->\n";
			echo sb_get_option( 'header_scripts' );
			echo "\n<!-- END SBX header scripts -->\n\n";
		}
	}

	/**
	 * Footer Script Output.
	 *
	 * @since 1.0.0
	 */
	function footer_output() {
		if ( sb_get_option( 'footer_scripts' ) ) {
			echo "\n\n<!-- BEGIN SBX footer scripts -->\n";
			echo sb_get_option( 'footer_scripts' );
			echo "\n<!-- END SBX footer scripts -->\n\n";
		}
	}

	/**
	 * Hooks
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function hooks() {
		add_action( 'wp_head', array( $this, 'header_output' ) );
		add_action( 'wp_footer', array( $this, 'footer_output' ) );
	}

}

sb_register_settings( 'sbx_site_script_settings' );
