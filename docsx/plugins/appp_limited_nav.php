<?php

/**
 * Example hooks for a Pico plugin
 *
 * @author Gilbert Pellegrom
 * @link http://pico.dev7studios.com
 * @license http://opensource.org/licenses/MIT
 */
class Appp_Limited_Nav {
	public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page) {
		global $config;
		$pages['topnav'] = array();

		foreach ( $config['topnav'] as $top ) {
			foreach ( $pages as $page ) {
				if ( isset( $page['url'] ) && ( substr( $page['url'], -1 ) == '/' ) && strpos( $page['url'], $top ) ) {
					$pages['topnav'][] = $page;
				}
			}
		}
		foreach ( $config['topnav_exclude'] as $exc ) {
			foreach ( $pages['topnav'] as $top ) {
				if ( isset( $top['url'] ) && strpos( $top['url'], $exc ) ) {
					$key = $this->array_find( $exc, $pages['topnav'] );
					$key1 = array_search( $key, $pages['topnav'] );
					unset( $pages['topnav'][ $key1 ] );
				}
			}
		}
	}
	//Same as WP version
	public function trailingslashit( $string ) {
		return $this->untrailingslashit( $string ) . '/';
	}
	//Same as WP version
	public function untrailingslashit( $string ) {
		return rtrim( $string, '/' );
	}
	//Find which array item has a portion of the URL we're trying to exclude. return the whole array value for that one.
	public function array_find( $needle, $haystack ) {
		foreach ( $haystack as $item ) {
			if ( false !== strpos( $item['url'], $needle ) ) {
				return $item;
				break;
			}
		}
	}
}
