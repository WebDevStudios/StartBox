# Child Themes:Adding Post Formats

## Description

Formatting content differently is now easier than ever thanks to the [Post Formats](http://codex.wordpress.org/Post_Formats) feature added with [WordPress 3.1](http://codex.wordpress.org/Version_3.1). The following documentation is taken straight from the [Post Formats Codex page](http://codex.wordpress.org/Post_Formats).

## Adding Support

	<?php add_theme_support('post-formats', $supported_formats ); ?>

### Default Usage

	<?php $supported_formats = array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ); ?>

> Note:If you don't specify an array of supported formats then there will be no formats for users to use.

The above example adds support for all of the standard post formats that WordPress supports.

## Using Post Formats

In your child theme theme, make use of [get_post_format()](http://codex.wordpress.org/Function_Reference/get_post_format) to check the format for a post, and change its presentation accordingly. Note that posts with the default format will return a value of FALSE. Or make use of the [has_post_format()](http://codex.wordpress.org/Function_Reference/has_post_format) [conditional tag](http://codex.wordpress.org/Conditional_Tags):

	if ( has_post_format( 'video' )) {
	  echo 'this is the video format';
	}

## Styling Post Formats

An alternate way to use formats is through styling rules. Themes should use the [post_class()](http://codex.wordpress.org/Template_Tags/post_class) function in the wrapper code that surrounds the post to add dynamic styling classes. Post formats will cause extra classes to be added in this manner, using the "format-foo" name.

For example, one could hide post titles from status format posts in this manner:

	.format-status .post-title {
		display:none;
	}

## External Resources

* [Post Formats is WP Codex](http://codex.wordpress.org/Post_Formats)
* [Post Types and Formats and Taxonomies, oh my!](http://ottopress.com/2010/post-types-and-formats-and-taxonomies-oh-my/) by Otto
* [On standardized Post Formats](http://andrewnacin.com/2011/01/27/on-standardized-post-formats/) by Andrew Nacin
* [Post Formats vs. Post Types](http://markjaquith.wordpress.com/2010/11/12/post-formats-vs-custom-post-types/) by Mark Jaquith
* [WordPress 3.1 Post Formats Reference](http://lisasabin-wilson.com/wordpress-3-1-post-formats-reference) by Lisa Sabin-Wilson
* [Smarter Post Formats?](http://dougal.gunters.org/blog/2010/12/10/smarter-post-formats) by Dougal Campbell 