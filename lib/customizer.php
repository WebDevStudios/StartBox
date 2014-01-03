<?php
/**
 * Hook into WordPress theme customizer
 * @link( /wp-admin/customize.php, link)
 */
function startbox_customizer_settings( $sections = array() ) {

	// Defines $prefix for setting IDs. Optional.
	$prefix = 'sb_';

	// Defines theme cusotmizer sections and settings
	$sections['branding_settings'] = array(
		'title'       => 'Branding',
		'description' => 'Upload a favicon, touch icon (iOS), and a tile icon (Windows 8).',
		'priority'    => 1,
		'settings'    => array(
			array(
				'id'                => $prefix . 'favicon',
				'label'             => 'Favicon (32x32 .ico)',
				'type'              => 'upload',
				'default'           => get_template_directory() . '/lib/images/favicon.ico',
				'priority'          => 10,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'touch_icon',
				'label'             => 'Touch Icon (152x152 .png)',
				'type'              => 'image',
				'default'           => get_template_directory() . '/lib/images/favicon-152x152.png',
				'priority'          => 20,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'tile_icon',
				'label'             => 'Tile Icon (144x144 .png)',
				'type'              => 'image',
				'default'           => get_template_directory() . '/lib/images/favicon-144x144.png',
				'priority'          => 30,
				'sanitize_callback' => 'esc_url',
				),
			array(
				'id'                => $prefix . 'tile_bg',
				'label'             => 'Tile Icon Background',
				'type'              => 'color',
				'default'           => '#fff',
				'priority'          => 40,
				'sanitize_callback' => 'esc_url',
				),
		)
	);
	$sections['color_settings'] = array(
		'title'       => 'Theme Colors',
		'description' => 'Customize theme colors.',
		'priority'    => 200,
		'settings'    => array(
			array(
				'id'                => $prefix . 'override_colors',
				'label'             => 'Use custom colors below',
				'type'              => 'checkbox',
				'priority'          => 10,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'primary_text_color',
				'label'             => 'Primary Text',
				'type'              => 'color',
				'default'           => '#111111',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'secondary_text_color',
				'label'             => 'Secondary Text',
				'type'              => 'color',
				'default'           => '#888888',
				'priority'          => 31,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'nav_footer_bg_color',
				'label'             => 'Nav and Footer Background',
				'type'              => 'color',
				'default'           => '#111111',
				'priority'          => 32,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'border_hover_color',
				'label'             => 'Borders, Hovers, and Highlights',
				'type'              => 'color',
				'default'           => '#999999',
				'priority'          => 33,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'url_color',
				'label'             => 'Links',
				'type'              => 'color',
				'default'           => '#777777',
				'priority'          => 34,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'content_bg_color',
				'label'             => 'Content and Sidebar Background',
				'type'              => 'color',
				'default'           => '#ffffff',
				'priority'          => 35,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'site_bg_color',
				'label'             => 'Site Background',
				'type'              => 'color',
				'default'           => '#f0f0f0',
				'priority'          => 36,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
		)
	);
	$sections['content_settings'] = array(
		'title'       => 'Post Content and Meta',
		'description' => 'Customize the post content area.',
		'priority'    => 300,
		'settings'    => array(
			array(
				'id'                => $prefix . 'post_header_meta',
				'label'             => 'Post Header Meta',
				'type'              => 'textarea',
				'default'           => 'Published by [author] on [date] at [time] - [comments]',
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.entry-header .entry-meta',
				),
			array(
				'id'                => $prefix . 'post_footer_meta',
				'label'             => 'Post Footer Meta',
				'type'              => 'textarea',
				'default'           => '[categories] [tags] [edit]',
				'priority'          => 20,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.entry-footer .entry-meta',
				),
			array(
				'id'                => $prefix . 'show_author_box',
				'label'             => 'Display Author Box',
				'type'              => 'checkbox',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
		)
	);
	$sections['footer_settings'] = array(
		'title'       => 'Footer',
		'description' => 'Customize the credits area of the Footer.',
		'priority'    => 400,
		'settings'    => array(
			array(
				'id'                => $prefix . 'rtt_link',
				'label'             => 'Return to Top Link',
				'type'              => 'checkbox',
				'priority'          => 10,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'credits',
				'label'             => 'Site Credits',
				'type'              => 'textarea',
				'default'           => '[copyright year="2013"] <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a>. Proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://wpstartbox.com">StartBox</a>.',
				'priority'          => 30,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '.site-info .credits',
				),
		)
	);
	$sections['example_settings'] = array(
		'title'       => 'Example Settings',
		'description' => 'Section description...',
		'priority'    => 999,
		'settings'    => array(
			array(
				'id'                => $prefix . 'text',
				'label'             => 'Text',
				'type'              => 'text',
				'default'           => 'Default content',
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'textarea',
				'label'             => 'Textarea',
				'type'              => 'textarea',
				'default'           => 'Some sample content...',
				'priority'          => 20,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => 'sbx_change_text',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'checkbox',
				'label'             => 'Checkbox',
				'type'              => 'checkbox',
				'priority'          => 30,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'radio_buttons',
				'label'             => 'Radio Buttons',
				'type'              => 'radio',
				'default'           => 'left',
				'choices'       => array(
					'left'      => 'Left',
					'right'     => 'Right',
					'center'    => 'Center',
					),
				'priority'          => 40,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'select_list',
				'label'             => 'Select list',
				'type'              => 'select',
				'default'           => 'two',
				'choices'     => array(
					'one'     => 'Option 1',
					'two'     => 'Option 2',
					'three'   => 'Option 3',
					),
				'priority'          => 50,
				'sanitize_callback' => 'wp_kses',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'page',
				'label'             => 'Page',
				'type'              => 'dropdown-pages',
				'priority'          => 60,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'color',
				'label'             => 'Color',
				'type'              => 'color',
				'default'           => '#f70',
				'priority'          => 70,
				'sanitize_callback' => 'esc_attr',
				'js_callback'       => 'sbx_change_text_color',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'upload',
				'label'             => 'Upload',
				'type'              => 'upload',
				'priority'          => 80,
				'sanitize_callback' => 'esc_url',
				'js_callback'       => '',
				'css_selector'      => '',
				),
			array(
				'id'                => $prefix . 'image',
				'label'             => 'Image',
				'type'              => 'image',
				'priority'          => 90,
				'sanitize_callback' => 'esc_url',
				'js_callback'       => '',
				'css_selector'      => '',
				),
		)
	);

	return $sections;
}
add_filter( 'sbx_customizer_settings', 'startbox_customizer_settings' );

/**
 * Theme color override
 */
function startbox_color_override() {

	// Check for override setting
	$override = sbx_get_theme_mod( 'sb_override_colors' );

	// If enabled, print CSS
	if ( $override ) { ?>
<style type="text/css" media="screen">
	body,
	.site-header,
	.site-inner,
	.site-footer {
		background-color: <?php echo sbx_get_theme_mod( 'sb_site_bg_color' ); ?>;
	}
	body,
	.site-title a,
	.entry-title a,
	.primary-widget-area .widget-title,
	h1, h2, h3, h4, h5, h6,
	blockquote:before,
	.site-info {
		color: <?php echo sbx_get_theme_mod( 'sb_primary_text_color' ); ?>;
	}
	a,
	.main-navigation a,
	.header_widget_area .widget_nav_menu a {
		color:  <?php echo sbx_get_theme_mod( 'sb_url_color' ); ?>;
	}
	a:hover,
	a:focus,
	a:active,
	.main-navigation a:hover,
	.header_widget_area .widget_nav_menu a:hover,
	.site-info a:hover,
	.entry-title a:hover,
	.footer-widgets a:hover,{
		color:  <?php echo sbx_get_theme_mod( 'sb_border_hover_color' ); ?>;
	}
	.main-navigation,
	.footer-widgets,
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.gform_wrapper input[type="submit"],
	.gform_wrapper .gform_footer input.button,
	.gform_wrapper .gform_footer input[type="submit"]  {
		background-color: <?php echo sbx_get_theme_mod( 'sb_nav_footer_bg_color' ); ?>;
		border-color: <?php echo sbx_get_theme_mod( 'sb_border_hover_color' ); ?>;
	}
	.entry-meta {
		color: <?php echo sbx_get_theme_mod( 'sb_secondary_text_color' ); ?>;
		border-color: <?php echo sbx_get_theme_mod( 'sb_border_hover_color' ); ?>;
	}
	.post,
	.site-inner .page,
	.primary-widget-area .widget {
		background-color: <?php echo sbx_get_theme_mod( 'sb_content_bg_color' ); ?>;
	}
</style>

<?php } }
add_action( 'wp_head', 'startbox_color_override', 999 );

/**
 * Conditionally add the author box after single posts.
 *
 * @since  3.0.0
 */
function sb_do_author_box() {

	// Output if theme setting warrants it
	if ( is_single() && sbx_get_theme_mod( 'sb_show_author_box' ) ) {
		sbx_author_box();
	}

}
add_action( 'entry_after', 'sb_do_author_box', 10 );

/**
 * Run sbx_get_theme_mod() through do_shortcode() before returning.
 *
 * @since  3.0.0
 *
 * @param  string $output Original setting output.
 * @return string         Modified setting output.
 */
function sb_theme_mod_do_shortcode( $output ) {
	return do_shortcode( $output );
}
add_filter( 'sbx_get_theme_mod', 'sb_theme_mod_do_shortcode' );
