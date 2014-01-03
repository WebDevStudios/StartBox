/*
Title: SBX Filters
Description: List of filters available in SBX
Author: Michael Beckwith
Date: 12-30-2013
Last Edited: 12-30-2013
 */

> Pending: Layouts, Options API, Sidebars.

# Filters

Below is a list of all the filters along with their default values and defining location. Learn how to tap into these filters via [add_filter()](http://codex.wordpress.org/Function_Reference/add_filter/).

## Admin

* **sb_theme_docs**
	* URL for the SBX Documentation
	* Default: [http://docs.wpstartbox.com/](http://docs.wpstartbox.com/)
	* Location: /sbx/admin/admin.php
* **sb_theme_support**
	* URL for the SBX Support forum.
	* Default: [http://wpstartbox.com/support/](http://wpstartbox.com/support/)
	* Location: /sbx/admin/admin.php

## Images

* **sbx_get_image_defaults**
	* Default arguments to be used with the sb_get_image function output.
	* Default:

			array(
				'post_id'          => $post->ID,
				'image_id'         => 0,
				'output'           => 'html',
				'size'             => 'full',
				'attr'             => '',
				'fallback'         => 'use_attachments',
				'attachment_index' => 0,
			)
	* Location: /sbx/core/images.php

* **sbx_get_image**
	* Final output of the sbx_get_image function, right before returned.
	* Default:

			$output

	* Extra parameters:

			$args,
			$id,
			$html,
			$url,
			$src

	* Location: /sbx/core/images.php

* **sbx_attachment_size**
	* Attachment size to be used with sbx_the_attached_image
	* Default:

			array( 1200, 1200 )

	* Location: /sbx/core/images.php

* **sbx_the_attached_image**
	* Final output of the sbx_the_attached_image function, right before echoed
	* Default:

			$output

	* Extra parameters:

			wp_get_attachment_image( $post->ID, $attachment_size ),
			esc_url( $next_attachment_url ),
			$post //Post object

	* Location: /sbx/core/images.php

## Utility

* **sbx_pre_get_page_title**
	* Lets you set the custom title, else one will be auto-generated for you based on the current post or page.
	* Default:

			$title //Empty string unless one is passed into sbx_get_page_title()

	* Extra parameters:

			$include_label,
			$post //Post object

	* Location: /sbx/core/utility.php

* **sbx_get_page_title**
	* Final output of the sbx_get_page_title function, right before returned.
	* Default:

			$title //Empty string unless one is passed into sbx_get_page_title()

	* Extra parameters:

			$original_title,
			$include_label,
			$post //Post object

	* Location: /sbx/core/utility.php

* **sbx_get_time_since**
	* Final output of the sbx_get_time_since function, right before returned.
	* Default:

			$output

	* Extra parameters:

			$older_date,
			$newer_date,

	* Location: /sbx/core/utility.php

* **sbx_dropdown_posts**
	* Final output of the sbx_dropdown_posts function, right before return or echo.
	* Default:

			$output

	* Extra parameters:

			$args

	* Location: /sbx/core/utility.php

* **sbx_page_menu_args**
	* Args used in the sb_nav_menu_fallback function
	* Default:

			$args

	* Location: /sbx/core/utility.php

* **sb_nav_menu_fallback**
	* The final `<li>` menu list before put in the `<ul>` and echoed or returned.
	* Default:

			$menu

	* Extra parameters;

			$args

	* Location: /sbx/core/utility.php

* **sbx_author_box_defaults**
	* Default arguments to be used with the sbx_get_author_box function output.
	* Default:

			array(
				'gravatar_size' => 96,
				'title'         => __( 'About', 'startbox' ),
				'name'          => get_the_author_meta( 'display_name' ),
				'email'         => get_the_author_meta( 'email' ),
				'description'   => get_the_author_meta( 'description' ),
				'user_id'       => get_the_author_meta( 'ID' ),
			)
	* Extra parameters:

			$args

	* Location: /sbx/core/images.php

* **sbx_author_box**
	* Final output of the sbx_author_box function, right before return
	* Default:

			$output

	* Extra parameters:

			$args

	* Location: /sbx/core/utility.php

* **sbx_rtt**
	* Final markup for the "Return to top" html
	* Default:

			sprintf(
				'<p class="rtt"><a href="#top" class="cb">%s</a></p>',
				apply_filters( 'sbx_rtt_text', __( 'Return to Top', 'startbox' ) )
			)

	* Location: /sbx/core/utility.php

* **sbx_rtt_text**
	* Text to use with the "Return to top" `<a>` link
	* Default:

			__( 'Return to Top', 'startbox' )

	* Location: /sbx/core/utility.php

* **sbx_get_term_meta_defaults**
	* Parsed array of term meta to be used in the sbx_get_term_filter function
	* Default:

			array()

	* Extra parameters:

			$term

	* Location: /sbx/core/utility.php

* **sbx_get_term_meta_{$field}**
	* Variable filter used with term meta values
	* Default:

			stripslashes( wp_kses_decode_entities( $value ) )

	* Extra parameters:

			$term

	* Location: /sbx/core/utility.php

* **sbx_get_term_meta**
	* Final output of the $term->meta before the $term object is returned.
	* Default:

			$term->meta

	* Extra parameters:

			$term

	* Location: /sbx/core/utility.php

## Customizer

* **sbx_customizer_settings**
	* Filter to add Customizer settings.
	* Default:

			array()

	* Location: /sbx/extensions/SBX_Customizer.php

* **sbx_get_theme_mod**
	* Final output of the sbx_get_theme_mod function, before return
	* Default:

			$output

	* Extra parameters:

			$setting,
			$default

	* Location: /sbx/extensions/SBX_Customizer.php

## Updater

* **sbx_updater_defaults**
	* Default arguments to be used with the SBX Updater class
	* Default:

			array(
				'url'             => 'http://wpstartbox.com/updates/',
				'product_name'    => 'SBX',
				'product_slug'    => 'sbx',
				'product_version' => SBX::$version,
				'wp_version'      => $wp_version,
				'php_version'     => phpversion(),
				'mysql_version'   => $wpdb->db_version(),
				'use_betas'       => false,
			)

	* Location: /sbx/extensions/SBX_Updater.php

* **sbx_updates_remote_request**
	* Headers and properties to be used with the remote request and the update.
	* Default:

			$sbx_updates

	* Extra parameters:

			$this->args

	* Location: /sbx/extensions/SBX_Updater.php

* **sbx_update_update_notification**
	* Final output for the update_notification method
	* Default:

			$output

	* Extra parameters:

			$sbx_update,
			$nonce_url

	* Location: /sbx/extensions/SBX_Updater.php
