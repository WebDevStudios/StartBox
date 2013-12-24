<?php
/**
 * SBX RSS Feeds
 *
 * Creates a redirect for RSS feeds based on the user input.
 *
 * @package SBX
 * @subpackage Options
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class sb_custom_feed_settings extends SB_Settings {

	function sb_custom_feed_settings() {

		$this->name = __( 'Custom Feeds', 'sbx' );
		$this->slug = 'sb_custom_feed_settings';
		$this->description = __( 'Allows you to replace the default WordPress RSS URLs with a custom URL, such as Feedburner.', 'sbx' );
		$this->location = 'primary';
		$this->priority = 'core';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'custom_rss_feed' => array(
					'type'		=> 'text',
					'label'		=> __( 'Enter your custom RSS feed URL', 'sbx' ),
					'sanitize'	=> false,
					'help'		=> __( 'You can override the default WordPress RSS feed URL here.', 'sbx' ),
					'align'		=> '',
					'size'		=> 'large',
				),
			'custom_comment_rss_feed' => array(
					'type'		=> 'text',
					'label'		=> __( 'Enter your custom comment RSS feed URL', 'sbx' ),
					'desc'      => __( 'Allows you to replace the default WordPress RSS URLs with a custom URL, such as Feedburner.', 'sbx' ),
					'sanitize'	=> false,
					'help'		=> __( 'You can override the default WordPress comment RSS feed URL here.', 'sbx' ),
					'align'		=> '',
					'size'		=> 'large',
				)
		);

		parent::__construct();

	}


	function feed_output( $output, $feed ) {

		$rss_url = sb_get_option( 'custom_rss_feed' );

		if ( $rss_url && ! mb_strpos( $output, 'comments' ) ) {

			// Set custom rss feed
			return esc_url( $rss_url );

		}

		$rss_comment_url = sb_get_option( 'custom_comment_rss_feed' );

		if ( $rss_comment_url && mb_strpos( $output, 'comments' ) ) {

			// Set custom comment rss feed
			return esc_url( $rss_comment_url );

		}

		return $output;

	}

	function hooks() {
		add_filter( 'feed_link', array( $this, 'feed_output' ), 10, 2 );
	}

}

sb_register_settings( 'sb_custom_feed_settings' );
