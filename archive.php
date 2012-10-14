<?php get_header(); ?>

	<div id="container">
		<div id="content">

			<?php
				do_action( 'sb_before_content' );
				the_post();
				do_action( 'sb_page_title' );
				rewind_posts();

				while ( have_posts() ) : the_post();

					get_template_part( 'loop', 'archive' );

				endwhile;

				do_action( 'sb_after_content' );
			?>

		</div><!-- #content .hfeed -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>