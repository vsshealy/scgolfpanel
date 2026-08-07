<?php
    /**
     * inc/admin.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<?php

    // HIDE NATIVE "WEBSITE" FIELD ON ADD/EDIT USER SCREENS
        function scgolfpanel_hide_user_website_field() {
            $screen = get_current_screen();

            if ( in_array( $screen->id, array( 'user', 'profile', 'user-edit' ), true ) ) {
                ?>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var urlField = document.getElementById('url');
                    if (urlField) {
                        var row = urlField.closest('tr');
                        if (row) {
                            row.style.display = 'none';
                        }
                    }
                });
                </script>
                <?php
            }
        }
        add_action( 'admin_footer', 'scgolfpanel_hide_user_website_field' );

?>
