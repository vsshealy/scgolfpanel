<?php
    /**
     * inc/cleanup.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // CLEAN UP <HEAD> — REMOVE UNUSED WORDPRESS DEFAULT TAGS
        remove_action( 'wp_head', 'rsd_link' );                  // Really Simple Discovery — used by Windows Live Writer, effectively obsolete
        remove_action( 'wp_head', 'wlwmanifest_link' );           // Windows Live Writer manifest — same, obsolete
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );       // Shortlink tag — only useful if you're actually using WP's short URLs
        remove_action( 'wp_head', 'rest_output_link_wp_head' );   // REST API discovery link — safe to remove from <head> without disabling the REST API itself
        remove_action( 'wp_head', 'wp_generator' );               // WordPress version number meta tag

    // REMOVE EMOJI SCRIPTS & STYLES
        // Core loads a detection script + inline styles on every page load to
        // polyfill emoji support in old browsers. Not needed on any modern
        // target browser and not worth the extra request/inline CSS.
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

        function scgolfpanel_disable_emojis_tinymce( $plugins ) {
            if ( is_array( $plugins ) ) {
                return array_diff( $plugins, array( 'wpemoji' ) );
            }
            return array();
        }
        add_filter( 'tiny_mce_plugins', 'scgolfpanel_disable_emojis_tinymce' );

        // Also drop the emoji CDN's dns-prefetch resource hint, left behind
        // even after the script/style removal above.
        function scgolfpanel_remove_emoji_dns_prefetch( $urls, $relation_type ) {
            if ( 'dns-prefetch' === $relation_type ) {
                $emoji_svg_url = 'https://s.w.org/images/core/emoji/';

                foreach ( $urls as $key => $url ) {
                    if ( is_string( $url ) && strpos( $url, $emoji_svg_url ) !== false ) {
                        unset( $urls[ $key ] );
                    }
                }
            }
            return $urls;
        }
        add_filter( 'wp_resource_hints', 'scgolfpanel_remove_emoji_dns_prefetch', 10, 2 );

    // REMOVE FRONT-END DASHICONS
        // Dashicons is a core dependency for the admin bar/admin UI, not
        // anything front-end here (all icons are Font Awesome). Only
        // deregister it when the admin bar isn't showing, so logged-in
        // Administrators still get Dashicons for the front-end admin bar.
        function scgolfpanel_remove_frontend_dashicons() {
            if ( ! is_admin_bar_showing() ) {
                wp_deregister_style( 'dashicons' );
            }
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_remove_frontend_dashicons' );

    // DISABLE GUTENBERG BLOCK EDITOR — EVERYTHING IS BUILT IN BRICKS
        add_filter( 'use_block_editor_for_post_type', '__return_false' );

    // REMOVE BLOCK-EDITOR / GLOBAL-STYLES CSS FROM THE FRONT END
        // These are core WordPress defaults tied to Gutenberg block styling
        // (button styles, gradients, spacing scale, global-styles custom
        // properties) that load on every page regardless of builder in use.
        // None of it applies here since every page is built in Bricks.
        function scgolfpanel_dequeue_block_library_styles() {
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );
            wp_dequeue_style( 'classic-theme-styles' );
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_dequeue_block_library_styles', 20 );

        // Two standard removal methods — unhooking wp_enqueue_global_styles,
        // and dequeuing the 'global-styles' handle at a late priority —
        // both failed to remove this identically across two attempts. That
        // points to Bricks likely calling wp_enqueue_global_styles()
        // directly in its own template markup rather than through the
        // wp_enqueue_scripts/wp_footer hooks, meaning no hook removal can
        // intercept it. Stripping it directly from the rendered <head>
        // output instead works regardless of how it got there.
        function scgolfpanel_strip_global_styles_start() {
            ob_start();
        }
        add_action( 'wp_head', 'scgolfpanel_strip_global_styles_start', -9999 );

        function scgolfpanel_strip_global_styles_end() {
            $head_output = ob_get_clean();
            $head_output = preg_replace(
                '#<style id=[\'"]global-styles-inline-css[\'"][^>]*>.*?</style>#s',
                '',
                $head_output
            );
            echo $head_output;
        }
        add_action( 'wp_head', 'scgolfpanel_strip_global_styles_end', 9999 );

    // REMOVE THE "AUTO SIZES" LAZY-LOAD CONTAIN CSS (WP 6.7+)
        // Another core default tied to block-image handling we're not using.
        add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );

    // REMOVE RSS FEED + OEMBED DISCOVERY LINKS FROM <HEAD>
        // No blog/native comments on this site — Fluent Community handles
        // discussion/community features independently of core WP comments.
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // DISABLE NATIVE WORDPRESS COMMENTS SITE-WIDE
        // Safe to remove entirely — Fluent Community runs its own
        // discussion system with its own database tables, independent
        // of core WP comments. Delete this block if that ever changes.
        add_filter( 'comments_open', '__return_false', 20, 2 );
        add_filter( 'pings_open', '__return_false', 20, 2 );

        function scgolfpanel_remove_comment_meta_boxes() {
            remove_meta_box( 'commentsdiv', 'page', 'normal' );
            remove_meta_box( 'commentsdiv', 'post', 'normal' );
            remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
            remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
        }
        add_action( 'admin_init', 'scgolfpanel_remove_comment_meta_boxes' );

        function scgolfpanel_remove_comments_admin_menu() {
            remove_menu_page( 'edit-comments.php' );
        }
        add_action( 'admin_menu', 'scgolfpanel_remove_comments_admin_menu' );

        function scgolfpanel_remove_comments_admin_bar() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu( 'comments' );
        }
        add_action( 'wp_before_admin_bar_render', 'scgolfpanel_remove_comments_admin_bar' );

?>
