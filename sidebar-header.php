<?php
/**
 * sidebar containing the header widget area.
 *
 * @package sbx
 */
?>

	<aside class="sidebar-header widget-area col span_6">
		<?php if ( ! dynamic_sidebar( 'sidebar-header' ) ) : ?>
		<?php endif; // end sidebar widget area ?>
	</aside>
