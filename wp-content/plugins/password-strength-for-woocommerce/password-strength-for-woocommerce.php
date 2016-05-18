<?php
/**
 * Plugin Name: Password Strength for WooCommerce
 * Description: Disables password strenth enforcement in WooCommerce.
 * Version: 1.0.1
 * Author: Potent Plugins
 * Author URI: http://potentplugins.com/?utm_source=password-strength-for-woocommerce&utm_medium=link&utm_campaign=wp-plugin-author-uri
 * License: GNU General Public License version 2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 */

/*
add_filter('woocommerce_min_password_strength', 'hm_wcps_min_strength');
function hm_wcps_min_strength($strength) {
	return 0;
}
*/

add_action('wp_enqueue_scripts', 'hm_wcps_enqueue_scripts');
function hm_wcps_enqueue_scripts() {
	wp_enqueue_script('hm_wcps', plugins_url('js/password-strength-wc.js', __FILE__), array('wc-password-strength-meter'), false, true);
	
}
?>