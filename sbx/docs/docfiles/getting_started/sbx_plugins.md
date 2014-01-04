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

## Changing the config settings for TGM Plugin Activation.

There is also chance that you will want to edit the configuration settings for the TGM Plugin Activation class, in case you need to do something custom with it. For that, we also offer a filter that you can use. It will pass in the array of settings and strings that you can alter and return.

### Default configuration

	$config = array(
		'domain'       		=> 'sbx',         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'sbx' ),
			'menu_title'                       			=> __( 'Install Plugins', 'sbx' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'sbx' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'sbx' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'sbx' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'sbx' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'sbx' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

To alter the configuration, just use the following. For our example, we will say we want automatic activation of plugins after installation. Just put this filter callback into your functions.php file.

	function my_sbx_theme_change_tgm_config( $config ) {
		$config['is_automatic'] = true;

		return $config;
	}
	add_filter( 'sbx_plugins_config', 'my_sbx_theme_change_tgm_config' );

### Translating the config strings

The verbage used in SBX and its configuration with the TGM Plugin Activation Class are translation ready. Out of the box, it will be translated by SBX translation files. However, if you want to provide your own custom translation files as well as completely different wording, you will have to filter through the array indexes in the $config variable. Add the following to your theme's functions.php file.

	function my_sbx_theme_translate_strings( $config ) {
		$config['strings'] = array(
			'page_title'                       			=> __( 'Install Required Plugins', 'my-custom-text-domain' ),
			'menu_title'                       			=> __( 'Install Plugins', 'my-custom-text-domain' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'my-custom-text-domain' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'my-custom-text-domain' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'my-custom-text-domain' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'my-custom-text-domain' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'my-custom-text-domain' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)

		return $config;
	}
	add_filter( 'sbx_plugins_config', 'my_sbx_theme_translate_strings' );

With this, you can edit the strings that will be translated, as well as edit the textdomain used. Due to the nature of php, we need to re-apply the gettext functions for this to be properly translateable.

[More information on Internationalization](http://codex.wordpress.org/I18n_for_WordPress_Developers)

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)

