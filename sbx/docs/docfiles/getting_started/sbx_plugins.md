/*
Title: SBX Required/Recommended Plugins
Description: Details on how to add required/recommended plugins page to an SBX powered theme.
Author: Michael Beckwith
Date: 12-30-2013
Last Edited: 01-03-2014
 */

# Required/recommended plugins.

With SBX, you are able to create a list of specific plugins and display them to the user. These plugins can either be marked as **required** and necessary for the theme to work, or **recommended**. On this page, your users will be able to install right away. No making them have to go to the [WordPress.org Plugin Repo](http://www.wordpress.org/plugins/) and search for it.

## Adding SBX Plugin support

To get this feature going, add the following to your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-plugins' );

## Adding Required/recommended Plugins list

Next up, we need to construct the list of plugins we want to require or recommend. The first step we need to do is add an action hook to the `sbx_register_plugins` hook.

	add_action( 'sbx_register_plugins', 'my_sbx_theme_required_plugins' );

Finally, we need to create our `my_sbx_theme_required_plugins` callback function.

	function my_sbx_theme_required_plugins() {

		$plugins = array(
			// This is an example of how to include a plugin from the WordPress Plugin Repository
			array(
				'name' 		=> 'Custom Post Type UI',
				'slug' 		=> 'custom-post-type-ui',
				'required' 	=> false,
			)
		);

		sbx_register_theme_plugins( $plugins );
	}

Within this function, we simply create an array of arrays that contains three indexes, and pass that array into the `sbx_register_theme_plugins` function. That is a special function that does all the heavy lifting, and integrates with the [TGM-Plugin-Activation](https://github.com/thomasgriffin/TGM-Plugin-Activation) class by Thomas Griffin.

The three array indexes, per plugin, that you need to provide are:

* Name
* Slug
* Required

The name index is used to indicate the Plugin name, the slug index is the WordPress.org plugin url slug, and the required index is a boolean value whether to mark it as required or recommended. Once all three are provided, the plugin will be listed with the rest and users will be able to begin installing.

Make sure that the slug index is the one that matches the plugin repo. Without it, the install will not succeed, and it will display an error like

> An unexpected error occurred. Something may be wrong with WordPress.org or this server’s configuration. If you continue to have problems, please try the [support forums](http://wordpress.org/support/).

Once any of the plugins are installed and activated, they will no longer show in the list. However, once they are deactivated or uninstalled, they will re-appear.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
