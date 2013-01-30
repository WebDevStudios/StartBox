<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php the_post(); ?>

		<?php do_action( 'sb_before_content' ); ?>

			<div id="entry-author-info">
				<?php sb_page_title(); ?>
				<?php if ( get_the_author_meta( 'description' ) ) : ?>
					<div id="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'sb_author_page_gravitar_size', 120 ) ); ?>
					</div><!-- #author-avatar -->
					<div id="author-description">
						<?php the_author_meta( 'description' ); ?>
					</div><!-- #author-description	-->
				<?php endif; ?>
			</div><!-- #entry-author-info -->

			<?php
				// Provide a hook for placing content before author posts
				do_action( 'sb_author_before_posts' );

				// Grab the author's posts
				rewind_posts();
				while ( have_posts() ) : the_post();
					get_template_part( 'loop', 'author' );
				endwhile;

				// Standard "after content" hook
				do_action( 'sb_after_content' );
			?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>