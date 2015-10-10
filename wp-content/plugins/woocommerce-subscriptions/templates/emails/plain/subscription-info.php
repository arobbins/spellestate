<?php
/**
 * Subscription information template
 *
 * @author	Brent Shepherd / Chuck Mac
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! empty( $subscriptions ) ) {

	echo __( 'Subscription Information:', 'woocommerce-subscriptions' ) . "\n\n";
	foreach ( $subscriptions as $subscription ) {
		echo __( 'Subscription', 'woocommerce-subscriptions' ) . ': ' . $subscription->get_order_number() . "\n";
		echo __( 'View Subscription', 'woocommerce-subscriptions' ) . ': ' . ( ( $is_admin_email ) ? wcs_get_edit_post_link( $subscription->id ) : $subscription->get_view_order_url() ) . "\n";
		echo __( 'Start Date', 'woocommerce-subscriptions' ) . ': ' . date_i18n( wc_date_format(), $subscription->get_time( 'start', 'site' ) ) . "\n";
		echo __( 'End Date', 'woocommerce-subscriptions' ) . ': ' . ( ( 0 < $subscription->get_time( 'end' ) ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : __( 'When Cancelled', 'woocommerce-subscriptions' ) ) . "\n";
		echo __( 'Price', 'woocommerce-subscriptions' ) . ': ' . $subscription->get_formatted_order_total();
		echo "\n\n";
	}

	echo "\n****************************************************\n\n";
}
