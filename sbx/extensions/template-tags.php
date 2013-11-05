<?php
/**
 * Custom template tags and helper functions for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package sbx
 */


if ( ! function_exists( 'sb_time_since' ) ) :
/**
 * Display Relative Timestamps
 *
 * This plugin is based on code from Dunstan Orchard's Blog. Pluginiffied by Michael Heilemann:
 * @link http://www.1976design.com/blog/archive/2004/07/23/redesign-time-presentation/
 *
 * Usage:
 * For posts: echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . ' ago';
 * For comments: echo time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time()) . ' ago';
 *
 * @since 2.4.6
 * @param integer $older_date The original date in question
 * @param integer $newer_date Specify a known date to determine elapsed time. Will use current time if false Default: false
 * @return string Time since
*/
function sb_time_since($older_date, $newer_date = false) {

	// array of time period chunks
	$chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'minute'),
	);

	// Newer Date (false to use current time)
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;

	// difference in seconds
	$since = $newer_date - $older_date;

	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];

		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}

	return $output;
}
endif;


if ( ! function_exists( 'sb_dropdown_posts' ) ) :
/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @since 2.4.7
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function sb_dropdown_posts($args = '') {

	$defaults = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'order_by' => 'post_date',
		'order' => 'DESC',
		'limit' => 30,
		'selected' => 0,
		'echo' => 1,
		'name' => '',
		'id' => '',
		'class' => 'postlist',
		'show_option_none' => true,
		'option_none_value' => 'Select a Post'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Query the Posts
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$limit = ( $limit ) ? ' LIMIT '.absint( $limit ) : '';
	$id = esc_attr($id);
	$name = esc_attr($name);
	$output = '';
	$order_by = sanitize_sql_orderby( $order_by . ' ' . $order );

	$post_list = (array)$wpdb->get_results(
		$wpdb->prepare("
		SELECT ID, post_title, post_date
		FROM $wpdb->posts
		WHERE post_type = %s
		AND post_status = %s
		ORDER BY {$order_by}
		{$limit}
	", $post_type, $post_status ) );

	$output .= "\t" . "\t" . '<select style="width:100%;" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '">'."\n";
	if ( !empty($post_list) ) {
		if ( $show_option_none ) $output .= "\t" . "\t" . "\t" . '<option value="">' . $option_none_value . '</option>';
		foreach ($post_list as $posts) {
			if ($selected == $posts->ID) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= "\t" . "\t" . "\t" . '<option value="' . $posts->ID . '"' . $select . '>' . $posts->post_title . '</option>';
		}
	} else {
		$output .= "\t" . "\t" . "\t" . '<option value="">Nothing to Display</option>';
	}
	$output .= '</select>';

	$output = apply_filters('wp_dropdown_posts', $output);

	if ( $echo )
		echo $output;
	else
		return $output;
}
endif;


if ( ! function_exists( 'sb_tag_query' ) ) :
/**
 * Create a nice multi-tag title
 *
 * Credits: Ian Stewart and Martin Kopischke for providing this code
 *
 * @since 2.4.7
 */
function sb_tag_query() {
	$nice_tag_query = get_query_var('tag'); // tags in current query
	$nice_tag_query = str_replace(' ', '+', $nice_tag_query); // get_query_var returns ' ' for AND, replace by +
	$tag_slugs = preg_split('%[,+]%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of tag slugs
	$tag_ops = preg_split('%[^,+]*%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of operators

	$tag_ops_counter = 0;
	$nice_tag_query = '';

	foreach ($tag_slugs as $tag_slug) {
		$tag = get_term_by('slug', $tag_slug ,'post_tag');
		// prettify tag operator, if any
		if ( isset( $tag_ops[$tag_ops_counter] ) &&  $tag_ops[$tag_ops_counter] == ',') {
			$tag_ops[$tag_ops_counter] = ', ';
		} elseif ( isset( $tag_ops[$tag_ops_counter] ) && $tag_ops[$tag_ops_counter] == '+') {
			$tag_ops[$tag_ops_counter] = ' + ';
		} else {
			$tag_ops[$tag_ops_counter] = '';
		}
		// concatenate display name and prettified operators
		$nice_tag_query = $nice_tag_query . $tag->name . $tag_ops[$tag_ops_counter];
		$tag_ops_counter += 1;
	}
	 return $nice_tag_query;
}
endif;


/**
 * Function for retrieving taxonomy meta information
 *
 * @since 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'get_taxonomy_term_type' ) ) {
	function get_taxonomy_term_type($taxonomy,$term_id) {
		return get_option("_term_type_{$taxonomy}_{$term->term_id}");
	}
}


/**
 * Function for updating taxonomy meta information
 *
 * @since 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 * @param mixed $value the new value
 *
 */
if ( !function_exists( 'update_taxonomy_term_type' ) ) {
	function update_taxonomy_term_type($taxonomy,$term_id,$value) {
		update_option("_term_type_{$taxonomy}_{$term_id}",$value);
	}
}


/**
 * Function for deleting taxonomy meta information
 *
 * @since 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'delete_taxonomy_term_type' ) ) {
	function delete_taxonomy_term_type($taxonomy,$term_id ) {
		delete_option("_term_type_{$taxonomy}_{$term_id}");
	}
}


/**
 * Forever eliminate "Startbox" from the planet (or at least the little bit we can influence).
 *
 * Violating our coding standards for a good function name.
 *
 * @since 2.7.0
 */
function capital_B_dangit( $text ) {

	// Simple replacement for titles
	if ( 'the_title' === current_filter() )
		return str_replace( 'Startbox', 'StartBox', $text );

	// Still here? Use the more judicious replacement
	static $dblq = false;
	if ( false === $dblq )
		$dblq = _x( '&#8220;', 'opening curly quote' );
	return str_replace(
		array( ' Startbox', '&#8216;Startbox', $dblq . 'Startbox', '>Startbox', '(Startbox' ),
		array( ' StartBox', '&#8216;StartBox', $dblq . 'StartBox', '>StartBox', '(StartBox' ),
	$text );

}
add_filter( 'the_content', 'capital_B_dangit', 11 );
add_filter( 'the_title', 'capital_B_dangit', 11 );
add_filter( 'comment_text', 'capital_B_dangit', 31 );


/**
 * Introduces a new column to the 'Page' dashboard that will be used to render the page template
 * for the given page.
 *
 * Credit: @tommcfarlin, http://tommcfarlin.com/view-page-templates/
 *
 * @since	2.7
 * @param	array	$page_columns	The array of columns rendering page meta data./
 * @return	array					The update array of page columns.
 */
function sb_add_template_column( $page_columns ) {
	$page_columns['template'] = __( 'Page Template', 'startbox' );
	return $page_columns;
}
add_filter( 'manage_edit-page_columns', 'sb_add_template_column' );


/**
 * Renders the name of the template applied to the current page. Will use 'Default' if no
 * template is used, but will use the friendly name of the template if one is applied.
 *
 * Credit: @tommcfarlin, http://tommcfarlin.com/view-page-templates/
 *
 * @since	2.7
 * @param	string	$column_name	The name of the column being rendered
 */
function sb_add_template_data( $column_name ) {

	// Grab a reference to the post that's currently being rendered
	global $post;

	// If we're looking at our custom column, then let's get ready to render some information.
	if( 'template' == $column_name ) {

		// First, the get name of the template
		$template_name = get_post_meta( $post->ID, '_wp_page_template', true );

		// If the file name is empty or the template file doesn't exist (because, say, meta data is left from a previous theme)...
		if( 0 == strlen( trim( $template_name ) ) || ! file_exists( get_template_directory() . '/' . $template_name ) ) {

			// ...then we'll set it as default
			$template_name = __( 'Default', 'startbox' );

		// Otherwise, let's actually get the friendly name of the file rather than the name of the file itself
		// by using the WordPress `get_file_description` function
		} else {

			$template_name = get_file_description( get_template_directory() . '/' . $template_name );

		}

	}

	// Finally, render the template name
	echo $template_name;

}
add_action( 'manage_page_posts_custom_column', 'sb_add_template_data' );


if ( ! function_exists( 'sb_get_image_id' ) ) :
/**
 * Pull an attachment ID from a post, if one exists.
 *
 * @since    3.0.0
 * @global   WP_Post   $post      Post object.
 * @param    integer   $index     Optional. Index of which image to return from a post. Default is 0.
 * @return   integer   boolean    Returns image ID, or false if image with given index does not exist.
 */
function sbx_get_image_id( $index = 0 ) {

	global $post;

	$ids = array_keys(
		get_children(
			array(
				'post_parent'    => $post->ID,
				'post_type'	     => 'attachment',
				'post_mime_type' => 'image',
				'orderby'        => 'menu_order',
				'order'	         => 'ASC',
			)
		)
	);

	if ( isset( $ids[$index] ) )
		return $ids[$index];

	return false;

}
endif;


if ( ! function_exists( 'sbx_get_image' ) ) :
/**
 * Return an image pulled from the media gallery.
 *
 * Supported $args keys are:
 *
 *  - format   - string, default is 'html'
 *  - size     - string, default is 'full'
 *  - num      - integer, default is 0
 *  - attr     - string, default is ''
 *  - fallback - mixed, default is 'first-attached'
 *
 * @since    3.0.0
 * @uses     sb_get_image_id()  Pull an attachment ID from a post, if one exists.
 * @global   WP_Post  $post     Post object.
 * @param    array    string    $args Optional. Image query arguments. Default is empty array.
 * @return   string   boolean   Return image element HTML, URL of image, or false.
 */
function sbx_get_image( $args = array() ) {

	global $post;

	$defaults = apply_filters( 'sbx_get_image_default_args', array(
		'format'   => 'html',
		'size'     => 'full',
		'num'      => 0,
		'attr'     => '',
		'fallback' => 'first-attached'
	) );

	$args = wp_parse_args( $args, $defaults );

	// Check for post image
	if ( has_post_thumbnail() && ( 0 === $args['num'] ) ) {
		$id = get_post_thumbnail_id();
		$html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
		list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
	}

	// Else if first-attached, pull the first image attachment
	elseif ( 'first-attached' === $args['fallback'] ) {
		$id = sb_get_image_id( $args['num'] );
		$html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
		list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
	}

	// Else if fallback array exists
	elseif ( is_array( $args['fallback'] ) ) {
		$id   = 0;
		$html = $args['fallback']['html'];
		$url  = $args['fallback']['url'];
	}

	// Else, return false
	else {
		return false;
	}

	// Source path, relative to the root
	$src = str_replace( home_url(), '', $url );

	// Determine output
	if ( 'html' === mb_strtolower( $args['format'] ) )
		$output = $html;
	elseif ( 'url' === mb_strtolower( $args['format'] ) )
		$output = $url;
	else
		$output = $src;

	// Return false if $url is blank
	if ( empty( $url ) ) $output = false;

	// Return data, filtered
	return apply_filters( 'sbx_get_image', $output, $args, $id, $html, $url, $src );

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
endif;


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
endif;


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
 * Generate makrup for an Author Box
 *
 * @since  3.0.0
 *
 * @param  array  $args Parameters used for output.
 * @return string       Concatenated markup.
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

/**
 * Output an Author Box
 *
 * @since 3.0.0
 *
 * @param array $args Parameters used for output.
 */
function sbx_author_box( $args = array() ) {
	echo sbx_get_author_box( $args );
}

/**
 * Conditionally add the author box after single posts
 */
function sbx_do_author_box() {

	// Return early if not a post
	if ( ! is_single() )
		return;

	if ( get_theme_mod( 'sb_show_author_box' ) ) { sbx_author_box(); }

}
add_action( 'entry_after', 'sbx_do_author_box', 10 );

/**
 * Flush out the transients used in sbx_categorized_blog
 */
function sbx_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'sbx_category_transient_flusher' );
add_action( 'save_post',     'sbx_category_transient_flusher' );
