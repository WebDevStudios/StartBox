# SBX_Breadcrumbs Class

## Description

Base class for handling breadcrumb navigation in StartBox

## Hooks

### Action Hooks

None

### Filter Hooks

### sb_breadcrumb_args

Allows you to modify the default arguments that get used when creating the breadcrumb output.

Default values:

**(array)** Default arguments

	array(
		'before_crumbs'           => '<div class="breadcrumbs">',
		'after_crumbs'            => '</div>',
		'separator'               => ' / ',
		'hierarchial_attachments' => true,
		'hierarchial_categories'  => true,
		'labels' => array(
			'prefix'    => __( 'You are here: ', 'startbox' ),
			'home'      => __( 'Home', 'startbox' ),
			'search'    => __( 'Search for ', 'startbox' ),
			'404'       => __( 'Link Not Found', 'startbox' )
		)
	);


### sb_build_crumb_trail

Allows you to modify the text and links created for the breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** Complete crumb string, including the specified separator.

	$this->glue_crumbs_together( $trail ) 

Extra parameters:

**(array)** Arguments used for the breadcrumb instance.

	$this->args

### sb_get_archive_crumb

Allows you to modify the text and links created for specifically the archive breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** Complete crumb string, including the specified separator.

	$this->glue_crumbs_together( $trail )

Extra parameters:

**(object)** Current WP_Query object
**(array)** arguments used for the breadcrumb instance.	

	$wp_query, $this->args

### sb_get_blog_crumb

Allows you to modify the text and links created for specifically the blog page breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** title for the specified blog page

	$trail

Extra parameters:

**(array)** arguments used for the breadcrumb instance.

	$this->args

### sb_get_breadcrumb_link

Allows you to modify the link that gets used within the breadcrumb trail.

Default value:

**(string)** Complete `<a>` tag with href attribute and link text

	sprintf( '<a href="%1$s">%2$s</a>', esc_attr( $url ), esc_html( $text ) )

Extra parameters:

**(array)** arguments used for the breadcrumb instance.

	$this->args

### sb_get_home_crumb

Allows you to modify the text and links created for specifically the home/front page breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** title for the specified home page

	$trail

Extra parameters:

**(array)** arguments used for the breadcrumb instance.

	$this->args

### sb_get_paged_crumb

Allows you to modify the text and links created for specifically the paginated "paged" breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** text for the current paginated page.

	$trail

Extra parameters:

**(array)** arguments used for the breadcrumb instance.

	$this->args

### sb_get_post_term_crumbs

Allows you to modify the text and links created for specifically the term archive breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** Complete crumb string, including the specified separator.

	$this->glue_crumbs_together( $trail )

Extra parameters:

**(object)** Current term object
**(object)** Current taxonomy object
**(array)** arguments used for the breadcrumb instance.	

	$wp_query, $this->args

### sb_get_search_crumb

Allows you to modify the text and links created for the search breadcrumb trail. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** text for the current searched word or phrase.

	$trail

Extra parameters:

**(array)** arguments used for the breadcrumb instance.

	$this->args

### sb_get_singular_crumb

Allows you to modify the text and links created for specifically for the single post being viewed. If the crumb is for the current url, it will not be linked, otherwise it will be linked.

Default value:

**(string)** Complete crumb string, including the specified separator.

	$this->glue_crumbs_together( $trail )

Extra parameters:

**(object)** Current $post object
**(array)** arguments used for the breadcrumb instance.	

	$post, $this->args

### sb_get_404_crumb

Allows you to intercept and modify the 404 error label text

Default value:

**(string)** Label used with the 404 label parameter

	$this->args['labels']['404']

**(array)** arguments used for the breadcrumb instance.

	$this->args

### the_search_query

Allows you to intercept and modify a searched for word or phrase.

Default value:

**(string)** Searched word or phrase

	get_search_query()


## Change Log

Since: 2.4.4

## Source File

SBX_Breadcrumb.php is located in /sbx/classes/
