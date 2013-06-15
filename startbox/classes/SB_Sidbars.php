<?php
/**
 * StartBox Sidebars
 *
 * A class structure for handling sidebar registration,
 * output, markup, the works.
 *
 * @package StartBox
 * @subpackage Classes
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * This is the main SB Sidebars class. You can extend and override this via child theme to alter your own widget markup
 */
class SB_Sidebars {

	// Variable for storing all registered sidebars, don't override this.
	public $sidebars = array();

	// Magic method that auto-loads default sidebars and custom sidebars. Don't override this.
	function SB_Sidebars() {

		// Register and activate all the sidebars
		add_action( 'after_setup_theme', array( $this, 'default_sidebars'), 10 );
		add_action( 'after_setup_theme', array( $this, 'custom_sidebars'), 11 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// Hook for other functions
		do_action( 'sb_sidebars_init' );

	}

	// Activate all sidebars, override this to customize sidebar markup
	function widgets_init() {

		// If there aren't any sidebars, skip the rest
		if ( !$this->sidebars || empty($this->sidebars) ) return;

		// Otherwise, lets register all of them
		foreach ( $this->sidebars as $sidebar_id => $sidebar_info ) {

			register_sidebar( apply_filters( 'sb_register_sidebar_defaults', array(
				'name'          => esc_attr( $sidebar_info['name'] ),
				'id'            => esc_attr( $sidebar_id ),
				'description'   => esc_attr( $sidebar_info['description'] ),
				'editable'      => absint( $sidebar_info['editable'] ),
				'before_widget' => "\n\t\t\t" . '<li id="%1$s" class="widget %2$s">',
				'after_widget'  => "\n\t\t\t</li>\n",
				'before_title'  => "\n\t\t\t\t". '<h3 class="widget-title"><span class="widget-title-left"><span class="widget-title-right">',
				'after_title'   => '</span></span></h3>'."\n"
			), $sidebar_id, $sidebar_info ) );

		}
	}

	/**
	 * Registers all default sidebars (don't override this)
	 *
	 * @since 2.5.0
	 */
	function default_sidebars() {
		$this->register_sidebar( array( 'name' => 'Primary Sidebar', 'id' => 'primary', 'description' => __('This is the primary sidebar when using two- or three-column layouts.', 'startbox') , 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Secondary Sidebar', 'id' => 'secondary', 'description' => __('This is the secondary sidebar for three-column layouts.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Home Featured', 'id' => 'home_featured', 'description' => __('These widgets will appear above the content on the homepage.', 'startbox'), 'editable' => 0 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 1', 'id' => 'footer_widget_area_1', 'description' => __('This is the first footer column. Use this before using any other footer columns.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 2', 'id' => 'footer_widget_area_2', 'description' => __('This is the second footer column. Only use this after using Footer Aside 1.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 3', 'id' => 'footer_widget_area_3', 'description' => __('This is the third footer column. Only use this after using Footer Aside 2.', 'startbox') , 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 4', 'id' => 'footer_widget_area_4', 'description' => __('This is the last footer column. Only use this after using all other columns.', 'startbox'), 'editable' => 1 ) );
	}

	/**
	 * Registers all custom sidebars (don't override this)
	 *
	 * @since 2.5.0
	 */
	function custom_sidebars() {

		$custom_sidebars = get_posts( array(
			'order'          => 'ASC',
			'orderby'        => 'date',
			'post_type'      => 'sidebar',
			'posts_per_page' => -1
		) );

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
	 * Register a sidebar (don't override this)
	 *
	 * @since 2.5.0
	 * @param array $args an array of arguments for naming and identifying a sidebar
	 */
	function register_sidebar( $args = '' ) {

		// Setup our defaults (all null, for the most part)
		$defaults = array(
			'name'        => '',
			'id'          => '',
			'description' => '',
			'editable'    => 1   // Makes this sidebar replaceable via the StartBox Easy Sidebars extension
		);
		extract( wp_parse_args( $args, $defaults) );

		// Rudimentary sanitization for editable var
		$editable = ($editable) ? 1 : 0;

		// If the sidebar doesn't already exist, register it
		if ( !isset($this->sidebars[$id]) )
			$this->sidebars[$id] = array( 'name' => $name, 'id' => $id, 'description' => $description, 'editable' => $editable );
	}

	/**
	 * Unregister a sidebar (don't override this)
	 *
	 * @since 2.5.0
	 * @param string $id the unique ID for the sidebar to unregister
	 */
	function unregister_sidebar( $id ) {
		if ( isset($this->sidebars[$id]) )
			unset($this->sidebars[$id]);
	}

	/**
	 * Render markup and action hooks for a given sidebar (override this to customize your markup)
	 *
	 * @since 2.5.0
	 * @param string $location the unique ID to give the container for this sidebar
	 * @param string $sidebar the ID of the sidebar to attach to this location by default
	 * @param string $classes additional custom classes to add to the container for this sidebar
	 */
	function do_sidebar( $location = null, $sidebar = null, $classes = null ) {

		// Maybe replace the default sidebar with a custom sidebar
		$sidebar = $this->maybe_replace_current_sidebar( $location, $sidebar );

		// If the sidebar has widgets, or an action attached to it, commence output
		if ( is_sidebar_active($sidebar) || has_action("sb_no_{$location}_widgets") ) { ?>

			<?php do_action( "sb_before_{$location}" ); ?>
			<div id="<?php echo esc_attr( $location ); ?>" class="aside <?php echo $location; ?>-aside<?php if ($classes) { echo ' ' . $classes; }?>">
				<?php do_action( "sb_before_{$location}_widgets" ); ?>
				<ul class="xoxo">
					<?php if ( !dynamic_sidebar($sidebar) ) { do_action( "sb_no_{$location}_widgets" ); }?>
				</ul>
				<?php do_action( "sb_after_{$location}_widgets" ); ?>
		   </div><!-- #<?php echo $location; ?> .aside-<?php echo $location; ?> .aside -->
		   <?php do_action( "sb_after_{$location}" ); ?>

		<?php }
	}

	/**
	 * Get all custom sidebars registered for a given scope (don't override this)
	 *
	 * @since  2.5.0
	 * @param  string $scope The query scope we're calling (accepts 'post_type' and 'taxonomy')
	 * @return array         An array of registered sidebars for the given scope
	 */
	function get_custom_sidebars() {

		// See if we've alread run this query and cached the results
		$sidebars = get_transient( 'sb_custom_sidebars' );

		// If no cached results, run a query for custom sidebars
		if ( empty( $sidebars ) ) {

			// Get all sidebar posts
			$sidebar_posts = get_posts( array( 'post_type' => 'sidebar', 'nopaging' => true ) );

			// Setup our custom sidebars array
			$sidebars = array();

			// If we have sidebars, loop through each and add relevant ones to our array
			if ( ! empty( $sidebar_posts ) ) { foreach ( $sidebar_posts as $sidebar ) {

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

			} }

			// Cache our query for one week
			set_transient( 'sb_custom_sidebars', $sidebars, (60*60*24*7) );

		}

		// Return our scoped sidebars (cast to an array for good measure)
		return (array) $sidebars;
	}

	/**
	 * Check if a custom sidebar exists to replace the default for a given location. Don't override this.
	 *
	 * @since 2.5.0
	 * @param string $location the registered location to check
	 * @param string $sidebar the sidebar to (maybe) replace
	 */
	function maybe_replace_current_sidebar( $location, $sidebar ) {

		// Grab our globals (so we know what we're querying)
		global $post, $wp_query;

		// Grab our custom sidebars
		$custom_sidebars = $this->get_custom_sidebars();

		// If we actually have custom sidebars, lets look deeper
		if ( !empty( $custom_sidebars ) ) {

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
				&& array_key_exists( $location, $custom_sidebars[$key]['locations'] )
			) {
				$sidebar = $custom_sidebars[$key]['locations'][$location];
			}

		}

		// Finally, return the appropriate sidebar
		return $sidebar;
	}

}

// Initialize the SB_Sidebars class, store it to the global $sb_sidebars variable
global $sb_sidebars;
$sb_sidebars = new SB_Sidebars;


/**
 * Wrapper Function for SB_Sidebars::register_sidebar()
 *
 * @since 2.5.2
 * @param string $name the display name for this sidebar
 * @param string $id the unique ID for this sidebar
 * @param string $description a short description for this sidebar
 * @param boolean $editable if true this sidebar can be overridden via custom sidebars (Default: false)
 */
function sb_register_sidebar( $name = null, $id = null, $description = null, $editable = 0 ) {
	global $sb_sidebars;
	$sb_sidebars->register_sidebar( array( 'name' => $name, 'id' => $id, 'description' => $description, 'editable' => $editable ) );
}

/**
 * Wrapper Function for SB_Sidebars::unregister_sidebar()
 *
 * @since 2.5.2
 * @param string $id the ID of the sidebar to unregister
 */
function sb_unregister_sidebar( $id ) {
	global $sb_sidebars;
	$sb_sidebars->unregister_sidebar( $id );
}


/**
 * Wrapper Function for SB_Sidebars::do_sidebar()
 *
 * @since 2.5.0
 * @param string $location the unique ID to give the container for this sidebar
 * @param string $sidebar the ID of the sidebar to attach to this location by default
 * @param string $classes additional custom classes to add to the container for this sidebar
 */
function sb_do_sidebar( $location = null, $sidebar = null, $classes = null ) {
	global $sb_sidebars;
	$sb_sidebars->do_sidebar( $location, $sidebar, $classes );
}



/**
 * Check for widgets in widget-ready areas to confirm if sidebar is active.
 *
 * @since 2.3.6
 */
if ( !function_exists('is_sidebar_active') ) {
	function is_sidebar_active( $index ) {
		global $wp_registered_sidebars;
		$widgetcolums = wp_get_sidebars_widgets();
		if ( isset( $widgetcolums[$index] ) && $widgetcolums[$index] == true ) return true;
		return false;
	}
}


/**
 * StartBox Sidebar Manager
 *
 * Create additional sidebars to replace any default sidebars in StartBox for any post-type or taxonomy.
 * Uses Custom Post Types to handle sidebar registration. Metabox functionality originally lifted
 * from WP's Custom Menu admin functions.
 *
 * @package StartBox
 * @subpackage Add-ons
 * @since 2.5.0
 */

// Check to see if current theme supports sidebars, skip the rest if not
if (!current_theme_supports( 'sb-sidebars' )) return;

/**
 * Creates Sidebar post type
 *
 * @since 2.5.0
 */
function sb_sidebars_init() {
	// Add custom post type
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
		'register_meta_box_cb' => 'sb_sidebars_metabox_setup'
	) );

}
add_action( 'init', 'sb_sidebars_init' );

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
function sb_sidebar_default_slug( $data , $postarr ) {

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
add_filter( 'wp_insert_post_data' , 'sb_sidebar_default_slug' , '10', 2 );

/**
 * Include scripts to make post editor run smoothly
 *
 * @since 2.5.0
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
 * @since 2.5.0
 */
function sb_sidebars_bar_init() {
	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array(
		'id'     => 'sb-sidebars',
		'parent' => 'appearance',
		'title'  => __('Sidebars', 'startbox'),
		'href'   => admin_url( 'edit.php?post_type=sidebar' )
	) );
}
add_action( 'wp_before_admin_bar_render', 'sb_sidebars_bar_init' );


/**
 * Add a link on the Widgets page to the Sidebars page
 *
 * @since 2.5.0
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
 * @since 2.5.0
 **/
function sb_sidebars_metabox_setup() {

	// Select box for sidebars to replace
	add_meta_box( "sidebar-select", 'Select Sidebar to Replace', 'sb_sidebars_select_meta_box', 'sidebar', 'normal', 'default' );

	// Description box
	add_meta_box( "sidebar-description", 'Describe this Sidebar', 'sb_sidebars_description_meta_box', 'sidebar', 'normal', 'default' );

	// Shortcode metabox
	add_meta_box( "sidebar-shortcode", 'Shortcode', 'sb_sidebars_shortcode_metabox', 'sidebar', 'side', 'default' );

	// Get all post types that are set to show in nav menus
	if ( $post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'object' ) ) {
		foreach ( $post_types as $post_type ) {
			$post_type = apply_filters( 'sb_sidebars_meta_box_object', $post_type );
			if ( $post_type ) {
				$id = $post_type->name;
				add_meta_box( "sidebar-post-{$id}", $post_type->labels->name, 'sb_sidebars_post_type_meta_box', 'sidebar', 'normal', 'default', $post_type );
			}
		}
	}

	// Get all taxonomies that are set to show in nav menus
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
 * @since 2.5.0
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
 * @since 2.5.0
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
 * @since 2.5.0
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
 * @since 2.5.0
 *
 * @param string $object Not used.
 * @param string $post_type The post type object.
 */
function sb_sidebars_post_type_meta_box( $post, $post_type ) {

	$post_type_name = $post_type['args']->name;

	// Arguments for quering posts
	$args = array(
		'order'                  => 'ASC',
		'orderby'                => 'title',
		'posts_per_page'         => -1,
		'post_type'              => $post_type_name,
		'suppress_filters'       => true,
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
						$output = '<li><label><input type="checkbox" ' . checked( in_array( 'all-' . $post_type['args']->name, $selected), true, false ) . ' name="post[all-' . $post_type['args']->name .']" value="true"/> All ' . $post_type['args']->labels->name . ' (includes all future ' . $post_type['args']->labels->name . ')</label></li>';
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
 * @since 2.5.0
 *
 * @param string $object Not used.
 * @param string $taxonomy The taxonomy object.
 */
function sb_sidebars_taxonomy_meta_box( $post, $taxonomy ) {

	$taxonomy_name = $taxonomy['args']->name;

	$args = array(
		'child_of'                 => 0,
		'exclude'                  => '',
		'hide_empty'               => false,
		'hierarchical'             => 1,
		'include'                  => '',
		'include_last_update_time' => false,
		'order'                    => 'ASC',
		'orderby'                  => 'name',
		'pad_counts'               => false,
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

/**
 * Save all the post data
 *
 * @since 2.5.0
 */
function sb_sidebars_save( $post_id ) {
	// Verify we should actually be saving any data
	if (
		! sb_verify_post_type( 'sidebar' )               // If it's not a sidebar post type,
		|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE // or if it's autosaving,
		|| ! isset($_POST['sidebar_replaced'])           // or we don't have some postdata,
		|| ! current_user_can('edit_theme_options')      // or the current user can't edit themes,
		|| ! isset( $_POST['_sb_sidebars_nonce'] )       // or the NONCE is not set,
		|| ! wp_verify_nonce( $_POST['_sb_sidebars_nonce'], 'sb-sidebar-update' ) // or the NONCE is invalid
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
	delete_transient( 'sb_custom_sidebars' );

	return $post_id;
}
add_action( 'save_post', 'sb_sidebars_save' );

/**
 * Delete cached sidebars when a sidebar is deleted
 *
 * @since 2.5.0
 */
function sb_sidebars_delete($post_id) {
	$post = get_post($post_id);

	// Verify we're actually deleting a sidebar
	if ( $post->post_type == 'sidebar' ) {
        // Delete transient data (used to cache all sidebars for front-end display)
		delete_transient( 'sb_custom_sidebars' );
	}

}
add_action( 'trashed_post', 'sb_sidebars_delete' );

/**
 * Filter the "post updated" messages
 *
 * @since 2.5.0
 */
function sb_sidebars_update_messages( $messages ) {
	$messages['sidebar']['1'] = sprintf( __('Sidebar saved. <a href="%s">Give it some widgets</a>', 'startbox'), esc_url( admin_url( 'widgets.php' ) ) );
	$messages['sidebar']['6'] = sprintf( __('Sidebar saved. <a href="%s">Give it some widgets</a>', 'startbox'), esc_url( admin_url( 'widgets.php' ) ) );
	return $messages;
}
add_filter( 'post_updated_messages', 'sb_sidebars_update_messages' );
