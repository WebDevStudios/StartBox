<?php
/**
 * StartBox Comments
 *
 * Filters and templates for the WP Comment form
 *
 * @package StartBox
 * @subpackage Comments
 */

// Filter the default comment form
function sb_comment_defaults($defaults) {
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$required_text = sprintf( ' ' . __('Required fields are marked %s', 'startbox'), '<span class="required">*</span>' );

	$fields =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'startbox' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" title="' . __( 'Your Name', 'startbox' ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'startbox' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" title="' . __( 'Your Email', 'startbox' ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'startbox' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" title="' . __( 'Your Website (optional)', 'startbox' ) . '" size="30"/></p>',
	);

	$defaults['title_reply'] =  __( 'Add Your Comment', 'startbox' ).' <a href="http://gravatar.com" class="h3-link">(Get a Gravatar)</a>';
	$defaults['fields'] = apply_filters( 'comment_form_default_fields', $fields );
	$defaults['comment_field'] = '';
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes_after'] = '<p class="comment-notes">' . __( 'Your email address will <strong>not</strong> be published.', 'startbox' ) . ( $req ? $required_text : '' ) . '.</p>';
	$defaults['label_submit'] = __( 'Post Your Comment', 'startbox' );

	return $defaults;
}
add_filter( 'comment_form_defaults', 'sb_comment_defaults' );

function sb_insert_comment_form() { ?>
		<div class="comment-meta">
			<div class="comment-author vcard">
				<?php global $user_ID; if ( $user_ID ) {$user_info = get_userdata($user_ID); echo get_avatar( $user_info->user_email, apply_filters( 'sb_comment_gravatar_size', 60 ) ); } else { ?><a href="http://gravatar.com/" title="<?php _e('Get a Gravatar!', 'startbox'); ?>" target="_blank"><img class="avatar" width="<?php echo apply_filters( 'sb_comment_gravatar_size', 60 ); ?>" height="<?php echo apply_filters( 'sb_comment_gravatar_size', 60 ); ?>" src="<?php echo IMAGES_URL . '/comments/gravatar.png'; ?>" alt="<?php _e('Get a Gravatar!', 'startbox'); ?>"/></a><?php } ?>
				<cite id="authorname" class="fn n comment-author-name"><?php if ( $user_ID ) {$user_info = get_userdata($user_ID); echo esc_html( $user_info->display_name ); } else { echo 'Your Name'; } ?></cite>
			</div>
			<div class="comment-date comment-permalink"><?php echo date('M jS, Y'); ?><br/><?php echo date('g:ia'); ?></div>

			<span class="comment-arrow"></span>
		</div>
	<?php
	echo '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun', 'startbox' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
}
add_action( 'comment_form_top', 'sb_insert_comment_form' );

/**
 * Adds default StartBox gravatar to the list in Settings > Discussion to replace Mystery Man
 *
 * @since 2.4.8
 */
function sb_avatar_defaults($avatar_defaults) {
 	unset($avatar_defaults['mystery']);
	$sb_mystery = IMAGES_URL . '/comments/gravatar.png';
	$avatar_defaults[$sb_mystery] = 'Mystery Man (enhanced)';

	if (get_option('avatar_default') === 'mystery') { update_option('avatar_default', $sb_mystery); }
	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'sb_avatar_defaults' );

/**
 * Produces an avatar image with the hCard-compliant photo class
 *
 * Override this function in a child theme by creating your own sb_commenter_link() function
 *
 * Used by sb_comments() for displaying comment author information
 *
 * @since 1.4
 */
function sb_commenter_link() {
	global $avatar_defaults;
	$commenter = get_comment_author_link();
	if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
		$commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1comment-author-name  ' , $commenter );
	} else {
		$commenter = '<a href="'.get_permalink().'" class="comment-author-name">'.$commenter.'</a>';
	}
	$avatar_email = get_comment_author_email();
	$avatar_size = apply_filters( 'sb_comment_gravatar_size', '60' ); // Available filter: sb_comment_gravatar_size
	$avatar_default = apply_filters( 'sb_comment_gravatar_default', get_option('avatar_default') ); // Available filter: sb_comment_gravatar_default. Note: must be full URL
	$avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, $avatar_size, $avatar_default ) );
	echo $avatar . ' <cite class="fn n">' . $commenter . '</cite>';
}

/**
 * Template for comments
 *
 * Override this function in a child theme by creating your own sb_comments() function
 *
 * Used as a callback by wp_list_comments() for displaying the comments in comments.php.
 *
 * @since 1.4
 * @uses sb_commenter_link()
 *
 */
if ( !function_exists( 'sb_comments' ) ) {
	function sb_comments($comment, $args, $depth) {
	    $GLOBALS['comment'] = $comment;
	    ?>
	    	<li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
	    		<div class="comment-wrap">
					<div class="comment-meta">
						<div class="comment-author vcard"><?php sb_commenter_link() ?></div>
		    			<div class="comment-date"><a href="<?php echo esc_url( '#comment-' . get_comment_ID() ); ?>" class="comment-permalink" title="Permalink to this comment"><?php printf(__('%1$s <br/> %2$s', 'startbox'), get_comment_date(), get_comment_time()); ?></a></div>
					</div>
		    		<?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'startbox') ?>
		            <div class="comment-entry">
						<span class="comment-arrow"></span>
		        		<?php comment_text() ?>
						<div class="comment-footer">
							<?php // echo the comment reply link with help from Justin Tadlock http://justintadlock.com/ and Will Norris http://willnorris.com/
								if($args['type'] == 'all' || get_comment_type() == 'comment') :
									comment_reply_link(array_merge($args, array(
										'reply_text' => __('Reply to this Comment','startbox'),
										'login_text' => __('Log in to reply.','startbox'),
										'depth' => $depth,
										'before' => '<span class="comment-reply-link">',
										'after' => '</span>'
									)));
								endif;
							?>
							<?php edit_comment_link(__('Edit', 'startbox'), ' <span class="meta-sep">|</span> <span class="comment-edit-link">', '</span>'); ?>
						</div>
		    		</div>
				</div>
	<?php }
}

/**
 * Template for Trackbacks
 *
 * Override this function in a child theme by creating your own sb_pings() function
 *
 * Used as a callback by wp_list_comments() for displaying the comments in comments.php.
 *
 * @since 1.4
 */
if ( !function_exists( 'sb_pings' ) ) {
	function sb_pings($comment, $args, $depth) {
	       $GLOBALS['comment'] = $comment;
	        ?>
	    		<li><?php comment_author_link() ?></li>
	<?php }
}