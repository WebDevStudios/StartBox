<?php
/**
 * StartBox Framework.
 *
 * @package StartBox\Widgets
 * @author  WebDev Studios
 * @link    http://wpstartbox.com/
 * @license GPL-2.0+
 */

/**
 * StartBox Tag Cloud widget.
 *
 * A highly configurable tag cloud, more useful than the default widget.
 *
 * @package StartBox\Widgets
 * @author  WebDev Studios
 *
 * @since Unknown
 */
class SB_Widget_Tag_Cloud extends WP_Widget {
	
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
		$defaults = array(
			'title'     => __( 'Tags', 'startbox' ),
			'smallest'  => '8',
			'largest'   => '22',
			'unit'      => 'pt',
			'number'    => '45',
			'format'    => 'flat',
			'separator' => ', ',
			'orderby'   => 'name',
			'order'     => 'ASC',
			'include'   => '',
			'exclude'   => '',
		);

		$widget_ops = array(
			'classname'   =>  'sb_tagcloud_widget',
			'description' =>  __( 'A configurable Tag Cloud.', 'startbox' )
		);

		// By using the ID base from the original Tag Cloud widget, we replace it
		parent::__construct( 'tagcloud-widget', __( 'SB Tag Cloud', 'startbox' ), $widget_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	protected function widget( array $args, array $instance ) {
		extract( $args );
		
		$tag_cloud_args = $instance;
		unset( $tag_cloud_args['title'] );

		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}

		wp_tag_cloud( $tag_cloud_args );

		echo $after_widget;
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved / updated.
	 * 
	 * @todo Better sanitization for enumerated tag cloud widget options.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	protected function update( array $new_instance, array $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['smallest']  = absint( $new_instance['smallest'] );
		$instance['largest']   = absint( $new_instance['largest'] );
		$instance['unit']      = strip_tags( $new_instance['unit'] );
		$instance['number']    = absint( $new_instance['number'] );
		$instance['format']    = strip_tags( $new_instance['format'] );
		$instance['separator'] = strip_tags( $new_instance['separator'] );
		$instance['orderby']   = strip_tags( $new_instance['orderby'] );
		$instance['order']     = strip_tags( $new_instance['order'] );
		$instance['include']   = strip_tags( $new_instance['include'] );
		$instance['exclude']   = strip_tags( $new_instance['exclude'] );
		
		return $instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	protected function form( array $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><?php _e( 'Smallest Size:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id( 'smallest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'smallest' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['smallest'] ); ?>" />
			<?php echo $instance['unit']; ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><?php _e( 'Largest Size:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id( 'largest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'largest' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['largest'] ); ?>" />
			<?php echo $instance['unit']; ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'unit' ); ?>"><?php _e( 'Unit of Measurement:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'unit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'unit' ) ); ?>">
				<option value="pt"<?php selected( $instance['unit'], 'pt' ); ?>></option>
				<option value="px"<?php selected( $instance['unit'], 'px' ); ?>></option>
				<option value="em"<?php selected( $instance['unit'], 'em' ); ?>></option>
				<option value="%"<?php selected( $instance['unit'], '%' ); ?>></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of tags:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Format:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>">
				<option value="flat"<?php selected( $instance['format'], 'flat' ); ?>><?php _e( 'Flat', 'startbox' ); ?></option>
				<option value="list"<?php selected( $instance['format'], 'list' ); ?>><?php _e( 'List', 'starybox' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id( 'separator' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'separator' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['separator'] ); ?>" /><br/>
			<span style="font-size:smaller"><?php _e( 'Only appears in flat-formatted clouds.<br/>Include any desired spaces.', 'startbox' ); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order by:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>><?php _e( 'Name', 'startbox' ); ?></option>
				<option value="count"<?php selected( $instance['orderby'], 'count' ); ?>><?php _e( 'Count', 'startbox' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="ASC"<?php selected( $instance['order'], 'ASC' ); ?>><?php _e( 'Ascending', 'startbox' ); ?></option>
				<option value="DESC"<?php selected( $instance['order'], 'DESC' ); ?>><?php _e( 'Descending', 'startbox' ); ?></option>
				<option value="RAND"<?php selected( $instance['order'], 'RAND' ); ?>><?php _e( 'Random', 'startbox' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude:', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
			<span style="font-size:smaller"><?php _e( 'Tag ID(s), separated by comma.<br/>Leave blank to display all tags.', 'startbox' ); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e( 'Include:', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['include'] ); ?>" />
			<span style="font-size:smaller"><?php _e( 'Tag ID(s), separated by comma.<br/>Leave blank to display all tags.', 'startbox' ); ?></span>
		</p>
	<?php
	}
}
