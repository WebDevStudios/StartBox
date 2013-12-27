<?php
/**
 * SBX Layout Class
 *
 * Theme Layouts was originally created by Justin Tadlock for use with Hybrid Core.
 * This allows developers to easily add/remove support for multiple layout structures.
 * It gives users the ability to control how each post type is displayed on the
 * front end of the site.  The layout can also be filtered for any page of a WordPress site.
 *
 * @package SBX
 * @subpackage Classes
 * @since 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * This is the main SB Sidebars class.
 *
 * You can extend this within your theme to alter the widget markup.
 *
 * @subpackage Classes
 * @since 1.0.0
 */
class SBX_Layouts {

	function __construct() {
		// Post Meta
		add_action( 'admin_menu', array( $this, 'post_metabox_add' ) );
		add_action( 'save_post', array( $this, 'save_layout_meta' ) );

		// Term Meta
		add_action( 'admin_menu', array( $this, 'term_meta_add' ) );
		add_action( 'edit_term', array( $this, 'save_layout_meta' ) );
		add_filter( 'sbx_get_term_meta_defaults', array( $this, 'get_term_meta_defaults' ) );

		// Customizer settings
		add_filter( 'sbx_customizer_settings', array( $this, 'customizer_settings' ) );
		add_filter( 'sbx_get_layout', array( $this, 'customizer_defaults' ) );

		// Body class filter
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Get supported layouts for the current theme.
	 *
	 * Returns a filterable, multi-dimensional array keyed with the
	 * layout's slug. Values include layout image and image. e.g.:
	 * 'layout-one-col' => array(
	 *     'label' => 'One column (no sidebars)',
	 *     'image' => 'images/layouts/one-col.png',
	 * )
	 *
	 * @since  1.0.0
	 *
	 * @return array Supported theme layouts.
	 */
	public static function get_supported_layouts() {
		// Only the first item in the supported layouts array is relevant
		$supported_layouts = get_theme_support( 'sbx-layouts' );
		$layouts = is_array( $supported_layouts ) ? array_shift( $supported_layouts ) : array();
		return apply_filters( 'sbx_get_supported_layouts', $layouts );
	} /* get_supported_layouts() */

	/**
	 * Get layout for a specifc post or term, or currently viewed object.
	 *
	 * @since  1.0.0
	 *
	 * @param  integer $post_id Post ID.
	 * @return string           Page layout.
	 */
	public static function get_layout( $post_id = 0 ) {
		global $wp_query;

		// If not given an explicit ID, and viewing a singular post
		// get post ID from query object
		if ( empty( $post_id ) && is_singular() ) {
			$post_id = $wp_query->get_queried_object_id();
		}

		// Get page/post layout from postmeta
		if ( ! empty( $post_id ) ) {
			$layout = esc_html( get_post_meta( $post_id, '_sbx_layout', true ) );

		// Get taxonomy artive layouts from custom term meta
		} elseif ( is_category() || is_tag() || is_tax() || is_archive() ) {
			$term = $wp_query->get_queried_object();
			if ( isset( $term->meta['layout'] ) ) {
				$layout = $term->meta['layout'];
			}
		}

		// If layout is not set, or is not supported, set to a filterable default
		if ( empty( $layout ) || ! array_key_exists( $layout, self::get_supported_layouts() ) ) {
			$layout = apply_filters( 'sbx_get_layout_default', 'default', $post_id );
		}

		// Return the filterable layout
		return esc_attr( apply_filters( 'sbx_get_layout', $layout ) );

	} /* get_layout() */

	/**
	 * Update layout for a post or term.
	 *
	 * @since  1.0.0
	 *
	 * @param  integer $object_id Post or term ID.
	 * @param  string  $layout    Layout slug.
	 * @return mixed              Meta ID on success for posts, true on success for terms, otherwise false.
	 */
	public static function update_layout( $object_id = 0, $layout = 'default' ) {

		// Attempt to get a post object
		$post = get_post( $object_id );

		// If object is a post, update postmeta
		if ( is_object( $post ) ) {
			return update_post_meta( $object_id, '_sbx_layout', esc_attr( $layout ) );

		// Otherwise, object is a term, update options
		} else {
			return sbx_update_term_meta( $object_id, 'layout', esc_attr( $layout ) );
		}

	} /* update_layout() */

	/**
	 * Save layout meta on post and taxonomy save.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $object_id Post or Term ID.
	 */
	public function save_layout_meta( $object_id ) {

		// If nonce doesn't match, bail here
		if ( ! isset( $_POST['sbx_layout_options_nonce'] ) || ! wp_verify_nonce( $_POST['sbx_layout_options_nonce'], 'update_layout' ) )
			return;

		// Get the submitted post layout
		if ( ! empty( $_POST['sbx_layout'] ) ) {
			$this->update_layout( $object_id, esc_attr( $_POST['sbx_layout'] ) );
		}

	} /* save_layout_meta() */

	/**
	 * Render layout radio options for use in WP admin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $selected_layout Currently selected layout.
	 * @param bool   $show_default    Show option for "use default" when true.
	 */
	public static function render_layout_options( $selected_layout = 'default', $show_default = true ) {
		wp_nonce_field( 'update_layout', 'sbx_layout_options_nonce' );
	?>
		<ul style="overflow:hidden;">
			<?php if ( $show_default ) { ?>
				<li>
					<label for="sbx_layout_default">
						<input type="radio" name="sbx_layout" id="sbx_layout_default" value="default" <?php checked( $selected_layout, 'default' );?> />
						<?php printf( __( 'Default (set in %s)', 'sbx' ), '<a href="' . admin_url( 'customize.php' ) . '">' . __( 'Theme Customizer', 'sbx' ). '</a>' ); ?>
					</label>
				</li>
			<?php } ?>
			<?php foreach ( self::get_supported_layouts() as $slug => $layout ) { ?>
				<li style="float:left; margin-right:15px; margin-bottom:10px">
					<label for="sbx_layout_<?php echo esc_attr( $slug ); ?>">
						<input type="radio" name="sbx_layout" id="sbx_layout_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $selected_layout, $slug ); ?>  style="float:left; margin-right:5px; margin-top:20px"/>
						<img src="<?php echo esc_url( apply_filters( 'sbx_layout_image', $layout['image'], $slug ) ); ?>" alt="<?php echo esc_attr( apply_filters( 'sbx_layout_label', $layout['label'], $slug ) ); ?>" width="40" height="40" />
					</label>
				</li>
			<?php } ?>
		</ul>
	<?php
	}

	/**
	 * Add a metabox for each publicly viewable post type.
	 *
	 * @since 1.0.0
	 */
	public function post_metabox_add() {
		$public_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $public_types as $type => $object ) {
			add_meta_box( 'sbx-layouts-metabox', __( 'Layout', 'sbx' ), array( $this, 'post_metabox_render' ), $type, 'normal', 'default' );
		}
	} /* post_metabox_add() */

	/**
	 * Render the layout metabox for a given post.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Post object.
	 */
	public function post_metabox_render( $post ) {
	?>
		<div class="post-layout">
			<p><?php _e( 'Specify a custom page layout for this content.', 'startbox' ); ?></p>
			<div class="post-layout-wrap">
				<?php $this->render_layout_options( $this->get_layout( $post->ID ) ); ?>
			</div>
		</div>
	<?php
	} /* post_metabox_render() */

	/**
	 * Add layout settings for each registered taxonomy with admin UI.
	 *
	 * @since 1.0.0
	 */
	public function term_meta_add() {
		foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name) {
			add_action( $tax_name . '_edit_form', array( $this, 'term_meta_render' ), 10, 2 );
		}
	} /* term_meta_add() */

	/**
	 * Render layout options for taxonomy editor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $term     Term object.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function term_meta_render( $term, $taxonomy ) {
		$tax_object = get_taxonomy( $taxonomy );
	?>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top"><label><?php _e('Custom Layout', 'sbx'); ?></label></th>
				<td>
					<?php $this->render_layout_options( $term->meta['layout'] ); ?>
					<p class="description"><?php printf( __( 'Select a custom layout for this %s.', 'sbx' ), strtolower( $tax_object->labels->singular_name ) ); ?></p>
				</td>
			</tr>
		</table>

	<?php
	} /* term_meta_render() */

	/**
	 * Add 'layout' as a default term meta key.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $meta Term meta defaults array.
	 * @return array       Updated term meta array.
	 */
	public function get_term_meta_defaults( $meta ) {
		$meta['layout'] = 'default';
		return $meta;
	} /* get_term_meta_defaults() */

	/**
	 * Add layout controls to the theme customizer.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $sections Registered settings.
	 * @return array           Updated settings.
	 */
	public function customizer_settings( $sections ) {
		$sections['layout_settings'] = array(
			'title'       => __( 'Layout', 'sbx' ),
			'description' => __( 'Set default layouts based on content.', 'sbx' ),
			'priority'    => 10,
			'settings'    => array(
				array(
					'id'                => SBX::$options_prefix . 'home_layout',
					'label'             => 'Home Layout',
					'type'              => 'SBX_Customize_Layout_Control',
					'theme_supports'    => 'sbx-layouts',
					'default'           => 'two-col-right',
					'priority'          => 10,
					'sanitize_callback' => 'sanitize_html_class',
					'js_callback'       => 'sbx_change_layout',
					'css_selector'      => 'body.home',
					),
				array(
					'id'                => SBX::$options_prefix . 'singular_layout',
					'label'             => 'Single Content Layout',
					'type'              => 'SBX_Customize_Layout_Control',
					'theme_supports'    => 'sbx-layouts',
					'default'           => 'two-col-right',
					'priority'          => 10,
					'sanitize_callback' => 'sanitize_html_class',
					'js_callback'       => 'sbx_change_layout',
					'css_selector'      => 'body.single',
					),
				array(
					'id'                => SBX::$options_prefix . 'archive_layout',
					'label'             => 'Archive Layout',
					'type'              => 'SBX_Customize_Layout_Control',
					'theme_supports'    => 'sbx-layouts',
					'default'           => 'two-col-right',
					'priority'          => 10,
					'sanitize_callback' => 'sanitize_html_class',
					'js_callback'       => 'sbx_change_layout',
					'css_selector'      => 'body.archive',
					),
			)
		);

		return $sections;
	} /* customizer_settings() */

	/**
	 * Filter SBX_Layouts::get_layout() with defaults set in Customizer.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $layout Current layout.
	 * @return string         Potentially modified layout.
	 */
	public function customizer_defaults( $layout ) {

		// If a layout has been set, bail here
		if ( 'default' !== $layout )
			return $layout;

		// Otherwise, use the default set via the customizer
		if ( is_front_page() ) {
			$layout = sanitize_html_class( sbx_get_theme_mod( SBX::$options_prefix . 'home_layout' ) );
		} elseif ( is_singular() ) {
			$layout = sanitize_html_class( sbx_get_theme_mod( SBX::$options_prefix . 'singular_layout' ) );
		} elseif ( is_category() || is_tag() || is_tax() || is_archive() ) {
			$layout = sanitize_html_class( sbx_get_theme_mod( SBX::$options_prefix . 'archive_layout' ) );
		}

		// Return the new layout
		return $layout;
	} /* customizer_defaults() */

	/**
	 * Add layout class to page body in form of "layout-$layout".
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes all the set classes.
	 */
	public function body_class( $classes ) {

		// Adds the layout to array of body classes
		$classes[] = sanitize_html_class( 'layout-' . $this->get_layout() );

		// Return the $classes array
		return $classes;
	}

}
$GLOBALS['sbx']->layouts = new SBX_Layouts;

/**
 * Get layout for a specifc post or term, or currently viewed object.
 *
 * Wrapper for SBX_Layouts::get_layout().
 *
 * @since 1.0.0
 * @return string The layout for the given page.
 */
function sbx_get_layout( $post_id = 0 ) {
	return SBX_Layouts::get_layout( $post_id );
}

/**
 * Update layout for a post or term.
 *
 * Wrapper for SBX_Layouts::update_layout().
 *
 * @since  1.0.0
 *
 * @param  integer $object_id Post or term ID.
 * @param  string  $layout    Layout slug.
 * @return mixed              Meta ID on success for posts, true on success for terms, otherwise false.
 */
function sbx_update_layout( $object_id = 0, $layout = 'default' ) {
	return SBX_Layouts::update_layout( $object_id, $layout );
}

// Make sure WP_Customize_Control is available
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Register textarea controller for the theme customizer.
	 *
	 * @subpackage Classes
	 * @link http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer
	 * @since 1.0.0
	 */
	class SBX_Customize_Layout_Control extends WP_Customize_Control {

		/**
		 * @access public
		 * @since 1.0.0
		 * @var string The type of form element being generated.
		 */
		public $type = 'layout';

		/**
		 * Overrides the render_content() function in the parent class
		 *
		 * @since 1.0.0
		 */
		public function render_content() {
		?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<ul style="overflow:hidden;">
				<?php foreach ( SBX_Layouts::get_supported_layouts() as $slug => $layout ) { ?>
					<li style="float:left; margin-right:15px; margin-bottom:10px">
						<label>
							<input type="radio" value="<?php echo esc_attr( $slug ); ?>" name="<?php echo esc_attr( "sbx_layout_{$this->id}" ); ?>" <?php $this->link(); checked( $this->value(), $slug ); ?> />
							<img src="<?php echo esc_url( apply_filters( 'sbx_layout_image', $layout['image'], $slug ) ); ?>" alt="<?php echo esc_attr( apply_filters( 'sbx_layout_label', $layout['label'], $slug ) ); ?>" width="40" height="40" />
						</label>
					</li>
				<?php } ?>
			</ul>
		<?php
		}
	}
}
