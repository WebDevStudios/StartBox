/*
Title: SBX Layouts
Description: Details on the available settings for SBX Layouts
Author: Michael Beckwith
Date: 01-02-2014
Last Edited: 01-02-2014
 */

# Enabling and Using SBX Layouts

SBX offers you the ability to set custom layouts for their pages. You can set a default that initially applies to all, but also allow for customization per-page.

## Adding SBX Layout support

Add the following to your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-layouts' );

With this, you will now see a metabox available for posts, pages, and custom post types. You will also see a "Layout" section appear in the Customizer settings. However, since we have not defined any layout options yet, there is nothing to set.

## Creating Some Custom Layout Options.
To do that, we need to amend our `add_theme_support()` line a bit.

	add_theme_support( 'sbx-layouts',
		array(
			'one-col' => array(
				'label' => '1 Column (no sidebars)',
				'image' => SBX::$sbx_uri . '/images/layouts/one-col.png'
			),
			'two-col-left' => array(
				'label' => '2 Columns, sidebar on left',
				'image' => SBX::$sbx_uri . '/images/layouts/two-col-left.png'
			),
		)
	);

With this, we have added a 1 column, no sidebars layout, as well as a 2 column layout, with the sidebar on the left.

The way this works is by passing in an array of layouts we want. Inside that array, we define a named array key for the layout type. These should be unique, as they are used for the radio button value. In those array keys, we add another array that holds the label to use for the option, as well as a url to display image.

The labels can be whatever you want, and you can define your own image path if you want. SBX does come with some predefined images for various layouts.

For the rest of this tutorial, we'll use the six layouts that SBX offers predefined.

	add_theme_support( 'sbx-layouts',
		array(
			'one-col' => array(
				'label' => '1 Column (no sidebars)',
				'image' => SBX::$sbx_uri . '/images/layouts/one-col.png'
			),
			'two-col-left' => array(
				'label' => '2 Columns, sidebar on left',
				'image' => SBX::$sbx_uri . '/images/layouts/two-col-left.png'
			),
			'two-col-right' => array(
				'label' => '2 Columns, sidebar on right',
				'image' => SBX::$sbx_uri . '/images/layouts/two-col-right.png'
			),
			'three-col-left' => array(
				'label' => '3 Columns, sidebar on left',
				'image' => SBX::$sbx_uri . '/images/layouts/three-col-left.png'
			),
			'three-col-right' => array(
				'label' => '3 Columns, sidebar on right',
				'image' => SBX::$sbx_uri . '/images/layouts/three-col-right.png'
			),
			'three-col-both' => array(
				'label' => '3 Columns, sidebar on each side',
				'image' => SBX::$sbx_uri . '/images/layouts/three-col-both.png'
			)
		)
	);

With these six layouts defined, our Customizer settings can now offer each for the Home Layout, Single Content Layout, and Archive Layout. These will be set as the "default" for each.

You can also override the default on a per post/page/post type basis. Whenever you're editing, you will have that metabox available that shows all six, or any custom ones you defined.

## Using Set Layout Options in the theme.

The primary way that you will use the selected layouts is through styling and class attributes. The selected layout will be added to the body_class output, and the class name will be `layout-three-col-left` for example. Others will follow a similar pattern. `layout-{named-array-key}`. The named-array-key portion will be what you set when you passed in the various layouts to the `add_theme_support()` function.

With the body class added, you will be able to use CSS to target and alter the layout dependent on the current layout.

> MICHAEL: firstly, they can lift our CSS as a starter for what they're doing
> secondly, the layout selector is really only helpful if the designer has full control over the css
> thirdly, setting the widget area to display:none; is how SB has been doing it for years, and how many people recommended doing it in the past – but, not including the content is certainly better

## Notes

The selected layout for posts are stored as post meta with the `_sbx_layout` meta key.

The selected layout for terms are stored as an option associated with the term ID.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
