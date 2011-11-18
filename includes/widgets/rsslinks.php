<?php
/**
 * StartBox RSS Links
 *
 * Provide RSS links for your posts and comments
 *
 * @package StartBox
 * @subpackage Widgets
 * @since Unknown
 */

class sb_rss_widget extends WP_Widget {
	function sb_rss_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_rss_widget',
			'description'  =>  __( "RSS links for both posts and comments.", "startbox" )
		);
		$this->WP_Widget( 'rsslinks-widget', __('SB RSS Links', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title' => 'RSS Links'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		
		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }	
	?>
		<ul>
			<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo esc_attr( esc_html( get_bloginfo('name'), 1 ) ); ?> <?php _e( 'Posts RSS feed', 'startbox' ); ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All posts', 'startbox' ) ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo esc_attr( esc_html(bloginfo('name'), 1) ); ?> <?php _e( 'Comments RSS feed', 'startbox' ); ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All comments', 'startbox' ) ?></a></li>
		</ul>
	<?php
		echo $after_widget;
	}
}
// register_widget('sb_rss_widget'); // Deprecated 09/03/10


?>