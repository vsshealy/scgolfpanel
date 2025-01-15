<?php 
    /**
     * php/menus/menu-members-mobile.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.01.15)
     * @copyright 2025 (2025.01.15)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'members-mobile',
            'menu_id' => 'Members-Mobile'
        )
    );
?>