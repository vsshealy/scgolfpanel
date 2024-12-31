<?php 
    /**
     * php/menus/menu-footer-mobile.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.12.04)
     * @copyright 2024 (2024.12.04)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'footer-mobile',
            'menu_id' => 'Footer-Mobile'
        )
    );
?>