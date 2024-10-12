<?php 
    /**
     * php/menus/menu-members-primary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.10.11)
     * @copyright 2024 (2024.10.11)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'members-primary',
            'menu_id' => 'Members-Primary'
        )
    );
?>