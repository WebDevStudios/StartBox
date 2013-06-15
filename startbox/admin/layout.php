<?php
/**
 * Options for controling the site layout for various content types
 */
class sb_layout_settings extends sb_settings {

	function sb_layout_settings() {
		$this->name = __( 'Layout Settings', 'startbox' );
		$this->slug = 'sb_layout_settings';
		$this->description = __( 'Take full control over the layout throughout your site.', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'high';
		$this->options = array(
			'home_layout' => array(
					'type'		=> 'layout',
					'label'		=> __( 'Homepage Layout:', 'startbox' ),
					'desc'		=> __( 'Select content and sidebar alignment. Choose from any of the available layouts.', 'startbox' ),
					'options'	=>  sb_supported_layouts('sb-layouts-home'),
					'default'	=> 'two-col-right',
					'help'		=> __( 'Select which page layout you would like to use for the homepage.', 'startbox' )
				),
			'layout' => array(
					'type'		=> 'layout',
					'label'		=> __( 'Interior Page Layout:', 'startbox' ),
					'desc'		=> __( 'Select content and sidebar alignment. This can be changed on each page individually.', 'startbox' ),
					'options'	=> sb_supported_layouts('sb-layouts'),
					'default'	=> 'two-col-right',
					'help'		=> __( 'Select the default layout for your interior pages.', 'startbox' )
				),
			'post_layout' => array(
					'type'		=> 'layout',
					'label'		=> __( 'Single Post Layout:', 'startbox' ),
					'desc'		=> __( 'Select content and sidebar alignment. This can be changed on each post individually.', 'startbox' ),
					'options'	=> sb_supported_layouts('sb-layouts'),
					'default'	=> 'two-col-right',
					'help'		=> __( 'Select the default layout for your single post views.', 'startbox' )
				)
		);
		parent::__construct();
	}

	// Apply selected layout stylesheet
	function layout() {
		$options = get_option( THEME_OPTIONS );
		if ( is_front_page() )
			return $options['home_layout'];
		elseif ( is_single() )
			return $options['post_layout'];
		else
			return $options['layout'];
	}

	function hooks() {
		add_filter( 'sb_get_post_layout_default', array( $this, 'layout' ) );

	}

}

sb_register_settings('sb_layout_settings');