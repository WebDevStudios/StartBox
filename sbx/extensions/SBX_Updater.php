<?php
/**
 * SBX Automatic Updates
 *
 * Handles all upgrade checks, notifications and updates.
 * Major props to Carl Hancock and the Gravity Forms team
 * for helping piece this one together.
 *
 * @package SBX
 * @subpackage Extensions
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports updates, skip the rest if not
if ( ! current_theme_supports( 'sbx-updates' ) )
	return;

/**
 * SBX Updates, Primary class.
 *
 * @subpackage Classes
 * @since 1.0.0
 */
class SBX_Updates {

	/**
	 * Updater args.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	var $args = array();

	public function __construct() {

		// Populate args
		$this->populate_args();

		// Check for updates
		add_filter( 'site_transient_update_themes', array( $this, 'check_for_updates' ) );
		add_filter( 'transient_update_themes', array( $this, 'check_for_updates' ) );

		// Flush transients
		add_action( 'load-update-core.php', array( $this, 'flush_transient' ) );
		add_action( 'load-themes.php', array( $this, 'flush_transient' ) );

		// Update notifiactions
		add_action( 'admin_notices', array( $this, 'update_notification' ) );

	} /* __construct() */

	/**
	 * Populate $this->args.
	 *
	 * @since  1.0.0
	 *
	 * @return array Updater args.
	 */
	private function populate_args() {
		global $wpdb, $wp_version;

		// Establish filterable defaults
		$defaults = apply_filters( 'sbx_updater_defaults', array(
			'url'             => 'http://wpstartbox.com/updates/',
			'product_name'    => 'SBX',
			'product_slug'    => 'sbx',
			'product_version' => SBX::$version,
			'wp_version'      => $wp_version,
			'php_version'     => phpversion(),
			'mysql_version'   => $wpdb->db_version(),
			'use_betas'       => false,
		) );

		// Get args passed via add_theme_support()
		$theme_support = get_theme_support( 'sbx-updates' );
		$theme_support_args = is_array( $theme_support ) ? array_shift( $theme_support ) : array();

		// Parse theme support args against defaults
		$this->args = wp_parse_args( $theme_support_args, $defaults );

		return $this->args;

	} /* populate_args() */

	/**
	 * Check for remote updates whenever WP makes a request.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $updates Update handler object.
	 * @return object          Modified update object.
	 */
	function check_for_updates( $updates ) {
		$sbx_update = $this->remote_request();

		if ( $sbx_update ) {
			$updates->response[ $this->args['product_slug'] ] = $sbx_update;
		}

		return $updates;

	} /* check_for_updates() */

	/**
	 * Delete SBX updates transient.
	 *
	 * @since 1.0.0
	 */
	function flush_transient() {
		delete_transient( 'sbx_updates' );
	} /* flush_transient() */

	/**
	 * Make a remote request for update information.
	 *
	 * @since  1.0.0
	 *
	 * @return array|bool Update information array, or false if no update available.
	 */
	private function remote_request() {
		global $wp_version;

		// Attempt to pull update data from transient
		$sbx_updates = get_transient( 'sbx_updates' );

		// If no transient found, make a new request
		if ( 1==1 || ! $sbx_updates ) {

			// Setup remote request options
			$options = array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
					'User-Agent'   => "WordPress/$wp_version;",
					'Referer'      => home_url()
				),
				'body' => $this->args,
			);

			// Make remote request
			$response = wp_remote_post( $this->args['url'], $options );
			$response_body = wp_remote_retrieve_body( $response );

			// wp_die( '<pre>' . var_export( $response_body, 1 ) . '</pre>' );

			// Cache errors for 1hr, successful responses for 12 hours
			if ( is_wp_error( $response_body ) || 'Error' == $response_body || ! is_serialized( $response_body ) ) {
				$sbx_updates = false;
				set_transient( 'sbx_updates', array( 'new_version' => $this->args['product_version'] ), HOUR_IN_SECONDS );
			} else {
				$sbx_updates = maybe_unserialize( $response_body );
				set_transient( 'sbx_updates', $sbx_updates, 12 * HOUR_IN_SECONDS );
			}
		}

		// If we're already using the latest version, bail here
		if ( ! isset( $sbx_updates['new_version'] ) || version_compare( $this->args['product_version'], $sbx_updates['new_version'], '>=' ) ) {
			$sbx_updates = false;
		}

		return apply_filters( 'sbx_updates_remote_request', $sbx_updates, $this->args );

	} /* remote_request() */

	// Add an update alert to the dashboard when upgrade is available
	function update_notification() {

		// If user cannot install themes, bail here
		if ( ! current_user_can( 'install_themes' ) ) {
			return;
		}

		// If no updates are available, bail here
		$sbx_update = $this->remote_request();
		if ( empty( $sbx_update ) ) {
			return;
		}

		// If user has already dismissed this notice, bail here
		if ( $sbx_update['new_version'] == get_user_meta( get_current_user_id(), 'sbx_dismissed_update_notice', true ) ) {
			return;
		}

		if ( isset( $_GET['sbx-update-dismiss'] ) ) {
			update_user_meta( get_current_user_id(), 'sbx_dismissed_update_notice', $sbx_update['new_version'] );
			return;
		}

		// Setup variables
		$update_url          = esc_url( $sbx_update['url'] );
		$update_version      = esc_html( $sbx_update['new_version'] );
		$nonce_url           = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . $this->args['product_slug'], 'upgrade-theme_' . $this->args['product_slug'] );
		$changelog_link_text = sprintf( __( 'Check out what\'s new in %s', 'sbx' ), $update_version );
		$prompt_text         = __( 'Upgrading will overwrite the currently installed version of StartBox. Are you sure you want to upgrade?', 'sbx' );
		$update_text         = __( 'upgrade now', 'sbx' );

		// Generate output
		$output = '<div class="update-nag">';
		$output .= sprintf(
			__( 'An update to %1$s is available. %2$s or %3$s. %4$s', 'startbox' ),
			$this->args['product_name'],
			'<a href="' . $update_url . '?KeepThis=true&TB_iframe=true" class="thickbox thickbox-preview">' . $changelog_link_text . '</a>',
			'<a href="' . $nonce_url . '" onclick="return sb_confirm(\'' . esc_js( $prompt_text ) . '\');">' . $update_text . '</a>',
			'&nbsp;&nbsp;&nbsp;[<a href="' . add_query_arg( 'sbx-update-dismiss', true ) . '">' . __( 'Dismiss', 'sbx' ) . '</a>]'
		);
		$output .= '</div>';

		echo apply_filters( 'sbx_update_update_notification', $output, $sbx_update, $nonce_url );
	}

}
$GLOBALS['sbx']->updates = new SBX_Updates;
