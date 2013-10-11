<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package sbx
 */
?>
		</div><!-- .wrap -->
	</div><!-- #content -->
	<div class="footer-widgets">
		<div class="wrap">
			<div class="footer-widgets-1 widget-area col span-4">
				<?php if ( ! dynamic_sidebar( 'footer_widget_area_1' ) ) { ?>
				<?php } ?>
			</div>

			<div class="footer-widgets-2 widget-area col span-4">
				<?php if ( ! dynamic_sidebar( 'footer_widget_area_2' ) ) { ?>
				<?php } ?>
			</div>

			<div class="footer-widgets-3 widget-area col span-4">
				<?php if ( ! dynamic_sidebar( 'footer_widget_area_3' ) ) { ?>
				<?php } ?>
			</div>
		</div>
	</div><!-- .footer-widgets -->
	<footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
		<div class="site-info clear">
			<?php do_action( 'sbx_credits' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>