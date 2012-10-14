<?php
/**
 * Options for controlling the site's navigation settings
 */
class sb_navigation_settings extends sb_settings {

	function sb_navigation_settings() {
		$this->name = __( 'Navigation Settings', 'startbox');
		$this->slug = 'sb_navigation_settings';
		$this->description = __( 'These options allow you to take full control of your site\'s navigation. Select which menu to display, where to display it, how deep the drop-down menus should go, whether to include a "Home" link at the beginning, and what (if any) extras to include (e.g. social links, site-wide search).', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'high';
		$this->options = array(
			'primary_nav' => array(
					'type'		=> 'navigation',
					'label'		=> __( 'Primary Navigation', 'startbox'),
					'default'	=> 'pages',
					'align'		=> 'left',
					'home_default' => true,
					'position_default' => 'sb_after_header',
					'extras'	=> true,
					'help'		=> __( 'This is the main menu for your site.', 'startbox' )
				),
			'div' => array( 'type' => 'divider' ),
			'secondary_nav' => array(
					'type'		=> 'navigation',
					'label'		=> __( 'Secondary Navigation', 'startbox'),
					'default'	=> 'none',
					'home_default' => false,
					'position_default' => 'sb_before',
					'extras'	=> true,
					'help'		=> __( 'This menu should only be used if you want two menus at the top of your site.', 'startbox' )
				),
			'div2' => array( 'type' => 'divider' ),
			'footer_nav' => array(
					'type'		=> 'navigation',
					'label'		=> __( 'Footer Navigation', 'startbox' ),
					'default'	=> 'disabled',
					'home_default' => false,
					'position'	=> apply_filters( 'sb_footer_nav_position', array(
						'sb_between_content_and_footer'	=> __( 'Before Footer Wrap', 'startbox' ),
						'sb_before_footer'	=> __( 'Before Footer Widgets', 'startbox' ),
						'sb_footer'			=> __( 'After Footer Widgets', 'startbox' ),
						'sb_footer_left'	=> __( 'Footer Left (above copyright)', 'startbox' ),
						'sb_footer_right'	=> __( 'Footer Right (above admin links)', 'startbox' ),
						'sb_after_footer'	=> __( 'Bottom of Page', 'startbox' ),
					)),
					'depth_default' => 1,
					'extras'	=> false,
					'help'		=> __( 'Use this if you want a menu in the footer of your site.', 'startbox' )
				),
			);

			parent::__construct();
	}

	function primary_nav() { sb_nav_menu( array( 'menu_id' => 'primary_nav', 'type' => sb_get_option( 'primary_nav' ), 'show_home' => sb_get_option( 'primary_nav-enable-home' ), 'extras' => sb_get_option( 'primary_nav-extras' ), 'class' => 'nav nav-primary nav-' . sb_get_option( 'primary_nav-position' ), 'depth' => sb_get_option( 'primary_nav-depth' ), 'echo' => true ) ); }
	function secondary_nav() { sb_nav_menu( array( 'menu_id' => 'secondary_nav', 'type' => sb_get_option( 'secondary_nav' ), 'show_home' => sb_get_option( 'secondary_nav-enable-home' ), 'extras' => sb_get_option( 'secondary_nav-extras' ), 'class' => 'nav nav-secondary nav-' . sb_get_option( 'secondary_nav-position' ), 'depth' => sb_get_option( 'secondary_nav-depth' ), 'echo' => true ) ); }
	function footer_nav() { sb_nav_menu( array( 'type' => sb_get_option( 'footer_nav' ), 'class' => 'nav nav-footer nav-' . sb_get_option( 'footer_nav-position' ), 'show_home' => sb_get_option( 'footer_nav-enable-home' ), 'depth' => sb_get_option( 'footer_nav-depth' ), 'echo' => true ) ); }

	function hooks() {
		$primary_nav = sb_get_option( 'primary_nav-position' );
		$secondary_nav = sb_get_option( 'secondary_nav-position' );
		$footer_nav = sb_get_option('footer_nav-position');

		add_action( $primary_nav, array( $this, 'primary_nav') );
		add_action( $secondary_nav, array( $this, 'secondary_nav') );
		add_action( $footer_nav, array( $this, 'footer_nav' ), 11 );

	}

}

sb_register_settings('sb_navigation_settings');