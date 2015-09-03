<?php
/**
 * Edit address form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php

global $woocommerce, $current_user;

get_currentuserinfo();

$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
$myaccount_page_url = "";
if ( $myaccount_page_id ) {
  $myaccount_page_url = get_permalink( $myaccount_page_id );
}

$page_title = ( $load_address == 'billing' ) ? __( 'Billing Address', 'swiftframework' ) : __( 'Shipping Address', 'swiftframework' );

get_currentuserinfo();
?>

<?php wc_print_notices(); ?>


<?php sf_woo_help_bar(); ?>

<div class="my-account-left">

	<h4 class="lined-heading"><span><?php _e("My Account", "swiftframework"); ?></span></h4>
	<ul class="nav my-account-nav">
	  <li><a href="<?php echo esc_attr($myaccount_page_url); ?>"><?php _e("Back to my account", "swiftframework"); ?></a></li>
	</ul>

</div>

<div class="my-account-right">

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>

	<form method="post">

		<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h3>

		<?php foreach ( $address as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>

		<?php endforeach; ?>

		<p>
			<input type="submit" class="button" name="save_address" value="<?php _e( 'Save Address', 'swiftframework' ); ?>" />
			<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
			<input type="hidden" name="action" value="edit_address" />
		</p>

	</form>

<?php endif; ?>

</div>