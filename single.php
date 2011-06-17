<?php get_header() ?>

	<div id="container">
		<div id="content">

		<?php the_post() ?>
			
			<?php sb_before_content();?>
			
			<div id="post-<?php the_ID() ?>" <?php post_class() ?>>
				<?php sb_page_title(); ?>
				<div class="entry-meta">
					<?php sb_post_header(); ?>
				</div>
				
				<?php sb_before_post_content(); ?>
				
				<div class="entry-content">
					<?php the_content(''.__('Read More <span class="meta-nav">&raquo;</span>', 'startbox').''); ?>
				</div>
				
				<?php sb_after_post_content(); ?>
				
				<div class="entry-footer">
					<?php sb_post_footer(); ?>
				</div>
			</div><!-- .post -->
			
			<?php sb_after_content();?>
			
			<?php comments_template('', true); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>