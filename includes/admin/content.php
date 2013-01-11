<?php
/**
 * Content settings for adjusting post meta, content, navigation, thumbnails, etc.
 */
class sb_content_settings extends sb_settings {

	function sb_content_settings() {
		$this->name = __( 'Content Settings', 'startbox' );
		$this->slug = 'sb_content_settings';
		$this->description = __( 'Take full control over the content portions of your site. Set thumbnail sizes, post navigation options and post meta information.', 'startbox' );
		$this->location = 'primary';
		$this->priority = 'core';
		$this->options = array(
			'post_content_heading' => array(
					'type'		=> 'intro',
					'label'		=> __( 'Post Content', 'startbox' )
			),
			'home_post_content' => array(
					'type'		=> 'select',
					'label'		=> __( 'Post Content on Homepage', 'startbox' ),
					'options'	=> array(
							'excerpt'	=> __( 'Excerpt', 'startbox' ),
							'full'		=> __( 'Full Post', 'startbox' )
						),
					'default'	=> 'excerpt',
					'align'		=> 'right',
					'size'		=> 'medium',
					'help'		=> __( 'Choose whether you would like to display the full post, or only an exceprt, on the homepage.', 'startbox' )
				),
			'archive_post_content' => array(
					'type'		=> 'select',
					'label'		=> __( 'Post Content in Archives', 'startbox' ),
					'options'	=> array(
							'excerpt'	=> __( 'Excerpt', 'startbox' ),
							'full'		=> __( 'Full Post', 'startbox' )
						),
					'default'	=> 'excerpt',
					'align'		=> 'right',
					'size'		=> 'medium',
					'help'		=> __( 'Choose whether you would like to display the full post, or only an exceprt, on all other blog pages.', 'startbox' )
				),
			'more_text' => array(
					'type'		=> 'text',
					'label'		=> __( 'Read More text', 'startbox' ),
					'default'	=> __( 'Continue Reading: [title]', 'startbox' ),
					'size'		=> 'medium',
					'align'		=> 'right',
					'help'		=> __( 'Specify your own link text for the "Read More" link on posts.', 'startbox' )
				),
			'author_bio' => array(
					'type'		=> 'select',
					'label'		=> __( 'Display Author Bio on Single posts', 'startbox' ),
					'options'	=> array(
								'disabled'	=> __( 'Disabled', 'startbox' ),
								'before'	=> __( 'Show Before Post Content', 'startbox' ),
								'after'		=> __( 'Show After Post Content', 'startbox' ),
						),
					'align'		=> 'right',
					'desc'		=> __( 'Author bio only displays if author provides a description in their profile.', 'startbox' ),
					'default'	=> 'true',
					'size'		=> 'medium',
					'help'		=> __( 'Select where you would like author bios to appear when viewing a single post (or disable them altogether).', 'startbox' )
				),
			'content_div_1' => array( 'type' => 'divider' ),
			'post_meta_heading' => array(
					'type'		=> 'intro',
					'label'		=> __( 'Post Meta', 'startbox' ),
					'desc'		=> sprintf( __( 'Control the meta information displayed on each post. You can use any %s you like, including: [author], [categories], [comments], [date], [time], [tags] and [edit].', 'startbox' ), '<a href="http://docs.wpstartbox.com/shortcodes" target="_blank">shortcodes</a>' ),
					'help'		=> __( 'Use shortcodes to control what information you would like to display for each post (e.g. Post author, category, comment count, etc).', 'startbox' )
				),
			'post_header_meta' => array(
					'type'		=> 'text',
					'label'		=> __( 'Post Header Meta', 'startbox' ),
					'default'	=> __( 'Published in [categories] on [date] [edit]', 'startbox' ),
					'size'		=> 'large',
					'align'		=> 'right',
					'kses'		=> 'unfiltered_html'
				),
			'post_footer_meta' => array(
					'type'		=> 'text',
					'label'		=> __( 'Post Footer Meta', 'startbox' ),
					'default'	=> '[tags] [comments]',
					'size'		=> 'large',
					'align'		=> 'right',
					'kses'		=> 'unfiltered_html'
			),
			'content_div_2' => array( 'type' => 'divider' ),
			'post_thumbnail_heading' => array(
					'type'		=> 'intro',
					'label'		=> __( 'Post Thumbnails', 'startbox' )
				),
			'enable_post_thumbnails' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Enable Post Thumbnails', 'startbox' ),
					'default'	=> true,
					'align'		=> 'left',
					'help'		=> __( 'Enable thumbnails for posts in archive lists (Default: true).', 'startbox' )
				),
			'post_thumbnail_rss' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Include Post Thumbnails in RSS feed', 'startbox' ),
					'default'	=> true,
					'align'		=> 'left',
					'help'		=> __( 'Enable thumbnails for posts in RSS feeds (Default: true).', 'startbox' )
				),
			'post_thumbnail_use_attachments' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Use any attached image if no Featured Image specified', 'startbox' ),
					'default'	=> true,
					'align'		=> 'left',
					'help'		=> __( 'If there is no user-specified "Featured Image" for a particular post, the theme will automatically attempt to use the last attached image (Default: true).', 'startbox' )
				),
			'post_thumbnail_hide_nophoto' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Hide thumbnails if no preview available', 'startbox' ),
					'default'	=> false,
					'align'		=> 'left',
					'help'		=> __( 'Disable the default "No Preview Available" image that is used when no post thumbnail is found.', 'startbox' )
				),
			'post_thumbnail_default_image' => array(
					'type'		=> 'upload',
					'label'		=> __( 'Default Thumbnail', 'startbox' ),
					'default'	=> IMAGES_URL . '/nophoto.jpg',
					'help'		=> __( 'Upload/Select your own custom defoult thumbnail to use when no available post thumbnail is found.', 'startbox' )
				),
			'post_thumbnail_width' => array(
					'type'		=> 'text',
					'label'		=> __( 'Thumbnail Width', 'startbox' ),
					'after'		=> ' px',
					'align'		=> 'right',
					'size'		=> 'small',
					'default'	=> '200',
					'help'		=> __( 'Specify your own thumbnail width in pixels (Default: 200).', 'startbox' )
				),
			'post_thumbnail_height' => array(
					'type'		=> 'text',
					'label'		=> __( 'Thumbnail Height', 'startbox' ),
					'after'		=> ' px',
					'align'		=> 'right',
					'size'		=> 'small',
					'default'	=> '200',
					'help'		=> __( 'Specify your own thumbnail height in pixels (Default: 200).', 'startbox' )
				),
			'post_thumbnail_align'	=> array(
					'type'		=> 'select',
					'label'		=> __('Image Crop Alignment','startbox'),
					'options'	=> array(
						'tl'	=> __('Top Left','startbox'),
						'tc'	=> __('Top Center','startbox'),
						'tr'	=> __('Top Right','startbox'),
						'l'		=> __('Middle Left','startbox'),
						'c'		=> __('Center','startbox'),
						'r'		=> __('Middle Right','startbox'),
						'bl'	=> __('Bottom Left','startbox'),
						'bc'	=> __('Bottom Center','startbox'),
						'br'	=> __('Bottom Right','startbox')
					),
					'default'	=> 'tc',
					'align'		=> 'right',
					'help'		=> __( 'Select where you would like the thumbnails to center the crop (Default: Top Center).', 'startbox' )
				),
			'content_div_3' => array( 'type' => 'divider' ),
			'post_navigation_heading' => array(
					'type'		=> 'intro',
					'label'		=> __( 'Post Navigation', 'startbox' ),
					'desc'		=> __( 'Specify how navigation for prev/next posts should appear.', 'startbox' ),
					'help'		=> __( 'Select where post navigation should appear for Blog, Archive and Single Post views', 'startbox' )
			),
			'archive_navigation' => array(
					'type'		=> 'select',
					'label'		=> __( 'Blog & Archives Navigation', 'startbox' ),
					'options'	=> array(
								'none' 	=> __( 'Disabled', 'startbox' ),
								'above' => __( 'Show Above Posts', 'startbox' ),
								'below' => __( 'Show Below Posts', 'startbox' ),
								'both' 	=> __( 'Show Above & Below Posts', 'startbox' )
						),
					'default'	=> 'below',
					'align'		=> 'right',
					'size'		=> 'medium'
				),
			'post_navigation' => array(
					'type'		=> 'select',
					'label'		=> __( 'Single Post Navigation', 'startbox' ),
					'options'	=> array(
								'none' 	=> __( 'Disabled', 'startbox' ),
								'above' => __( 'Show Above Posts', 'startbox' ),
								'below' => __( 'Show Below Posts', 'startbox' ),
								'both' 	=> __( 'Show Above & Below Posts', 'startbox' )
						),
					'default'	=> 'below',
					'align'		=> 'right',
					'size'		=> 'medium'
				)
		);
		parent::__construct();
	}

	// Prev/Next Post Links
	function sb_post_nav() {
		$position = ( did_action('sb_after_content') ) ? 'below' : 'above';

		if ( is_attachment() || ( is_page() && !is_page_template('page_category.php') ) )
			return;

		if ( !is_single() && ( sb_get_option( 'archive_navigation' ) == $position || sb_get_option( 'archive_navigation' ) == 'both' ) ) {
			echo '<div id="nav-' . $position . '" class="navigation">';
			if ( function_exists('wp_pagenavi') ) { wp_pagenavi(); }
			else { ?>
				<div class="nav-previous"><?php next_posts_link( sprintf( __('%s Older posts', 'startbox'), '<span class="meta-nav">&laquo;</span>' ) ); ?></div>
				<div class="nav-next"><?php previous_posts_link( sprintf( __('Newer posts %s', 'startbox'), '<span class="meta-nav">&raquo;</span>' ) ); ?></div>
			<?php }
			echo '</div>';
		} elseif ( is_single() && ( sb_get_option( 'post_navigation' ) == $position || sb_get_option( 'post_navigation' ) == 'both' ) ) { ?>
			<div id="nav-<?php echo $position; ?>" class="navigation">
				<div class="nav-previous"><?php previous_post_link('%link', apply_filters( 'sb_previous_post_link', '<span class="meta-nav">&laquo;</span> %title') ); ?></div>
				<div class="nav-next"><?php next_post_link('%link', apply_filters( 'sb_next_post_link', '%title <span class="meta-nav">&raquo;</span>') ); ?></div>
			</div>
		<?php }
	}

	// More Text filter
	function more_text() {
		return sb_get_option( 'more_text' );
	}

	// Display Author Bio
	function sb_author_bio() {
		if ( get_the_author_meta( 'description' ) && is_single() ) { do_shortcode('[author_bio]'); } // If a user has filled out their description, show a bio on their entries
	}

	// Post Meta
	function sb_header_meta() {
		$content = sb_get_option( 'post_header_meta' ) ;
		echo apply_filters( 'sb_header_meta', do_shortcode($content) );
	}
	function sb_footer_meta() {
		$content = sb_get_option( 'post_footer_meta' );
		echo apply_filters( 'sb_footer_meta', do_shortcode($content) );
	}

	// Post Thumbnails
	function image_settings($defaults) {
		$options = get_option( THEME_OPTIONS );

		( isset($options['post_thumbnail_width']) ? $defaults['width'] = $options['post_thumbnail_width'] : '' );
		( isset($options['post_thumbnail_height']) ? $defaults['height'] = $options['post_thumbnail_height'] : '' );
		( isset($options['post_thumbnail_align']) ? $defaults['align'] = $options['post_thumbnail_align'] : '' );
		( isset($options['post_thumbnail_hide_nophoto']) ? $defaults['hide_nophoto'] = $options['post_thumbnail_hide_nophoto'] : '' );
		( isset($options['post_thumbnail_use_attachments']) ? $defaults['use_attachments'] = $options['post_thumbnail_use_attachments'] : '' );
		( isset($options['enable_post_thumbnails']) ? $defaults['enabled'] = $options['enable_post_thumbnails'] : '' );

		return $defaults;
	}


	function image_default() { return sb_get_option( 'post_thumbnail_default_image' ); }


	// Post Thumbnail in RSS feed
	function post_image_feeds($content) {
		global $post;
		$content = '<div><a href="' . the_permalink() . '" title="' . esc_attr( get_the_title() ) . '">' . sb_post_image() . '</a></div>' . $content;
		return $content;
	}

	function hooks() {
		add_filter( 'sb_read_more', array($this, 'more_text' ) );
		add_action( 'sb_post_header', array($this, 'sb_header_meta' ) );
		add_action( 'sb_post_footer', array($this, 'sb_footer_meta' ) );
		if ( sb_get_option( 'author_bio' ) == 'before' ) { add_action( 'sb_before_post_content', array( $this, 'sb_author_bio' ) ); }
		if ( sb_get_option( 'author_bio' ) == 'after' ) { add_action( 'sb_after_post_content', array( $this, 'sb_author_bio' ) ); }
		add_action( 'sb_before_content', array( $this, 'sb_post_nav' ) );
		add_action( 'sb_after_content', array( $this, 'sb_post_nav' ) );
		if ( sb_get_option( 'post_thumbnail_rss' ) ) {
			add_filter('the_excerpt_rss', array( $this, 'post_image_feeds' ) );
			add_filter('the_content_feed', array( $this, 'post_image_feeds' ) );
		}
		add_filter( 'sb_post_image_settings', array( $this, 'image_settings' ) );
		add_filter( 'sb_post_image_none', array( $this, 'image_default' ) );

	}

}

sb_register_settings('sb_content_settings');
