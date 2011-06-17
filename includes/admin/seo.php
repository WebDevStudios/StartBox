<?php
class sb_seo_settings extends sb_settings {
	
	function sb_seo_settings() {
		$this->name = __('Basic SEO Settings', 'startbox');
		$this->slug = 'sb_settings_seo';
		$this->location = 'secondary';
		$this->priority = 'core';
		$this->options = array(
			'seo_intro' => array(
				'type'		=> 'intro',
				'label'		=> '',
				'desc'		=> __( 'These are only rudimentary options that will disappear as soon as you activate an SEO plugin. I highly recommend you use the <a href="http://wordpress.org/extend/plugins/wordpress-seo/" target="_blank">WordPress SEO Plugin</a> instead of these.', 'startbox')
			),
			'seo_description' => array(
				'type'		=> 'textarea',
				'label'		=> __( 'Site Description', 'startbox' ),
				'default'	=> get_bloginfo('description'),
				'desc'		=> __( 'Try to keep this brief.', 'startbox' )
			),
			'seo_keywords' => array(
				'type'		=> 'text',
				'label'		=> __( 'Site-wide Keywords', 'startbox' ),
				'size'		=> 'medium',
				'desc'		=> __( 'Comma separated (e.g. WordPress, themes, etc)', 'startbox' )
			)
		);
		parent::__construct();
	}

	function sb_seo_keywords() {
		if ( $keywords = sb_get_option( 'seo_keywords' ) ) { echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '" />' . "\n"; }
	}
	
	function sb_seo_description( $content ) {
		if ( $description = sb_get_option( 'seo_description' ) ) { echo '<meta name="description" content="' . apply_filters( 'sb_description', esc_attr( $description ) ) . '" />'; }
	}
	
	function hooks() {
		add_action( 'wp_head', array( $this, 'sb_seo_description' ));
		add_action( 'wp_head', array( $this, 'sb_seo_keywords' ));
	}
}

// Only include this metabox if no other popular SEO plugins are active
if ( !defined('WPSEO_FRONT_URL') && !class_exists('All_in_One_SEO_Pack') && !class_exists('Platinum_SEO_Pack') && !class_exists('HeadSpace_Plugin') )
	sb_register_settings('sb_seo_settings');
	
?>