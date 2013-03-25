<?php
/**
 * StartBox Sidebars
 *
 * A class structure for handling sidebar registration, output, markup, the works.
 *
 * @package StartBox
 * @subpackage Functions
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
			$sidebar_posts = get_posts( array( 'post_type' => 'sidebar', 'no_paging' => true ) );

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
			if ( array_key_exists( 'Home', $custom_sidebars ) && is_front_page() ) { $key = 'Home'; }
			elseif ( array_key_exists( 'all-Posts', $custom_sidebars ) && is_single() ) { $key = 'all-Posts'; }
			elseif ( array_key_exists( 'all-Pages', $custom_sidebars ) && is_page() ) { $key = 'all-Pages'; }
			elseif ( array_key_exists( 'all-category', $custom_sidebars ) && is_category() ) { $key = 'all-category'; }
			elseif ( array_key_exists( 'all-tag', $custom_sidebars ) && is_tag() ) { $key = 'all-tag'; }
			elseif ( is_home() ) { $key = $wp_query->queried_object_id; } // Refers to the blog page if front page is set to static page
			elseif ( is_category() ) { $key = get_query_var('cat'); }
			elseif ( is_tag() ) { $key = get_query_var('tag_id'); }
			elseif ( is_tax() ) { $key = get_query_var('term_id'); }
			else { $key = $post->ID; }

			// Determine if we should override the current sidebar
			if (
				array_key_exists( $key, $custom_sidebars )
				&& array_key_exists( $location, $custom_sidebars[$key]['locations'] )
			) {
				$sidebar = $custom_sidebars[$key]['locations'][$location];
			}

		}

		// Finally, return the given sidebar
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