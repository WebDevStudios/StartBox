<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package sbx
 */
?>
		<?php do_action( 'content_bottom' ); ?>
		</div><!-- .wrap -->
	</div><!-- #content -->
	<?php do_action( 'content_after' ); ?>
	<div class="footer-widgets">
		<div class="wrap">
			<?php sb_do_sidebar( 'footer_widget_area_1', 'footer-widget-area-1 col span-4' ); ?>
			<?php sb_do_sidebar( 'footer_widget_area_2', 'footer-widget-area-1 col span-4' ); ?>
			<?php sb_do_sidebar( 'footer_widget_area_3', 'footer-widget-area-1 col span-4' ); ?>
		</div>
	</div><!-- .footer-widgets -->
	<?php do_action( 'footer_before' ); ?>
	<footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
		<div class="wrap">
			<?php do_action( 'footer_top' ); ?>
			<div class="site-info">
				<?php

				/**
				 * THIS IS SUPER SLOPPY. These will be moved.
				 */

				// Credits
				echo '<span class="credits">' . do_shortcode( sb_get_theme_mod( 'sb_credits' ) ) . '</span>';

				// Return To Top Link
				if ( sb_get_theme_mod( 'sb_rtt_link' ) ) {
					echo sb_rtt();
				} 

				?>
			</div><!-- .site-info -->
			<?php do_action( 'footer_bottom' ); ?>
		</div><!-- .wrap -->
	</footer><!-- #colophon -->
	<?php do_action( 'footer_after' ); ?>
</div><!-- #page -->
<?php do_action( 'body_bottom' ); ?>
<?php wp_footer(); ?>
</body>
</html>