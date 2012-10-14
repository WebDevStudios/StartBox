<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php the_post(); ?>
		
		<?php do_action( 'sb_before_content' );?>
		
			<div id="post-<?php the_ID(); ?>">
				<?php do_action( 'sb_page_title' ); ?>
				<div class="entry-content">
					
					<?php do_action( 'sb_404' ); ?>
					
				</div><!-- .entry-content -->
			</div><!-- .post -->
			
		<?php do_action( 'sb_after_content' );?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>