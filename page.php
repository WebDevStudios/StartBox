<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php the_post(); ?>

		<?php do_action( 'sb_before_content' ); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( 'sb_page_title' ); ?>
				<div class="entry-content">

					<?php
						the_content();
						wp_link_pages( array( 'before' => '<div class="entry-pages cb">' . __( 'Pages:', 'startbox' ), 'after' => '</div>' ) );
						edit_post_link(__('Edit', 'startbox'),'<span class="edit-link">','</span>');
					?>

				</div><!-- .entry-content -->
			</div><!-- .post -->

		<?php do_action( 'sb_after_content' ); ?>

		<?php comments_template( '', true ); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>