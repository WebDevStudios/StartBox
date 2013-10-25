<?php

/**
 * These settings create an input for footer script code that gets hooked into wp_footer
 */
class sb_custom_feed_settings extends sb_settings {

	function sb_custom_feed_settings() {

		$this->name = __( 'Custom Feeds', 'startbox' );
		$this->slug = 'sb_custom_feed_settings';
		$this->description = __( 'Allows you to include scripts in the footer of your website (like Google Analytics, Woopra, etc).', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'core';
		$this->hide_ui_if_cannot = 'unfiltered_html';
		$this->options = array(
			'custom_rss_feed' => array(
					'type'		=> 'text',
					'label'		=> __( 'Enter your custom RSS feed URL', 'startbox' ),
					'sanitize'	=> false,
					'help'		=> __( 'You can override the default WordPress RSS feed URL here.', 'startbox' ),
					'align'		=> 'right',
					'size'		=> 'large',
				),
			'custom_comment_rss_feed' => array(
					'type'		=> 'text',
					'label'		=> __( 'Enter your custom comment RSS feed URL', 'startbox' ),
					'sanitize'	=> false,
					'help'		=> __( 'You can override the default WordPress comment RSS feed URL here.', 'startbox' ),
					'align'		=> 'right',
					'size'		=> 'large',
				)
		);

		parent::__construct();

	}


	function output( $output, $feed ) {

		$rss_url = sb_get_option( 'custom_rss_feed' );

		if ( $rss_url && ! mb_strpos( $output, 'comments' ) ) {

			//set custom rss feed
			return esc_url( $rss_url );

		}

		$rss_comment_url = sb_get_option( 'custom_comment_rss_feed' );

		if ( $rss_comment_url && mb_strpos( $output, 'comments' ) ) {

			//set custom comment rss feed
			return esc_url( $rss_comment_url );

		}

		return $output;

	}

	function hooks() {

		add_filter( 'feed_link', array( $this, 'output' ), 10, 2 );

	}

}

sb_register_settings( 'sb_custom_feed_settings' );