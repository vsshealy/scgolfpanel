<?php 
    /**
     * php/menus/menu-footer-primary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.12.01)
     * @copyright 2025 (2025.12.01)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'footer-primary',
            'menu_id' => 'Footer-Primary'
        )
    );
?>