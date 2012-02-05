<?php do_action( 'sb_before_post' ); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf(__('Permalink to %s', 'startbox'), esc_html(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<div class="entry-meta">
			<?php do_action( 'sb_post_header' ); ?>
		</div><!-- .entry-meta -->
	</div><!-- .entry-header -->
	
	<?php do_action( 'sb_before_post_content' ); ?>
	
	<div class="entry-content">
		<a class="entry-photo" href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo esc_attr( get_the_title() ); ?>">
			<?php sb_post_image(); ?>
		</a>
		
		<?php
			if ( 
				( ( is_home() || is_front_page() ) && sb_get_option( 'home_post_content' ) == 'full' ) OR 		// If we're on the homepage and should display full content
				( ( !is_home() || !is_front_page() ) && sb_get_option( 'archive_post_content' ) == 'full' ) ) {	// Or, If were on an archive page and should display full content
					the_content( apply_filters( "sb_read_more", sprintf( __("Continue Reading: %s &rarr;", "startbox"), get_the_title() ) ) ); 				// Display the full content using a filterable read-more tag when necessary
				}
			else { // Otherwise, display the excerpt with a fliterable read-more tag
				the_excerpt(); echo '<a href="' . get_permalink() . '" title="' . sprintf(__("Continue Reading: %s", "startbox"), esc_html(get_the_title(), 1)) . '" class="more-link">' . do_shortcode( apply_filters( "sb_read_more", sprintf( __("Continue Reading: %s &rarr;", "startbox"), get_the_title() ) ) ) . '</a>'; 
			}
		?>
		
	</div><!-- .entry-content -->
	
	<?php do_action( 'sb_after_post_content' ); ?>
	
	<div class="entry-footer">
		<?php do_action( 'sb_post_footer' ); ?>
	</div><!-- .entry-footer -->
</div><!-- .post -->

<?php do_action( 'sb_after_post' ); ?>