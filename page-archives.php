<?php
/*
Template Name: Archives Page
*/
?>
<?php get_header(); ?>
	
	<div id="container">
		<div id="content">

		<?php the_post(); ?>
		
		<?php do_action( 'sb_before_content' ); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( 'sb_page_title' ); ?>
				<div class="entry-content">
					<?php the_content(); ?>

					<ul id="archives-page" class="xoxo">
						<li id="category-archives">
							<h3><?php _e('Archives by Category', 'startbox'); ?></h3>
							<ul>
								<?php wp_list_categories('optioncount=1&feed=RSS&title_li=&show_count=1'); ?> 
							</ul>
						</li>
						<li id="monthly-archives">
							<h3><?php _e('Archives by Month', 'startbox'); ?></h3>
							<ul>
								<?php wp_get_archives('type=monthly&show_post_count=1'); ?>
							</ul>
						</li>
						<li id="author-archives">
							<h3><?php _e('Archives by Author', 'startbox'); ?></h3>
							<ul>
								<?php wp_list_authors('optioncount=1'); ?>
							</ul>
						</li>
					</ul>
					<?php edit_post_link( __( 'Edit', 'startbox' ), '<span class="edit-link">', '</span>' ); ?>

				</div>
			</div><!-- .post -->
			
			<?php do_action( 'sb_after_content' ); ?>
			
			<?php comments_template( '', true ); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>