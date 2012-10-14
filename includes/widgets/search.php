<?php
/**
 * StartBox Search
 *
 * Configurable search widget, set custom input text and submit button text.
 *
 * @package StartBox
 * @subpackage Widgets
 * @since Unknown
 */

function load_widget_sb_search_widget() { // Widget: Search Widget
	unregister_widget('WP_Widget_Search'); // We're being StartBox-specific; remove WP default (disabled, there's space for both)
	register_widget('sb_search_widget');
}
add_action( 'widgets_init', 'load_widget_sb_search_widget', 0 );

class sb_search_widget extends WP_Widget {
	function sb_search_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_search_widget',
			'description'  =>  __( "A search form for your blog.", "startbox" )
		);
		$this->WP_Widget( 'search-widget', __('SB Search', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title' => 'Search',
			'search-input' => 'Search',
			'search-button' => 'Search'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Input Text: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('search-input') ); ?>" name="<?php echo esc_attr( $this->get_field_name('search-input') ); ?>" type="text" value="<?php echo esc_attr( $instance['search-input'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Button Text: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('search-button') ); ?>" name="<?php echo esc_attr( $this->get_field_name('search-button') ); ?>" type="text" value="<?php echo esc_attr( $instance['search-button'] ); ?>" />
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['search-input'] = strip_tags( $new_instance['search-input'] );
		$instance['search-button'] = strip_tags( $new_instance['search-button'] );

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$text = $instance['search-input'];
		$button = $instance['search-button'];

		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }
	?>

		<form class="searchform" method="get" action="<?php echo esc_url( home_url() ); ?>">
			<div>
				<input name="s" type="text" class="searchtext" value="" title="<?php echo esc_attr( $text ); ?>" size="10" tabindex="1" />
				<input type="submit" class="button" value="<?php echo esc_attr( $button ); ?>" tabindex="2" />
			</div>
		</form>

	<?php
		echo $after_widget;
	}
}