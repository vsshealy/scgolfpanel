<?php 
    /**
     * php/menus/menu-header-secondary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.03.16)
     * @copyright 2024 (2024.03.16)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'menu-4',
            'menu_id' => 'Header-Secondary'
        )
    );
?>