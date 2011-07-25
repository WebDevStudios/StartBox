<?php
/**
 * StartBox Social Widget
 *
 * List social profiles across several networks.
 *
 * @package StartBox
 * @subpackage Widgets
 * @since Unknown
 */

function sb_social_load_widgets() { // Widget: Stay Connected (offers fields/links for social media, rss and e-mail subscriptions)
	register_widget('sb_widget_social');
}
add_action( 'widgets_init', 'sb_social_load_widgets' );

class sb_widget_social extends WP_Widget {

	function sb_widget_social() {
	
			$widget_ops = array(
				'classname'    =>  'sb_social_widget',
				'description'  =>  __( "Provide visitors with links to your social media profiles.", "startbox" )
			);
			$this->WP_Widget( 'stay-connected-widget', __('SB Social', 'startbox'), $widget_ops);
			
		}

		function widget($args, $instance) {
			extract($args);
			
			$title = apply_filters('widget_title', $instance['title'] );
			$intro = $instance['intro'];
			$display = $instance['display'];
			$linksopen = ($instance['linksopen']) ? ' target="_blank"' : '' ;
			$hideicon = ($display == "text") ? true : false ;
			$hidetext = ($display == "icon") ? true : false ;
			$icon_url = apply_filters( 'sb_social_images_url', IMAGES_URL.'/social/' );
			$icon_size = apply_filters( 'sb_social_images_size', 24 );
			$rss = (isset($instance['rss'])) ? get_bloginfo('rss2_url') : '';
			$comment_rss = (isset($instance['comment_rss'])) ? get_bloginfo('comment_rss2_url') : '';
			$twitter = (isset($instance['twitter']) && $instance['twitter'] != '') ? 'http://twitter.com/' . $instance['twitter'] : '';
			$services = array(
				'rss'			=> $rss,
				'comment_rss'	=> $comment_rss,
				'twitter'		=> $twitter,
				'facebook'		=> $instance['facebook'],
				'youtube'		=> $instance['youtube'],
				'vimeo'			=> $instance['vimeo'],
				'flickr'		=> $instance['flickr'],
				'delicious'		=> $instance['delicious'],
				'linkedin'		=> $instance['linkedin'],
				'digg'			=> $instance['digg']
			);
			
			echo $before_widget;
			if ($title) { echo $before_title . $title . $after_title; }
			if ($intro) { echo '<p>'.$intro.'</p>'; }
			echo '<ul>';
			foreach ($services as $service => $url) {
				if ( isset($url) && $url != '' ) {
					if ( $service == 'rss' ) $text = apply_filters( 'sb_social_rss', __( 'Subscribe via RSS', 'startbox') );
					elseif ( $service == 'comment_rss' ) $text = apply_filters( 'sb_social_comment_rss', __( 'Subscribe to Comments RSS', 'startbox') );
					else $text = apply_filters( "sb_social_{$service}", sprintf( __( 'Connect on %s', 'startbox'), $service ), $instance );

					echo '<li class="listing listing-' . $service . '">';
					echo '<a href="' . $url . '" target="_blank" title="' . $text . '"'. $linksopen .'>';
					if (!$hideicon) echo '<img src="' . $icon_url . $service . '.png" width="' . $icon_size . 'px" height="' . $icon_size . 'px" alt="' . $text . '" />';
					if (!$hidetext) echo '<span>' . $text . '</span>';
					echo '</a></li>';
				}
			}
			echo '</ul>';
			echo $after_widget;

		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['intro'] = strip_tags( $new_instance['intro'] );
			$instance['rss'] = $new_instance['rss'];
			$instance['comment_rss'] = $new_instance['comment_rss'];
			$instance['twitter'] = strip_tags( $new_instance['twitter'] );
			$instance['facebook'] = strip_tags( $new_instance['facebook'] );
			$instance['delicious'] = strip_tags( $new_instance['delicious'] );
			$instance['flickr'] = strip_tags( $new_instance['flickr'] );
			$instance['youtube'] = strip_tags( $new_instance['youtube'] );
			$instance['vimeo'] = strip_tags( $new_instance['vimeo'] );
			$instance['digg'] = strip_tags( $new_instance['digg'] );
			$instance['linkedin'] = strip_tags( $new_instance['linkedin'] );
			$instance['linksopen'] =  $new_instance['linksopen'];
			$instance['display'] = $new_instance['display'];
			
			return $instance;
			
		}

		function form($instance) {
			$defaults = array(
				'title' => 'Stay Connected',
				'intro' => '',
				'rss' => 'on',
				'comment_rss' => '',
				'twitter' => '',
				'facebook' => '',
				'delicious' => '',
				'flickr' => '',
				'youtube' => '',
				'vimeo' => '',
				'digg' => '',
				'linkedin' => '',
				'linksopen' => '',
				'display' => ''
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			?>
				<p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="textarea" value="<?php echo $instance['title']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('intro'); ?>"><?php _e( 'Intro text: ', 'startbox' ) ?></label>
					<textarea id="<?php echo $this->get_field_id('intro'); ?>" name="<?php echo $this->get_field_name('intro'); ?>"><?php echo $instance['intro']; ?></textarea>
				</p>
				
				<h3><?php _e( 'Social Media', 'startbox' ); ?></h3>
				<p><?php _e( 'Fill in the links for the Social Media tabs you wish to activate, please include http:// on all links except Twitter.', 'startbox' ); ?></p>				
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance['rss'], 'on' ); ?> id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" />
					<label for="<?php echo $this->get_field_id('rss'); ?>"><?php _e( 'Display RSS Feed', 'startbox' ) ?></label>
				</p>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance['comment_rss'], 'on' ); ?> id="<?php echo $this->get_field_id('comment_rss'); ?>" name="<?php echo $this->get_field_name('comment_rss'); ?>" />
					<label for="<?php echo $this->get_field_id('comment_rss'); ?>"><?php _e( 'Display Comment RSS Feed', 'startbox' ) ?></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e( 'Twitter Username: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo $instance['twitter']; ?>" />
					<span style="font-size:smaller">Username only, no web address.</span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e( 'Facebook: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo $instance['facebook']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('delicious'); ?>"><?php _e( 'Delicious: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('delicious'); ?>" name="<?php echo $this->get_field_name('delicious'); ?>" type="text" value="<?php echo $instance['delicious']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('flickr'); ?>"><?php _e( 'Flickr: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('flickr'); ?>" name="<?php echo $this->get_field_name('flickr'); ?>" type="text" value="<?php echo $instance['flickr']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e( 'YouTube: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo $instance['youtube']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('vimeo'); ?>"><?php _e( 'Vimeo: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('vimeo'); ?>" name="<?php echo $this->get_field_name('vimeo'); ?>" type="text" value="<?php echo $instance['vimeo']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('digg'); ?>"><?php _e( 'Digg: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('digg'); ?>" name="<?php echo $this->get_field_name('digg'); ?>" type="text" value="<?php echo $instance['digg']; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e( 'LinkedIn: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="text" value="<?php echo $instance['linkedin']; ?>" />
				</p>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance['linksopen'], 'on' ); ?> id="<?php echo $this->get_field_id('linksopen'); ?>" name="<?php echo $this->get_field_name('linksopen'); ?>" />
					<label for="<?php echo $this->get_field_id('linksopen'); ?>"><?php _e( 'Open all links in new window ', 'startbox' ) ?></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e( 'Link Display:', 'startbox' ) ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
						<option value="both" <?php if($instance['display'] == 'both'){ echo 'selected="selected"'; }?>>Text and Icon</option>
						<option value="icon" <?php if($instance['display'] == 'icon'){ echo 'selected="selected"'; }?>>Icon Only</option>
						<option value="text" <?php if($instance['display'] == 'text'){ echo 'selected="selected"'; }?>>Text Only</option>
					</select>
				</p>
			<?php
	}
}
?>