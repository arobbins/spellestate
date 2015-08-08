<?php

/**
 * Handles version to version upgrading
 * 
 * @author Eric Daams <eric@ericnicolaas.com>
 */

class enda_woocommerce_bundlerate_upgrade {
    
    /**
     * Perform upgrade
     * @param int $current
     * @param int|false $db_version 
     * @static
     */
    public static function do_upgrade($current, $db_version) {
    	if ($db_version === false) {            
            self::upgrade_1_1_0();
        }
        return;
    }
    
    /**
     * Upgrade to version 1.1.0
     */
    protected static function upgrade_1_1_0() {

    	$rates = enda_woocommerce_bundlerate_shipping::get_bundle_rates();        

    	// No rates were defined anyway
    	if ( empty( $rates ) ) {
    		return;
    	}

        $updated_rates = array();

        foreach ( $rates as $category_slug => $rate ) {
            if ( $rate['enabled'] == 'on' ) {                            

                unset( $rate['enabled'] );

                $category = get_term_by( 'slug', $category_slug, 'product_cat' );

                $new_rate = array(
                    'rates'             => $rate,
                    'destination'       => 'all',
                    'countries'         => '',
                    'category'          => $category->term_id,
                    'shipping_class'    => ''
                );

                $updated_rates[] = $new_rate;
            }
        }

        update_option('woocommerce_enda_bundle_rates', $updated_rates);

        // Also set the 'apply_base_rate_once' setting if user had a filter set up
        $apply_base_rate_once = apply_filters('woocommerce_brs_apply_base_rate_once', true);
        if ( $apply_base_rate_once === false ) {
            $settings = get_option( 'woocommerce_enda_bundle_rate_settings' );
            $settings['apply_base_rate_once'] = 0;
            update_option( 'woocommerce_enda_bundle_rate_settings', $settings );
        }
    }
}