# Functions: sb_register_settings

## Description

Register a new metabox to the Theme Options page. This function also requires that a new class be created to contain all of the settings for the options, callbacks and defaults.

## Usage

	<?php sb_register_settings( $class_name ); ?>

## Parameters

* **$class_name**

	(string) (required) The name of the new metabox to register.

	* Default: None

## Examples

### Adding a Settings Panel

Creating your own options panel is very simple. All you need to do is extend the standard setting class and some of its functions. The base class takes care of all the heavy lifting, all you need to do is supply the information. Have a look at Creating Options to better understand what you can pass through the $options variable.

	<?php
	class my_new_settings extends sb_settings {

		public $name = 'My New Settings'; // Name your options panel
		public $slug = 'new_settings'; // Give it a nice-name
		public $location = 'primary'; // Place it in a column
		public $priority = 'default'; // Set the priority (high/low/default)
		public $options = ''; // Multi-dimensional array() of options

		function output() {
			// Whatever you'd like to output based on your settings
		}

		function hooks() {
			// Hooking in your options to the front-end
			add_action('hook_name', array( $this, 'output' ) );
		}
	}
	sb_register_settings('my_new_settings');
	?>

> Note: Within the class you can define as many of your own functions and hook them as many places as you like. And, because the functions are wrapped inside the class, you don't need to worry about naming them uniquely!

### A Sample Options Panel

Below is the code to create simple option panel that contains a text input, a checkbox, a select box and a text area.

	<?php class my_new_settings extends sb_settings {
		public $name = 'New Settings';
		public $slug = 'new_settings';
		public $location = 'primary';
		public $priority = 'low';
		public $options = array(
			'sometext' => array(
				'type'		=> 'text',
				'label'		=> 'Some Text',
				'default'	=> 'The Default',
				'size'		=> 'medium'
			),
			'enable_link' => array(
				'type'		=> 'checkbox',
				'label'		=> 'Enable Link',
				'default'	=> 'true'
			),
			'my_name' => array(
				'type'		=> 'select',
				'label'		=> 'My Name',
				'options'	=> array(
					'john1' => 'John Doe',
					'john2' => 'Johnny Appleseed',
					'john3' => 'Johnny Depp'
					),
				'default'	=> 'john2'
			),
			'moretext' => array(
				'type'		=> 'textarea',
				'label'		=> 'Long-winded text:',
				'desc'		=> 'Full HTML is allowed.'
			)
		);

		// Insert a link
		function link() {
			if ( sb_get_option(	'enable_link' ) ) {
				echo '<a href="#nogo">Link Text</a>'."\n";
			}
		}

		// Generate some output
		function output() {
			echo '<div id="my_settings_text">';
			if ( sb_get_option( 'my_name' ) == 'john1' ) { echo 'John Doe'; }
			elseif ( sb_get_option( 'my_name' ) == 'john2' ) { echo 'Johnny Appleseed'; }
			else { echo 'Johnny Depp'; }
			echo '<br />';
			echo sb_get_option( 'sometext' ) . '<br />';
			echo sb_get_option( 'moretext' );
			echo '</div>';
		}

		function hooks() {
			// Let's hook these into the footer
			add_action( 'sb_footer', array( $this, 'link'), 5 );
			add_action( 'sb_footer', array( $this, 'output' ), 11 );
		}
	}
	sb_register_settings('my_new_settings');
	?>

## Notes

For more examples, have a look at any of the settings panels inside

	/startbox/includes/admin/

## Change Log

Since: 2.4.2

##Source File

sb_register_settings() is located in /startbox/includes/functions/admin_settings.php
