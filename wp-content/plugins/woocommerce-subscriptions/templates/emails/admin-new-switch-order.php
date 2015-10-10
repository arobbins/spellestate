<?php
/**
 * Admin new switch order email
 *
 * @author	Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php if ( $count = count( $subscriptions ) > 1 ) : ?>
	<p><?php printf( esc_html__( 'Customer %s has switched %d of their subscriptions. The details of their new subscriptions are as follows:', 'woocommerce-subscriptions' ), esc_html( $order->billing_first_name ) . ' ' . esc_html( $order->billing_last_name ), esc_html( $count ) ); ?></p>
<?php else : ?>
	<p><?php printf( esc_html__( 'Customer %s has switched their subscription. The details of their new subscription are as follows:', 'woocommerce-subscriptions' ), esc_html( $order->billing_first_name ) . ' ' . esc_html( $order->billing_last_name ) ); ?></p>
<?php endif; ?>

<h2><?php printf( esc_html__( 'Switch Order Details', 'woocommerce-subscriptions' ), '<a href="' . esc_url( wcs_get_edit_post_link( $order->id ) ) . '">' . esc_html( $order->get_order_number() ) . '</a>' ); ?></h2>
<p><?php printf( esc_html__( 'Order %s', 'woocommerce-subscriptions' ), '<a href="' . esc_url( wcs_get_edit_post_link( $order->id ) ) . '">' . esc_html( $order->get_order_number() ) .'</a>' ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false ); ?>

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
					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
					<td style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
				</tr><?php
			}
		}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false ); ?>
<?php do_action( 'woocommerce_email_order_meta', $order, true, false ); ?>

<h2><?php esc_html_e( 'New Subscription Details', 'woocommerce-subscriptions' ); ?></h2>

<?php foreach ( $subscriptions as $subscription ) : ?>
	<?php do_action( 'woocommerce_email_before_subscription_table', $subscription , true, false ); ?>
	<p><?php printf( esc_html__( 'Subscription %s', 'woocommerce-subscriptions' ), '<a href="' . esc_url( wcs_get_edit_post_link( $subscription->id ) ) . '">' . esc_html( $subscription->get_order_number() ) .'</a>' ); ?></p>

	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
			<tr>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-subscriptions' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-subscriptions' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-subscriptions' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php echo wp_kses_post( $subscription->email_order_items_table( false, true ) ); ?>
		</tbody>
		<tfoot>
			<?php
			if ( $totals = $subscription->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++; ?>
					<tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
						<td style="text-align:left; border: 1px solid #eee; <?php if ( 1 == $i ) { echo 'border-top-width: 4px;'; } ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr><?php
				}
			}
			?>
		</tfoot>
	</table>
	<?php do_action( 'woocommerce_email_after_subscription_table', $subscription , true, false ); ?>
<?php endforeach; ?>

<?php do_action( 'woocommerce_email_customer_details', $order, true, false ); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>
