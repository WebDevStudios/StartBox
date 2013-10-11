<?php
/**
 * StartBox Sidebars
 *
 * A class structure for handling sidebar registration,
 * output, markup, the works.
 *
 * @package StartBox
 * @subpackage Sidebars
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports sidebars, skip the rest if not
if ( ! current_theme_supports( 'sb-sidebars' ) )
	return;

/**
 * This is the main SB Sidebars class.
 *
 * You can extend this within your theme to alter the widget markup
 *
 * @subpackage Classes
 * @since 2.5.0
 */
class SB_Sidebars {

	/**
	 * Variable for storing all registered sidebars, don't override this.
	 *
	 * @since 2.5.0
	 * @var array
	 */
	public $registered_sidebars = array();

	/**
	 * Auto-load default and custom sidebars. Don't override this.
	 *
	 * @since 2.5.0
	 */
	function __construct() {

		// Register and activate all the sidebars
		add_action( 'init', array( $this, 'register_default_sidebars') );

	}

	/**
	 * Registers all default sidebars (don't override this)
	 *
	 * @since 2.5.0
	 */
	function register_default_sidebars() {

		// Grab the default supported sidebars
		$supported_sidebars = get_theme_support( 'sb-sidebars' );

		// If there aren't any sidebars, skip the rest
		if ( empty( $supported_sidebars ) )
			return;

		// Loop through each supported sidebar and register it
		foreach ( $supported_sidebars[0] as $key => $sidebar ) {
			$this->register_sidebar( $sidebar );
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
			'id'          => '',
			'name'        => '',
			'description' => '',
			'class'       => '',
			'editable'    => 1 // Makes this sidebar replaceable via SB Custom Sidebars extension
		);
		$sidebar = wp_parse_args( $args, $defaults );

		// Rudimentary sanitization for editable var
		$editable = ( $sidebar['editable'] ) ? 1 : 0;

		// Register the sidebar in WP
		register_sidebar( apply_filters( 'sb_sidebars_register_sidebar', array(
			'id'            => esc_attr( $sidebar['id'] ),
			'name'          => esc_attr( $sidebar['name'] ),
			'description'   => esc_attr( $sidebar['description'] ),
			'class'         => esc_attr( $sidebar['class'] ),
			'editable'      => absint( $sidebar['editable'] ),
			'before_widget' => apply_filters( 'sb_sidebars_before_widget', '<aside id="%1$s" class="widget %2$s">', $sidebar['id'], $sidebar ),
			'after_widget'  => apply_filters( 'sb_sidebars_after_widget', '</aside><!-- #%1$s -->', $sidebar['id'], $sidebar ),
			'before_title'  => apply_filters( 'sb_sidebars_before_title', '<h1 class="widget-title">', $sidebar['id'], $sidebar ),
			'after_title'   => apply_filters( 'sb_sidebars_after_title', '</h1>', $sidebar['id'], $sidebar )
		), $sidebar ) );

		// Add the sidebar to our registered array
		$this->registered_sidebars[$sidebar['id']] = $sidebar;		
		
	}

	/**
	 * Render markup and action hooks for a given sidebar (override this to customize your markup)
	 *
	 * @since 2.5.0
	 * @param string $sidebar  The default sidebar to render
	 * @param string $class  Additional CSS classes to apply to the container
	 */
	function do_sidebar( $sidebar = null, $classes = null ) {

		// Cache the sidebar location we're rendering
		$location = $sidebar;		

		// Maybe replace the default sidebar with a custom sidebar
		$sidebar = apply_filters( 'sb_do_sidebar', $sidebar );

		// If the sidebar has widgets, or an action attached to it, commence output
		if ( is_active_sidebar( $sidebar ) || has_action( "sb_no_{$location}_widgets" ) ) {

			do_action( "before_{$location}" );
			echo '<div id="' . esc_attr( $location ) . '" class="widget-area sidebar ' . esc_attr( $location ) . ' ' . esc_attr( $classes ) . '" role="complimentary" itemscope itemtype="http://schema.org/WPSideBar">';
			do_action( "before_{$location}_widgets" );

			if ( ! dynamic_sidebar( $sidebar ) )
				do_action( "no_{$location}_widgets" );

			do_action( "after_{$location}_widgets" );
			echo '</div><!-- #' . esc_attr( $location ) . ' .' . esc_attr( $classes ) . ' -->';
			do_action( "after_{$location}" );
		}
	}

}
$GLOBALS['startbox']->sidebars = new SB_Sidebars;

/**
 * Wrapper Function for SB_Sidebars::register_sidebar()
 *
 * @since 2.5.2
 * @param array $args An array of sidebar registration arguments (id, name, description, editable)
 */
function sb_register_sidebar( $args = array() ) {
	global $startbox;
	$startbox->sidebars->register_sidebar( $args );
}

/**
 * Wrapper Function for SB_Sidebars::do_sidebar()
 *
 * @since 2.5.0
 * @param string $sidebar The default sidebar to render
 * @param string $classes Additional CSS classes to apply to the container
 */
function sb_do_sidebar( $sidebar = null, $classes = null ) {
	global $startbox;
	$startbox->sidebars->do_sidebar( $sidebar, $classes );
}

/**
 * Check if a sidebar is editable
 *
 * @since  3.0.0
 * @param  string $sidebar The sidebar to check
 * @return bool            True if sidebar is editable, false otherwise
 */
function sb_is_sidebar_editable( $sidebar = null ) {
	global $startbox;

	// If the editable field is empty, it is not editable
	if ( empty( $startbox->sidebars->registered_sidebars[$sidebar]['editable'] ) )
		return false;
	else
		return true;
}