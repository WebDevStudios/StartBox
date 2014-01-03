/*
Title: sbx_page_title
Description: Parameters and examples of the sbx_page_title function
Author: Michael Beckwith
Date: 12-31-2013
Last Edited: 12-31-2013
 */

# sbx_page_title

## Description

Echo the content returned by [sbx_get_page_title()](../sbx_get_page_title)

## Usage

	<?php sbx_page_title( $title, $include_label ); ?>

## Parameters

* **title**

	(string) (optional) The default title

	* Default: None

* **include_label**

	(bool) (optional) Whether or not to prepend the title with a fitting label. E.g. "Category Archives" on categories.

	* Default: true

## Examples

	sbx_page_title( 'My Page', false );

## Note

This is considered a "pluggable" function, meaning you can declare your own `sbx_page_title` function and the SBX default version will not be created.
