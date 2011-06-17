		<?php sb_after_container(); ?>
	</div><!-- #container_wrap .hfeed -->
</div><!-- #wrap .hfeed -->
<?php sb_between_content_and_footer(); ?>
<div id="footer_wrap">
	
	<?php sb_before_footer(); ?>
	
	<div id="footer">
		
		<?php get_sidebar('footer') ?>
		
		<?php sb_footer(); ?>
		
		<?php if ( has_action( 'wp_footer' ) ) { ?>
			<div id="wp_footer">
				<?php wp_footer() ?>
			</div><!-- #wp_footer -->
		<?php } ?>
		
	</div><!-- #footer -->
	
	<?php sb_after_footer(); ?>
	
</div><!-- #footer_wrap .hfeed -->

<?php sb_after(); ?>
	
</body>
</html>