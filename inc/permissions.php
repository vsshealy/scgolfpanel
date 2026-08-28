<?php
/**
 * inc/permissions.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // CAPABILITY GATE — SHARED THEME-WIDE, PRINTS A RESTRICTED NOTICE AND RETURNS FALSE WHEN THE VIEWER LACKS THE CAPABILITY
        function scgolfpanel_require_capability( $capability, $message = '' ) {
            if ( is_user_logged_in() && current_user_can( $capability ) ) { return true; }

            printf(
                '<div class="apps-shared-notice apps-shared-notice-restricted"><p>%s</p></div>',
                esc_html( $message ?: __( 'You don’t have access to this page.', 'scgolfpanel' ) )
            );

            return false;
        }