<?php 
    /**
     * php/menus/menu-header-primary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
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