<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package sbx
 */
?>
		<?php do_action( 'after_wrap' ); ?>
		</div><!-- .wrap -->
		<?php do_action( 'after_content' ); ?>
	</div><!-- #content -->
	<?php do_action( 'before_footer_widgets_area' ); ?>
	<div class="footer-widgets">
		<?php do_action( 'before_footer_widgets' ); ?>
		<div class="wrap">
			<?php get_sidebar( 'footer' ); ?>
		</div>
		<?php do_action( 'after_footer_widgets' ); ?>
	</div><!-- .footer-widgets -->
	<?php do_action( 'before_footer_area' ); ?>
	<footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
		<div class="site-info clear">
			<?php do_action( 'before_credits' ); ?>
			<?php do_action( 'sbx_credits' ); ?>
			<?php do_action( 'after_credits' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
	<?php do_action( 'after_footer_area' ); ?>
</div><!-- #page -->
<?php do_action( 'after_page' ); ?>
<?php wp_footer(); ?>
</body>
<?php do_action( 'after_body' ); ?>
</html>
<?php do_action( 'after_html' ); ?>