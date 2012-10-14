<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title(); ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	
	<!-- BEGIN wp_head() -->
	<?php wp_head(); ?>
	<!-- END wp_head() -->
</head>

<body <?php body_class(); ?>>
<?php do_action( 'sb_before' ); ?>
<div id="wrap" class="hfeed">
	
	<?php do_action( 'sb_before_header' ); ?>
	
	<?php if ( has_action( 'sb_header' ) ) { ?>
		<div id="header">
			<?php do_action( 'sb_header' ); ?>
		</div><!-- #header -->
	<?php } ?>
	
	<?php do_action( 'sb_after_header' ); ?>
	
	<div id="container_wrap">
		<?php do_action( 'sb_before_container' ); ?>