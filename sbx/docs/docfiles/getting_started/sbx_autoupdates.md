/*
Title: SBX Updates
Description: Details on enabling auto-updates for the SBX framework.
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 12-31-2013
 */

# Enabling SBX Updates.

Built in to SBX, is the ability for it to check for updates to itself. This is a transient powered feature that uses the built in notification system to let you know when there is a new version available, and help you download the updated files. To enable updates, add the following line of code to your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-updates' );

With this, your WordPress install will occasionally make a POST request to [WPStartBox.com](http://wpstartbox.com) to check if there is a new version of SBX available. If there is, an update notification alert will be displayed to your administrators.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
