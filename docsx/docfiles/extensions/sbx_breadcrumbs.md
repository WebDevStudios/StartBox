/*
Title: Enabling and Using SBX Breadcrumbs
 */

# Enabling and Using SBX Breadcrumbs.

Breadcrumbs provide an easy and convenient way for users to navigate your website hierarchy, and adding support for them in your theme is just as easy. First thing we need to do is enable the feature. To do that, add the following line of code to your `my_sbx_theme_setup` function.

	add_theme_support( 'sbx-breadcrumbs' );

After the breadcrumbs are enabled, we need to display them in your theme somewhere. To do this, we have two different ways available.

## By function

The first way to display breadcrumbs is with the [sbx_breadcrumbs()](../functions/sbx_breadcrumbs/) function. Open the template file that is responsible for where you want the breadcrumbs displayed, and simply type the following to use with the default parameters.

	<?php sbx_breadcrumbs(); ?>

## By action hook

The second way is via an optional hook that you can add to your theme. Once you have added the following hook to where you want the output, the breadcrumbs will appear automatically.

	<?php do_action( 'content_top' ); ?>

A benefit of this action hook is that if you end up having other content you want to hook onto, you can add other callbacks and have it automattically displayed.

If you wish to customize the breadcrumb output for either method, see [sbx_breadcrumbs()](../../functions/sbx_breadcrumbs/) for details.

&larr; [Back to "Enable SBX Features"](../enable_sbx_features/)
