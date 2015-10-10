<?php
/**
 * Customer completed subscription change email (plain text)
 *
 * @author	Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails/Plain
 * @version 1.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

echo sprintf( __( 'Hi there. You have successfully changed your subscription items on %s. Your new order and subscription details are shown below for your reference:', 'woocommerce-subscriptions' ), get_option( 'blogname' ) );

echo "\n\n****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, false, true );

echo strtoupper( sprintf( __( 'Order number: %s', 'woocommerce-subscriptions' ), $order->get_order_number() ) ) . "\n";
echo sprintf( __( 'Order date: %s', 'woocommerce-subscriptions' ), date_i18n( woocommerce_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, false, true );

echo "\n" . $order->email_order_items_table( true, false, true, '', '', true );

echo "***********\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n" . sprintf( __( 'View your order: %s', 'woocommerce-subscriptions' ), $order->get_view_order_url() ) . "\n";
echo "\n****************************************************\n\n";

foreach ( $subscriptions as $subscription ) {

	do_action( 'woocommerce_email_before_subscription_table', $subscription, false, true );

	echo strtoupper( sprintf( __( 'Subscription number: %s', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ) . "\n";

	echo "\n" . $subscription->email_order_items_table( true, false, true, '', '', true );
	echo "***********\n";

	if ( $totals = $subscription->get_order_item_totals() ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . "\t " . $total['value'] . "\n";
		}
	}
	echo "\n" . sprintf( __( 'View your subscription: %s', 'woocommerce-subscriptions' ), $subscription->get_view_order_url() ) . "\n";
	do_action( 'woocommerce_email_after_subscription_table', $subscription, false, true );
}
echo "\n***************************************************\n\n";

do_action( 'woocommerce_email_customer_details', $order, true, true );

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
