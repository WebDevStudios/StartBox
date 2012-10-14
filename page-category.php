<?php
/*
Template Name: Display Posts
*/
?>
<?php get_header(); ?>

	<div id="container">
		<div id="content">

		<?php the_post(); ?>	
		
		<?php do_action( 'sb_before_content' ); ?>

		<?php do_action( 'sb_page_title' ); ?>
		<?php the_content(); ?>
		<?php edit_post_link( __('Edit', 'startbox'), '<span class="edit-link">', '</span>' ); ?>

			<?php
				$temp_post = $post;
				$temp_query = $wp_query;
				$loop = ( $loop = get_post_meta($post->ID, 'loop', true) ) ? $loop : get_post_format();
				$post_type = ( $post_type = get_post_meta($post->ID, 'post_type', true) ) ? $post_type : 'post';
				$categoryid = ( isset( $_GET['cat'] ) ) ? $_GET['cat'] : get_post_meta($post->ID, 'categoryid', true);
				$posts_per_page = ($posts_per_page = get_post_meta($post->ID, 'posts_per_page', true) ) ? $posts_per_page : 10;
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				
				$wp_query = new WP_query( array(
					'post_type' => $post_type,
					'cat' => $categoryid,
					'posts_per_page' => $posts_per_page,
					'paged' => $paged
					) );
					
				while ( have_posts() ) : the_post();
					get_template_part( 'loop', $loop );
				endwhile;
				
				$wp_query = $temp_query;
				$post = $temp_post;
			?>
			
		<?php do_action( 'sb_after_content' ); ?>
		
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>