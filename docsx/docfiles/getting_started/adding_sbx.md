/*
Title: Adding SBX
Description: Details on how to get started with SBX by adding it to your theme.
Author: Michael Beckwith
Date: 12-20-2013
Last Updated: 12-30-2013
 */

# Adding SBX To Your Theme.

## Download and Integrate Into Your Desired Theme.

> SBX is intended to be used in parent themes, and not inside a child theme.

1. Extract the sbx folder from the acquired zip file, into your root theme directory.
2. Open your theme's functions.php and add the following line, then save.

		require_once( get_template_directory() . '/sbx/sbx.php' );

3. Prepare to bring the awesome.

Next up: [Enabling SBX Features](../enable_sbx_features/) &rarr;
