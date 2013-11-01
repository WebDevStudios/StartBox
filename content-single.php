<?php
/**
 * The template used for displaying post content
 *
 * @package sbx
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">
	<?php do_action( 'entry_top' ); ?>
	<header class="entry-header">
		<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<?php sb_get_theme_mod( 'sb_post_header_meta' ); ?>
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
		<?php sb_get_theme_mod( 'sb_post_footer_meta' ); ?>
		<?php edit_post_link( __( 'Edit', 'sbx' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
	<?php do_action( 'entry_bottom' ); ?>
</article><!-- #post-## -->