<?php
/**
 * StartBox Featured Content Widget
 *
 * List posts, pages, or any other post type. Highly configurable.
 *
 * @package StartBox
 * @subpackage Widgets
 * @since Unknown
 */

function load_widget_sb_featured_content() { // Widget: Featured Content
	register_widget('sb_featured_content_widget');
}
add_action( 'widgets_init', 'load_widget_sb_featured_content', 0 );

function load_featured_widget_js() {
	wp_enqueue_script( 'sb-widgets', SCRIPTS_URL . '/widgets.js', array('jquery') );
}
add_action( 'sidebar_admin_setup', 'load_featured_widget_js' );

class sb_featured_content_widget extends WP_Widget {

	function sb_featured_content_widget() {

		$widget_ops = array(
			'classname'    =>  'sb_featured_content_widget',
			'description'  =>  __( "Display featured content in any widget area", "startbox" )
		);
		$this->WP_Widget( 'featured-content-widget', __('SB Featured Content', 'startbox'), $widget_ops);

	}

	function widget($args, $instance) {
		extract($args);

		global $post;

		$title = apply_filters('widget_title', $instance['title'] );
		if ( $instance['content_type'] != 'page' ) {
			$category = $instance['post_categories'];
			$orderby = $instance['post_orderby'];
			$order = $instance['post_order'];
			$count = $instance['post_count'];

			$showtitle = $instance['post_showtitle'];

			if ( $showmeta = $instance['post_showmeta'] ) { $meta = $instance['post_meta']; }

			if ( $showimage = $instance['post_showimage'] ) {
				$imagewidth = $instance['post_imagewidth'];
				$imageheight = $instance['post_imageheight'];
				$imagealignment = $instance['post_imagealignment'];
				$imagelocation = $instance['post_imagelocation'];
			}

			$content = $limit = $readmore = '' ;
			if ( $showcontent = $instance['post_showcontent'] ) {
				$content = $instance['post_content'];
				$limit = $instance['post_limit'];
				$readmore = $instance['post_readmore'];
			}

			$morelink = $moretext = $morelocation = '';
			if ( $showmore = $instance['post_showmore'] ) {
				$morelink = $instance['post_morelink'];
				$moretext = $instance['post_moretext'];
				$morelocation = $instance['post_morelocation'];
			}

			$morecount = $offset = $include = $exclude = '';
			if ( $showadvanced = $instance['post_showadvanced'] ) {
				$offset = $instance['post_offset'];
				$exclude = ($instance['post_exclude']) ? array($instance['post_exclude']) : '' ;
				$include = ($instance['post_include']) ? array($instance['post_include']) : '' ;
				$morecount = $instance['post_morecount'];
			}

		} else {
			$page_id = $instance['page'];
			$showimage = $instance['page_showimage'];
			$imagewidth = $instance['page_imagewidth'];
			$imageheight = $instance['page_imageheight'];
			$imagealignment = $instance['page_imagealignment'];
			$imagelocation = $instance['page_imagelocation'];
			$showcontent = $instance['page_showcontent'];
			$limit = $instance['page_limit'];
			$readmore = $instance['page_readmore'];
		}

		echo $before_widget;

		if ( $instance['content_type'] != 'page' ) {
			if ($title && $instance['content_type'] != 'page') { echo $before_title . $title . $after_title; }
			if ($showmore && ( $morelocation == 'before' || $morelocation == 'before_after' ) ) { echo '<a href="'.$morelink.'" class="more-link">'.$moretext.'</a>'; }
			$postcount = $count + $morecount; $i = 1;
			$posts = new WP_query( array(
				'post_type' => $instance['content_type'],
				'cat' => $category,
				'posts_per_page' => $postcount,
				'orderby' => $orderby,
				'order' => $order,
				'offset' => $offset,
				'post__in' => $include,
				'post__not_in' => $exclude
				) );
			?>
			<ul>
			<?php if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post(); if ( $i <= $count ) { $i++; ?>
				<li class="featured-item">
					<?php if ($showimage && $imagelocation == 'before') { ?><a class="featured-image align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php } ?>
					<?php if ($showtitle ) {?><a class="featured-title" href="<?php the_permalink() ?>" title="<?php printf(__('Permalink to %s', 'startbox'), esc_html(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title() ?></a><?php } ?>
					<?php if ($showmeta) { echo '<div class="featured-meta">' . do_shortcode($meta) . '</div>'; } ?>
					<?php
						if ($showimage && $imagelocation == 'after') { ?><a class="featured-photo align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php }
						if ( $content != "none" ) {
							echo '<div class="featured-content">';
							if ( $limit ) { echo substr( get_the_excerpt(), 0, $limit) . '... '; if ( $readmore ) echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>';}
							elseif ( $content == "full" ) { the_content( $readmore ); }
							elseif ( $content == "excerpt" ) { the_excerpt(); if ( $readmore ) echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>'; }
							echo '</div><!-- .featured-contet -->'."\n";
						}
					?>
				</li>
			<?php } else { ?>
				<li class="featured-item">
					<a class="featured-title" href="<?php the_permalink() ?>" title="<?php printf(__('Permalink to %s', 'startbox'), esc_html(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title() ?></a>
				</li>
			<?php } endwhile; endif; ?>
			</ul>
			<?php
			if ($showmore && ( $morelocation == 'after' || $morelocation == 'before_after' ) ) { echo '<a href="'.$morelink.'" class="more-link">'.$moretext.'</a>'; }
		} else {
			$page = new WP_query( 'page_id='.$page_id );
		?>
			<ul>
			<?php if ( $page->have_posts() ) : while ( $page->have_posts() ) : $page->the_post(); ?>
				<li class="featured-item">
					<?php if ($showimage && $imagelocation == 'before') { ?><a class="featured-image align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php } ?>
					<?php $title = (!$title) ? apply_filters( 'widget_title', get_the_title() ) : $title; echo $before_title . $title . $after_title; ?>
					<?php if ($showimage && $imagelocation == 'after') { ?><a class="featured-photo align<?php echo $imagealignment; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php sb_post_image( array( 'width' => $imagewidth, 'height' => $imageheight ) ); ?></a><?php } ?>
					<?php
						if ( $limit ) { echo substr( get_the_content(), 0, $limit) . '... '; if ( $readmore ) echo '<a href="'. get_permalink() .'" title="' . $readmore . '" rel="bookmark" class="more-link">' . $readmore . '</a>'; }
						else { the_content( apply_filters( 'sb_read_more', $readmore ) ); }
					?>
				</li>
			<?php endwhile; endif; ?>
			</ul>
		<?php }
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['content_type'] = $new_instance['content_type'];

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['post_categories'] = $new_instance['post_categories'];
		$instance['post_orderby'] = $new_instance['post_orderby'];
		$instance['post_order'] = $new_instance['post_order'];
		$instance['post_count'] = strip_tags( $new_instance['post_count'] );

		$instance['post_showtitle'] = $new_instance['post_showtitle'];
		$instance['post_showmeta'] = $new_instance['post_showmeta'];
		$instance['post_meta'] = strip_tags( $new_instance['post_meta'] );

		$instance['post_showimage'] = $new_instance['post_showimage'];
		$instance['post_imagewidth'] = strip_tags( $new_instance['post_imagewidth'] );
		$instance['post_imageheight'] = strip_tags( $new_instance['post_imageheight'] );
		$instance['post_imagealignment'] = $new_instance['post_imagealignment'];
		$instance['post_imagelocation'] = $new_instance['post_imagelocation'];

		$instance['post_showcontent'] = $new_instance['post_showcontent'];
		$instance['post_content'] = $new_instance['post_content'];
		$instance['post_limit'] = strip_tags( $new_instance['post_limit'] );
		$instance['post_readmore'] = strip_tags( $new_instance['post_readmore'] );

		$instance['post_showmore'] =  $new_instance['post_showmore'];
		$instance['post_morelink'] = strip_tags( $new_instance['post_morelink'] );
		$instance['post_moretext'] = strip_tags( $new_instance['post_moretext'] );
		$instance['post_morelocation'] = strip_tags( $new_instance['post_morelocation'] );

		$instance['post_showadvanced'] = strip_tags( $new_instance['post_showadvanced'] );
		$instance['post_morecount'] = strip_tags( $new_instance['post_morecount'] );
		$instance['post_include'] = strip_tags( $new_instance['post_include'] );
		$instance['post_exclude'] = strip_tags( $new_instance['post_exclude'] );
		$instance['post_offset'] = strip_tags( $new_instance['post_offset'] );

		$instance['page'] = $new_instance['page'];

		$instance['page_showimage'] = $new_instance['page_showimage'];
		$instance['page_imagewidth'] = strip_tags( $new_instance['page_imagewidth'] );
		$instance['page_imageheight'] = strip_tags( $new_instance['page_imageheight'] );
		$instance['page_imagealignment'] = $new_instance['page_imagealignment'];
		$instance['page_imagelocation'] = $new_instance['page_imagelocation'];

		$instance['page_showcontent'] = $new_instance['page_showcontent'];
		$instance['page_limit'] = strip_tags( $new_instance['page_limit'] );
		$instance['page_readmore'] = strip_tags( $new_instance['page_readmore'] );

		return $instance;

	}

	function advanced_hide( $selector ) {
		if ( $selector != 'on' ) { echo ' style="display:none;"'; }
	}

	function form($instance) {
		$defaults = array(
			'title'				=> 'Featured',
			'content_type'		=> 'posts',
			'post_categories'	=> '',
			'post_count'		=> '5',
			'post_showtitle'	=> 'on',
			'post_limit'		=> '',
			'post_meta'			=> 'Published on [date] by [author]',
			'post_imagewidth'	=> '60',
			'post_imageheight'	=> '60',
			'post_readmore'		=> 'Read &amp; Discuss &raquo;',
			'post_morelink'		=> '',
			'post_moretext'		=> 'More Posts &raquo;',
			'post_morecount'	=> '0',
			'post_exclude'		=> '',
			'post_include'		=> '',
			'post_offset'		=> '',
			'page'				=> '',
			'page_imagewidth'	=> '60',
			'page_imageheight'	=> '60',
			'page_limit'		=> '500',
			'page_readmore'		=> 'Continue Reading &raquo;'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$post_types = get_post_types( array('public'=>true),'objects');

	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('content_type'); ?>"><?php _e( 'Featured Content Type:', 'startbox' ) ?></label>
			<select class="widefat content-selector" id="<?php echo esc_attr( $this->get_field_id('content_type') ); ?>" name="<?php echo esc_attr( $this->get_field_name('content_type') ); ?>">
				<?php
					foreach ($post_types as $post_type ) {
						if ( !in_array( $post_type->name, array('attachment', 'revision', 'nav_menu_item', 'slideshow') ) )
							echo '<option value="' . $post_type->name . '"' . selected( $instance['content_type'], $post_type->name ) . '>' . $post_type->label . '</option>';
					}
				?>
			</select>
		</p>

		<div id="<?php echo esc_attr( $this->get_field_id('post_options') ); ?>" class="content-settings post-settings"<?php if( $instance['content_type'] == 'page' ){ echo ' style="display:none;"'; }?>>
			<p>
				<label for="<?php echo $this->get_field_id('post_categories'); ?>"><?php _e( 'Category: ', 'startbox' ) ?></label>
				<?php wp_dropdown_categories( 'class=widefat&show_option_all=All&orderby=Name&id=' . $this->get_field_id('post_categories') . '&name=' . $this->get_field_name('post_categories') . '&selected=' . $instance['post_categories'] ); ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('post_orderby'); ?>"><?php _e( 'Order By:', 'startbox' ) ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_orderby') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_orderby') ); ?>">
					<option value="date" <?php if($instance['post_orderby'] == 'date'){ echo 'selected="selected"'; }?>><?php _e( 'Date', 'startbox' ) ?></option>
					<option value="ID" <?php if($instance['post_orderby'] == 'id'){ echo 'selected="selected"'; }?>><?php _e( 'Post ID', 'startbox' ) ?></option>
					<option value="title" <?php if($instance['post_orderby'] == 'title'){ echo 'selected="selected"'; }?>><?php _e( 'Title', 'startbox' ) ?></option>
					<option value="author" <?php if($instance['post_orderby'] == 'author'){ echo 'selected="selected"'; }?>><?php _e( 'Author', 'startbox' ) ?></option>
					<option value="comment_count" <?php if($instance['post_orderby'] == 'comment_count'){ echo 'selected="selected"'; }?>><?php _e( 'Comment Count', 'startbox' ) ?></option>
					<option value="random" <?php if($instance['post_orderby'] == 'random'){ echo 'selected="selected"'; }?>><?php _e( 'Random', 'startbox' ) ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('post_order'); ?>"><?php _e( 'Sort Order:', 'startbox' ) ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_order') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_order') ); ?>">
					<option value="DESC" <?php if($instance['post_order'] == 'DESC'){ echo 'selected="selected"'; }?>><?php _e( 'Descending (3, 2, 1; c, b, a)', 'startbox' ) ?></option>
					<option value="ASC" <?php if($instance['post_order'] == 'ASC'){ echo 'selected="selected"'; }?>><?php _e( 'Ascending (1, 2, 3; a, b, c)', 'startbox' ) ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e( 'Number of posts to display: ', 'startbox' ) ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id('post_count') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_count') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_count'] ); ?>" size="3" />
			</p>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showtitle'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showtitle') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showtitle') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('post_showtitle'); ?>"><?php _e( 'Display Post Title', 'startbox' ) ?></label>
			</p>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showmeta'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showmeta') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showmeta') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('post_showmeta'); ?>"><?php _e( 'Display Meta Information', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('post_info') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['post_showmeta']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('post_meta'); ?>"><?php _e( 'Display Meta Information: ', 'startbox' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_meta') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_meta') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_meta'] ); ?>" /><br/>
					<span style="font-size:smaller">You can use any <a href="http://docs.wpstartbox.com/shortcodes" target="_blank">shortcodes</a> you like here, including: [author], [categories], [comments], [date], [time], [tags] and [edit].</span>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showimage'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showimage') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showimage') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('post_showimage'); ?>"> <?php _e( 'Display Featured Image', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('post_image') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['post_showimage']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('post_imagewidth'); ?>"><?php _e( 'Image Size: ', 'startbox' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id('post_imagewidth') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_imagewidth') ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['post_imagewidth'] ); ?>" />
					x
					<input id="<?php echo esc_attr( $this->get_field_id('post_imageheight') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_imageheight') ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['post_imageheight'] ); ?>" />px
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_imagealignment'); ?>"><?php _e( 'Image Alignment:', 'startbox' ); ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('post_imagealignment') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_imagealignment') ); ?>">
						<option value="none" <?php if($instance['post_imagealignment'] == 'none'){ echo 'selected="selected"'; }?>><?php _e( 'None', 'startbox' ); ?></option>
						<option value="left" <?php if($instance['post_imagealignment'] == 'left'){ echo 'selected="selected"'; }?>><?php _e( 'Left', 'startbox' ); ?></option>
						<option value="right" <?php if($instance['post_imagealignment'] == 'right'){ echo 'selected="selected"'; }?>><?php _e( 'Right', 'startbox' ); ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_imagelocation'); ?>"><?php _e( 'Image Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('post_imagelocation') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_imagelocation') ); ?>">
						<option value="before" <?php if($instance['post_imagelocation'] == 'before'){ echo 'selected="selected"'; }?>><?php _e( 'Before Post Info', 'startbox' ) ?></option>
						<option value="after" <?php if($instance['post_imagelocation'] == 'after'){ echo 'selected="selected"'; }?>><?php _e( 'After Post Info', 'startbox' ) ?></option>
					</select>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showcontent'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showcontent') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showcontent') ); ?>" />
				<label for="<?php echo $this->get_field_id('post_showcontent'); ?>"><?php _e( 'Display Post Content', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('post_contents') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['post_showcontent']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('post_content'); ?>"><?php _e( 'Content:', 'startbox' ); ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('post_content') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_content') ); ?>">
						<option value="excerpt" <?php if($instance['post_content'] == 'excerpt'){ echo 'selected="selected"'; }?>><?php _e( 'Post Excerpt', 'startbox' ); ?></option>
						<option value="full" <?php if($instance['post_content'] == 'full'){ echo 'selected="selected"'; }?>><?php _e( 'Full Content', 'startbox' ); ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_limit'); ?>"><?php echo sprintf( __( 'Limit content to %s characters', 'startbox' ), '<input id="' . $this->get_field_id('post_limit') . '" name="' . $this->get_field_name('post_limit') . '" type="text" size="3" value="' . $instance['post_limit'] . '" />' ); ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'Leave blank for no limit.', 'startbox' ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_readmore'); ?>"><?php _e( 'Read More Text: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_readmore') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_readmore') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_readmore'] ); ?>" />
					<span style="font-size:smaller"><?php _e( 'Leave blank to hide the Read More link.', 'startbox' ); ?></span>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showmore'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showmore') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showmore') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('post_showmore'); ?>"><?php _e( 'Display Link to More Posts', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('post_more') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['post_showmore']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('post_morelink'); ?>"><?php _e( 'Link URL: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_morelink') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_morelink') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_morelink'] ); ?>" /><br/>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_moretext'); ?>"><?php _e( 'Link Text: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_moretext') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_moretext') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_moretext'] ); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_morelocation'); ?>"><?php _e( 'Link Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('post_morelocation') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_morelocation') ); ?>">
						<option value="before" <?php if($instance['post_morelocation'] == 'before'){ echo 'selected="selected"'; }?>><?php _e( 'Before Posts', 'startbox' ) ?></option>
						<option value="after" <?php if($instance['post_morelocation'] == 'after'){ echo 'selected="selected"'; }?>><?php _e( 'After Posts', 'startbox' ) ?></option>
						<option value="after" <?php if($instance['post_morelocation'] == 'before_after'){ echo 'selected="selected"'; }?>><?php _e( 'Before &amp; After Posts', 'startbox' ) ?></option>
					</select>
				</p>
				<hr/>
			</div>

			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['post_showadvanced'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('post_showadvanced') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_showadvanced') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('post_showadvanced'); ?>"><?php _e('Display Advanced Query Options', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('post_advanced') ); ?>" class="advanced-settings" <?php $this->advanced_hide($instance['post_showadvanced']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('post_morecount'); ?>"><?php _e( 'Display ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id('post_morecount') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_morecount') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_morecount'] ); ?>" size="3" />
					<label for="<?php echo $this->get_field_id('post_morecount'); ?>"><?php _e( ' additional posts', 'startbox' ) ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'These posts will display as title-only.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_exclude'); ?>"><?php _e( 'Exclude these posts: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_exclude') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_exclude') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_exclude'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Comma-separated list of post ID\'s.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_include'); ?>"><?php _e( 'Include only these posts: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('post_include') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_include') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_include'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Comma-separated list of post ID\'s.', 'startbox' ) ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('post_offset'); ?>"><?php _e( 'Offset posts: ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id('post_offset') ); ?>" name="<?php echo esc_attr( $this->get_field_name('post_offset') ); ?>" type="text" value="<?php echo esc_attr( $instance['post_offset'] ); ?>" /><br/>
					<span style="font-size:smaller"><?php _e( 'Number of posts to skip before displaying.', 'startbox' ) ?></span>
				</p>

				<hr/>
			</div>
		</div>

		<div id="<?php echo esc_attr( $this->get_field_id('page_options') ); ?>" class="content-settings page-settings"<?php if($instance['content_type'] == 'posts'){ echo ' style="display:none;"'; }?>>
			<p>
				<label for="<?php echo $this->get_field_id('page'); ?>"><?php _e( 'Page: ', 'startbox' ) ?></label>
				<?php wp_dropdown_pages( $this->get_field_id('page') . '&name=' . $this->get_field_name('page') . '&selected=' . $instance['page'] ); ?>
				<span style="font-size:smaller"><?php _e( 'Leave widget title blank to use Page Title instead', 'startbox' ) ?></span>
			</p>
			<hr/>
			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['page_showimage'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('page_showimage') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_showimage') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('page_showimage'); ?>"> <?php _e('Display Featured Image', 'startbox' ) ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('page_image') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['page_showimage']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('page_imagewidth'); ?>"><?php _e( 'Image Size: ', 'startbox' ) ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id('page_imagewidth') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_imagewidth') ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['page_imagewidth'] ); ?>" />
					x
					<input id="<?php echo esc_attr( $this->get_field_id('page_imageheight') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_imageheight') ); ?>" type="text" size="3" value="<?php echo esc_attr( $instance['page_imageheight'] ); ?>" />px
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('page_imagealignment'); ?>"><?php _e( 'Image Alignment:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('page_imagealignment') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_imagealignment') ); ?>">
						<option value="none" <?php if($instance['page_imagealignment'] == 'none'){ echo 'selected="selected"'; }?>><?php _e( 'None', 'startbox' ) ?></option>
						<option value="left" <?php if($instance['page_imagealignment'] == 'left'){ echo 'selected="selected"'; }?>><?php _e( 'Left', 'startbox' ) ?></option>
						<option value="right" <?php if($instance['page_imagealignment'] == 'right'){ echo 'selected="selected"'; }?>><?php _e( 'Right', 'startbox' ) ?></option>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('page_imagelocation'); ?>"><?php _e( 'Image Position:', 'startbox' ) ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id('page_imagelocation') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_imagelocation') ); ?>">
						<option value="before" <?php if($instance['page_imagelocation'] == 'before'){ echo 'selected="selected"'; }?>><?php _e( 'Before Title', 'startbox' ) ?></option>
						<option value="after" <?php if($instance['page_imagelocation'] == 'after'){ echo 'selected="selected"'; }?>><?php _e( 'After Title', 'startbox' ) ?></option>
					</select>
				</p>
			<hr/>
			</div>
			<p>
				<input class="checkbox advanced-toggle" type="checkbox" <?php checked( $instance['page_showcontent'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id('page_showcontent') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_showcontent') ); ?>'); ?>" />
				<label for="<?php echo $this->get_field_id('page_showcontent'); ?>"><?php _e( 'Display Page Content', 'startbox' ); ?></label>
			</p>
			<div id="<?php echo esc_attr( $this->get_field_id('page_content') ); ?>" class="advanced-settings"<?php $this->advanced_hide($instance['page_showcontent']); ?>>
				<p>
					<label for="<?php echo $this->get_field_id('page_limit'); ?>"><?php echo sprintf( __( 'Limit content to %s characters', 'startbox' ), '<input id="' . $this->get_field_id('page_limit') . '" name="' . $this->get_field_name('page_limit') . '" type="text" size="3" value="' . $instance['page_limit'] . '" />' ); ?></label><br/>
					<span style="font-size:smaller"><?php _e( 'Leave blank for no limit.', 'startbox' ); ?></span>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('page_readmore'); ?>"><?php _e( 'Read More Text: ', 'startbox' ) ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('page_readmore') ); ?>" name="<?php echo esc_attr( $this->get_field_name('page_readmore') ); ?>" type="text" value="<?php echo esc_attr( $instance['page_readmore'] ); ?>" />
					<span style="font-size:smaller"><?php _e( 'Leave blank to hide the Read More link.', 'startbox' ); ?></span>
				</p>
			</div>
			<hr/>
		</div>

	<?php
	}
}