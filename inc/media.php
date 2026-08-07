<?php
    /**
     * inc/media.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.1.0 (2026.07.31)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // DISABLE SCALED IMAGES (WP 5.3+ auto-generated 1536/2048 "scaled" sizes)
        add_filter( 'big_image_size_threshold', '__return_false' );

    // STRIP DEFAULT WP SIZES, KEEP OUR CUSTOM SCGOLFPANEL SIZES
    // (replaces the old "disable everything" approach — that blocked our
    // registered sizes below from ever generating)
        function scgolfpanel_remove_default_sizes( $sizes ) {
            unset(
                $sizes['thumbnail'],
                $sizes['medium'],
                $sizes['medium_large'],
                $sizes['large'],
                $sizes['1536x1536'],
                $sizes['2048x2048']
            );
            return $sizes;
        }
        add_filter( 'intermediate_image_sizes_advanced', 'scgolfpanel_remove_default_sizes' );

    // REGISTER SCGOLFPANEL IMAGE SIZES
    // Master upload dimensions (exact, required at upload — these ARE the
    // "full" size, no separate registration needed for them):
    //   Hero Primary     2520x1080  (21:9)
    //   Hero Secondary   2520x360   (7:1)
    //   Image Gallery    1920x1080  (16:9)
    //   Featured Image   1200x800   (3:2) — also source for social share crops
        function scgolfpanel_register_image_sizes() {

            // Hero Primary — master 2520x1080 is "full"
            add_image_size( 'scgolfpanel-hero-primary-sm', 840, 360, true );
            add_image_size( 'scgolfpanel-hero-primary-md', 1680, 720, true );

            // Hero Secondary — master 2520x360 is "full"
            add_image_size( 'scgolfpanel-hero-secondary-sm', 840, 120, true );
            add_image_size( 'scgolfpanel-hero-secondary-md', 1680, 240, true );

            // Image Gallery — master 1920x1080 is "full"
            add_image_size( 'scgolfpanel-gallery-sm', 640, 360, true );
            add_image_size( 'scgolfpanel-gallery-md', 1280, 720, true );

            // Featured Image — master 1200x800 is "full"
            add_image_size( 'scgolfpanel-featured-xs', 300, 200, true );
            add_image_size( 'scgolfpanel-featured-sm', 450, 300, true );
            add_image_size( 'scgolfpanel-featured-md', 600, 400, true );
            add_image_size( 'scgolfpanel-featured-lg', 900, 600, true );
        }
        add_action( 'after_setup_theme', 'scgolfpanel_register_image_sizes' );

        function scgolfpanel_custom_size_names( $sizes ) {
            return array_merge( $sizes, array(
                'scgolfpanel-hero-primary-sm'   => 'Hero Primary — Small (840×360)',
                'scgolfpanel-hero-primary-md'   => 'Hero Primary — Medium (1680×720)',
                'scgolfpanel-hero-secondary-sm' => 'Hero Secondary — Small (840×120)',
                'scgolfpanel-hero-secondary-md' => 'Hero Secondary — Medium (1680×240)',
                'scgolfpanel-gallery-sm'        => 'Gallery — Small (640×360)',
                'scgolfpanel-gallery-md'        => 'Gallery — Medium (1280×720)',
                'scgolfpanel-featured-xs'       => 'Featured — XS (300×200)',
                'scgolfpanel-featured-sm'       => 'Featured — Small (450×300)',
                'scgolfpanel-featured-md'       => 'Featured — Medium (600×400)',
                'scgolfpanel-featured-lg'       => 'Featured — Large (900×600)',
            ) );
        }
        add_filter( 'image_size_names_choose', 'scgolfpanel_custom_size_names' );

    // MEDIA LIBRARY | FILE SIZE COLUMN
        function scgolfpanel_add_media_file_size_column( $columns ) {
            $columns['file_size'] = __( 'File Size', 'scgolfpanel' );
            return $columns;
        }
        add_filter( 'manage_media_columns', 'scgolfpanel_add_media_file_size_column' );

        function scgolfpanel_populate_media_file_size_column( $column_name, $post_id ) {
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
        add_action( 'manage_media_custom_column', 'scgolfpanel_populate_media_file_size_column', 10, 2 );

        function scgolfpanel_make_media_file_size_column_sortable( $columns ) {
            $columns['file_size'] = 'file_size';
            return $columns;
        }
        add_filter( 'manage_upload_sortable_columns', 'scgolfpanel_make_media_file_size_column_sortable' );

?>