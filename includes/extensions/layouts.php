<?php
/**
 * StartBox Theme Layouts
 *
 * Theme Layouts was originally created by Justin Tadlock for use with Hybrid Core.
 * This allows developers to easily add/remove support for multiple layout structures.
 * It gives users the ability to control how each post type is displayed on the
 * front end of the site.  The layout can also be filtered for any page of a WordPress site.
 *
 * @package StartBox
 * @subpackage Theme Layouts
 * @copyright Original Copyright (c) 2010 - 2011, Justin Tadlock <justin@justintadlock.com>
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


/**
 * Gets the layout for the current post, page or taxonomy. If none is specified, use 'layout-default'.
 *
 * @since 2.5
 * @return string The layout for the given page.
 */

function sb_get_layout() {
	global $wp_query;

	/* Get the available post layouts and store them in an array */
	foreach ( get_theme_support( 'sb-layouts' ) as $layout => $key ) {
		$layouts[] = $layout;
	}


	/* Set the layout to an empty string. */
	$layout = '';

	/* If viewing a singular post/page, check if a layout has been specified. */
	if ( is_home() || is_singular() ) {

		/* Get the current post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Get the post layout. */
		$layout = sb_get_post_layout( $post_id );
	}

	/* If viewing a taxonomy, check if a layout has been specified */
	if ( is_category() || is_tag() || is_tax() || is_archive() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$layout = $term->meta['layout'];
	}

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, $layouts ) || $layout == 'default' )
		$layout = apply_filters( 'sb_get_post_layout_default', 'default' );

	/* Return the layout and allow plugin/theme developers to override it. */
	return esc_attr( apply_filters( 'get_theme_layout', "layout-{$layout}" ) );
}

/**
 * Get the post layout based on the given post ID.
 *
 * @since 2.5
 */
function sb_get_post_layout( $post_id ) {
	$post_layout = get_post_meta( $post_id, '_sb_layout', true );
	return ( !empty( $post_layout ) ? $post_layout : 'default' );
}

/**
 * Update/set the post layout based on the given post ID and layout.
 *
 * @since 2.5
 */
function sb_set_post_layout( $post_id, $layout ) {
	update_post_meta( $post_id, '_sb_layout', $layout );
}

/**
 * Generate an array of supported layouts with their text string
 *
 * @since 2.5
 */
function sb_supported_layouts($instance) {

	/* Get theme-supported theme layouts. */
	$supported_layouts = get_theme_support( $instance );
	$post_layouts = $supported_layouts[0];

	return $post_layouts;
}

/**
 * Post layouts admin setup.  Registers the post layouts meta box for the post editing screen.  Adds the
 * metadata save function to the 'save_post' hook.
 *
 * @since 2.5
 */
function sb_layouts_admin_setup() {

	// For each available post type, create a meta box on its edit page if 'public' is set to true
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
		if ($type->name != 'slideshow')
			add_meta_box( 'theme-layouts-post-meta-box', __( 'Layout', 'startbox' ), 'sb_layouts_post_meta_box', $type->name, 'side', 'default' );
	}

	// For each available taxonomy, add meta information if it supports 'show_ui'
	foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name) {
        add_action($tax_name . '_edit_form', 'sb_layouts_term_meta_box', 10, 2);
    }

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'sb_layouts_save_post', 10, 2 );
}
add_action( 'admin_menu', 'sb_layouts_admin_setup' );

/**
 * Adds the post layout class to the WordPress body class in the form of "layout-$layout"
 *
 * @since 2.5
 * @param array $classes all the set classes
 */
function sb_layouts_body_class( $classes ) {

	/* Adds the layout to array of body classes. */
	$classes[] = sanitize_html_class( sb_get_layout() );

	/* Return the $classes array. */
	return $classes;
}
add_filter( 'body_class', 'sb_layouts_body_class' );

/**
 * Displays a meta box of radio selectors on the post editing screen, which allows theme users to select
 * the layout they wish to use for the specific post.
 *
 * @since 2.5
 */
function sb_layouts_post_meta_box( $post, $box ) {

	/* Get theme-supported theme layouts. */
	$layouts = get_theme_support( 'sb-layouts' );
	$post_layouts = $layouts[0];

	/* Get the current post's layout. */
	$post_layout = sb_get_post_layout( $post->ID ); ?>

	<div class="post-layout">

		<input type="hidden" name="sb_layouts_post_meta_box_nonce" value="<?php echo esc_attr( wp_create_nonce( basename( __FILE__ ) ) ); ?>" />

		<p><?php _e( 'Specify a custom page layout for this content.', 'startbox' ); ?></p>

		<div class="post-layout-wrap">
			<ul style="overflow:hidden;">
				<li><input type="radio" name="post_layout" id="post_layout_default" value="default" <?php checked( $post_layout, 'default' );?> /> <label for="post_layout_default"><?php _e( 'Default', 'startbox' ); ?> (Set in <a href="<?php echo esc_url( admin_url( 'themes.php?page=sb_admin' ) ); ?>"><?php _e('Theme Options','startbox');?></a>)</label></li>

				<?php foreach ( $post_layouts as $layout => $key ) { ?>
					<li style="float:left; margin-right:15px; margin-bottom:10px">
						<label for="post_layout_<?php echo esc_attr( $layout ); ?>">
							<input type="radio" name="post_layout" id="post_layout_<?php echo esc_attr( $layout ); ?>" value="<?php echo esc_attr( $layout ); ?>" <?php checked( $post_layout, $layout ); ?>  style="float:left; margin-right:5px; margin-top:20px"/>
							<img src="<?php echo esc_url( $key['img'] ); ?>" alt="<?php echo esc_attr( $key['label'] ); ?>"  width="50" height="40" />
						</label>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div><?php
}

/**
 * Saves the post layout metadata if on the post editing screen in the admin.
 *
 * @since 2.5
 */
function sb_layouts_save_post( $post_id, $post ) {

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['sb_layouts_post_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['sb_layouts_post_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the previous post layout. */
	$old_layout = sb_get_post_layout( $post_id );

	/* Get the submitted post layout. */
	$new_layout = esc_attr( $_POST['post_layout'] );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	if ( $old_layout !== $new_layout )
		sb_set_post_layout( $post_id, $new_layout );
}

/**
 * Displays the layout selector form
 *
 * @since 2.5
 */
function sb_layouts_term_meta_box($tag, $taxonomy) {

	$tax = get_taxonomy( $taxonomy ); // create an object from the given taxonomy
	$layouts = get_theme_support( 'sb-layouts' ); // Get theme-supported layouts
	$post_layouts = $layouts[0]; // Grab the first item in the layouts array (which is the array of supported layouts)

?>

	<table class="form-table">

	<tr>
		<th scope="row" valign="top"><label><?php _e('Custom Layout', 'startbox'); ?></label></th>
		<td>
			<ul style="overflow:hidden;">
				<li><input type="radio" name="meta[layout]" id="post_layout_default" value="" <?php checked( $tag->meta['layout'], '' ); ?> /> <label for="post_layout_default"><?php _e( 'Default', 'startbox' ); ?> (Set in <a href="<?php echo esc_url( admin_url( 'themes.php?page=sb_admin' ) ); ?>">Theme Options</a>)</label></li>

				<?php foreach ( $post_layouts as $layout => $key ) { ?>
					<li style="float:left; margin-right:15px; margin-bottom:10px">
						<label for="post_layout_<?php echo esc_attr( $layout ); ?>">
							<input type="radio" name="meta[layout]" id="post_layout_<?php echo esc_attr( $layout ); ?>" value="<?php echo esc_attr( $layout ); ?>" <?php checked( $tag->meta['layout'], $layout ); ?>  style="float:left; margin-right:5px; margin-top:20px"/>
							<img src="<?php echo esc_url( $key[img] ); ?>" alt="<?php echo esc_attr( $key[label] ); ?>"  width="50" height="40" />
						</label>
					</li>
				<?php } ?>
			</ul>
			<p class="description">Select a custom layout for this <?php echo $taxonomy; ?>.</p>
		</td>
	</tr>

	</table>

<?php
}

/**
 * Save the taxonomy layout meta when the taxonomy is saved (hat tip to Nathan Rice of Genesis! and Joost DeValk)
 *
 * @since 2.5
 */
function sb_layouts_term_meta_save($term_id, $tt_id, $taxonomy) {

	// Grab the saved meta from the stored option
	$term_meta = (array) get_option( 'startbox_termmeta' );

	// If meta is already stored for the given term, use it. Else, use an empty array
	$term_meta[$term_id] = isset( $_POST['meta'] ) ? (array) $_POST['meta'] : array();

	// Update the saved meta with the new values
	update_option( 'startbox_termmeta', $term_meta );

}
add_action('edit_term', 'sb_layouts_term_meta_save', 10, 3);

/**
 * Delete the taxonomy layout meta when the taxonomy is deleted (hat tip to Nathan Rice of Genesis! and Joost DeValk)
 *
 * @since 2.5
 */
function sb_layouts_term_meta_delete($term_id, $tt_id, $taxonomy) {

	// Grab the saved meta from the stored option
	$term_meta = (array) get_option( 'startbox_termmeta' );

	// Unset the meta for the given term ID
	unset( $term_meta[$term_id] );

	// Update the saved meta, now one value lighter
	update_option( 'startbox_termmeta', (array) $term_meta );

}
add_action('delete_term', 'sb_layouts_term_meta_delete', 10, 3);

/**
 * Filter get_term to attach the layout meta to each taxonomy (hat tip to Nathan Rice of Genesis! and Joost DeValk)
 *
 * @since 2.5
 */
function sb_layouts_term_meta_filter($term, $taxonomy) {

	// Grab the saved meta from the stored option
	$meta = get_option( 'startbox_termmeta' );

	// If meta is already stored for the given term, use it. Else, use an empty array
	$term_meta = isset( $meta[$term->term_id] ) ? $meta[$term->term_id] : array();

	// Parse all the meta items, stacked against a null default
	$term->meta = wp_parse_args( $term_meta, array(
			'layout' => ''
	) );

	// Sanitize each term meta with a simple kses
	foreach ( $term->meta as $field => $value ) {
		$term->meta[$field] = stripslashes( wp_kses_decode_entities( $value ) );
	}

	// Return the meta (but you already knew that)
	return $term;

}
add_filter('get_term', 'sb_layouts_term_meta_filter', 10, 2);