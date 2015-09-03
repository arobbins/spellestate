<?php
    /**
     * Login Form
     *
     * @author        WooThemes
     * @package       WooCommerce/Templates
     * @version       2.2.6
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    } // Exit if accessed directly

?>

<?php

    global $woocommerce, $sf_options;

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="my-account-login-wrap">

    <?php if (get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes') : ?>

    <div class="col2-set" id="customer_login">

        <div class="col-1">

            <?php endif; ?>

            <div class="login-wrap">
                <h4 class="lined-heading"><span><?php _e( 'Registered customers', 'swiftframework' ); ?></span></h4>

                <form method="post" class="login">

                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <p class="form-row form-row-wide">
                        <label for="username"><?php _e( 'Username or email address', 'swiftframework' ); ?> <span
                                class="required">*</span></label>
                        <input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
                    </p>

                    <p class="form-row form-row-wide">
                        <label for="password"><?php _e( 'Password', 'swiftframework' ); ?> <span
                                class="required">*</span></label>
                        <input class="input-text" type="password" name="password" id="password"/>
                    </p>

                    <?php do_action( 'woocommerce_login_form' ); ?>

                    <p class="form-row">
                        <?php wp_nonce_field( 'woocommerce-login' ); ?>
                        <input type="submit" class="button" name="login"
                               value="<?php _e( 'Login', 'swiftframework' ); ?>"/>
                        <label for="rememberme" class="inline">
                            <input name="rememberme" type="checkbox" id="rememberme"
                                   value="forever"/> <?php _e( 'Remember me', 'swiftframework' ); ?>
                        </label>
                    </p>

                    <p class="lost_password">
                        <a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'swiftframework' ); ?></a>
                    </p>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>

                </form>
            </div>
            <?php if (get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes') : ?>

        </div>

        <div class="col-2">

            <h4 class="lined-heading"><span><?php _e( 'Not registered? No problem', 'swiftframework' ); ?></span></h4>

            <div class="new-user-text"><?php _e( $sf_options['checkout_new_account_text'], 'swiftframework' ); ?></div>

            <form method="post" class="register">

                <?php do_action( 'woocommerce_register_form_start' ); ?>

                <?php if ( get_option( 'woocommerce_registration_generate_username' ) === 'no' ) : ?>

                    <p class="form-row form-row-wide username">
                        <label for="reg_username"><?php _e( 'Username', 'swiftframework' ); ?> <span
                                class="required">*</span></label>
                        <input type="text" class="input-text" name="username" id="reg_username"
                               value="<?php if ( ! empty( $_POST['username'] ) ) {
                                   esc_attr_e( $_POST['username'] );
                               } ?>"/>
                    </p>

                <?php endif; ?>

                <p class="form-row form-row-wide email">
                    <label for="reg_email"><?php _e( 'Email address', 'swiftframework' ); ?> <span
                            class="required">*</span></label>
                    <input type="email" class="input-text" name="email" id="reg_email"
                           value="<?php if ( ! empty( $_POST['email'] ) ) {
                               esc_attr_e( $_POST['email'] );
                           } ?>"/>
                </p>

                <p class="form-row form-row-wide">
                    <label for="reg_password"><?php _e( 'Password', 'swiftframework' ); ?> <span
                            class="required">*</span></label>
                    <input type="password" class="input-text" name="password" id="reg_password"
                           value="<?php if ( ! empty( $_POST['password'] ) ) {
                               esc_attr_e( $_POST['password'] );
                           } ?>"/>
                </p>

                <!-- Spam Trap -->
                <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'swiftframework' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

                <?php do_action( 'woocommerce_register_form' ); ?>
                <?php do_action( 'register_form' ); ?>

                <p class="form-row">
                    <?php wp_nonce_field( 'woocommerce-register' ); ?>
                    <input type="submit" class="button" name="register"
                           value="<?php _e( 'Register', 'swiftframework' ); ?>"/>
                </p>

                <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>

        </div>

    </div>
<?php endif; ?>

    <?php do_action( 'woocommerce_after_customer_login_form' ); ?>

</div>