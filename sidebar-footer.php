<?php sb_before_footer_widgets(); ?>
<?php 
	/**
	 * Footer Aside Widget Areas
	 *
	 * Each StartBox-based theme can have up to 4 aside columns in the footer.
	 * The logic below determines which to include and what the proper widths
	 * should be. When adding widgets, always start with Footer Column and 
	 * advance numerically to Footer Column 4. Skipping a column breaks the logic.
	 *
	 * @since StartBox 2.4.2
	 */
	$footer1 = ( is_sidebar_active('footer_widget_area_1') || has_action('sb_no_footer_aside_widgets') );
	$footer2 = ( is_sidebar_active('footer_widget_area_2') || has_action('sb_no_footer_aside_2_widgets') );
	$footer3 = ( is_sidebar_active('footer_widget_area_3') || has_action('sb_no_footer_aside_3_widgets') );
	$footer4 = ( is_sidebar_active('footer_widget_area_4') || has_action('sb_no_footer_aside_4_widgets') );
	
	if ( $footer1 || $footer2 || $footer3 || $footer4 ) { 
	
	$column = $column1 = $column2 = $column3 = $column4 = null;
	
	if ( $footer1 && $footer3 && $footer3 && $footer4 ) { $column = 'column one_fourth'; $column4 = ' last'; }
	elseif ( $footer1 && $footer2 && $footer3 ) { $column = 'column one_third'; $column3 = ' last'; }
	elseif ( $footer1 && $footer2 ) { $column = 'column one_half'; $column2 = ' last'; }
	else { $column = 'column last'; }
	
?>
	<div id="footer_sidebar">
		<?php 
			sb_do_sidebar( 'footer_widget_area_1', 'footer_widget_area_1', $column );
			sb_do_sidebar( 'footer_widget_area_2', 'footer_widget_area_2', $column . $column2 );
			sb_do_sidebar( 'footer_widget_area_3', 'footer_widget_area_3', $column . $column3 );
			sb_do_sidebar( 'footer_widget_area_4', 'footer_widget_area_4', $column . $column4 );
		?>
	</div><!-- #footer_sidebar -->
	
<?php } ?>

<?php sb_after_footer_widgets(); ?>