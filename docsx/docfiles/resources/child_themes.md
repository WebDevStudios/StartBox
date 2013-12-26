# Child Themes

## Introduction

Creating a Child Theme is essentially the same as creating a regular theme for WordPress, except you only really need style.css and functions.php and are relying on a Parent Theme (the theme template) to provide all the essential core files. The benefit here is that you are keeping all modifications separate from the original parent theme, allowing you the freedom to upgrade without hesitation and without losing all of your customizations.

## Defining a Template

The only change you need to make to your stylesheet to make it into a child theme is declare a TEMPLATE in the opening comments, like so:

	/*
	THEME NAME: My New Theme
	THEME URI: http://www.example.com/
	DESCRIPTION: A new look for StartBox
	VERSION: 0.1
	AUTHOR: John Doe
	AUTHOR Uri: http://www.example.com
	TAGS: one column, two columns, three columns, awesome
	TEMPLATE: startbox
	*/

The line TEMPLATE: startbox tells WordPress to use the theme files installed in the /startbox/ folder with the "My New Theme" style.css style sheet.

A simplified way to do this is to duplicate the "startbox-child" folder that comes with StartBox and rename the folder to whatever you'd like. Then change the THEME NAME section in the freshly duplicated style.css file.

You can read more detailed information about developing [WordPress Theme Style Sheets](http://codex.wordpress.org/Theme_Development#Theme_Style_Sheet) and [Child Themes](http://codex.wordpress.org/Child_Themes) on the WordPress Codex.

## Working with Child Themes

* [Hooks]()
* [Filters]()
* [Included Scripts and Styles]()
* [Custom Functions]()
* [Options API]()
* [Annotated Markup]()

## Child Themes for Public Release

If you are interested in creating a design for public release, consider creating a theme template based on StartBox. First read [Designing Themes for Public Release](http://codex.wordpress.org/Designing_Themes_for_Public_Release) on the WordPress Codex and seek support on the [WordPress.org Forums](http://wordpress.org/support/).
External Resources

* [Child Themes](http://codex.wordpress.org/Child_Themes) (WordPress Codex)
* [Theme Development](http://codex.wordpress.org/Theme_Development) (WordPress Codex)
* [How to Modify WordPress Themes the Smart Way](http://themeshaper.com/modify-wordpress-themes/) - Four-part series on child themes
* [How to make a child theme for WordPress: A pictorial introduction for beginners](http://op111.net/53) - Illustrated introduction to child themes
* [Introducing Thirty Ten](http://aaron.jorb.in/blog/2010/04/05/introducing-thirty-ten/) - A guide to creating a TwentyTen-based Child Theme 