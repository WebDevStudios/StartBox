<?php

// After div#primary, before div#secondary
function sb_between_primary_and_secondary_widgets() {
	_deprecated_function( __FUNCTION__, '2.5', 'sb_after_primary_aside_widgets' );
	do_action('sb_between_primary_and_secondary_widgets');
}

// Warn users of deprecated page templates
function sb_deprecated_templates() {
	if ( current_user_can('edit_pages') ) {
		if ( is_page_template( 'page-tertiarysidebar.php' ) )
			echo '<div class="box alert">This page uses the Tertiary Sidebar page template, which is now obsolete thanks to the Sidebar Manager introduced in StartBox 2.5. Please select the Default page template instead. This template will be removed in StartBox 2.6. (Only users who can edit this page can see this warning.)</div>';
		elseif ( is_page_template( 'page-fullwidth.php' ) )
			echo '<div class="box alert">This page uses the Full Width page template, which is now obsolete thanks to the Layout Settings introduced in StartBox 2.5. Please select the Default page template instead. This template will be removed in StartBox 2.6. (Only users who can edit this page can see this warning.)</div>';
	}
}
add_action( 'sb_before_content', 'sb_deprecated_templates' );

?>