<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package StartBox
 */
?>
		<?php do_action( 'content_bottom' ); ?>
		</div><!-- .wrap -->
	</div><!-- #content -->
	<?php do_action( 'content_after' ); ?>
	<div class="footer-widgets">
		<div class="wrap">
			<?php sbx_do_sidebar( 'footer_widget_area_1', 'footer-widget-area' ); ?>
			<?php sbx_do_sidebar( 'footer_widget_area_2', 'footer-widget-area' ); ?>
			<?php sbx_do_sidebar( 'footer_widget_area_3', 'footer-widget-area' ); ?>
		</div>
	</div><!-- .footer-widgets -->
	<?php do_action( 'footer_before' ); ?>
	<footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
		<div class="wrap">
			<?php do_action( 'footer_top' ); ?>
			<div class="site-info">
				<p class="site-credits"><?php echo sbx_get_theme_mod( 'sb_credits' ); ?></p>
				<?php $rtt = sbx_get_theme_mod( 'sb_rtt_link' ); if ( $rtt ) { echo sbx_rtt(); } ?>
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
