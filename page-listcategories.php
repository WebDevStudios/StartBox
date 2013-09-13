<?php
/*
Template Name: List Child Categories
*/
?>
<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php if ( have_posts() ) the_post(); ?>

		<?php do_action( 'before_content' ); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( 'page_title' ); ?>
				<div class="entry-content">
					<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="entry-pages cb">' . __( 'Pages:', 'startbox' ), 'after' => '</div>' ) ); ?>

					<?php edit_post_link(__('Edit', 'startbox'),'<span class="edit-link">','</span>'); ?>

					<ul id="category-list">
						<?php
							$categoryid = ( $_GET['cat'] ) ? $_GET['cat'] : get_post_meta($post->ID, 'categoryid', true);
							$show_count	 = (get_post_meta($post->ID, 'show_count', true)) ? get_post_meta($post->ID, 'show_count', true) : 0;
							wp_list_categories('child_of='.$categoryid.'&show_count='.$show_count.'&title_li=');
						?>
					</ul>

				</div>
			</div><!-- .post -->

			<?php do_action( 'after_content' ); ?>

			<?php comments_template( '', true ); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>