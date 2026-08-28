<?php
/**
 * functions.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // LOAD THEME INCLUDES — ORDER MATTERS: ROLES BEFORE PERMISSIONS BEFORE APPS
        $scgolfpanel_inc = get_stylesheet_directory() . '/inc/';

        require_once $scgolfpanel_inc . 'enqueue.php';
        require_once $scgolfpanel_inc . 'cleanup.php';
        require_once $scgolfpanel_inc . 'menus.php';
        require_once $scgolfpanel_inc . 'formatting.php';
        require_once $scgolfpanel_inc . 'media.php';
        require_once $scgolfpanel_inc . 'analytics.php';
        require_once $scgolfpanel_inc . 'admin.php';
        require_once $scgolfpanel_inc . 'roles.php';
        require_once $scgolfpanel_inc . 'permissions.php';
        require_once $scgolfpanel_inc . 'bricks.php';

        unset( $scgolfpanel_inc );