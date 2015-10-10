<?php
/**
 * Admin new switch order email (plain text)
 *
 * @author	Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails/Plain
 * @version 1.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

if ( $count = count( $subscriptions ) > 1 ) {
	echo sprintf( __( 'Customer %s has switched %d of their subscriptions. The details of their new subscriptions are as follows:', 'woocommerce-subscriptions' ), $order->billing_first_name . ' ' . $order->billing_last_name, $count );
} else {
	echo sprintf( __( 'Customer %s has switched their subscription. The details of their new subscription are as follows:', 'woocommerce-subscriptions' ), $order->billing_first_name . ' ' . $order->billing_last_name );
}

echo "\n\n****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, true, true );

echo strtoupper( sprintf( __( 'Order number: %s', 'woocommerce-subscriptions' ), $order->get_order_number() ) ) . "\n";
echo date_i18n( _x( 'jS F Y', 'date format for order date in new switching email', 'woocommerce-subscriptions' ), strtotime( $order->order_date ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, true, true );

echo "\n" . $order->email_order_items_table( false, true, '', '', '', true );

echo "***********\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n" . sprintf( __( 'View order: %s', 'woocommerce-subscriptions' ), wcs_get_edit_post_link( $order->id ) ) . "\n";
echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, true, true );
remove_filter( 'woocommerce_order_item_meta_end', 'WC_Subscriptions_Switcher::print_switch_link', 10 );

foreach ( $subscriptions as $subscription ) {

	do_action( 'woocommerce_email_before_subscription_table', $subscription , true, true );

	echo strtoupper( sprintf( __( 'Subscription number: %s', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ) . "\n";

	echo "\n" . $subscription->email_order_items_table( false, true, '', '', '', true );
	echo "***********\n";

	if ( $totals = $subscription->get_order_item_totals() ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . "\t " . $total['value'] . "\n";
		}
	}
	echo "\n" . sprintf( __( 'View Subscription: %s', 'woocommerce-subscriptions' ), wcs_get_edit_post_link( $subscription->id ) ) . "\n";
	do_action( 'woocommerce_email_after_subscription_table', $subscription , true, true );
}

add_filter( 'woocommerce_order_item_meta_end', 'WC_Subscriptions_Switcher::print_switch_link', 10 );
echo "\n***************************************************\n\n";

do_action( 'woocommerce_email_customer_details', $order, true, true );

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
