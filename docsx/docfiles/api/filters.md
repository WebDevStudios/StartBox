# Filters

Below is a list of all the filters along with their default values and defining location. Learn how to tap into these filters via [add_filter()](http://codex.wordpress.org/Function_Reference/add_filter).

## Theme Option Filters

* sb_option_defaults
	* Default: $defaults
	* Location: /startbox/includes/functions/admin_settings.php
* sb_child_option_defaults
	* Default: $defaults
	* Location: /startbox/includes/functions/startbox.php
* sb_theme_docs
	* Default: [http://docs.wpstartbox.com/](http://docs.wpstartbox.com/)
	* Location: /startbox/includes/functions/admin_settings.php
* sb_theme_support
	* Default: [http://wpstartbox.com/support/forum](http://wpstartbox.com/support/forum)
	* Location: /startbox/includes/functions/admin_settings.php}}

### Header Settings

* sb_logo_container
	* Default: h2
	* Location: /startbox/includes/admin/header.php
* sb_description_container
	* Default: div
	* Location: /startbox/includes/admin/header.php

### Content Settings

* sb_header_meta
	* Default: do_shortcode($content)
	* Location: /startbox/includes/admin/content.php
* sb_footer_meta
	* Default: do_shortcode($content)
	* Location: /startbox/includes/admin/content.php
* sb_read_more
	* Default: 'Read & Discuss »'
	* Location: /startbox/includes/functions/shortcodes.php
* sb_previous_post_link
	* Default: <span class="meta-nav">«</span> %title
	* Location: /startbox/includes/admin/content.php
* sb_next_post_link
	* Default: %title <span class="meta-nav">»</span>
	* Location: /startbox/includes/admin/content.php

### Footer Settings

* sb_copyright
	* Default: $sb_copyright
	* Location: /startbox/includes/admin/footer.php
* sb_design_credit
	* Default: $design_credit
	* Location: /startbox/includes/admin/footer.php}

### SEO Settings

* sb_description
	* Default: esc_attr( $description )
	* Location: /startbox/includes/admin/seo.php

## Menu Filters

* sb_{$menu_id}_menu
	* Default: $output
	* Location: /startbox/includes/functions/custom.php
* sb_{$menu_id}_positions
	* Default: array()
	* Location: /startbox/includes/functions/admin_settings.php
* sb_{$menu_id}_extras
	* Default: array()
	* Location: /startbox/includes/functions/admin_settings.php
* sb_nav_depth
	* Default: array()
	* Location: /startbox/includes/functions/admin_settings.php
* sb_nav_menu_defaults
	* Default: $defaults
	* Location: /startbox/includes/functions/custom.php
* sb_nav_social_images_size
	* Default: 24
	* Location: /startbox/includes/functions/custom.php
* sb_nav_social_images_url
	* Default: IMAGES_URL . '/social/'
	* Location: /startbox/includes/functions/custom.php
* sb_nav_social_services
	* Default: array()
	* Location: /startbox/includes/functions/admin_settings.php
* sb_nav_social_{$service}
	* Default: sprintf(__('Connect on %s','startbox'), $service)
	* Location: /startbox/includes/functions/custom.php
* sb_nav_types
	* Default: array()
	* Location: /startbox/includes/functions/admin_settings.php
* sb_footer_nav_position
	* Default: array()
	* Location: /startbox/includes/admin/navigation.php

## Post Image Filters

* sb_post_image_alt
	* Default: get_the_title()
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_class
	* Default: 'post-image'
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_crop
	* Default: topcenter
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_echo
	* Default: true
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_height
	* Default: 200
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_none
	* Default: IMAGES_URL . '/nophoto.jpg'
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_settings
	* Default: $defaults
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_use_attachments
	* Default: false
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_width
	* Default: 200
	* Location: /startbox/includes/functions/custom.php
* sb_post_image_zoom
	* Default: 1
	* Location: /startbox/includes/functions/custom.php

## Gravatar Filters

* sb_author_article_gravatar_size
	* Default: 60
	* Location: /startbox/includes/functions/shortcodes.php
* sb_author_page_gravatar_size
	* Default: 120
	* Location: /startbox/author.php
* sb_comment_gravatar_default
	* Default: get_option('avatar_default')
	* Location: /startbox/includes/functions/comment_format.php
* sb_comment_gravatar_size
	* Default: 60
	* Location: /startbox/includes/functions/comment_format.php

## Page Filters

* sb_doctitle
	* Default: $title
	* Location: /startbox/includes/functions/hooks.php
* sb_default_page_title
	* Default: $content
	* Location: /startbox/includes/functions/hooks.php
* sb_page_title_container
	* Default: h1
	* Location: /startbox/includes/functions/hooks.php
* sb_archive_meta
	* Default: category_description() (or tag_description(), for tag-based archives)
	* Location: /startbox/includes/functions/hooks.php
* sb_attachment_size
	* Default: 900
	* Location: /startbox/attachment.php
* sb_sitemap_defaults
	* Default: $defaults
	* Location: /startbox/includes/functions/custom.php

## Shortcode Filters

* sb_protected_text
	* Default: __( 'Sorry, you must be logged in to view this content', 'startbox' )
	* Location: /startbox/includes/functions/shortcodes.php
* sb_protected
	* Default: $output
	* Location: /startbox/includes/functions/shortcodes.php
* sb_rtt_text
	* Default: __( 'Return to Top', 'startbox' )
	* Location: /startbox/includes/functions/shortcodes.php

## Slideshow Filters

* sb_slideshow_interface
	* Default: array()
	* Location: /startbox/includes/plugins/slideshows.php
* sb_slideshow_result
	* Default: $result, $id, $dimensions, $control, $slides
	* Location: /startbox/includes/plugins/slideshows.php

## Sidebar Filters

* sb_home_featured_description
	* Default: __( 'This is the.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_primary_widget_area_description
	* Default: __( 'This is the primary sidebar when using two- or three-conumn layouts.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_secondary_widget_area_description
	* Default: __( 'This is the secondary sidebar for three-column layouts. It appears beneath the primary sidebar on two-column layouts.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_tertiary_widget_area_description
	* Default: __( 'This sidebar replaces both the Primary and Secondary sidebars on any pages using the Tertiary Aside layout.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_footer_widget_area_1_description
	* Default: __( 'This is the first footer column. Use this before using any other footer columns.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_footer_widget_area_2_description
	* Default: __( 'This is the second footer column. Use this before using Footer Aside Column 3.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_footer_widget_area_3_description
	* Default: __( 'This is the third footer column. Use this before using Footer Aside Column 4.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php
* sb_footer_widget_area_4_description
	* Default: __( 'This is the fourth footer column. Use this only after using Columns 1-3.', 'startbox' )
	* Location: /startbox/includes/functions/sidebars.php

### SB Search Widget Filters

* sb_search_text
	* Default: 'Type your search and press Enter.'
	* Location: /startbox/includes/functions/custom.php

### SB Social Widget Filters

* sb_social_comment_rss
	* Default: __('Subscribe to Comments RSS', 'startbox' )
	* Location: /startbox/includes/widgets/social.php
* sb_social_images_size
	* Default: 24
	* Location: /startbox/includes/widgets/social.php
* sb_social_images_url
	* Default: IMAGES_URL . '/social/'
	* Location: /startbox/includes/widgets/social.php
* sb_social_rss
	* Default: __( 'Sibscribe via RSS', 'startbox' )
	* Location: /startbox/includes/widgets/social.php
* sb_social_rss
	* Default: __('Subscribe via RSS', 'startbox')
	* Location: /startbox/includes/functions/custom.php
* sb_social_{$service}
	* Default: sprintf( __( 'Connect on %s', 'startbox' ), $service )
	* Location: /startbox/includes/widgets/social.php