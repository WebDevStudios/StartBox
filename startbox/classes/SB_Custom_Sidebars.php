<?php
/**
 * StartBox Custom Sidebar Manager
 *
 * Create additional sidebars to replace any default sidebars
 * in StartBox for any post-type or taxonomy. Uses Custom Post
 * Types to handle sidebar registration. Metabox functionality
 * originally lifted from WP's Custom Menu admin functions.
 *
 * @package StartBox
 * @subpackage Classes
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports sidebars, skip the rest if not
if ( ! current_theme_supports( 'sb-custom-sidebars' ) )
	return;

class SB_Custom_Sidebars extends SB_Sidebars {

	/**
	 * Hook everything where it belongs and fire up the engine
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// Build Custom Sidebar Support
		add_action( 'init', array( $this, 'register_cpt' ), 5 );
		add_filter( 'wp_insert_post_data' , array( $this, 'default_post_slug' ), '10', 2 );
		add_action( 'load-post-new.php', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar' ) );
		add_action( 'widgets_admin_page', array( $this, 'widget_page_output' ) );
		add_action( 'save_post', array( $this, 'save_sidebar' ) );
		add_action( 'trashed_post', array( $this, 'dump_sidebar_cache' ) );
		add_action( 'untrash_post', array( $this, 'dump_sidebar_cache' ) );
		add_filter( 'post_updated_messages', array( $this, 'sidebar_update_messages' ) );

		// Register and render custom sidebars
		add_action( 'init', array( $this, 'register_custom_sidebars'), 11 );
		add_filter( 'sb_do_sidebar', array( $this, 'maybe_replace_current_sidebar' ), 10, 2 );

		// Do all the normal SB_Sidebars business
		parent::__construct();
	}

	/**
	 * Creates Sidebar post type
	 *
	 * @since 2.5.0
	 */
	function register_cpt() {
		register_post_type( 'sidebar', array(
			'labels' => array(
				'name'               => _x( 'Sidebars', 'post type general name', 'startbox' ),
				'singular_name'      => _x( 'Sidebar', 'post type singular name', 'startbox' ),
				'add_new'            => _x( 'Add New', 'post type add link', 'startbox' ),
				'add_new_item'       => __( 'Add New Sidebar', 'startbox' ),
				'edit_item'          => __( 'Edit Sidebar', 'startbox' ),
				'new_item'           => __( 'New Sidebar', 'startbox' ),
				'view_item'          => __( 'View Sidebar', 'startbox' ),
				'search_items'       => __( 'Search Sidebars', 'startbox' ),
				'not_found'          => __( 'No sidebars found', 'startbox' ),
				'not_found_in_trash' => __( 'No sidebars found in Trash', 'startbox' ),
				'parent_item_colon'  => '' ),
			'label'                => __( 'Sidebars', 'startbox' ),
			'singular_label'       => __( 'Sidebar', 'startbox' ),
			'public'               => false,
			'exclude_from_search'  => true,
			'show_ui'              => true,
			'hierarchical'         => false,
			'rewrite'              => false,
			'query_var'            => false,
			'supports'             => array( 'title' ),
			'show_in_menu'         => 'themes.php',
			'capability_type'      => 'post',
			'show_in_nav_menus'    => false,
			'register_meta_box_cb' => array( $this, 'metabox_setup' )
		) );

	}

	/**
	 * Set our base slug for new sidebars to "custom-sidebar"
	 *
	 * This is to correct an issue where no widgets can be added to the sidebar
	 * if it's slug is numeric only (e.g. 586), which is the default slug for
	 * new sidebars that have no title during autosave.
	 *
	 * @since  2.7.0
	 * @param  array $data    An array of sanitized post data
	 * @param  array $postarr An array of the raw post data
	 * @return array          An array of our modified, sanitized post data
	 */
	function default_post_slug( $data , $postarr ) {

		// If this is a sidebar post
		// And this is NOT an unsaved post (auto-draft)
		// And there is currently no post-name (slug)
		if (
			'sidebar' == $data['post_type']
			&& 'auto-draft' != $postarr['post_status']
			&& '' == $data['post_name']
		) {
			// Override the slug, enumerated with the post's ID
			$data['post_name'] = 'custom-sidebar-' . $postarr['ID'];
		}

		// Don't forget to return the data, otherwise we ruin everything.
		return $data;

	}

	/**
	 * Include scripts to make sidebar editor run smoothly
	 *
	 * @since 2.5.0
	 */
	function enqueue_scripts() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	/**
	 * Adds Sidebar link to Appearance menu in admin nav bar
	 *
	 * @since 2.5.0
	 */
	function admin_bar() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id'     => 'sb-sidebars',
			'parent' => 'appearance',
			'title'  => __('Sidebars', 'startbox'),
			'href'   => admin_url( 'edit.php?post_type=sidebar' )
		) );
	}

	/**
	 * Add a link on the Widgets page to the Sidebars page
	 *
	 * @since 2.5.0
	 */
	function widget_page_output() {
		// Only display this link if the user can edit theme options
		if ( current_user_can('edit_theme_options') )
			echo '<p>' . sprintf( __( 'Add additional widget areas via the <a href="%s">Sidebars</a> page.', 'startbox' ), admin_url( 'edit.php?post_type=sidebar' ) ) . '</p>'."\n";
	}

	/**
	 * Register all metaboxes on sidebar post editor
	 *
	 * @since 2.5.0
	 **/
	function metabox_setup() {

		// Select box for sidebars to replace
		add_meta_box( "sidebar-select", 'Select Sidebar to Replace', array( $this, 'sidebar_select_metabox' ), 'sidebar', 'normal', 'default' );

		// Description box
		add_meta_box( "sidebar-description", 'Describe this Sidebar', array( $this, 'sidebar_description_metabox' ), 'sidebar', 'normal', 'default' );

		// Shortcode metabox
		add_meta_box( "sidebar-shortcode", 'Shortcode', array( $this, 'sidebar_shortcode_metabox' ), 'sidebar', 'side', 'default' );

		// Get all post types that are set to show in nav menus
		if ( $post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'object' ) ) {
			foreach ( $post_types as $post_type ) {
				$post_type = apply_filters( 'sb_sidebars_meta_box_object', $post_type );
				if ( $post_type ) {
					$id = $post_type->name;
					add_meta_box( "sidebar-post-{$id}", $post_type->labels->name, array( $this, 'post_type_metabox' ), 'sidebar', 'normal', 'default', $post_type );
				}
			}
		}

		// Get all taxonomies that are set to show in nav menus
		if ( $taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'object' ) ) {
			foreach ( $taxonomies as $tax ) {
				$tax = apply_filters( 'sb_sidebars_meta_box_object', $tax );
				if ( $tax ) {
					$id = $tax->name;
					add_meta_box( "sidebar-tax-{$id}", $tax->labels->name, array( $this, 'taxonomy_metabox' ), 'sidebar', 'normal', 'default', $tax );
				}
			}
		}
	}

	/**
	 * Creates metabox for selecting a sidebar
	 *
	 * @since 2.5.0
	 */
	function sidebar_select_metabox() {
		global $post_id, $startbox;

		// If there aren't any sidebars, skip the rest
		if ( empty( $startbox->sidebars->sidebars ) )
			return;

		// Grab the current selection
		$selected = get_post_meta( $post_id, '_sidebar_replaced', true );

		$output = '<select name="sidebar_replaced">';

		// Include the option to replace none, because
		// a sidebar can be displayed via shortcode
		$output .= '<option value="none"' . selected( $selected, 'none', false) . ' >' . __( 'None', 'startbox') . '</option>';

		// Loop through all registered sidebars, add them to the list
		foreach ( $startbox->sidebars->sidebars as $sidebar ) {
			// Only include editable sidebars in our list
			if ( 1 === $sidebar['editable'] )
				$output .= '<option value="' . $sidebar['id'] . '"' . selected( $selected, $sidebar['id'], false) . ' >' . $sidebar['name'] . '</option>';
		}
		$output .= '</select>';

		// Nonce, for security
		$output .= wp_nonce_field( 'sb-sidebar-update', '_sb_sidebars_nonce', true, false );

		// Disply our output
		echo $output;

	}

	/**
	 * Creates metabox for describing a sidebar
	 *
	 * @since 2.5.0
	 */
	function sidebar_description_metabox($post) {

		// Grab the saved description
		$description = get_post_meta( $post->ID, '_sidebar_description', true );

		// Concatenate our output
		$output = '<textarea class="" name="description" id="excerpt">' . $description . '</textarea>';
		$output .= '<p class="description">';
		$output .= sprintf(
			__( 'A short description. This appears on the %s page.', 'startbox' ),
			'<a href="' . admin_url( 'widgets.php' ) . '">' . __( 'Widgets', 'startbox' ) . '</a>'
		);
		$output .= '</p>';

		// Display our output
		echo $output;
	}

	/**
	 * Creates metabox for displaying sidebar shortcode
	 *
	 * @since 2.5.0
	 */
	function sidebar_shortcode_metabox( $post ) {

		// Concatenate our output
		$output = '';
		$output .= '<p class="description">';
		$output .= __( 'Paste this shortcode anywhere you want this sidebar to appear:', 'startbox' );
		$output .= '</p>';
		$output .= '<input class="text urlfield widefat" readonly="readonly" value="[sidebar id=&quot;' . $post->post_name . '&quot;]" type="text">';

		// Display out output
		echo $output;
	}

	/**
	 * Create post type metaboxes
	 *
	 * Note: This logic was lifted directly from WP's custom menus
	 *
	 * @since 2.5.0
	 *
	 * @param string $object    Not used.
	 * @param string $post_type The post type object.
	 */
	function post_type_metabox( $post, $post_type ) {

		// Grab just the post type name
		$post_type_name = $post_type['args']->name;

		// Arguments for quering posts
		$args = array(
			'post_type'              => $post_type_name,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'posts_per_page'         => -1,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false
		);

		// Merge our query args with additional default query args
		if ( isset( $post_type['args']->_default_query ) )
			$args = array_merge( $args, (array) $post_type['args']->_default_query );

		// Grab the post type object
		$post_type_object = get_post_type_object( $post_type_name );

		// Grab the array of selected pots
		$selected = (array) maybe_unserialize( get_post_meta( $post->ID, '_post', true ) );

		// Generate our output
	?>
		<p><?php printf( __('Select which %s should use this sidebar:', 'startbox'), $post_type['args']->labels->name ); ?></p>
		<div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv">
			<div id="<?php echo esc_attr( $post_type_name ); ?>-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
				<ul id="<?php echo esc_attr( $post_type_name ); ?>checklist" class="list:<?php echo $post_type_name?> categorychecklist form-no-clear">
					<?php
						$get_posts = new WP_Query;
						$posts = $get_posts->query( $args );
						if ( $get_posts->post_count ) {
							$output = '<li><label><input type="checkbox" ' . checked( in_array( 'all-' . $post_type['args']->name, $selected), true, false ) . ' name="post[all-' . $post_type['args']->name .']" value="true"/> All ' . $post_type['args']->labels->name . ' (includes all future ' . $post_type['args']->labels->name . ')</label></li>';
							if ( $post_type['args']->name == 'page' )
								$output .= '<li><label><input type="checkbox" ' . checked( in_array( 'Home', $selected ), true, false ) . ' name="post[Home]" value="true"/>Home</label></li>';
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
	 * @since 2.5.0
	 *
	 * @param string $object Not used.
	 * @param string $taxonomy The taxonomy object.
	 */
	function taxonomy_metabox( $post, $taxonomy ) {

		// Grab the taxonomy name
		$taxonomy_name = $taxonomy['args']->name;

		// Query our terms for the given taxonomy
		$terms = get_terms( $taxonomy_name, array(
			'child_of'                 => 0,
			'exclude'                  => '',
			'hide_empty'               => false,
			'hierarchical'             => 1,
			'include'                  => '',
			'include_last_update_time' => false,
			'order'                    => 'ASC',
			'orderby'                  => 'name',
			'pad_counts'               => false,
		) );

		// Grab the selected terms
		$selected = (array) maybe_unserialize( get_post_meta( $post->ID, '_tax', true ) );

		if ( ! $terms || is_wp_error($terms) ) {
			echo '<p>' . __( 'No items.', 'startbox' ) . '</p>';
			return;
		}

		// Use a custom walker for displaying the taxonomy list
		$walker = new SB_Sidebars_Checklist;

		// Generate our output
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
	 * Save all the post data
	 *
	 * @since 2.5.0
	 */
	function save_sidebar( $post_id ) {
		// Verify we should actually be saving any data
		if (
			'sidebar' !== get_post_type( $post_id )              // If it's not a sidebar post type,
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) // OR it's an autosave,
			|| ! isset($_POST['sidebar_replaced'])               // OR we don't have some postdata,
			|| ! current_user_can('edit_theme_options')          // OR the current user can't edit themes,
			|| ! isset( $_POST['_sb_sidebars_nonce'] )           // OR the NONCE is not set,
			|| ! wp_verify_nonce( $_POST['_sb_sidebars_nonce'], 'sb-sidebar-update' ) // or the NONCE is invalid
		)
			return $post_id;									// skip the rest...

		// update post meta for the sidebar we're replacing
		update_post_meta( $post_id, '_sidebar_replaced', $_POST['sidebar_replaced'] );

		// update post meta for the sidebar we're replacing
		update_post_meta( $post_id, '_sidebar_description', $_POST['description'] );

		// save checked status for all post types
		delete_post_meta( $post_id, '_post' );
		if ( ! empty( $_POST['post'] ) ) {
			foreach( $_POST['post'] as $post => $value ) {
				$data = (array) maybe_unserialize( get_post_meta($post_id, '_post', true ) );

				if( count( $data ) != 0 ) {
					if ( !in_array( $post, $data ) ) {
						$data[] = $post;
					}
					$data = array_unique( $data ); // remove duplicates
					sort( $data ); // sort array
					$data = serialize( $data );
					update_post_meta($post_id, '_post', $data);
				} else {
					$data = array();
					$data[0] = $post;
					$data = serialize( $data );
					update_post_meta($post_id, '_post', $data);
				}
			}
		}

		// save checked status for all taxonomies
		delete_post_meta( $post_id, '_tax' );
		if ( ! empty( $_POST['tax'] ) ) {
			foreach( $_POST['tax'] as $tax => $value ) {
				$data = (array) maybe_unserialize( get_post_meta($post_id, '_tax', true ) );
				if( count( $data ) != 0 ) {
					if ( !in_array( $tax, $data ) ) {
						$data[] = $tax;
					}
					$data = array_unique( $data ); // remove duplicates
					sort( $data ); // sort array
					$data = serialize( $data );
					update_post_meta($post_id, '_tax', $data);
				} else {
					$data = array();
					$data[0] = $tax;
					$data = serialize( $data );
					update_post_meta($post_id, '_tax', $data);
				}
			}
		}

		// Delete transient data (used to cache all sidebars for front-end display)
		delete_transient( 'sb_custom_sidebars' );
		delete_transient( 'sb_custom_sidebar_locations' );

		return $post_id;
	}

	/**
	 * Delete cached sidebars when a sidebar is deleted
	 *
	 * @since 2.5.0
	 */
	function dump_sidebar_cache( $post_id ) {
		// Verify we're actually deleting a sidebar
		if ( 'sidebar' == get_post_type( $post_id ) ) {
	        delete_transient( 'sb_custom_sidebars' );
	    	delete_transient( 'sb_custom_sidebar_locations' );
	    }
	}

	/**
	 * Filter the "post updated" messages for sidebars
	 *
	 * @since 2.5.0
	 */
	function sidebar_update_messages( $messages ) {
		$messages['sidebar']['1'] = $messages['sidebar']['6'] = sprintf( __( 'Sidebar saved. %s', 'startbox' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . __( 'Give it some widgets', 'startbox' ) . '</a>' );
		return $messages;
	}

	/**
	 * Get all custom sidebars
	 *
	 * This is just a wrapper for get_posts() that
	 * caches the query for 30 days.
	 *
	 * @since  2.5.0
	 * @return array An array of registered sidebars
	 */
	function get_custom_sidebars() {

		// See if we've alread run this query and cached the results
		$sidebars = get_transient( 'sb_custom_sidebars' );

		// If no cached results, run a query for custom sidebars
		if ( empty( $sidebars ) ) {

			// Get all sidebar posts
			$sidebars = get_posts( array(
				'post_type' => 'sidebar',
				'nopaging'  => true,
				'orderby'   => 'date',
				'order'     => 'ASC',
			) );

			// Cache our query for 30 days
			set_transient( 'sb_custom_sidebars', $sidebars, 30 * DAY_IN_SECONDS );

		}

		// Return our scoped sidebars (cast to an array for good measure)
		return (array) maybe_unserialize( $sidebars );
	}


	/**
	 * Registers all custom sidebars
	 *
	 * @since 2.5.0
	 */
	function register_custom_sidebars() {

		// Get our custom sidebars
		$custom_sidebars = $this->get_custom_sidebars();

		// Loop through each and register it
		foreach( $custom_sidebars as $sidebar ) {
			$this->register_sidebar( array(
				'name'        => $sidebar->post_title,
				'id'          => $sidebar->post_name,
				'description' => get_post_meta( $sidebar->ID, '_sidebar_description', true ),
				'editable'    => 0
			) );
		}

	}

	/**
	 * Get each assigned sidebar location
	 *
	 * Return an array keyed with post/term ID containing
	 * each location that has a custom assigned sidebar.
	 *
	 * @since  3.0.0
	 * @return array Keyed location array
	 */
	function get_custom_sidebar_locations() {

		$sidebars = get_transient( 'sb_custom_sidebar_locations' );

		// If we don't have a transient, rebuild our array
		if ( empty( $sidebars ) || ! is_array( $sidebars ) ) {

			// Grab our custom sidebars
			$sidebar_posts = $this->get_custom_sidebars();

			// Setup our custom sidebars array
			$sidebars = array();

			// If we have any sidebars, add the relevant ones to our array
			if ( ! empty( $sidebar_posts ) ) {
				foreach ( $sidebar_posts as $sidebar ) {
					// Grab our sidebar_id and location
					$name     = $sidebar->post_name;
					$location = get_post_meta( $sidebar->ID, '_sidebar_replaced', true);
					$posts    = (array) maybe_unserialize( get_post_meta( $sidebar->ID, '_post', true ) );
					$terms    = (array) maybe_unserialize( get_post_meta( $sidebar->ID, '_tax', true ) );
					$keys     = array_merge( $posts, $terms );

					// Loop through every key Store all the associated IDs in our multidimensional array
					foreach ( $keys as $key ) {
						$sidebars[$key]['locations'][$location] = $name;
					}

				}
			}

			// Cache our location data for 30 days
			set_transient( 'sb_custom_sidebar_locations', $sidebars, 30 * DAY_IN_SECONDS );
		}

		return (array) maybe_unserialize( $sidebars );
	}

	/**
	 * Potentialy replace the default sidebar for a given location
	 *
	 * @since  2.5.0
	 * @param  string $sidebar  The sidebar being rendered
	 * @return string           The sidebar to rendered
	 */
	function maybe_replace_current_sidebar( $sidebar ) {
		global $post, $wp_query;

		// Grab our assigned locations
		$custom_sidebars = $this->get_custom_sidebar_locations();

		// If we actually have custom sidebars, lets look deeper
		if ( ! empty( $custom_sidebars ) ) {

			// Determine which key we're testing based on what we're viewing
			// For our special cases we'll want to return early
			if ( array_key_exists( 'Home', $custom_sidebars ) && is_front_page() ) { $key = 'Home'; }
			elseif ( array_key_exists( 'all-Posts', $custom_sidebars ) && is_single() && get_post_type() == 'post' ) { $key = 'all-Posts'; }
			elseif ( array_key_exists( 'all-Pages', $custom_sidebars ) && is_page() ) { $key = 'all-Pages'; }
			elseif ( array_key_exists( 'all-'.$post->post_type, $custom_sidebars ) && is_single() && get_post_type() == $post->post_type ) { $key = 'all-'.$post->post_type; }
			elseif ( array_key_exists( 'all-category', $custom_sidebars ) && is_category() ) { $key = 'all-category'; }
			elseif ( array_key_exists( 'all-tag', $custom_sidebars ) && is_tag() ) { $key = 'all-tag'; }
			elseif ( is_home() ) { $key = $wp_query->queried_object_id; } // This catches the blog page when front page is set to a static page
			elseif ( is_category() ) { $key = get_query_var('cat'); }
			elseif ( is_tag() ) { $key = get_query_var('tag_id'); }
			elseif ( is_tax() ) { $key = get_query_var('term_id'); }
			else { $key = $post->ID; }

			// If we have a custom sidebar for this page, and for this location, use it
			if (
				array_key_exists( $key, $custom_sidebars )
				&& array_key_exists( $sidebar, $custom_sidebars[$key]['locations'] )
			) {
				$sidebar = $custom_sidebars[$key]['locations'][$sidebar];
			}

		}

		// Finally, return the appropriate sidebar
		return $sidebar;
	}
}
$sb_custom_sidebars = new SB_Custom_Sidebars;

/**
 * Custom walker class for taxonomy lists
 *
 * @since 2.5.0
 * @uses Walker_Nav_Menu
 */
class SB_Sidebars_Checklist extends Walker_Nav_Menu  {

	/**
	 * @see Walker::start_el()
	 * @since 2.5.0
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