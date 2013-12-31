/*
Title: SBX Shortcodes
Description: details on available content-based shortcodes in SBX
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# Enabling and Using SBX Shortcodes.

## Adding SBX Shortcode support

SBX comes with a handful of content-focused shortcodes ready for you to use. What I mean by content-focused is that they will display dynamic text for you to use in cases where the text shouldn't be hardcoded. To get this started, include the following in your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-shortcodes' );

## Available shortcodes

The list of available shortcodes goes as follows:

* [title]
	* Renders the title of the current post.
* [author]
	* Renders an hAtom-formatted author link for the current post.
* [author_box]
	* Renders an author box for the current post.
* [categories]*
	* Renders a category list for the current post.
* [tags]*
	* Renders a tag list for the current post.
* [comments]
	* Renders a comments link for the current post.
* [date]*
	* Renders an hAtom-formatted date stamp for the current post.
* [time]
	* Renders a time stamp for the current post.
* [edit]
	* Renders an "Edit" link for the current post, if the current user has edit_posts capabilities.
* [more]*
	* Renders a "Read More" link for the current post.
* [copyright]*
	* Renders a copyright date timestamp.

A * indicates shortcodes that have optional parameters.

## Shortcode Attributes.

Many of the SBX shortcodes are going to have attributes that you can use with them to dictate what gets returned when the shortcode is parsed.

### [categories]

This shortcode has 2 different attributes that you can use to customize output.

1. 'label'. Text that appears prepended to the list of categories.
	* Default: 'Categories: '
2. 'class'. Class attribute that is used on the span tag.
	* Default: 'entry-categories'

> You can pass in multiple classes, just make sure to separate them with a space.

Example:

	[categories label="My Categories: " class="my_sbx_entry_categories"]

### [tags]

This shortcode has 2 different attributes that you can use to customize output.

1. 'label'. Text that appears prepended to the list of categories.
	* Default: 'Tags: '
2. 'class'. Class attribute that is used on the span tag.
	* Default: 'entry-tags'

> You can pass in multiple classes, just make sure to separate them with a space.

Example:

	[tags label="My Tags: " class="my_sbx_entry_tags"]

### [date]

This shortcode has 2 different attributes that you can use to customize output.

1. 'format'. The [php date format](http://www.php.net/manual/en/function.date.php) that you want to use.
	* Default: date format specified in your General Settings page.
2. 'relative'. Boolean parameter that indicates whether to show relative time, aka "time since" or "22 hours ago", or the actual date timestamp.
	* Default: false

Example:

	[date format="Y-m-d" relative="false"]

### [more]

This shortcode has 2 different attributes that you can use to customize output.

1. 'text'. The text to use when displaying the "Read more" link.
	* Default: 'Read &amp; Discuss &raquo;'
2. 'class'. Class attribute that is used on the `<a>` tag.
	* Default: 'more-link'

> You can pass in multiple classes, just make sure to separate them with a space.

Example:

	[more text="Read more of this post" class="my_sbx_more_link"]

### [copyright]

This shortcode has 1 attribute that you can use to customize output.

1. 'year'. The beginning year to use with the copyright text.
	* Default: current year.

Example:

	[copyright year="2012"]

If you want to check out the details of any shortcodes, you can open and read through the shortcode definitions in `/sbx/extensions/shortcodes.php`.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
