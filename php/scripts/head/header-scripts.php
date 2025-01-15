<?php 
    /**
     * php/scripts/head/header-scripts.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.01.15)
     * @copyright 2025 (2025.01.15)
    **/
?>

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