<?php

if ( ! function_exists( 'sbx_post_type' ) ) :
/**
 * Register Custom Post Types
 */
function sbx_post_type( $type, $args = array() ) {

	if ( is_array( $type ) ) {
		$types = isset( $type[1] ) ? $type[1] : $type . 's';
		$key = isset( $type[2] ) ? $type[2] : strtolower( str_ireplace( ' ', '_', $type[1] ) );
		$slug = isset( $type[3] ) ? $type[3] : str_ireplace( '_', '-', $key );
		$type = $type[1];
	} else {
		$types = $type . 's';
		$key = strtolower( str_ireplace( ' ', '_', $type ) );
		$slug = str_ireplace( '_', '-', $key );
	}

	// Setup our labels
	$labels = array(
		'name'                => $type,
		'singular_name'       => $type,
		'add_new'             => 'Add New',
		'add_new_item'        => 'Add New ' .$type,
		'edit_item'           => 'Edit '.$type,
		'new_item'            => 'New '.$type,
		'view_item'           => 'View '.$type,
		'search_items'        => 'Search '.$types,
		'not_found'           => 'No '.$types.' found',
		'not_found_in_trash'  => 'No '.$types.' found in Trash',
		'parent_item_colon'   => '',
		'menu_name'           => $types
	);

	$rewrite = array(
		'slug'                => $slug,
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);

	$args = wp_parse_args( $args, array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'query_var'           => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'menu_position'       => '5',
		'has_archive'         => true,
		'exclude_from_search' => false,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
		'taxonomies'          => array()
	));

	// Finally, register all of this with WordPress
	register_post_type( $key, $args );

}
endif;


if ( ! function_exists( 'sbx_taxonomy' ) ) :
/**
 * Register Taxonomy
 */
function sbx_taxonomy( $type, $types, $key, $url_slug, $post_type_keys ) {

	$labels = array(
		'name'                       => $types,
		'singular_name'              => $type,
		'search_items'               => 'Search '.$types,
		'popular_items'              => 'Common '.$types,
		'all_items'                  => 'All '.$types,
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => 'Edit '.$type,
		'update_item'                => 'Update '.$type,
		'add_new_item'               => 'Add New '.$type,
		'new_item_name'              => 'New '. $type .' Name',
		'separate_items_with_commas' => 'Separate '. $types. ' with commas',
		'add_or_remove_items'        => 'Add or remove '.$types,
		'choose_from_most_used'      => 'Choose from the most used '.$types
	);

	$rewrite = array(
		'slug'                       => $url_slug,
		'with_front'                 => true,
		'hierarchical'               => true,

	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'query_var'                  => true,
		'rewrite'                    => $rewrite,
	);

	// Finally, register all of this with WordPress
	register_taxonomy( $key, $post_type_keys, $args );

}
endif;