<?php
/**
 * StartBox Social Widget
 *
 * @package StartBox
 * @subpackage Widgets
 * @author  WebDev Studios
 * @link    http://wpstartbox.com/
 * @license GPL-2.0+
 */

/**
 * StartBox Social widget.
 *
 * List social profiles across several networks. Offers fields/links for social media, RSS and email subscriptions.
 *
 * @package StartBox\Widgets
 * @author  WebDev Studios
 *
 * @since Unknown
 */
class SB_Widget_Social extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Holds details of available services, populated in constructor.
	 *
	 * @var array
	 */
	protected $services;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	public function __construct() {
		$this->defaults = array(
			'title'       => __( 'Stay Connected', 'startbox' ),
			'intro'       => '',
			'rss'         => 'on',
			'comment_rss' => '',
			'twitter'     => '',
			'facebook'    => '',
			'gplus'       => '',
			'delicious'   => '',
			'flickr'      => '',
			'youtube'     => '',
			'vimeo'       => '',
			'digg'        => '',
			'linkedin'    => '',
			'linksopen'   => '',
			'display'     => '',
		);

		$widget_ops = array(
			'classname'   =>  'sb_social_widget',
			'description' =>  __( 'Provide visitors with links to your social media profiles.', 'startbox' ),
		);

		parent::__construct( 'stay-connected-widget', __( 'SB Social', 'startbox' ), $widget_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( array $args, array $instance ) {
		extract($args);
		$instance  = wp_parse_args( $instance, $this->defaults );
		$linksopen = $instance['linksopen'] ? ' target="_blank"' : '' ;
		$hideicon  = ( 'text' == $instance['display'] ) ? true : false ;
		$hidetext  = ( 'icon' == $instance['display'] ) ? true : false ;

		// Icon properties
		$icon_url       = apply_filters( 'sb_social_images_url', IMAGES_URL . '/social/' );
		$icon_size      = apply_filters( 'sb_social_images_size', 24 );
		$icon_extension = apply_filters( 'sb_social_images_extension', '.png' );

		// Pre-defined values
		$rss         = ( isset( $instance['rss'] ) ) ? get_bloginfo('rss2_url') : '';
		$comment_rss = ( isset( $instance['comment_rss'] ) ) ? get_bloginfo('comment_rss2_url') : '';
		$twitter     = ( isset( $instance['twitter'] ) ) ? 'https://twitter.com/' . $instance['twitter'] : '';

		// Setup our services
		$services = array(
			'rss'         => array( 'name' => 'RSS',          'url' => $rss, 'text' => __( 'Subscribe via RSS', 'startbox' ) ),
			'comment_rss' => array( 'name' => 'Comments RSS', 'url' => $comment_rss, 'text' => __( 'Subscribe to Comments RSS', 'startbox' ) ),
			'twitter'     => array( 'name' => 'Twitter',      'url' => $twitter ),
			'facebook'    => array( 'name' => 'Facebook',     'url' => $instance['facebook'] ),
			'gplus'       => array( 'name' => 'Google+',      'url' => $instance['gplus'] ),
			'youtube'     => array( 'name' => 'YouTube',      'url' => $instance['youtube'] ),
			'vimeo'       => array( 'name' => 'Vimeo',        'url' => $instance['vimeo'] ),
			'flickr'      => array( 'name' => 'Flickr',       'url' => $instance['flickr'] ),
			'delicious'   => array( 'name' => 'Del.icio.us',  'url' => $instance['delicious'] ),
			'linkedin'    => array( 'name' => 'LinkedIn',     'url' => $instance['linkedin'] ),
			'digg'        => array( 'name' => 'Digg',         'url' => $instance['digg'] ),
		);

		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}

		if ( $instance['intro'] ) {
			echo '<p>' . $instance['intro'] . '</p>';
		}

		echo '<ul>';
		foreach ( $services as $service_id => $service ) {
			// Skip if there's no URL
			if ( ! isset( $service['url'] ) || ! $service['url'] ) {
				continue;
			}

			// Give default (filterable) text strings
			if ( ! isset( $service['text'] ) ) {
				$service['text'] = sprintf( __( 'Connect on %s', 'startbox'), $service['name'] );
			}

			$service['text'] = apply_filters( "sb_social_{$service_id}", $service['text'] );

			printf(
				'<li class="%s">',
				esc_attr( 'listing ' . sanitize_class_html( 'listing-' . $service_id ) )
			);
			printf(
				'<a href="%s" title="%s"%s>',
				esc_url( $service['url'] ),
				esc_attr( $service['text'] ),
				$linksopen
			);
			if ( ! $hideicon ) {
				printf(
					'<img src="%1$s" width="%2$s" height="%2$s" alt="%3$s" />',
					esc_url( $icon_url . $service_id . $icon_extension ),
					esc_attr( $icon_size ),
					esc_attr( $service['text'] )
				);
			}
			if ( ! $hidetext ) {
				echo '<span>' . $service['text'] . '</span>';
			}
			echo '</a></li>';
		}
		echo '</ul>';

		echo $after_widget;
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved / updated.
	 *
	 * @todo Better sanitization for social widget options.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( array $new_instance, array $old_instance ) {
		$instance = $old_instance;

		$instance['title']       = esc_html( $new_instance['title'] );
		$instance['intro']       = esc_html( $new_instance['intro'] );
		$instance['rss']         = $new_instance['rss'];
		$instance['comment_rss'] = $new_instance['comment_rss'];
		$instance['twitter']     = esc_html( $new_instance['twitter'] );
		$instance['facebook']    = esc_html( $new_instance['facebook'] );
		$instance['gplus']       = esc_html( $new_instance['gplus'] );
		$instance['delicious']   = esc_html( $new_instance['delicious'] );
		$instance['flickr']      = esc_html( $new_instance['flickr'] );
		$instance['youtube']     = esc_html( $new_instance['youtube'] );
		$instance['vimeo']       = esc_html( $new_instance['vimeo'] );
		$instance['digg']        = esc_html( $new_instance['digg'] );
		$instance['linkedin']    = esc_html( $new_instance['linkedin'] );
		$instance['linksopen']   =  $new_instance['linksopen'];
		$instance['display']     = $new_instance['display'];

		return $instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	public function form( array $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="textarea" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'intro' ); ?>"><?php _e( 'Intro text: ', 'startbox' ) ?></label>
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'intro' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'intro' ) ); ?>"><?php echo $instance['intro']; ?></textarea>
		</p>

		<h3><?php _e( 'Social Media', 'startbox' ); ?></h3>
		<p><?php _e( 'Fill in the links for the Social Media tabs you wish to activate, please include http:// on all links except Twitter.', 'startbox' ); ?></p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['rss'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss' ) ); ?>" />
			<label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e( 'Display RSS Feed', 'startbox' ) ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['comment_rss'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'comment_rss' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'comment_rss' ) ); ?>" />
			<label for="<?php echo $this->get_field_id( 'comment_rss' ); ?>"><?php _e( 'Display Comment RSS Feed', 'startbox' ) ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter Username: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['twitter'] ); ?>" />
			<span style="font-size:smaller"><?php _e( 'Username only, no web address.', 'startbox' ); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['facebook'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'gplus' ); ?>"><?php _e( 'Google+: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'gplus' ); ?>" name="<?php echo $this->get_field_name( 'gplus' ); ?>" type="text" value="<?php echo esc_attr( $instance['gplus'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'delicious' ); ?>"><?php _e( 'Delicious: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'delicious' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'delicious' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['delicious'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr' ); ?>"><?php _e( 'Flickr: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['flickr'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e( 'YouTube: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['youtube'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'vimeo' ); ?>"><?php _e( 'Vimeo: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vimeo' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['vimeo'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'digg' ); ?>"><?php _e( 'Digg: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'digg' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'digg' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['digg'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'LinkedIn: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['linkedin'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['linksopen'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'linksopen' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linksopen' ) ); ?>" />
			<label for="<?php echo $this->get_field_id( 'linksopen' ); ?>"><?php _e( 'Open all links in new window ', 'startbox' ) ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Link Display:', 'startbox' ) ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
				<option value="both"<?php selected( $instance['display'], 'both' ); ?>><?php _e( 'Text and Icon', 'startbox' ); ?></option>
				<option value="icon"<?php selected( $instance['display'], 'icon' ); ?>><?php _e( 'Icon Only', 'startbox' ); ?></option>
				<option value="text"<?php selected( $instance['display'], 'text' ); ?>><?php _e( 'Text Only', 'startbox' ); ?></option>
			</select>
		</p>
		<?php
	}
}
