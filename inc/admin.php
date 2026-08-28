<?php
/**
 * inc/admin.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // HIDE THE NATIVE "WEBSITE" FIELD ON ADD/EDIT USER SCREENS
        function scgolfpanel_hide_user_website_field( $hook_suffix ) {
            if ( ! in_array( $hook_suffix, array( 'profile.php', 'user-edit.php', 'user-new.php' ), true ) ) { return; }

            wp_add_inline_style( 'common', '.user-url-wrap { display: none; }' );
        }
        add_action( 'admin_enqueue_scripts', 'scgolfpanel_hide_user_website_field' );