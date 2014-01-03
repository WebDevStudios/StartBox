/*
Title: Enabling SBX Features
Description: Beginning information on enabling SBX features.
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# Enabling SBX Features

SBX has many many features available for you to enable for use within your SBX powered theme. These features include:

* [WordPress Customizer support](../sbx_customizer/)
* [Content-focused shortcodes](../sbx_shortcodes/)
* [Options page](../sbx_options_page/)
* [Easy widget sidebar creation](../sbx_sidebars/)
* [SBX Auto-update support](../sbx_autoupdates/)
* [SBX Plugin Browsing.](../sbx_plugins/)

Enabling any of these features is as simple as adding `add_theme_support()` function calls within your functions.php

All of our examples in the rest of this page will be run within the following action hook callback.

	function my_sbx_theme_setup() {

	}
	add_action( 'after_setup_theme', 'my_sbx_theme_setup' );

Click on any of the features above to learn more about them.
