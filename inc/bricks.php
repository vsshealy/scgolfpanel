<?php
/**
 * inc/bricks.php
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.08.28)
 * @copyright 2026 (2026.01.01)
**/

// REGISTER BRICKS
    set_transient( 'bricks_license_status', 'active' );
    update_option( '_transient_timeout_bricks_license_status', time() + 3 * 60 * 60 );
    update_option( 'bricks_license_key', 'ZIMEEK00-0000-0000-0000-51A9E6E1A930' );