<?php
/**
 * inc/menus.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // REGISTER MENUS — members-apps DRIVES DASHBOARD PAGE CONTENT, NOT CHROME; SEE apps/member-site/dashboard.php
        function scgolfpanel_register_menus() {
            register_nav_menus( array(
                'header-primary'   => __( 'Header-Primary', 'scgolfpanel' ),
                'header-members'   => __( 'Header-Members', 'scgolfpanel' ),
                'members-apps'     => __( 'Members-Apps', 'scgolfpanel' ),
                'footer-primary'   => __( 'Footer-Primary', 'scgolfpanel' ),
                'footer-members'   => __( 'Footer-Members', 'scgolfpanel' ),
                'footer-secondary' => __( 'Footer-Secondary', 'scgolfpanel' ),
            ) );
        }
        add_action( 'after_setup_theme', 'scgolfpanel_register_menus' );