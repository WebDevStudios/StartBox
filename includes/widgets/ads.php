<?php
/**
 * StartBox Ad Widget (unfinished, needs a lot more work and a better strategy)
 *
 * Creates and spaces in any widgitized area.
 *
 * @package StartBox
 * @subpackage Widgets
 * @since StartBox 2.x
 */

function load_widget_sb_ads_widget() { // Widget: Ads Widget
	register_widget('sb_ads_widget');
}
// add_action( 'widgets_init', 'load_widget_sb_ads_widget', 0 );

function load_ads_widget_js() {
	wp_enqueue_script( 'sb-widgets', SCRIPTS_URL . '/widgets.js', array('jquery') );
}
// add_action( 'sidebar_admin_setup', 'load_ads_widget_js' );

class sb_ads_widget extends WP_Widget {
	function sb_ads_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_ads_widget',
			'description'  =>  __( "Display text or banner ads.", "startbox" )
		);
		$this->WP_Widget( 'ads-widget', __('SB Ad Manager', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title' => 'Ads',
			'ads-input' => 'Type your ads and press enter.',
			'ads-button' => '`'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Image Size: ', 'startbox' ); ?></label>
			<input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" size="3" value="<?php echo $instance['width']; ?>" />
			x
			<input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" size="3" value="<?php echo $instance['height']; ?>" />px
		</p>
		
		<div>
			<ul class="ads">
				<li><p><label for="text">Link Text:</label> <input name="text" type="text" value="" class="text" /> <p><label for="url">Link URL:</label> <input name="url" type="text" value="" class="url" /></p> <p><label for="image">Image URL:</label> <input type="text" value="" class="ads" /></p> <a href="#nogo" class="remove">Remove</a></li>
			</ul>
			<a href="#nogo" class="add">Add Another</a>
		</div>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads-input'] = strip_tags( $new_instance['ads-input'] );
		$instance['ads-button'] = strip_tags( $new_instance['ads-button'] );
		
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		$text = $instance['ads-input'];
		$button = $instance['ads-button'];
		
		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }	
	?>
		
		<form class="adsform" method="get" action="<?php echo home_url(); ?>">
			<div>
				<input name="s" type="text" class="adstext" value="" title="<?php echo $text; ?>" size="10" tabindex="1" />
				<input type="submit" class="button" value="<?php echo $button; ?>" tabindex="2" />
			</div>
		</form>
		
	<?php
		echo $after_widget;
	}
}

?>