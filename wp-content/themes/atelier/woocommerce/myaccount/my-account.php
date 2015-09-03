<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php

global $woocommerce, $yith_wcwl;

if (function_exists('wc_print_notices')) {wc_print_notices();} else {$woocommerce->show_messages();}; ?>

<p class="myaccount_user">
	<?php
	printf(
		__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'swiftframework' ) . ' ',
		$current_user->display_name,
		wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) )
	);

	printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'swiftframework' ),
		wc_customer_edit_account_url()
	);
	?>
</p>

<?php sf_woo_help_bar(); ?>

<div class="my-account-left">

	<h4 class="lined-heading"><span><?php _e("My Account", "swiftframework"); ?></span></h4>
	<ul class="nav my-account-nav">
	  <li class="active"><a href="#my-orders" data-toggle="tab"><?php _e("My Orders", "swiftframework"); ?></a></li>
	  <li><a href="#my-downloads" data-toggle="tab"><?php _e("My Downloads", "swiftframework"); ?></a></li>
	  <?php if ( class_exists( 'YITH_WCWL_UI' ) ) { ?>
	  <li><a href="<?php echo esc_url($yith_wcwl->get_wishlist_url()); ?>"><?php _e("My Wishlist", "swiftframework"); ?></a></li>
	  <?php } ?>
	  <li><a href="#address-book" data-toggle="tab"><?php _e("Address Book", "swiftframework"); ?></a></li>
	  <?php if (function_exists('wc_customer_edit_account_url')) { ?>
	  <li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e("Change Details", "swiftframework"); ?></a></li>
	  <?php } ?>
	</ul>

</div>

<div class="my-account-right tab-content">
	
	<?php do_action( 'woocommerce_before_my_account' ); ?>
	
	<div class="tab-pane load active" id="my-orders">
	
		<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
	
	</div>
		
	<div class="tab-pane load" id="my-downloads">
	
		<?php woocommerce_get_template( 'myaccount/my-downloads.php' ); ?>
	
	</div>
	
	<div class="tab-pane load" id="address-book">
	
		<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
	
	</div>	
	
	<?php do_action( 'woocommerce_after_my_account' ); ?>
	
</div>
