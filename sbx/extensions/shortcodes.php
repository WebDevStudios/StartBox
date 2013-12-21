<?php
/**
 * SBX Shortcodes
 *
 * @package SBX
 * @subpackage Extensions
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Render title for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_title() {
	return get_the_title();
}
add_shortcode( 'title', 'sbx_entry_title' );

/**
 * Render hAtom-formatted author link for current post.
 *
 * @since 1.0.0
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
 * @since 1.0.0
 */
function sbx_entry_author_box() {
	return sbx_get_author_box();
}
add_shortcode( 'author_box', 'sbx_entry_author_box' );

/**
 * Render category list for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_categories( $atts ) {

	$atts = shortcode_atts(
			array(
				'label' => __( 'Categories: ', 'sbx' ),
				'class' => 'entry-categories',
				),
			$atts
		);

	if ( $categories = get_the_category_list( ', ' ) )
		return '<span class="' . $atts['class'] . '">' . $atts['label'] . $categories . '</span>';
}
add_shortcode( 'categories', 'sbx_entry_categories' );

/**
 * Render tag list for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_tags( $atts ) {

	$atts = shortcode_atts(
			array(
				'label' => __( 'Tags: ', 'sbx' ),
				'class' => 'entry-tags',
				),
			$atts
		);

	if ( $tags = get_the_tag_list( '', ', ' ) )
		return '<span class="' . $atts['class'] . '">' . $atts['label'] . $tags . '</span>';
}
add_shortcode( 'tags', 'sbx_entry_tags' );

/**
 * Render comments link for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_comments() {
	ob_start();
 	comments_popup_link( __( 'Post a comment', 'sbx' ), __( '1 Comment', 'sbx' ), __( '% Comments', 'sbx' ), 'comments-link', __( 'Comments closed', 'sbx' ) );
	return '<span class="entry-comments">' . ob_get_clean() . '</span>';
}
add_shortcode( 'comments', 'sbx_entry_comments' );

/**
 * Render hAtom-formatted date stamp for current post.
 *
 * @since 1.0.0
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
		? sbx_get_time_since( get_the_time( 'U' ) )
		: esc_attr( $date );

	return '<time class="entry-date published updated" itemprop="datePublished" datetime="' . esc_attr( $isodate ) . '">' . $output_date . '</time>';

}
add_shortcode( 'date', 'sbx_entry_date' );

/**
 * Render time stamp for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_time() {
	return '<span class="entry-time">' . get_the_time() . '</span>';
}
add_shortcode( 'time', 'sbx_entry_time' );

/**
 * Render "Edit" link for current post for authorized users.
 *
 * @since 1.0.0
 */
function sbx_entry_edit() {
	if ( current_user_can( 'edit_posts' ) ) {
		return '<span class="edit-link">&nbsp;(<a href="' . get_edit_post_link() . '">' . __( 'Edit', 'sbx' ) . '</a>)</span>';
	}
}
add_shortcode( 'edit', 'sbx_entry_edit' );

/**
 * Render "Read More" link for current post.
 *
 * @since 1.0.0
 */
function sbx_entry_readmore( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'text'   => __( 'Read &amp; Discuss &raquo;', 'sbx' ),
			'class' => 'more-link'
			),
		$atts
	);

	return sprintf(
		'<a href="%s" rel="nofollow" class="%2$s">%3$s</a>',
		get_permalink(),
		$atts['class'],
		$atts['text']
		);
}
add_shortcode( 'more', 'sbx_entry_readmore' );

/**
 * Render copyright date(s).
 *
 * If provided date is not the current year, this will
 * intelligently render XXXX-YYYY.
 *
 * @since 1.0.0
 */
function sbx_copyright( $atts ) {
	$atts = shortcode_atts(
		array( 'year' => date( 'Y' ) ),
		$atts
	);

	$given_year = absint( $atts['year'] );
	$current_year = date( 'Y' );

	return ( $given_year == $current_year )
		? sprintf( '&copy; %d', $current_year )
		: sprintf( '&copy; %1$d-%2$d', min( $given_year, $current_year ), max( $given_year, $current_year ) );

}
add_shortcode( 'copyright', 'sbx_copyright' );
