<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package StartBox
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/CreativeWork">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title" itemprop="headline">
					<?php sbx_page_title(); ?>
				</h1>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php do_action( 'entry_before' ); ?>
				<?php get_template_part( 'content', 'search' ); ?>
				<?php do_action( 'entry_after' ); ?>
			<?php endwhile; ?>

			<?php sbx_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<?php get_template_part( 'no-results', 'search' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
