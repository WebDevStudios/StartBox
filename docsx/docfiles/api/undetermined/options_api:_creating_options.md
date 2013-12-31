# Options API: Creating Options

This page outlines all the different option types you can create, along with some of the parameters each commonly accepts. If you're looking to add a single option to an existing metabox, have a look at [sb_register_option()]()

## Creating Options

To add options to your settings panel, you'll need to create an array.

	$options = array(
		'option_name' => array(  // Unique name for your option
			'type'      => '',     // type of option (see list below)
			'label'     => '',     // label for the option
			'options'   => '', // array, for select and radio options only
			'default'   => '', // the default value
			'desc'      => '',     // a short description
			'size'      => '',     // for textboxes and select options only (small/medium/large)
			'align'     => ''  // specify left/right alignment (default: right)
		)
	);

## Option Types

* **intro** - creates a subheading based on the label and intro text based on description
* **divider** - inserts a horizontal divider, no other arguments necessary
* **text** - text input
* **textarea** - textarea
* **checkbox** - checkbox
* **radio** - radio, requires options argument
* **select*** - select box, requires options argument
* **enable_select*** - a select box that has an 'Enable' checkbox
* **upload** - an AJAX uploader
* **color** - a jQuery color selector
* **background** - full set of options for setting element backgrounds
* **font** - full set of options for controlling fonts
* **border** - full set of options for controlling borders
* **navigation** - select box containing navigational items
* **wysiwyg** - a TinyMCE style text area 

> A Note About Select Options: Instead of defining the select-type options as an array, you can specify either 'categories' or 'pages' to automatically produce a comprehensive list of either item. (e.g. 'options' => 'categories')