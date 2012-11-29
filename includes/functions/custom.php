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
 * StartBox Handle Upload AJAX
 *
 * Necessary functions for handling the media uploads gracefully.
 * Currently only supports images.
 *
 * @uses sb_handle_upload
 * @since 2.4.4
 */
function sb_handle_upload_ajax()
{
	// check_ajax_referer('sb'); // security
	$thumb = $full = array();
	$error = '';
	if( !isset($_REQUEST['file_id']) )
		$error = htmlentities( sb_error(7, array(), false) ); // no $file_id found, error out (with no html formatting)
	if($error == '')
	{
		$id = sb_handle_upload($_REQUEST['file_id']);
		if(is_numeric($id))
		{
			$thumb = wp_get_attachment_image_src($id, 'thumbnail');
			$full = wp_get_attachment_image_src($id, 'full');
			if($thumb[0] == '' || $full[0] == '')
				$error = 'Error: Could not retrieve uploaded image.';
		}
		else
		{
			$error = $id;
		}
	}
	die(json_encode( array('thumb' => $thumb[0], 'full' => $full[0], 'error' => $error) ));
}
add_action('wp_ajax_sb_action_handle_upload_ajax', 'sb_handle_upload_ajax');

/**
 * StartBox Upload Handler
 *
 * @param integer $file_id the ID of the media to be uploaded
 *
 * @since 2.4.4
 */
function sb_handle_upload($file_id = '')
{
	if(empty($_FILES))
		return 'Error: No file received.';

	return media_handle_upload($file_id, 0, array(), array('test_form' => false)); // returns attachment id
}

// Custom Media Tab: Suggested Files -- Credit: Joel Kuczmarski
function sb_filter_media_upload_tabs($_default_tabs) {
    if( isset( $_GET['suggested'] ) && $_GET['suggested'] != '')
        $_default_tabs['suggested'] = __( 'Suggested', 'startbox' );

    return $_default_tabs;
}
add_filter('media_upload_tabs', 'sb_filter_media_upload_tabs');

function sb_media_upload_suggested() {
    $errors = array();

    if(!empty($_POST))
    {
        $return = media_upload_form_handler();

        if(is_string($return))
            return $return;
        if(is_array($return))
            $errors = $return;
    }

    return wp_iframe( 'sb_media_upload_suggested_form', $errors );
}
add_action('media_upload_suggested', 'sb_media_upload_suggested');

function sb_media_upload_suggested_form($errors) {
    global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types, $images;

    media_upload_header();

    $images = explode(', ', $_GET['suggested']);

?>
    <script type="text/javascript">
    function doSend(url) {
        var win = window.dialogArguments || opener || parent || top;
        window.parent.send_to_editor(url);
        return false;
    }
    </script>

    <div style="margin:1em;">
        <h3 class="media-title">Use media files suggested by your theme</h3>
        <div id="media-items">
<?php
    $missing = array(); // to store all missing files for later error output
    foreach($images as $index => $image) :
        global $blog_id;
        $replace = explode('/', get_blog_details($blog_id)->path); // necessary for timthumb to play nice with WordPress Multisite
		if( is_subdomain_install() )
			$theme_uri = THEME_URI;
		else
			$theme_uri = str_replace($replace[2] . '/', '', THEME_URI);
        $fullimage = $theme_uri . '/' . $image;

        if(!@getimagesize($fullimage)) // doing this funky-ness b.c. file_exists doesn't want to work for these URLs...
        {
            array_push($missing, $image);
            continue;
        }
?>
            <div id="media-item-<?php echo $index; ?>" class="media-item">
                <div style="float:right; width:30%; text-align:center; padding-top:35px;"><input type="button" value="Insert into Post" class="button" onclick="doSend('<?php echo $fullimage; ?>')"></div>
                <div style="width:70%; height:100px; overflow:hidden;">
                    <img src="<?php echo SCRIPTS_URL; ?>/timthumb.php?src=<?php echo $fullimage; ?>&amp;h=100&amp;zc=1&amp;cropfrom=middleleft&amp;q=100" alt="" height="100" />
                </div>
                <div style="clear:both;"></div>
            </div>
<?php
	endforeach;

    // error output:
    if(count($missing) > 0) : ?>
        <div class="media-item">
            <div style="padding:1em;">
                Warning! The following items are missing from the current theme's directory:
                <ul style="list-style:inside circle; margin-top:1em;">
                <?php foreach($missing as $index => $url)
                        echo '<li style="padding-left:1em;">' . $url . '</li>'; ?>
                </ul>
            </div>
        </div>
<?php endif; ?>
        </div>
    </div>
<?php
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

// Format StartBox
foreach ( array( 'the_content', 'the_title' ) as $filter )
	add_filter( $filter, 'capital_B_dangit', 11 );
add_filter( 'comment_text', 'capital_B_dangit', 31 );
