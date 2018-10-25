<?php

/**
 * Plugin Name: APK Downloader Box
 * Plugin URI: https://moddroid.com
 * Description: Plugin to create a custom APK Downloader Box
 * Version: 1.0
 * Author: Tuan Dao
 * Author URI: https://tuandaoit.me
 * License: GPL2
 * Created On: 18-10-2018
 * Updated On: 19-10-2018
 */

// Define CONST
if (!defined('AK_DOWNLOADER_DIR')) {
    define('APK_DOWNLOADER_DIR', plugin_dir_path(__FILE__));
}
if (!defined('APK_DOWNLOADER_URL')) {
    define('APK_DOWNLOADER_URL', plugin_dir_url(__FILE__));
}

require_once 'includes/ez_custom_loader.php';

add_action('plugins_loaded', 'ez_custom_tools_plugin_init');

function ez_custom_tools_plugin_init() {
    add_action('admin_menu', 'ez_custom_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
    
    // set default server
    add_option('apk_downloader_url', "https://sv1.moddroid.com");
}

function ez_custom_tools_admin_menu() {
    add_menu_page('APK Downloader', 'APK Downloader', 'manage_options', 'apk-downloader-box', 'function_apk_downloader_box_page', 'dashicons-admin-tools', 4);
    add_submenu_page('apk-downloader-box', __('Options'), __('Options'), 'manage_options', 'apk-downloader-box');
}

function function_apk_downloader_box_page() {
    
    if (isset($_POST['apk_downloader_url'])) {
        update_option('apk_downloader_url', $_POST['apk_downloader_url']);
    }
    
    $loader = new EZ_Custom_Loader();
    $loader->load_assets_page_options();
    
    echo '<div class="wrap"><div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Setup APK Downloader Box
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Server URL: </label>
                                                <input class="form-control" type="text" id="apk_downloader_url" name="apk_downloader_url" value="' . get_option('apk_downloader_url') . '" required>
                                                <p class="help-block">Example: https://sv1.moddroid.com</p>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="reset" class="btn btn-default">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>';
}

add_action( 'wp_enqueue_scripts', 'apk_downloader_box_style' );

function apk_downloader_box_style() {
    wp_register_style('css_apk_downloader', APK_DOWNLOADER_URL . 'assets/style.css');
//    wp_register_script('js_apk_downloader', APK_DOWNLOADER_URL . 'assets/apk_downloader.js');
}

function apk_downloader_box_shortcode() {
    
    wp_enqueue_style('css_apk_downloader');
//    wp_enqueue_script('js_apk_downloader');
    
    wp_enqueue_script(
		'global',
		APK_DOWNLOADER_URL . 'assets/apk_downloader.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    wp_localize_script(
		'global',
		'global',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		)
	);
    
    
    $html = '<div class="container downloader__container">
            <div class="card downloader__card">
                <div class="downloader__title">
                    <h1 class="title-big">APK Downloader</h1>
                </div>
                <form class="downloader__form" method="POST" id="apk-downloader">
                    <label>
                        <input type="text" id="packname" name="packname" placeholder="Package ID or Google Play URL">
                    </label>
                    <div class="downloader__form__submit">
                        <div id="apkmsg_success" class="alert alert--success" style="display: none;"></div>
                        <div id="apkmsg_error" class="alert alert--error" style="display: none;"></div>
                        <input type="hidden" name="action" value="check_apk_downloader_url"/>
                        <input type="hidden" id="server_url" value="' . get_option('apk_downloader_url') . '/download-apk.php?url="/>
                        <button class="btn-main" type="submit">Download APK</button>
                    </div>
                </form>
                <div class="downloader__footer">
                    <p class="text-normal">
                        Please note that you can
                        donwload only free apps and games
                        using our APK downloader.
                        Moreover, all APK files are pulled
                        from the official servers of <a href="https://play.google.com">Google Play Store</a> so they are 100% safe and original
                    </p>
                </div>
            </div>
        </div>';
    
    return $html;
}
	
add_shortcode('apk_downloader_box','apk_downloader_box_shortcode');

function ja_ajax_check_apk_downloader_url() {
    
    //Form Input Values
    if (isset($_POST['packname'])) {

        $packname = $_POST['packname'];

        if (strpos($packname, 'http') !== false) {
            $parts = parse_url($packname);
            parse_str($parts['query'], $query);

            if (isset($query['id']) && !empty($query['id'])) {
                $packname = $query['id'];
            }
        }

        $package_url = "https://apkpure.com/store/apps/details?id=" . $packname;
        
        $app_url = GetApkPureFullUrlByPackname(get_page_content($package_url, false));
        
        if ($app_url) {
            $return['status'] = "1";
            $return_url = str_replace('https://apkpure.com', "", $app_url);
            $return_url .= "&packname=".$packname;
            $return_url = urlencode($return_url);
            $return['download_url'] = $return_url;
            $return['html'] = "The download is ready.";
        } else {
            $return['status'] = "0";
            $return['html'] = "File not found, please check the package name or URL.";
        }
    }
    
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_check_apk_downloader_url', 'ja_ajax_check_apk_downloader_url' );
add_action( 'wp_ajax_nopriv_check_apk_downloader_url', 'ja_ajax_check_apk_downloader_url' );

function GetApkPureFullUrlByPackname($page_content) {
    
    $apkpure_url = "https://apkpure.com";
    
    $doc = new DomDocument;
    // We need to validate our document before refering to the id
    $doc->validateOnParse = true;
    $internalErrors = libxml_use_internal_errors(true); 
    $doc->loadHtml($page_content);
    libxml_use_internal_errors($internalErrors);
    
    $xpath = new \DOMXpath($doc);
    $articles = $xpath->query('//div[@class="ny-down"]');
    
    if (count($articles) == 0) return false;
    
    $links = [];
    
    foreach($articles as $container) {
      $arr = $container->getElementsByTagName("a");
      foreach($arr as $item) {
          $href =  $item->getAttribute("href");
          $links[] = $apkpure_url . $href;
      }
    }
    
    if (count($links) > 0) {
        foreach ($links as $url) {
            if (strpos($url, 'download?from=details') !== false) {
                return $url;
            }
        }
    }
    
    return false;
}

function get_page_content($url, $body_only = true) {
    $proxy = null;

    $http["method"] = "GET";
    if ($proxy) {
        $http["proxy"] = "tcp://" . $proxy;
        $http["request_fulluri"] = true;
    }
    $options['http'] = $http;
    $context = stream_context_create($options);
    $body = @file_get_contents($url, NULL, $context);
    
    if ($body_only) {
        if (preg_match('~<body[^>]*>(.*?)</body>~si', $body, $matches)) {
            $body = $matches[1];
        }
    }
    return $body;
}

?>