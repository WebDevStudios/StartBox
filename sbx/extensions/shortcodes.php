<?php
/**
 * SBX Shortcodes
 *
 * This file contains definitions for core SBX shortcodes.
 *
 * @package SBX
 * @subpackage Shortcodes
 */

/**
 * Render title for current post.
 *
 * @since 2.5.4
 */
function sbx_entry_title() {
	return get_the_title();
}
add_shortcode( 'title', 'sbx_entry_title' );

/**
 * Render hAtom-formatted author link for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_author() {

	$output = '<span class="vcard author entry-author" itemprop="author" itemscope itemptype="http://schema.org/Person">';
	$output .= '<a class="url fn n" itemprop="url" rel="author" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">';
	$output .= '<span class="entry-author-name" itemprop="name">' . get_the_author() . '</span>';
	$output .= '</a>';
	$output .= '</span>';

	return $output;
}
add_shortcode( 'author', 'sbx_entry_author' );

/**
 * Render author box for current post.
 *
 * @since 2.4.8
 */
function sbx_entry_author_box() {
	return sbx_get_author_box();
}
add_shortcode( 'author_box', 'sbx_entry_author_box' );

/**
 * Render category list for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_categories() {
	if ( $categories = get_the_category_list( ', ' ) )
		return '<span class="entry-categories">' . __( 'Categories: ', 'sbx' ) . $categories . '</span>';
}
add_shortcode( 'categories', 'sbx_entry_categories' );

/**
 * Render tag list for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_tags() {
	if ( $tags = get_the_tag_list( '', ', ' ) )
		return '<span class="entry-tags">' . __( 'Tags: ', 'sbx' ) . $tags . '</span>';
}
add_shortcode( 'tags', 'sbx_entry_tags' );

/**
 * Render comments link for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_comments() {
	ob_start();
	comments_popup_link(__( 'No Comments', 'startbox' ), __( '1 Comment', 'startbox' ), __( '% Comments', 'startbox' ) );
	return '<span class="entry-comments">' . ob_get_clean() . '</span>';
}
add_shortcode( 'comments', 'sbx_entry_comments' );

/**
 * Render hAtom-formatted date stamp for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_date( $atts ) {

	$atts = shortcode_atts(
		array(
			'format'   => get_option( 'date_format' ),
			'relative' => false
			),
		$atts
	);

	$date        = get_the_time( $atts['format'] );
	$isodate     = get_the_time( 'c' );
	$output_date = $atts['relative']
		? sprintf( __( '%s ago', 'startbox' ), sbx_time_since( absint( strtotime( $date ) ), time() ) )
		: esc_attr( $date );

	return '<time class="entry-date published updated" itemprop="datePublished" datetime="' . esc_attr( $isodate ) . '">' . $output_date . '</time>';

}
add_shortcode( 'date', 'sbx_entry_date' );

/**
 * Render time stamp for current post.
 *
 * @since 2.4.6
 */
function sbx_entry_time() {
	return '<span class="entry-time">' . get_the_time() . '</span>';
}
add_shortcode( 'time', 'sbx_entry_time' );

/**
 * Render "Edit" link for current post for authorized users.
 *
 * @since 2.4.6
 */
function sbx_entry_edit() {
	if ( current_user_can( 'edit_posts' ) )
		return '<span class="edit-link">&nbsp;(<a href="' . get_edit_post_link() . '">' . __( 'Edit', 'startbox' ) . '</a>)</span>';
}
add_shortcode( 'edit', 'sbx_entry_edit' );

/**
 * Render "Read More" link for current post.
 *
 * @since 2.4.9
 */
function sbx_entry_readmore() {
	return '<a href="' . get_permalink() . '" rel="nofollow" class="more-link">' . apply_filters( 'sb_read_more', 'Read &amp; Discuss &raquo;' ) . '</a>';
}
add_shortcode( 'more', 'sbx_entry_readmore' );

/**
 * Render copyright date(s).
 *
 * @since 2.6.0
 */
function sbx_copyright( $atts ) {
	$atts = shortcode_atts(
		array(
			'year' => date( 'Y' ),
			),
		$atts
	);

	$current_year = date('Y');

	return ( $atts['year'] == $current_year )
		? sprintf( __( '&copy; %d', 'startbox' ), $current_year )
		: sprintf( __( '&copy; %1$d-%2$d', 'startbox' ), $atts['year'], $current_year );

}
add_shortcode( 'copyright', 'sbx_copyright' );
