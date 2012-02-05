<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php the_post(); ?>
			
			<?php do_action( 'sb_before_content' ); ?>
			
			<div id="post-<?php the_ID() ?>" <?php post_class(); ?>>
				<?php do_action( 'sb_page_title' ); ?>
				<div class="entry-meta">
					<?php do_action( 'sb_post_header' ); ?>
				</div>
				
				<?php do_action( 'sb_before_post_content' ); ?>
				
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				
				<?php do_action( 'sb_after_post_content' ); ?>
				
				<div class="entry-footer">
					<?php do_action( 'sb_post_footer' ); ?>
				</div>
			</div><!-- .post -->
			
			<?php do_action( 'sb_after_content' ); ?>
			
			<?php comments_template('', true); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>