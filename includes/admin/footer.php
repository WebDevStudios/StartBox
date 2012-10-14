<?php
/**
 * Settings for content to include in the site footer
 */
class sb_footer_settings extends sb_settings {

	function sb_footer_settings() {
		$this->name = __( 'Footer Settings', 'startbox' );
		$this->slug = 'sb_footer_settings';
		$this->description = __( 'Select what text (if any) you would like to appear in the footer area of your site.', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'core';
		$this->options = array(
				'enable_rtt' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Enable Return to Top link', 'startbox' ),
					'default'	=> 'true',
					'help'		=> __( 'Include a link for users to return to the top of the site (Default: true).', 'startbox' )
				),
				'enable_admin' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Enable Admin Links', 'startbox' ),
					'default'	=> 'false',
					'help'		=> __( 'Include admin links in the site footer (Default: false).', 'startbox' )
				),
				'footer_text' => array(
					'type'		=> 'textarea',
					'sanitize'	=> array( 'allowed_html' => array('a' => array('href' => array(),'title' => array(), 'target' => array()),'br' => array(),'em' => array(),'strong' => array(), 'div' => array(), 'span' => array(), 'ul' => array(), 'ol' => array(), 'li' => array() ) ),
					'label'		=> __( 'Footer Text:', 'startbox'),
					'desc'		=> __( 'Full HTML and Shortcodes are allowed.', 'startbox' ),
					'default'	=> sprintf( __( '[copyright year="%s"] [site_link].<br/>Proudly powered by [WordPress] and [StartBox].', 'startbox' ), date('Y') ),
					'help'		=> __( 'Display any custom text you would like, including full HTML if your user account permits it.', 'startbox' )
				)

			);
		parent::__construct();
	}

	// Insert Return to Top link
	function sb_rtt() {
		if (sb_get_option( 'enable_rtt')) {
			echo sb_rtt();
		}
	}

	// Add Copyright, Design Credit and add'l info to footer
	function copyright() {

		if ( $text = sb_get_option( 'footer_text' ) ) { ?>
			<div id="credits" class="fine">
				<span class="footer_text"><?php echo do_shortcode( wpautop( $text ) ); ?></span>
			</div><!-- #credits -->
		<?php }
	}

	// Add Admin links to footer
	function admin() {
		if ( sb_get_option( 'enable_admin' ) ) {

			// Grab global user data
			global $user_ID, $user_identity;

			// Our default links, can be overriden using the sb_footer_admin_links filter
			$loggedin_defaults = array(
				'Admin Dashboard'	=> admin_url(),
				'Widgets'			=> admin_url('widgets.php'),
				'Theme Options'		=> admin_url('themes.php?page=sb_admin'),
				'Logout'			=> wp_logout_url( get_permalink() )
			);
			$loggedout_defaults = array(
				'Admin Dashboard'	=> admin_url()
			);

			// Filter the links arrays so they can be overridden
			$loggedin_links = apply_filters( 'sb_footer_admin_loggedin_links', $loggedin_defaults );
			$loggedout_links = apply_filters( 'sb_footer_admin_loggedout_links', $loggedout_defaults );
			$separator = apply_filters( 'sb_footer_admin_links_separator', '<li class="meta-sep">|</li>' );
			$links = array();
			$output = '';

			// If the user is logged in, use the logged in links, else use the logged out links
			if ($user_ID) { $links_array = $loggedin_links; }
			else { $links_array = $loggedout_links; }

			// Loop through all the links and store them in an array with proper HTML
			foreach( $links_array as $title => $url ) {
				$links[] = '<li><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></li>';
			}

			// Begin output
			$output .= '<div id="admin_links" class="fine">';
			if ($user_ID) { $output .= '<span id="login_identity">Logged in as <strong>' . $user_identity . '</strong>.</span>'; }
			$output .= '<ul>';
			$output .= implode( $separator, $links );
			$output .= '</ul>';
			$output .= '</div> <!-- #admin_links -->';

			// Filter the whole thing, incase someone wants to replace it entirely
			echo apply_filters( 'sb_footer_admin', $output );

		}
	}

	function hooks() {
		add_action( 'sb_footer', array( $this, 'sb_rtt'), 5 );
		add_action( 'sb_footer_left', array( $this, 'copyright' ), 12 );
		add_action( 'sb_footer_right', array( $this, 'admin' ), 12 );
	}

}

sb_register_settings('sb_footer_settings');