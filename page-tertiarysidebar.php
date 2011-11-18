<?php
/*
Template Name: Tertiary Sidebar
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

<?php
	$sidebar = 'tertiary_widget_area';
	$location = 'tertiary';
	if ( is_sidebar_active($sidebar) || has_action("sb_no_{$sidebar}_widgets") ) { ?>

		<?php do_action( "sb_before_{$location}_widgets" ); ?>
		<div id="<?php echo esc_attr( $location ); ?>" class="aside <?php echo $location; ?>-aside<?php if ($classes) { echo ' ' . $classes; }?>">
			<ul class="xoxo">
				<?php if ( !dynamic_sidebar($sidebar) ) { do_action( "sb_no_{$sidebar}_widgets"); }?>

			</ul>
	   </div><!-- #<?php echo $location; ?> .aside-<?php echo $location; ?> .aside -->
	   <?php do_action( "sb_after_{$location}_widgets" ); ?>
	
<?php } ?>
<?php get_footer() ?>