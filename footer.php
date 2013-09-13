		<?php do_action( 'after_container' ); ?>
	</div><!-- #container_wrap .hfeed -->
</div><!-- #wrap .hfeed -->
<?php do_action( 'between_content_and_footer' ); ?>
<div id="footer_wrap">

	<?php do_action( 'before_footer' ); ?>

	<div id="footer">

		<?php
			do_action( 'footer_widgets' );
			do_action( 'footer' );
			if ( has_action( 'wp_footer' ) ) {
				echo '<div id="wp_footer">';
				wp_footer();
				echo '</div><!-- #wp_footer -->';
			}
		?>

	</div><!-- #footer -->

	<?php do_action( 'after_footer' ); ?>

</div><!-- #footer_wrap .hfeed -->

<?php do_action( 'after' ); ?>

</body>
</html>