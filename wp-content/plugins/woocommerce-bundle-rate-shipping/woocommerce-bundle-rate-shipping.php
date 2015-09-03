<?php
/*
Plugin Name: Bundle Rate Shipping Module for WooCommerce
Plugin URI: http://ericnicolaas.com
Description: Adds a bundle rate shipping method to your WooCommerce store
Version: 1.3
Author: Eric Daams
Author URI: http://ericnicolaas.com
*/

// Load bundle rate shipping
function woocommerce_bundle_rate_shipping_load() {
    if ( !class_exists('enda_woocommerce_bundlerate_shipping') && class_exists('WC_Shipping_Method')) {
        require_once('bundle_rate.class.php');
    }
}
    
// Actions to run on woocommerce_init hook
add_action('woocommerce_init', 'woocommerce_bundle_rate_shipping_init'); 
function woocommerce_bundle_rate_shipping_init() {

    // Load bundle rate shipping
    woocommerce_bundle_rate_shipping_load();

    // Load plugin text domain
    load_plugin_textdomain('woocommerce-bundle-rate-shipping', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

    // Check whether we are updated to the most recent version
    $version = mktime(0,0,0,3,07,2013);
    $db_version = get_option('woocommerce_enda_bundle_rate_version');
    if ( $db_version === false || $db_version < $version ) {                
        require_once('upgrade.class.php');        

        enda_woocommerce_bundlerate_upgrade::do_upgrade($version, $db_version);
        update_option('woocommerce_enda_bundle_rate_version', $version);
    }
}

// Register shipping module
add_filter('woocommerce_shipping_methods', 'woocommerce_bundle_rate_shipping_register_method' );    
function woocommerce_bundle_rate_shipping_register_method($methods) {
    $methods[] = 'enda_woocommerce_bundlerate_shipping';
    return $methods;
}

// Load CSS and Javascript
add_action('admin_enqueue_scripts', 'woocommerce_bundle_rate_shipping_scripts');
function woocommerce_bundle_rate_shipping_scripts($hook) {
    // Only load the Javascript and CSS on the wpsc settings page
    $possible_hooks = array( 'toplevel_page_woocommerce', 'woocommerce_page_woocommerce_settings');
    if ( in_array( $hook, $possible_hooks ) ) {
        wp_enqueue_script( 'woocommerce_bundle_rate_shipping_admin_js', plugins_url('/admin.js', __FILE__), array('jquery') );
        wp_register_style( 'woocommerce_bundle_rate_shipping_admin_css',plugins_url('/admin.css', __FILE__), false, '1.1' );
        wp_enqueue_style( 'woocommerce_bundle_rate_shipping_admin_css' );
    }
}    

// Add layer
add_action('wp_ajax_get_new_layer', 'woocommerce_bundle_rate_shipping_add_layer' );
function woocommerce_bundle_rate_shipping_add_layer() {
    // Load bundle rate shipping
    woocommerce_bundle_rate_shipping_load();

    enda_woocommerce_bundlerate_shipping::display_layer();
}

// Add configuration layer
add_action('wp_ajax_get_new_configuration_layer', 'woocommerce_bundle_rate_shipping_add_configuration' );
function woocommerce_bundle_rate_shipping_add_configuration() {
    // Load bundle rate shipping
    woocommerce_bundle_rate_shipping_load();

    enda_woocommerce_bundlerate_shipping::display_configuration_layer();
}