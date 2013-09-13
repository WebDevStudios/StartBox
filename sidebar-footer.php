<?php
/**
 * sidebar containing the footer widget areas.
 *
 * @package sbx
 */
?>
	<div class="footer-widgets-1 widget-area col span_4">
		<?php if ( ! dynamic_sidebar( 'sidebar-footer-1' ) ) : ?>
		<?php endif; // end sidebar widget area ?>
	</div>

	<div class="footer-widgets-2 widget-area col span_4">
		<?php if ( ! dynamic_sidebar( 'sidebar-footer-2' ) ) : ?>
		<?php endif; // end sidebar widget area ?>
	</div>

	<div class="footer-widgets-3 widget-area col span_4">
		<?php if ( ! dynamic_sidebar( 'sidebar-footer-3' ) ) : ?>
		<?php endif; // end sidebar widget area ?>
	</div>