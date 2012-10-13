<?php get_header(); ?>

	<div id="container">
		<div id="content">

			<?php
				do_action( 'sb_before_content' );

				while ( have_posts() ) : the_post();

					get_template_part( 'loop', 'index' );

				endwhile;

				do_action( 'sb_after_content' );
			?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>