<?php
/**
 * StartBox Search Widget
 *
 * @package StartBox
 * @subpackage Widgets
 * @author  WebDev Studios
 * @link    http://wpstartbox.com/
 * @license GPL-2.0+
 */

/**
 * StartBox Search widget.
 *
 * Configurable search widget, set custom input text and submit button text.
 *
 * @package StartBox\Widgets
 * @author  WebDev Studios
 *
 * @since Unknown
 */
class SB_Widget_Search extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	public function __construct() {
		$this->defaults = array(
			'title'         => __( 'Search', 'startbox' ),
			'search-input'  => __( 'Search', 'startbox' ),
			'search-button' => __( 'Search', 'startbox' ),
		);

		$widget_ops = array(
			'classname'    =>  'sb_search_widget',
			'description'  =>  __( 'A search form for your blog.', 'startbox' )
		);

		parent::__construct( 'search-widget', __( 'SB Search', 'startbox' ), $widget_ops);
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}
		?>
		<form class="searchform" method="get" action="<?php echo esc_url( home_url() ); ?>" role="search">
			<div>
				<input type="text" class="searchtext" value="" name="s" title="<?php echo esc_attr( $instance['search-input'] ); ?>" size="10" tabindex="1" />
				<input type="submit" class="button" value="<?php echo esc_attr( $instance['search-button'] ); ?>" tabindex="2" />
			</div>
		</form>
		<?php
		echo $after_widget;
	}

	/**
	 * Update a particular instance.
 	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved / updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']         = esc_html( $new_instance['title'] );
		$instance['search-input']  = esc_html( $new_instance['search-input'] );
		$instance['search-button'] = esc_html( $new_instance['search-button'] );

		return $instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'search-input' ) ); ?>"><?php _e( 'Input Text: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'search-input' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'search-input' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['search-input'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'search-button' ) ); ?>"><?php _e( 'Button Text: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'search-button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'search-button' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['search-button'] ); ?>" />
		</p>
		<?php
	}
}
