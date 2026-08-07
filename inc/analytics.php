<?php
    /**
     * inc/analytics.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // LOAD GOOGLE ANALYTICS — DELAYED UNTIL USER INTERACTION
        function scgolfpanel_enqueue_google_analytics() {

            // Bail out early if the current user is an Administrator or Editor.
            if ( current_user_can( 'edit_others_posts' ) ) {
                return;
            }

            $ga_measurement_id = 'G-2002CJ5PXF';
            ?>
            <script>
            (function() {
                var gaLoaded = false;
                var gaId = '<?php echo esc_js( $ga_measurement_id ); ?>';

                function loadGoogleAnalytics() {
                    if ( gaLoaded ) return;
                    gaLoaded = true;

                    var script = document.createElement('script');
                    script.async = true;
                    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + gaId;
                    document.head.appendChild(script);

                    window.dataLayer = window.dataLayer || [];
                    function gtag(){ dataLayer.push(arguments); }
                    window.gtag = gtag;
                    gtag('js', new Date());
                    gtag('config', gaId);

                    // Clean up listeners once loaded
                    ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(evt) {
                        window.removeEventListener(evt, loadGoogleAnalytics, { passive: true });
                    });
                    clearTimeout(gaTimeout);
                }

                // Load on first real interaction
                ['scroll', 'mousemove', 'touchstart', 'click', 'keydown'].forEach(function(evt) {
                    window.addEventListener(evt, loadGoogleAnalytics, { passive: true });
                });

                // Fallback: load anyway after 3.5s even with no interaction,
                // so short/passive visits still get counted.
                var gaTimeout = setTimeout(loadGoogleAnalytics, 3500);
            })();
            </script>
            <?php
        }
        add_action( 'wp_head', 'scgolfpanel_enqueue_google_analytics' );

?>
