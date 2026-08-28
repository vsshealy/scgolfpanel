<?php
/**
 * inc/media.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // DISABLE SCALED IMAGES
        add_filter( 'big_image_size_threshold', '__return_false' );

    // IMAGE FAMILIES — MASTERS ARE UPLOADED, NEVER REGISTERED; SEE claude/imagery-standard.md §1
        function scgolfpanel_image_families() {
            static $families = null;

            if ( null !== $families ) { return $families; }

            $families = array(
                'hero-primary' => array(
                    'ratio' => 3780 / 1620,
                    'sizes' => array(
                        'scgolfpanel-hero-primary-lg' => array( 2520, 1080, 'Hero Primary — Large (2520×1080)' ),
                        'scgolfpanel-hero-primary-md' => array( 1680,  720, 'Hero Primary — Medium (1680×720)' ),
                        'scgolfpanel-hero-primary-sm' => array(  840,  360, 'Hero Primary — Small (840×360)' ),
                    ),
                ),
                'hero-secondary' => array(
                    'ratio' => 3780 / 540,
                    'sizes' => array(
                        'scgolfpanel-hero-secondary-lg' => array( 2520, 360, 'Hero Secondary — Large (2520×360)' ),
                        'scgolfpanel-hero-secondary-md' => array( 1680, 240, 'Hero Secondary — Medium (1680×240)' ),
                        'scgolfpanel-hero-secondary-sm' => array(  840, 120, 'Hero Secondary — Small (840×120)' ),
                    ),
                ),
                'gallery' => array(
                    'ratio' => 2880 / 1620,
                    'sizes' => array(
                        'scgolfpanel-gallery-lg' => array( 1920, 1080, 'Gallery — Large (1920×1080)' ),
                        'scgolfpanel-gallery-md' => array( 1280,  720, 'Gallery — Medium (1280×720)' ),
                        'scgolfpanel-gallery-sm' => array(  640,  360, 'Gallery — Small (640×360)' ),
                    ),
                ),
                'featured' => array(
                    'ratio' => 1200 / 800,
                    'sizes' => array(
                        'scgolfpanel-featured-lg' => array( 900, 600, 'Featured — Large (900×600)' ),
                        'scgolfpanel-featured-md' => array( 600, 400, 'Featured — Medium (600×400)' ),
                        'scgolfpanel-featured-sm' => array( 450, 300, 'Featured — Small (450×300)' ),
                        'scgolfpanel-featured-xs' => array( 300, 200, 'Featured — XS (300×200)' ),
                    ),
                ),
                'square' => array(
                    'ratio' => 1080 / 1080,
                    'sizes' => array(
                        'scgolfpanel-square-lg' => array( 600, 600, 'Square — Large (600×600)' ),
                        'scgolfpanel-square-md' => array( 300, 300, 'Square — Medium (300×300)' ),
                        'scgolfpanel-square-sm' => array( 180, 180, 'Square — Small (180×180)' ),
                    ),
                ),
            );

            return $families;
        }

    // REGISTER IMAGE SIZES
        function scgolfpanel_register_image_sizes() {
            foreach ( scgolfpanel_image_families() as $family ) {
                foreach ( $family['sizes'] as $name => $spec ) {
                    add_image_size( $name, $spec[0], $spec[1], true );
                }
            }
        }
        add_action( 'after_setup_theme', 'scgolfpanel_register_image_sizes' );

    // ADMIN SIZE PICKER LABELS
        function scgolfpanel_custom_size_names( $sizes ) {
            $names = array();

            foreach ( scgolfpanel_image_families() as $family ) {
                foreach ( $family['sizes'] as $name => $spec ) {
                    $names[ $name ] = $spec[2];
                }
            }

            return array_merge( $sizes, $names );
        }
        add_filter( 'image_size_names_choose', 'scgolfpanel_custom_size_names' );

    // SIZE GENERATION — STRIP WP DEFAULTS EXCEPT thumbnail, THEN SCOPE TO FAMILY BY ASPECT RATIO WITHIN 2%
        function scgolfpanel_filter_generated_sizes( $sizes, $image_meta = array() ) {
            unset(
                $sizes['medium'],
                $sizes['medium_large'],
                $sizes['large'],
                $sizes['1536x1536'],
                $sizes['2048x2048']
            );

            $width  = isset( $image_meta['width'] )  ? (int) $image_meta['width']  : 0;
            $height = isset( $image_meta['height'] ) ? (int) $image_meta['height'] : 0;

            if ( ! $width || ! $height ) { return $sizes; }

            $ratio = $width / $height;

            foreach ( scgolfpanel_image_families() as $family ) {
                if ( abs( $ratio - $family['ratio'] ) <= $family['ratio'] * 0.02 ) {
                    return array_intersect_key( $sizes, $family['sizes'] + array( 'thumbnail' => true ) );
                }
            }

            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf( 'scgolfpanel: upload at %1$dx%2$d (ratio %3$.3f) matches no image family — every registered size will be generated from it. See inc/media.php.', $width, $height, $ratio ) );
            }

            return $sizes;
        }
        add_filter( 'intermediate_image_sizes_advanced', 'scgolfpanel_filter_generated_sizes', 10, 2 );

    // FILE SIZE META — WRITTEN ON UPLOAD, BACKFILLED LAZILY BY THE COLUMN
        function scgolfpanel_store_attachment_file_size( $post_id ) {
            $file_path = get_attached_file( $post_id );

            if ( ! $file_path || ! file_exists( $file_path ) ) { return 0; }

            $bytes = (int) filesize( $file_path );
            update_post_meta( $post_id, '_scgolfpanel_file_size', $bytes );

            return $bytes;
        }
        add_action( 'add_attachment', 'scgolfpanel_store_attachment_file_size' );

    // MEDIA LIBRARY | FILE SIZE COLUMN
        function scgolfpanel_add_media_file_size_column( $columns ) {
            $columns['file_size'] = __( 'File Size', 'scgolfpanel' );

            return $columns;
        }
        add_filter( 'manage_media_columns', 'scgolfpanel_add_media_file_size_column' );

        function scgolfpanel_populate_media_file_size_column( $column_name, $post_id ) {
            if ( 'file_size' !== $column_name ) { return; }

            $bytes = get_post_meta( $post_id, '_scgolfpanel_file_size', true );

            if ( '' === $bytes ) { $bytes = scgolfpanel_store_attachment_file_size( $post_id ); }

            echo $bytes ? esc_html( size_format( (int) $bytes, 2 ) ) : '—';
        }
        add_action( 'manage_media_custom_column', 'scgolfpanel_populate_media_file_size_column', 10, 2 );

        function scgolfpanel_make_media_file_size_column_sortable( $columns ) {
            $columns['file_size'] = 'file_size';

            return $columns;
        }
        add_filter( 'manage_upload_sortable_columns', 'scgolfpanel_make_media_file_size_column_sortable' );

        function scgolfpanel_sort_media_by_file_size( $query ) {
            if ( ! is_admin() || ! $query->is_main_query() || 'file_size' !== $query->get( 'orderby' ) ) { return; }

            $query->set( 'meta_query', array(
                'relation'   => 'OR',
                'size_bytes' => array( 'key' => '_scgolfpanel_file_size', 'type' => 'NUMERIC', 'compare' => 'EXISTS' ),
                array( 'key' => '_scgolfpanel_file_size', 'compare' => 'NOT EXISTS' ),
            ) );

            $query->set( 'orderby', array( 'size_bytes' => 'ASC' === strtoupper( (string) $query->get( 'order' ) ) ? 'ASC' : 'DESC' ) );
        }
        add_action( 'pre_get_posts', 'scgolfpanel_sort_media_by_file_size' );