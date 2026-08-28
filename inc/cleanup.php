<?php
/**
 * inc/cleanup.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // HEAD CLEANUP — OBSOLETE CORE TAGS; REST API AND SHORT URLS THEMSELVES STAY ENABLED
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'rest_output_link_wp_head' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

    // FEED AND OEMBED DISCOVERY — NO BLOG, NO NATIVE COMMENTS
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // EMOJI POLYFILL — DETECTION SCRIPT, INLINE STYLES, DNS PREFETCH
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'embed_head', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

        function scgolfpanel_disable_emojis_tinymce( $plugins ) {
            return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
        }
        add_filter( 'tiny_mce_plugins', 'scgolfpanel_disable_emojis_tinymce' );

        function scgolfpanel_remove_emoji_dns_prefetch( $urls, $relation_type ) {
            if ( 'dns-prefetch' !== $relation_type ) { return $urls; }

            foreach ( $urls as $key => $url ) {
                if ( is_string( $url ) && false !== strpos( $url, 'https://s.w.org/images/core/emoji/' ) ) { unset( $urls[ $key ] ); }
            }

            return $urls;
        }
        add_filter( 'wp_resource_hints', 'scgolfpanel_remove_emoji_dns_prefetch', 10, 2 );

    // FRONT-END DASHICONS — KEPT WHENEVER THE ADMIN BAR IS SHOWING; ALL SITE ICONS ARE FONT AWESOME
        function scgolfpanel_remove_frontend_dashicons() {
            if ( ! is_admin_bar_showing() ) { wp_deregister_style( 'dashicons' ); }
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_remove_frontend_dashicons' );

    // BLOCK EDITOR — DISABLED; EVERY PAGE IS BUILT IN BRICKS
        add_filter( 'use_block_editor_for_post_type', '__return_false' );
        add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );

        function scgolfpanel_dequeue_block_library_styles() {
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );
            wp_dequeue_style( 'classic-theme-styles' );
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_dequeue_block_library_styles', 20 );

    // GLOBAL STYLES — STRIPPED FROM RENDERED HEAD; HOOK REMOVAL FAILS BECAUSE BRICKS PRINTS IT OUTSIDE THE ENQUEUE HOOKS
        function scgolfpanel_strip_global_styles_start() {
            ob_start();
        }
        add_action( 'wp_head', 'scgolfpanel_strip_global_styles_start', -9999 );

        function scgolfpanel_strip_global_styles_end() {
            $head = ob_get_clean();

            if ( false === $head ) { return; }

            $stripped = preg_replace( '#<style id=[\'"]global-styles-inline-css[\'"][^>]*>.*?</style>#s', '', $head );

            echo null === $stripped ? $head : $stripped;
        }
        add_action( 'wp_head', 'scgolfpanel_strip_global_styles_end', 9999 );

    // NATIVE COMMENTS — DISABLED SITE-WIDE; FLUENT COMMUNITY OWNS DISCUSSION ON ITS OWN TABLES
        add_filter( 'comments_open', '__return_false', 20 );
        add_filter( 'pings_open', '__return_false', 20 );

        function scgolfpanel_remove_comment_meta_boxes() {
            foreach ( array( 'post', 'page' ) as $screen ) {
                remove_meta_box( 'commentsdiv', $screen, 'normal' );
                remove_meta_box( 'commentstatusdiv', $screen, 'normal' );
            }
        }
        add_action( 'admin_init', 'scgolfpanel_remove_comment_meta_boxes' );

        function scgolfpanel_remove_comments_admin_menu() {
            remove_menu_page( 'edit-comments.php' );
        }
        add_action( 'admin_menu', 'scgolfpanel_remove_comments_admin_menu' );

        function scgolfpanel_remove_comments_admin_bar( $wp_admin_bar ) {
            $wp_admin_bar->remove_node( 'comments' );
        }
        add_action( 'admin_bar_menu', 'scgolfpanel_remove_comments_admin_bar', 999 );