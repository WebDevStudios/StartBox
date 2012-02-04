<?php get_header() ?>

	<div id="container">
		<div id="content">
		
		<?php do_action( 'sb_before_content' );?>
		
		<?php if (have_posts()) : ?>

		<?php do_action( 'sb_page_title' ); ?>

			<?php while ( have_posts() ) : the_post() ?>
				<?php get_template_part( 'loop', 'search' ); ?>
			<?php endwhile ?>

			<?php else : ?>

			<div id="post-0" class="post noresults">
				<h2 class="entry-title"><?php _e('Nothing Found', 'startbox') ?></h2>
				<div class="entry-content">
					<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'startbox') ?></p>
				</div>
				
				<?php get_template_part( 'searchform' ); ?>
				
			</div><!-- .post -->

		<?php endif; ?>
		
		<?php do_action( 'sb_after_content' );?>
		
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>