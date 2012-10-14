<?php get_header(); ?>

	<div id="container">
		<div id="content">

			<?php
				do_action( 'sb_before_content' );
				do_action( 'sb_before_featured' );
				do_action( 'sb_featured' );
				do_action( 'sb_after_featured' );
				do_action( 'sb_home' );
				do_action( 'sb_after_content' );
			?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>