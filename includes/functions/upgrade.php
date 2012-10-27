<?php
/**
 * StartBox Upgrade class
 *
 * Handles all upgrade checks, notifications and updates. Major props to Carl Hancock and the Gravity Forms team for helping piece this one together.
 *
 * @package StartBox
 * @subpackage Upgrades
 * @since 2.4.9
 */

class sb_upgrade {

	public function __construct() {

		// Hook everything where it belongs so upgrades will work
		add_filter('site_transient_update_themes', array( $this, 'update_include') );
		add_filter('transient_update_themes', array( $this, 'update_include') );
		add_action('admin_notices', array( $this, 'update_notification') );
		add_action('load-update.php', array( $this, 'clear_update_transient') );
		add_action('load-themes.php', array( $this, 'clear_update_transient') );

		// If we already have a version set, and it's older than current, update!
		if ( version_compare( get_option( 'startbox_version' ), SB_VERSION, '<') )
			$this->perform_upgrade();
	}

	// Check to see if a new version of StartBox is available.
	function update_check() {
		global $wpdb;

		// Don't bother checking if updates are disabled
		if (!sb_get_option('enable_updates'))
			return;

		$sb_update = get_transient('sb_update');

		if ( !$sb_update ) {
			$options = array(
				'method' => 'POST',
				'timeout' => 3,
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
		            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
		            'Referer' => home_url()
				)
			);
			$sb = SB_VERSION;
			$wp = get_bloginfo("version") ;
			$php = phpversion();
			$mysql = $wpdb->db_version();
			$use_beta = "false";
			$url = 'http://wpstartbox.com/updates/index.php?product=StartBox&sb_version=' . urlencode($sb) . '&wp_version=' . urlencode($wp) . '&php_version=' . urlencode($php) . '&mysql_version=' . urlencode($mysql) . '&use_beta=' . $use_beta;
			$raw_response = wp_remote_request($url, $options);
			$sb_update = wp_remote_retrieve_body($raw_response);

			// If an error occurred, return false and store transient for 1 hour
			// Else, unserialize and store transient for 12hrs
			if ( is_wp_error($sb_update) || $sb_update == 'error' || !is_serialized($sb_update) ) {
				set_transient('sb_update', array('new_version' => SB_VERSION), 3600); // cache for 1hr (3600)
				return false;
			} else {
				$sb_update = maybe_unserialize($sb_update);
				set_transient('sb_update', $sb_update, 43200); // cache for 12hrs (43200)
			}
		}

		// If we're already using the latest version, return false
		if ( version_compare(SB_VERSION, $sb_update['new_version'], '>=') )
			return false;

		return $sb_update;
	}

	// Adds upgrade notification to WP's built-in check
	function update_include($value) {
		if ( $sb_update = $this->update_check() ) {
			$value->response['startbox'] = $sb_update;
		}
		return $value;
	}

	// Add an update alert to the dashboard when upgrade is available
	function update_notification() {

		// Don't bother checking if updates are disabled
		if (!sb_get_option('enable_updates') || sb_get_option('disable_update_notifications') )
			return;

		$sb_update = $this->update_check();

		if ( !is_super_admin() || !$sb_update )
			return false;

		$update_url = wp_nonce_url('update.php?action=upgrade-theme&amp;theme=startbox', 'upgrade-theme_startbox');
		$update_onclick = __('Upgrading will overwrite the currently installed version of StartBox. Are you sure you want to upgrade?', 'startbox');

		$output = '<div class="update-nag">';
		$output .= sprintf( __('An update to StartBox is available. <a href="%s?KeepThis=true&TB_iframe=true" class="thickbox thickbox-preview">Check out what\'s new in %s</a> or <a href="%s" onclick="return sb_confirm(\'%s\');">upgrade now</a>.', 'startbox'), esc_url( $sb_update['url'] ), esc_html( $sb_update['new_version'] ), $update_url, esc_js( $update_onclick ) );
		$output .= '</div>';

		echo $output;
	}

	// Delete the update transient and disable the update notification.
	function clear_update_transient() {
		delete_transient('sb_update');
		remove_action('admin_notices', 'sb_update_notification');
	}


	// Upgrade StartBox core -- Available hook: sb_upgrade
	public function perform_upgrade() {

		// Make sure we're not on the current version
		if ( version_compare( get_option('startbox_version'), SB_VERSION, '>=' ) )
			return;

		// Upgrade to 2.4.8
		if ( version_compare( get_option('startbox_version'), '2.4.8', '<' ) ) {

			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'post_thumbnail_width' => 200,
				'post_thumbnail_height' => 200
			);

			// Update column layouts properly
			if ( isset( $theme_settings['home_layout'] ) ) {
				if ( $theme_settings['home_layout'] == '1cr' ) { $new_settings['home_layout'] = 'one-col'; }
				elseif ( $theme_settings['home_layout'] == '2cl' ) { $new_settings['home_layout'] = 'two-col-left'; }
				elseif ( $theme_settings['home_layout'] == '2cr' ) { $new_settings['home_layout'] = 'two-col-right'; }
				elseif ( $theme_settings['home_layout'] == '3cl' ) { $new_settings['home_layout'] = 'three-col-left'; }
				elseif ( $theme_settings['home_layout'] == '3cr' ) { $new_settings['home_layout'] = 'three-col-right'; }
				elseif ( $theme_settings['home_layout'] == '3cb' ) { $new_settings['home_layout'] = 'three-col-both'; }
			} else {
				$theme_settings['home_layout'] = 'two-col-right';
			}

			if ( isset( $theme_settings['layout'] ) ) {
				if ( $theme_settings['layout'] == '1cr' ) { $new_settings['layout'] = 'one-col'; }
				elseif ( $theme_settings['layout'] == '2cl' ) { $new_settings['layout'] = 'two-col-left'; }
				elseif ( $theme_settings['layout'] == '2cr' ) { $new_settings['layout'] = 'two-col-right'; }
				elseif ( $theme_settings['layout'] == '3cl' ) { $new_settings['layout'] = 'three-col-left'; }
				elseif ( $theme_settings['layout'] == '3cr' ) { $new_settings['layout'] = 'three-col-right'; }
				elseif ( $theme_settings['layout'] == '3cb' ) { $new_settings['layout'] = 'three-col-both'; }
			} else {
				$theme_settings['layout'] = 'two-col-right';
			}

			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.8' );
		}

		// Upgrade to 2.4.9
		if ( version_compare( get_option('startbox_version'), '2.4.9', '<') ) {

			$theme_settings = get_option( THEME_OPTIONS );

			if (!isset($theme_settings['nav_after_header'])) $theme_settings['nav_after_header'] = 'pages';
			if (!isset($theme_settings['nav_after_header_home'])) $theme_settings['nav_after_header_home'] = true;
			if (!isset($theme_settings['nav_before_header'])) $theme_settings['nav_before_header'] = 'disabled';
			if (!isset($theme_settings['nav_before_header_home'])) $theme_settings['nav_before_header_home'] = false;

			$new_settings = array(
				'enable_updates'			=> true,
				'primary_nav'				=> $theme_settings['nav_after_header'],
				'prymary_nav-enable-home'	=> $theme_settings['nav_after_header_home'],
				'primary_nav-position'		=> 'sb_after_header',
				'primary_nav-depth'			=> '0',
				'secondary_nav'				=> $theme_settings['nav_before_header'],
				'secondary_nav-enable-home'	=> $theme_settings['nav_before_header_home'],
				'secondary_nav-position' 	=> 'sb_before_header',
				'secondary_nav-depth'		=> '0',
				'site_url'					=> home_url(),
				'site_name'					=> get_bloginfo('name')
			);

			unset($theme_settings['nav_after_header']);
			unset($theme_settings['nav_after_header_home']);
			unset($theme_settings['nav_before_header']);
			unset($theme_settings['nav_before_header_home']);

			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.9' );
		}

		// Upgrade to 2.4.9.2
		if ( version_compare( get_option('startbox_version'), '2.4.9.2', '<') ) {

			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'post_thumbnail_rss'	=> true
			);
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.4.9.2' );
		}

		// Upgrade to 2.5
		if ( version_compare( get_option('startbox_version'), '2.5', '<') ) {

			$theme_settings = get_option( THEME_OPTIONS );
			$new_settings = array(
				'enable_post_thumbnails'			=> true,
				'post_thumbnail_use_attachments'	=> true,
				'post_thumbnail_hide_nophoto'		=> false,
				'post_thumbnail_align'				=> 'tc',
				'post_thumbnail_default_image'		=> IMAGES_URL . '/nophoto.jpg'
			);
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.5' );
		}

		// Upgrade to 2.5.6
		if ( version_compare( get_option('startbox_version'), '2.5.6', '<') ) {
			$theme_settings = get_option( THEME_OPTIONS );
			$theme_settings['layout'] = isset( $theme_settings['layout']) ? $theme_settings['layout'] : '';
			$new_settings = array( 'post_layout' => $theme_settings['layout'] );
			$new_settings = wp_parse_args($new_settings, $theme_settings);
			update_option( THEME_OPTIONS, $new_settings);
			update_option( 'startbox_version', '2.5.6' );
		}

		// Upgrade to 2.6
		if ( version_compare( get_option('startbox_version'), '2.6', '<') ) {

			// Replace the Full Width page template with the one-column layout
			global $wpdb;
			$where = array(
				'meta_key' => '_wp_page_template',
				'meta_value' => 'page-fullwidth.php' );
			$new_values = array(
				'meta_key' => '_wp_page_template', 'meta_value' => '',
				'meta_key' => '_sb_layout', 'meta_value' => 'one-col' );
			$wpdb->update( $wpdb->postmeta, $new_values, $where );

			// If we have existing copyright information in the site
			if ( sb_get_option('enable_copyright') ) {

				// Grab our legacy footer text settings
				$enable_copyright	= sb_get_option('enable_copyright');
				$copyright_year		= sb_get_option('copyright_year') ? sb_get_option('copyright_year') : date('Y');
				$enable_wp_credit	= sb_get_option('enable_wp_credit');
				$enable_sb_credit	= sb_get_option('enable_sb_credit');
				$old_footer_text	= sb_get_option('footer_text');

				// Build new footer text content
				// Roughly: [copyright year="2012"] [site_link].<br/>Proudly powered by [WordPress] and [StartBox].
				$new_footer_text = '';
				if ( $enable_copyright ) { $new_footer_text .= '[copyright year="' . $copyright_year . '"] [site_link].'; }
				if ( $enable_copyright && ( $enable_wp_credit || $enable_sb_credit ) ) { $new_footer_text .= '<br/>'; }
				if ( $enable_wp_credit || $enable_sb_credit ) {
					$new_footer_text .= 'Proudly powered by ';
					if ( $enable_wp_credit ) { $new_footer_text .= '[WordPress]'; }
					if ( $enable_wp_credit && $enable_sb_credit ) { $new_footer_text .= ' and '; }
					if ( $enable_sb_credit ) { $new_footer_text .= '[StartBox]'; }
					$new_footer_text .= '.';
				}
				if ( $old_footer_text ) { $new_footer_text .= '<br/>' . $old_footer_text; }

				// Update our new footer text option
				sb_update_option( 'footer_text', $new_footer_text);

				// Finally, delete our old footer options
				sb_delete_option('enable_copyright');
				sb_delete_option('copyright_year');
				sb_delete_option('enable_wp_credit');
				sb_delete_option('enable_sb_credit');
				sb_delete_option('enable_designer_credit');
				sb_delete_option('site_name');
				sb_delete_option('site_url');
				sb_delete_option('footer_text');

			}

			// Update our working version to 2.6
			update_option( 'startbox_version', '2.6' );

		}

		// Upgrade to 2.6.1
		// if ( version_compare( get_option('startbox_version'), '2.6.1', '<') ) {
		//
		// 	$theme_settings = get_option( THEME_OPTIONS );
		// 	$new_settings = array();
		// 	$new_settings = wp_parse_args($new_settings, $theme_settings);
		// 	update_option( THEME_OPTIONS, $new_settings);
		// 	update_option( 'startbox_version', '2.6.1' );
		// }

		// Included hook for other things to do during upgrade
		do_action( 'sb_upgrade' );

	}

}
$sb_upgrade = new sb_upgrade;