# Functions: sb_do_sidebar

## Description

Output the markup for a sidebar, complete with relevant hooks and widgets.

## Usage

	<?php sb_do_sidebar( $location, $sidebar, $classes ); ?>

## Arguments

* **$location**

	(string) (required) The unique id for this location (e.g. 'featured_aside'). This makes it possible for other (custom) sidebars to be used in this location.

	* Default: None

* **$sidebar**

	(string) (required) The default sidebar ID (e.g. my_custom_sidebar).

	* Default: None

* **$classes**

	(string) (optional) Additional classes to apply to the sidebar container.

	* Default: None

## Examples

### Output A New Sidebar

To display a sidebar anywhere, you can just drop this function wherever you want it to appear:

	<?php sb_do_sidebar( "location_name", "sidebar_id", "custom_class1 custom_class2" ); ?>

This would output the following:

	<?php do_action( "sb_before_location_name" ); ?>
		<div id="location_name" class="aside location_name-aside custom_class1 custom_class2">
			<?php do_action( "sb_before_location_name_widgets" ); ?>
			<ul class="xoxo">
				<?php if ( !dynamic_sidebar(sidebar_id) ) { do_action( "sb_no_sidebar_id_widgets" ); }?>
			</ul>
			<?php do_action( "sb_after_location_name_widgets" ); ?>
		</div><!-- #location_name .aside-location_name .aside -->
	<?php do_action( "sb_after_location_name" ); ?>

### Output three new sidebars on your homepage

The following example will generate the output for three sidebars on the homepage Note that these functions are hooked into sb_home.

	<?php
	function my_custom_sidebars_output() {
			sb_do_sidebar( 'home_column_left', 'home_column_left' );
			sb_do_sidebar( 'home_column_middle', 'home_column_middle' );
			sb_do_sidebar( 'home_column_right', 'home_column_right' );
	}
	add_action( 'sb_home', 'my_custom_sidebars_output' );
	?>

## Change Log

Since: 2.5

## Source File

sb_register_sidebar() is located in /startbox/includes/functions/sidebars.php