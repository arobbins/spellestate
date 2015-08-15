<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php

global $woocommerce;

$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
$myaccount_page_url = "";
if ( $myaccount_page_id ) {
  $myaccount_page_url = get_permalink( $myaccount_page_id );
}

$order = wc_get_order( $order_id );

?>

<?php sf_woo_help_bar(); ?>

<div class="my-account-left">

	<h4 class="lined-heading"><span><?php _e("My Account", "swiftframework"); ?></span></h4>
	<ul class="nav my-account-nav">
	  <li><a href="<?php echo esc_url($myaccount_page_url); ?>"><?php _e("Back to my account", "swiftframework"); ?></a></li>
	</ul>

</div>

<div class="my-account-right">

	<h4><?php _e( 'Order Details', 'swiftframework' ); ?></h4>
	<table class="shop_table order_details">
		<thead>
			<tr>
				<th class="product-name"><?php _e( 'Product', 'swiftframework' ); ?></th>
				<th class="product-total"><?php _e( 'Total', 'swiftframework' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $order->get_items() as $item_id => $item ) {
					wc_get_template( 'order/order-details-item.php', array(
						'order'   => $order,
						'item_id' => $item_id,
						'item'    => $item,
						'product' => apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item )
					) );
				}
			?>
			<?php do_action( 'woocommerce_order_items_table', $order ); ?>
		</tbody>
		<tfoot>
			<?php
				foreach ( $order->get_order_item_totals() as $key => $total ) {
					?>
					<tr>
						<th scope="row"><?php echo $total['label']; ?></th>
						<td><?php echo $total['value']; ?></td>
					</tr>
					<?php
				}
			?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
	
	<?php wc_get_template( 'order/order-details-customer.php', array( 'order' =>  $order ) ); ?>

</div>
