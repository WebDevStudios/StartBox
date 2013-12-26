# Functions: sb_is_page_template_active

## Description

Tests if the specified page template is active on any page.

## Usage

	<?php sb_is_pagetemplate_active($pagetemplate) ?>

## Parameters

* **$pagetemplate**

	(string) (required) The page template filename.

	* Default: None

## Return Values

(boolean) Whether the page template is is use.

## Examples

### Conditionally Register a Sidebar

page_customsidebar.php is the exact filename of the page template inside our theme.

	<?php
	if (sb_is_pagetemplate_active('page_customsidebar.php')) {
		register_sidebar(array(
			'name'			=>	'Custom Sidebar',
			'id'			=>	'custom_sidebar',
			'description'	=>	'This sidebar is exclusively for use on the Custom Sidebar page template.',
			'before_widget'	=>	 '<li id="%1$s" class="widget %2$s">',
			'after_widget'	=>	'</li>',
			'before_title'	=>	'<h3 class="widget-title">',
			'after_title'	=>	'</h3>'
		));
	}
	?>

## Notes

## Change Log

Since: Unknown

## Source File

sb_is_page_template_active() is located in /startbox/includes/functions/custom.php