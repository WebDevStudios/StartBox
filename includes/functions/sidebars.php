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
		foreach ( $this->sidebars as $id => $info ) {

			register_sidebar(array(
				'name'			=> esc_html( $info['name'] ),
				'id'			=> $id,
				'description'	=> esc_html( $info['description'] ),
				'editable'		=> intval( $info['editable'] ),
				'before_widget'	=>	"\n\t\t\t" . '<li id="%1$s" class="widget %2$s">',
				'after_widget'	=>	"\n\t\t\t</li>\n",
				'before_title'	=>	"\n\t\t\t\t". '<h3 class="widget-title"><span class="widget-title-left"><span class="widget-title-right">',
				'after_title'	=>	'</span></span></h3>'."\n"
			));

		}
	}

	/**
	 * Registers all default sidebars (don't override this)
	 *
	 * @since 2.5
	 */
	function default_sidebars() {
		$this->register_sidebar( array( 'name' => 'Primary Sidebar', 'id' => 'primary_widget_area', 'description' => __('This is the primary sidebar when using two- or three-column layouts.', 'startbox') , 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Secondary Sidebar', 'id' => 'secondary_widget_area', 'description' => __('This is the secondary sidebar for three-column layouts.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Home Featured', 'id' => 'home_featured', 'description' => __('These widgets will appear above the content on the homepage.', 'startbox'), 'editable' => 0 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 1', 'id' => 'footer_widget_area_1', 'description' => __('This is the first footer column. Use this before using any other footer columns.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 2', 'id' => 'footer_widget_area_2', 'description' => __('This is the second footer column. Only use this after using Footer Aside 1.', 'startbox'), 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 3', 'id' => 'footer_widget_area_3', 'description' => __('This is the third footer column. Only use this after using Footer Aside 2.', 'startbox') , 'editable' => 1 ) );
		$this->register_sidebar( array( 'name' => 'Footer Aside 4', 'id' => 'footer_widget_area_4', 'description' => __('This is the last footer column. Only use this after using all other columns.', 'startbox'), 'editable' => 1 ) );
	}

	/**
	 * Registers all custom sidebars (don't override this)
	 *
	 * @since 2.5
	 */
	function custom_sidebars() {

		$get_posts = new WP_Query( array(
			'order' => 'ASC',
			'orderby' => 'date',
			'post_type' => 'sidebar',
			'posts_per_page' => 100
		));

		while ( $get_posts->have_posts() ) : $get_posts->the_post();
			global $post;
			$name = get_the_title();
			$id = $post->post_name;
			$description = get_post_meta($post->ID, '_sidebar_description', true);
			$this->register_sidebar( array( 'name' => $name, 'id' => $id, 'description' => $description, 'editable' => 0 ) );
		endwhile;
		
		wp_reset_postdata();

	}


	/**
	 * Register a sidebar (don't override this)
	 *
	 * @since 2.5
	 *
	 * @param array $args an array of arguments for naming and identifying a sidebar
	 */
	function register_sidebar( $args = '' ) {

		// Setup our defaults (all null, for the most part)
		$defaults = array(
			'name'			=> '',
			'id'			=> '',
			'description'	=> '',
			'editable'		=> 1	// Makes this sidebar replaceable via the StartBox Easy Sidebars extension
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
	 * @since 2.5
	 *
	 * @param string $id the unique ID for the sidebar to unregister
	 */
	function unregister_sidebar( $id ) {
		if ( isset($this->sidebars[$id]) )
			unset($this->sidebars[$id]);
	}

	/**
	 * Render markup and action hooks for a given sidebar (override this to customize your markup)
	 *
	 * @since 2.5
	 *
	 * @param string $location the unique ID to give the container for this sidebar
	 * @param string $sidebar the ID of the sidebar to attach to this location by default
	 * @param string $classes additional custom classes to add to the container for this sidebar
	 */
	function do_sidebar( $location = null, $sidebar = null, $classes = null ) {

		// Grab the stored post types and taxonomies that will display a custom sidebar
		$post_type = $this->get_custom_sidebars('post_type');
		$tax = $this->get_custom_sidebars('taxonomy');

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
	 * Loop through all custom sidebars, store them as a multi-deminsional array
	 * for each post type and taxonomy. Uses transients to reduce queries. (don't override this)
	 *
	 * @since 2.5
	 * @param string $return the array to return (accepts 'post_type' and 'taxonomy')
	 */
	function get_custom_sidebars( $return ) {

		// See if we've alread run this query and cached the results
		$post_type = get_transient('sb_sidebars_post_type');
		$tax = get_transient('sb_sidebars_tax');

		// If no cached results, run a query for custom sidebars
		if ( ! $post_type || !$tax ) {
			$stored_post_type = $post_type;	// prevent needless updates, see below (kevinB)
			$stored_tax = $tax;

			// prevent excess queries when no record stored (kevinB)
			// NOTE: these options should also be set to autoload
			if ( ! is_array($post_type) )
				set_transient('sb_sidebars_post_type', array(), 86400 );

			if ( ! is_array($tax) )
				set_transient('sb_sidebars_tax', array(), 86400 );

			static $all_post_type, $all_tax; // avoid multiple queries for all posts/taxonomies (kevinB)
			if ( ! isset($all_post_type) ) {

				global $post;

				// Cache the current query, just for good measure
				$temp = $post;

				// Get all sidebar posts
				$get_posts = new WP_Query( array( 'post_type' => 'sidebar', 'posts_per_page' => -1 ) );
				$all_post_type = $all_tax = array();

				// If there are no sidebars, return false
				if ( !$get_posts->have_posts() ) {
					return false;
				}

				// If there are sidbars, loop through them all and store them in arrays for each post and tax type
				while ( $get_posts->have_posts() ) : $get_posts->the_post();

					global $post;

					// The sidebar id is the post slug
					$sidebar_id = $post->post_name;

					// The location for this sidebar is based on which sidebar it replaces
					$location = get_post_meta( $post->ID, '_sidebar_replaced', true);

					// Grab all the saved posts and taxonomies that should use this sidebar
					$posts = (array) maybe_unserialize( get_post_meta( $post->ID, '_post', true ) );
					$taxes = (array) maybe_unserialize( get_post_meta( $post->ID, '_tax', true ) );

					// Grab all posts associated with this sidebar, add each to the $post_type array
					foreach ( $posts as $post_id ) {
						$all_post_type[$post_id] = array( 'location' => $location, 'sidebar' => $sidebar_id );
					}

					// Grab all taxonomies associated with this sidebar, add each to the $tax array
					foreach ( $taxes as $tax_id ) {
						$all_tax[$tax_id] = array( 'location' => $location, 'sidebar' => $sidebar_id );
					}

				endwhile;

				// Restore the cached query, just incase
				$post = $temp;

			} // endif all_post_type and all_tax cached as static vars here

			$post_type = $all_post_type;
			$tax = $all_tax;

			// Store transient data for the post types and taxonomies for use later
			if ( $stored_post_type != $post_type )	// otherwise options are re-updated on every site load (kevinB)
				set_transient('sb_sidebars_post_type', $post_type, 259200); // cache for three days (259200)

			if ( $stored_tax != $tax )
				set_transient('sb_sidebars_tax', $tax, 259200); // cache for three days (259200)

		} // end if any transient-cached results

		// Return either post_type or taxonomy, based on what was requested
		if ( $return == 'post_type' )
			return $post_type;
		elseif ( $return == 'taxonomy' )
			return $tax;
	}

	/**
	 * Check if a custom sidebar exists to replace the default for a given location. Don't override this.
	 *
	 * @since 2.5
	 * @param string $location the registered location to check
	 * @param string $sidebar the sidebar to (maybe) replace
	 */
	function maybe_replace_current_sidebar( $location, $sidebar ) {
		global $post, $wp_query;
		$post_type = (array)$this->get_custom_sidebars('post_type');
		$tax = (array)$this->get_custom_sidebars('taxonomy');

		// Set the ID for the page/post to retrive. If a sidebar is set for all- Pages, Posts, Categories or Tags use it instead.
		if ( is_front_page() && array_key_exists( 'Home', $post_type ) ) { $pid = 'Home'; }
		elseif ( is_home() ) { $pid = $wp_query->queried_object_id; } // when the home page is not the front page (e.g. posts display on a page per Settings > Reading)
		elseif ( is_single() && array_key_exists( 'all-Posts', $post_type ) ) { $pid = 'all-Posts'; }
		elseif ( is_page() && array_key_exists( 'all-Pages', $post_type) ) { $pid = 'all-Pages'; }
		elseif ( is_category() && array_key_exists( 'all-category', $tax) ) { $pid = 'all-category'; }
		elseif ( is_tag() && array_key_exists( 'all-tag', $tax) ) { $pid = 'all-tag'; }
		elseif ( is_category() ) { $pid = get_query_var('cat'); }
		elseif ( is_tag() ) { $pid = get_query_var('tag_id'); }
		else { $pid = $post->ID; }

		// Confirm which sidebar to output based on current front-end view
		if ( is_front_page() && array_key_exists( $pid, $post_type ) && $sidebar == $post_type[$pid]['location'] ) {
			$sidebar = $post_type[$pid]['sidebar'];
		} elseif ( ( is_singular() || is_home() ) && array_key_exists( $pid, $post_type ) && $sidebar == $post_type[$pid]['location'] ) {
			$sidebar = $post_type[$pid]['sidebar'];
		} elseif ( ( is_category() || is_tag() ) && array_key_exists( $pid, $tax ) && $sidebar == $tax[$pid]['location']) {
			$sidebar = $tax[$pid]['sidebar'];
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
 *
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
 *
 * @param string $id the ID of the sidebar to unregister
 */
function sb_unregister_sidebar( $id ) {
	global $sb_sidebars;
	$sb_sidebars->unregister_sidebar( $id );
}


/**
 * Wrapper Function for SB_Sidebars::do_sidebar()
 *
 * @since 2.5
 *
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