# Shortcodes

## Introduction

StartBox includes a number of useful functions in the form of shortcodes. To learn more about shortcodes and how to create and use them, read the [WordPress Codex](http://codex.wordpress.org/Shortcode_API).

## Usage

Shortcodes can be added to any post, page or widget by simply placing the shortcode as shown in the examples below. Some shortcodes also have optional parameters so you can customize them further.

You can use shortcodes directly in your template files by using the do_shortcode() function. So, if you wanted to add a twitter button to a page template, you would simply add this to the PHP file:


	<?php echo do_shortcode('[twitter]'); ?>


Note: you **must** use echo in front of the function if you want it to display.

> All of the official StartBox themes have shortcodes active by default. To include support within your own custom themes, you must include the [stylesheet].

## Demo

[Click here to see these shortcodes in action](http://demo.wpstartbox.com/shortcodes)

## Post Meta

The following shortcodes display relevant meta information about the given post. Currently none of them accept any arguments.

* [author]
	
	Displays the Page/Post author in an hCard friendly microformat. 
	
* [author_bio]

	Displays the short bio of the post's author (if they've supplied one). 

* [categories]

	Displays a comma-separated list of categories. 

* [tags]

	Displays a comma-separated list of the post's tags, prefixed by the text "Tagged: " 

* [comments]

	Displays the current comment count. 

* [date]

	Displays the page/post publication date. Format based on Settings > General. 

* [time]
  
	Displays the page/post publication time. Format based on Settings > General.

* [edit]

	Displays an Edit link (only visible to logged-in admins). 

## Buttons

Turn your plain-text links into a highly stylized CSS3 button. Specify your own colors and sizes with ease.

### Usage

	[button link="http://example.com" color="red" size="large"]Button Text[/button]

### Arguments

* link

	(string) (optional) The desired URI for this button (e.g. http://wpstartbox.com)

	* Default: #nogo 

* size

	(string) (optional) How large/small this button should be (e.g. small, normal, large, xl).

	* Default: normal 

* color

	(string) (optional) The color of this button (red, orange, yellow, green, blue, purple, dark, light, or any hexadecimal color)

	* Default: light 

* border

	(string) (optional) Custom hexadecimal color for the border (note: will default to a color that matches pre-defined colors above)

	* Default: None 

* text

	(string) (optional) Custom hexadecimal color for button text (e.g. #FFFF00)

	* Default: #FFFFFF 

* class
 
	(string) (optional) Specify a custom class name for this button

	* Default: None 

## Content Boxes

Quickly grab a reader’s attention by setting small bits of content apart in a different colored box. Wraps the content in a `<div>`.

### Usage

	[box type="info"]This is an info box.[/box]

### Arguments

* type

	(string) (optional) Give the box a specific class, predefined styles are: info, note, alert, download and dark.

	* Default: info 

## Columns

Quickly and easily break your content into manageable columns to accommodate whatever information you’re presenting. They are named logically so you can quickly guess their width and use them accordingly.

* Two Columns
	
	[one_half] 

* Three Columns
  
	[one_third], [two_thirds] 

* Four Columns
  
	[one_fourth], [two_fourths], [three_fourths] 

* Five Columns
  
	[one_fifth], [two_fifths], [three_fifths], [four_fifths] 

* Six Columns
	[one_sixth], [two_sixths], [three_sixths], [four_sixths], [five_sixths] 

> Important: These column shortcodes are designed to wrap their content, so you must close the tag at the end of each column. Note: You should add "_last" to the opening and closing tags of the last column in a row to clear extra space and correct it's margins (e.g. [one_half_last] $content [/one_half_last]).

### Usage

To put your content into two column, use the following format:

	[one_half]This content is in the left column.[/one_half] [one_half_last]This Content is in the right column.[/one_half_last]


## Social Badges

You can use **[digg]**, **[stumble]**, **[twitter]** and **[facebook]** to display social badges and buttons anywhere on your site. These are highly configurable, just like if you were creating them via their respective sites.