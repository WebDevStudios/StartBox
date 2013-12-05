# SBX_Sidebars Class

## Description

Base class for handling StartBox Sidebar registration, output, markup, etc.

## Hooks

### Action Hooks

### no\_{$location}\_widgets

This hook executes if there is no dynamic_sidebar output to display. This hook has a variable in it, meaning it can end up being one of many values for the hook name. If the sidebar location is `primary` then the filter will be `no_primary_widgets`, or if the sidebar location is `footer_left`, then it'll be  `no_footer_left_widgets`.

### sidebar_bottom

Hook executes right at the end of the dynamic sidebar, just before the closing `</div>` tag.

### sidebar_top

Hook executes right at the start of the dynamic sidebar, just after the opening `<div>` tag.

### sidebars_after

Hook executes right after the sidebar's closing `</div>` tag.

### sidebars_before

Hook executes right before the sidebar's opening `<div>` tag.

### Filter Hooks

### sb_sidebars_after_title

Allows you to modify the text and markup applied after each widget title field when displayed on the frontend.

Default value:

**(string)** Closing heading tag

	</h1> 

Extra parameters:

**(string)** Current sidebar's unique ID
**(array)** Current sidebar's complete arguments

	$sidebar['id'], $sidebar


### sb_sidebars_after_widget

Allows you to modify the text and markup applied after each widget when displayed on the frontend.

Default value:

**(string)** Closing aside tag with html comment that has sprintf placeholders that will be replaced by WordPress core.

	</aside><!-- #%1$s --> 

Extra parameters:

**(string)** Current sidebar's unique ID
**(array)** Current sidebar's complete arguments

	$sidebar['id'], $sidebar

### sb_sidebars_before_title

Allows you to modify the text and markup applied before each widget title field when displayed on the frontend.

Default value:

**(string)** Opening heading tag with default class.

	<h1 class="widget-title">

Extra parameters:

**(string)** Current sidebar's unique ID
**(array)** Current sidebar's complete arguments

	$sidebar['id'], $sidebar

### sb_sidebars_before_widget

Allows you to modify the text and markup applied before each widget when displayed on the frontend.

Default value:

**(string)** Opening aside tag with id and class attributes that have sprintf placeholders that will be replaced by WordPress core.

	<aside id="%1$s" class="widget %2$s"> 

Extra parameters:

**(string)** Current sidebar's unique ID
**(array)** Current sidebar's complete arguments

	$sidebar['id'], $sidebar

### sb_sidebars_register_sidebar

Allows you to intercept and modify all of the array arguments used with registering a sidebar.

Default value:

**(array)**

	array(
		'id'            => esc_attr( $sidebar['id'] ),
		'name'          => esc_attr( $sidebar['name'] ),
		'description'   => esc_attr( $sidebar['description'] ),
		'before_widget' => apply_filters( 'sb_sidebars_before_widget', '<aside id="%1$s" class="widget %2$s">', $sidebar['id'], $sidebar ),
		'after_widget'  => apply_filters( 'sb_sidebars_after_widget', '</aside><!-- #%1$s -->', $sidebar['id'], $sidebar ),
		'before_title'  => apply_filters( 'sb_sidebars_before_title', '<h1 class="widget-title">', $sidebar['id'], $sidebar ),
		'after_title'   => apply_filters( 'sb_sidebars_after_title', '</h1>', $sidebar['id'], $sidebar )
	);

Extra parameters:

**(array)** Current sidebar's complete arguments

	$sidebar

### sbx_do_sidebar

Allows you to intercept and modify the default sidebar and replace with a custom one

Default value:

**(string)** default sidebar slug.

	$sidebar


## Change Log

Since: 3.0.0

## Source File

SBX_Sidebars.php is located in /sbx/classes/
