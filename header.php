<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <main id="main">
 *
 * @package sbx
 */
?><!DOCTYPE html>
<?php do_action( 'before_html' ); ?>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js" type="text/javascript"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js" type="text/javascript"></script>
<![endif]-->
</head>
<?php do_action( 'before_body' ); ?>
<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
<?php do_action( 'before_page' ); ?>
<div id="page" class="hfeed site">
	<?php do_action( 'before_header_area' ); ?>
	<header id="masthead" class="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
		<?php do_action( 'before_header' ); ?>
		<div class="wrap">
			<div class="site-branding">
				<?php do_action( 'before_site_title' ); ?>
				<h1 class="site-title" itemprop="headline"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php do_action( 'before_site_description' ); ?>
				<h2 class="site-description" itemprop="description"><?php bloginfo( 'description' ); ?></h2>
				<?php do_action( 'after_site_description' ); ?>
			</div>
			<?php get_sidebar( 'header' ); ?>
		</div>
		<?php do_action( 'after_header' ); ?>
	</header><!-- #masthead -->
	<?php do_action( 'before_menu_area' ); ?>
	<nav id="site-navigation" class="main-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
		<div class="wrap">
			<h1 class="menu-toggle"><?php _e( 'Menu', 'sbx' ); ?></h1>
			<div class="screen-reader-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'sbx' ); ?>"><?php _e( 'Skip to content', 'sbx' ); ?></a></div>
			<?php wp_nav_menu( array( 'theme_location' => 'main-navigation' ) ); ?>
		</div>
	</nav><!-- #site-navigation -->
	<?php do_action( 'before_content_area' ); ?>
	<div id="content" class="site-inner">
		<?php do_action( 'before_wrap' ); ?>
		<div class="wrap">
