<?php
/**
 * StartBox Menu Functions
 *
 * @package StartBox
 * @subpackage Functions
 */


/**
 * Helper function for building a menu based on user selection with the Options API
 *
 * @since 2.4.8
 *
 * @uses wp_page_menu()
 * @uses wp_nav_menu()
 * @uses wp_list_categories()
 *
 * @param array|string $args Optional. Override default arguments. type = type of menu, class = class of containing element, show_home will show/hide home link
 */
if ( !function_exists( 'sb_nav_menu' ) ) {
	function sb_nav_menu( $args = '' ) {
		$defaults = array(
				'type'			=> 'pages',
				'class'			=> 'nav',
				'show_home'		=> 1,
				'echo'			=> false,
				'container'		=> 'div',
				'container_id'	=> '',
				'menu_class'	=> '',
				'menu_id'		=> '',
				'before'		=> '',
				'after'			=> '',
				'link_before'	=> '',
				'link_after'	=> '',
				'depth'			=> 0,
				'fallback_cb'	=> 'sb_nav_menu_fallback',
				'extras'		=> '',
				'walker'		=> ''
			);
		$r = wp_parse_args( $args, apply_filters( "sb_nav_menu_defaults", $defaults ) );
		extract( $r, EXTR_SKIP );

		if ( $type == 'none' || $type == '' )
			return;

		$output = wp_nav_menu( array(
			'menu' 				=> $type,
			'container'			=> $container,
			'container_class'	=> $class,
			'container_id'		=> $container_id,
			'menu_class'		=> $menu_class,
			'menu_id'			=> $menu_id,
			'before'			=> $before,
			'after'				=> $after,
			'link_before'		=> $link_before,
			'link_after'		=> $link_after,
			'depth'				=> $depth,
			'show_home'			=> $show_home,
			'fallback_cb'		=> $fallback_cb,
			'extras'			=> $extras,
			'walker'			=> $walker,
			'echo'				=> false ) );

		$nav_menu = apply_filters( "sb_{$menu_id}_menu", $output );

		if ($echo)
			echo $nav_menu;
		else
			return $nav_menu;
	}
}

/**
 * Fallback function for building menus in the event no custom menus exist -- copied mostly from wp_nav_menu()
 *
 * @since 2.4.9
*/
if ( !function_exists('sb_nav_menu_fallback') ) {
	function sb_nav_menu_fallback( $args = array() ) {
		$args = apply_filters( 'wp_nav_menu_args', $args );
		$args = (object) $args;

		$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
		$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';

		$nav_menu = $items = '';
		$nav_menu .= '<'. $args->container . $id . $class . '>';
		$nav_menu .= '<ul id="' . $args->menu_id . '">';
		$nav_menu .= apply_filters( 'wp_nav_menu_items', $items, $args );
		$nav_menu .= '</ul>';
		$nav_menu .= '</' . $args->container . '>';
		$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

		if ( $args->echo )
			echo $nav_menu;
		else
			return $nav_menu;
	}
}

/**
 * Filter for replacing wp_nav_menu_items with either pages or categories
 *
 * @since 2.4.9
 *
 */
function sb_nav_menu_items($items, $args ) {
	extract( wp_parse_args( $args ) );

	// Include Link to homepage based on user selection
	$is_home = ( is_front_page() ) ? ' current-menu-item' : '' ;
	$home = ( $show_home ) ? '<li class="menu-item menu-item-home' . $is_home . '"><a href="' . home_url('/') . '">Home</a></li>' : '' ;

	// Change menu contents based on user selection
	if ( $menu == 'pages' ) {
		$exclude = (get_option('show_on_front') == 'page') ? get_option('page_on_front') : '';
		$items = $home . wp_list_pages('title_li=&exclude=' . $exclude . '&depth=' . $depth . '&echo=0');
		if( $page = strripos( $items, 'current_page_item') ) { $items = substr_replace( $items, ' current-menu-item', $page+17, 0 ); }
		if( $page_parent = strripos( $items, 'current_page_ancestor') ) { $items = substr_replace( $items, ' current-menu-ancestor', $page_parent+21, 0 ); }
	} elseif ( $menu == 'categories' ) {
		$items = $home . wp_list_categories('title_li=&depth=' . $depth . '&echo=0');
		if( $cat = strripos( $items, 'current-cat') ) { $items = substr_replace( $items, ' current-menu-item', $cat+11, 0 ); }
		if( $cat_parent = strripos( $items, 'current-cat-parent') ) { $items = substr_replace( $items, ' current-menu-ancestor', $cat_parent+18, 0 ); }
	} else {
		$items = $home . $items;
	}

	// Adds .first and .last classes to respective menu items
    if( $first = strpos( $items, 'class=' ) ) { $items = substr_replace( $items, 'first ', $first+7, 0 ); }
    if( $last = strripos( $items, 'class=') ) { $items = substr_replace( $items, 'last ', $last+7, 0 ); }

	// Add extras
	if ( $extras == 'search' ) {
		$items .= '<li class="menu-item menu-item-type-search">';
		ob_start();
		get_template_part( 'searchform', 'menu' );
		$items .= ob_get_clean();
		$items .= '</li>';
	} elseif ( $extras == 'social' ) {
		$options = get_option(THEME_OPTIONS);
		$rss = (isset($options[$menu_id . '-social-rss'])) ? $options[$menu_id . '-social-rss'] : '';
		$services = array(
			'rss'		=> $rss,
			'twitter'	=> $options[$menu_id . '-social-twitter'],
			'facebook'	=> $options[$menu_id . '-social-facebook'],
			'youtube'	=> $options[$menu_id . '-social-youtube'],
			'vimeo'		=> $options[$menu_id . '-social-vimeo'],
			'flickr'	=> $options[$menu_id . '-social-flickr'],
			'delicious'	=> $options[$menu_id . '-social-delicious'],
			'linkedin'	=> $options[$menu_id . '-social-linkedin'],
		);
		$icon_url = apply_filters( 'sb_nav_social_images_url', IMAGES_URL.'/social/' );
		$icon_size = apply_filters( 'sb_nav_social_images_size', 24 );

		foreach ($services as $service => $url) {
			$text = apply_filters( "sb_social_{$service}", sprintf( __( 'Connect on %s', 'startbox'), $service ) );

			if ( $service == 'rss' ) {
				if ( isset($url) && true == $url ) {
					$rss_text = apply_filters( 'sb_social_rss', __( 'Subscribe via RSS', 'startbox') );
					$items .= '<li class="menu-item menu-item-type-social menu-item-' . $service . '">';
					$items .= '<a href="' . get_bloginfo('rss2_url') . '" target="_blank" title="' . $rss_text . '">';
					$items .= '<img src="' . $icon_url . $service . '.png" width="' . $icon_size . 'px" height="' . $icon_size . 'px" alt="' . $rss_text . '" />';
					$items .= '<span>RSS Feed</span>';
					$items .= '</a></li>';
				}
			} elseif ( isset($url) && $url != '' ) {
				$items .= '<li class="menu-item menu-item-type-social menu-item-' . $service . '">';
				$items .= '<a href="' . $url . '" target="_blank" title="' . $text . '">';
				$items .= '<img src="' . $icon_url . $service . '.png" width="' . $icon_size . 'px" height="' . $icon_size . 'px" alt="' . $text . '" />';
				$items .= '<span>' . $text . '</span>';
				$items .= '</a></li>';
			}
		}
	}

    return $items;
}
add_filter( 'wp_nav_menu_items', 'sb_nav_menu_items', 10, 2 );