<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to sbx_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @package sbx
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>
	<div id="comments" class="comments-area">
	<?php do_action( 'comments_before' ); ?>
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'startbox' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'sbx' ); ?></h1>
			<div class="nav-previous" itemprop="name"><?php previous_comments_link( __( '&larr; Older Comments', 'startbox' ) ); ?></div>
			<div class="nav-next" itemprop="name"><?php next_comments_link( __( 'Newer Comments &rarr;', 'startbox' ) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>
		<ol class="comment-list">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use sbx_comment() to format the comments.
				 * If you want to override this in a child theme then you can
				 * define sbx_comment() and that will be used instead.
				 * See sbx_comment() in inc/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => 'sbx_comment', 'avatar_size' => '64' ) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="comment-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'startbox' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'startbox' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'startbox' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'startbox' ); ?></p>
	<?php endif; ?>
	<?php comment_form(); ?>
	<?php do_action( 'comments_after' ); ?>
</div><!-- #comments -->
