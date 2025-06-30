<?php 
    /**
     * php/menus/menu-footer-secondary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.06.30)
     * @copyright 2025 (2025.06.30)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'footer-secondary',
            'menu_id' => 'Footer-Secondary'
        )
    );
?>