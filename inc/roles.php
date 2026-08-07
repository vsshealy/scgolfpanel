<?php
    /**
     * inc/roles.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.07.30)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // REGISTER CUSTOM ROLES
        function scgolfpanel_register_roles() {
            if ( ! get_role( 'board_member' ) ) {
                add_role( 'board_member', 'Board Member', array(
                    'read' => true,
                ) );
            }

            if ( ! get_role( 'regional_director' ) ) {
                add_role( 'regional_director', 'Regional Director', array(
                    'read' => true,
                ) );
            }

            if ( ! get_role( 'panel_member' ) ) {
                add_role( 'panel_member', 'Panel Member', array(
                    'read' => true,
                ) );
            }
        }
        add_action( 'init', 'scgolfpanel_register_roles' );

    // GRANT A CAPABILITY TO A SET OF ROLES (IDEMPOTENT — SAFE TO RUN ON EVERY LOAD)
        function scgolfpanel_grant_capability_to_roles( $capability, $roles ) {
            foreach ( $roles as $role_slug ) {
                $role = get_role( $role_slug );

                if ( $role && ! $role->has_cap( $capability ) ) {
                    $role->add_cap( $capability );
                }
            }
        }

    // REGISTER APP-LEVEL CAPABILITIES
        function scgolfpanel_register_capabilities() {

            // Course Rankings — submit: all four roles, manage: Board Member + Administrator
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_submit_ranking', array( 'administrator', 'board_member', 'regional_director', 'panel_member' ) );
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_rankings', array( 'administrator', 'board_member' ) );

            // Course Reviews — submit: all four roles, manage: Board Member + Administrator
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_submit_review', array( 'administrator', 'board_member', 'regional_director', 'panel_member' ) );
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_reviews', array( 'administrator', 'board_member' ) );

            // Course Invitations — submit: all four roles, manage: Regional Director + Administrator
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_submit_invitation', array( 'administrator', 'board_member', 'regional_director', 'panel_member' ) );
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_invitations', array( 'administrator', 'regional_director' ) );

            // Panel Outings — submit/participate: all four roles, manage: Regional Director + Administrator
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_submit_outing', array( 'administrator', 'board_member', 'regional_director', 'panel_member' ) );
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_outings', array( 'administrator', 'regional_director' ) );

            // Member Application — review/approve only: Board Member + Administrator (no submit capability; applicants aren't yet users)
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_applications', array( 'administrator', 'board_member' ) );

            // Member management / promotion chain — Board Member + Administrator (role-specific whitelist enforced in apps/shared/roles.php)
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_manage_members', array( 'administrator', 'board_member' ) );

            // Fluent Community super-admin treatment — Board Member + Administrator, without granting any wp-admin access
                scgolfpanel_grant_capability_to_roles( 'scgolfpanel_fc_super_admin', array( 'administrator', 'board_member' ) );

        }
        add_action( 'init', 'scgolfpanel_register_capabilities', 20 );

    // FLUENT COMMUNITY — TREAT BOARD MEMBER AS FC SUPER ADMIN ALONGSIDE ADMINISTRATOR
        function scgolfpanel_fc_super_admin_capability( $capability ) {
            return 'scgolfpanel_fc_super_admin';
        }
        add_filter( 'fluent_community/super_admin_capability', 'scgolfpanel_fc_super_admin_capability' );

?>
