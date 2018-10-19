<?php

if( ! class_exists( 'EZ_Custom_Loader' ) ) {
    
    class EZ_Custom_Loader {

        public function load_assets_page_options() {
            $this->load_assets_common_admin();
        }

        public function load_assets_common_admin() {
            
            // JS
            wp_register_script('prefix_bootstrap', APK_DOWNLOADER_URL . 'assets/admin/lib/bootstrap.min.js');
            wp_enqueue_script('prefix_bootstrap');
            wp_register_script('prefix_jquery', APK_DOWNLOADER_URL . 'assets/admin/lib/jquery-3.3.1.min.js');
            wp_enqueue_script('prefix_jquery');

            // CSS
            wp_register_style('prefix_bootstrap', APK_DOWNLOADER_URL . 'assets/admin/css/bootstrap.min.css');
            wp_enqueue_style('prefix_bootstrap');
            wp_enqueue_style('my-styles', APK_DOWNLOADER_URL . 'assets/admin/css/styles.css' );
        }
    }
}

?>