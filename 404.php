<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php do_action( 'before_content' );?>

			<div id="post-<?php the_ID(); ?>">
				<?php do_action( 'page_title' ); ?>
				<div class="entry-content">

					<?php do_action( '404' ); ?>

				</div><!-- .entry-content -->
			</div><!-- .post -->

		<?php do_action( 'after_content' );?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>