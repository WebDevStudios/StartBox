<?php
	class sb_feedburner_settings extends sb_settings {
		
		function sb_feedburner_settings() {
			$this->name = __( 'Feedburner Settings', 'startbox' );
			$this->slug = 'sb_feedburner_settings';
			$this->description = __( 'Replace your site-wide default feed URLs with ones from feedburner (or another feed service).', 'startbox' );
			$this->location = 'secondary';
			$this->priority = 'core';
			$this->options = array(
					'feedburner_feed_url' => array(
							'type'	=> 'text',
							'label'	=> sprintf( __( 'Enter your %s feed URL below', 'startbox' ), '<a href="http://feedburner.google.com" target="_blank">Feedburner</a>' ),
							'size' => 'medium'
						),
					'feedburner_comments_url' => array(
							'type'	=> 'text',
							'label'	=> __( 'Enter your Feedburner Comments URL', 'startbox' ),
							'size'	=>	'medium'
						)
				);
			parent::__construct();
		}

		// Adapted directly from Feedburner plugin
		function sb_feed_redirect() {
			global $wp, $feed, $withcomments;
			if (is_feed() && $feed != 'comments-rss2' && !is_single() && $wp->query_vars['category_name'] == '' && ($withcomments != 1) && trim(sb_get_option( 'feedburner_feed_url' )) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim(sb_get_option( 'feedburner_feed_url' )));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			} elseif (is_feed() && ($feed == 'comments-rss2' || $withcomments == 1) && trim(sb_get_option( 'feedburner_comments_url' )) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim(sb_get_option( 'feedburner_comments_url' )));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
		}

		// Also adapted from Feedburner plugin
		function sb_check_url() {
			switch (basename($_SERVER['PHP_SELF'])) {
				case 'wp-rss.php':
				case 'wp-rss2.php':
				case 'wp-atom.php':
				case 'wp-rdf.php':
					if (trim(sb_get_option( 'feedburner_feed_url' )) != '') {
						if (function_exists('status_header')) status_header( 302 );
						header("Location:" . trim(sb_get_option( 'feedburner_feed_url' )));
						header("HTTP/1.1 302 Temporary Redirect");
						exit();
					}
					break;
				case 'wp-commentsrss2.php':
					if (trim(sb_get_option( 'feedburner_comments_url' )) != '') {
						if (function_exists('status_header')) status_header( 302 );
						header("Location:" . trim(sb_get_option( 'feedburner_comments_url' )));
						header("HTTP/1.1 302 Temporary Redirect");
						exit();
					}
					break;
			}
		}
		
		function hooks() {
			if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
				add_action( 'template_redirect', array( $this, 'sb_feed_redirect' ) );
				add_action( 'init', array( $this, 'sb_check_url' ) );
			}
		}

	}
	
	if ( !function_exists('ol_feed_redirect') ) { sb_register_settings('sb_feedburner_settings'); }

?>