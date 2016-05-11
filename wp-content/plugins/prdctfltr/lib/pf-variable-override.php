<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	$curr_variable = get_option( 'wc_settings_prdctfltr_use_variable_images', 'no' );

	if ( $curr_variable == 'yes' ) {

		if ( function_exists('runkit_function_rename') && function_exists( 'woocommerce_get_product_thumbnail' ) ) :
			runkit_function_rename( 'woocommerce_get_product_thumbnail', 'old_woocommerce_get_product_thumbnail' );
		endif;

		if ( !function_exists( 'woocommerce_get_product_thumbnail' ) ) {

			function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {

				global $product;

				if ( $product->is_type( 'variable' ) ) {

					global $prdctfltr_global;

					$pf_activated = isset( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array();

					if ( !empty( $pf_activated ) ) {
						$attrs = array();
						foreach( $pf_activated as $k => $v ){
							if ( substr( $k, 0, 3 ) == 'pa_' ) {
								$attrs = $attrs + array(
									$k => $v[0]
								);
							}
						}

						if ( count($attrs) > 0 ) {
							$curr_var = $product->get_available_variations();
							foreach( $curr_var as $key => $var ) {
								$curr_var_set[$key]['attributes'] = $var['attributes'];
								$curr_var_set[$key]['variation_id'] = $var['variation_id'];
							}
							$found = WC_Prdctfltr::prdctrfltr_search_array( $curr_var_set, $attrs );
						}
					}

				}

				if ( isset( $found[0] ) && $found[0]['variation_id'] && has_post_thumbnail( $found[0]['variation_id'] ) ) {
					$image = get_the_post_thumbnail( $found[0]['variation_id'], $size );
				} elseif ( has_post_thumbnail( $product->id ) ) {
					$image = get_the_post_thumbnail( $product->id, $size );
				} elseif ( ( $parent_id = wp_get_post_parent_id( $product->id ) ) && has_post_thumbnail( $parent_id ) ) {
					$image = get_the_post_thumbnail( $product, $size );
				} else {
					$image = wc_placeholder_img( $size );
				}

				return $image;

			}
		}
	}

?>