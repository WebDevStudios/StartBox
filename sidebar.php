<?php
/**
 * sb_do_sidebar() is defined in /startbox/classes/SB_Sidebars.php
 *
 * Default Output:
 * hook: sb_before_{$sidebar}
 * <div id="{$sidebar}" class="widget-area {$sidebar}-widget-area">
 * 	hook: sb_before_{$sidebar}_widgets
 * 		widgets (<aside class="widget"></aside>), or hook: sb_no_{$sidebar}_widgets
 * 	hook: sb_after_{$sidebar}_widgets
 * </div>
 * hook: sb_after_{$sidebar}
 */
sb_do_sidebar( 'primary' );
sb_do_sidebar( 'secondary' );