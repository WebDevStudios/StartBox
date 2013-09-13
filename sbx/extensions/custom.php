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
 * Adds an option to the options db. Props @ptahdunbar
 *
 * @since 2.4.4
 * @param string $name Option Name. Must be unique.
 * @param mixed $value Option Value.
 * @return bool True on success, false if the option already exists.
 */
function sb_add_option( $name, $value ) {
	$options = get_option( THEME_OPTIONS );
	if ( $options and !isset($options[$name]) ) {
		$options[$name] = $value;
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Updates an option to the options db. Props @ptahdunbar
 *
 * @since 2.4.4
 * @param string $name Option Name. Must be unique.
 * @param mixed $value Option Value.
 * @return bool true|false
 */
function sb_update_option( $name, $value ) {
	$options = get_option( THEME_OPTIONS );
	if ( !isset($options[$name]) || $value != $options[$name] ) {
		$options[$name] = $value;
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

/**
 * Returns the value of an option from the db if it exists. Props @ptahdunbar
 *
 * @since 2.4.4
 * @param string $name Option Name.
 * @return mixed Returns the option's value if it exists, false if it doesn't.
 */
function sb_get_option( $name ) {
	$options = get_option( THEME_OPTIONS );
	if ( is_array($options) && isset($options[$name]) ) {
		return $options[$name];
	} else {
		return false;
	}
}

/**
 * Deletes an option from the options db. Props @ptahdunbar
 *
 * @since 2.4.4
 * @param string $name Option Name. Must be unique.
 * @return bool true|false
 */
function sb_delete_option( $name ) {
	$options = get_option( THEME_OPTIONS );
	if ( $options[$name] ) {
		unset( $options[$name] );
		return update_option( THEME_OPTIONS, $options );
	} else {
		return false;
	}
}

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
 * @since 2.4.6
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
 * @since 2.4.7
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
 * @since 2.4.7
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
 * Function for retrieving taxonomy meta information
 *
 * @since 2.5
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
 * @since 2.5
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
 * @since 2.5
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

/**
 * Forever eliminate "Startbox" from the planet (or at least the little bit we can influence).
 *
 * Violating our coding standards for a good function name.
 *
 * @since 2.7.0
 */
function capital_B_dangit( $text ) {

	// Simple replacement for titles
	if ( 'the_title' === current_filter() )
		return str_replace( 'Startbox', 'StartBox', $text );

	// Still here? Use the more judicious replacement
	static $dblq = false;
	if ( false === $dblq )
		$dblq = _x( '&#8220;', 'opening curly quote' );
	return str_replace(
		array( ' Startbox', '&#8216;Startbox', $dblq . 'Startbox', '>Startbox', '(Startbox' ),
		array( ' StartBox', '&#8216;StartBox', $dblq . 'StartBox', '>StartBox', '(StartBox' ),
	$text );

}
add_filter( 'the_content', 'capital_B_dangit', 11 );
add_filter( 'the_title', 'capital_B_dangit', 11 );
add_filter( 'comment_text', 'capital_B_dangit', 31 );

/**
 * Introduces a new column to the 'Page' dashboard that will be used to render the page template
 * for the given page.
 *
 * Credit: @tommcfarlin, http://tommcfarlin.com/view-page-templates/
 *
 * @since	2.7
 * @param	array	$page_columns	The array of columns rendering page meta data./
 * @return	array					The update array of page columns.
 */
function sb_add_template_column( $page_columns ) {
	$page_columns['template'] = __( 'Page Template', 'startbox' );
	return $page_columns;
}
add_filter( 'manage_edit-page_columns', 'sb_add_template_column' );

/**
 * Renders the name of the template applied to the current page. Will use 'Default' if no
 * template is used, but will use the friendly name of the template if one is applied.
 *
 * Credit: @tommcfarlin, http://tommcfarlin.com/view-page-templates/
 *
 * @since	2.7
 * @param	string	$column_name	The name of the column being rendered
 */
function sb_add_template_data( $column_name ) {

	// Grab a reference to the post that's currently being rendered
	global $post;

	// If we're looking at our custom column, then let's get ready to render some information.
	if( 'template' == $column_name ) {

		// First, the get name of the template
		$template_name = get_post_meta( $post->ID, '_wp_page_template', true );

		// If the file name is empty or the template file doesn't exist (because, say, meta data is left from a previous theme)...
		if( 0 == strlen( trim( $template_name ) ) || ! file_exists( get_template_directory() . '/' . $template_name ) ) {

			// ...then we'll set it as default
			$template_name = __( 'Default', 'startbox' );

		// Otherwise, let's actually get the friendly name of the file rather than the name of the file itself
		// by using the WordPress `get_file_description` function
		} else {

			$template_name = get_file_description( get_template_directory() . '/' . $template_name );

		}

	}

	// Finally, render the template name
	echo $template_name;

}
add_action( 'manage_page_posts_custom_column', 'sb_add_template_data' );
