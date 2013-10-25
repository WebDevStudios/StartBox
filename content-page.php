<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package sbx
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
	<?php do_action( 'entry_top' ); ?>
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
<?php do_action( 'entry_bottom' ); ?>
</article><!-- #post-## -->