<?php
/**
 * StartBox Theme Customizer Settings
 *
 * Base extensions for the theme customizer introduced in WP3.4
 *
 * @package StartBox
 * @subpackage Add-ons
 * @since 2.6
 */

// Check to see if current theme supports the customizer, skip the rest if not
if (!current_theme_supports( 'sb-theme-customizer' )) return;

$header_defaults = array(
	'default-image'          => '',
	'random-default'         => false,
	'width'                  => 960,
	'height'                 => 240,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '#111111',
	'header-text'            => true,
	'uploads'                => true,
	'wp-head-callback'       => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '',
);
add_theme_support( 'custom-header', $header_defaults );


$background_defaults = array(
	'default-color'          => '#ffffff',
	'default-image'          => '',
	'wp-head-callback'       => '_custom_background_cb',
	'admin-head-callback'    => '',
	'admin-preview-callback' => ''
);
add_theme_support( 'custom-background', $background_defaults );

add_action( 'customize_register', 'sb_customize_register' );
function sb_customize_register($wp_customize) {

	$wp_customize->add_section( 'sb_header_settings', array(
		'title'          => __( 'Header Settings', 'startbox' ),
		'priority'       => 35,
	) );
	

	// startbox['']
	// logo-image ( uploader, default: /startbox/images/logo.png )
	// logo-text ( textbox, default: sitename )
	// logo-align ( select, default: left )
	// logo-disabled ( checkbox, default: false )
	// tagline ( checkbox, default: false )
	// favicon ( uploader, default: /startbox/images/favicon.png )
	
	$wp_customize->add_setting( 'startbox[logo-image]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_setting( 'startbox[logo-text]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_setting( 'startbox[logo-align]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_setting( 'startbox[logo-disabled]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_setting( 'startbox[tagline]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_setting( 'startbox[favicon]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );
	
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
		'label'		=> 'Logo',
		'section'	=> 'sb_header_settings',
		'settings'	=> 'startbox[logo-image]'
	)));
	
	$wp_customize->add_control( 'startbox[logo-text]', array(
		'label'      => __( 'Logo Text' ),
		'section'    => 'sb_header_settings',
	) );
	
	$wp_customize->add_control( 'startbox[logo-align]', array(
		'label'      => __( 'Logo Alignment' ),
		'section'    => 'sb_header_settings',
		'type'		 => 'select',
		'choices'    => array(
			'left' 		=> 'Left',
			'center'	=> 'Center',
			'right'		=> 'Right'
		)
	) );
	
	$wp_customize->add_control( 'startbox[logo-disabled]', array(
		'label'      => __( 'Disable Logo' ),
		'section'    => 'sb_header_settings',
		'type'		 => 'checkbox'
	) );
	
	$wp_customize->add_control( 'startbox[tagline]', array(
		'label'      => __( 'Display Site Tagline' ),
		'section'    => 'sb_header_settings',
		'type'		 => 'checkbox'
	) );
	
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'favicon', array(
		'label'		=> 'Favicon',
		'section'	=> 'sb_header_settings',
		'settings'	=> 'startbox[favicon]'
	)));
	
}


?>