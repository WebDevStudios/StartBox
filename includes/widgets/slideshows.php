<?php
// Check to see if current theme supports slideshows
if( !current_theme_supports('sb-slideshows') ) return;

function load_widget_sb_slideshow_widget() { // Widget: Search Widget
	register_widget('sb_slideshow_widget');
}
add_action( 'widgets_init', 'load_widget_sb_slideshow_widget', 0 );

class sb_slideshow_widget extends WP_Widget {
	function sb_slideshow_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_slideshow_widget',
			'description'  =>  __( "A widget for displaying your slideshows.", "startbox" )
		);
		$this->WP_Widget( 'slide-widget', __('SB Slideshow', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title' => '',
			'slideshow' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('slideshow'); ?>"><?php _e( 'Slideshow: ', 'startbox' ) ?></label>
			<?php
				$args = array(
					'post_type'	=> 'slideshow',
					'order_by'	=> 'post_title',
					'order'		=> 'ASC',
					'id'		=> $this->get_field_id('slideshow'),
					'name'		=> $this->get_field_name('slideshow'),
					'selected'	=> $instance['slideshow'],
					'option_none_value' => 'Select a Slideshow'
				);
				sb_dropdown_posts($args);
			?>
		</p>
		<p>
			<a href="edit.php?post_type=slideshow"><?php _e( 'Edit Slideshow Settings', 'startbox' ); ?></a>
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['slideshow'] = strip_tags( $new_instance['slideshow'] );
		
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		$slideshow = ( isset( $instance['slideshow'] ) ) ? $instance['slideshow'] : false;
		
		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }	
		if ($slideshow) echo do_shortcode('[slideshow id="' . $slideshow . '"]');
		echo $after_widget;
	}
}

?>