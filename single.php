<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php if ( have_posts() ) the_post(); ?>

			<?php do_action( 'before_content' ); ?>

			<div id="post-<?php the_ID() ?>" <?php post_class(); ?>>
				<?php do_action( 'page_title' ); ?>
				<div class="entry-meta">
					<?php do_action( 'post_header' ); ?>
				</div>

				<?php do_action( 'before_post_content' ); ?>

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="entry-pages cb">' . __( 'Pages:', 'startbox' ), 'after' => '</div>' ) ); ?>
				</div>

				<?php do_action( 'after_post_content' ); ?>

				<div class="entry-footer">
					<?php do_action( 'post_footer' ); ?>
				</div>
			</div><!-- .post -->

			<?php do_action( 'after_content' ); ?>

			<?php comments_template('', true); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>