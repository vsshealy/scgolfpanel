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

    // LOAD PARENT THEME STYLES
        add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
        function enqueue_parent_styles() {
            wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.min.css' );
        }

    // LOAD BOOTSTRAP, CHILD THEME STYLES, AND CHILD THEME SCRIPTS
        add_action( 'wp_enqueue_scripts', function() {
            // Enqueue your files on the canvas & frontend, not the builder panel. Otherwise custom CSS might affect builder)
            if ( ! bricks_is_builder_main() ) {

                // Bootstrap CSS (loaded first so our overrides can win the cascade)
                wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css', [], '5.3.8' );

                // Child theme compiled styles (depends on Bricks frontend + Bootstrap loading first)
                wp_enqueue_style( 'bricks-child', get_stylesheet_directory_uri() . '/style.min.css', ['bricks-frontend', 'bootstrap'], filemtime( get_stylesheet_directory() . '/style.min.css' ) );

                // Bootstrap JS bundle (includes Popper, needed for dropdowns/tooltips/popovers)
                wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', [], '5.3.8', true );

                // Child theme compiled script
                wp_enqueue_script( 'scgolfpanel-script', get_stylesheet_directory_uri() . '/script.min.js', ['bootstrap'], filemtime( get_stylesheet_directory() . '/script.min.js' ), true );
            }
        } );

    // LOAD FONTS
        function preload_self_hosted_fonts() {
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

        add_action( 'wp_head', 'preload_self_hosted_fonts', 1 );

    // LOAD FONT AWESOME KIT
        function enqueue_font_awesome_kit() {
            wp_enqueue_script(
                'font-awesome-kit',
                'https://kit.fontawesome.com/ef06e0bb5d.js',
                array(),
                null,
                false // load in head, not footer
            );
        }

        add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome_kit' );

        function font_awesome_kit_crossorigin_attribute( $tag, $handle ) {
            if ( 'font-awesome-kit' === $handle ) {
                $tag = str_replace( ' src', ' crossorigin="anonymous" src', $tag );
            }
            return $tag;
        }

        add_filter( 'script_loader_tag', 'font_awesome_kit_crossorigin_attribute', 10, 2 );

    // REGISTER MENUS
        function register_menus() {
            register_nav_menus(
                array(
                    'header-primary'   => __('Header-Primary'),
                    'footer-primary'   => __('Footer-Primary'),
                    'footer-secondary' => __('Footer-Secondary'),
                )
            );
        }

        add_action('init', 'register_menus');

    // CHANGE TITLE TAG SEPARATOR
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
                $attributes['id'] = $post->post_name;
            }

            return $attributes;
        }

    // ADD PERMISSIONS TO ADD USERS
        function grant_editor_user_management() {
            // Fetch the Editor role object
            $editor = get_role('editor');

            if ($editor) {
                // Add capabilities to add and manage users
                $editor->add_cap('create_users');
                $editor->add_cap('edit_users');
                $editor->add_cap('list_users');
                $editor->add_cap('promote_users'); // Allows assigning roles to new users
            }
        }

        add_action('admin_init', 'grant_editor_user_management');

    // DISABLE SCALED IMAGES
        add_filter('big_image_size_threshold', '__return_false');

    // DISABLE WORDPRESS GENERATED IMAGE SIZES (image pipeline is handled externally via Gulp/Sharp)
        add_filter( 'wp_generate_attachment_metadata', 'disable_all_generated_sizes', 10, 2 );

        function disable_all_generated_sizes( $metadata, $attachment_id ) {
            // Check if the uploaded file is an image
            if ( wp_attachment_is_image( $attachment_id ) ) {
                // Force the 'sizes' array to be completely empty
                $metadata['sizes'] = array();
            }
            return $metadata;
        }

        add_filter( 'fallback_intermediate_image_sizes', '__return_empty_array' );
        add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

    // AP STYLE DATE & TIME FORMATTING
        // Native WP Settings > General date/time formats can't express AP style
        // (conditional month abbreviation, no ordinal suffixes, "noon"/"midnight",
        // omitted :00 on the hour) since those require a single fixed format string.
        // Use these functions anywhere a date/time needs to render on the front end.

        function scgolfpanel_ap_date( $datetime, $include_time = false ) {
            $date = scgolfpanel_normalize_datetime( $datetime );

            // AP style month abbreviations.
            // March, April, May, June, July are never abbreviated.
            $ap_months = array(
                1  => 'Jan.',
                2  => 'Feb.',
                3  => 'March',
                4  => 'April',
                5  => 'May',
                6  => 'June',
                7  => 'July',
                8  => 'Aug.',
                9  => 'Sept.',
                10 => 'Oct.',
                11 => 'Nov.',
                12 => 'Dec.',
            );

            $month = $ap_months[ (int) $date->format( 'n' ) ];
            $day   = $date->format( 'j' ); // no leading zero, no ordinal suffix
            $year  = $date->format( 'Y' );

            $formatted = "{$month} {$day}, {$year}";

            if ( $include_time ) {
                $formatted .= ', ' . scgolfpanel_ap_time( $date );
            }

            return $formatted;
        }

        function scgolfpanel_ap_time( $datetime ) {
            $date = scgolfpanel_normalize_datetime( $datetime );

            $hour     = (int) $date->format( 'g' ); // 12-hour, no leading zero
            $minute   = $date->format( 'i' );
            $meridiem = ( $date->format( 'a' ) === 'am' ) ? 'a.m.' : 'p.m.';

            // Noon and midnight special cases
            if ( $hour === 12 && $minute === '00' ) {
                return ( $meridiem === 'p.m.' ) ? 'noon' : 'midnight';
            }

            // Omit :00 for on-the-hour times
            if ( $minute === '00' ) {
                return "{$hour} {$meridiem}";
            }

            return "{$hour}:{$minute} {$meridiem}";
        }

        // Shared helper: accepts a DateTime object, a timestamp, or a date string
        function scgolfpanel_normalize_datetime( $datetime ) {
            if ( $datetime instanceof DateTime ) {
                return $datetime;
            }

            if ( is_numeric( $datetime ) ) {
                $date = new DateTime( '@' . $datetime );
                $date->setTimezone( wp_timezone() );
                return $date;
            }

            return new DateTime( $datetime, wp_timezone() );
        }

    // MEDIA LIBRARY | FILE SIZE COLUMN
        function add_media_file_size_column( $columns ) {
            $columns['file_size'] = __( 'File Size', 'scgolfpanel' );
            return $columns;
        }

        function populate_media_file_size_column( $column_name, $post_id ) {
            if ( $column_name === 'file_size' ) {
                $file_path = get_attached_file( $post_id );

                if ( $file_path && file_exists( $file_path ) ) {
                    $bytes = filesize( $file_path );
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $i     = 0;

                    while ( $bytes >= 1024 && $i < count( $units ) - 1 ) {
                        $bytes /= 1024;
                        $i++;
                    }

                    echo round( $bytes, 2 ) . ' ' . $units[$i];
                } else {
                    echo '—';
                }
            }
        }

        function make_media_file_size_column_sortable( $columns ) {
            $columns['file_size'] = 'file_size';
            return $columns;
        }

        add_filter( 'manage_media_columns',           'add_media_file_size_column' );
        add_action( 'manage_media_custom_column',     'populate_media_file_size_column', 10, 2 );
        add_filter( 'manage_upload_sortable_columns', 'make_media_file_size_column_sortable' );

    // LOAD GOOGLE ANALYTICS
        function enqueue_google_analytics() {

            // Bail out early if the current user is an Administrator or Editor.
            if ( current_user_can( 'edit_others_posts' ) ) {
                return;
            }

            $ga_measurement_id = 'G-2002CJ5PXF';
            ?>
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_measurement_id ); ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?php echo esc_js( $ga_measurement_id ); ?>');
            </script>
            <?php
        }
        
        add_action( 'wp_head', 'enqueue_google_analytics' );

?>