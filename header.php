<?php
/**
 * The Header for our theme.
 *
 * @package sbx
 */
?>
<?php do_action( 'html_before' ); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php do_action( 'head_top' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php do_action( 'head_bottom' ); ?>
<?php wp_head(); ?>
<!--[if lt IE 9]>
	<script src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/html5shiv.js" type="text/javascript"></script>
	<script src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/respond.min.js" type="text/javascript"></script>
<![endif]-->
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
<?php do_action( 'body_top' ); ?>
<div id="page" class="hfeed site">
	<?php do_action( 'header_before' ); ?>
	<header id="masthead" class="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
		<div class="wrap">
			<?php do_action( 'header_top' ); ?>
			<div class="site-branding">
				<h1 class="site-title" itemprop="headline"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<h2 class="site-description" itemprop="description"><?php bloginfo( 'description' ); ?></h2>
			</div>
			<?php sb_do_sidebar( 'header_widget_area', 'header-widget-area', 'span-6' ); ?>
			<?php do_action( 'header_bottom' ); ?>
		</div>
	</header><!-- #masthead -->
	<?php do_action( 'header_after' ); ?>
	<nav id="site-navigation" class="main-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
		<div class="wrap">
			<h1 class="menu-toggle"><?php _e( 'Menu', 'sbx' ); ?></h1>
			<div class="screen-reader-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'sbx' ); ?>"><?php _e( 'Skip to content', 'sbx' ); ?></a></div>
			<?php wp_nav_menu( array( 'theme_location' => 'main-navigation' ) ); ?>
		</div>
	</nav><!-- #site-navigation -->
	<?php do_action( 'content_top' ); ?>
	<div id="content" class="site-inner">
		<div class="wrap">