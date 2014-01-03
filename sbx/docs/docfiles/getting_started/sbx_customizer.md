/*
Title: SBX Customizer
Description: Details on the available settings in SBX for the WordPress Customizer
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# Enabling and Using SBX Customizer

## Adding SBX Customizer support

WordPress comes with a utility named the "Theme Customizer" that theme developers can utilize for various settings they want in the theme. To get this started, include the following in your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-customizer' );

## Adding our filter and callback function

Next, we need to add a callback function to the [sb_customizer_settings]() hook so that it knows what to add to the output of the Customizer.

	add_filter( 'sb_customizer_settings', 'my_sbx_customizer_settings' );

Lastly, we need to create the `my_sbx_customizer_settings` so we can provide what we want to add to the customizer screen.

	function my_sbx_customizer_settings( $sections = array() ) {

	}

## Adding our custom settings.

We will be accepting one parameter with this callback function, that defaults to an empty array, if the value passed in by the filter is not already an array. We will then add our desired settings to the array and return it back to the filter. For our example, we are going to add a section that will be used for settings in the footer area.

	function my_sbx_customizer_settings( $sections = array() ) {

		//Lets create a prefix for our option keys in the database.
		$prefix = 'my_sbx_';

		$sections['footer_settings'] = array(
			'title' => 'Footer',
			'description' => 'Customize the credits area of the Footer.',
			'priority' => 400,
			'settings' => array()
		);

		//Return our array back to the filter.
		return $sections;
	}

At this point, we have a few things going on. First we created a `$prefix` variable and assigning 'my_sbx' to it. This will be used to make the options keys, in the database, unique so that we are not conflicting with other settings. Next we added a "footer_settings" index to the `$sections` array, and that index is storing an array itself. Within that stored array, is four different key/value pairs.

1. 'title' field. This will be what the Customizer setting displays when it shows the different sections.
2. 'description' field. This will display once the user has clicked into this section and provide a bit of context for what the settings are for.
3. 'priority' field. This setting dictates what order all of the Customizer settings appear in. The lower the value, the higher up it will appear.
4. 'settings' field. This will provide an array of arrays that will tell the Customizer what to render and enable the values to be saved to our option. Once saved, we can use the option to display within the theme. We will cover how to provide these settings next. Code will be reduced for clarity.


		function my_sbx_customizer_settings( $sections = array() ) {

		...

		$sections['footer_settings'] = array(

			...

			'settings' => array(
				array(
					'id' => $prefix . 'credits',
					'label' => 'Site Credits',
					'type' => 'textarea',
					'default' => '[copyright year="2013"] <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a>. Proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://wpstartbox.com">StartBox</a>.',
					'priority' => 30,
					'sanitize_callback' => 'wp_kses',
					'js_callback' => 'sbx_change_text',
					'css_selector' => '.site-info .credits',
				),
			)
		);

		...
		}

Here is where we are really getting to the heart of it. We have many things going on in this array value.

1. 'id' field. This is where we are setting the options key that we want to use for this setting. You will see our use if the $prefix variable that we set. Our example will use the option key of "my_sbx_credits".
2. 'label' field. This is some text provided to explain what the field is intended for.
3. 'type' field. This field is used to set what type of html input to use. For our example, we are going with a simple textarea. Valid choices include
	* text (default)
	* textarea
    * checkbox
    * radio (requires choices array in $args)
    * select (requires choices array in $args)
    * dropdown-pages
    * image
    * color
    * upload
4. 'default' field. Here you can provide some default values that will be used if the user does not provide any themselves. Our example is creating some default credits utilizing the [copyright shortcode](), if you have the shortcodes feature enabled, and some WordPress functions to fetch some site information.
5. 'priority' field. Much like the previous priority field for the section's settings, this priority field will dictate the order of our setting fields, disregarding the order that we provide them inside the array. The lower the value, the higher up it will appear in our Footer section.
6. 'sanitize_callback' field. We want to make sure that the values being saved are safe and "clean" before writing them to the database. With this field, we can provide a callback function to run on the value before that happens. With our example, we are using the [wp_kses](https://codex.wordpress.org/Function_Reference/wp_kses) function.
7. 'js_callback' field. This is a field that is used to specify a javascript callback function to help do some work with text value changes.
8. 'css_select' field. This is a field that is use to specify some custom classes to be attached with the output of this field.

## Examples of all available customizer types.

The following is an example Customizer section that uses all available types for the 'settings' index.

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

## Using your SBX Customizer Settings.

Once you've created your settings and saved some test values to them, you need to be able to use them in the theme display. For that we have the following function

	<?php echo sbx_get_theme_mod(); ?>

To use it, we simply need to pass in the option key we set. These will be 'id' field in our settings arrays above. For example, if we wanted to display the credit text we set in the Footer section we'd need to `echo` the following:

	<?php echo sbx_get_theme_mod( 'my_sbx_credits' ); ?>

Make sure to note that we included the prefix value we assigned earlier. Without matching the prefix, you won't be able to grab the value from the database.

Click to learn more about the [sbx_get_theme_mod() function](../../functions/sbx_get_theme_mod/.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
