<?php get_header() ?>

	<div id="container">
		<div id="content">

		<?php the_post() ?>
		
		<?php do_action( 'sb_before_content' );?>
			
			<div id="entry-author-info">
				<?php do_action( 'sb_page_title' ); ?>
				<?php if ( get_the_author_meta( 'description' ) ) : ?>
					<div id="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'sb_author_page_gravitar_size', 120 ) ); // Available Filter: sb_author_gravitar_size ?>
					</div><!-- #author-avatar -->
					<div id="author-description">
						<?php the_author_meta( 'description' ); ?>
					</div><!-- #author-description	-->
				<?php endif; ?>
			</div><!-- #entry-author-info -->
			
			<?php 
				rewind_posts();
				get_template_part( 'loop', 'author' ); 
			?>
			
		<?php do_action( 'sb_after_content' );?>
		
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>