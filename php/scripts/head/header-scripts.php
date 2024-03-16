<?php 
    /**
     * php/scripts/head/header-scripts.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.03.16)
     * @copyright 2024 (2024.03.16)
    **/
?>

<!-- FAVICON -->
<link rel="icon" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logos/icon-scgolfpanel-160x160.png"/>

<!-- FONTS -->
<?php include(get_stylesheet_directory().'/php/scripts/external/fonts/merriweather.php'); ?>
<?php include(get_stylesheet_directory().'/php/scripts/external/fonts/roboto.php'); ?>

<!-- FONT-AWESOME -->
<?php include(get_stylesheet_directory().'/php/scripts/external/font-awesome/header.php'); ?>

<!-- FRAMEWORK -->
<?php include(get_stylesheet_directory().'/php/scripts/external/bootstrap/header.php'); ?>

<!-- WP-HEAD -->
<?php wp_head(); ?>

<!-- GOOGLE-ANALYTICS -->
<?php include(get_stylesheet_directory().'/php/scripts/external/google-analytics/header.php'); ?>

<!-- STYLESHEET -->
<link rel="stylesheet" src="<?php echo get_stylesheet_directory_uri(); ?>/style.min.css"/>