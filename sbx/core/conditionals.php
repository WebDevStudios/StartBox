<?php
/**
 * SBX Conditional Functions
 *
 * @package SBX
 * @subpackage Core
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Check if a post is in a category or any of its a descendents.
 *
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 *
 * @since  1.0.0
 *
 * @param  int|array  $cats  Target category (ID), or categories (array of IDs).
 * @param  int|object $_post Post object or ID.
 * @return bool              True if at least 1 of the post's categories is a descendant of any of the target categories.
 */
function sbx_in_descendant_category( $cats, $_post = null ) {
	foreach ( (array) $cats as $cat ) {
		// If post is in the given category, stop here
		if ( in_category( $cat, $_post ) ) {
			return true;
		// Otherwise, look through this category's decendants
		} else {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $cat, 'category');
			if ( $descendants && in_category( $descendants, $_post ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Check if a given page template file is in use anywhere.
 *
 * @since  1.0.0
 *
 * @param  string $filename Template filename.
 * @return bool             True if active, otherwise false.
*/
function sbx_is_page_template_active( $filename = '' ) {
	global $wpdb;
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '_wp_page_template' AND meta_value = %s", $filename ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check if current page has a parent.
 *
 * Optionally check if page is child of a specific parent page ID.
 *
 * @since 1.0.0
 *
 * @param integer $page_id   Page ID.
 * @param integer $parent_id Specific parent page ID.
*/
function sbx_is_child_page( $page_id = 0, $parent_id = 0 ) {
	global $post;

	// If no $page_id specified, use current page
	if ( empty( $page_id ) )
		$page_id = $post->ID;

	// Get object from ID
	$page = get_post( $page_id );

	// If not working with a page, bail here
	if ( 'page' !== $page->post_type )
		return false;

	// If page has has no parent, bail here
	if ( empty( $page->post_parent ) )
		return false;

	// If looking for a specific parent, and we have no match, bail here
	if ( ! empty( $parent_id ) && ! in_array( $parent_id, get_post_ancestors( $page_id ) ) )
		return false;

	// Otherwise, object is a page, and it has a (matching) parent
	return true;

}
