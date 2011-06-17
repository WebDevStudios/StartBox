<?php 

/**
 * Callback for StartBox Style Options Page layout
 *
 * @since Unkown
 */
function sb_style() { 
	
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

?>

<?php if ( $_GET['updated'] ) echo '<div id="message" class="updated fade"><p>'.THEME_NAME.' Style Options Saved.</p></div>'; ?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>

    <h2><?php echo THEME_NAME; ?> Style Options</h2>
	
    <div class="metabox-holder">
    	<form method="post" enctype="multipart/form-data" action="options.php" id="sb_options">
   	    <?php settings_fields( 'sb_style' ); ?>

        <div id="primary-options" class="postbox-container primary-options-column column">
            <?php do_meta_boxes( 'sb_style', 'primary', null ); ?>
        </div>  <!-- postbox-container -->
        
        <div id="secondary-options" class="postbox-container secondary-options-column column">
        	<?php do_meta_boxes( 'sb_style', 'secondary', null ); ?>
        </div>  <!-- postbox-container -->

        </form>
    </div>  <!-- metabox-holder -->
</div> <!-- wrap -->

<?php
}

/**
 * Creates option page and enqueues all necessary scripts
 *
 * @since Unknown
 */
function sb_style_init() {
	$sb_style = add_theme_page( THEME_NAME." Style Options", "Style Options", 'edit_theme_options', 'sb_style', 'sb_style' );
	register_setting( 'sb_admin', THEME_OPTIONS, 'sb_sanitize');
	add_action( "admin_print_scripts-$sb_style", 'sb_admin_scripts' );
	add_action( "admin_print_styles-$sb_style", 'sb_admin_scripts' );
}
//add_action( 'admin_menu', 'sb_style_init' );

?>