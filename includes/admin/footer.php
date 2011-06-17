<?php
	class sb_footer_settings extends sb_settings {
		
		function sb_footer_settings() {
			$this->name = __( 'Footer Settings', 'startbox' );
			$this->slug = 'sb_footer_settings';
			$this->description = __( 'Select what text (if any) you would like to appear in the footer area of your site. The textbox allows use of full HTML and Shortcodes.', 'startbox' );
			$this->location = 'primary';
			$this->priority = 'core';
			$this->options = array(
					'site_name' => array(
						'type'		=> 'text',
						'label'		=> __( 'Name to use for Copyright', 'startbox' ),
						'default'	=> get_bloginfo('name'),
						'size'		=> 'medium',
						'align'		=> 'right'
					),
					'site_url' => array(
						'type'		=> 'text',
						'label'		=> __( 'Web Address to use for Copyright', 'startbox' ),
						'desc'		=> __( 'Include http://. Leave blank for no link.', 'startbox' ),
						'default'	=> home_url(),
						'size'		=> 'medium',
						'align'		=> 'right'
					),
					'copyright_year' => array(
						'type'		=> 'text',
						'label'		=> __( 'Original Copyright Year', 'startbox' ),
						'default'	=> '2010',
						'desc'		=> __( 'If prior to this year, theme will automatically display [original year] - [current year].', 'startbox' ),
						'size'		=> 'small',
						'align'		=> 'right'
					),
					'enable_copyright' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Copyright', 'startbox' ),
							'default'	=> 'true'
						),
					'enable_designer_credit' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Designer Credit', 'startbox' ),
							'default'	=> 'true'
						),
					'enable_wp_credit' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable WordPress Credit', 'startbox' ),
							'default'	=> 'true'
						),
					'enable_sb_credit' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable StartBox Credit', 'startbox' ),
							'default'	=> 'true'
						),
					'enable_admin' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Admin Links', 'startbox' ),
							'default'	=> 'true'
						),
					'footer_text' => array(
							'type'		=> 'textarea',
							'sanitize'	=> array( 'allowed_html' => array('a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(), 'div' => array(), 'span' => array(), 'ul' => array(), 'ol' => array(), 'li' => array() ) ),
							'label'		=> __( 'Enter any additional footer text below:', 'startbox'),
							'desc'		=> __( 'Full HTML and Shortcodes are allowed.', 'startbox' )
						),
					'enable_rtt' => array(
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable Return to Top link', 'startbox' ),
							'default'	=> 'true'
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
			$site_name = sb_get_option( 'site_name' );
			$site_url = sb_get_option( 'site_url');
			$sb_copyright = ($site_url) ? '<a href="' . $site_url . '">' . $site_name . '</a>' : $site_name;
			$design_credit = THEME_NAME . ' for <a href="http://wpstartbox.com" title="StartBox Theme Framework for WordPress" target="_blank" class="link-designer">StartBox</a>.';
			$copyright = sb_get_option( 'enable_copyright' );
			$copyright_year = ( sb_get_option( 'copyright_year' ) ) ? sb_get_option( 'copyright_year' ) : date(Y);
			$current_year = date('Y');
			$designer = sb_get_option( 'enable_designer_credit' );
			$wordpress = sb_get_option( 'enable_wp_credit' );
			$startbox = sb_get_option( 'enable_sb_credit' );
			$text = sb_get_option( 'footer_text' );

			if ($copyright || $designer || $text) { ?>
				<div id="credits" class="fine">
					<?php if ($copyright) { ?><span class="copyright">&copy;<?php if ($copyright_year == $current_year) { echo $current_year; } else { echo $copyright_year.'-'.$current_year;} ?> <?php echo apply_filters( 'sb_copyright', $sb_copyright ); ?>. <span class="rights">All Rights Reserved.</span></span><?php } ?>
					<?php if ($designer) { ?><span class="design_credit"><?php echo apply_filters( 'sb_design_credit', $design_credit ); ?></span><?php } ?>
					<?php if ($wordpress || $startbox) { ?><span class="wp_credit">Powered by <?php if ($wordpress) { ?><a href="http://www.wordpress.org/" title="WordPress" target="_blank" class="link-wordpress">WordPress</a><?php } if ($wordpress && $startbox) { ?> and <?php } if ($startbox) { ?><a href="http://wpstartbox.com" class="link-startbox">StartBox</a><?php } ?>.</span><?php } ?>
					<?php if ($text) { ?><span class="footer_text"><?php echo do_shortcode( $text ); ?></span><?php } ?>
				</div><!-- #credits -->
			<?php }
		}

		// Add Admin links to footer
		function admin() {
			$home = home_url();
			if ( sb_get_option( 'enable_admin' ) ) { ?>
				<div id="admin_links" class="fine">
					<?php global $user_ID, $user_identity, $user_level ?>
					<?php if ( $user_ID ) { ?>
						<span id="login_identity">Logged in as <strong><?php echo $user_identity ?></strong>.</span>
						<ul>
							<li><a href="<?php echo $home; ?>/wp-admin/">Admin Dashboard</a></li>
							<li class="meta-sep">|</li>
							<li><a href="<?php echo $home; ?>/wp-admin/widgets.php">Widgets</a></li>
							<li class="meta-sep">|</li>
							<li><a href="<?php echo $home; ?>/wp-admin/themes.php?page=sb_admin">Theme Options</a></li>
							<li class="meta-sep">|</li>
							<li><a href="<?php echo $home; ?>/wp-login.php?action=logout&amp;redirect_to=<?php echo $home; ?>">Logout</a></li>
						</ul>
					<?php } else { ?>
						<a href="<?php echo $home; ?>/wp-admin/">Admin Login</a>
					<?php } ?>
				</div> <!-- #admin_links -->
			<?php }
		}
		
		function hooks() {
			add_action( 'sb_footer', array( $this, 'sb_rtt'), 5 );
			add_action( 'sb_footer_left', array( $this, 'copyright' ), 12 );
			add_action( 'sb_footer_right', array( $this, 'admin' ), 12 );
		}

	}
	
	sb_register_settings('sb_footer_settings');
?>