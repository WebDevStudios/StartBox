/*
Title: sbx_dropdown_posts
Description: Parameters and examples of the sbx_dropdown_posts function
Author: Michael Beckwith
Date: 12-20-13
Last Edited: 12-31-13
 */

# sbx_dropdown_posts

## Description

Display or retrieve the HTML dropdown list of posts.

## Usage

	<?php sb_dropdown_posts( $args ); ?>

### Default Usage

	<?php $args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'order_by' => 'post_date',
		'order' => 'DESC',
		'limit' => 30,
		'selected' => 0,
		'echo' => 1,
		'name' => '',
		'id' => '',
		'class' => 'postlist',
		'option_none_value' => __( 'Select a Post', 'sbx' )
	); ?>

By default, this usage shows:

* The "posts" post type
* Published posts
* Ordered by published date
* Ordered in descending order
* Limited to 30 posts
* No item selected by default
* Echoing the full HTML output
* No name attribute in HTML
* No id attribute in HTML
* Class name of "postlist"
* Set the "none" options display text to "Select a Post"

## Parameters

* **args**

	(string|array) (optional) Override default arguments. See Notes.

	* Default:

## Return

(string) HTML content only if 'echo' argument is 0. Otherwise, this function echos the HTML and returns nothing.

## Arguments

* **post_type**

	(string) (optional) The type of post to display.

	* Default: post

* **post_status**

	(string) (optional) The status type of posts to dispalay. Valid values are 'publish', 'draft', 'private'.

	* Default: 'publish'

* **order_by**

	(string) (optional) Column to sort the posts by

	* Default: "post_date"

* **order**

	(string) (optional) Direction to sort posts. Valid values are 'ASC' and 'DESC'.

	* Default: "DESC"

* **limit**

	(integer) (optional) Number of posts to include

	* Default: 30

* **selected**

	(integer) (optional) Which Post ID is selected by default

	* Default: 0

* **echo**

	(boolean) (optional) Send output to browser (1/True) or return output to PHP (0/False)

	* Default: true

* **name**

	(string) (optional) The name attribute for the HTML `<select>` element.

	* Default: None

* **id**

	(string) (optional) The CSS id for the `<select>` list.

	* Default: None

* **class**

	(string) (optional) The CSS class name for the `<select>` element.

	* Default: postlist

* **option_none_value**

	(integer) (optional) The label for the "none" select option.

	* Default: "Select a Post"
