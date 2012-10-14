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
	'width'				      => 960,
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

	/********************** Remove Stock Sections **********************/
	// http://wptheming.com/2012/06/add-options-to-theme-customizer-default-sections/
	$wp_customize->remove_section( 'title_tagline' );
	// $wp_customize->remove_section( 'colors' );
	// $wp_customize->remove_section( 'header_image' );
	// $wp_customize->remove_section( 'background_image' );

	/********************** Register New Controls **********************/

	class _Customize_Textarea_Control extends WP_Customize_Control {
		public $type = 'textarea';

		public function render_content() {
			?>
			<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</label>
			<?php
		}
	}


	/********************** Branding Section **********************/

	$wp_customize->add_section( 'sb_branding', array(
		'title'          => __( 'Title, Tagline & Branding', 'startbox' ),
		'priority'       => 20,
	) );

	$wp_customize->add_setting( 'blogname', array(
		'default'    => get_option( 'blogname' ),
		'type'       => 'option',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'blogname', array(
		'label'      => __( 'Site Title' ),
		'section'    => 'sb_branding',
		'priority'	=> 10
	) );

	$wp_customize->add_setting( 'blogdescription', array(
		'default'    => get_option( 'blogdescription' ),
		'type'       => 'option',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'blogdescription', array(
		'label'      => __( 'Site Tagline' ),
		'section'    => 'sb_branding',
		'priority'	=> 20
	) );

	$wp_customize->add_setting( 'startbox[tagline]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'startbox[tagline]', array(
		'label'      => __( 'Display Site Tagline in Header' ),
		'section'    => 'sb_branding',
		'type'		 => 'checkbox',
		'priority'	=> 30
	) );

	$wp_customize->add_setting( 'startbox[logo-type]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'startbox[logo-type]', array(
		'label'      => __( 'Logo Display' ),
		'section'    => 'sb_branding',
		'type'		 => 'select',
		'choices'    => array(
			'image' 	=> 'Image',
			'text'		=> 'Site Title (plain text)',
			'disabled'	=> 'Disabled'
		),
		'priority'	=> 40
	) );

	$wp_customize->add_setting( 'startbox[logo-align]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'startbox[logo-align]', array(
		'label'      => __( 'Logo Alignment' ),
		'section'    => 'sb_branding',
		'type'		 => 'select',
		'choices'    => array(
			'left' 		=> 'Left',
			'center'	=> 'Center',
			'right'		=> 'Right'
		),
		'priority'	=> 50
	) );

	$wp_customize->add_setting( 'startbox[logo-image]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'startbox[logo-image]', array(
		'label'		=> __( 'Logo Image' ),
		'section'	=> 'sb_branding',
		'settings'	=> 'startbox[logo-image]',
		'priority'	=> 60
	)));

	$wp_customize->add_setting( 'startbox[favicon]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'startbox[favicon]', array(
		'label'		=> 'Favicon',
		'section'	=> 'sb_branding',
		'settings'	=> 'startbox[favicon]',
		'priority'	=> 70
	)));

	/********************** Colors & Styles Section **********************/

	$wp_customize->add_section( 'colors', array(
		'title'          => __( 'Colors & Styles', 'startbox' ),
		'priority'       => 30,
	) );

	$wp_customize->add_setting( 'background_color', array(
		'default'        => get_theme_support( 'custom-background', 'default-color' ),
		'theme_supports' => 'custom-background',

		'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		'sanitize_js_callback' => 'maybe_hash_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
		'label'   => __( 'Page Background Color' ),
		'section' => 'colors',
		'priority' => 1
	) ) );

	$wp_customize->add_setting( 'header_bgcolor', array(
		'theme_supports' => array( 'custom-header', 'header-text' ),
		'default'        => get_theme_support( 'custom-header', 'default-text-color' ),
		'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		'sanitize_js_callback' => 'maybe_hash_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_bgcolor', array(
		'label'   => __( 'Header Background Color' ),
		'section' => 'colors',
		'priority' => 5
	) ) );

	/********************** Footer Section **********************/

	$wp_customize->add_section( 'sb_footer', array(
		'title'          => __( 'Footer', 'startbox' ),
		'priority'       => 100,
	) );

	$wp_customize->add_setting( 'startbox[enable_rtt]', array(
		'default'		=> true,
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
	) );

	$wp_customize->add_control( 'startbox[enable_rtt]', array(
		'label'			=> __( 'Enable "Return to Top" Link' ),
		'section'		=> 'sb_footer',
		'type'			=> 'checkbox',
		'priority'		=> 10
	) );

	$wp_customize->add_setting( 'startbox[enable_admin]', array(
		'default'		=> true,
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
	) );

	$wp_customize->add_control( 'startbox[enable_admin]', array(
		'label'			=> __( 'Enable Admin Links' ),
		'section'		=> 'sb_footer',
		'type'			=> 'checkbox',
		'priority'		=> 20
	) );

	$wp_customize->add_setting( 'startbox[footer_text]', array(
		'default'		=> 'Copyright &copy;[copyright year="' . date('Y') . '"] [site_link]. All Rights Reserved.
		Proudly powered by [WordPress] and [StartBox].',
		'type'			=> 'option',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( new _Customize_Textarea_Control( $wp_customize, 'startbox[footer_text]', array(
		'label' 		=> __( 'Footer Text' ),
		'section' 		=> 'sb_footer',
		'settings'		=> 'startbox[footer_text]',
		'priority'		=> 30
	) ) );

}

add_action( 'customize_register', 'sb_customizer_controls', 0 );
function sb_customizer_controls() {

}