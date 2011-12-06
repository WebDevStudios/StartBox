<?php
	class sb_header_settings extends sb_settings {
		
		function sb_header_settings() {
			$this->name = __( 'Branding', 'startbox');
			$this->slug = 'sb_header_settings';
			$this->description = __( 'Control various aspects of the header area of your site, including logo, site description, favicon and navigation. The logo options allow you to specify a text-based logo to use instead of an image.', 'startbox' );
			$this->location = 'primary';
			$this->priority = 'high';
			$this->options = array(
				'logo' => array(
						'type'		=> 'logo',
						'desc'		=> __( 'Upload an image or specify some text to use for your logo.', 'startbox' ),
					),
				'div1' => array( 'type' => 'divider' ),
				'tagline' => array(
						'type'		=> 'checkbox',
						'label'		=> __( 'Display Site Tagline', 'startbox' ),
						'desc'		=> sprintf( __( 'You can set your site Tagline in %1$sSettings > General%2$s.', 'startbox' ), '<a href="' . admin_url('options-general.php') . '">', '</a>' )
					),
				'div2' => array( 'type' => 'divider' ),
				'favicon' => array(
						'type'		=> 'upload',
						'label'		=> __( 'Favicon', 'startbox' ),
						'desc'		=> __( 'The favicon is a small logo/icon that displays alongside your URL in most browsers.', 'startbox' ),
						'default'	=> '/wp-content/themes/startbox/images/favicon.png'
					)
				);
				parent::__construct();
		}
		
		function logo() {
			$logo_container = apply_filters( 'sb_logo_container', (is_front_page()) ? 'h1' : 'h2' );
				
			if ( !sb_get_option( 'logo-disabled') ) {
				echo '<div id="logo" class="' . esc_attr( sb_get_option( 'logo-align' ) ). '">';
				if ( sb_get_option( 'logo-text') ) {
					echo '<' . $logo_container . ' id="site-title"><a href="'.get_option( 'home' ).'" title="Home" >'.esc_html(sb_get_option( 'logo-text' )).'</a></' . $logo_container . '>';
				} else {
					$logo = ( $logo = sb_get_option( 'logo-image' ) ) ? $logo : IMAGES_URL . "/logo.png";
					echo '<' . $logo_container . ' id="site-title"><a href="'.home_url().'" title="'.esc_attr(get_bloginfo('name')).'"><img src="'.esc_url($logo).'" alt="'.esc_attr(get_bloginfo('name')).'" /><span id="blog-title">'.esc_html(get_bloginfo('name')).'</span></a></' . $logo_container . '>';
				}
				echo '</div>';
			}
		}
		
		function favicon() {
			if ( sb_get_option( 'favicon' ) ) {
				echo '<link rel="icon" type="image/png" href="' . esc_url( sb_get_option( 'favicon' ) ) . '" />'."\n";
			} else {
				echo '<link rel="icon" type="image/png" href="' . IMAGES_URL . '/favicon.png" />'."\n";
			}
		}
		
		function tagline() {
			if ( sb_get_option( 'tagline' ) ) {
				$tag_container = apply_filters( 'sb_description_container', 'div');
				echo '<' . $tag_container . ' id="blog-description">' . esc_html( get_bloginfo( 'description' ) ) . '</' . $tag_container . '>';
			}
		}
		
		// Deprecated Functions
		function nav_after() { _deprecated_function( __FUNCTION__, '2.4.9', 'primary_nav' ); }
		function nav_before() { _deprecated_function( __FUNCTION__, '2.4.9', 'secondary_nav' ); }
		
		function hooks() {
			add_action( 'sb_header', array( $this, 'logo') );
			add_action( 'sb_header', array( $this, 'tagline') );
			add_action( 'wp_head', array( $this, 'favicon') );
		}

	}
	
	sb_register_settings('sb_header_settings');

?>