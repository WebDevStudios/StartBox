<?php
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks.' );
?>
<div id="comments">
<?php
	if ( post_password_required() ) { ?>
		<div class="nopassword"><?php _e( 'This post is protected. Enter the password to view any comments.', 'startbox' ) ?></div>
	</div><!-- .comments -->
<?php return; } ?>

<?php if ( have_comments() ) : ?>

	<?php // Number of pings and comments
		$ping_count = $comment_count = 0;
		foreach ( $comments as $comment )
			get_comment_type() == "comment" ? ++$comment_count : ++$ping_count;
			if ( ! empty($comments_by_type['comment']) ) : ?>

				<div id="comments-list" class="comments">
					<h3><?php printf($comment_count > 1 ? __('<span>%d</span> Comments', 'startbox') : __('<span>One</span> Comment', 'startbox'), $comment_count) ?> <a href="#respond" class="h3-link">(Add Yours)</a></h3>

					<ol class="commentlist">
						<?php wp_list_comments('type=comment&callback=sb_comments'); // format controlled via /includes/functions/comment_format.php ?>
					</ol>
					
					<?php if ( get_comment_pages_count() > 1 ) : // Are there comments to navigate through? ?>
						<div id="comments-nav-below" class="comment-navigation">
							<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
						</div>
					<?php endif; // check for comment navigation ?>

				</div><!-- #comments-list .comments -->
				<div class="cb"></div>
			<?php endif; // REFERENCE: if ( $comment_count ) ?>
			<?php if ( ! empty($comments_by_type['pings']) ) : ?>

				<div id="trackbacks-list" class="comments">
					<h3><?php printf($ping_count > 1 ? __('<span>%d</span> Trackbacks', 'startbox') : __('<span>One</span> Trackback', 'startbox'), $ping_count) ?></h3>

					<ol class="trackbacklist">
						<?php wp_list_comments('type=pings&callback=sb_pings'); // format controlled via /includes/functions/comment_format.php ?>
					</ol>
				</div><!-- #trackbacks-list .comments -->
				
		<?php endif // REFERENCE: if ( $ping_count ) ?>
	<?php endif // REFERENCE: if ( $comments ) ?>
	
	<?php comment_form(); ?>

</div><!-- #comments -->