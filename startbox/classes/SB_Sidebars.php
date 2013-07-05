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

// Check to see if current theme supports sidebars, skip the rest if not
if ( ! current_theme_supports( 'sb-sidebars' ) )
	return;

/**
 * This is the main SB Sidebars class.
 *
 * You can extend this within your theme to alter the widget markup
 *
 * @since 2.5.0
 */
class SB_Sidebars {

	/**
	 * Variable for storing all registered sidebars, don't override this.
	 *
	 * @since 2.5.0
	 * @var array
	 */
	public $sidebars = array();

	/**
	 * Auto-load default and custom sidebars. Don't override this.
	 *
	 * @since 2.5.0
	 */
	function __construct() {

		// Grab the default supported sidebars
		$supported_sidebars = get_theme_support( 'sb-sidebars' );
		$this->sidebars = $supported_sidebars[0];

		// Register and activate all the sidebars
		add_action( 'after_setup_theme', array( $this, 'register_default_sidebars') );
		add_action( 'init', array( $this, 'widgets_init' ) );

		// Available hook for other functions
		do_action( 'sb_sidebars_init' );

	}

	/**
	 * Activate all registered sidebars.
	 *
	 * @since 2.5.0
	 */
	function widgets_init() {

		// If there aren't any sidebars, skip the rest
		if ( empty( $this->sidebars ) )
			return;

		// Otherwise, lets register all of them
		foreach ( $this->sidebars as $sidebar_id => $sidebar_info ) {

			register_sidebar( apply_filters( 'sb_sidebars_register_sidebar', array(
				'id'            => esc_attr( $sidebar_id ),
				'name'          => esc_attr( $sidebar_info['name'] ),
				'description'   => esc_attr( $sidebar_info['description'] ),
				'editable'      => absint( $sidebar_info['editable'] ),
				'before_widget' => apply_filters( 'sb_sidebars_before_widget', '<li id="%1$s" class="widget %2$s">', $sidebar_id, $sidebar_info ),
				'after_widget'  => apply_filters( 'sb_sidebars_after_widget', '</li>', $sidebar_id, $sidebar_info ),
				'before_title'  => apply_filters( 'sb_sidebars_before_title', '<h3 class="widget-title">', $sidebar_id, $sidebar_info ),
				'after_title'   => apply_filters( 'sb_sidebars_after_title', '</h3>', $sidebar_id, $sidebar_info )
			), $sidebar_id, $sidebar_info ) );

		}
	}

	/**
	 * Registers all default sidebars (don't override this)
	 *
	 * @since 2.5.0
	 */
	function register_default_sidebars() {

		/* Get the available post layouts and store them in an array */
		foreach ( get_theme_support( 'sb-sidebars' ) as $sidebar ) {
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
		$sidebar = apply_filters( 'sb_do_sidebar', $sidebar, $location );

		// If the sidebar has widgets, or an action attached to it, commence output
		if ( is_active_sidebar( $sidebar ) || has_action( "sb_no_{$location}_widgets" ) ) { ?>

			<?php do_action( "sb_before_{$location}" ); ?>
			<div id="<?php echo esc_attr( $location ); ?>" class="aside <?php echo $location; ?>-aside<?php if ($classes) { echo ' ' . $classes; }?>" role="complimentary">
				<?php do_action( "sb_before_{$location}_widgets" ); ?>
				<ul class="xoxo">
					<?php if ( !dynamic_sidebar($sidebar) ) { do_action( "sb_no_{$location}_widgets" ); }?>
				</ul>
				<?php do_action( "sb_after_{$location}_widgets" ); ?>
		   </div><!-- #<?php echo $location; ?> .aside-<?php echo $location; ?> .aside -->
		   <?php do_action( "sb_after_{$location}" ); ?>

		<?php }
	}

}
$GLOBALS['startbox']->sidebars = new SB_Sidebars;

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
	global $startbox;
	$startbox->sidebars->register_sidebar( array( 'name' => $name, 'id' => $id, 'description' => $description, 'editable' => $editable ) );
}

/**
 * Wrapper Function for SB_Sidebars::unregister_sidebar()
 *
 * @since 2.5.2
 * @param string $id the ID of the sidebar to unregister
 */
function sb_unregister_sidebar( $id ) {
	global $startbox;
	$startbox->sidebars->unregister_sidebar( $id );
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
	global $startbox;
	$startbox->sidebars->do_sidebar( $location, $sidebar, $classes );
}
