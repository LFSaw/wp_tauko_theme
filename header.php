<?php
/*
 * @package Tauko
 *
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <title><?php bloginfo('name');?></title>
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/style_screen.css" />
    <link rel="stylesheet" media="handheld" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/style_mobile.css" />
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.gif" />
    <link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri() . "/slimbox2.css";?>" />
    <?php wp_head(); ?>
  </head>



  <body <?php body_class(); ?> onload="$('#tauko_boxes').isotope( 'reLayout' );">
	 <div id="jelly">
	 <header class="site-header" role="banner">
	 <nav id="site-navigation" class="main-navigation" role="navigation">
	 <?php wp_nav_menu( array( 'menu_class' => 'nav_menu', 'order_column' => 'menu_order', 'depth' => 2)); ?>
	 </nav>
	 </header>

	 <div id="content">
