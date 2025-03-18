<?php 
    /**
     * php/menus/menu-header-mobile.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.03.17)
     * @copyright 2025 (2025.03.17)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'header-mobile',
            'menu_id' => 'Header-Mobile'
        )
    );
?>