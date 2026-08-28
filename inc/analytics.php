<?php
/**
 * inc/analytics.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // TRACKING CONDITIONS — SITE KIT OWNS ANALYTICS ONCE CONNECTED
        function scgolfpanel_ga_should_track() {
            if ( is_admin() || is_customize_preview() || is_preview() ) { return false; }
            if ( current_user_can( 'edit_others_posts' ) ) { return false; }

            $site_kit = (array) get_option( 'googlesitekit_active_modules', array() );
            if ( in_array( 'analytics-4', $site_kit, true ) ) { return false; }

            return (bool) apply_filters( 'scgolfpanel_ga_should_track', true );
        }

    // DEFERRED LOADER OUTPUT
        function scgolfpanel_print_google_analytics() {
            $measurement_id = apply_filters( 'scgolfpanel_ga_measurement_id', 'G-2002CJ5PXF' );

            if ( ! $measurement_id || ! scgolfpanel_ga_should_track() ) { return; }

            $id = wp_json_encode( $measurement_id );

            wp_print_inline_script_tag(
                <<<JS
                (function () {
                    'use strict';

                    var id = {$id};
                    var events = ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'];
                    var options = { passive: true };
                    var loaded = false;
                    var timer;

                    function load() {
                        if (loaded) { return; }
                        loaded = true;
                        clearTimeout(timer);
                        events.forEach(function (event) { window.removeEventListener(event, load, options); });

                        if (document.querySelector('script[src^="https://www.googletagmanager.com/gtag/js"]')) { return; }

                        window.dataLayer = window.dataLayer || [];
                        if (typeof window.gtag !== 'function') {
                            window.gtag = function () { window.dataLayer.push(arguments); };
                        }
                        window.gtag('js', new Date());
                        window.gtag('config', id);

                        var script = document.createElement('script');
                        script.async = true;
                        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + id;
                        document.head.appendChild(script);
                    }

                    events.forEach(function (event) { window.addEventListener(event, load, options); });
                    timer = setTimeout(load, 3500);
                })();
                JS
            );
        }

        add_action( 'wp_head', 'scgolfpanel_print_google_analytics' );