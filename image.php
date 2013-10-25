<?php
/**
 * The template for displaying image attachments.
 *
 * @package sbx
 */

get_header();
?>

	<div id="primary" class="content-area image-attachment">
		<main id="main" class="site-main col span-12" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/CreativeWork">

		<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'entry_before' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( 'entry_top' ); ?>
				<header class="entry-header">
					<h1 class="entry-title" itemprop="headline"><?php _e( 'Attachment:', 'sbx' ); ?> <?php the_title(); ?></h1>


					<div class="entry-meta">
						<?php
							$metadata = wp_get_attachment_metadata();
							printf( __( 'Published <span class="entry-date"><time class="entry-date published updated" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> by <span class="vcard author"><span class="fn">%6$s</span></span> in <a href="%7$s" title="Return to %8$s" rel="gallery">%9$s</a>', 'sbx' ),
								esc_attr( get_the_date( 'c' ) ),
								esc_html( get_the_date() ),
								esc_url( wp_get_attachment_url() ),
								$metadata['width'],
								$metadata['height'],
								esc_attr( get_the_author() ),
								esc_url( get_permalink( $post->post_parent ) ),
								esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
								get_the_title( $post->post_parent )
							);

							edit_post_link( __( 'Edit', 'sbx' ), ' <span class="edit-link">', '</span>' );
						?>
					</div><!-- .entry-meta -->

					<nav id="image-navigation" class="image-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<div class="nav-previous"><?php previous_image_link( false, __( '<span class="meta-nav" itemprop="name">&larr;</span> Previous', 'sbx' ) ); ?></div>
						<div class="nav-next"><?php next_image_link( false, __( 'Next <span class="meta-nav" itemprop="name">&rarr;</span>', 'sbx' ) ); ?></div>
					</nav><!-- #image-navigation -->
				</header><!-- .entry-header -->

				<div class="entry-content">
					<div class="entry-attachment">
						<div class="attachment" itemprop="associatedMedia">
							<?php sbx_the_attached_image(); ?>
						</div><!-- .attachment -->

						<?php if ( has_excerpt() ) : ?>
						<div class="entry-caption" itemprop="text">
							<?php the_excerpt(); ?>
						</div><!-- .entry-caption -->
						<?php endif; ?>
					</div><!-- .entry-attachment -->

					<?php
						the_content();
						wp_link_pages( array(
							'before' => '<div class="page-links">' . __( 'Pages:', 'sbx' ),
							'after'  => '</div>',
						) );
					?>
				</div><!-- .entry-content -->

				<footer class="entry-meta">
					<?php
						if ( comments_open() && pings_open() ) : // Comments and trackbacks open
							printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'sbx' ), esc_url( get_trackback_url() ) );
						elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open
							printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'sbx' ), esc_url( get_trackback_url() ) );
						elseif ( comments_open() && ! pings_open() ) : // Only comments open
							 _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'sbx' );
						elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed
							_e( 'Both comments and trackbacks are currently closed.', 'sbx' );
						endif;

						edit_post_link( __( 'Edit', 'sbx' ), ' <span class="edit-link">', '</span>' );
					?>
				</footer><!-- .entry-meta -->
			</article><!-- #post-## -->
			<?php do_action( 'entry_bottom' ); ?>
			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>
		<?php do_action( 'entry_after' ); ?>
		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>