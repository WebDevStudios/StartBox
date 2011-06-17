<?php get_header() ?>

	<div id="container">
		<div id="content">
			
			<?php sb_before_content();?>

			<?php the_post() ?>
			
			<?php sb_page_title(); ?>
			
			<?php rewind_posts() ?>

			<?php while ( have_posts() ) : the_post() ?>
				<?php get_template_part( 'loop', 'archive' ); ?>
			<?php endwhile ?>
			
			<?php sb_after_content();?>

		</div><!-- #content .hfeed -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>