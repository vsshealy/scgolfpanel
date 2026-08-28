<?php
/**
 * inc/roles.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.1.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
*/

    defined( 'ABSPATH' ) || exit;

    // CUSTOM ROLES — BASE READ ONLY; ALL FEATURE ACCESS IS CAPABILITY-DRIVEN
        function scgolfpanel_roles() {
            return array(
                'board_member'      => 'Board Member',
                'regional_director' => 'Regional Director',
                'panel_member'      => 'Panel Member',
            );
        }

    // LEGACY WORDPRESS ROLES — RETIRED; SEE scgolfpanel_remove_legacy_roles()
        function scgolfpanel_legacy_roles() {
            return array( 'subscriber', 'contributor', 'editor' );
        }

    // PERMISSION MATRIX — THE SINGLE SOURCE OF TRUTH FOR WHO CAN DO WHAT
        function scgolfpanel_capability_map() {
            $all_roles = array( 'administrator', 'board_member', 'regional_director', 'panel_member' );
            $board     = array( 'administrator', 'board_member' );
            $directors = array( 'administrator', 'regional_director' );
            $staff     = array( 'administrator', 'board_member', 'regional_director' );

            return array(
                'scgolfpanel_submit_ranking'      => $all_roles,
                'scgolfpanel_manage_rankings'     => $board,
                'scgolfpanel_submit_review'       => $all_roles,
                'scgolfpanel_manage_reviews'      => $board,
                'scgolfpanel_submit_invitation'   => $all_roles,
                'scgolfpanel_manage_invitations'  => $directors,
                'scgolfpanel_manage_courses'      => $staff,
                'scgolfpanel_submit_outing'       => $all_roles,
                'scgolfpanel_manage_outings'      => $directors,
                'scgolfpanel_manage_applications' => $board,
                'scgolfpanel_manage_members'      => $board,
                'scgolfpanel_fc_super_admin'      => $board,
            );
        }

    // ROLE AND CAPABILITY SYNC — IDEMPOTENT, WRITES ONLY ON DRIFT, REVOKES scgolfpanel_ CAPS NO LONGER IN THE MATRIX
        function scgolfpanel_sync_roles_and_capabilities() {
            foreach ( scgolfpanel_roles() as $slug => $label ) {
                if ( ! get_role( $slug ) ) { add_role( $slug, $label, array( 'read' => true ) ); }
            }

            $map = scgolfpanel_capability_map();

            foreach ( array_merge( array( 'administrator' ), array_keys( scgolfpanel_roles() ) ) as $slug ) {
                $role = get_role( $slug );

                if ( ! $role ) { continue; }

                foreach ( $map as $capability => $granted_to ) {
                    $should_have = in_array( $slug, $granted_to, true );

                    if ( $should_have && ! $role->has_cap( $capability ) ) { $role->add_cap( $capability ); }
                    if ( ! $should_have && $role->has_cap( $capability ) ) { $role->remove_cap( $capability ); }
                }

                foreach ( array_keys( (array) $role->capabilities ) as $existing ) {
                    if ( 0 === strpos( $existing, 'scgolfpanel_' ) && ! isset( $map[ $existing ] ) ) { $role->remove_cap( $existing ); }
                }
            }
        }
        add_action( 'init', 'scgolfpanel_sync_roles_and_capabilities' );

    // LEGACY ROLE REMOVAL — ADMIN ONLY, SKIPS ANY ROLE STILL OCCUPIED OR SET AS THE REGISTRATION DEFAULT
        function scgolfpanel_remove_legacy_roles() {
            $default_role = get_option( 'default_role' );

            foreach ( scgolfpanel_legacy_roles() as $slug ) {
                if ( ! get_role( $slug ) ) { continue; }

                if ( $slug === $default_role ) {
                    scgolfpanel_log_role_notice( sprintf( 'role "%s" kept — it is the registration default (Settings > General). Change it before this role can be retired.', $slug ) );
                    continue;
                }

                $occupants = get_users( array( 'role' => $slug, 'number' => 1, 'fields' => 'ID' ) );

                if ( $occupants ) {
                    scgolfpanel_log_role_notice( sprintf( 'role "%s" kept — users are still assigned to it. Reassign them and it will be removed automatically.', $slug ) );
                    continue;
                }

                remove_role( $slug );
            }
        }
        add_action( 'admin_init', 'scgolfpanel_remove_legacy_roles' );

        function scgolfpanel_log_role_notice( $message ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { error_log( 'scgolfpanel: ' . $message . ' See inc/roles.php.' ); }
        }

    // FLUENT COMMUNITY — BOARD MEMBER GETS SUPER ADMIN TREATMENT WITHOUT ANY wp-admin ACCESS
        function scgolfpanel_fc_super_admin_capability() {
            return 'scgolfpanel_fc_super_admin';
        }
        add_filter( 'fluent_community/super_admin_capability', 'scgolfpanel_fc_super_admin_capability' );