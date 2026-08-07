<?php
    /**
     * inc/enqueue.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // LOAD PARENT THEME STYLES
        function scgolfpanel_enqueue_parent_styles() {
            wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.min.css' );
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_enqueue_parent_styles' );

    // LOAD BOOTSTRAP, CHILD THEME STYLES, AND CHILD THEME SCRIPTS
        function scgolfpanel_enqueue_theme_assets() {
            // Enqueue your files on the canvas & frontend, not the builder panel. Otherwise custom CSS might affect builder)
            if ( ! bricks_is_builder_main() ) {

                // Bootstrap CSS (loaded first so our overrides can win the cascade)
                wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css', [], '5.3.8' );

                // Child theme compiled styles (depends on Bricks frontend + Bootstrap loading first)
                wp_enqueue_style( 'bricks-child', get_stylesheet_directory_uri() . '/style.min.css', ['bricks-frontend', 'bootstrap'], filemtime( get_stylesheet_directory() . '/style.min.css' ) );

                // Bootstrap JS bundle (includes Popper, needed for dropdowns/tooltips/popovers)
                wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', [], '5.3.8', true );

                // Alpine.js Collapse plugin (no dependencies of its own — must print before Alpine
                // core below, since plugins register via Alpine.plugin() before Alpine auto-starts)
                wp_enqueue_script( 'alpinejs-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js', [], '3.14.1', true );

                // Alpine.js core (depends on the Collapse plugin handle above, so WP always prints
                // Collapse first regardless of enqueue order elsewhere)
                wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js', ['alpinejs-collapse'], '3.14.1', true );

                // Child theme compiled script (depends on Bootstrap + Alpine, since app JS will
                // register Alpine.data() components and may reference Bootstrap's JS API)
                wp_enqueue_script( 'scgolfpanel-script', get_stylesheet_directory_uri() . '/script.min.js', ['bootstrap', 'alpinejs'], filemtime( get_stylesheet_directory() . '/script.min.js' ), true );
            }
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_enqueue_theme_assets' );

    // LOAD FONTS
        function scgolfpanel_preload_fonts() {
            $fonts = array(
                'assets/fonts/roboto/roboto-vf.woff2',
                'assets/fonts/roboto/roboto-vf-italic.woff2',
                'assets/fonts/merriweather/merriweather-vf.woff2',
                'assets/fonts/merriweather/merriweather-vf-italic.woff2',
            );

            foreach ( $fonts as $font_path ) {
                printf(
                    '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">' . "\n",
                    esc_url( get_stylesheet_directory_uri() . '/' . $font_path )
                );
            }
        }
        add_action( 'wp_head', 'scgolfpanel_preload_fonts', 1 );

    // LOAD FONT AWESOME KIT
        function scgolfpanel_enqueue_font_awesome_kit() {
            wp_enqueue_script(
                'font-awesome-kit',
                'https://kit.fontawesome.com/ef06e0bb5d.js',
                array(),
                null,
                false // load in head, not footer
            );
        }
        add_action( 'wp_enqueue_scripts', 'scgolfpanel_enqueue_font_awesome_kit' );

        function scgolfpanel_font_awesome_kit_crossorigin_attribute( $tag, $handle ) {
            if ( 'font-awesome-kit' === $handle ) {
                $tag = str_replace( ' src', ' crossorigin="anonymous" src', $tag );
            }
            return $tag;
        }
        add_filter( 'script_loader_tag', 'scgolfpanel_font_awesome_kit_crossorigin_attribute', 10, 2 );

?>
