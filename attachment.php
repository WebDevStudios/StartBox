<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		
			<?php do_action( 'sb_before_content' ); ?>

			<?php do_action( 'sb_page_title' ); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title"><?php the_title(); ?></h2>
				<div class="entry-meta">
					<?php do_action( 'sb_post_header' ); ?>
					<?php
						if ( wp_attachment_is_image() ) {
							echo ' <span class="meta-sep">|</span> ';
							$metadata = wp_get_attachment_metadata();
							printf( __( 'Full size is %s pixels', 'startbox'),
								sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
									wp_get_attachment_url(),
									esc_attr( __('Link to full-size image', 'startbox') ),
									$metadata['width'],
									$metadata['height']
								)
							);
						}
					?>
				</div>
				<div class="entry-content">
					<div class="entry-attachment">
					
					<?php 
						if ( wp_attachment_is_image() ) {
							$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
							foreach ( $attachments as $k => $attachment ) {
								if ( $attachment->ID == $post->ID )
									break;
							}
							$k++;
							if ( isset( $attachments[ $k ] ) ) {
								$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
							} else { 
								$next_attachment_url = get_permalink( $post->post_parent );
							}
						?>
					
						<p class="attachment">
							<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
							<?php
								$attachment_size = apply_filters( 'sb_attachment_size', 900 );
								echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
							?>
							</a>
						</p>

						<div id="nav-below" class="navigation">
							<div class="nav-previous"><?php previous_image_link( false ); ?></div>
							<div class="nav-next"><?php next_image_link( false ); ?></div>
						</div><!-- #nav-below -->
				
						<?php } else { ?>
						
							<a href="<?php echo esc_url( wp_get_attachment_url() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
					
						<?php } ?>
					
					</div><!-- .entry-attachment -->
				
					<div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>

					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'startbox' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'startbox' ), 'after' => '</div>' ) ); ?>

				</div><!-- .entry-content -->

			</div><!-- .post -->

		<?php do_action( 'sb_after_content' ); ?>
			
		<?php comments_template(); ?>
			
		<?php endwhile; endif; ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>