<?php
/**
 * inc/enqueue.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // ASSET VERSION — FILEMTIME CACHE BUSTING, THEME VERSION FALLBACK
        function scgolfpanel_asset_version( $relative_path ) {
            $file = get_stylesheet_directory() . $relative_path;

            return file_exists( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
        }

    // FONT AWESOME KIT URL
        function scgolfpanel_font_awesome_kit_url() {
            return 'https://kit.fontawesome.com/ef06e0bb5d.js';
        }

    // STYLES AND SCRIPTS
        function scgolfpanel_enqueue_assets() {
            $child_uri = get_stylesheet_directory_uri();

            wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.min.css', array(), wp_get_theme( get_template() )->get( 'Version' ) );
            wp_enqueue_script( 'font-awesome-kit', scgolfpanel_font_awesome_kit_url(), array(), null, false );

            if ( function_exists( 'bricks_is_builder_main' ) && bricks_is_builder_main() ) { return; }

            wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css', array(), '5.3.8' );
            wp_enqueue_style( 'bricks-child', $child_uri . '/style.min.css', array( 'parent-style', 'bricks-frontend', 'bootstrap' ), scgolfpanel_asset_version( '/style.min.css' ) );

            $deferred = array( 'in_footer' => true, 'strategy' => 'defer' );

            wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', array(), '5.3.8', $deferred );
            wp_enqueue_script( 'alpinejs-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js', array(), '3.14.1', $deferred );
            wp_enqueue_script( 'scgolfpanel-script', $child_uri . '/script.min.js', array( 'bootstrap', 'alpinejs-collapse' ), scgolfpanel_asset_version( '/script.min.js' ), $deferred );

            // ALPINE CORE LOADS LAST BY DESIGN — start() FIRES ON MICROTASK, SO EVERY alpine:init LISTENER MUST ALREADY BE ATTACHED
            wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js', array( 'alpinejs-collapse', 'scgolfpanel-script' ), '3.14.1', $deferred );
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_enqueue_assets' );

    // FONT AWESOME CROSSORIGIN ATTRIBUTE
        function scgolfpanel_font_awesome_kit_crossorigin( $tag, $handle ) {
            if ( 'font-awesome-kit' !== $handle ) { return $tag; }

            return str_replace( ' src', ' crossorigin="anonymous" src', $tag );
        }
        add_filter( 'script_loader_tag', 'scgolfpanel_font_awesome_kit_crossorigin', 10, 2 );

    // FONT PRELOADING
        function scgolfpanel_preload_fonts( $resources ) {
            $fonts = array(
                'roboto/roboto-vf.woff2',
                'roboto/roboto-vf-italic.woff2',
                'merriweather/merriweather-vf.woff2',
                'merriweather/merriweather-vf-italic.woff2',
            );

            foreach ( $fonts as $font ) {
                $resources[] = array(
                    'href'        => get_stylesheet_directory_uri() . '/assets/fonts/' . $font,
                    'as'          => 'font',
                    'type'        => 'font/woff2',
                    'crossorigin' => 'anonymous',
                );
            }

            return $resources;
        }
        add_filter( 'wp_preload_resources', 'scgolfpanel_preload_fonts' );