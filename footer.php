<?php 
    /**
     * footer.php
     * @package scgolfpanel
     * @author Scott Shealy
     * @version 1.0.0 (2024.03.16)
     * @copyright 2024 (2024.03.16)
    **/
?>

        <!-- #FOOTER-MAIN -->
        <?php 
            if (! defined('ABSPATH')) {
                exit;
            }

            if (! function_exists('elementor_theme_do_location') || ! elementor_theme_do_location('footer')) {
                if (did_action('elementor/loaded') && hello_header_footer_experiment_active()) {
                    get_template_part('template-parts/dynamic-footer');
                } else {
                    get_template_part('template-parts/footer');
                }
            }
        ?>

        <!-- #FOOTER-SCRIPTS -->
        <div id="footer-scripts">
            
            <!-- WP_FOOTER -->
            <?php wp_footer(); ?>

            <!-- FRAMEWORK -->
            <?php include(get_stylesheet_directory().'/php/scripts/external/bootstrap/footer.php'); ?>

            <!-- THEME -->
            <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri();?>/script.min.js"></script>

        </div>

    </body>
</html>