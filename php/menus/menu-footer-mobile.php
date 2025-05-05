<?php 
    /**
     * php/menus/menu-footer-mobile.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.05.04)
     * @copyright 2025 (2025.05.04)
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