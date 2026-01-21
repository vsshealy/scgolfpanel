<?php 
    /**
     * functions.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php 

    // LOAD STYLESHEETS
        add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
        function enqueue_parent_styles() {
            wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.min.css');
        }

    // REGISTER CUSTOM SCRIPTS AND STYLES
        add_action( 'wp_enqueue_scripts', function() {
            // Enqueue your files on the canvas & frontend, not the builder panel. Otherwise custom CSS might affect builder)
            if ( ! bricks_is_builder_main() ) {
                wp_enqueue_style( 'bricks-child', get_stylesheet_uri(), ['bricks-frontend'], filemtime( get_stylesheet_directory() . '/style.css' ) );
            }
        } );

    // REGISTER CUSTOM ELEMENTS
        add_action( 'init', function() {
            $element_files = [
            __DIR__ . '/elements/title.php',
            ];
        
            foreach ( $element_files as $file ) {
            \Bricks\Elements::register_element( $file );
            }
        }, 11 );

    // ADD TEXT STRINGS TO BUILDER
        add_filter( 'bricks/builder/i18n', function( $i18n ) {
            // For element category 'custom'
            $i18n['custom'] = esc_html__( 'Custom', 'bricks' );
        
            return $i18n;
        } );

    // REGISTER MENUS
        function register_menus() {
            register_nav_menus(
                array(
                    'header-primary' => __('Header-Primary'),
                    'footer-primary' => __('Footer-Primary'),
                    'footer-secondary' => __('Footer-Secondary'),
                )
            );
        }

        add_action('init', 'register_menus');

    // CHANGE TITLE TAG
        add_filter( 'document_title_separator', 'wpse_set_document_title_separator' );
        function wpse_set_document_title_separator( $sep ) {
            return '|';
        }

    // DISABLE WPAUTOP
        add_filter('tiny_mce_before_init', function($options) {
            $options['wpautop'] = false;
            return $options;
        });

        remove_filter('the_content', 'wpautop');
        remove_filter('the_excerpt', 'wpautop');

    // ADD PAGE TITLE TO BODY TAG
        add_filter( 'bricks/body/attributes', 'add_page_title_id_to_body' );

        function add_page_title_id_to_body( $attributes ) {
            $post = get_post();

            if ( $post ) {
                $page_title_slug = sanitize_title( $post->post_title );

                $attributes['id'] = $page_title_slug;
            }

            return $attributes;
        }

?>