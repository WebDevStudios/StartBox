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

	/**
	 * Success/Error text strings
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	var $strings = array();

	public function __construct() {

		// Populate args
		$this->populate_args();

		// Check for updates
		add_filter( 'site_transient_update_themes', array( $this, 'check_for_updates' ) );
		add_filter( 'transient_update_themes', array( $this, 'check_for_updates' ) );

		// Flush transients
		add_action( 'load-update-core.php', array( $this, 'flush_transients' ) );
		add_action( 'load-themes.php', array( $this, 'flush_transients' ) );

		// Update notifiactions
		add_action( 'admin_notices', array( $this, 'theme_update_notification' ) );
		add_action( 'admin_notices', array( $this, 'framework_update_notification' ) );
		add_action( 'admin_init', array( $this, 'update_framework' ) );

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
		$defaults = apply_filters( 'sbx_updater_args_defaults', array(
			'url'               => 'http://wpstartbox.com/updates/',
			'product_name'      => '',
			'product_slug'      => '',
			'product_version'   => '',
			'wp_version'        => $wp_version,
			'php_version'       => phpversion(),
			'mysql_version'     => $wpdb->db_version(),
			'use_betas'         => false,
			'framework_update'  => true,
			'framework_url'     => 'http://wpstartbox.com/updates/framework.php',
			'framework_name'    => 'SBX',
			'framework_slug'    => 'sbx',
			'framework_version' => SBX::$version,
			'framework_strings' => apply_filters( 'sbx_updater_framework_strings', array(
				'upload_failed'        => __( 'SBX Update Failed. Upload resulted in the following error: %s', 'sbx' ),
				'filesystem_error'     => __( 'SBX Update Failed: filesystem is preventing downloads. (%s)', 'sbx' ),
				'empty_archive'        => __( 'SBX Update Failed: downloaded archive was empty.', 'sbx' ),
				'incompatible_archive' => __( 'SBX Update Failed: incompatible archive file.', 'sbx' ),
				'mkdir_failed'         => __( 'SBX Update Failed: unable to create empty directory.', 'sbx' ),
				'copy_failed'          => __( 'SBX Update Failed: unable to copy extracted files.', 'sbx' ),
				'update_success'       => __( 'SBX was successfully updated.', 'sbx' ),
			) )
		) );

		// Get args passed via add_theme_support()
		$theme_support = get_theme_support( 'sbx-updates' );
		$theme_support_args = is_array( $theme_support ) ? array_shift( $theme_support ) : array();

		// Parse theme support args against defaults
		$this->args = apply_filters( 'sbx_updater_args', wp_parse_args( $theme_support_args, $defaults ) );

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
	public function check_for_updates( $updates ) {

		// If a theme update exists, include it
		$theme_update = $this->remote_request_theme();
		if ( $theme_update ) {
			$updates->response[ $this->args['product_slug'] ] = $theme_update;
		}

		return $updates;

	} /* check_for_updates() */

	/**
	 * Output an update notification when updates are available.
	 *
	 * @since 1.0.0
	 */
	public function theme_update_notification() {

		// If user cannot install themes, bail here
		if ( ! current_user_can( 'install_themes' ) ) {
			return;
		}

		// If no updates are available, bail here
		$theme_update = $this->remote_request_theme();
		if ( empty( $theme_update ) ) {
			return;
		}

		// If user has dismissed this notice, bail here
		if ( isset( $_GET['sbx-update-dismiss'] ) ) {
			update_user_meta( get_current_user_id(), 'sbx_dismissed_update_notice', $theme_update['new_version'] );
			return;
		} elseif ( $theme_update['new_version'] == get_user_meta( get_current_user_id(), 'sbx_dismissed_update_notice', true ) ) {
			return;
		}

		// Setup variables
		$update_url          = esc_url( $theme_update['url'] );
		$update_version      = esc_html( $theme_update['new_version'] );
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

		echo apply_filters( 'sbx_update_update_notification', $output, $theme_update, $nonce_url );

	} /* theme_update_notification() */

	/**
	 * Output an update notification when updates are available.
	 *
	 * @since 1.0.0
	 */
	public function framework_update_notification() {

		// If user cannot install themes, bail here
		if ( ! current_user_can( 'install_themes' ) ) {
			return;
		}

		// If framework updates are disabled, bail here
		if ( true !== $this->args['framework_update'] ) {
			return;
		}

		// If the theme has an update available, bail here
		$theme_update = $this->remote_request_theme();
		if ( false !== $theme_update ) {
			return;
		}

		// If no updates are available, bail here
		$framework_update = $this->remote_request_framework();
		if ( empty( $framework_update ) ) {
			return;
		}

		// If user has dismissed this notice, bail here
		if ( isset( $_GET['sbx-update-framework-dismiss'] ) ) {
			update_user_meta( get_current_user_id(), 'sbx_dismissed_framework_update_notice', $framework_update['new_version'] );
			return;
		} elseif ( $framework_update['new_version'] == get_user_meta( get_current_user_id(), 'sbx_dismissed_framework_update_notice', true ) ) {
			return;
		}

		// Setup variables
		$update_url          = esc_url( $framework_update['url'] );
		$update_version      = esc_html( $framework_update['new_version'] );
		$nonce_url           = wp_nonce_url( add_query_arg( 'update_framework', true ), 'update_framework', '_update_framework_nonce' );
		$changelog_link_text = sprintf( __( 'Check out what\'s new in %s', 'sbx' ), $update_version );
		$prompt_text         = __( 'Upgrading will overwrite the currently installed version of SBX. Are you sure you want to upgrade?', 'sbx' );
		$update_text         = __( 'upgrade now', 'sbx' );

		// Generate output
		$output = '<div class="update-nag">';
		$output .= sprintf(
			__( 'An update to %1$s is available. %2$s or %3$s. %4$s', 'startbox' ),
			$this->args['framework_name'],
			'<a href="' . $update_url . '?KeepThis=true&TB_iframe=true" class="thickbox thickbox-preview">' . $changelog_link_text . '</a>',
			'<a href="' . $nonce_url . '" onclick="return sb_confirm(\'' . esc_js( $prompt_text ) . '\');">' . $update_text . '</a>',
			'&nbsp;&nbsp;&nbsp;[<a href="' . add_query_arg( 'sbx-update-framework-dismiss', true ) . '">' . __( 'Dismiss', 'sbx' ) . '</a>]'
		);
		$output .= '</div>';

		echo apply_filters( 'sbx_update_update_notification', $output, $framework_update, $nonce_url );

	} /* framework_update_notification() */

	/**
	 * Delete SBX updates transients.
	 *
	 * @since 1.0.0
	 */
	public function flush_transients() {
		delete_transient( 'sbx_theme_update' );
		delete_transient( 'sbx_framework_update' );
	} /* flush_transients() */

	/**
	 * Make a remote request for update information.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed Array of update data on success, otherwise WP_Error object or error string.
	 */
	private function remote_request( $url = '', $options = array() ) {
		global $wp_version;

		// If no url provided, use url from args
		if ( empty( $url ) ) {
			$url = $this->args['url'];
		}

		// Setup remote request options
		if ( empty( $options ) || ! is_array( $options ) ) {
			$options = array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
					'User-Agent'   => "WordPress/$wp_version;",
					'Referer'      => home_url()
				),
				'body' => $this->args,
			);
		}

		// Make remote request
		$response = wp_remote_post( $url, $options );
		$response_body = wp_remote_retrieve_body( $response );
		$unserialized_response = maybe_unserialize( $response_body );

		// Return filterable response
		return apply_filters( 'sbx_updates_remote_request', $unserialized_response, $this->args );

	} /* remote_request() */

	/**
	 * Check for theme updates.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed Array of update data on success, otherwise false.
	 */
	private function remote_request_theme() {

		// Attempt to pull update data from transient
		$theme_update = get_transient( 'sbx_theme_update' );

		// If no transient data exists, make a new remote request
		if ( 1==1 || ! $theme_update ) {
			$theme_update = $this->remote_request();

			// Cache errors for 1hr, successful responses for 12 hours
			if ( is_wp_error( $theme_update ) || 'Error' == $theme_update ) {
				$theme_update = false;
				set_transient( 'sbx_theme_update', array( 'new_version' => $this->args['product_version'] ), HOUR_IN_SECONDS );
			} else {
				set_transient( 'sbx_theme_update', $theme_update, 12 * HOUR_IN_SECONDS );
			}
		}

		// If we're already using the latest version, there is nothing to update
		if ( ! isset( $theme_update['new_version'] ) || version_compare( $this->args['product_version'], $theme_update['new_version'], '>=' ) ) {
			$theme_update = false;
		}

		return $theme_update;

	} /* remote_request_theme() */

	/**
	 * Check for framework updates.
	 *
	 * @since  1.0.0
	 *
	 * @return mixed Array of update data on success, otherwise false.
	 */
	private function remote_request_framework() {

		// Attempt to pull update data from transient
		$framework_update = get_transient( 'sbx_framework_update' );

		// If no transient data exists, make a new remote request
		if ( 1==1 || ! $framework_update ) {
			$framework_update = $this->remote_request( $this->args['framework_url'] );

			// Cache errors for 1hr, successful responses for 12 hours
			if ( is_wp_error( $framework_update ) || 'Error' == $framework_update ) {
				$framework_update = false;
				set_transient( 'sbx_framework_update', array( 'new_version' => $this->args['product_version'] ), HOUR_IN_SECONDS );
			} else {
				set_transient( 'sbx_framework_update', $framework_update, 12 * HOUR_IN_SECONDS );
			}
		}

		// If we're already using the latest version, there is nothing to update
		if ( ! isset( $framework_update['new_version'] ) || version_compare( $this->args['framework_version'], $framework_update['new_version'], '>=' ) ) {
			$framework_update = false;
		}

		return $framework_update;

	} /* remote_request_framework() */

	/**
	 * Run framework update routine.
	 *
	 * This function will pull back the framework update data and
	 * run the update through the WP_Filesystem API to download,
	 * unzip, and replace the contents of SBX::$sbx_dir. After
	 * completion, an admin notification will be output on success
	 * or failure.
	 *
	 * @TODO make add_filter/add_action magic work
	 *
	 * @since 1.0.0
	 */
	public function update_framework() {
		global $wp_filesystem;

		// If nonce is not set, or does not match, bail here
		if ( ! isset( $_GET['update_framework'] ) ) {
			return;
		}

		// If nonce is not set, or does not match, bail here
		if ( ! check_admin_referer( 'update_framework', '_update_framework_nonce' ) ) {
			return;
		}

		// Get framework update data
		$framework_update = $this->remote_request_framework();

		// If there is no update available, bail here
		if ( empty( $framework_update ) ) {
			return;
		}

		// Setup Filesystem
		$filesystem = WP_Filesystem();

		// If filesystem access failed, output notice
		if ( false == $filesystem ) {
			apply_filters( 'sbx_updates_framework_notification', sprintf( $this->args['framework_strings']['filesystem_error'], get_filesystem_method() ) );
			add_action( 'admin_notices', array( $this, 'framework_update_status_notification' ) );
			return;
		}

		// Download framework package
		$temp_file = download_url( $framework_update['package'] );

		// If download failed, output notice
		if ( is_wp_error( $temp_file ) ) {
			apply_filters( 'sbx_updates_framework_notification', sprintf( $this->args['framework_strings']['upload_failed'], $temp_file->get_error_code() ) );
			add_action( 'admin_notices', array( $this, 'framework_update_status_notification' ) );
			return;
		}

		// Unzip and delete temp file
		$unzipped_file = unzip_file( $temp_file, dirname( SBX::$sbx_dir ) );
		unlink( $temp_file );

		// If file resulted in error object
		if ( is_wp_error( $unzipped_file ) ) {

			// Get error data
			$error = $unzipped_file->get_error_code();
			$data  = $unzipped_file->get_error_data( $error );

			// Render an admin notice
			switch( $error ) {
				case 'incompatible_archive' :
				case 'empty_archive' :
				case 'mkdir_failed' :
				case 'copy_failed' :
					apply_filters( 'sbx_updates_framework_notification', $this->args['framework_strings'][ $error ] );
					add_action( 'admin_notices', array( $this, 'framework_update_status_notification' ) );
					break;
				default :
					apply_filters( 'sbx_updates_framework_notification', sprintf( $this->args['framework_strings']['upload_failed'], $error ) );
					add_action( 'admin_notices', array( $this, 'framework_update_status_notification' ) );
					break;
			}
			return;

		}

		apply_filters( 'sbx_updates_framework_notification', $this->args['framework_strings']['update_success'] );
		add_action( 'admin_notices', array( $this, 'framework_update_status_notification' ) );

	} /* update_framework() */

	/**
	 * Output framework update susccess/failure status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String to output in notice
	 */
	public function framework_update_status_notification( $string = '' ) {

		// If user cannot install themes, bail here
		if ( ! current_user_can( 'install_themes' ) ) {
			return;
		}

		echo '<div class="update-nag"><p>' . apply_filters( 'sbx_updates_framework_notification', $string ) . '</p></div>';

	} /* framework_update_status_notification() */

}
$GLOBALS['sbx']->updates = new SBX_Updates;
