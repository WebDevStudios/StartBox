		<?php do_action( 'sb_after_container' ); ?>
	</div><!-- #container_wrap .hfeed -->
</div><!-- #wrap .hfeed -->
<?php do_action( 'sb_between_content_and_footer' ); ?>
<div id="footer_wrap">
	
	<?php do_action( 'sb_before_footer' ); ?>
	
	<div id="footer">
		
		<?php
			do_action( 'sb_footer_widgets' );
			do_action( 'sb_footer' );
			if ( has_action( 'wp_footer' ) ) { 
				echo '<div id="wp_footer">';
				wp_footer();
				echo '</div><!-- #wp_footer -->';
			}
		?>
		
	</div><!-- #footer -->
	
	<?php do_action( 'sb_after_footer' ); ?>
	
</div><!-- #footer_wrap .hfeed -->

<?php do_action( 'sb_after' ); ?>
	
</body>
</html>