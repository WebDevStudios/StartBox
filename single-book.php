<?php
/**
 * The Template for displaying single custom post type entries.
 *
 * CHANGE THE NAME OF THIS FILE TO MATCH YOUR CPT
 *
 * @package sbx
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main col span_8" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

		<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">
			<header class="entry-header">
				<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>

				<div class="entry-meta">
					<?php sbx_posted_on(); ?>
				</div><!-- .entry-meta -->
			</header><!-- .entry-header -->

			<div class="entry-content" itemprop="text">
				<?php the_content(); ?>
				<?php
					wp_link_pages( array(
						'before' => '<div class="page-links" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">' . __( 'Pages:', 'sbx' ),
						'after'  => '</div>',
					) );
				?>
			</div><!-- .entry-content -->

			<footer class="entry-meta">
				<?php 		
					// Get post type
					$cpt = get_post_type( get_the_ID() );

					// Get terms based on Taxonomy slug. Seperate them with a comma
					$term_list = get_the_term_list( $post->ID, 'book_tags', '', ', ', '' );

					// If there are terms
					if ( '' != $term_list ) {
						$meta_text = __( 'This entry was posted in <a href="%1$s">%2$s</a> and tagged with %3$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'sbx' );

					// If there are no terms
					} else {
						$meta_text = __( 'This entry was posted in <a href="%1$s">%2$s</a>. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'sbx' );
					}

					printf(
						$meta_text,
						get_post_type_archive_link( $cpt ),
						ucwords( $cpt ),
						$term_list,
						get_permalink(),
						the_title_attribute( 'echo=0' )
					);
				?>
				<?php edit_post_link( __( 'Edit', 'sbx' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-meta -->
		</article><!-- #post-## -->


			<?php sbx_content_nav( 'nav-below' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>