# sbx_breadcrumbs()

> Draft: Will be getting Customizer setting to disable the filtering

## Description

StartBox provides a convenient function to output breadcrumb navigation. You can use it to insert a breadcrumb trail wherever you would like in your theme files. Breadcrumbs are enabled by default and sbx_breadcrumbs() is used as the last resort if you don't use BreadCrumb NavXT, Yoast Breadcrumbs/WordPress SEO Breadcrumbs, or bbPress Breadcrumbs.

## Usage

### Default:

	<?php sbx_breadcrumbs(); ?>

## Parameters

* **before_crumbs**

    **(string)** Markup and content you want before the breadcrumbs start displaying.

	* Default: `<div class="breadcrumbs">`

* **after_crumbs**

    **(string)** Markup and content you want after the breadcrumbs are done displaying.

	* Default: `</div>`

* **separator**

    **(string)** Character to use to visually separate each part of the breadcrumb.

	* Default: `' / '`
	
* **hierarchial_attachments**

    **(bool)** Whether or not to include attachments in the breadcrumb trail

	* Default: `true`

* **hierarchial_categories**

	**(bool)** Whether or not to include hierarchical categories in the breadcrumb trail
	
	* Default: `true`
	
* **labels**

	**(array)** Array of values to use withe text around the breadcrumb.
	
	* Default:
		
		`array(
			'prefix' => 'You are here: ',
			'home' => 'Home',
			'search' => 'Search for '
			'404' => 'Link Not Found'
		)`


## Examples

### Using the function directly

#### Changing label values in default arguments.

	<?php
	$args['labels'] = array(
		'prefix' => 'My new prefix: ',
		'home' => 'My home',
		'search' => 'My search 	',
		'404' => 'No link found'	
	);
	sbx_breadcrumbs( $args ); ?>
	
#### Changing the separator to use '>'

	<?php
	$args['separator'] = ' > ';
	sbx_breadcrumbs( $args ); ?>

#### Adding some extra classes to the wrapping div

	<?php
	$args['before_crumbs'] = '<div class="breadcrumbs mycustomclass">';
	sbx_breadcrumbs( $args ); ?>

### Filtering default arguments on the auto-added breadcrumb trail.

**Note** You will need to add these examples to your functions.php file to have them work properly. 

	function my_custom_breadcrumbs( $args ) {
		$args['before_crumbs'] = '<div class="breadcrumbs mycustomclass">';
		$args['separator'] = ' > ';
		
		return $args;
	}
	add_filter( 'sb_breadcrumb_args', 'my_custom_breadcrumbs' );
	


## Change Log

Since: 2.4.4

## Source File

sbx_breadcrumbs() is located in /sbx/classes/SBX_Breadcrumb.php
