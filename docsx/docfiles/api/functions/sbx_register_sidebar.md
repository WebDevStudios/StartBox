# Functions: sb_register_sidebar

## Description

Registers a sidebar within WordPress.

## Usage

	<?php sb_register_sidebar( $name, $id, $description, $editable ); ?>

## Arguments

* **$name**

    (string) (required) The display name for this sidebar (e.g. My Custom Sidebar).

	* Default: None 

* **$id**

    (string) (required) The unique identifier for this sidebar (e.g. my_custom_sidebar).

	* Default: None 

* **$description**

    (string) (optional) Descriptive text for this sidebar to display on the Widgets page.

	* Default: None 

* **$editable**

    (boolean) (optional) Whether or not this sidebar can be replaced via the custom Sidebars creator.

	* Default: 0 (false) 

## Examples

### Register three new sidebars for your homepage

The following example will register create three new sidebars on the Widgets page. Note that these functions are hooked into after_setup_theme.

	<?php
	function my_custom_sidebars() {
		sb_register_sidebar( 'Home Column Left', 'home_column_left', 'This is the left-most column on the homepage.' );
		sb_register_sidebar( 'Home Column Middle', 'home_column_middle', 'This is the center column on the homepage.' );
		sb_register_sidebar( 'Home Column Right', 'home_column_right', 'This is the right-most column on the homepage.' );
	}
	add_action( 'after_setup_theme', 'my_custom_sidebars' );
	?>

## Change Log

Since: 2.5.2

## Source File

sb_register_sidebar() is located in /startbox/includes/functions/sidebars.php