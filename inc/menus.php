<?php
    /**
     * inc/menus.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // REGISTER MENUS
        function scgolfpanel_register_menus() {
            register_nav_menus(
                array(
                    'header-primary'   => __( 'Header-Primary' ),
                    'footer-primary'   => __( 'Footer-Primary' ),
                    'footer-secondary' => __( 'Footer-Secondary' ),
                )
            );
        }
        add_action( 'init', 'scgolfpanel_register_menus' );

?>
