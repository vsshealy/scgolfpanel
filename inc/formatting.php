<?php
    /**
     * inc/formatting.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // CHANGE TITLE TAG SEPARATOR
        function scgolfpanel_set_document_title_separator( $sep ) {
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

    // ADD PAGE TITLE TO BODY TAG
        function scgolfpanel_add_page_title_id_to_body( $attributes ) {
            $post = get_post();

            if ( $post ) {
                $attributes['id'] = $post->post_name;
            }

            return $attributes;
        }
        add_filter( 'bricks/body/attributes', 'scgolfpanel_add_page_title_id_to_body' );

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

?>
