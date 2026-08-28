<?php
/**
 * inc/formatting.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // TITLE TAG SEPARATOR
        function scgolfpanel_set_document_title_separator() {
            return '|';
        }
        add_filter( 'document_title_separator', 'scgolfpanel_set_document_title_separator' );

    // DISABLE WPAUTOP
        function scgolfpanel_disable_wpautop_tinymce( $options ) {
            $options['wpautop'] = false;

            return $options;
        }
        add_filter( 'tiny_mce_before_init', 'scgolfpanel_disable_wpautop_tinymce' );

        remove_filter( 'the_content', 'wpautop' );
        remove_filter( 'the_excerpt', 'wpautop' );

    // BODY ID FROM POST SLUG
        function scgolfpanel_add_page_title_id_to_body( $attributes ) {
            if ( ! is_singular() ) { return $attributes; }

            $post = get_post();

            if ( $post ) {
                $attributes['id'] = $post->post_name;
            }

            return $attributes;
        }
        add_filter( 'bricks/body/attributes', 'scgolfpanel_add_page_title_id_to_body' );

    // DATETIME NORMALIZATION — ACCEPTS DateTimeInterface, UNIX TIMESTAMP, OR DATE STRING; NULL WHEN UNPARSEABLE
        function scgolfpanel_normalize_datetime( $datetime ) {
            if ( $datetime instanceof DateTimeInterface ) { return $datetime; }

            if ( is_int( $datetime ) || ( is_string( $datetime ) && preg_match( '/^\d{9,11}$/', $datetime ) ) ) {
                return ( new DateTime( '@' . $datetime ) )->setTimezone( wp_timezone() );
            }

            if ( ! is_string( $datetime ) || '' === trim( $datetime ) || 0 === strpos( $datetime, '0000-00-00' ) ) { return null; }

            try {
                return new DateTime( $datetime, wp_timezone() );
            } catch ( Exception $e ) {
                return null;
            }
        }

    // AP STYLE DATE — MARCH THROUGH JULY NEVER ABBREVIATED, NO ORDINAL SUFFIX
        function scgolfpanel_ap_date( $datetime, $include_time = false ) {
            $date = scgolfpanel_normalize_datetime( $datetime );

            if ( ! $date ) { return ''; }

            $months = array(
                1  => 'Jan.',  2  => 'Feb.',  3  => 'March', 4  => 'April',
                5  => 'May',   6  => 'June',  7  => 'July',  8  => 'Aug.',
                9  => 'Sept.', 10 => 'Oct.',  11 => 'Nov.',  12 => 'Dec.',
            );

            $formatted = sprintf( '%s %s, %s', $months[ (int) $date->format( 'n' ) ], $date->format( 'j' ), $date->format( 'Y' ) );

            return $include_time ? $formatted . ', ' . scgolfpanel_ap_time( $date ) : $formatted;
        }

    // AP STYLE TIME — NOON AND MIDNIGHT NAMED, :00 OMITTED ON THE HOUR
        function scgolfpanel_ap_time( $datetime ) {
            $date = scgolfpanel_normalize_datetime( $datetime );

            if ( ! $date ) { return ''; }

            $hour     = (int) $date->format( 'g' );
            $minute   = $date->format( 'i' );
            $meridiem = 'am' === $date->format( 'a' ) ? 'a.m.' : 'p.m.';

            if ( 12 === $hour && '00' === $minute ) { return 'p.m.' === $meridiem ? 'noon' : 'midnight'; }
            if ( '00' === $minute ) { return "{$hour} {$meridiem}"; }

            return "{$hour}:{$minute} {$meridiem}";
        }