<?php 
    /**
     * php/menus/menu-members-secondary.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2025.03.17)
     * @copyright 2025 (2025.03.17)
    **/
?>

<?php 
    wp_nav_menu(
        array(
            'theme_location' => 'members-secondary',
            'menu_id' => 'Members-Secondary'
        )
    );
?>