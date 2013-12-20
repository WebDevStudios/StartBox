<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package sbx
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main col span-12" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/CreativeWork">
			<?php do_action( 'entry_before' ); ?>
			<section class="error-404 not-found">
				<?php do_action( 'entry_top' ); ?>
				<header class="page-header">
					<h1 class="page-title" itemprop="headline">
						<?php sbx_page_title( __( 'Oops! That page can&rsquo;t be found.', 'startbox' ) ); ?>
					</h1>
				</header><!-- .page-header -->

				<div class="page-content" itemprop="text">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'startbox' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

					<?php if ( sbx_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
					<div class="widget widget_categories">
						<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'startbox' ); ?></h2>
						<ul>
						<?php
							wp_list_categories( array(
								'orderby'    => 'count',
								'order'      => 'DESC',
								'show_count' => 1,
								'title_li'   => '',
								'number'     => 10,
							) );
						?>
						</ul>
					</div><!-- .widget -->
					<?php endif; ?>

					<?php
					/* translators: %1$s: smiley */
					$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'startbox' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
					?>

					<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

				</div><!-- .page-content -->
				<?php do_action( 'entry_bottom' ); ?>
			</section><!-- .error-404 -->
			<?php do_action( 'entry_after' ); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
