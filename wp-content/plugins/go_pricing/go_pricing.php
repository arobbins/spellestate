<?php
/**
 * Plugin Name: Go - Responsive Pricing & Compare Tables
 * Plugin URI:  http://granthweb.com/go-pricing
 * Description: The New Generation Pricing Tables. If you like traditional Pricing Tables, but you would like get much more out of it, then this rodded product is a useful tool for you.
 * Version:     3.0.2
 * Author:      Granth
 * Author URI:  http://granthweb.com/
 * Text Domain: go_pricing_textdomain
 * Domain Path: /lang
 */


/* Prevent direct call */
if ( !defined( 'WPINC' ) ) die;

/* Prevent redeclaring class */
if ( class_exists( 'GW_GoPricing' ) ) wp_die ( __( 'GW_GoPricing class has been declared!', 'go_pricing_textdomain' ) );	

/* Include & init main class */
include_once( plugin_dir_path( __FILE__ ) . 'class_go_pricing.php' );
GW_GoPricing::instance();

?>