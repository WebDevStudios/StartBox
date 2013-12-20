<?php
/**
 * SBX Utility Functions
 *
 * @package SBX
 * @subpackage Extensions
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! function_exists( 'sbx_get_page_title' ) ) :
/**
 * Build a smarter page title.
 *
 * @since  1.0.0
 *
 * @param  string $title         Default title.
 * @param  bool   $include_label True to include title label, otherwise false.
 * @return string                Filtered title.
 */
function sbx_get_page_title( $title = '', $include_label = true ) {
	global $post, $page, $paged;

	// Cache the original title
	$original_title = $title;

	// Filter for short-circuiting this function
	$title = apply_filters( 'sbx_pre_get_page_title', $title, $include_label, $post );

	// If no title was specified, try to build one
	if ( empty( $title ) ) {
		if ( is_singular() ) {
			$label = is_attachment()
				? sprintf(
					'<a href="%1$s" rev="attachment"><span class="meta-nav">&laquo; %2$s</span></a>',
					get_permalink( $post->post_parent ),
					get_the_title( $post->post_parent )
					)
				: '';
			$title = get_the_title();
		} elseif ( is_category() ) {
			$label = __( 'Category Archives: ', 'sbx' );
			$title = single_term_title( '', false );
		} elseif ( is_tag() ) {
			$label = __( 'Tag Archives: ', 'sbx' );
			$title = single_term_title( '', false );
		} elseif ( is_tax() ) {
			$taxonomy = get_query_var( 'taxonomy' );
			$label = sprintf( __( '%s Archives: ', 'sbx' ), $taxonomy->singular_label );
			$title = single_term_title( '', false );
		} elseif ( is_post_type_archive() ) {
			$label = __( 'Content Archives: ', 'sbx' );
			$title = post_type_archive_title( '', false );
		} elseif ( is_day() ) {
			$label = __( 'Daily Archives: ', 'sbx' );
			$title = get_the_time( get_option('date_format') );
		} elseif ( is_month() ) {
			$label = __( 'Monthly Archives: ', 'sbx' );
			$title = get_the_time( 'F Y' );
		} elseif ( is_year() ) {
			$label = __( 'Yearly Archives: ', 'sbx' );
			$title = get_the_time( 'Y' );
		} elseif ( is_tax( 'post_format' ) ) {
			$label = __( 'Format Archives: ', 'sbx' );
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = __( 'Asides', 'sbx' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) )  {
				$title = __( 'Images', 'sbx');
			} elseif ( is_tax( 'post_format', 'post-format-video' ) )  {
				$title = __( 'Videos', 'sbx' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) )  {
				$title = __( 'Quotes', 'sbx' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) )  {
				$title = __( 'Links', 'sbx' );
			}
		} elseif ( is_author() ) {
			$label = __( 'Author Archives: ', 'sbx' );
			the_post();
			$title = esc_html( get_the_author() );
			rewind_posts();
		} elseif ( is_search() ) {
			$label = __( 'Search Results for: ', 'sbx' );
			$title = esc_html( stripslashes( $_GET['s'] ), true );
		}  elseif ( is_404() ) {
			$label = '';
			$title = __( '404 - File Not Found', 'sbx' );
		} elseif ( $paged >= 2 || $page >= 2 ) {
			$label = __( 'Blog Archives: ', 'sbx' );
			$title = sprintf( __( 'Page %d', 'sbx' ), max( $paged, $page ) );
		} else {
			$label = '';
			$title = get_the_title();
		}

		// If prefix is not explicitly false, include the prefix.
		if ( false !== $include_label ) {
			$title = sprintf(
				'<span class="label">%1$s</span> %2$s',
				$label,
				$title
				);
		}

	}

	return apply_filters( 'sbx_get_page_title', $title, $original_title, $include_label, $post );
}
endif;

if ( ! function_exists( 'sbx_page_title' ) ) :
/**
 * Output contents of sbx_get_page_title().
 *
 * @since 1.0.0
 *
 * @param string $title         Default title.
 * @param bool   $include_label True to include title label, otherwise false.
 */
function sbx_page_title( $title = '', $include_label = true ) {
	echo sbx_get_page_title( $title, $include_label );
}
endif;

if ( ! function_exists( 'sbx_date_classes' ) ) :
/**
 * Generates time- and date-based classes relative to GMT (UTC).
 *
 * @since  1.0.0
 *
 * @param  integer $timestamp Timestamp.
 * @param  array   $classes   Original classes array.
 * @param  string  $prefix    Optional prefix string.
 * @return array              Updated classes array.
 */
function sbx_date_classes( $timestamp = 0, $classes = array(), $prefix = '' ) {

	// Relativise the timestamp
	$timestamp = $timestamp + ( get_option('gmt_offset') * 3600 );

	// Add a class for each major division of time
	$classes[] = $prefix . 'y' . gmdate( 'Y', $timestamp ); // Year
	$classes[] = $prefix . 'm' . gmdate( 'm', $timestamp ); // Month
	$classes[] = $prefix . 'd' . gmdate( 'd', $timestamp ); // Day
	$classes[] = $prefix . 'h' . gmdate( 'H', $timestamp ); // Hour

	return $classes;
}
endif;

if ( ! function_exists( 'sbx_time_since' ) ) :
/**
 * Generate a Relative Timestamp (alternative to human_time_diff()).
 *
 * Render a human-readable time difference between any
 * two timestamps. Difference will be displayed in the
 * two largest chunks of time, e.g.
 *   2 years, 5 months
 *   1 day, 14 hours
 *
 * Sample Usage: sbx_time_since( get_the_time( 'U' ) )
 *
 * @since  1.0.0
 *
 * @param  integer $older_date Original timestamp.
 * @param  integer $newer_date Known future date (Default: current timestamp).
 * @return string              Human-readable time difference.
*/
function sbx_get_time_since( $older_date = 0, $newer_date = 0 ) {

	// Get current time if no newer date specified
	$newer_date = empty( $newer_date )
		? ( time() + HOUR_IN_SECONDS * get_option( 'gmt_offset' ) )
		: $newer_date;

	// Calculate the time difference
	$elapsed = $newer_date - $older_date;

	// Define time chunks with labels
	$chunks = array(
		array( 'chunk' => YEAR_IN_SECONDS,     'label' => _n_noop( '%s year',   '%s years' ) ),
		array( 'chunk' => 30 * DAY_IN_SECONDS, 'label' => _n_noop( '%s month',  '%s months' ) ),
		array( 'chunk' => WEEK_IN_SECONDS,     'label' => _n_noop( '%s week',   '%s weeks' ) ),
		array( 'chunk' => DAY_IN_SECONDS,      'label' => _n_noop( '%s day',    '%s days' ) ),
		array( 'chunk' => HOUR_IN_SECONDS,     'label' => _n_noop( '%s hour',   '%s hours' ) ),
		array( 'chunk' => MINUTE_IN_SECONDS,   'label' => _n_noop( '%s minute', '%s minutes' ) ),
	);
	$total_chunks = count( $chunks );

	// Initialize output
	$output = array();

	// First Chunk
	for ( $i = 0; $i < $total_chunks; $i++ ) {
		// Break at the biggest chunk
		$count1 = floor( $elapsed / $chunks[ $i ]['chunk'] );
		if ( 0 != $count1 ) {
			$output[] = sprintf( translate_nooped_plural( $chunks[ $i ]['label'], $count1, 'sbx' ), $count1 );
			$i++;
			break;
		}
	}

	// Second Chunk
	if ( $i < $total_chunks ) {
		$count2 = floor( ( $elapsed - $chunks[ $i-1 ]['chunk'] * $count1 ) / $chunks[ $i ]['chunk'] );
		if ( 0 != $count2 ) {
			$output[] = sprintf( translate_nooped_plural( $chunks[ $i ]['label'], $count2, 'sbx' ), $count2 );
		}
	}

	// Flatten output
	$output = sprintf( __( '%s ago', 'sbx' ), implode( ', ', $output ) );

	// Return filterable output
	return apply_filters( 'sbx_get_time_since', $output, $older_date, $newer_date );
}
endif;


if ( ! function_exists( 'sbx_dropdown_posts' ) ) :
/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @since 1.0.0
 *
 * @param  array  $args Configuration args.
 * @return string       HTML markup.
 */
function sbx_dropdown_posts( $args = '' ) {
	global $wpdb;

	// Setup default args
	$defaults = array(
		'post_type'         => 'post',
		'post_status'       => 'publish',
		'order_by'          => 'post_date',
		'order'             => 'DESC',
		'limit'             => 30,
		'selected'          => 0,
		'echo'              => 1,
		'name'              => '',
		'id'                => '',
		'class'             => 'postlist',
		'show_option_none'  => __( 'Select a Post', 'sbx' ),
	);
	$args = wp_parse_args( $args, $defaults );

	// Query the Posts
	$order_by  = sanitize_sql_orderby( $args['order_by'] . ' ' . $args['order'] );
	$post_list = $wpdb->get_results(
		$wpdb->prepare(
			"
			SELECT ID, post_title, post_date
			FROM $wpdb->posts
			WHERE post_type = %s
			AND post_status = %s
			ORDER BY {$order_by}
			LIMIT %d
			",
			$args['post_type'],
			$args['post_status'],
			$args['limit']
		),
		'ARRAY_N'
	);

	// Build Output
	$output = "\n\t\t" . '<select style="width:100%;" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="' . esc_attr( $args['class'] ) . '">';
	if ( $args['show_option_none'] ) {
		$output .= "\n\t\t\t" . '<option value="">' . $args['show_option_none'] . '</option>';
	}
	if ( ! empty( $post_list ) ) {
		foreach ( $post_list as $post ) {
			$output .= "\n\t\t\t" . '<option value="' . $post->ID . '"' . selected( $post->ID, $args['selected'], false ) . '>' . $post->post_title . '</option>';
		}
	}
	$output .= "\n\t\t" . '</select>';

	$output = apply_filters( 'sbx_dropdown_posts', $output, $args );

	if ( $echo )
		echo $output;

	return $output;
}
endif;

if ( !function_exists( 'sb_nav_menu_fallback' ) ) :
/**
 * Fallback menu function if custom menus exist.
 *
 * @since  1.0.0
 *
 * @param  array $args Output args.
 * @return string      HTML Markup.
*/
function sb_nav_menu_fallback( $args = array() ) {

	$defaults = array(
		'depth'       => 1,
		'sort_column' => 'menu_order, post_title',
		'menu_class'  => 'menu',
		'include'     => '',
		'exclude'     => '',
		'echo'        => true,
		'show_home'   => true,
		'link_before' => '',
		'link_after'  => ''
	);
	$args = wp_parse_args( $args, $defaults );

	$output = '<ul>' . wp_page_menu( $args ) . '</ul>';

	return apply_filters( 'sb_nav_menu_fallback', $output, $args );

}
endif;

if ( ! function_exists( 'sbx_content_nav' ) ) :
/**
 * Output next/previous navigation when applicable.
 *
 * @since 1.0.0
 *
 * @param string $container_id Container CSS ID.
 */
function sbx_content_nav( $container_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';
	?>
	<nav id="<?php echo esc_attr( $container_id ); ?>" class="<?php echo $nav_class; ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'sbx' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous" itemprop="name">%link</div>', '<span class="meta-nav" itemprop="url">' . _x( '&larr;', 'Previous post link', 'sbx' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next" itemprop="name">%link</div>', '%title <span class="meta-nav" itemprop="url">' . _x( '&rarr;', 'Next post link', 'sbx' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous" itemprop="name"><?php next_posts_link( __( '<span class="meta-nav" itemprop="url">&larr;</span> Older posts', 'sbx' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next" itemprop="name"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav" itemprop="url">&rarr;</span>', 'sbx' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $container_id ); ?> -->
	<?php
}
endif;

if ( ! function_exists( 'sbx_comment' ) ) :
/**
 * Output template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since 1.0.0
 *
 * @param object  $comment Comment object.
 * @param array   $args    Formatting args.
 * @param integer $depth   Threaded comment depth.
 */
function sbx_comment( $comment = null, $args = array(), $depth = 0 ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'sbx' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'sbx' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">

			<header class="comment-meta">
				<div class="comment-gravatar col span-2">
					<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div>
				<div class="comment-metadata col no-gutters span-10">
					<div class="comment-author vcard" itemprop="creator" itemscope itemtype="http://schema.org/Person">
						<?php printf( __( '%s <span class="says">says:</span>', 'sbx' ), sprintf( '<cite class="fn" itemprop="name">%s</cite>', get_comment_author_link() ) ); ?>
					</div><!-- .comment-author -->

					<div class="comment-meta">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time itemprop="commentTime" datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'sbx' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>
					</div>
				</div><!-- .comment-metadata -->
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'sbx' ); ?></p>
				<?php endif; ?>
			</header><!-- .comment-meta -->

			<div class="comment-content col span-12" itemprop="commentText">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<footer class="comment-reply col span-12">
				<?php
					// Comment reply link
					comment_reply_link( array_merge( $args,
						array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply">',
							'after'     => '</div>',
						)));

					// Edit comment
					edit_comment_link( __( '(Edit this comment)', 'sbx' ), '<span class="edit-link">', '</span>' );
				?>
			</footer><!-- .comment-reply -->

		</article><!-- .comment-body -->

	<?php
	endif;
}
endif;

if ( ! function_exists( 'sbx_get_author_box' ) ) :
/**
 * Render an Author Box.
 *
 * @since  1.0.0
 *
 * @param  array  $args Output args.
 * @return string       HTML markup.
 */
function sbx_get_author_box( $args = array() ) {

	// Setup defaults
	$defaults = apply_filters( 'sbx_author_box_defaults',
		array(
			'gravatar_size' => 96,
			'title'         => __( 'About', 'startbox' ),
			'name'          => get_the_author_meta( 'display_name' ),
			'email'         => get_the_author_meta( 'email' ),
			'description'   => get_the_author_meta( 'description' ),
			'user_id'       => get_the_author_meta( 'ID' ),
		),
		$args
	);

	// Parse defaults against passed args
	$args = wp_parse_args( $args, $defaults );

	$output = '';
	$output .= '<section class="author-box" itemprop="author" itemscope itemtype="http://schema.org/Person">';
	$output .= '<div class="author-gravatar">' . get_avatar( sanitize_email( $args['email'] ), absint( $args['gravatar_size'] ) ) . '</div>';
	$output .= '<div class="author-bio">';
	$output .= '<h2 class="author-title">' . wp_kses_post( $args['title'] ) . '<span itemprop="name">' . wp_kses_post( $args['name'] ) . '</span></h2>';
	$output .= '<p><span itemprop="description">' . wp_kses_post( $args['description'] ) . '</span></p>';
	$output .= '</div>';
	$output .= '</section>';

	// Return our filterable markup
	return apply_filters( 'sbx_author_box', $output, $args );
}
endif;

if ( ! function_exists( 'sbx_author_box' ) ) :
/**
 * Output an Author Box.
 *
 * @since 1.0.0
 *
 * @param array $args Output args.
 */
function sbx_author_box( $args = array() ) {
	echo sbx_get_author_box( $args );
}
endif;

if ( ! function_exists( 'sbx_rtt' ) ) :
/**
 * Render a "Return to Top" link.
 *
 * Renders an anchor tag, wrapped in a span, pointing to #top.
 *
 * @since  1.0.0
 *
 * @return string HTML output.
 */
function sbx_rtt() {
	return apply_filters( 'sbx_rtt', sprintf(
		'<p class="rtt"><a href="#top" class="cb">%s</a></p>',
		apply_filters( 'sbx_rtt_text', __( 'Return to Top', 'startbox' ) )
		)
	);
}
endif;
