<?php 
    /**
     * php/menus/menu-header-primary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.10.11)
     * @copyright 2024 (2024.10.11)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'header-primary',
            'menu_id' => 'Header-Primary'
        )
    );
?>