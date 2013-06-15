<?php
/**
 * StartBox Conditional Functions
 *
 * @package StartBox
 * @subpackage Functions
 */

/**
 * Tests if any of a post's assigned categories are descendants of specified categories
 *
 * @since Unknown
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 */
function sb_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

/**
 * Tests to see if a specific page template is active.
 *
 * @since Unknown
 *
 * @param string $pagetemplate is the template filename
*/
function sb_is_pagetemplate_active($pagetemplate = '') {
	global $wpdb;
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '_wp_page_template' AND meta_value = %s", $pagetemplate ) ) ) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Tests to see if current page has a parent.
 *
 * @since 2.4.9
 *
 * @param integer $page_id the page ID to test
 * @param integer $parent_id (optional) check if page is child of specific parent
*/
function sb_is_child_page( $parent_id = null, $page_id = null ) {
	global $post;
	$pid = ($page_id) ? $page_id : $post->ID;

	if ( is_page($pid) && $post->post_parent ) { // Verify we're working with a page and it has a parent
		if ( isset( $parent_id ) && !in_array( $parent_id, get_post_ancestors($pid) ) ) { return false; }// If the specified parent_id is not an ancestor of the current page, return false
		else { return true; } // Otherwise, it has a parent and the specified parent id match. Return true.
	} else {
		return false; // if it's not a page or has no parent, return false.
	}

}

/**
 * Utility: Verify a given post type
 *
 * @since 2.5
 * @param string $type the post type to verify against
 */
function sb_verify_post_type( $type ) {
	global $post_type;

	if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $type ) || ( isset( $post_type ) && $post_type == $type ) )
		return true;
	else
		return false;
}