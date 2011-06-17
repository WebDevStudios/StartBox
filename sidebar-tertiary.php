<?php sb_before_tertiary_widgets(); ?>

<?php if (is_sidebar_active('tertiary_widget_area') || has_action( 'sb_no_tertiary_widgets' ) ) { ?>
	<div id="tertiary" class="aside tertiary-aside">
		<ul class="xoxo">
			<?php if ( !dynamic_sidebar('tertiary-aside') ) { do_action( 'sb_no_tertiary_widgets' ); } ?>
		</ul>
	</div><!-- #tertiary .aside -->
<?php } ?>

<?php sb_after_tertiary_widgets(); ?>