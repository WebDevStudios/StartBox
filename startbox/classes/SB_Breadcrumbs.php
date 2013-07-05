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
	public var $args = array();

	/**
	 * Constructor. Set up cacheable values and settings.
	 *
	 * @since 3.0.0
	 */
	function __construct() {

		// Setup argument defaults
		$this->args = array(
			'home'                    => __( 'Home', 'startbox' ),
			'sep'                     => ' / ',
			'before_crumbs'           => '<div class="breadcrumb">',
			'after_crumbs'            => '</div>',
			'heirarchial_attachments' => true,
			'heirarchial_categories'  => true,
			'display'                 => true,
			'labels' => array(
				'prefix'    => __( 'You are here: ', 'startbox' ),
				'author'    => __( 'Archives for ', 'startbox' ),
				'category'  => __( 'Archives for ', 'startbox' ),
				'tag'       => __( 'Archives for ', 'startbox' ),
				'date'      => __( 'Archives for ', 'startbox' ),
				'search'    => __( 'Search for ', 'startbox' ),
				'tax'       => __( 'Archives for ', 'startbox' ),
				'post_type' => __( 'Archives for ', 'startbox' ),
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
		$this->build_crumb_trail();

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
		return sprintf( '<a href="%1$s">%2$s</a>', esc_attr( $url ), esc_html( $text ) );
	}

	/**
	 * Return home (front) page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_home_crumb() {}

	/**
	 * Return blog page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_blog_crumb() {}

	/**
	 * Return search results page breadcrumb
	 *
	 * @since 3.0.0
	 * @return string HTML markup
	 */
	function get_search_crumb() {}

	/**
	 * Return 404 page breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_404_crumb() {}

	/**
	 * Return archive breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_archive_crumb() {}

	/**
	 * Return single post type breadcrumb
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function get_single_crumb() {}

	/**
	 * Return recursive linked crumbs of post parents
	 * for hierarchical post types (e.g. page)
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML Markup
	 */
	function get_post_parent_crumbs() {}

	/**
	 * Return recursive linked crumbs of category,
	 * tag or custom taxonomy parents.
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML Markup
	 */
	function get_term_parent_crumbs() {}

	/**
	 * Return the combined breadcrumb trail
	 *
	 * @since  3.0.0
	 * @return string Concatenated HTML markup
	 */
	function build_crumb_trail() {}

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
