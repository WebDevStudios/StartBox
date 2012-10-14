<?php
/**
 * sb_do_sidebar() is defined in /includes/functions/sidebars.php
 *
 * Default Output:
 * hook: sb_before_{$location}
 * <div id="$location" class="aside $location-aside">
 * 	hook: sb_before_{$location}_widgets
 * 	<ul class="xoxo">
 * 		sidebar contents, or hook: sb_no_{$location}_widgets
 * 	</ul>
 * 	hook: sb_after_{$location}_widgets
 * </div>
 * hook: sb_after_{$location}
 */
sb_do_sidebar( 'primary', 'primary_widget_area');
sb_do_sidebar( 'secondary', 'secondary_widget_area');