<?php
/**
 * StartBox Sidebar Manager
 *
 * Create additional sidebars to replace any default sidebars in StartBox for any post-type or taxonomy.
 * Uses Custom Post Types to handle sidebar registration. Metabox functionality originally lifted
 * from WP's Custom Menu admin functions.
 *
 * @package StartBox
 * @subpackage Add-ons
 * @since 2.5
 */

// Check to see if current theme supports sidebars, skip the rest if not
if (!current_theme_supports( 'sb-sidebars' )) return;

/**
 * Creates Sidebar post type
 *
 * @since 2.5
 */
function sb_sidebars_init() {
	// Add custom post type
	register_post_type( 'sidebar', array(
		'labels' 				=> array(
			'name' 				=> _x( 'Sidebars', 'post type general name', 'startbox' ),
			'singular_name' 	=> _x( 'Sidebar', 'post type singular name', 'startbox' ),
			'add_new' 			=> _x( 'Add New', 'post type add link', 'startbox' ),
			'add_new_item' 		=> __( 'Add New Sidebar', 'startbox' ),
			'edit_item' 		=> __( 'Edit Sidebar', 'startbox' ),
			'new_item' 			=> __( 'New Sidebar', 'startbox' ),
			'view_item' 		=> __( 'View Sidebar', 'startbox' ),
			'search_items' 		=> __( 'Search Sidebars', 'startbox' ),
			'not_found' 		=> __( 'No sidebars found', 'startbox' ),
			'not_found_in_trash'=> __( 'No sidebars found in Trash', 'startbox' ),
			'parent_item_colon' => '' ),
		'label' 				=> __( 'Sidebars', 'startbox' ),
		'singular_label' 		=> __( 'Sidebar', 'startbox' ),
		'public' 				=> false,
		'exclude_from_search' 	=> true,
		'show_ui' 				=> true,
		'hierarchical' 			=> false,
		'rewrite' 				=> false,
		'query_var' 			=> false,
		'supports' 				=> array( 'title' ),
		'show_in_menu'			=> 'themes.php',
		'capability_type'		=> 'post',
		'show_in_nav_menus' 	=> false,
		'register_meta_box_cb' 	=> 'sb_sidebars_setup' ));

}
add_action( 'init', 'sb_sidebars_init' );


/**
 * Include scripts to make post editor run smoothly
 *
 * @since 2.5
 */
function sb_sidebars_includes() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
}
add_action( 'load-post-new.php', 'sb_sidebars_includes' );


/**
 * Adds Sidebar link to Appearance menu in admin nav bar
 *
 * @since 2.5
 */
function sb_sidebars_bar_init() {
	global $wp_admin_bar;
    $wp_admin_bar->add_menu( array( 'id' => 'sb-sidebars', 'parent' => 'appearance', 'title' => __('Sidebars', 'startbox'), 'href' => admin_url( 'edit.php?post_type=sidebar' ) ) );
}
add_action( 'wp_before_admin_bar_render', 'sb_sidebars_bar_init' );


/**
 * Add a link on the Widgets page to the Sidebars page
 *
 * @since 2.5
 */
function sb_sidebars_widget_page() {
	// Only display this link if the user can edit theme options
	if ( current_user_can('edit_theme_options') )
		echo '<p>' . sprintf( __( 'Add additional widget areas via the <a href="%s">Sidebars</a> page.', 'startbox' ), admin_url( 'edit.php?post_type=sidebar' ) ) . '</p>'."\n";
}
add_action( 'widgets_admin_page', 'sb_sidebars_widget_page');


/**
 * Register all metaboxes on sidebar post editor
 *
 * @since 2.5
 **/
function sb_sidebars_setup() {

	// Select box for sidebars to replace
	add_meta_box( "sidebar-select", 'Select Sidebar to Replace', 'sb_sidebars_select_meta_box', 'sidebar', 'normal', 'default' );

	// Description box
	add_meta_box( "sidebar-description", 'Describe this Sidebar', 'sb_sidebars_description_meta_box', 'sidebar', 'normal', 'default' );

	// Shortcode metabox
	add_meta_box( "sidebar-shortcode", 'Shortcode', 'sb_sidebars_shortcode_metabox', 'sidebar', 'side', 'default' );

	// Get all post types, but only if they appear in nav menus
	if ( $post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'object' ) ) {
		foreach ( $post_types as $post_type ) {
			$post_type = apply_filters( 'sb_sidebars_meta_box_object', $post_type );
			if ( $post_type ) {
				$id = $post_type->name;
				add_meta_box( "sidebar-post-{$id}", $post_type->labels->name, 'sb_sidebars_post_type_meta_box', 'sidebar', 'normal', 'default', $post_type );
			}
		}
	}

	// Get all taxonomies, but only if they appear in nav menus
	if ( $taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'object' ) ) {
		foreach ( $taxonomies as $tax ) {
			$tax = apply_filters( 'sb_sidebars_meta_box_object', $tax );
			if ( $tax ) {
				$id = $tax->name;
				add_meta_box( "sidebar-tax-{$id}", $tax->labels->name, 'sb_sidebars_taxonomy_meta_box', 'sidebar', 'normal', 'default', $tax );
			}
		}
	}
}

/**
 * Creates metabox for selecting a sidebar
 *
 * @since 2.5
 */
function sb_sidebars_select_meta_box() {
	global $post_id, $sb_sidebars;

	// If there aren't any sidebars, skip the rest
	if ( !$sb_sidebars->sidebars || empty($sb_sidebars->sidebars) ) return;

	// Grab the current selection
	$selected = get_post_meta( $post_id, '_sidebar_replaced', true );

	$output = '<select name="sidebar_replaced">';

	// Include the option to replace none, so sidebar can simply be displayed via shortcode
	$output .= '<option value="none"' . selected( $selected, 'none', false) . ' >' . __( 'None', 'startbox') . '</option>';

	// Loop through all registered sidebars, add them to the list
	foreach ( $sb_sidebars->sidebars as $sidebar => $info ) {
		if ( $info['editable'] == 1 ) $output .= '<option value="' . $sidebar . '"' . selected( $selected, $sidebar, false) . ' >' . $info['name'] . '</option>';
	}
	$output .= '</select>';

	echo $output;
	wp_nonce_field( 'sb-sidebar-update', '_sb_sidebars_nonce', false );
}

/**
 * Creates metabox for describing a sidebar
 *
 * @since 2.5
 */
function sb_sidebars_description_meta_box($post) {

	// Grab the saved description
	$description = get_post_meta( $post->ID, '_sidebar_description', true );

	$output = '<textarea class="" name="description" id="excerpt">' . $description . '</textarea>';
	$output .= '<p class="description">A short description. This appears on the <a href="' . admin_url( 'widgets.php' ) . '">Widgets</a> page.</p>';

	echo $output;
}

/**
 * Creates metabox for displaying sidebar shortcode
 *
 * @since 2.5
 */
function sb_sidebars_shortcode_metabox( $post ) {
	$output = '';
	$output .= '<p class="description">Paste this shortcode anywhere you want this sidebar to appear:</p>';
	$output .= '<input class="text urlfield widefat" readonly="readonly" value="[sidebar id=&quot;' . $post->post_name . '&quot;]" type="text">';

	echo $output;
}

/**
 * Create post type metaboxes
 *
 * @since 2.5
 *
 * @param string $object Not used.
 * @param string $post_type The post type object.
 */
function sb_sidebars_post_type_meta_box( $post, $post_type ) {

	$post_type_name = $post_type['args']->name;

	// Arguments for quering posts
	$args = array(
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => -1,
		'post_type' => $post_type_name,
		'suppress_filters' => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false
	);

	if ( isset( $post_type['args']->_default_query ) )
		$args = array_merge($args, (array) $post_type['args']->_default_query );

	$post_type_object = get_post_type_object($post_type_name);

	$selected = (array) maybe_unserialize(get_post_meta($post->ID, '_post', true));

	?>
	<p><?php printf( __('Select which %s should use this sidebar:', 'startbox'), $post_type['args']->labels->name ); ?></p>
	<div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv">
		<div id="<?php echo esc_attr( $post_type_name ); ?>-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="<?php echo esc_attr( $post_type_name ); ?>checklist" class="list:<?php echo $post_type_name?> categorychecklist form-no-clear">
				<?php
					$get_posts = new WP_Query;
					$posts = $get_posts->query( $args );
					if ( $get_posts->post_count ) {
						$output = '<li><label><input type="checkbox" ' . checked( in_array( 'all-' . $post_type['args']->labels->name, $selected), true, false ) . ' name="post[all-' . $post_type['args']->labels->name .']" value="true"/> All ' . $post_type['args']->labels->name . ' (includes all future ' . $post_type['args']->labels->name . ')</label></li>';
						if ( $post_type['args']->name == 'page' ) { $output .= '<li><label><input type="checkbox" ' . checked( in_array( 'Home', $selected), true, false ) . ' name="post[Home]" value="true"/>Home</label></li>'; }
						while ( $get_posts->have_posts() ) : $get_posts->the_post();
							$output .= '<li>';
							$output .= '<label>';
							$output .= '<input type="checkbox" ' . checked( in_array( get_the_id(), $selected), true, false ) . ' name="post[' . get_the_id() . ']" value="true" /> ';
							$output .= get_the_title();
							$output .= '</label>';
							$output .= '</li>';
						endwhile;
						echo $output;
					} else {
						echo '<li id="error">'. $post_type['args']->labels->not_found .'</li>';
					}
				?>
			</ul>
		</div><!-- /.tabs-panel -->

	</div><!-- /.posttypediv -->
	<?php
}

/**
 * Displays taxonomy metaboxes
 *
 * @since 2.5
 *
 * @param string $object Not used.
 * @param string $taxonomy The taxonomy object.
 */
function sb_sidebars_taxonomy_meta_box( $post, $taxonomy ) {

	$taxonomy_name = $taxonomy['args']->name;

	$args = array(
		'child_of' => 0,
		'exclude' => '',
		'hide_empty' => false,
		'hierarchical' => 1,
		'include' => '',
		'include_last_update_time' => false,
		'order' => 'ASC',
		'orderby' => 'name',
		'pad_counts' => false,
	);

	$terms = get_terms( $taxonomy_name, $args );
	$selected = (array) maybe_unserialize( get_post_meta( $post->ID, '_tax', true ) );

	if ( ! $terms || is_wp_error($terms) ) {
		echo '<p>' . __( 'No items.', 'startbox' ) . '</p>';
		return;
	}

	// Use a custom walker for displaying the taxonomy list
	$walker = new SB_Sidebars_Checklist;

	?>
	<p><?php printf( __('Select which %s should use this sidebar:', 'startbox'), $taxonomy['args']->label ); ?></p>
	<div id="taxonomy-<?php echo esc_attr( $taxonomy_name ); ?>" class="taxonomydiv">
		<div id="tabs-panel-<?php echo esc_attr( $taxonomy_name ); ?>-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="<?php echo esc_attr( $taxonomy_name ); ?>checklist" class="list:<?php echo esc_attr( $taxonomy_name ) ?> categorychecklist form-no-clear">
				<?php
					echo '<li><label><input type="checkbox" ' . checked( in_array( 'all-' . $taxonomy['args']->rewrite['slug'], $selected), true, false ) . ' name="tax[all-' . esc_attr( $taxonomy['args']->rewrite['slug'] ) .']" value="true"/> All ' . $taxonomy['args']->label . ' (includes all future ' . $taxonomy['args']->label . ')</label></li>';
					$args['walker'] = $walker;
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
				?>
			</ul>
		</div><!-- /.tabs-panel -->

	</div><!-- /.taxonomydiv -->
	<?php
}



/**
 * Custom walker class for taxonomy lists
 *
 * @since 2.5
 * @uses Walker_Nav_Menu
 */
class SB_Sidebars_Checklist extends Walker_Nav_Menu  {

	/**
	 * @see Walker::start_el()
	 * @since 2.5
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $post_id;

		$selected = (array) maybe_unserialize( get_post_meta( $post_id, '_tax', true ) );
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<label>';
		$output .= '<input type="checkbox" ' . checked( in_array( esc_attr( $item->object_id ) , $selected), true, false ) . ' name="tax[' . esc_attr( $item->object_id )  . ']" value="true" /> ';
		$output .= empty( $item->label ) ? esc_html( $item->title ) : esc_html( $item->label );
		$output .= '</label>';
		$output .= '</li>';
	}
}

/**
 * Save all the post data
 *
 * @since 2.5
 */
function sb_sidebars_save( $post_id ) {
	// Verify we should actually be saving any data
	if (
		!sb_verify_post_type( 'sidebar' ) || 				// If it's not a sidebar post type,
		defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||	// or if it's autosaving,
		!isset($_POST['sidebar_replaced']) ||				// or we don't have some postdata,
		!current_user_can('edit_theme_options')	||			// or the current user can't edit themes,
		!isset( $_POST['_sb_sidebars_nonce'] ) ||			// or the NONCE is not set,
		!wp_verify_nonce( $_POST['_sb_sidebars_nonce'], 'sb-sidebar-update' ) // or the NONCE is invalid
	)
		return $post_id;									// skip the rest...

	// update post meta for the sidebar we're replacing
	update_post_meta( $post_id, '_sidebar_replaced', $_POST['sidebar_replaced'] );

	// update post meta for the sidebar we're replacing
	update_post_meta( $post_id, '_sidebar_description', $_POST['description'] );

	// save checked status for all post types
	delete_post_meta( $post_id, '_post' );
	if ($_POST['post']) {
		foreach( $_POST['post'] as $post => $value ) {
			$data = (array) maybe_unserialize(get_post_meta($post_id, '_post', true));

			if( count($data) != 0 ) {
				if ( !in_array( $post, $data ) ) {
					$data[] = $post;
				}
				$data = array_unique($data); // remove duplicates
				sort( $data ); // sort array
				$data = serialize($data);
				update_post_meta($post_id, '_post', $data);
			} else {
				$data = array();
				$data[0] = $post;
				$data = serialize($data);
				update_post_meta($post_id, '_post', $data);
			}
		}
	}

	// save checked status for all taxonomies
	delete_post_meta( $post_id, '_tax' );
	if ($_POST['tax']) {
		foreach( $_POST['tax'] as $tax => $value ) {
			$data = (array) maybe_unserialize(get_post_meta($post_id, '_tax', true));
			if( count($data) != 0 ) {
				if ( !in_array( $tax, $data ) ) {
					$data[] = $tax;
				}
				$data = array_unique($data); // remove duplicates
				sort( $data ); // sort array
				$data = serialize($data);
				update_post_meta($post_id, '_tax', $data);
			} else {
				$data = array();
				$data[0] = $tax;
				$data = serialize($data);
				update_post_meta($post_id, '_tax', $data);
			}
		}
	}

	// Delete transient data (used to cache all sidebars for front-end display)
	delete_transient( 'sb_sidebars_post_type' );
	delete_transient( 'sb_sidebars_tax' );

	return $post_id;
}
add_action( 'save_post', 'sb_sidebars_save' );

/**
 * Delete cached sidebars when a sidebar is deleted
 *
 * @since 2.5
 */
function sb_sidebars_delete($post_id) {
	$post = get_post($post_id);

	// Verify we're actually deleting a sidebar
	if ( $post->post_type == 'sidebar' ) {
        // Delete transient data (used to cache all sidebars for front-end display)
		delete_transient( 'sb_sidebars_post_type' );
		delete_transient( 'sb_sidebars_tax' );
	}

}
add_action( 'trash_post', 'sb_sidebars_delete' );

/**
 * Filter the "post updated" messages
 *
 * @since 2.5
 */
function sb_sidebars_update_messages( $messages ) {
	$messages['sidebar']['1'] = sprintf( __('Sidebar saved. <a href="%s">Give it some widgets</a>', 'startbox'), esc_url( admin_url( 'widgets.php' ) ) );
	$messages['sidebar']['6'] = sprintf( __('Sidebar saved. <a href="%s">Give it some widgets</a>', 'startbox'), esc_url( admin_url( 'widgets.php' ) ) );
	return $messages;
}
add_filter( 'post_updated_messages', 'sb_sidebars_update_messages' );