<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package sbx
 */

get_header(); ?>

<?php do_action( 'before_content' ); ?>
	<div id="primary" class="content-area">
		<?php do_action( 'before_main' ); ?>
		<main id="main" class="site-main col span_8" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
		
		<?php do_action( 'before_entry' ); ?>
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php do_action( 'after_while' ); ?>
				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>
				<?php do_action( 'before_endwhile' ); ?>
			<?php endwhile; ?>
			<?php do_action( 'before_post_nav' ); ?>
			<?php sbx_content_nav( 'nav-below' ); ?>
			<?php do_action( 'after_post_nav' ); ?>

		<?php else : ?>

			<?php get_template_part( 'no-results', 'index' ); ?>

		<?php endif; ?>
		<?php do_action( 'after_entry' ); ?>

		</main><!-- #main -->
		<?php do_action( 'after_main' ); ?>
	</div><!-- #primary -->
<?php do_action( 'after_content' ); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>