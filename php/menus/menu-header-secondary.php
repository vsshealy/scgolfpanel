<?php 
    /**
     * php/menus/menu-header-secondary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.06.30)
     * @copyright 2025 (2025.06.30)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'header-secondary',
            'menu_id' => 'Header-Secondary'
        )
    );
?>