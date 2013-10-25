<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package sbx
 */
?>
<?php do_action( 'before_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
	<header class="entry-header">
		<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content" itemprop="text">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'sbx' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php edit_post_link( __( 'Edit', 'sbx' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
</article><!-- #post-## -->
<?php do_action( 'after_post' ); ?>