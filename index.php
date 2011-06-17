<?php get_header() ?>

	<div id="container">
		<div id="content">

			<?php sb_before_content();?>
				
			<?php while ( have_posts() ) : the_post() ?>
				<?php get_template_part( 'loop', 'index' ); ?>
			<?php endwhile ?>

			<?php sb_after_content();?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>