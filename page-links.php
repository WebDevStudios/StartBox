<?php
/*
Template Name: Links Page
*/
?>
<?php get_header(); ?>
	
	<div id="container">
		<div id="content">

		<?php the_post(); ?>
		<?php
			$toc = ( get_post_meta($post->ID, 'links_toc', true) ) ?  get_post_meta($post->ID, 'links_toc', true) : false;
			$rtt = ( $toc ) ? '<li><a class="rtt" href="#top">Return to Top</a></li><hr/>' : '' ;
			$category = get_post_meta($post->ID, 'links_categoryid', true)
		?>
		<?php do_action( 'sb_before_content' ); ?>
		
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( 'sb_page_title' ); ?>
				<div class="entry-content">
					<?php the_content() ?>
					<?php if ($toc) { ?>
					<ul id="links-toc" class="xoxo">
						<li><h3>Contents</h3></li>
						<?php
							$args = array(
						    'orderby'          => 'name',
						    'order'            => 'ASC',
						    'limit'            => 1,
						    'category'         => '',
						    'exclude_category' => '',
						    'category_name'    => '',
						    'hide_invisible'   => 1,
						    'show_updated'     => 0,
							'show_description' => 0,
							'show_images'	   => 0,
							'show_name'		   => 0,
						    'echo'             => 1,
						    'categorize'       => 1,
						    'title_li'         => '',
						    'title_before'     => '',
						    'title_after'      => '',
						    'category_orderby' => 'name',
						    'category_order'   => 'ASC',
						    'class'            => 'linkcat',
						    'category_before'  => '<li class="%class"><a href="#%id">',
						    'category_after'   => '</a></li>',
							'before'		   => '<li>',
							'after'			   => '</li>',
							'between'		   => '');
							wp_list_bookmarks($args);
						?>
					</ul>
					<?php } ?>
					<ul id="links" class="xoxo">
						<?php
							$args = array(
						    'orderby'          => 'name',
						    'order'            => 'ASC',
						    'limit'            => -1,
						    'category'         => $category,
						    'exclude_category' => '',
						    'category_name'    => '',
						    'hide_invisible'   => 1,
						    'show_updated'     => 0,
							'show_description' => 1,
							'show_images'	   => 1,
							'show_name'		   => 1,
						    'echo'             => 1,
						    'categorize'       => 1,
						    'title_li'         => '',
						    'title_before'     => '<h3>',
						    'title_after'      => '</h3>',
						    'category_orderby' => 'name',
						    'category_order'   => 'ASC',
						    'class'            => 'linkcat',
						    'category_before'  => '<li id="%id" class="%class">',
						    'category_after'   => '</li>'.$rtt,
							'before'		   => '<li>',
							'after'			   => '</p></li>',
							'between'		   => '<p>');
							wp_list_bookmarks($args);
						?>
					</ul>
					
					<?php edit_post_link( __('Edit', 'startbox'),'<span class="edit-link">','</span>'); ?>

				</div>
			</div><!-- .post -->

			<?php do_action( 'sb_after_content' ); ?>
			
			<?php comments_template( '', true ); ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>