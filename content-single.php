<?php
/**
 * The template used for displaying post content
 *
 * @package sbx
 */
?>

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
		<?php sbx_entry_meta(); ?>
		<?php edit_post_link( __( 'Edit', 'sbx' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
