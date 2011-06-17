<?php
/*
Template Name: Full-Width
*/
?>
<?php get_header() ?>

	<div id="container">
		<div id="content">

		<?php the_post() ?>
		
		<?php sb_before_content();?>
		
			<div id="post-<?php the_ID(); ?>" <?php post_class() ?>>
				<?php sb_page_title(); ?>
				<div class="entry-content">
					<?php the_content() ?>

					<?php wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'startbox'), "</div>\n", 'number'); ?>

					<?php edit_post_link(__('Edit', 'startbox'),'<span class="edit-link">','</span>') ?>
				</div>
			</div><!-- .post -->

			<?php sb_after_content();?>
			
			<?php comments_template( '', true ); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer() ?>