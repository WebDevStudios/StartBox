<?php 
/**
 * StartBox Custom Functions
 *
 * Lots of misc functions. These should probably be reviewed and moved to more specific files.
 *
 * @package StartBox
 * @subpackage Functions
 */


/** 
 * Display Relative Timestamps
 *
 * This plugin is based on code from Dunstan Orchard's Blog. Pluginiffied by Michael Heilemann:
 * @link http://www.1976design.com/blog/archive/2004/07/23/redesign-time-presentation/
 *
 * Usage:
 * For posts: echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . ' ago';
 * For comments: echo time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time()) . ' ago';
 *
 * @since StartBox 2.4.6
 * @param integer $older_date The original date in question
 * @param integer $newer_date Specify a known date to determine elapsed time. Will use current time if false Default: false 
 * @return string Time since 
*/

function sb_time_since($older_date, $newer_date = false) {

	// array of time period chunks
	$chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'minute'),
	);
	
	// Newer Date (false to use current time)
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	return $output;
}

	
/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @since StartBox 2.4.7
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function sb_dropdown_posts($args = '') {

	$defaults = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'order_by' => 'post_date',
		'order' => 'DESC',
		'limit' => 30,
		'selected' => 0,
		'echo' => 1,
		'name' => '',
		'id' => '',
		'class' => 'postlist',
		'show_option_none' => true,
		'option_none_value' => 'Select a Post'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Query the Posts
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$limit = ( $limit ) ? ' LIMIT '.absint( $limit ) : '';
	$id = esc_attr($id);
	$name = esc_attr($name);
	$output = '';
	$order_by = sanitize_sql_orderby( $order_by . ' ' . $order );
	
	$post_list = (array)$wpdb->get_results(
		$wpdb->prepare("
		SELECT ID, post_title, post_date
		FROM $wpdb->posts
		WHERE post_type = %s
		AND post_status = %s
		ORDER BY {$order_by}
		{$limit}
	", $post_type, $post_status ) );
	
	$output .= "\t" . "\t" . '<select style="width:100%;" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '">'."\n";
	if ( !empty($post_list) ) {
		if ( $show_option_none ) $output .= "\t" . "\t" . "\t" . '<option value="">' . $option_none_value . '</option>';
		foreach ($post_list as $posts) {
			if ($selected == $posts->ID) { $select = 'selected="selected"'; } else { $select = ''; }
			$output .= "\t" . "\t" . "\t" . '<option value="' . $posts->ID . '"' . $select . '>' . $posts->post_title . '</option>';
		}
	} else {
		$output .= "\t" . "\t" . "\t" . '<option value="">Nothing to Display</option>';
	}
	$output .= '</select>';

	$output = apply_filters('wp_dropdown_posts', $output);

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Create a nice multi-tag title
 *
 * Credits: Ian Stewart and Martin Kopischke for providing this code
 *
 * @since StartBox 2.4.7
 */
function sb_tag_query() {
	$nice_tag_query = get_query_var('tag'); // tags in current query
	$nice_tag_query = str_replace(' ', '+', $nice_tag_query); // get_query_var returns ' ' for AND, replace by +
	$tag_slugs = preg_split('%[,+]%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of tag slugs
	$tag_ops = preg_split('%[^,+]*%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of operators

	$tag_ops_counter = 0;
	$nice_tag_query = '';

	foreach ($tag_slugs as $tag_slug) { 
		$tag = get_term_by('slug', $tag_slug ,'post_tag');
		// prettify tag operator, if any
		if ( isset( $tag_ops[$tag_ops_counter] ) &&  $tag_ops[$tag_ops_counter] == ',') {
			$tag_ops[$tag_ops_counter] = ', ';
		} elseif ( isset( $tag_ops[$tag_ops_counter] ) && $tag_ops[$tag_ops_counter] == '+') {
			$tag_ops[$tag_ops_counter] = ' + ';
		} else {
			$tag_ops[$tag_ops_counter] = '';
		}
		// concatenate display name and prettified operators
		$nice_tag_query = $nice_tag_query . $tag->name . $tag_ops[$tag_ops_counter];
		$tag_ops_counter += 1;
	}
	 return $nice_tag_query;
}

/**
 * Function for producing a sitemap.
 *
 * @since StartBox 2.4.9
 *
 * @uses apply_filters() to pass new 'sb_sitemap_defaults' 
 * @uses wp_list_pages()
 * @uses wp_list_categories()
 *
 * @param array $args array of all configurable options
 *
 */
function sb_sitemap( $args = '' ) {
	global $wp_query, $post;
	$cached_query = $wp_query;
	$cached_post = $post;
	$output = '';
	
	$defaults = array(
		'show_pages'		=> true,
		'show_categories'	=> true,
		'show_posts'		=> true,
		'show_cpts'			=> true,
		'exclude_pages'		=> '',
		'exclude_categories' => '',
		'exclude_post_types' => apply_filters( 'sb_sitemap_exclude_post_types', array('attachment', 'revision', 'nav_menu_item', 'slideshow', 'page', 'post') ),
		'class'				=> 'sitemap',
		'container_class'	=> 'sitemap-container',
		'header_container'	=> 'h3',
		'subheader_container' => 'h4',
		'echo'				=> true
	);
	$r = wp_parse_args( $args, apply_filters( 'sb_sitemap_defaults', $defaults ) );
	extract( $r, EXTR_SKIP );
	
	if ( $show_pages ) {
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-page">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Pages', 'startbox' ) . '</' . $header_container . '>' . "\n";
		$output .= "\t" . '<ul id="pagelist" class="' . $class . '">' . "\n";
		$output .= "\t\t" . wp_list_pages('title_li=&exclude=' . $exclude_pages . '&depth=0&echo=0') . "\n";
		$output .= "\t" . '</ul>' . "\n";
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-page -->' . "\n";
	} if ($show_cpts) {
		$post_types = get_post_types( array('public'=>true),'objects'); 
		foreach ( $post_types as $cpt ) {
			if ( !in_array( $cpt->name, $exclude_post_types ) ) {
        		$posts = new WP_query( array(
					'posts_per_page' => 500,
					'post_type'	=> $cpt->name
					) );
				if ( $posts->have_posts() ) {
					$output .= '<div class="' . $container_class . ' ' . $container_class . '-cpt ' . $container_class . '-' . $cpt->name . '">' . "\n";
					$output .= "\t" . '<' . $header_container . '>' . $cpt->label . '</' . $header_container . '>' . "\n";
					$output .= "\t" . '<ul id="cptlist" class="' . $class . '">' . "\n";
					while ( $posts->have_posts()) : $posts->the_post();
						$output .= "\t\t" . '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . get_comments_number() . ')</li>' . "\n";
					endwhile;
					$output .= "\t" . '</ul>' . "\n";
					$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-cpt ' . $container_class . '-' . $cpt->name . ' -->' . "\n";
				}
			}
		}
	} if ( $show_categories ) {
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-category">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Categories', 'startbox' ) . '</' . $header_container . '>' . "\n";
		$output .= "\t" . '<ul id="catlist" class="' . $class . '">' . "\n";
		$output .= "\t\t" . wp_list_categories('title_li=&exclude=' . $exclude_categories . '&depth=0&echo=0') . "\n";
		$output .= "\t" . '</ul>' . "\n";
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-category -->' . "\n";
	} if ( $show_posts ) {
		
        $categories = get_categories( 'exclude=' . $exclude_categories );
		
		$output .= '<div class="' . $container_class . ' ' . $container_class . '-post">' . "\n";
		$output .= "\t" . '<' . $header_container . '>' . __( 'Posts by Category', 'startbox' ) . '</' . $header_container . '>' . "\n";
		foreach ( $categories as $cat ) {
        	query_posts( 'cat=' . $cat->cat_ID );
			if ( have_posts() ) {
	            $output .= "\t" . '<' . $subheader_container . '>' . $cat->cat_name . '</' . $subheader_container . '>' . "\n";
	            $output .= "\t" . '<ul id="postlist" class="' . $class . '">' . "\n";
				while (have_posts()) : the_post();
					$output .= "\t\t" . '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . get_comments_number() . ')</li>' . "\n";
				endwhile;
				$output .= "\t" . '</ul>' . "\n";
			}
		}
		$output .= '</div><!-- ' . $container_class . ' ' . $container_class . '-post -->' . "\n";
	}
	
	$wp_query = $cached_query;
	$post = $cached_post;
	
	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Function for retrieving taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'get_taxonomy_term_type' ) ) {
	function get_taxonomy_term_type($taxonomy,$term_id) {
		return get_option("_term_type_{$taxonomy}_{$term->term_id}");
	}
}

/**
 * Function for updating taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 * @param mixed $value the new value
 *
 */
if ( !function_exists( 'update_taxonomy_term_type' ) ) {
	function update_taxonomy_term_type($taxonomy,$term_id,$value) {
		update_option("_term_type_{$taxonomy}_{$term_id}",$value);
	}
}

/**
 * Function for deleting taxonomy meta information
 *
 * @since StartBox 2.5
 *
 * @uses get_option()
 * @param string $taxonomy the desired taxonomy name
 * @param string $term_id the desired meta information name
 *
 */
if ( !function_exists( 'delete_taxonomy_term_type' ) ) {
	function delete_taxonomy_term_type($taxonomy,$term_id ) {
		delete_option("_term_type_{$taxonomy}_{$term_id}");
	}
}

?>