<?php 
    /**
     * footer.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2026.01.01)
     * @copyright 2026 (2026.01.01)
    **/
?>

<!-- FOOTER -->
<?php 
    do_action('bricks_before_footer');
    do_action('render_footer');
    do_action('bricks_after_footer');
    do_action('bricks_after_site_wrapper');
?>

<!-- FOOTER-SCRIPTS -->
<div id="footer-scripts">
    
    <!-- WP-FOOTER -->
    <?php wp_footer(); ?>

    <!-- FRAMEWORK -->
    <?php include(get_stylesheet_directory().'/php/scripts/external/bootstrap/footer.php'); ?>

    <!-- THEME -->
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri();?>/script.min.js"></script>
    
</div>

<!-- CLOSING -->
<?php 
    echo '</body>';
    echo '</html>';
?>