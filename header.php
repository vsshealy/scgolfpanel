<?php 
    /**
     * header.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<!-- SOUTH CAROLINA GOLF COURSE RANKINGS PANEL | WEBSITE DESIGN BY SCOTT SHEALY | SCOTTSHEALY.COM -->

<html id="scgolfpanel" class="h-100" <?php language_attributes(); ?>>
    
    <!-- HEAD -->
    <head>
        
        <!-- META -->
        <?php include(get_stylesheet_directory().'/php/scripts/head/meta.php'); ?>

        <!-- HEADER-SCRIPTS -->
        <?php include(get_stylesheet_directory().'/php/scripts/head/header-scripts.php'); ?>

    </head>

    <!-- BODY -->
    <?php 
        do_action('bricks_body');

        do_action('bricks_before_site_wrapper');

        do_action('bricks_before_header');

        do_action('render_header');

        do_action('bricks_after_header');
    ?>