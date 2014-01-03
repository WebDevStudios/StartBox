/*
Title: SBX Actions
Description: List of actions available in SBX
Author: Michael Beckwith
Date: 12-30-2013
Last Edited: 12-30-2013
 */

>Layouts, Options API, Sidebars
>entry_before vs before_post
>
>entry_after vs after_post
>
>Do we want to document TGM/CMB hooks? Reference their own documentation?

# Actions

## Suggested action hooks for your theme.

Below is a list of some action hooks that are used in the StartBox theme, that you can use for your own theme as well. Some action hooks later in the page rely on these hooks. All locations are not required, just suggestions :)

* **html_before**
	* Put before the doctype

* **head_top**
	* Put at the top of the `<head>` tag.

* **head_bottom**
	* Put at the bottom of the `<head>` tag.

* **body_top**
	* Put at the top of the `<body>` tag.

* **header_before**
	* Put before the output of the header

* **header_top**
	* Put at the top of the output of the header.

* **header_bottom**
	* Put at the bottom of the output of the header.

* **header_after**
	* Put after the output of the header

* **content_top**
	* Put before The Loop and inside the container for all of the posts.

* **entry_before**
	* Put before the main content container in the loop

* **entry_top**
	* Put at the top, inside of the main content container in the loop.

* **entry_bottom**
	* Put at the bottom, inside of the main content container in the loop.

* **entry_after**
	* Put after the main content container in the loop.

* **comments_before**
	* Put before the start of the loop for current comments

* **comments_after**
	* Put after the comment_form() call.

* **content_bottom**
	* Put after The Loop and inside the container for all of the posts.

* **content_after**
	* Put after the container for all of the posts.

* **footer_before**
	* Put before the content output in the footer

* **footer_top**
	* Put at the top of the content output in the footer

* **footer_bottom**
	* Put at the bottom of the content output in the footer

* **footer_after**
	* Put after the content output in the footer

* **body_bottom**
	* Put after all of the content for the page, before wp_footer();

Below is a list of all the actions along with their default values and defining location. Learn how to tap into these actions via [add_action()](http://codex.wordpress.org/Function_Reference/add_action/).

## SBX-specific

* **sb_init**
	* Runs in the __construct SBX function, allows you to add your own hooks on SBX init
	* Location: /sbx/sbx.php

* **before_first_post**
	* Runs right before the first post in [The Loop](http://codex.wordpress.org/The_Loop)
	* Location: /sbx/core/hooks.php

* **after_first_post**
	* Runs right after the first post in [The Loop](http://codex.wordpress.org/The_Loop)
	* Location: /sbx/core/hooks.php
