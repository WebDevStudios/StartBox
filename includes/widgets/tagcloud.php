<?php
/**
 * StartBox Tag Cloud
 *
 * A highly configurable tag cloud, more useful than the default widget.
 *
 * @package StartBox
 * @subpackage Widgets
 * @since Unknown
 */

function load_widget_sb_tagcloud_widget() { // Widget: Tag Cloud Widget
	unregister_widget('WP_Widget_Tag_Cloud'); // We're being StartBox-specific; remove WP default
	register_widget('sb_tagcloud_widget');
}
add_action( 'widgets_init', 'load_widget_sb_tagcloud_widget', 0 );

class sb_tagcloud_widget extends WP_Widget {
	function sb_tagcloud_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_tagcloud_widget',
			'description'  =>  __( "A configurable Tag Cloud.", "startbox" )
		);
		$this->WP_Widget( 'tagcloud-widget', __('SB Tag Cloud', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title'		=> 'Tags',
			'smallest'	=> '8',
			'largest'	=> '22',
			'unit'		=> 'pt',
			'number'	=> '45',
			'format'	=> 'flat',
			'separator'	=> ', ',
			'orderby'	=> 'name',
			'order'		=> 'ASC',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('smallest'); ?>"><?php _e( 'Smallest Size:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id('smallest') ); ?>" name="<?php echo esc_attr( $this->get_field_name('smallest') ); ?>" type="text" value="<?php echo esc_attr( $instance['smallest'] ); ?>" />
			<?php echo $instance['unit']; ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('largest'); ?>"><?php _e( 'Largest Size:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id('largest') ); ?>" name="<?php echo esc_attr( $this->get_field_name('largest') ); ?>" type="text" value="<?php echo esc_attr( $instance['largest'] ); ?>" />
			<?php echo $instance['unit']; ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('unit'); ?>"><?php _e( 'Unit of Measurement:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('unit') ); ?>" name="<?php echo esc_attr( $this->get_field_name('unit') ); ?>">
				<option value="pt" <?php if ( $instance['unit'] == 'pt' ) echo 'selected="selected"'; ?>>pt</option>
				<option value="px" <?php if ( $instance['unit'] == 'px' ) echo 'selected="selected"'; ?>>px</option>
				<option value="em" <?php if ( $instance['unit'] == 'em' ) echo 'selected="selected"'; ?>>em</option>
				<option value="%" <?php if ( $instance['unit'] == '%' ) echo 'selected="selected"'; ?>>%</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of tags:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e( 'Format:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('format') ); ?>" name="<?php echo esc_attr( $this->get_field_name('format') ); ?>">
				<option value="flat" <?php if ( $instance['format'] == 'flat' ) echo 'selected="selected"'; ?>>Flat</option>
				<option value="list" <?php if ( $instance['format'] == 'list' ) echo 'selected="selected"'; ?>>List</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('separator'); ?>"><?php _e( 'Separator:', 'startbox' ) ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id('separator') ); ?>" name="<?php echo esc_attr( $this->get_field_name('separator') ); ?>" type="text" value="<?php echo esc_attr( $instance['separator'] ); ?>" /><br/>
			<span style="font-size:smaller">Only appears in flat-formatted clouds.<br/>Include any desired spaces.</span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e( 'Order by:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('orderby') ); ?>" name="<?php echo esc_attr( $this->get_field_name('orderby') ); ?>">
				<option value="name" <?php if ( $instance['orderby'] == 'name' ) echo 'selected="selected"'; ?>>Name</option>
				<option value="count" <?php if ( $instance['orderby'] == 'count' ) echo 'selected="selected"'; ?>>Count</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e( 'Order:', 'startbox' ) ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('order') ); ?>" name="<?php echo esc_attr( $this->get_field_name('order') ); ?>">
				<option value="ASC" <?php if ( $instance['order'] == 'ASC' ) echo 'selected="selected"'; ?>>Ascending</option>
				<option value="DESC" <?php if ( $instance['order'] == 'DESC' ) echo 'selected="selected"'; ?>>Descending</option>
				<option value="RAND" <?php if ( $instance['order'] == 'RAND' ) echo 'selected="selected"'; ?>>Random</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('exclude') ); ?>" name="<?php echo esc_attr( $this->get_field_name('exclude') ); ?>" type="text" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
			<span style="font-size:smaller">Tag ID(s), separated by comma.<br/>Leave blank to display all tags.</span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('include'); ?>"><?php _e( 'Include:', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('include') ); ?>" name="<?php echo esc_attr( $this->get_field_name('include') ); ?>" type="text" value="<?php echo esc_attr( $instance['include'] ); ?>" />
			<span style="font-size:smaller">Tag ID(s), separated by comma.<br/>Leave blank to display all tags.</span>
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['smallest'] = strip_tags( $new_instance['smallest'] );
		$instance['largest'] = strip_tags( $new_instance['largest'] );
		$instance['unit'] = strip_tags( $new_instance['unit'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['format'] = strip_tags( $new_instance['format'] );
		$instance['separator'] = strip_tags( $new_instance['separator'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['exclude'] = strip_tags( $new_instance['exclude'] );

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$smallest = $instance['smallest'];
		$largest = $instance['largest'];
		$unit = $instance['unit'];
		$number = $instance['number'];
		$format = $instance['format'];
		$separator = $instance['separator'];
		$orderby = $instance['orderby'];
		$order = $instance['order'];
		$exclude = $instance['exclude'];
		$include = $instance['include'];

		$settings = array(
			'smallest'	=> $smallest,
			'largest'	=> $largest,
			'unit'		=> $unit,
			'number'	=> $number,
			'format'	=> $format,
			'separator'	=> $separator,
			'orderby'	=> $orderby,
			'order'		=> $order,
			'exclude'	=> $exclude,
			'include'	=> $include,
			'taxonomy'	=> 'post_tag',
			'echo'		=> true
		);

		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }
		wp_tag_cloud($settings);
		echo $after_widget;
	}
}