<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package sbx
 */


if ( ! function_exists( 'sbx_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function sbx_posted_on() {
	$time_string = '<time class="entry-date published updated" itemprop="datePublished" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
		$time_string .= '<time class="entry-updated updated" itemprop="dateModified" datetime="%3$s">%4$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( __( '<span class="posted-on">Posted on %1$s</span><span class="byline"> by %2$s</span>', 'sbx' ),
		sprintf( '%3$s',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$time_string
		),
		sprintf( '<span class="author vcard" itemprop="author" itemscope itemptype="http://schema.org/Person"><a class="url fn n" href="%1$s" title="%2$s" itemprop="url" rel="author"><span class="entry-author-name" itemprop="name">%3$s</span></a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'sbx' ), get_the_author() ) ),
			esc_html( get_the_author() )
		)
	);
}
endif;


if ( ! function_exists( 'sbx_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function sbx_content_nav( $nav_id ) {
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
	<nav id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
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

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // sbx_content_nav


if ( ! function_exists( 'sbx_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function sbx_comment( $comment, $args, $depth ) {
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
endif; // ends check for sbx_comment()


if ( ! function_exists( 'sbx_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function sbx_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'sbx_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment" itemprop="thumbnailUrl">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;


if ( ! function_exists( 'sbx_entry_meta' ) ) :
/**
 * Create CPT entry meta
 */
function sbx_entry_meta() {

	// Get the categories
	$category_list = get_the_category_list( __( ', ', 'sbx' ) );

	// Get the tags
	$tag_list = get_the_tag_list( '', __( ', ', 'sbx' ) );

	if ( ! sbx_categorized_blog() ) {
		// This blog only has 1 category so we just need to worry about tags in the meta text
		if ( '' != $tag_list ) {
			$meta_text = __( 'This entry was tagged with %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sbx' );
		} else {
			$meta_text = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sbx' );
		}

	} else {
		// But this blog has loads of categories so we should probably display them here
		if ( '' != $tag_list ) {
			$meta_text = __( 'This entry was posted in %1$s and tagged with %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sbx' );
		} else {
			$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sbx' );
		}

	} // end check for categories on this blog

	printf(
		$meta_text,
		$category_list,
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);

}
endif;


/**
 * Returns true if a blog has more than 1 category
 */
function sbx_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so sbx_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so sbx_categorized_blog should return false
		return false;
	}
}


/**
 * Flush out the transients used in sbx_categorized_blog
 */
function sbx_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'sbx_category_transient_flusher' );
add_action( 'save_post',     'sbx_category_transient_flusher' );
