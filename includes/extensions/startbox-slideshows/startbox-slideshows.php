<?php
/*
Plugin Name: StartBox Slideshows
Plugin URI: http://wpstartbox.com/
Description: This plugin creates a custom post type called Slideshows which integrates with the Media Library, allowing you to create customizable slideshows to suit your needs. To embed a slideshow into a post or page, simply insert [slideshow id=""] anywhere in the post, page or widget content (or use the built-in widget).
Version: 1.0
Author: Joel Jcuzmarski, Brian Richards
Author URI: http://www.joelak.com
Requires at least: 3.3.1
Tested up to: 3.4.2
*/

// register_activation_hook( __FILE__, 'sb_slideshow_activation' );
// function sb_slideshow_activation() {
// 	$sb_version = get_option( 'startbox_version' );
// 	if ( false == $sb_version || version_compare( get_option('startbox_version'), '2.5.6', '<') ) {
// 		if ( isset($_GET['action']) && $_GET['action'] == 'error_scrape' ) {
// 			echo '<strong>The plugin StartBox Slideshows requires StartBox 2.6 or later.</strong>';
// 			exit;
// 		} else { trigger_error('The plugin StartBox Slideshows requires StartBox 2.6 or later.', E_USER_ERROR); }
// 	}
// }

// Make sure these functions don't exist elsewhere and that the current theme supports this functionality
if ( ! function_exists('sb_slideshow_init') && current_theme_supports( 'sb-slideshows' ) ) {

// Define Globals
global $wpdb, $sb_slideshow_slides, $sb_slideshow_used_ids, $sb_slideshow_footer_javascript, $sb_slideshow_interface;

$sb_slideshow_slides = $sb_slideshow_used_ids = array();
$sb_slideshow_footer_javascript = '';
$sb_slideshow_interface = apply_filters( 'sb_slideshow_interface', array(
	'transitions' 		=> array( 'sliceDown', 'sliceDownLeft', 'sliceUp', 'sliceUpLeft', 'sliceUpDown', 'sliceUpDownLeft', 'fold', 'fade' ),
	'controls' 		=> array(
						'arrows' 		=> __( 'Show Overlay Arrows', 'startbox' ),
						'navigation' 	=> __( 'Show Bottom Navigation', 'startbox' ) ),
	'ssorris' 		=> array(
						'slideshow_on' 		=> __('Slideshow', 'startbox'),
						'slideshow_off' 	=> __('Random Image', 'startbox')  ),
	'sizes' 			=> array(
						'max' 	=> __( 'Largest Image', 'startbox' ),
						'min' 	=> __( 'Smallest Image', 'startbox' ),
						'custom' 	=> __( 'Custom', 'startbox' ) ),
	'link_text' 		=> 'http://',
	'pause' 			=> 3000,
	'opacity'			=> 0.8,
	'color' 			=> '#FFFFFF',
	'slide_width' 		=> 300,
	'slide_height' 	=> 188,
	'filter_width'		=> 60,
	'filter_height' 	=> 60,
	'mime_types' 		=> array( 'image/jpeg', 'image/png', 'image/gif' ) ) );

$sb_slideshow_interface['mysql_select'] = "SELECT ID, post_date, post_content, post_excerpt, post_mime_type FROM $wpdb->posts WHERE
	post_type = 'attachment' AND post_status != 'trash' AND
	post_mime_type = '" . implode( "' OR post_mime_type='", $sb_slideshow_interface['mime_types'] ) . "'";
/*
 * Use add_filter('sb_slideshow_interface', 'your_filter_function'); to filter interface options for a custom player
 * Filters used for implimenting a custom player located in sb_slideshow_shortcode():
 *	add_filter('sb_slideshow_footer_javascript', 'your_filter_function', 10, 5); // your filter function should expect 5 parameters
 *	add_filter('sb_slideshow_result', 'your_filter_function', 10, 5); // your filter function should expect 5 parameters
 * Actions used for replacing nivo scripts/styles:
 *	add_action('sb_slideshow_enqueue', 'your_custom_enqueues_function'); // is executed at template_redirect, replaces default enqueues
 *	add_action('sb_slideshow_wp_head', 'your_custom_script_and_style'); // a controlled way of adding to the head for the custom player
 */


// Utility: Generate Shortcode Input
function sb_slideshow_embed_input( $post_id ) {
	return '<input class="text urlfield" readonly="readonly" value="[slideshow id=&quot;' . $post_id . '&quot;]" type="text">';
}

// Utility: Sort Slides
function sb_slideshow_slide_sort( $a, $b ) {
	if ($a['order'] == $b['order']) return 0;
	return ($a['order'] < $b['order'] ? -1 : 1);
}

// Utility: Verify Slideshow ID
function sb_slideshow_verify_id( $id ) {
	global $sb_slideshow_used_ids;

	while (in_array( $id, $sb_slideshow_used_ids )) $id++;

	array_push( $sb_slideshow_used_ids, $id );

	return $id;
}

// Utility: Generate A Checkbox
function sb_slideshow_checkbox( $value, $key, $name, $checked = array() ) {
	return '<input type="checkbox" name="sb_' . esc_attr( $name ) . '[]" id="' . esc_attr( $name . '_' . $value ) . '"
		value="' . esc_attr( $value ) . '"' . (in_array( $value, (array)$checked ) ? 'checked="checked"' : '') . ' />
		<label for="' . esc_attr( $name . '_' . $value ) . '">' . $key . '</label><br />';
}

// Utility: Generate A Radio Select
function sb_slideshow_radio( $value, $key, $name, $checked = '' ) {
	return '<input type="radio" name="sb_' . esc_attr( $name ) . '" id="' . esc_attr( $name . '_' . $value ) . '"
		value="' . esc_attr( $value ) . '"' . ($value == $checked ? 'checked="checked"' : '') . ' />
		<label for="' . esc_attr( $name . '_' . $value ) . '">' . $key . '</label><br />';
}

// Plugin Initialization
function sb_slideshow_init() {
	// Add custom post type
	register_post_type( 'slideshow', array(
		'labels' 				=> array(
								'name' 				=> _x('Slideshows', 'post type general name'),
								'singular_name' 		=> _x('Slideshow', 'post type singular name'),
								'add_new' 			=> _x('Add New', 'startbox' ),
								'add_new_item' 		=> __( 'Add New Slideshow', 'startbox' ),
								'edit_item' 			=> __( 'Edit Slideshow', 'startbox' ),
								'new_item' 			=> __( 'New Slideshow', 'startbox' ),
								'view_item' 			=> __( 'View Slideshow', 'startbox' ),
								'search_items' 		=> __( 'Search Slideshows', 'startbox' ),
								'not_found' 			=> __( 'No slideshows found', 'startbox' ),
								'not_found_in_trash' 	=> __( 'No slideshows found in Trash', 'startbox' ),
								'parent_item_colon' 	=> '' ),
		'label' 				=> __( 'Slideshows', 'startbox' ),
		'singular_label' 		=> __( 'Slideshow', 'startbox' ),
		'public' 				=> true,
		'exclude_from_search' 	=> true,
		'show_ui' 			=> true,
		'capability_type' 		=> 'post',
		'hierarchical' 		=> false,
		'rewrite' 			=> array(
								'slug' 		=> 'slideshows',
								'with_front' 	=> false ),
		'query_var' 			=> true,
		'supports' 			=> array( 'title' ),
		'menu_position' 		=> 5,
		'show_in_nav_menus' 	=> false,
		'register_meta_box_cb' 	=> 'sb_slideshow_meta_box_callback' ));
}
add_action( 'init', 'sb_slideshow_init' );

// Callback From register_post_type In sb_slideshow_init
function sb_slideshow_meta_box_callback() {
	global $wpdb, $sb_slideshow_interface, $sb_slideshow_slides;

	sb_slideshow_slides( $sb_slideshow_interface['mysql_select'] ); // push slides

	// Add custom meta boxes to custom post type
	add_meta_box( 'sb_slides', __( 'Slides', 'startbox' ), 'sb_slideshow_slides_meta', 'slideshow', 'normal', 'low' );
	add_meta_box( 'sb_library', __( 'Media Library', 'startbox' ), 'sb_slideshow_library_meta', 'slideshow', 'normal', 'low' );
	add_meta_box( 'sb_shortcode', __( 'Shortcode', 'startbox' ), 'sb_slideshow_shortcode_meta', 'slideshow', 'side', 'low' );
	add_meta_box( 'sb_slide_options', __( 'Options', 'startbox' ), 'sb_slideshow_options_meta', 'slideshow', 'side', 'low' );
}

// Output "Slides" Meta Box
function sb_slideshow_slides_meta() {
	echo '<input type="hidden" name="sb_noncename" id="sb_noncename"
		value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />'; // security nonce

	sb_slideshow_meta_box( 'slides' );
}

// Output "Media Library" Meta Box
function sb_slideshow_library_meta() {
	global $sb_slideshow_interface, $sb_slideshow_slides;

	$mime_types = $years = $months = array();
	foreach( $sb_slideshow_slides as $slide ) {
		if (!in_array( $type = $slide['attachment']['type'], $mime_types )) array_push( $mime_types, $type );
		if (!in_array( $year = $slide['attachment']['year'], $years )) array_push( $years, $year );
		if (!in_array( $month = $slide['attachment']['month'], $months )) array_push( $months, $month );
		if (!isset( $month_years[$month] )) $month_years[$month] = array();
		if (!in_array( $year = 'y' . $year, $month_years[$month] )) array_push( $month_years[$month], $year );
	}

	sort( $years, SORT_NUMERIC );
	sort( $months, SORT_NUMERIC );

	sb_slideshow_meta_box( 'library' );
?>
	<div id="libraryFilters">
		<div> <em><?php _e( 'Library Filters', 'startbox' ); ?></em> </div>
		<div class="filterGroup"> <a href="#" id="clear-filters"><?php _e( 'Clear', 'startbox' ); ?></a> </div>
		<div class="filterGroup">
			<label for="filter_year"> <?php _e( 'Year', 'startbox' ); ?>: </label>
			<select id="filter_year">
				<option value="all">&infin;&nbsp;</option>
				<?php foreach ($years as $year) : ?>
				<option value="<?php echo esc_attr( $year ); ?>"><?php echo $year; ?></option>
				<?php endforeach; ?>
			</select>
			<label for="filter_month">&nbsp;<?php _e( 'Month', 'startbox' ); ?>: </label>
			<select id="filter_month">
				<option value="all">&infin;&nbsp;</option>
				<?php foreach ($months as $month) : ?>
				<option value="<?php echo esc_attr( $month ); ?>" class="<?php echo implode( ' ', $month_years[$month] ); ?>"><?php echo $month; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="filterGroup">
			<label for="filter_type"> <?php _e( 'Media Type', 'startbox' ); ?>: </label>
			<select id="filter_type">
				<option value="all">&infin;&nbsp;</option>
				<?php foreach ($mime_types as $type) : ?>
				<option value="<?php echo esc_attr( $type ); ?>"><?php echo $type; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="filterGroup">
			<label for="filter_width_ltgt"> <?php _e( 'Width', 'startbox' ); ?>: </label>
			<select id="filter_width_ltgt">
				<option value="gt"><?php _e( 'greater than', 'startbox' ); ?>&nbsp;</option>
				<option value="lt"><?php _e( 'less than', 'startbox' ); ?></option>
			</select>
			<input type="text" id="filter_width" class="pixelfield"
				value="<?php echo esc_attr( $sb_slideshow_interface['filter_width'] ); ?>" /><code>px</code>
			<label for="filter_height_ltgt">&nbsp;<?php _e( 'Height', 'startbox' ); ?>: </label>
			<select id="filter_height_ltgt">
				<option value="gt"><?php _e( 'greater than', 'startbox' ); ?>&nbsp;</option>
				<option value="lt"><?php _e( 'less than', 'startbox' ); ?></option>
			</select>
			<input type="text" id="filter_height" class="pixelfield"
				value="<?php echo esc_attr( $sb_slideshow_interface['filter_height'] ); ?>" /><code>px</code>
		</div>
	</div>
	<div id="uploaddiv"> <a title="Upload a file" id="uploadlink" class="button" href="#"><?php _e( 'Add New', 'startbox' ); ?></a> <span id="uploadresult"></span> </div>
<?php
}

// Output "Shortcode" Meta Box
function sb_slideshow_shortcode_meta() {
	global $post;
	echo sb_slideshow_embed_input( $post->ID );
}

// Output "Options" Meta Box
function sb_slideshow_options_meta() {
	global $post, $sb_slideshow_interface;

	$pause = get_post_meta( $post->ID, 'pause', true );
	$opacity = get_post_meta( $post->ID, 'opacity', true );
	$effect = get_post_meta( $post->ID, 'effect', true );
	$control = get_post_meta( $post->ID, 'control', true );
	$size = get_post_meta( $post->ID, 'size', true );
	$size_custom = get_post_meta( $post->ID, 'size_custom', true );
	$ssorri = get_post_meta($post->ID, 'ssorri', true);
?>

    <p><strong><?php _e('Shortcode Output', 'startbox'); ?></strong></p>
    <p><?php _e('Should slides be used for a slideshow or single, random image?', 'startbox'); ?></p>
    <p id="slideshow_radio_buttons">
    <?php //foreach($slideshow_or_random_image as $key => $value) { echo sb_slideshow_radio($key, $value, 'ssorri', ($ssorri == '' ? 'slideshow_on' : $ssorri)); } ?>
    <?php foreach ($sb_slideshow_interface['ssorris'] as $key => $value) echo sb_slideshow_radio( $key, $value, 'ssorri', ($ssorri == '' ? 'slideshow_on' : $ssorri) ); ?>
    </p>

	<p><strong><?php _e( 'Slideshow Size', 'startbox' ); ?></strong></p>
	<p id="size_radio_buttons">
	<?php foreach ($sb_slideshow_interface['sizes'] as $key => $value) echo sb_slideshow_radio( $key, $value, 'size', ($size == '' ? 'min' : $size) ); ?>
	<span id="custom_size" class="hide-if-js">
	<label for="custom_width"><?php _e( 'Width', 'startbox' ); ?></label>
		<input type="text" id="custom_width" class="pixelfield" name="sb_size_custom[width]"
			value="<?php echo esc_attr( $size_custom['width'] ); ?>" /> <code>px</code>
	<label for="custom_height"><?php _e( 'Height', 'startbox' ); ?></label>
		<input type="text" id="custom_height" class="pixelfield" name="sb_size_custom[height]"
			value="<?php echo esc_attr( $size_custom['height'] ); ?>" /> <code>px</code>
	</span>
	</p>
	<div id="slideshow_on_options">
	<p><strong><?php _e( 'Pause Timer', 'startbox' ); ?></strong></p>
	<p><?php _e( 'How long, in miliseconds, to pause on a slide before transitioning.', 'startbox' ); ?></p>
	<p><input type="text" size="4" name="sb_pause"
		value="<?php echo esc_attr( ($pause == '' ? $sb_slideshow_interface['pause'] : $pause) ); ?>" /> <code><?php _e( 'miliseconds', 'startbox' ); ?></code></p>

	<p><strong><?php _e( 'Caption Opacity', 'startbox' ); ?></strong></p>
	<p><?php _e( 'How opaque to make the slide caption.', 'startbox' ); ?></p>
	<p><input type="text" size="4" name="sb_opacity"
		value="<?php echo esc_attr( ($opacity == '' ? $sb_slideshow_interface['opacity'] : $opacity) ); ?>" /> <code><?php _e( '1 = 100%. Default: 0.8.', 'startbox' ); ?></code></p>
<?php
	if( isset( $sb_slideshow_interface['transitions'] ) &&
		is_array( $sb_slideshow_interface['transitions'] ) &&
		count( $sb_slideshow_interface['transitions'] ) > 0 ) :
?>
		<p><strong><?php _e( 'Transition Effects', 'startbox' ); ?></strong></p>
		<p><?php _e( 'If no transition effect is checked, all effects will be used at random.', 'startbox' ); ?></p>
		<p><?php foreach ($sb_slideshow_interface['transitions'] as $option) echo sb_slideshow_checkbox( $option, $option, 'effect', $effect ); ?></p>
<?php
	endif;

	if( isset( $sb_slideshow_interface['controls'] ) &&
		is_array( $sb_slideshow_interface['controls'] ) &&
		count( $sb_slideshow_interface['controls'] ) > 0 ) :
?>
		<p><strong><?php _e( 'Show', 'startbox' ); ?></strong></p>
		<p><?php foreach ($sb_slideshow_interface['controls'] as $key => $value)
			echo sb_slideshow_checkbox( $key, $value, 'control', $control ); ?></p>
        </div>
<?php
	endif;
}

// Output "Slides" OR "Media Library" Meta Boxes
function sb_slideshow_meta_box( $box ) {
	global $sb_slideshow_slides;
?>
	<div class="scrollingContainer">
		<div class="scrollingHotSpotLeft"></div><div class="scrollingHotSpotRight"></div>
		<ul class="connectedSortable">
<?php
		foreach( $sb_slideshow_slides as $index => $slide ) {
			if ($slide['box'] == $box) sb_slideshow_sortable_item( $index );
		}
?>
		</ul>
	</div>
<?php
}

// Push Attachments Onto Global Slide Array And Return Added Indexes
function sb_slideshow_slides( $sql ) {
	global $wpdb, $post, $sb_slideshow_interface, $sb_slideshow_slides;

	$slides = get_post_meta( (!isset( $post->ID ) ? $_POST['id'] : $post->ID), 'slide', false ); // set single (third parameter) to false to pull ALL records with key "slide"

	$attachments = $wpdb->get_results( $sql );

	// push all image attachments info into array
	$indexes = array();
	foreach( $attachments as $attachment ) {
		array_push( $indexes, count( $sb_slideshow_slides ) );

		$box = 'library';
		$order = '';
		foreach( $slides as $slide ) {
			if( $slide['attachment_id'] == $attachment->ID ) {
				$box = 'slides';
				$order = $slide['order'];
				break;
			}
		}

		$metadata = wp_get_attachment_metadata( $attachment->ID );

		array_push( $sb_slideshow_slides, array(
			'box' 			=> $box,
			'order'			=> $order,
			'attachment'		=> array(
				'id' 		=> $attachment->ID,
				'year' 		=> mysql2date( 'Y', $attachment->post_date ),
				'month' 	=> mysql2date( 'm', $attachment->post_date ),
				'width'		=> $metadata['width'],
				'height' 	=> $metadata['height'],
				'type' 		=> $attachment->post_mime_type,
				'link'		=> $attachment->post_excerpt,
				'content'	=> $attachment->post_content ),
				'image' 	=> '<img src="' . sb_get_post_image_url( array( 'width' => $sb_slideshow_interface['slide_width'], 'height' => $sb_slideshow_interface['slide_height'], 'image_id' 	=> $attachment->ID, 'echo' 	=> false ) ) . '" width="' . $sb_slideshow_interface['slide_width'] . '" height="' . $sb_slideshow_interface['slide_height'] . '" />'
			)
		);
	}

	usort( $sb_slideshow_slides, 'sb_slideshow_slide_sort' ); // order the elements of the array

	return $indexes;
}

// Output A Sortable Item To Be Used In "Slides" OR "Media Library"
function sb_slideshow_sortable_item( $index ) {
	global $sb_slideshow_interface, $sb_slideshow_slides;

	$slide = $sb_slideshow_slides[$index];
	$attachment = $slide['attachment'];

	$post = get_post( $attachment['id'] );

	if (isset( $_POST['index_offset'] )) $index += $_POST['index_offset']; // required to properly index ajax uploads
?>
	<li id="attachment_id-<?php echo $post->ID; ?>" class="sb_<?php echo esc_attr( $slide['box'] ); ?> sb_item">
		<div class="sb_right">
			<a href="#" class="move-to-library"><?php _e( 'Remove Slide', 'startbox' ); ?></a>
			<a href="#" class="move-to-slides"><?php _e( 'Add Slide', 'startbox' ); ?></a>
		</div>
		<?php echo $slide['image']; ?>
		<textarea name="slide[<?php echo esc_attr( $index ); ?>][post_content]"><?php echo $post->post_content; ?></textarea>
		<label for="slide-link-<?php echo $index; ?>"><?php _e( 'Link to:', 'startbox' ); ?></label>
		<input type="text" class="slide-link" name="slide[<?php echo esc_attr( $index ); ?>][post_excerpt]" id="slide-link-<?php echo $index; ?>"
			value="<?php echo esc_attr( ($post->post_excerpt != '' ? $post->post_excerpt : $sb_slideshow_interface['link_text']) ); ?>" />
		<input type="hidden" name="slide[<?php echo esc_attr( $index ); ?>][attachment_id]"
			value="<?php echo esc_attr( $post->ID ); ?>" />
		<input type="hidden" name="slide[<?php echo esc_attr( $index ); ?>][box]" class="sb_box"
			value="<?php echo esc_attr( $slide['box'] ); ?>" />
		<input type="hidden" name="slide[<?php echo esc_attr( $index ); ?>][order]" class="sb_order"
			value="<?php echo esc_attr( $slide['order'] ); ?>" />
	</li>
<?php
}

// Save Custom Data
function sb_slideshow_save( $post_id ) {
	global $sb_slideshow_interface;

	if (!isset( $_POST['sb_noncename'] ) || !wp_verify_nonce( $_POST['sb_noncename'], plugin_basename( __FILE__ ) ) // security check
		|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE // avoid auto save routine
		|| 'page' == $_POST['post_type'] && !(current_user_can( 'edit_page', $post_id ) || current_user_can( 'edit_post', $post_id )) // check permissions
		|| !sb_verify_post_type('slideshow')) return $post_id; // verify post type

	delete_post_meta( $post_id, 'slide' ); // flush all post meta entries with "slide" as their key

	// initialize default min and max image sizes
	$image_size = array(
		'min' 	=> array(
					'width' 	=> 1000,
					'height' 	=> 1000 ),
		'max' 	=> array(
					'width' 	=> 100,
					'height' 	=> 100 ) );

	foreach( (array) $_POST['slide'] as $slide ) {
		// update attachment details for any slide
		wp_update_post( array(
			'ID' 			=> $slide['attachment_id'],
			'post_excerpt' 	=> apply_filters( 'sb_pre_link_url',
								($slide['post_excerpt'] != $sb_slideshow_interface['link_text'] ? $slide['post_excerpt'] : '') ),
			'post_content'		=> apply_filters( 'sb_link_description', $slide['post_content'] ) ) );

		if ($slide['box'] == 'library') continue; // stop here if slide is from library

		// create a post meta record with key "slide" to hold this slide's details
		add_post_meta( $post_id, 'slide', array(
			'attachment_id' 	=> $slide['attachment_id'],
			'order' 			=> $slide['order'] ) );

		// from here on is just doing some checks to figure out the max and min image heights and widths
		$image = wp_get_attachment_image_src( $slide['attachment_id'], 'full' );

		// min
		if ($image[1] < $image_size['min']['width']) $image_size['min']['width'] = $image[1];
		if ($image[2] < $image_size['min']['height']) $image_size['min']['height'] = $image[2];

		// max
		if ($image[1] > $image_size['max']['width']) $image_size['max']['width'] = $image[1];
		if ($image[2] > $image_size['max']['height']) $image_size['max']['height'] = $image[2];
	}

	// update post meta records for all of the options
	update_post_meta($post_id, 'ssorri', $_POST['sb_ssorri']);
	update_post_meta( $post_id, 'effect', $_POST['sb_effect'] );
	update_post_meta( $post_id, 'control', $_POST['sb_control'] );
	update_post_meta( $post_id, 'pause', (is_numeric( $_POST['sb_pause'] ) ? $_POST['sb_pause'] : $sb_slideshow_interface['pause']) );
	update_post_meta( $post_id, 'opacity', (is_numeric( $_POST['sb_opacity'] ) ? $_POST['sb_opacity'] : $sb_slideshow_interface['opacity']) );
	update_post_meta( $post_id, 'size', $_POST['sb_size'] );
	$size_custom = $_POST['sb_size_custom'];
	if ($size_custom['width'] == '') $size_custom['width'] = $image_size['min']['width'];
	if ($size_custom['height'] == '') $size_custom['height'] = $image_size['min']['height'];
	update_post_meta( $post_id, 'size_custom', $size_custom );
	update_post_meta( $post_id, 'size_min', $image_size['min'] );
	update_post_meta( $post_id, 'size_max', $image_size['max'] );

	return $post_id;
}
add_action( 'save_post', 'sb_slideshow_save' );

// Handle Shortcode
function sb_slideshow_shortcode( $atts, $content = NULL ) {
	extract( shortcode_atts( array( 'id' => 0 ), $atts ) );

	// get various options stored as post meta records
	$ssorri = get_post_meta($id, 'ssorri', true);
	$effect = get_post_meta( $id, 'effect', true );
	$control = get_post_meta( $id, 'control', true );
	if (!$control) $control = array(); // so the in_array() below doesn't error out
	$pause = get_post_meta( $id, 'pause', true );
	$opacity = get_post_meta( $id, 'opacity', true );
	$size = get_post_meta( $id, 'size', true );

	// get the width and height array that corresponds to the chosen slideshow size
	switch( $size ) {
		case 'max':
			$dimensions = get_post_meta( $id, 'size_max', true );
		break;
		case 'custom':
			$dimensions = get_post_meta( $id, 'size_custom', true );
		break;
		case 'min':
		default:
			$dimensions = get_post_meta( $id, 'size_min', true );
		break;
	}

	if($ssorri == 'slideshow_on'){

	// get and sort all slides stored as post meta records
	$slides = get_post_meta( $id, 'slide', false ); // set single (third parameter) to false to pull ALL records with key "slide"
	usort( $slides, 'sb_slideshow_slide_sort' ); // order the elements of the array

	// add javascript to be output in the footer (safer than outputing in the content area)
	global $sb_slideshow_footer_javascript, $sb_slideshow_interface;
	$id = sb_slideshow_verify_id( $id ); // need to do this to allow for the same slideshow to be embedded multiple times on one page
	$controlNav = in_array( 'navigation', $control );
	$sb_slideshow_footer_javascript .= apply_filters(
		'sb_slideshow_footer_javascript',
		'$("#slider-' . $id . '").nivoSlider({
			effect:"' . ($effect == '' ? 'random' : implode( ',', $effect )) . '",
			pauseTime:' . ($pause == '' ? $sb_slideshow_interface['pause'] : $pause) . ',
			captionOpacity: ' . ($opacity == '' ? $sb_slideshow_interface['opacity'] : $opacity) . ',
			directionNav:' . (in_array( 'arrows', $control ) ? 'true' : 'false') . ',
			controlNav:' . ($controlNav ? 'true' : 'false') . '
		});', $id, $effect, $pause, $control, $opacity );

	// create the code for the slideshow
	$result = '';
	$result .= '<div class="slider-wrapper' . ($controlNav ? ' with-controlNav' : '') . '" style="width:' . absint( $dimensions['width'] ) . 'px;">
		<div class="slider" id="slider-' . $id . '">';
	foreach( $slides as $slide ) {
		$attachment = get_post( $slide['attachment_id'] );
		$description = $attachment->post_content;
		if ($attachment->post_excerpt != '') $result .= '<a href="' . esc_url( $attachment->post_excerpt ) . '">';
		$result .= '<img src="' . sb_get_post_image_url( array( 'width' => $dimensions['width'], 'height' => $dimensions['height'], 'image_id' 	=> $slide['attachment_id'], 'echo' 	=> false ) ) . '" width="' .  $dimensions['width'] . '" height="' .  $dimensions['height'] . '" alt="' . esc_attr($description) . '" title="' . esc_attr($description) .'" />';
		if ($attachment->post_excerpt != '') $result .= '</a>';
	}
	$result .= '</div></div>';

	return apply_filters( 'sb_slideshow_result', $result, $id, $dimensions, $control, $slides ); // finally, output the resulting code

	} else {
		$slides = get_post_meta($id, 'slide', false);
	//usort($slides, 'sb_slide_sort'); // order the elements of the array
	$total = count($slides);
	$random = (mt_rand()%$total);
	$slide = $slides[$random];

	$result = '';
	$result .= '<div class="slider_wrapper" style="width:' . absint( $dimensions['width'] ) . 'px">';
	$result .= '<img src="' . sb_get_post_image_url( array( 'width' => $dimensions['width'], 'height' => $dimensions['height'], 'image_id' 	=> $slide['attachment_id'], 'echo' 	=> false ) ) . '" width="' .  $dimensions['width'] . '" height="' .  $dimensions['height'] . '" alt="' . esc_attr($description) . '" title="' . esc_attr($description) .'" />';
	$result .= '<span class="slide_caption">'.$slide['caption'].'</span>';
	$result .= '</div>';

	$result .= '';

	return $result;
	}
}
add_shortcode( 'slideshow', 'sb_slideshow_shortcode' );

// Add Custom Management Columns
function sb_slideshow_columns( $columns ) {
	unset( $columns['date'] ); // remove date column
	$columns['id'] = 'ID';
	$columns['shortcode'] = 'Shortcode';
	$columns['date'] = 'Date'; // add date column back, at the end

	return $columns;
}
add_filter( 'manage_edit-slideshow_columns', 'sb_slideshow_columns', 10, 1 );

// Handle Custom Management Columns
function sb_slideshow_custom_columns( $column, $post_id ) {
	switch( $column ) {
		case 'id':
			echo $post_id;
		break;
		case 'shortcode':
			echo sb_slideshow_embed_input( $post_id );
		break;
	}
}
add_action( 'manage_slideshow_posts_custom_column', 'sb_slideshow_custom_columns', 10, 2 );

// Filter Slideshow Post Content (for when visiting a slideshow's public URL)
function sb_slideshow_content_filter( $content ) {
	global $post;

	if( $post->post_type == 'slideshow' )
		return '[slideshow id="' . $post->ID . '"]';
	else
		return $content;
}
add_filter( 'the_content', 'sb_slideshow_content_filter' );

// Add Scripts To Head On Front End Only
function sb_slideshow_template_redirect() {
	wp_enqueue_script( 'jquery' );

	if( has_action( 'sb_slideshow_enqueue' ) ) { // allow default enqueues to be replaced
		do_action( 'sb_slideshow_enqueue' );
	} else {
		wp_enqueue_script( 'nivoslider', INCLUDES_URL . '/extensions/startbox-slideshows/jquery-nivo/jquery.nivo.slider.js', __FILE__ );
		wp_enqueue_style( 'nivo_slider', INCLUDES_URL . '/extensions/startbox-slideshows/jquery-nivo/css/nivo-slider.css', __FILE__ );
		wp_enqueue_style( 'nivo_custom', INCLUDES_URL . '/extensions/startbox-slideshows/jquery-nivo/css/custom-nivo-slider.css', __FILE__ );
	}
}
add_action( 'template_redirect', 'sb_slideshow_template_redirect' );

// Exclusively Output In Head On Front End
function sb_slideshow_wp_head() {
	do_action( 'sb_slideshow_wp_head' ); // allow for a safe place to add to the head
}
add_action( 'wp_head', 'sb_slideshow_wp_head' );

// Add Javascript To Footer On Front End Only
function sb_slideshow_footer_javascript() {
	global $sb_slideshow_used_ids, $sb_slideshow_footer_javascript;

	if (empty( $sb_slideshow_used_ids )) return; // don't add javascript to footer if there are no slideshows
?>
	<script type="text/javascript">
	//<![CDATA[
	;(function($) {
		$(document).ready(function(){
<?php
			echo $sb_slideshow_footer_javascript;
?>
			// center any existing nivo control navs
			$('.with-controlNav').each(function( index, value ) {
				ctrlnav = $('.nivo-controlNav', this);
				ctrlnav.css('left', ($(this).width() - ctrlnav.width()) / 2);
			});
		});
	})(jQuery);
	//]]>
	</script>
<?php
}
add_action( 'wp_footer', 'sb_slideshow_footer_javascript' );

// Add Plugin Javascript To Head On Back End Post Pages
function sb_slideshow_post_admin_print_scripts() {
	// enqueue scripts
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ajaxuploader', SCRIPTS_URL . '/jquery.ajaxupload.js' );
}
add_action( 'admin_print_scripts-post.php', 'sb_slideshow_post_admin_print_scripts' );
add_action( 'admin_print_scripts-post-new.php', 'sb_slideshow_post_admin_print_scripts' );


/**
 * StartBox Handle Upload AJAX
 *
 * Necessary functions for handling the media uploads gracefully.
 * Currently only supports images.
 *
 * @uses sb_handle_upload
 * @since 2.4.4
 */
function sb_test_ajax() {

	echo json_encode( array('thumb' => 'thumb', 'full' => 'full', 'error' => '') );
	die();

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
	echo json_encode( array('thumb' => $thumb[0], 'full' => $full[0], 'error' => $error) );
	die();
}
add_action('wp_ajax_sb_test', 'sb_test_ajax');

// Add Plugin Javascript And Style To Head On Back End Post Pages
function sb_slideshow_post_admin_head() {
	global $post, $sb_slideshow_interface, $sb_slideshow_slides;

	$attachments = array();
	foreach ($sb_slideshow_slides as $slide) array_push( $attachments, $slide['attachment'] );

	if (!sb_verify_post_type('slideshow')) return; // verify post type
?>
	<script type="text/javascript">
		//<![CDATA[
		;(function($) {
			$(document).ready(function(){
				var attachments = <?php echo json_encode( $attachments ); ?>
				// sortable container resize
				function container_resize() {
					$.each(['#sb_slides', '#sb_library'], function( index, obj ) {
						$(obj).removeClass( 'closed' ); // make sure meta box is opened before calculating width
						var sortable = $('ul.connectedSortable', obj);
						sortable.outerWidth( ($('li.sb_item:visible', sortable).length + 1) * $('li.sb_item:visible:first', sortable).outerWidth() );
						$('div.scrollingHotSpotLeft, div.scrollingHotSpotRight', obj).outerHeight( sortable.outerHeight() );
					});

					checkScrollPos();
				}
				container_resize(); // initial sizing

				// update hidden sortable inputs
				function hidden_inputs() {
					$('#sb_slides ul.connectedSortable li').each(function(index) {
						$('.sb_order', this).val( index );
					});
					$('#sb_slides ul.connectedSortable li .sb_box').val( 'slides' );
					$('#sb_library ul.connectedSortable li .sb_box').val( 'library' );
				}

				// prepend an item to a box
				function prepend_to( location, sb_item ) {
					var sortable = $('#sb_' + location + ' ul.connectedSortable');
					sortable.parent().stop().animate({ scrollLeft: 0 }, 'slow'); // alternative: sortable.outerWidth() - $(this).scrollLeft()
					sb_item = $(sb_item); // force object
					sb_item.fadeOut('fast', function() {
						sb_item.prependTo( sortable ); // alternative: append
						sb_item.fadeIn('slow');
						filter_library();
						container_resize();
						hidden_inputs();
					});
				}

				// move sortable item via add/remove link
				$('.move-to-library').live('click', function() {
					prepend_to( 'library', $(this).parent('div.sb_right').parent('li.sb_item') );
					return false;
				});
				$('.move-to-slides').live('click', function() {
					prepend_to( 'slides', $(this).parent('div.sb_right').parent('li.sb_item') );
					return false;
				});

				// connected sortable lists
				$('#sb_slides ul.connectedSortable, #sb_library ul.connectedSortable').sortable({
					connectWith: '.connectedSortable',
					handle: 'img',
					forceHelperSize: false,
					forcePlaceholderSize: true,
					helper: function( ev, el ) { return $('img', el).clone().width(<?php
						echo floor( $sb_slideshow_interface['slide_width'] / 3 ); ?>).height(<?php
						echo floor( $sb_slideshow_interface['slide_height'] / 3 ); ?>); },
					placeholder: 'placeholder',
					revert: true,
					cursor: 'move',
					cursorAt: { top: -1, left: <?php echo floor( $sb_slideshow_interface['slide_height'] / 4.2 ); ?> },
					containment: '#normal-sortables',
					scrollSensitivity: 0,
					tolerance: 'pointer'
				});
				$('#sb_slides ul.connectedSortable, #sb_library ul.connectedSortable').bind('sortreceive', function( ev, ui ) {
					filter_library();
					container_resize();
				});
				$('#sb_slides ul.connectedSortable').bind('sortupdate', function( ev, ui ) {
					hidden_inputs();
				});

				// sortable list scrolling
				var interval_id = 0;
				$('div.scrollingHotSpotLeft, div.scrollingHotSpotRight').mouseenter(function() {
					var scrollDiv = $(this).parent();
					var scrollAmount = $('li.sb_item:first', scrollDiv).outerWidth() * 2;
					scrollAmount *= ($(this).attr('class') == 'scrollingHotSpotLeft' ? -1 : 1);

					if( interval_id == 0 ) {
						interval_id = setInterval(function() {
							scrollDiv.stop().animate({ scrollLeft: scrollDiv.scrollLeft() + scrollAmount }, 280);
						}, 400);
					}
				});
				$('div.scrollingHotSpotLeft, div.scrollingHotSpotRight').mouseleave(function() {
					if( interval_id != 0 ) {
						clearInterval(interval_id);
						interval_id = 0;
					}
				});
				$('div.scrollingContainer').scroll(function() {
					checkScrollPos();
				});
				function checkScrollPos() {
					$('div.scrollingContainer').each(function() {
						var scrollLeft = $(this).scrollLeft();
						var left = $('div.scrollingHotSpotLeft', this);
						var right = $('div.scrollingHotSpotRight', this);
						var sortableW = $('ul.connectedSortable', this).outerWidth();
						var containerW = $(this).outerWidth();

						if( sortableW > containerW ) {
							if( scrollLeft == 0 )
								left.fadeOut('slow');
							else
								left.fadeIn('slow');

							if( scrollLeft == (sortableW - containerW) )
								right.fadeOut('slow');
							else
								right.fadeIn('slow');
						} else {
							left.fadeOut('slow');
							right.fadeOut('slow');
						}
					});
				}

				// library filtering
				$('div#libraryFilters input').keypress(function( e ) {
					if( e.which == 13 ) {
						e.preventDefault();
						return false;
					}
				});
				$('div#libraryFilters input').keyup(function() {
					var oldVal = $(this).val();
					var newVal = (!isNaN( parseInt( oldVal, 10 ) ) ? parseInt( oldVal ) : '');
					newVal = String( newVal ).replace( /[^0-9]/g, '' );

					if( newVal == '0' )
						$(this).val( 0 );
					else
						$(this).val( newVal );

					if (oldVal == '' || oldVal.slice( 0, -1 ) != newVal) filter_library();
				});
				$('div#libraryFilters select').not('select#filter_year').change(function() {
					filter_library();
				});
				// show/hide filter months on year change
				function filterMonthToggle( monthClass ) {
					var monthClass = (monthClass != 'all' ? ', option.y' + monthClass : '');
					$('select#filter_month option').removeAttr( 'selected' ).removeAttr( 'disabled' ).not('option:first' + monthClass).attr( 'disabled', 'disabled' );
					$('select#filter_month option:first').attr( 'selected', 'selected' );
				}
				$('select#filter_year').change(function() {
					filterMonthToggle( $(this).val() );
					filter_library();
				});
				filterMonthToggle( $('select#filter_year').val() ); // initial month input value
				var stopFiltering;
				function filter_library() {
					stopFiltering = true;

					var year = $('#filter_year').val();
					var month = $('#filter_month').val();
					var type = $('#filter_type').val();
					var width_ltgt = $('#filter_width_ltgt').val();
					var width = parseInt( $('#filter_width').val() );
					var height_ltgt = $('#filter_height_ltgt').val();
					var height = parseInt( $('#filter_height').val() );

					$(attachments).each(function( index, attachment ) {
						if( index == 0 ) {
							stopFiltering = false;
						} else {
							if (stopFiltering) return false; // stop if mid-way through when a new is filter initiated
						}

						slide = $('#sb_library ul.connectedSortable li#attachment_id-' + attachment.id);

						if( slide.length != 0 ) { // check if slide exists
							var hide = false;

							if( year != 'all' && attachment.year != year ) hide = true;

							if( month != 'all' && attachment.month != month ) hide = true;

							if( type != 'all' && attachment.type != type ) hide = true;

							attachment.width = parseInt( attachment.width );
							if( typeof width == 'number' && typeof attachment.width == 'number' ) {
								if (width_ltgt == 'lt' && attachment.width > width) hide = true;
								if (width_ltgt == 'gt' && attachment.width < width) hide = true;
							}

							attachment.height = parseInt( attachment.height );
							if( typeof height == 'number' && typeof attachment.height == 'number' ) {
								if (height_ltgt == 'lt' && attachment.height > height) hide = true;
								if (height_ltgt == 'gt' && attachment.height < height) hide = true;
							}

							if (hide) slide.hide(); else slide.show();
						}

						if (index == $(attachments).length-1) container_resize(); // resize container when finished filtering
					});
				}
				filter_library(); // initial filtering

				// clear filters
				$('#clear-filters').click(function() {
					$('div#libraryFilters select').each(function( i, v ) {
						$('option:selected', this).removeAttr( 'selected' );
						$('option:first', this).attr( 'selected', 'selected' );
					});
					$('div#libraryFilters input#filter_width').val(<?php echo $sb_slideshow_interface['filter_width']; ?>);
					$('div#libraryFilters input#filter_height').val(<?php echo $sb_slideshow_interface['filter_height']; ?>);

					filter_library();

					return false;
				});

				// file upload
				var fid = 'userfile';
				new AjaxUpload($('a#uploadlink'), {
					action: ajaxurl,
					name: fid,
					data: { action: 'sb_action_handle_upload_ajax', file_id: fid },
					responseType: 'json',
					onSubmit: function( file , ext ){
						if( ext && /^(jpg|png|jpeg|gif)$/.test(ext) ) {
							$('span#uploadresult').html( 'Uploading ' + file + '...' );
						} else {
							// extension is not allowed
							$('span#uploadresult').html( 'Error: Only images are allowed.' );
							return false;	// cancel upload
						}
					},
					onComplete: function( file, response ){
						if( response.error != '' ) {
							$('span#uploadresult').html( response.error ); // show user the error
						} else {
							$('span#uploadresult').html( file + ' has been uploaded!' );

							var offset = $(attachments).length;
							$.post(ajaxurl, { action: 'sb_slideshow_upload', id: '<?php echo $post->ID; ?>', index_offset: offset }, function(response) {
								attachments[offset] = $.parseJSON( response.object ); // append to attachments array
								addToSelect( 'type', attachments[offset]['type'], null ); // add mime type to filter area if not already present

								// add year and month to filter if not already present
								var year = attachments[offset]['year'];
								addToSelect( 'year', year, null );
								addToSelect( 'month', attachments[offset]['month'], year );

								$('select#filter_month option').sort( sortNum ).appendTo( 'select#filter_month' ); // sort the months to make sure they are in order
								filterMonthToggle( $('select#filter_year').val() ); // enable/disable months based on year value
								prepend_to( 'library', response.html ); // prepend to library
							}, 'json');
						}
					}
				});

				// this is stupid, but it works for now
				function addToSelect( key, text, year ) {
					sel = 'select#filter_' + key;

					var kill = false;
					$(sel + ' option').each(function( i, v ) {
						if ($(this).val() == text) {
							if (year != null) $(this).addClass('y' + year);

							kill = true; // not able to add to select, option already exists
						}
					});

					if (kill) return false;

					if (year != null)
						$('<option>').val( text ).text( text ).addClass('y' + year).appendTo( sel );
					else
						$('<option>').val( text ).text( text ).appendTo( sel );

					return true; // option added to select
				}

				// custom number sorting funtion
				function sortNum( a, b ){
					if ($(a).val() == 'all') return -1;
					return $(a).val() > $(b).val() ? 1 : -1;
				};

				// show/hide custom size
				$('#size_radio_buttons input[type="radio"]').change(function() {
					if( $(this).val() == 'custom' )
						$('#custom_size').show();
					else
						$('#custom_size').hide();
				});
				if( $('#size_radio_buttons input[id="size_custom"]').is(':checked') )
					$('#custom_size').show(); // initial custom size show/hide

				// slideshow options view
				$('#slideshow_radio_buttons input[type="radio"]').change(function() {
					if($(this).val() == 'slideshow_on')
						$('#slideshow_on_options').show();
					else
						$('#slideshow_on_options').hide();
				});
				if( $('#slideshow_radio_buttons input[id="ssorri_slideshow_off"]').is(':checked') )
					$('#slideshow_on_options').hide();

				// slideshow title is required
				$('input#publish, input#save-post, a#post-preview').mousedown(function(e) {
					if( $('input#title').val() == '' ) {
						e.preventDefault();
						$('input#title').focus();
						alert( 'You must enter a title for this slideshow!' );
						return false;
					}
				});


				// clear default link
				$('input.slide-link').live('focus', function() {
					if ($(this).val() == '<?php echo $sb_slideshow_interface['link_text']; ?>') $(this).val( '' );
				});
				$('input.slide-link').live('blur', function() {
					if ($(this).val() == '') $(this).val( '<?php echo $sb_slideshow_interface['link_text']; ?>' );
				});
			});
		})(jQuery);
		//]]>
	</script>

	<style type="text/css">
		/* slides and library */
		#sb_slides div.inside,
		#sb_library div.inside { position:relative; }
		div.scrollingContainer {
			overflow:auto;
			white-space:nowrap;
			margin:0;
		}
		ul.connectedSortable { width:9999px; overflow:auto; }
		ul.connectedSortable li {
			float:left;
			display:inline;
			position:relative;
			margin:0;
		}
		ul.connectedSortable img { cursor:move;	}
		ul.connectedSortable li,
		.ui-sortable-helper { padding:5px; }
		.placeholder { background:#257DA6 !important; }
		div.sb_right { position:absolute; top:1px; right:2px; padding:5px 10px; }
		div.sb_right a { text-decoration:none; }

		/* slides */
		#sb_slides ul.connectedSortable {
			min-width:<?php echo $sb_slideshow_interface['slide_width'] * 2; ?>px;
			min-height:<?php echo $sb_slideshow_interface['slide_height'] + 87; ?>px;
		}
		#sb_slides.closed ul.connectedSortable { min-height:0; }
		#sb_slides ul.connectedSortable img {
			width:<?php echo $sb_slideshow_interface['slide_width']; ?>px;
			height:<?php echo $sb_slideshow_interface['slide_height']; ?>px;
		}
		#sb_slides ul.connectedSortable .placeholder {
			width:<?php echo $sb_slideshow_interface['slide_width'] + 2; ?>px;
			height:<?php echo $sb_slideshow_interface['slide_height'] + 77; ?>px;
		}
		#sb_slides ul.connectedSortable textarea {
			width:<?php echo $sb_slideshow_interface['slide_width']; ?>px;
			height:50px;
			display:block;
		}
		#sb_slides ul.connectedSortable label { float:left; line-height:32px; width:55px; }
		#sb_slides ul.connectedSortable .slide-link {
			height:25px;
			margin-top:5px;
			display:block; width:<?php echo $sb_slideshow_interface['slide_width'] - 54; ?>px;
		}
		#sb_slides ul.connectedSortable .sb_right a.move-to-slides { display:none; }

		/* library */
		#sb_library ul.connectedSortable {
			min-width:<?php echo $sb_slideshow_interface['slide_width'] * 2; ?>px;
			min-height:<?php echo ($sb_slideshow_interface['slide_height'] / 3) + 10; ?>px;
		}
		#sb_library ul.connectedSortable img,
		#sb_library .placeholder {
			width:<?php echo floor($sb_slideshow_interface['slide_width'] / 3); ?>px;
			height:<?php echo floor($sb_slideshow_interface['slide_height'] / 3); ?>px;
		}
		#sb_library ul.connectedSortable textarea,
		#sb_library ul.connectedSortable label,
		#sb_library ul.connectedSortable .slide-link,
		#sb_library ul.connectedSortable .sb_right a.move-to-library { display:none !important; }

		/* scrolling hotspots */
		div.scrollingHotSpotLeft, div.scrollingHotSpotRight {
			display:none;
			width:50px;
			height:100%;
			top:0;
			position:absolute;
			z-index:200;
			background-position:center center;
			background-repeat:no-repeat;
			background-color:#F9F9F9;
			opacity:0.35;
			-moz-opacity:0.35;
			filter:alpha(opacity = 35);
			zoom:1; /* trigger "hasLayout" in Internet Explorer 6 or older */
		}
		div.scrollingHotSpotLeft	{
			left: 0;
			background-image: url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/arrow_left.gif'; ?>);
			cursor: url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/cursors/cursor_arrow_left.cur'; ?>),
				url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/cursors/cursor_arrow_left.cur'; ?>), w-resize;
		}
		div.scrollingHotSpotRight {
			right: 0;
			background-image: url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/arrow_right.gif'; ?>);
			cursor: url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/cursors/cursor_arrow_right.cur'; ?>),
				url(<?php echo INCLUDES_URL . '/extensions/startbox-slideshows/images/admin/cursors/cursor_arrow_right.cur'; ?>), w-resize;
		}

		/* interface */
		div.scrollingContainer,
		.ui-sortable-helper,
		div.sb_right,
		ul.connectedSortable li {
			background:<?php echo $sb_slideshow_interface['color']; ?>;
		}
		input.pixelfield { width:44px; }
		input.urlfield { width:155px; }
		#uploaddiv { position:absolute; top:-29px; left:110px; }
		div#libraryFilters { margin-top:8px; text-align:left; overflow:auto; }
		div#libraryFilters div {
			margin-top:6px;
			line-height:2em;
			white-space:nowrap;
			float:left;
			margin-left:10px;
		}
		div#libraryFilters div em { line-height:2em; display:block; }
		div.filterGroup {
			padding-left:10px;
			border-left:1px solid #EAEAEA;
		}
	</style>
<?php
}
add_action( 'admin_head-post.php', 'sb_slideshow_post_admin_head' );
add_action( 'admin_head-post-new.php', 'sb_slideshow_post_admin_head' );

// Add Uploaded File To Library
function sb_slideshow_upload() {
	global $sb_slideshow_interface, $sb_slideshow_slides;

	// create slide array for newest attachment
	$index = sb_slideshow_slides( $sb_slideshow_interface['mysql_select'] . ' ORDER BY post_date DESC LIMIT 1' );

	ob_start();
	sb_slideshow_sortable_item( $index[0] );
	$html = ob_get_clean();

	die( json_encode( array(
		'object' 	=> json_encode( $sb_slideshow_slides[$index[0]]['attachment'] ),
		'html' 	=> $html ) ) );
}
add_action( 'wp_ajax_sb_slideshow_upload', 'sb_slideshow_upload' );

// Add Plugin Style To Head On Back End Edit Page
function sb_slideshow_edit_admin_style() {
	if (!sb_verify_post_type('slideshow')) return; // verify post type
?>
	<style type="text/css">
		th.column-id { width:50px; }
		th.column-shortcode { width:180px; }
		input.urlfield { width:155px; }
	</style>
<?php
}
add_action( 'admin_print_styles-edit.php', 'sb_slideshow_edit_admin_style' );


function load_widget_sb_slideshow_widget() { // Widget: Search Widget
	register_widget('sb_slideshow_widget');
}
add_action( 'widgets_init', 'load_widget_sb_slideshow_widget', 0 );

class sb_slideshow_widget extends WP_Widget {
	function sb_slideshow_widget() {
		$widget_ops = array(
			'classname'    =>  'sb_slideshow_widget',
			'description'  =>  __( "A widget for displaying your slideshows.", "startbox" )
		);
		$this->WP_Widget( 'slide-widget', __('SB Slideshow', 'startbox'), $widget_ops);
	}

	function form($instance) {
		$defaults = array(
			'title' => '',
			'slideshow' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'startbox' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('slideshow'); ?>"><?php _e( 'Slideshow: ', 'startbox' ) ?></label>
			<?php
				$args = array(
					'post_type'	=> 'slideshow',
					'order_by'	=> 'post_title',
					'order'		=> 'ASC',
					'id'		=> $this->get_field_id('slideshow'),
					'name'		=> $this->get_field_name('slideshow'),
					'selected'	=> $instance['slideshow'],
					'option_none_value' => 'Select a Slideshow'
				);
				sb_dropdown_posts($args);
			?>
		</p>
		<p>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=slideshow' ) ); ?>"><?php _e( 'Edit Slideshow Settings', 'startbox' ); ?></a>
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['slideshow'] = strip_tags( $new_instance['slideshow'] );

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$slideshow = ( isset( $instance['slideshow'] ) ) ? $instance['slideshow'] : false;

		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }
		if ($slideshow) echo do_shortcode('[slideshow id="' . $slideshow . '"]');
		echo $after_widget;
	}
}

}