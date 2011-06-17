<?php get_header(); ?>

	<div id="container">
		<div id="content">
			
			<?php sb_before_content(); ?>
			<?php sb_before_featured(); ?>
			<?php sb_featured(); ?>
			<?php sb_after_featured(); ?>
			<?php sb_home(); ?>
			<?php sb_after_content();?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>