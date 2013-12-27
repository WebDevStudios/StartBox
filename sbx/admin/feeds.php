<?php
/**
 * SBX RSS Feeds
 *
 * Creates a redirect for RSS feeds based on the user input.
 *
 * @package SBX
 * @subpackage Admin
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class sb_custom_feed_settings extends SB_Settings {

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function sb_custom_feed_settings() {

		$this->name              = __( 'Custom Feeds', 'sbx' );
		$this->slug              = 'sb_custom_feed_settings';
		$this->description       = __( 'Allows you to replace the default WordPress RSS URLs with a custom URL or service.', 'sbx' );
		$this->location          = 'primary';
		$this->priority          = 'core';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options           = array(
			'custom_rss_feed' => array(
					'type'     => 'text',
					'label'    => __( 'Custom RSS feed URL', 'sbx' ),
					'sanitize' => false,
					'desc'     => __( 'Override the default WordPress RSS feed URL.', 'sbx' ),
					'help'     => __( 'Override the default WordPress RSS feed URL.', 'sbx' ),
					'size'     => 'large',
				),
			'custom_comment_rss_feed' => array(
					'type'		=> 'text',
					'label'		=> __( 'Custom comment RSS feed URL', 'sbx' ),
					'sanitize'	=> false,
					'desc'      => __( 'Override the default Comment RSS feed URL.', 'sbx' ),
					'help'      => __( 'Override the default Comment RSS feed URL.', 'sbx' ),
					'size'		=> 'large',
				)
		);

		parent::__construct();

	}


	/**
	 * Output
	 *
	 * @since  1.0.0
	 * @param  [type] $output [description]
	 * @param  [type] $feed   [description]
	 * @return [type]         [description]
	 */
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

	/**
	 * Hook
	 *
	 * @since  1.0.0
	 * @return [type] [description]
	 */
	function hooks() {
		add_filter( 'feed_link', array( $this, 'feed_output' ), 10, 2 );
	}

}

sb_register_settings( 'sb_custom_feed_settings' );
