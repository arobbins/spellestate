<?php
/**
 * Admin new renewal order email
 *
 * @author	Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( esc_html__( 'You have received a subscription renewal order from %s. Their order is as follows:', 'woocommerce-subscriptions' ), esc_html( $order->billing_first_name ) . ' ' . esc_html( $order->billing_last_name ) ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false ); ?>

<h2><?php printf( esc_html__( 'Order: %s', 'woocommerce-subscriptions' ), esc_html( $order->get_order_number() ) ); ?> (<?php printf( '<time datetime="%s">%s</time>', esc_attr( date_i18n( 'c', strtotime( $order->order_date ) ) ), esc_html( date_i18n( woocommerce_date_format(), strtotime( $order->order_date ) ) ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-subscriptions' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-subscriptions' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-subscriptions' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo wp_kses_post( $order->email_order_items_table( false, true ) ); ?>
	</tbody>
	<tfoot>
		<?php
		if ( $totals = $order->get_order_item_totals() ) {
			$i = 0;
			foreach ( $totals as $total ) {
				$i++;
				?><tr>
					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo esc_html( $total['label'] ); ?></th>
					<td style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr><?php
			}
		}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, false ); ?>

<h2><?php esc_html_e( 'Customer details', 'woocommerce-subscriptions' ); ?></h2>

<?php if ( $order->billing_email ) : ?>
	<p><strong><?php esc_html_e( 'Email:', 'woocommerce-subscriptions' ); ?></strong> <?php echo esc_html( $order->billing_email ); ?></p>
<?php endif; ?>
<?php if ( $order->billing_phone ) : ?>
	<p><strong><?php esc_html_e( 'Tel:', 'woocommerce-subscriptions' ); ?></strong> <?php echo esc_html( $order->billing_phone ); ?></p>
<?php endif; ?>

<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>
