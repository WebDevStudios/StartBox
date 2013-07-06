<?php
/**
 * StartBox Breadcrumbs
 *
 * @package StartBox
 * @subpackage Breadcrumbs
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports sidebars, skip the rest if not
if ( ! current_theme_supports( 'sb-breadcrumbs' ) )
	return;

/**
 * Base class for hanling breadcrumb navigation
 *
 * @subpackage Classes
 * @since 3.0.0
 */
class SB_Breadcrumbs {

	/**
	 * Settings array, a merge of provided values and defaults. Private.
	 *
	 * @since 3.0.0
	 * @var array Holds the breadcrumb arguments
	 */
	public $args = array();

	/**
	 * Constructor. Set up cacheable values and settings.
	 *
	 * @since 3.0.0
	 */
	function __construct() {

		// Setup argument defaults
		$this->args = array(
			'before_crumbs'           => '<div class="breadcrumb">',
			'after_crumbs'            => '</div>',
			'sep'                     => ' / ',
			'heirarchial_attachments' => true,
			'heirarchial_categories'  => true,
			'display'                 => true,
			'labels' => array(
				'prefix'    => __( 'You are here: ', 'startbox' ),
				'home'      => __( 'Home', 'startbox' ),
				'search'    => __( 'Search for ', 'startbox' ),
				'404'       => __( 'Not found: ', 'startbox' )
			)
		);

	}

	/**
	 * Return the final completed breadcrumb in markup wrapper. Public.
	 *
	 * @since 3.0.0
	 *
	 * @param  array  $args Breadcrumb arguments
	 * @return string       Concatenated HTML markup
	 */
	function get_output( $args = array() ) {

		// Merge and filter our default arguments
		$this->args = apply_filters( 'sb_breadcrumb_args', wp_parse_args( $args, $this->args ) );

		// Setup our output
		$output = '';
		$output .= $this->args['before_crumbs'];
		$output .= $this->args['labels']['prefix'];

		// Get our crumbs
		$output .= $this->build_crumb_trail();

		// Close our output
		$output .= $this->args['after_crumbs'];

		// Return our output
		return $output;

	}

	/**
	 * Return anchor link for a single crumb. Private.
	 *
	 * @since  3.0.0
	 * @param  string $url  The link URL for the crumb
	 * @param  string $text The link text for the crumb
	 * @return string       Concatenated HTML markup
	 */
	function get_breadcrumb_link( $url = '', $text = '' ) {
		$link = sprintf( '<a href="%1$s">%2$s</a>', esc_attr( $url ), esc_html( $text ) );
		return apply_filters( 'sb_get_breadcrumb_link', $link, $this->args );
	}

	/**
	 * Return home (front) page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_home_crumb() {

		if ( is_front_page() )
			$trail = $this->args['labels']['home'];
		else
			$trail = $this->get_breadcrumb_link( site_url(), $this->args['labels']['home'] );

		return apply_filters( 'sb_get_home_crumb', $trail, $this->args );
	}

	/**
	 * Return blog page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_blog_crumb() {

		if ( 'page' == get_option( 'show_on_front' ) )
			$trail = $this->get_breadcrumb_link( home_url(), get_the_title( get_option( 'page_for_posts' ) ) );
		else
			$trail = '';

		return apply_filters( 'sb_get_blog_crumb', $trail, $this->args );
	}

	/**
	 * Return search results page breadcrumb
	 *
	 * @since 3.0.0
	 * @return string HTML markup
	 */
	function get_search_crumb() {
		$search_query = esc_html( apply_filters( 'the_search_query', get_search_query() ) );
		$trail = $this->args['labels']['search'] . ' "' . $search_query . '"';
		return apply_filters( 'sb_get_search_crumb', $trail, $this->args );
	}

	/**
	 * Return 404 page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_404_crumb() {
		return apply_filters( 'sb_get_404_crumb', $this->args['labels']['404'], $this->args );
	}

	/**
	 * Return archive breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_archive_crumb() {
		global $wp_query, $wp_locale;

		// Setup our empty trail
		$trail = array();

		// If we're on a blog-based archive, and the blog is set to a page, include the blog crumb
		if ( is_category() || is_tag() || is_tax() || is_year() || is_month() || is_day() )
			if ( $this->get_blog_crumb() )
				$trail[] = $this->get_blog_crumb();

		// Iterate through the possible archives
		if ( is_category() ) {
			$trail[] = $this->get_post_term_crumbs( get_query_var( 'cat' ), 'category' );
		} elseif ( is_tag() ) {
			$trail[] = single_term_title( '', false );
		} elseif ( is_tax() ) {
			$term  = $wp_query->get_queried_object();
			$trail[] = $this->get_post_term_crumbs( $term->term_id, $term->taxonomy );
		} elseif ( is_year() ) {
			$trail[] = get_query_var( 'year' );
		} elseif ( is_month() ) {
			$trail[] = $this->get_breadcrumb_link( get_year_link( get_query_var( 'year' ) ), get_query_var( 'year' ) );
			$trail[] = single_month_title( ' ', false );
		} elseif ( is_day() ) {
			$trail[] = $this->get_breadcrumb_link( get_year_link( get_query_var( 'year' ) ), get_query_var( 'year' ) );
			$trail[] = $this->get_breadcrumb_link( get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ), $wp_locale->get_month( get_query_var( 'monthnum' ) ) );
			$trail[] = get_query_var( 'day' ) . date( 'S', mktime( 0, 0, 0, 1, get_query_var( 'day' ) ) );
		} elseif ( is_author() ) {
			$trail[] = $this->args['labels']['author'] . esc_html( $wp_query->queried_object->display_name );
		} elseif ( is_post_type_archive() ) {
			$trail[] = $this->args['labels']['post_type'] . esc_html( post_type_archive_title( '', false ) );
		}

		// Return filterable output
		return apply_filters( 'sb_get_archive_crumb', $this->glue_crumbs_together( $trail ), $wp_query, $this->args );

	}

	/**
	 * Return single post type breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_singular_crumb() {

		// Get our relevant post data
		$post             = get_queried_object();
		$parent           = absint( $post->post_parent );
		$post_type        = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );

		// Assume we have nothing to output
		$trail = array();

		// If we have any ancestors, include them in the trail
		if ( $post->ancestors ) {
			$ancestors = array_reverse( $post->ancestors );
			foreach ( $ancestors as $ancestor ) {
				$trail[] = $this->get_breadcrumb_link( get_permalink( $ancestor ), get_the_title( $ancestor ) );
			}

		// Or we're dealing with a categorized post
		} elseif ( is_singular( 'post' ) && $categories = get_the_category( $post->ID ) ) {
			if ( is_array( $categories ) )
				$trail[] = $this->get_post_term_crumbs( $categories[0]->cat_ID, 'category' );

		// Or we're dealing with a CPT
		} elseif ( 'page' != $post->post_type ) {
			if ( $post_type_archive = get_post_type_archive_link( $post_type ) )
				$trail[] = $this->get_breadcrumb_link( $post_type_archive, $post_type_object->labels->name );
			else
				$trail[] = $post_type_object->labels->name;
		}

		// Include our current post at the end of the trail
		$trail[] = $post->post_title;

		// Return filterable output
		return apply_filters( 'sb_get_singular_crumb', $this->glue_crumbs_together( $trail ), $post, $this->args );
	}

	/**
	 * Return recursive linked crumbs of category,
	 * tag or custom taxonomy parents.
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML Markup
	 */
	function get_post_term_crumbs( $term_id = 0, $taxonomy = '' ) {

		// Grab the term object (suppress errors)
		$term = &get_term( absint( $term_id ), $taxonomy );

		// Setup our empty trail
		$trail = array();

		// If the term has a parent, and we support
		// category heirarchy, loop back and get it
		if ( ! empty( $term->parent ) && $this->args['heirarchial_categories'] )
			$trail[] = $this->get_post_term_crumbs( $term->parent, $taxonomy );

		// Include our current term
		if ( is_object( $term ) )
			$trail[] = $this->get_breadcrumb_link( get_term_link( $term ), $term->name );

		// Return filterable output
		return apply_filters( 'sb_get_post_term_crumbs', $this->glue_crumbs_together( $trail ), $term_id, $taxonomy, $this->args );
	}

	/**
	 * Return the combined breadcrumb trail
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function build_crumb_trail() {

		// Setup our empty trail
		$trail = array();

		// Get the starting crumb
		$trail[] = $this->get_home_crumb();

		if ( is_home() && ! is_front_page() )
			$trail[] = $this->get_blog_crumb();
		elseif ( is_search() )
			$trail[] = $this->get_search_crumb();
		elseif ( is_404() )
			$trail[] = $this->get_404_crumb();
		elseif ( is_archive() )
			$trail[] = $this->get_archive_crumb();
		elseif ( is_singular() )
			$trail[] = $this->get_singular_crumb();

		// Return filterable output
		return apply_filters( 'sb_build_crumb_trail', $this->glue_crumbs_together( $trail ), $this->args );

	}

	/**
	 * Glues individual crumbs together with separator
	 * and drops duplicates.
	 *
	 * @since  3.0.0
	 * @param  array  $crumbs The array of crumbs
	 * @return string         Flattened, unique list of crumbs
	 */
	function glue_crumbs_together( array $crumbs ) {
		return implode( $this->args['sep'], array_unique( $crumbs ) );
	}

}
$GLOBALS['startbox']->breadcrumbs = new SB_Breadcrumbs;

/**
 * Helper function for the startbox Breadcrumb Class.
 *
 * @since 3.0.0
 * @param array $args Breadcrumb arguments
 */
function sb_breadcrumbs( $args = array() ) {
	global $startbox;
	echo $startbox->breadcrumbs->get_output( $args );
}
add_action( 'sb_after_header', 'sb_breadcrumbs' );
