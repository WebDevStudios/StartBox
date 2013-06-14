<?php
/**
 * StartBox Featured Content Widget
 *
 * @package StartBox
 * @subpackage Widgets
 * @author  WebDev Studios
 * @link    http://wpstartbox.com/
 * @license GPL-2.0+
 */

/**
 * StartBox Featured Content widget.
 *
 * List posts, pages, or any other post type. Highly configurable.
 *
 * @package StartBox\Widgets
 * @author  WebDev Studios
 *
 * @since Unknown
 */
class SB_Widget_Featured_Content extends WP_Widget {

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
			'title'            => __( 'Featured', 'startbox' ),
			'content_type'     => 'posts',
			'post_categories'  => '',
			'post_count'       => '5',
			'post_showtitle'   => 'on',
			'post_limit'       => '',
			'post_meta'        => __( 'Published on [date] by [author]', 'startbox' ),
			'post_imagewidth'  => '60',
			'post_imageheight' => '60',
			'post_readmore'    => __( 'Read &amp; Discuss &#xbb;', 'startbox' ),
			'post_morelink'    => '',
			'post_moretext'    => __( 'More Posts &#xbb;', 'startbox' ),
			'post_morecount'   => '0',
			'post_exclude'     => '',
			'post_include'     => '',
			'post_offset'      => '',
			'page'             => '',
			'page_imagewidth'  => '60',
			'page_imageheight' => '60',
			'page_limit'       => '500',
			'page_readmore'    => __( 'Continue Reading &#xbb;',  'startbox' ),
		);

		$widget_ops = array(
			'classname'    =>  'sb_featured_content_widget',
			'description'  =>  __( 'Display featured content in any widget area', 'startbox' )
		);

		parent::__construct( 'featured-content-widget', __( 'SB Featured Content', 'startbox' ), $widget_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( array $args, array $instance ) {
		extract( $args );

		global $post;

		if ( $instance['content_type'] != 'page' ) {
			$category = $instance['post_categories'];
			$orderby  = $instance['post_orderby'];
			$order    = $instance['post_order'];
			$count    = $instance['post_count'];

			$showtitle = $instance['post_showtitle'];

			if ( $showmeta = $instance['post_showmeta'] ) {
				$meta = $instance['post_meta'];
			}

			if ( $showimage = $instance['post_showimage'] ) {
				$imagewidth     = $instance['post_imagewidth'];
				$imageheight    = $instance['post_imageheight'];
				$imagealignment = $instance['post_imagealignment'];
				$imagelocation  = $instance['post_imagelocation'];
			}

			$content = $limit = $readmore = '' ;
				if ( $showcontent = $instance['post_showcontent'] ) {
				$content = $instance['post_content'];
				$limit = $instance['post_limit'];
				$readmore = $instance['post_readmore'];
			}

			$morelink = $moretext = $morelocation = '';
			if ( $showmore = $instance['post_showmore'] ) {
				$morelink     = $instance['post_morelink'];
				$moretext     = $instance['post_moretext'];
				$morelocation = $instance['post_morelocation'];
			}

			$morecount = $offset = $include = $exclude = '';
			if ( $showadvanced = $instance['post_showadvanced'] ) {
				$offset    = $instance['post_offset'];
				$exclude   = ($instance['post_exclude']) ? array($instance['post_exclude']) : '' ;
				$include   = ($instance['post_include']) ? array($instance['post_include']) : '' ;
				$morecount = $instance['post_morecount'];
			}

		} else {
			$page_id        = $instance['page'];
			$showimage      = $instance['page_showimage'];
			$imagewidth     = $instance['page_imagewidth'];
			$imageheight    = $instance['page_imageheight'];
			$imagealignment = $instance['page_imagealignment'];
			$imagelocation  = $instance['page_imagelocation'];
			$showcontent    = $instance['page_showcontent'];
			$limit          = $instance['page_limit'];
			$readmore       = $instance['page_readmore'];
		}

		echo $before_widget;

		if ( $instance['content_type'] != 'page' ) {
			if ( ! empty( $instance['title'] ) ) {
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
			}
			if ( $showmore && ( 'before' == $morelocation || 'before_after' == $morelocation ) ) {
				echo '<a href="'.$morelink.'" class="more-link">'.$moretext.'</a>';
			}
			$postcount = $count + $morecount;
			$postcount = ( $postcount > 999 ) ? -1 : $postcount;
			$i = 1;
			$posts = new WP_query( apply_filters( 'sb_featured_content_widget_query_args',
				array(
					'post_type'           => $instance['content_type'],
					'cat'                 => $category,
					'posts_per_page'      => $postcount,
					'orderby'             => $orderby,
					'order'               => $order,
					'offset'              => $offset,
					'post__in'            => $include,
					'post__not_in'        => $exclude,
					'post_status'         => 'publish',
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true
				), $instance, $args ) );
			?>
			<ul>
			<?php if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();
				if ( $i <= $count ) {
					$i++;
				?>
				<li class="featured-item">
					<?php if ( $showimage && 'before' == $imagelocation ) {
						?><a class="featured-image align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php
					}
					if ( $showtitle ) {
						?><a class="featured-title" href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'startbox' ), esc_html( get_the_title(), 1 ) ) ?>" rel="bookmark"><?php the_title() ?></a><?php
					}
					if ( $showmeta ) {
						echo '<div class="featured-meta">' . do_shortcode($meta) . '</div>';
					}
					if ( $showimage && 'after' == $imagelocation ) {
						?><a class="featured-photo align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php
					}
					if ( 'none' != $content ) {
						echo '<div class="featured-content">';
						if ( $limit && $shortened_content = substr( get_the_excerpt(), 0, $limit ) ) {
							echo $shortened_content . '... ';
							if ( $readmore ) {
								echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>';
							}
						} elseif ( 'full' == $content ) {
							the_content( $readmore );
						} elseif ( 'excerpt' == $content ) {
							the_excerpt();
							if ( $readmore ) {
								echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>';
							}
						}
						echo '</div><!-- .featured-contet -->'."\n";
					}
					?>
				</li>
			<?php } else { ?>
				<li class="featured-item">
					<a class="featured-title" href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'startbox' ), esc_html( get_the_title(), 1 ) ) ?>" rel="bookmark"><?php the_title(); ?></a>
				</li>
			<?php }
			endwhile; endif;
			?>
			</ul>
			<?php
			if ( $showmore && ( 'after' == $morelocation || 'before_after' == $morelocation ) ) {
				echo '<a href="' . $morelink . '" class="more-link">' . $moretext . '</a>';
			}
		} else {
			$page = new WP_query( 'page_id=' . $page_id );
		?>
			<ul>
			<?php if ( $page->have_posts() ) : while ( $page->have_posts() ) : $page->the_post(); ?>
				<li class="featured-item">
					<?php
					if ( $showimage && 'before' == $imagelocation ) {
						?><a class="featured-image align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a>
					<?php }
					$title = ( ! $title ) ? apply_filters( 'widget_title', get_the_title() ) : $title;
					echo $before_title . $title . $after_title;

					if ( $showimage && 'after' == $imagelocation ) {
						?><a class="featured-photo align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php
					}
					if ( $limit && $shortened_content = substr( get_the_content(), 0, $limit ) ) {
						echo $shortened_content . '... ';
						if ( $readmore )
							echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>';
					} else {
						the_content( apply_filters( 'sb_read_more', $readmore ) );
					}
					?>
				</li>
			<?php
			endwhile; endif;
			?>
			</ul>
		<?php }
		echo $after_widget;
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved / updated.
	 *
	 * @todo Better sanitization for enumerated featured content widget options.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( array $new_instance, array $old_instance ) {
		$instance = $old_instance;

		$instance['content_type']        = $new_instance['content_type'];

		$instance['title']               = esc_html( $new_instance['title'] );
		$instance['post_categories']     = $new_instance['post_categories'];
		$instance['post_orderby']        = $new_instance['post_orderby'];
		$instance['post_order']          = $new_instance['post_order'];
		$instance['post_count']          = absint( $new_instance['post_count'] );

		$instance['post_showtitle']      = $new_instance['post_showtitle'];
		$instance['post_showmeta']       = $new_instance['post_showmeta'];
		$instance['post_meta']           = esc_html( $new_instance['post_meta'] );

		$instance['post_showimage']      = $new_instance['post_showimage'];
		$instance['post_imagewidth']     = absint( $new_instance['post_imagewidth'] );
		$instance['post_imageheight']    = absint( $new_instance['post_imageheight'] );
		$instance['post_imagealignment'] = $new_instance['post_imagealignment'];
		$instance['post_imagelocation']  = $new_instance['post_imagelocation'];

		$instance['post_showcontent']    = $new_instance['post_showcontent'];
		$instance['post_content']        = $new_instance['post_content'];
		$instance['post_limit']          = absint( $new_instance['post_limit'] );
		$instance['post_readmore']       = esc_html( $new_instance['post_readmore'] );

		$instance['post_showmore']       =  $new_instance['post_showmore'];
		$instance['post_morelink']       = esc_html( $new_instance['post_morelink'] );
		$instance['post_moretext']       = esc_html( $new_instance['post_moretext'] );
		$instance['post_morelocation']   = esc_html( $new_instance['post_morelocation'] );

		$instance['post_showadvanced']   = esc_html( $new_instance['post_showadvanced'] );
		$instance['post_morecount']      = absint( $new_instance['post_morecount'] );
		$instance['post_include']        = esc_html( $new_instance['post_include'] );
		$instance['post_exclude']        = esc_html( $new_instance['post_exclude'] );
		$instance['post_offset']         = absint( $new_instance['post_offset'] );

		$instance['page']                = $new_instance['page'];

		$instance['page_showimage']      = $new_instance['page_showimage'];
		$instance['page_imagewidth']     = absint( $new_instance['page_imagewidth'] );
		$instance['page_imageheight']    = absint( $new_instance['page_imageheight'] );
		$instance['page_imagealignment'] = $new_instance['page_imagealignment'];
		$instance['page_imagelocation']  = $new_instance['page_imagelocation'];

		$instance['page_showcontent']    = $new_instance['page_showcontent'];
		$instance['page_limit']          = absint( $new_instance['page_limit'] );
		$instance['page_readmore']       = esc_html( $new_instance['page_readmore'] );

		return $instance;

	}

	/**
	 * Helper function to hide sections of redundant settings.
	 *
	 * @param string $selector
	 */
	protected function advanced_hide( $selector ) {
		if ( 'on' != $selector ) {
			echo ' style="display:none;"';
		}
	}

	/**
	 * Echo the settings update form.
	 *
	 * @todo Consolidate featured content widget settings.
	 *
	 * @param array $instance Current settings
	 */
	public function form( array $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		$post_types = get_post_types( array( 'public' => true, ), 'objects' );
		$dropdown_category_args = array(
			'class'           => 'widefat',
			'show_option_all' => 'All',
			'orderby'         => 'Name',
			'id'              => $this->get_field_id('post_categories'),
			'name'            => $this->get_field_name('post_categories'),
			'selected'        => $instance['post_categories'],
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'content_type' ); ?>"><?php _e( 'Featured Content Type:', 'startbox' ) ?></label>
			<select class="widefat content-selector" id="<?php echo esc_attr( $this->get_field_id( 'content_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_type' ) ); ?>">
			<?php
				foreach ( $post_types as $post_type ) {
					if ( ! in_array( $post_type->name, array( 'attachment', 'revision', 'nav_menu_item', 'slideshow' ) ) ) {
						echo '<option value="' . $post_type->name . '"' . selected( $instance['content_type'], $post_type->name ) . '>' . $post_type->label . '</option>';
					}
				}
			?>
			</select>
		</p>

		<div id="<?php echo esc_attr( $this->get_field_id( 'post_options' ) ); ?>" class="content-settings post-settings"<?php if( 'page' == $instance['content_type'] ){ echo ' style="display:none;"'; }?>>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_categories' ); ?>"><?php _e( 'Category: ', 'startbox' ) ?></label>
				<?php wp_dropdown_categories( $dropdown_category_args ); ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_orderby' ); ?>"><?php _e( 'Order By:', 'startbox' ) ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_orderby' ) ); ?>">
					<option value="date"<?php selected( $instance['post_orderby'], 'date' ); ?>><?php _e( 'Date', 'startbox' ) ?></option>
					<option value="id"<?php selected( $instance['post_orderby'], 'id' ); ?>><?php _e( 'Post ID', 'startbox' ) ?></option>
					<option value="title"<?php selected( $instance['post_orderby'], 'title' ); ?>><?php _e( 'Title', 'startbox' ) ?></option>
					<option value="author"<?php selected( $instance['post_orderby'], 'author' ); ?>><?php _e( 'Author', 'startbox' ) ?></option>
					<option value="comment_count"<?php selected( $instance['post_orderby'], 'comment_count' ); ?>><?php _e( 'Comment Count', 'startbox' ) ?></option>
					<option value="random" <?php selected( $instance['post_orderby'], 'random' ); ?>><?php _e( 'Random', 'startbox' ) ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_order' ); ?>"><?php _e( 'Sort Order:', 'startbox' ) ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_order' ) ); ?>">
					<option value="DESC"<?php selected( $instance['post_order'], 'DESC' ); ?>><?php _e( 'Descending (3, 2, 1; c, b, a)', 'startbox' ) ?></option>
					<option value="ASC"<?php selected( $instance['post_order'], 'ASC' ); ?>><?php _e( 'Ascending (1, 2, 3; a, b, c)', 'startbox' ) ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_count' ); ?>"><?php _e( 'Number of posts to display: ', 'startbox' ) ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'post_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_count' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_count'] ); ?>" size="3" />
			</p>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showtitle'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_showtitle' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showtitle' ); ?>"><?php _e( 'Display Post Title', 'startbox' ) ?></label>
			</p>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showmeta'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showmeta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showmeta') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showmeta' ); ?>"><?php _e( 'Display Meta Information', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'post_info' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['post_showmeta'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_meta' ); ?>"><?php _e( 'Display Meta Information: ', 'startbox' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_meta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_meta' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_meta'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'You can use any <a href="http://docs.wpstartbox.com/shortcodes" target="_blank">shortcodes</a> you like here, including: [author], [categories], [comments], [date], [time], [tags] and [edit].', 'startbox' ); ?></span>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showimage'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showimage' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_showimage' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showimage' ); ?>"> <?php _e( 'Display Featured Image', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'post_image' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['post_showimage'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_imagewidth' ); ?>"><?php _e( 'Image Size:', 'startbox' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'post_imagewidth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_imagewidth' ) ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['post_imagewidth'] ); ?>" /> &#xd7;
					<input id="<?php echo esc_attr( $this->get_field_id( 'post_imageheight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_imageheight' ) ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['post_imageheight'] ); ?>" />px
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_imagealignment' ); ?>"><?php _e( 'Image Alignment:', 'startbox' ); ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'post_imagealignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_imagealignment' ) ); ?>">
						<option value="none"<?php selected( $instance['post_imagealignment'], 'none' ); ?>><?php _e( 'None', 'startbox' ); ?></option>
						<option value="left"<?php selected( $instance['post_imagealignment'], 'left' ); ?>><?php _e( 'Left', 'startbox' ); ?></option>
						<option value="right"<?php selected( $instance['post_imagealignment'], 'right' ); ?>><?php _e( 'Right', 'startbox' ); ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_imagelocation' ); ?>"><?php _e( 'Image Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'post_imagelocation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_imagelocation' ) ); ?>">
						<option value="before"<?php selected( $instance['post_imagelocation'], 'before' ); ?>><?php _e( 'Before Post Info', 'startbox' ) ?></option>
						<option value="after"<?php selected( $instance['post_imagelocation'], 'after'); ?>><?php _e( 'After Post Info', 'startbox' ) ?></option>
					</select>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showcontent'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showcontent' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_showcontent' ) ); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showcontent' ); ?>"><?php _e( 'Display Post Content', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'post_contents' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['post_showcontent'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_content' ); ?>"><?php _e( 'Content:', 'startbox' ); ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'post_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_content' ) ); ?>">
						<option value="excerpt"<?php selected( $instance['post_content'], 'excerpt' ); ?>><?php _e( 'Post Excerpt', 'startbox' ); ?></option>
						<option value="full"<?php selected( $instance['post_content'], 'full' ); ?>><?php _e( 'Full Content', 'startbox' ); ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_limit' ); ?>"><?php echo sprintf( __( 'Limit content to %s characters', 'startbox' ), '<input id="' . $this->get_field_id( 'post_limit' ) . '" name="' . $this->get_field_name( 'post_limit' ) . '" type="text" size="3" value="' . $instance['post_limit'] . '" />' ); ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'Leave blank for no limit.', 'startbox' ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_readmore' ); ?>"><?php _e( 'Read More Text: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_readmore' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_readmore' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_readmore'] ); ?>" />
					<span style="font-size:smaller"><?php _e( 'Leave blank to hide the Read More link.', 'startbox' ); ?></span>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showmore'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showmore' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_showmore' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showmore' ); ?>"><?php _e( 'Display Link to More Posts', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'post_more' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['post_showmore'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_morelink' ); ?>"><?php _e( 'Link URL: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_morelink' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_morelink' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_morelink'] ); ?>" /><br/>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_moretext' ); ?>"><?php _e( 'Link Text: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_moretext' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_moretext' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_moretext'] ); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_morelocation' ); ?>"><?php _e( 'Link Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'post_morelocation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_morelocation' ) ); ?>">
						<option value="before"<?php selected( $instance['post_morelocation'], 'before' ); ?>><?php _e( 'Before Posts', 'startbox' ) ?></option>
						<option value="after"<?php selected( $instance['post_morelocation'], 'after' ); ?>><?php _e( 'After Posts', 'startbox' ) ?></option>
						<option value="after"<?php selected( $instance['post_morelocation'], 'before_after' ); ?>><?php _e( 'Before &amp; After Posts', 'startbox' ) ?></option>
					</select>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showadvanced'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'post_showadvanced' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_showadvanced' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'post_showadvanced' ); ?>"><?php _e( 'Display Advanced Query Options', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'post_advanced' ) ); ?>" class="advanced-settings" <?php $this->advanced_hide( $instance['post_showadvanced'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_morecount' ); ?>"><?php _e( 'Display ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'post_morecount' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_morecount' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_morecount'] ); ?>" size="3" />
					<label for="<?php echo $this->get_field_id( 'post_morecount' ); ?>"><?php _e( ' additional posts', 'startbox' ) ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'These posts will display as title-only.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_exclude' ); ?>"><?php _e( 'Exclude these posts: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_exclude'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Comma-separated list of post ID\'s.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_include' ); ?>"><?php _e( 'Include only these posts: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_include' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_include'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Comma-separated list of post ID\'s.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'post_offset' ); ?>"><?php _e( 'Offset posts: ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'post_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['post_offset'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Number of posts to skip before displaying.', 'startbox' ) ?></span>
				</p>

				<hr/>
			</div>
		</div>

		<div id="<?php echo esc_attr( $this->get_field_id( 'page_options' ) ); ?>" class="content-settings page-settings"<?php if( 'posts' == $instance['content_type'] ){ echo ' style="display:none;"'; } ?>>
			<p>
				<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Page: ', 'startbox' ) ?></label>
				<?php wp_dropdown_pages( $this->get_field_id( 'page' ) . '&name=' . $this->get_field_name( 'page' ) . '&selected=' . $instance['page'] ); ?>
				<span style="font-size:smaller"><?php _e( 'Leave widget title blank to use Page Title instead', 'startbox' ) ?></span>
			</p>
			<hr/>
			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['page_showimage'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'page_showimage' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_showimage' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'page_showimage' ); ?>"> <?php _e( 'Display Featured Image', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'page_image' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['page_showimage'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'page_imagewidth' ); ?>"><?php _e( 'Image Size: ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'page_imagewidth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_imagewidth' ) ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['page_imagewidth'] ); ?>" /> &#xd7;
					<input id="<?php echo esc_attr( $this->get_field_id( 'page_imageheight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_imageheight' ) ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['page_imageheight'] ); ?>" />px
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'page_imagealignment' ); ?>"><?php _e( 'Image Alignment:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'page_imagealignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_imagealignment' ) ); ?>">
						<option value="none"<?php selected( $instance['page_imagealignment'], 'none' ); ?>><?php _e( 'None', 'startbox' ) ?></option>
						<option value="left"<?php selected( $instance['page_imagealignment'], 'left' ); ?>><?php _e( 'Left', 'startbox' ) ?></option>
						<option value="right"<?php selected( $instance['page_imagealignment'], 'right' ); ?>><?php _e( 'Right', 'startbox' ) ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'page_imagelocation' ); ?>"><?php _e( 'Image Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'page_imagelocation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_imagelocation' ) ); ?>">
						<option value="before"<?php selected( $instance['page_imagelocation'], 'before' ); ?>><?php _e( 'Before Title', 'startbox' ) ?></option>
						<option value="after"<?php selected( $instance['page_imagelocation'], 'after' ); ?>><?php _e( 'After Title', 'startbox' ) ?></option>
					</select>
				</p>
			<hr/>
			</div>
			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['page_showcontent'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'page_showcontent' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_showcontent' ) ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id( 'page_showcontent' ); ?>"><?php _e( 'Display Page Content', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id( 'page_content' ) ); ?>" class="advanced-settings"<?php $this->advanced_hide( $instance['page_showcontent'] ); ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'page_limit' ); ?>"><?php echo sprintf( __( 'Limit content to %s characters', 'startbox' ), '<input id="' . $this->get_field_id( 'page_limit' ) . '" name="' . $this->get_field_name( 'page_limit' ) . '" type="text" size="3" value="' . $instance['page_limit'] . '" />' ); ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'Leave blank for no limit.', 'startbox' ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'page_readmore' ); ?>"><?php _e( 'Read More Text:', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'page_readmore' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_readmore' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['page_readmore'] ); ?>" />
					<span style="font-size:smaller"><?php _e( 'Leave blank to hide the Read More link.', 'startbox' ); ?></span>
				</p>
			</div>
			<hr/>
		</div>
		<?php
	}
}
