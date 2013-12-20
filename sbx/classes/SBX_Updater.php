<?php
/**
 * StartBox Update Class
 *
 * Handles all upgrade checks, notifications and updates.
 * Major props to Carl Hancock and the Gravity Forms team
 * for helping piece this one together.
 *
 * @package StartBox
 * @subpackage Classes
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports updates, skip the rest if not
if ( ! current_theme_supports( 'sbx-updates' ) )
	return;

class SBX_Updater {

	public function __construct() {

		// Hook everything where it belongs so upgrades will work
		add_filter('site_transient_update_themes', array( $this, 'update_include') );
		add_filter('transient_update_themes', array( $this, 'update_include') );
		add_action('admin_notices', array( $this, 'update_notification') );
		add_action('load-update.php', array( $this, 'clear_update_transient') );
		add_action('load-themes.php', array( $this, 'clear_update_transient') );

	}

	// Check to see if a new version of StartBox is available.
	function update_check() {
		global $wpdb;

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
			$sb = SBX_VERSION;
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
				set_transient('sb_update', array('new_version' => SBX_VERSION), 3600); // cache for 1hr (3600)
				return false;
			} else {
				$sb_update = maybe_unserialize($sb_update);
				set_transient('sb_update', $sb_update, 43200); // cache for 12hrs (43200)
			}
		}

		// If we're already using the latest version, return false
		if ( version_compare(SBX_VERSION, $sb_update['new_version'], '>=') )
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

}
$GLOBALS['startbox']->updater = new SBX_Updater;
