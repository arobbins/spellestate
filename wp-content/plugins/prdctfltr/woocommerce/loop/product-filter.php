<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	if ( WC_Prdctfltr::prdctfltr_check_appearance() === false ) {
		return;
	}

	do_action( 'prdctfltr_filter_hooks' );

	global $wp, $prdctfltr_global;
	if ( !isset( $prdctfltr_global['done_filters'] ) ) {
		WC_Prdctfltr::make_global( $_REQUEST, 'FALSE' );
	}

	if ( is_shop() || is_product_taxonomy() ) {
		global $wp_the_query;
		$paged = max( 1, $wp_the_query->get( 'paged' ) );
		$per_page = $wp_the_query->get( 'posts_per_page' );
		$total = $wp_the_query->found_posts;
		$first = ( $per_page * $paged ) - $per_page + 1;
		$last = $wp_the_query->get( 'offset' ) > 0 ? min( $total, $wp_the_query->get( 'offset' ) + $wp_the_query->get( 'posts_per_page' ) ) : min( $total, $wp_the_query->get( 'posts_per_page' ) * $paged );
		$pf_request = $wp_the_query->request;
	}
	else if ( isset( $prdctfltr_global['instance_data']) ) {
		$paged = $prdctfltr_global['instance_data']['paged'];
		$per_page = $prdctfltr_global['instance_data']['per_page'];
		$total = $prdctfltr_global['instance_data']['total'];
		$first = $prdctfltr_global['instance_data']['first'];
		$last = $prdctfltr_global['instance_data']['last'];
		$pf_request = $prdctfltr_global['instance_data']['request'];
	}
	else {
		$paged = 1;

		$default_args = array(
			'prdctfltr'				=> 'active',
			'wc_query'				=> 'product_query',
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'posts_per_page' 		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
			'paged'					=> $paged,
			'meta_query'			=> array(
				array(
					'key'			=> '_visibility',
					'value'			=> array( 'catalog', 'visible' ),
					'compare'		=> 'IN'
				)
			)
		);

		$products = new WP_Query( $default_args );

		$per_page = $products->get( 'posts_per_page' );
		$total = $products->found_posts;
		$first = ( $per_page * $paged ) - $per_page + 1;
		$last = $products->get( 'offset' ) > 0 ? min( $total, $products->get( 'offset' ) + $products->get( 'posts_per_page' ) ) : min( $total, $products->get( 'posts_per_page' ) * $paged );
		$pf_request = $products->request;

	}

	$curr_options = WC_Prdctfltr::prdctfltr_get_settings();

	if ( !isset( $prdctfltr_global['widget_search'] ) ) {
		if ( isset( $curr_options['wc_settings_prdctfltr_style_mode'] ) ) {
			if ( !in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_select', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
				$curr_mod = $curr_options['wc_settings_prdctfltr_style_mode'];
			}
			else {
				$curr_mod = 'pf_mod_multirow';
			}
		}
		else {
			$curr_mod = 'pf_mod_multirow';
		}
		$curr_widget_add = '';
	}
	else {
		$curr_options['wc_settings_prdctfltr_style_preset'] = $prdctfltr_global['widget_options']['style'];
		$curr_mod = 'pf_mod_multirow';
		$curr_widget_add = ' data-preset="' . $prdctfltr_global['widget_options']['style'].'" data-template="' . $prdctfltr_global['widget_options']['preset'] . '"';
	}

	if ( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array('pf_arrow','pf_arrow_inline') ) !== false ) {
		$curr_options['wc_settings_prdctfltr_always_visible'] = 'no';
		$curr_options['wc_settings_prdctfltr_disable_bar'] = 'no';
	}

	$curr_elements = ( $curr_options['wc_settings_prdctfltr_active_filters'] !== NULL ? $curr_options['wc_settings_prdctfltr_active_filters'] : array() );

	if ( empty( $curr_elements ) ) {
		return;
	}

	$pf_order_default = array(
		''              => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ),
		'menu_order'    => __( 'Default', 'prdctfltr' ),
		'comment_count' => __( 'Review Count', 'prdctfltr' ),
		'popularity'    => __( 'Popularity', 'prdctfltr' ),
		'rating'        => __( 'Average rating', 'prdctfltr' ),
		'date'          => __( 'Newness', 'prdctfltr' ),
		'price'         => __( 'Price: low to high', 'prdctfltr' ),
		'price-desc'    => __( 'Price: high to low', 'prdctfltr' ),
		'rand'          => __( 'Random Products', 'prdctfltr' ),
		'title'         => __( 'Product Name', 'prdctfltr' )
	);

	if ( !empty( $curr_options['wc_settings_prdctfltr_include_orderby'] ) ) {
		foreach ( $pf_order_default as $u => $i ) {
			if ( !in_array( $u, $curr_options['wc_settings_prdctfltr_include_orderby'] ) ) {
				unset( $pf_order_default[$u] );
			}
		}
		$pf_order_default = array_merge( array( '' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ) ), $pf_order_default );
	}

	$catalog_orderby = apply_filters( 'prdctfltr_catalog_orderby', $pf_order_default );

	$catalog_instock = apply_filters( 'prdctfltr_catalog_instock', array(
		'both'    => __( 'All Products', 'prdctfltr' ),
		'in'  => __( 'In Stock', 'prdctfltr' ),
		'out' => __( 'Out Of Stock', 'prdctfltr' )
	) );

	$default_args = array(
		'prdctfltr'				=> 'active',
		'wc_query'				=> 'product_query',
		'post_type'				=> 'product',
		'post_status'			=> 'publish',
		'posts_per_page'		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
		'paged'					=> $paged,
		'meta_query'			=> array(
			array(
				'key'			=> '_visibility',
				'value'			=> array( 'catalog', 'visible' ),
				'compare'		=> 'IN'
			)
		)
	);

	if ( !isset( $prdctfltr_global['sc_query'] ) ) {
		if ( ( $pf_order = get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) ) !== 'menu_order' ) {
			$default_args['orderby'] = $pf_order;
		}
		else {
			$default_args['orderby'] = 'menu_order title';
		}
		
	}

	$curr_styles = WC_Prdctfltr::prdctfltr_get_styles( $curr_options, $curr_mod );

	$curr_maxheight = ( $curr_options['wc_settings_prdctfltr_limit_max_height'] == 'yes' ? ' style="max-height:' . $curr_options['wc_settings_prdctfltr_max_height'] . 'px;"' : '' );

	if ( WC_Prdctfltr::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' ) {
		$curr_add_query = ' data-page="' . $paged . '"';
	}

	$pf_activated = isset( $prdctfltr_global['active_in_filter'] ) ? $prdctfltr_global['active_in_filter'] : array();

	$pf_permalinks = isset( $prdctfltr_global['active_permalinks'] ) ? $prdctfltr_global['active_permalinks'] : array();

	$dont_show = array();

	if ( !isset( $prdctfltr_global['sc_init'] ) ) {
		if ( WC_Prdctfltr::$settings['wc_settings_prdctfltr_clearall'] == 'category' ) {

			foreach ( $pf_permalinks as $dsk => $dsv ) {
				if ( isset( $pf_activated[$dsk] ) && $pf_activated[$dsk] == $dsv ) {
					unset( $pf_activated[$dsk] );
					$dont_show[$dsk] = $dsv;
				}
			}
		}
		else {
			foreach( $pf_permalinks as $dsk => $dsv ) {
				if ( !isset( $pf_activated[$dsk] ) ) {
					$pf_activated[$dsk] = $dsv;
				}
			}
		}
	}
	else if ( isset( $prdctfltr_global['sc_init'] ) && !empty( $prdctfltr_global['sc_init'] ) ) {
		foreach ( $pf_permalinks as $dsk => $dsv ) {
			if ( isset( $pf_activated[$dsk] ) && $pf_activated[$dsk] == $dsv ) {
				unset( $pf_activated[$dsk] );
				$dont_show[$dsk] = $dsv;
			}
		}
	}

	do_action( 'prdctfltr_filter_before', $curr_options, $pf_activated );

	$prdctfltr_id = isset( $prdctfltr_global['unique_id'] ) ? $prdctfltr_global['unique_id'] : uniqid( 'prdctfltr-' );

	$prdctfltr_global['filter_js'][$prdctfltr_id] = array(
		'args' => $default_args,
		'atts' => isset( $prdctfltr_global['ajax_js'] ) ? $prdctfltr_global['ajax_js'] : array(),
		'atts_sc' => isset( $prdctfltr_global['ajax_atts'] ) ? $prdctfltr_global['ajax_atts'] : array(),
		'adds' => isset( $prdctfltr_global['ajax_adds'] ) ? $prdctfltr_global['ajax_adds'] : array(),
		'widget_search' => isset( $prdctfltr_global['widget_search'] ) ? 'yes' : 'no',
		'widget_options' => isset( $prdctfltr_global['widget_options'] ) ? $prdctfltr_global['widget_options'] : ''
	);

?>
	<div class="prdctfltr_wc prdctfltr_woocommerce woocommerce <?php echo isset( $prdctfltr_global['widget_search'] ) ? 'prdctfltr_wc_widget' : 'prdctfltr_wc_regular' ; ?> <?php echo preg_replace( '/\s+/', ' ', implode( $curr_styles, ' ' ) ); ?>"<?php echo $curr_widget_add; ?><?php echo ( isset( $curr_add_query ) ? $curr_add_query : '' ); ?> data-loader="<?php echo ( $curr_options['wc_settings_prdctfltr_loader'] !== '' ? $curr_options['wc_settings_prdctfltr_loader'] : 'oval' ); ?>"<?php echo ( WC_Prdctfltr::prdctfltr_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' ); ?> data-nonce="<?php echo $nonce = wp_create_nonce( 'prdctfltr_analytics' ); ?>" data-id="<?php echo $prdctfltr_id; ?>">
	<?php

		if ( !isset( $prdctfltr_global['widget_search'] ) && $curr_options['wc_settings_prdctfltr_disable_bar'] == 'no' ) {

			$prdctfltr_icon = $curr_options['wc_settings_prdctfltr_icon'];
		?>
			<span class="prdctfltr_filter_title">
				<a class="prdctfltr_woocommerce_filter<?php echo ' pf_ajax_' . ( $curr_options['wc_settings_prdctfltr_loader'] !== '' ? $curr_options['wc_settings_prdctfltr_loader'] : 'oval' ); ?>" href="#"><i class="<?php echo ( $prdctfltr_icon == '' ? 'prdctfltr-bars' : $prdctfltr_icon ); ?>"></i></a>
		<?php

			if ( $curr_options['wc_settings_prdctfltr_title'] !== '' ) {
				echo $curr_options['wc_settings_prdctfltr_title'];
			}
			else {
				_e( 'Filter products', 'prdctfltr' );
			}

			if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' ) {

				if ( isset( $pf_activated ) ) {

					foreach( $pf_activated as $k => $v ) {

						if ( substr( $k, 0, 10 ) == 'rng_order_' || substr( $k, 0, 12 ) == 'rng_orderby_' || $k == 'order' ) {
							continue;
						}

						if ( isset( $prdctfltr_global['sc_init'] ) && isset( $prdctfltr_global['sc_query'] ) ) {
							if ( array_key_exists( $k, $prdctfltr_global['sc_query'] ) && $v == $prdctfltr_global['sc_query'][$k] ) {
								continue;
							}
						}

						switch( $k ) {
							case 's' :
								if ( isset( $pf_activated['s'] ) ) {
									echo ' / <span>' . __( 'Search', 'prdctfltr' ) . ': '. $pf_activated['s'] . ' ' . '</span> <a href="#" class="prdctfltr_title_remove" data-key="s"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'products_per_page' :
								if ( isset( $pf_activated['products_per_page'] ) ) {
									echo ' / <span>' . $pf_activated['products_per_page'] . ' ' . __( 'Products per page', 'prdctfltr' ) . '</span> <a href="#" class="prdctfltr_title_remove" data-key="products_per_page"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'sale_products' :
								if ( isset( $pf_activated['sale_products'] ) ) {
									echo ' / <span>' . __( 'Products on sale', 'prdctfltr' ) . '</span> <a href="#" class="prdctfltr_title_remove" data-key="sale_products"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'instock_products' :
								if ( isset( $pf_activated['instock_products'] ) ) {
									echo ' / <span>' . $catalog_instock[$pf_activated['instock_products']] . '</span> <a href="#" class="prdctfltr_title_remove" data-key="instock_products"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'orderby' :
								if ( isset( $pf_activated['orderby'] ) ) {
									if ( !array_key_exists( $pf_activated['orderby'], $catalog_orderby ) ) continue;
									echo ' / <span>' . $catalog_orderby[$pf_activated['orderby']] . '</span> <a href="#" class="prdctfltr_title_remove" data-key="orderby"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'min_price' :
								if ( isset( $pf_activated['min_price'] ) && $pf_activated['min_price'] !== '' ) {

									$min_price = $pf_activated['min_price'];

									if ( isset( $pf_activated['max_price'] ) && $pf_activated['max_price'] !== '' ) {
										$curr_max_price = $pf_activated['max_price'];
										$max_price = $pf_activated['max_price'];
									}
									else {
										$max_price = '+';
									}

									echo ' / <span>' . strip_tags( wc_price( $min_price ) ) . ( isset( $max_price ) && $max_price > 0 ? ' - ' . strip_tags( wc_price( $max_price ) ) : $max_price ) . '</span> <a href="#" class="prdctfltr_title_remove" data-key="byprice"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'max_price' :
							break;
							case 'rng_min_price' :
								if ( isset( $pf_activated['rng_min_price'] ) && $pf_activated['rng_min_price'] !== '' ) {

									$min_price = $pf_activated['rng_min_price'];

									if ( isset( $pf_activated['rng_max_price'] ) && $pf_activated['rng_max_price'] !== '' ) {
										$curr_max_price = $pf_activated['rng_max_price'];
										$max_price = $pf_activated['rng_max_price'];
									}
									else {
										$max_price = '+';
									}

									echo ' / <span>' . __('Price range', 'prdctfltr') . ' ' . strip_tags( wc_price( $min_price ) ) . ' &rarr; ' . strip_tags( wc_price( $max_price ) ) . '</span> <a href="#" class="prdctfltr_title_remove" data-key="byprice"><i class="prdctfltr-delete"></i></a>';
								}
							break;
							case 'rng_max_price' :
							break;
							default :
								if ( substr( $k, 0, 4 ) == 'rng_' ) {

									$true_val = substr($k, 8);

									if ( substr($k, 0, 8) == 'rng_max_' || $k == 'rng_min_price' || $k == 'rng_max_price' ) {
										continue;
									}

									if ( term_exists( $v, $true_val ) !== null ) {
										$curr_term = get_term_by( 'slug', $v, $true_val );
										$curr_selected['min'] = $curr_term->name;
									}
									if ( isset( $pf_activated['rng_max_' . $true_val] ) ) {
										if ( term_exists( $pf_activated['rng_max_' . $true_val], $true_val ) !== null ) {
											$curr_term = get_term_by( 'slug', $pf_activated['rng_max_' . $true_val], $true_val );
											$curr_selected['max'] = $curr_term->name;
										}
									}

									echo ' / <span>' . __( 'From', 'prdctfltr' ) . ' ' . $curr_selected['min'] . ' ' . __( 'to' , 'prdctfltr' ) . ' ' . $curr_selected['max'];
									echo '</span> <a href="#" class="prdctfltr_title_remove" data-key="' . $k . '"><i class="prdctfltr-delete"></i></a>';

								}
								else {

									if ( array_key_exists( $k, $prdctfltr_global['range_filters'] ) ) {
										continue;
									}

									if ( $k == 'cat' || $k == 'tag' ) {
										$k = 'product_' . $k;
									}

									$curr_selected = isset( $pf_activated[$k] ) ? $pf_activated[$k] : array();

									if ( substr( $k, 0, 3 ) == 'pa_' && $v !== '' ) {
										$pf_attr_title = ' / <span>';
									}
									else {
										$pf_attr_title = ' / <span>';
									}

									$pf_i=0;
									$pf_attr_active = false;

									foreach( $curr_selected as $selected ) {

										if ( isset( $dont_show[$k] ) && in_array( $selected, $dont_show[$k] ) ) {
											continue;
										}

										if ( term_exists( $selected, $k ) !== null ) {
											$curr_term = get_term_by( 'slug', $selected, $k );

											$pf_attr_title .= ( $pf_i !== 0 ? ', ' : '' ) . $curr_term->name;

											$pf_i++;
											$pf_attr_active = true;
										}

									}

									$pf_attr_title .= '</span> <a href="#" class="prdctfltr_title_remove" data-key="' . ( $k == 'characteristics' ? 'char' : $k ) . '"><i class="prdctfltr-delete"></i></a>';

									if ( $pf_attr_active ) {
										echo $pf_attr_title;
									}

								}

							break;
						}
					}
				}


				if ( $curr_options['wc_settings_prdctfltr_noproducts'] !== '' && $total == 0 ) {
					echo ' / ' . __( 'No products found!', 'prdctfltr' );
				} elseif ( $total == 0 ) {
					echo ' / ' . __( 'No products found!', 'prdctfltr' );
				} elseif ( $total == 1 ) {
					echo ' / ' . __( 'Showing the single result', 'prdctfltr' );
				} elseif ( $total <= $per_page || -1 == $per_page ) {
					echo ' / ' . __( 'Showing all', 'prdctfltr') . ' ' . $total . ' ' . __( 'results', 'prdctfltr' );
				} else {
					echo ' / ' . __( 'Showing', 'prdctfltr' ) . ' ' . $first . ' - ' . $last . ' ' . __( 'of', 'prdctfltr' ) . ' ' . $total . ' ' . __( 'results', 'prdctfltr' );
				}
			}
		?>
			</span>
		<?php
		}

		if ( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
			$curr_columns = 1;
		}
		else {
			$curr_mix_count = ( count($curr_elements) );
			$curr_columns = ( $curr_mix_count < $curr_options['wc_settings_prdctfltr_max_columns'] ? $curr_mix_count : $curr_options['wc_settings_prdctfltr_max_columns'] );
		}

		$curr_columns_class = ' prdctfltr_columns_' . $curr_columns;

		$pf_adoptive_active = false;
		switch ( $curr_options['wc_settings_prdctfltr_adoptive_mode'] ) {
			case 'always' :
				$pf_adoptive_active = true;
			break;
			case 'permalink' :
				if ( !empty( $prdctfltr_global['active_filters'] ) || !empty( $prdctfltr_global['active_permalinks'] ) ) {
					$pf_adoptive_active = true;
				}
			break;
			case 'filter' :
				if ( !empty( $prdctfltr_global['active_filters'] ) ) {
					$pf_adoptive_active = true;
				}
			break;
			default :
				$pf_adoptive_active = false;
			break;
		}

		if ( $pf_adoptive_active === true && $curr_options['wc_settings_prdctfltr_adoptive'] == 'yes' && $total > 0 ) {

			$adpt_taxes = $curr_options['wc_settings_prdctfltr_adoptive_depend'];
			$pf_products = array();

			if ( !empty( $adpt_taxes ) && is_array( $adpt_taxes ) ) {

				$adpt_go = false;
				foreach( $adpt_taxes as $adpt_key => $adpt_tax ) {
					if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_filters'] ) ) {
						$adpt_go = true;
					}
					if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_permalinks'] ) ) {
						$adpt_go = true;
					}
				}

				if ( $adpt_go === true ) {

					$adoptive_args = array(
						'post_type'				=> 'product',
						'post_status' 			=> 'publish',
						'posts_per_page' 		=> 10000000,
						'meta_query'			=> array(
							array(
								'key'			=> '_visibility',
								'value'			=> array( 'catalog', 'visible' ),
								'compare'		=> 'IN'
							)
						)
					);

					$tax_query = array();

					for ( $i = 0; $i < count( $adpt_taxes ); $i++ ) {

						if ( isset( $prdctfltr_global['active_filters'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
							$tax_query[] = array(
								'taxonomy' => $adpt_taxes[$i],
								'field' => 'slug',
								'terms' => $prdctfltr_global['active_filters'][$adpt_taxes[$i]]
							);
						}

						if ( isset( $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
							$tax_query[] = array(
								'taxonomy' => $adpt_taxes[$i],
								'field' => 'slug',
								'terms' => $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]]
							);
						}

					}

					if ( !empty( $tax_query ) ) {
						$tax_query['relation'] = 'AND';
						$adoptive_args['tax_query'] = $tax_query;
					}

					$pf_help_products = new WP_Query( $adoptive_args );

					global $wpdb;
					$pf_products = $wpdb->get_results( $pf_help_products->request );

				}

			}
			else {

				$request = $pf_request;

				if ( !empty( $request ) && is_string( $request ) ) {

					$t_pos = strpos( $request, 'LIMIT' );

					if ( $t_pos !== false ) {
						$t_str = substr( $request, 0, $t_pos );
					}
					else {
						$t_str = $request;
					}

					$t_str .= ' LIMIT 0,10000000 ';

					global $wpdb;
					$pf_products = $wpdb->get_results( $t_str );

				}

			}

			if ( !empty( $pf_products ) ) {

				$curr_in = array();
				foreach ( $pf_products as $p ) {
					if ( !isset( $p->ID ) ) {
						continue;
					}
					$curr_in[] = $p->ID;

				}

				if ( !empty( $curr_in ) && is_array( $curr_in ) ) {

					$output_terms = array();

					$pf_product_terms_query = '
						SELECT %4$s.slug, %3$s.taxonomy, COUNT(DISTINCT %1$s.ID) as count FROM %1$s
						INNER JOIN %2$s ON (%1$s.ID = %2$s.object_id)
						INNER JOIN %3$s ON (%2$s.term_taxonomy_id = %3$s.term_taxonomy_id )
						INNER JOIN %4$s ON (%3$s.term_id = %4$s.term_id )
						WHERE 1=1
						AND %1$s.ID IN ("' . implode( '","', array_map( 'esc_sql', $curr_in ) ) . '")
						GROUP BY slug,taxonomy
					';

					$pf_product_terms = $wpdb->get_results( $wpdb->prepare( $pf_product_terms_query, $wpdb->posts, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms ) );

					foreach ( $pf_product_terms as $p ) {
						if ( !isset($output_terms[$p->taxonomy]) ) {
							$output_terms[$p->taxonomy] = array();
						}
						if ( !array_key_exists( $p->slug, $output_terms[$p->taxonomy] ) ) {
							$output_terms[$p->taxonomy][$p->slug] = $p->count;
						}
					}
				}

			}

		}

		$pf_structure = get_option( 'permalink_structure' );
		$curr_cat_query = get_option( 'wc_settings_prdctfltr_force_categories', 'no' );

		if ( isset( $curr_options['wc_settings_prdctfltr_custom_action'] ) && !empty( $curr_options['wc_settings_prdctfltr_custom_action'] ) ) {
			$curr_action = esc_url( $curr_options['wc_settings_prdctfltr_custom_action'] );
		}
		else {
			if ( is_shop() || is_product_taxonomy() || is_product() ) {

				if ( is_product() ) {
					$curr_action = get_permalink( WC_Prdctfltr::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) );
				}
				else if ( is_shop() || ( WC_Prdctfltr::$settings['wc_settings_prdctfltr_filtering_mode'] == 'simple' && WC_Prdctfltr::$settings['permalink_structure'] !== '' && is_product_taxonomy() ) ) {
					$curr_action = get_permalink( WC_Prdctfltr::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) );
				}
				else {
					if ( $pf_structure == '' ) {
						$curr_action = esc_url( remove_query_arg( array( 'page', 'paged' ), esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) ) ) );
					} else {
						$curr_action = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
					}
				}
			}
			else if ( !isset( $prdctfltr_global['action'] ) || $prdctfltr_global['action'] == '' ) {
				if ( $pf_structure == '' ) {
					$curr_action = esc_url( remove_query_arg( array( 'page', 'paged' ), esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) ) ) );
				} else {
					$curr_action = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
				}
			}
			else {
				$curr_action = esc_url( $prdctfltr_global['action'] );
			}
		}


	?>
	<form action="<?php echo trailingslashit( $curr_action ); ?>" class="prdctfltr_woocommerce_ordering" method="get">

		<?php do_action( 'prdctfltr_filter_form_before', $curr_options, $pf_activated ); ?>

		<div class="prdctfltr_filter_wrapper<?php echo $curr_columns_class; ?>" data-columns="<?php echo $curr_columns; ?>">
			<div class="prdctfltr_filter_inner">
			<?php

				$q = 0;
				$n = 0;
				$p = 0;

				$active_filters = array();

				$pf_adv_check = array(
					'pfa_title' => '',
					'pfa_taxonomy' => '',
					'pfa_include' => array(),
					'pfa_orderby' => 'name',
					'pfa_order' => 'ASC',
					'pfa_multi' => 'no',
					'pfa_relation' => 'IN',
					'pfa_adoptive' => 'no',
					'pfa_none' => 'no',
					'pfa_hierarchy' => 'no',
					'pfa_hierarchy_mode' => 'no',
					'pfa_mode' => 'showall',
					'pfa_style' => 'pf_attr_text',
					'pfa_limit' => 0
				);

				$pf_rng_check = array(
					'pfr_title' => '',
					'pfr_taxonomy' => '',
					'pfr_include' => array(),
					'pfr_orderby' => 'name',
					'pfr_order' => 'ASC',
					'pfr_style' => 'no',
					'pfr_grid' => 'no',
					'pfr_adoptive' => 'no',
					'pfr_custom' => ''
				);

				foreach ( $curr_elements as $curr_el ) :

					$curr_fo = array();
					$customization = array();
					$curr_term_customization = '';
					$curr_term_style = '';

					if ( !isset( $prdctfltr_global['widget_search'] ) && $q == $curr_columns && ( $curr_options['wc_settings_prdctfltr_style_mode'] == 'pf_mod_multirow' || $curr_options['wc_settings_prdctfltr_style_preset'] == 'pf_select' ) ) {
						$q = 0;
						echo '<div class="prdctfltr_clear"></div>';
					}

					switch ( $curr_el ) :

					case 'per_page' :

						if ( $curr_options['wc_settings_prdctfltr_perpage_term_customization'] !== '' ) {
							$language = WC_Prdctfltr::prdctfltr_wpml_language();

							if ( isset( $language ) && $language !== false ) {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_perpage_term_customization'] . '_' . $language, '' );
								if ( $get_customization == '' ) {
									$get_customization = get_option( $curr_options['wc_settings_prdctfltr_perpage_term_customization'], '' );
								}
							}
							else {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_perpage_term_customization'], '' );
							}
							

							if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
								$ctcid = $curr_options['wc_settings_prdctfltr_perpage_term_customization'];
								$curr_term_customization = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $ctcid;

								$customization = $get_customization;
								if ( $customization['style'] == 'text' ) {
									WC_Prdctfltr::add_customized_terms_css( $ctcid, $customization );
								}
							}
						}
						if ( !isset( $customization ) ) {
							$customization = array();
							$curr_term_customization = '';
							$curr_options['wc_settings_prdctfltr_perpage_term_customization'] = '';
						}

					?>
						<div class="prdctfltr_filter prdctfltr_per_page<?php echo $curr_term_customization; ?>" data-filter="pf_per_page">
							<input name="products_per_page" type="hidden"<?php echo ( isset($pf_activated['products_per_page'] ) ? ' value="'.$pf_activated['products_per_page'].'"' : '' );?>>
							<?php
								if ( isset( $prdctfltr_global['widget_search'] ) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) && isset($pf_activated['products_per_page'] ) ) {

									$paste_title = true;

									if ( isset( $prdctfltr_global['sc_query'] ) ) {
										if ( array_key_exists( 'products_per_page', $prdctfltr_global['sc_query'] ) && $pf_activated['products_per_page'] == $prdctfltr_global['sc_query']['products_per_page'] ) {
											$paste_title = false;
										}
									}

									if ( $paste_title === true ) {
										echo '<a href="#" data-key="products_per_page"><i class="prdctfltr-delete"></i></a> <span>' . $pf_activated['products_per_page'] . '</span> / ';
									}

								}

								if ( $curr_options['wc_settings_prdctfltr_perpage_title'] != '' ) {
									echo $curr_options['wc_settings_prdctfltr_perpage_title'];
								}
								else {
									_e( 'Products Per Page', 'prdctfltr' );
								}
							?>
							<i class="prdctfltr-down"></i>
							<?php echo $pf_after_title; ?>
							<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
							<?php

								$filter_customization = WC_Prdctfltr::get_filter_customization( 'per_page', $curr_options['wc_settings_prdctfltr_perpage_filter_customization'] );

								if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

									foreach( $filter_customization['settings'] as $v ) {
										$curr_perpage[$v['value']] = $v['text'];
									}

								}
								else {

									$curr_perpage_set = $curr_options['wc_settings_prdctfltr_perpage_range'];
									$curr_perpage_limit = $curr_options['wc_settings_prdctfltr_perpage_range_limit'];

									$curr_perpage = array();

									for ($i = 1; $i <= $curr_perpage_limit; $i++) {

										$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . ( $curr_options['wc_settings_prdctfltr_perpage_label'] == '' ? __( 'Products', 'prdctfltr' ) : $curr_options['wc_settings_prdctfltr_perpage_label'] );

									}

								}

								foreach ( $curr_perpage as $id => $name ) {

									$checked = ( isset($pf_activated['products_per_page']) && $pf_activated['products_per_page'] == $id ? ' checked' : ' ' );

									if ( $curr_options['wc_settings_prdctfltr_perpage_term_customization'] !== '' ) {
										$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization, $checked );
									}
									else {
										$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
									}

									printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['products_per_page']) && $pf_activated['products_per_page'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );
								}
							?>
							</div>
						</div>

					<?php break;

					case 'instock' :

						if ( $curr_options['wc_settings_prdctfltr_instock_term_customization'] !== '' ) {
							$language = WC_Prdctfltr::prdctfltr_wpml_language();

							if ( isset( $language ) && $language !== false ) {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_instock_term_customization'] . '_' . $language, '' );
								if ( $get_customization == '' ) {
									$get_customization = get_option( $curr_options['wc_settings_prdctfltr_instock_term_customization'], '' );
								}
							}
							else {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_instock_term_customization'], '' );
							}
							

							if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
								$ctcid = $curr_options['wc_settings_prdctfltr_instock_term_customization'];
								$curr_term_customization = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $ctcid;

								$customization = $get_customization;
								if ( $customization['style'] == 'text' ) {
									WC_Prdctfltr::add_customized_terms_css( $ctcid, $customization );
								}
							}
						}
						if ( !isset( $customization ) ) {
							$customization = array();
							$curr_term_customization = '';
							$curr_options['wc_settings_prdctfltr_instock_term_customization'] = '';
						}
					?>
						<div class="prdctfltr_filter prdctfltr_instock<?php echo $curr_term_customization; ?>" data-filter="pf_instock">
							<input name="instock_products" type="hidden"<?php echo ( isset($pf_activated['instock_products'] ) ? ' value="'.$pf_activated['instock_products'].'"' : '' );?>>
							<?php
								if ( isset( $prdctfltr_global['widget_search'] ) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) && isset($pf_activated['instock_products'] ) ) {

									$paste_title = true;

									if ( isset( $prdctfltr_global['sc_query'] ) ) {
										if ( array_key_exists( 'instock_products', $prdctfltr_global['sc_query'] ) && $pf_activated['instock_products'] == $prdctfltr_global['sc_query']['instock_products'] ) {
											$paste_title = false;
										}
									}

									if ( $paste_title === true ) {
										echo '<a href="#" data-key="instock_products"><i class="prdctfltr-delete"></i></a> <span>'.$catalog_instock[$pf_activated['instock_products']] . '</span> / ';
									}

									
								}

								if ( $curr_options['wc_settings_prdctfltr_instock_title'] != '' ) {
									echo $curr_options['wc_settings_prdctfltr_instock_title'];
								}
								else {
									_e( 'Product Availability', 'prdctfltr' );
								}
							?>
							<i class="prdctfltr-down"></i>
							<?php echo $pf_after_title; ?>
							<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
							<?php

								foreach ( $catalog_instock as $id => $name ) {

									$checked = ( isset($pf_activated['instock_products']) && $pf_activated['instock_products'] == $id ? ' checked' : ' ' );

									if ( $curr_options['wc_settings_prdctfltr_instock_term_customization'] !== '' ) {
										$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization, $checked );
									}
									else {
										$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
									}

									printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['instock_products']) && $pf_activated['instock_products'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );

								}
							?>
							</div>
						</div>

					<?php break;

					case 'sort' :

						if ( $curr_options['wc_settings_prdctfltr_orderby_term_customization'] !== '' ) {
							$language = WC_Prdctfltr::prdctfltr_wpml_language();

							if ( isset( $language ) && $language !== false ) {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_orderby_term_customization'] . '_' . $language, '' );
								if ( $get_customization == '' ) {
									$get_customization = get_option( $curr_options['wc_settings_prdctfltr_orderby_term_customization'], '' );
								}
							}
							else {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_orderby_term_customization'], '' );
							}
							

							if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
								$ctcid = $curr_options['wc_settings_prdctfltr_orderby_term_customization'];
								$curr_term_customization = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $ctcid;

								$customization = $get_customization;
								if ( $customization['style'] == 'text' ) {
									WC_Prdctfltr::add_customized_terms_css( $ctcid, $customization );
								}
							}
						}
						if ( !isset( $customization ) ) {
							$customization = array();
							$curr_term_customization = '';
							$curr_options['wc_settings_prdctfltr_orderby_term_customization'] = '';
						}
					?>
						<div class="prdctfltr_filter prdctfltr_orderby<?php echo $curr_term_customization; ?>" data-filter="orderby">
							<input name="orderby" type="hidden"<?php echo ( isset($pf_activated['orderby'] ) ? ' value="'.$pf_activated['orderby'].'"' : '' );?>>
							<?php
								if ( isset($prdctfltr_global['widget_search']) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) && isset( $pf_activated['orderby'] ) && isset( $catalog_orderby[$pf_activated['orderby']] ) ) {

									$paste_title = true;

									if ( isset( $prdctfltr_global['sc_query'] ) ) {
										if ( array_key_exists( 'orderby', $prdctfltr_global['sc_query'] ) && $pf_activated['orderby'] == $prdctfltr_global['sc_query']['orderby'] ) {
											$paste_title = false;
										}
									}

									if ( $paste_title === true ) {
										echo '<a href="#" data-key="orderby"><i class="prdctfltr-delete"></i></a> <span>' . $catalog_orderby[$pf_activated['orderby']] . '</span> / ';
									}

								}

								if ( $curr_options['wc_settings_prdctfltr_orderby_title'] != '' ) {
									echo $curr_options['wc_settings_prdctfltr_orderby_title'];
								}
								else {
									_e('Sort by', 'prdctfltr');
								}
							?>
							<i class="prdctfltr-down"></i>
							<?php echo $pf_after_title; ?>
							<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
							<?php
								if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
									unset( $catalog_orderby['rating'] );
								}
								if ( $curr_options['wc_settings_prdctfltr_orderby_none'] == 'yes' ) {
									unset( $catalog_orderby[''] );
								}

								foreach ( $catalog_orderby as $id => $name ) {

									$checked = ( isset($pf_activated['orderby']) && $pf_activated['orderby'] == $id ? ' checked' : ' ' );

									if ( $curr_options['wc_settings_prdctfltr_orderby_term_customization'] !== '' ) {
										$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization, $checked );
									}
									else {
										$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
									}

									printf( '<label%1$s>%2$s</label>', ( isset($pf_activated['orderby']) && $pf_activated['orderby'] == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );

								}
							?>
							</div>
						</div>

					<?php break;

					case 'price' :

						if ( $curr_options['wc_settings_prdctfltr_price_term_customization'] !== '' ) {
							$language = WC_Prdctfltr::prdctfltr_wpml_language();

							if ( isset( $language ) && $language !== false ) {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_price_term_customization'] . '_' . $language, '' );
								if ( $get_customization == '' ) {
									$get_customization = get_option( $curr_options['wc_settings_prdctfltr_price_term_customization'], '' );
								}
							}
							else {
								$get_customization = get_option( $curr_options['wc_settings_prdctfltr_price_term_customization'], '' );
							}
							

							if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
								$ctcid = $curr_options['wc_settings_prdctfltr_price_term_customization'];
								$curr_term_customization = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $ctcid;

								$customization = $get_customization;
								if ( $customization['style'] == 'text' ) {
									WC_Prdctfltr::add_customized_terms_css( $ctcid, $customization );
								}
							}
						}
						if ( !isset( $customization ) ) {
							$customization = array();
							$curr_term_customization = '';
							$curr_options['wc_settings_prdctfltr_price_term_customization'] = '';
						}

					?>
						<div class="prdctfltr_filter prdctfltr_byprice<?php echo $curr_term_customization; ?>"  data-filter="pf_byprice">
							<input name="min_price" type="hidden"<?php echo ( isset( $pf_activated['min_price'] ) ? ' value="' . $pf_activated['min_price'] . '"' : '' );?>>
							<input name="max_price" type="hidden"<?php echo ( isset( $pf_activated['max_price'] ) ? ' value="' . $pf_activated['max_price'] . '"' : '' );?>>
							<?php
								if ( isset($prdctfltr_global['widget_search']) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) && isset($pf_activated['min_price']) && $pf_activated['min_price'] !== '' ) {

									$paste_title = true;

									if ( isset( $prdctfltr_global['sc_query'] ) ) {
										if ( array_key_exists( 'min_price', $prdctfltr_global['sc_query'] ) && $pf_activated['min_price'] == $prdctfltr_global['sc_query']['min_price'] ) {
											$paste_title = false;
										}
									}

									if ( $paste_title === true ) {
										$min_price = strip_tags( wc_price( $pf_activated['min_price'] ) );
										if ( isset( $pf_activated['max_price'] ) && $pf_activated['max_price'] !== '' ) {
											$curr_max_price = $pf_activated['max_price'];
											$max_price = $pf_activated['max_price'];
										}
										else {
											$max_price = ' +';
										}

										echo '<a href="#" data-key="byprice"><i class="prdctfltr-delete"></i></a> <span>' . $min_price . ( isset( $max_price ) && $max_price > 0 ? ' - ' . strip_tags( wc_price( $max_price ) ) : $max_price ) . '</span> / ';
									}

								}

								if ( $curr_options['wc_settings_prdctfltr_price_title'] != '' ) {
									echo $curr_options['wc_settings_prdctfltr_price_title'];
								}
								else {
									_e( 'Price range', 'prdctfltr' );
								}
							?>
							<i class="prdctfltr-down"></i>
							<?php

							echo $pf_after_title;

							$filter_customization = WC_Prdctfltr::get_filter_customization( 'price', $curr_options['wc_settings_prdctfltr_price_filter_customization'] );

							$catalog_ready_price = array();
							$curr_price = ( isset($pf_activated['min_price']) ? $pf_activated['min_price'].'-'.( isset($pf_activated['max_price']) ? $pf_activated['max_price'] : '' ) : '' );

							if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

								if ( $curr_options['wc_settings_prdctfltr_price_none'] == 'no' ) {
									$catalog_ready_price = array(
										'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
									);
								}

								foreach( $filter_customization['settings'] as $k => $v ) {
									$catalog_ready_price[$k] = $v;
								}

							}
							else {

								$curr_prices = array();
								$curr_prices_currency = array();

								$curr_price_set = $curr_options['wc_settings_prdctfltr_price_range'];
								$curr_price_add = $curr_options['wc_settings_prdctfltr_price_range_add'];
								$curr_price_limit = $curr_options['wc_settings_prdctfltr_price_range_limit'];

								global $wpdb;
								$min = floor( $wpdb->get_var(
									$wpdb->prepare('
										SELECT min(meta_value + 0)
										FROM %1$s
										LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
										WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
										AND meta_value != ""
										', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price' )
									)
								);

								if ( $curr_options['wc_settings_prdctfltr_price_none'] == 'no' ) {
									$catalog_ready_price = array(
										'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
									);
								}

								for ( $i = 0; $i < $curr_price_limit; $i++ ) {

									if ( $i == 0 ) {
										$min_price = $min;
										$max_price = $curr_price_set;
									}
									else {
										$min_price = $curr_price_set+($i-1)*$curr_price_add;
										$max_price = $curr_price_set+$i*$curr_price_add;
									}

									$curr_prices[$i] = $min_price . '-' . ( ($i+1) == $curr_price_limit ? '' : $max_price );

									$curr_prices_currency[$i] = strip_tags( wc_price( $min_price ) ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . strip_tags( wc_price( $max_price ) ) );

									$catalog_ready_price = $catalog_ready_price + array(
										$curr_prices[$i] => $curr_prices_currency[$i]
									);

								}

							}

							$catalog_price = apply_filters( 'prdctfltr_catalog_price', $catalog_ready_price );

						?>
						<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
							<?php
								foreach ( $catalog_price as $id => $name ) {
									$checked = ( $curr_price == $id ? ' checked' : ' ' );

									if ( $curr_options['wc_settings_prdctfltr_price_term_customization'] !== '' ) {
										$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization, $checked );
									}
									else {
										$curr_insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
									}

									printf( '<label%1$s>%2$s</label>', ( $curr_price == $id ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $curr_insert );
								}
							?>
							</div>
						</div>

					<?php break;

					case 'range' :

						foreach ( $pf_rng_check as $k => $v ) {
							if ( !isset( $curr_options['wc_settings_prdctfltr_range_filters'][$k][$p] ) ) {
								$curr_options['wc_settings_prdctfltr_range_filters'][$k][$p] = $v;
							}
						}
						$adpt_rng = isset( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_adoptive'][$p] ) ? $curr_options['wc_settings_prdctfltr_range_filters']['pfr_adoptive'][$p] : 'no';
						$attr = $curr_options['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p];

						if ( $attr !== 'price' && $total !== 0 && $adpt_rng == 'yes' && ( isset( $output_terms ) && ( !isset( $output_terms[$attr] ) || isset( $output_terms[$attr] ) && empty( $output_terms[$attr]) ) === true ) ) {
							continue;
						}

			?>
						<div class="prdctfltr_filter prdctfltr_range prdctfltr_<?php echo $attr; ?> <?php echo 'pf_rngstyle_' . $curr_options['wc_settings_prdctfltr_range_filters']['pfr_style'][$p]; ?>">
							<input name="rng_min_<?php echo $attr; ?>" type="hidden"<?php echo ( isset( $pf_activated['rng_min_' . $attr] ) ? ' value="'.$pf_activated['rng_min_' . $attr].'"' : '' );?>>
							<input name="rng_max_<?php echo $attr; ?>" type="hidden"<?php echo ( isset( $pf_activated['rng_max_' . $attr] ) ? ' value="'.$pf_activated['rng_max_' . $attr].'"' : '' );?>>
							<input name="rng_orderby_<?php echo $attr; ?>" type="hidden" value="<?php echo $curr_options['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p]; ?>">
							<input name="rng_order_<?php echo $attr; ?>" type="hidden" value="<?php echo $curr_options['wc_settings_prdctfltr_range_filters']['pfr_order'][$p]; ?>">
							<?php
								if ( isset($prdctfltr_global['widget_search']) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) ) {
									if ( isset($pf_activated['rng_min_' . $attr]) && isset($pf_activated['rng_max_' . $attr]) ) {
										echo '<a href="#" data-key="rng_' . $attr . '"><i class="prdctfltr-delete"></i></a> <span>';
										if ( $attr == 'price' ) {
											echo strip_tags( wc_price( $pf_activated['rng_min_' . $attr] ) ) . ' - ' . strip_tags( wc_price( $pf_activated['rng_max_' . $attr] ) );
										}
										else {
											$pf_f_term = get_term_by('slug', $pf_activated['rng_min_' . $attr], $attr);
											$pf_s_term = get_term_by('slug', $pf_activated['rng_max_' . $attr], $attr);
											echo $pf_f_term->name . ' - ' . $pf_s_term->name;
										}
										echo '</span> / ';
									}
								}

								if ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_title'][$p] !== '' ) {
									echo $curr_options['wc_settings_prdctfltr_range_filters']['pfr_title'][$p];
								}
								else {
									if ( !in_array($curr_options['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p], array('price') ) ) {
										echo wc_attribute_label( $attr );
									}
									else {
										_e( 'Price range', 'prdctfltr' );
									}

								}
							?>
							<i class="prdctfltr-down"></i>
							<?php echo $pf_after_title; ?>
							<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
							<?php

								$add_rng_js = '';

								$curr_rng_id = 'prdctfltr_rng_' . $p;
								$prdctfltr_global['ranges'][$curr_rng_id] = array();
								$prdctfltr_global['ranges'][$curr_rng_id]['type'] = 'double';
								$prdctfltr_global['ranges'][$curr_rng_id]['min_interval'] = 1;

								if ( !in_array($curr_options['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p], array( 'price' ) ) ) {

									$curr_include = $curr_options['wc_settings_prdctfltr_range_filters']['pfr_include'][$p];

									$curr_include = WC_Prdctfltr::prdctfltr_wpml_translate_terms( $curr_include, $attr );

									if ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] == 'number' ) {
										$attr_args = array(
											'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
											'orderby' => 'slug'
										);
										$curr_attributes = WC_Prdctfltr::prdctfltr_get_terms( $attr, $attr_args );
										$pf_sort_args = array(
											'order' => ( isset( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] ) ? $curr_options['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] : 'ASC' )
										);
										$curr_attributes = WC_Prdctfltr::prdctfltr_sort_terms_naturally( $curr_attributes, $pf_sort_args );
									}
									else {
										$attr_args = array(
											'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
											'orderby' => ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] !== '' ? $curr_options['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p] : 'name' ),
											'order' => ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] !== '' ? $curr_options['wc_settings_prdctfltr_range_filters']['pfr_order'][$p] : 'ASC' )
										);
										$curr_attributes = WC_Prdctfltr::prdctfltr_get_terms( $attr, $attr_args );
									}

									$prdctfltr_global['ranges'][$curr_rng_id]['values'] = array();

									$c=0;

									foreach ( $curr_attributes as $attribute ) {

										if ( !empty( $curr_include ) && !in_array( $attribute->slug, $curr_include ) ) {
											continue;
										}

										if ( $adpt_rng == 'yes' && isset( $output_terms[$attr] ) ) {
											if ( !isset( $output_terms[$attr][$attribute->slug] ) ) {
												continue;
											}
										}

										if ( isset( $pf_activated['rng_min_' . $attr] ) && $pf_activated['rng_min_' . $attr] == $attribute->slug ) {
											$prdctfltr_global['ranges'][$curr_rng_id]['from'] = $c;
										}

										if ( isset( $pf_activated['rng_max_' . $attr] ) && $pf_activated['rng_max_' . $attr] == $attribute->slug ) {
											$prdctfltr_global['ranges'][$curr_rng_id]['to'] = $c;
										}

										$prdctfltr_global['ranges'][$curr_rng_id]['values'][] = '<span class=\'pf_range_val\'>' . $attribute->slug . '</span>' . $attribute->name;

										$c++;
									}

									$prdctfltr_global['ranges'][$curr_rng_id]['decorate_both'] = false;
									$prdctfltr_global['ranges'][$curr_rng_id]['values_separator'] = ' &rarr; ';

									if ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p] !== '' ) {
										$add_rng_js = $curr_options['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p];
									}

								}
								else {

									$prices = WC_Prdctfltr::get_filtered_price( $adpt_rng );
									$pf_curr_min = floor( $prices->min_price );
									$pf_curr_max = ceil( $prices->max_price );

									if ( $pf_curr_min == $pf_curr_max ) {
										$pf_curr_min = $pf_curr_min-5;
										$pf_curr_max = $pf_curr_max+5;
									}

									$pf_curr_min = WC_Prdctfltr::price_to_float( strip_tags( wc_price( $pf_curr_min ) ) );
									$pf_curr_max = WC_Prdctfltr::price_to_float( strip_tags( wc_price( $pf_curr_max ) ) );

									$prdctfltr_global['ranges'][$curr_rng_id]['min'] = $pf_curr_min;
									$prdctfltr_global['ranges'][$curr_rng_id]['max'] = $pf_curr_max;

									if ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p] !== '' ) {
										$add_rng_js = $curr_options['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p];
									}

									$currency_pos = get_option( 'woocommerce_currency_pos', 'left' );
									$currency = get_woocommerce_currency_symbol();

									switch ( $currency_pos ) {
										case 'right' :
										case 'right_space' :
											$prdctfltr_global['ranges'][$curr_rng_id]['postfix'] = $currency;
										break;
										case 'left_space' :
										case 'left' :
										default :
											$prdctfltr_global['ranges'][$curr_rng_id]['prefix'] = $currency;
										break;
									}

									if ( isset( $pf_activated['rng_min_' . $attr] ) ) {
										$prdctfltr_global['ranges'][$curr_rng_id]['from'] = floor( $pf_activated['rng_min_' . $attr] );
									}

									if ( isset( $pf_activated['rng_max_' . $attr] ) ) {
										$prdctfltr_global['ranges'][$curr_rng_id]['to'] = ceil( $pf_activated['rng_max_' . $attr] );
									}

								}

								if ( $curr_options['wc_settings_prdctfltr_range_filters']['pfr_grid'][$p] == 'yes' ) {
									$prdctfltr_global['ranges'][$curr_rng_id]['grid'] = true;
								}

								$prdctfltr_global['price_ratio'] = 100/WC_Prdctfltr::price_to_float( strip_tags( wc_price( 100 ) ) );

								if ( $add_rng_js !== '' ) {

									$rng_set = explode( PHP_EOL, stripslashes( $add_rng_js ) );

									foreach( $rng_set as $rv ) {
										if ( $rv == '' ) {
											continue;
										}
										if ( substr( $rv, -1 ) == ',' ) {
											$rv = substr( $rv, 0, -1 );
										}
										$rng_pieces = explode( ':', $rv, 2 );
										if ( count( $rng_pieces ) == 2 ) {
											if ( substr( $rng_pieces[1], 0, 1 ) == "'" || substr( $rng_pieces[1], 0, 1 ) == '"' ) {
												$rng_pieces[1] = substr( $rng_pieces[1], 1 );
											}
											if ( substr( $rng_pieces[1], -1 ) == "'" || substr( $rng_pieces[1], -1 ) == '"' ) {
												$rng_pieces[1] = substr( $rng_pieces[1], 0, -1 );
											}
											$prdctfltr_global['ranges'][$curr_rng_id][$rng_pieces[0]] = $rng_pieces[1];
										}
									}

								}

								printf( '<input id="%1$s" class="pf_rng_%2$s" />', $curr_rng_id, $curr_options['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p] );
							?>
							</div>
						</div>
						<?php

						$p++;
					break;

					case 'search' :

						$pf_srch = ( isset( $prdctfltr_global['sc_init'] ) && $prdctfltr_global['sc_init'] === true ? 'search_products' : 's' );
					?>
						<div class="prdctfltr_filter prdctfltr_search" data-filter="pf_search">
							<?php
								if ( isset( $prdctfltr_global['widget_search'] ) ) {
									$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
									$pf_after_title = '</span>' . $after_title;
								}
								else {
									$pf_before_title = '<span class="prdctfltr_regular_title">';
									$pf_after_title = '</span>';
								}

								echo $pf_before_title;

								if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) && isset($pf_activated['s'] ) ) {

									$paste_title = true;

									if ( isset( $prdctfltr_global['sc_query'] ) ) {
										if ( array_key_exists( 's', $prdctfltr_global['sc_query'] ) && $pf_activated['s'] == $prdctfltr_global['sc_query']['s'] ) {
											$paste_title = false;
										}
									}

									if ( $paste_title === true ) {
										echo '<a href="#" data-key="s"><i class="prdctfltr-delete"></i></a> <span>'.$pf_activated['s'] . '</span> / ';
									}

								}

								if ( $curr_options['wc_settings_prdctfltr_search_title'] != '' ) {
									echo $curr_options['wc_settings_prdctfltr_search_title'];
								}
								else {
									_e( 'Search', 'prdctfltr' );
								}
							?>
							<i class="prdctfltr-down"></i>
							<?php echo $pf_after_title; ?>
							<div class="prdctfltr_checkboxes">
							<?php
								$pf_placeholder = $curr_options['wc_settings_prdctfltr_search_placeholder'] != '' ? esc_attr( $curr_options['wc_settings_prdctfltr_search_placeholder'] ) : esc_attr( __( 'Product keywords', 'prdctfltr' ) );
								$curr_insert = '<input class="pf_search" name="' . $pf_srch .'" type="text"' . ( isset($pf_activated['s'] ) ? ' value="' . $pf_activated['s'] . '"' : '' ) . ' placeholder="' . $pf_placeholder . '">';
								printf( '<label%1$s>%2$s<a href="#" class="pf_search_trigger"></a></label>', ( isset($pf_activated['s'] ) ? ' class="prdctfltr_active"' : '' ), $curr_insert );
							?>
							</div>
						</div>

				<?php

					break;

					default :

						if ( $curr_el == 'cat' ) {

							$curr_fo['filter'] = 'product_cat';
							$mod = 'regular';
						}
						else if ( $curr_el == 'tag' ) {

							$curr_fo['filter'] = 'product_tag';
							$mod = 'regular';
						}
						else if ( $curr_el == 'char' ) {

							$curr_fo['filter'] = 'characteristics';
							$mod = 'regular';
						}
						else if ( substr( $curr_el, 0, 3) == 'pa_' ) {

							$curr_fo['filter'] = $curr_el;
							$mod = 'attribute';
						}
						else if ( $curr_el == 'advanced' ) {

							$curr_fo['filter'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_taxonomy'][$n];
							$mod = 'advanced';
						}

						if ( in_array( $mod, array( 'regular', 'attribute' ) ) ) {
							$curr_fo['settings']['title'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_title'];

							if ( $mod == 'attribute' ) {
								$curr_fo['settings']['include'] = $curr_options['wc_settings_prdctfltr_include_' . $curr_el];
							}
							else {
								$curr_fo['settings']['include'] = $curr_options['wc_settings_prdctfltr_include_' . $curr_el . 's'];
							}

							$checked_customization = isset( $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) .'_term_customization'] ) ? $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) .'_term_customization'] : '' ;

							$curr_fo['settings']['orderby'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_orderby'];
							$curr_fo['settings']['order'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_order'];
							$curr_fo['settings']['limit'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_limit'];
							$curr_fo['settings']['multi'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_multi'];
							$curr_fo['settings']['relation'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'custom_tax' : $curr_el ) . '_relation'];
							$curr_fo['settings']['adoptive'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_adoptive'];
							$curr_fo['settings']['none'] = $curr_options['wc_settings_prdctfltr_' . ( $curr_el == 'char' ? 'chars' : $curr_el ) . '_none'];
							$curr_fo['settings']['customization'] = $checked_customization;

							if ( $mod == 'attribute' || $curr_el == 'cat' ) {
								$curr_fo['settings']['hierarchy'] = $curr_options['wc_settings_prdctfltr_' . $curr_el . '_hierarchy'];
								$curr_fo['settings']['hierarchy_mode'] = $curr_options['wc_settings_prdctfltr_' . $curr_el . '_hierarchy_mode'];
								$curr_fo['settings']['mode'] = $curr_options['wc_settings_prdctfltr_' . $curr_el . '_mode'];
							}
							if ( $mod == 'attribute' ) {
								$curr_fo['settings']['style'] = $curr_options['wc_settings_prdctfltr_' . $curr_el];
							}
						}
						else {


							foreach ( $pf_adv_check as $ck => $cv ) {
								if ( !isset($curr_options['wc_settings_prdctfltr_advanced_filters'][$ck][$n]) ) {
									$curr_options['wc_settings_prdctfltr_advanced_filters'][$ck][$n] = $cv;
								}
							}

							$checked_customization = isset( $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] ) ? $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] : '' ;

							$curr_fo['settings']['title'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_title'][$n];
							$curr_fo['settings']['include'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_include'][$n];
							$curr_fo['settings']['orderby'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_orderby'][$n];
							$curr_fo['settings']['order'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_order'][$n];
							$curr_fo['settings']['limit'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_limit'][$n];
							$curr_fo['settings']['multi'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_multiselect'][$n];
							$curr_fo['settings']['relation'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_relation'][$n];
							$curr_fo['settings']['adoptive'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_adoptive'][$n];
							$curr_fo['settings']['none'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_none'][$n];

							$curr_fo['settings']['hierarchy'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy'][$n];
							$curr_fo['settings']['hierarchy_mode'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy_mode'][$n];
							$curr_fo['settings']['mode'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_mode'][$n];
							$curr_fo['settings']['style'] = $curr_options['wc_settings_prdctfltr_advanced_filters']['pfa_style'][$n];
							$curr_fo['settings']['customization'] = $checked_customization;

						}

						if ( $total !== 0 && $curr_fo['settings']['adoptive'] == 'yes' && $curr_options['wc_settings_prdctfltr_adoptive_style'] == 'pf_adptv_default' && ( isset( $output_terms ) && ( !isset( $output_terms[$curr_fo['filter']] ) || isset( $output_terms[$curr_fo['filter']] ) && empty( $output_terms[$curr_fo['filter']]) ) === true ) ) {
							continue;
						}

						if ( $curr_fo['settings']['orderby'] == 'number' ) {
							$curr_term_args = array(
								'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
								'orderby' => 'slug'
							);
							$catalog_categories = WC_Prdctfltr::prdctfltr_get_terms( $curr_fo['filter'], $curr_term_args );
							$pf_sort_args = array(
								'order' => ( isset( $curr_fo['settings']['order'] ) ? $curr_fo['settings']['order'] : 'ASC' )
							);
							$catalog_categories = WC_Prdctfltr::prdctfltr_sort_terms_naturally( $catalog_categories, $pf_sort_args );
						}
						else {
							$curr_term_args = array(
								'hide_empty' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_hideempty'],
								'orderby' => ( $curr_fo['settings']['orderby'] !== '' ? $curr_fo['settings']['orderby'] : 'name' ),
								'order' => ( $curr_fo['settings']['order']!== '' ? $curr_fo['settings']['order'] : 'ASC' )
							);
							$catalog_categories = WC_Prdctfltr::prdctfltr_get_terms( $curr_fo['filter'], $curr_term_args );
						}

						if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ) {

							$curr_cat_selected = array();

							if ( isset( $pf_activated[$curr_fo['filter']] ) ) {
								$curr_cat_selected = array_map( 'strtolower', $pf_activated[$curr_fo['filter']] );
							}

							if ( !isset( $prdctfltr_global['sc_init'] ) && empty( $curr_cat_selected ) && isset( $prdctfltr_global['active_permalinks'][$curr_fo['filter']] ) ) {
								$curr_cat_selected = array_map( 'strtolower', $prdctfltr_global['active_permalinks'][$curr_fo['filter']] );
							}

							if ( !empty( $curr_cat_selected ) ) {
								$curr_cat_selected = array_map( 'strtolower', $curr_cat_selected );
							}

							$curr_term_subonly = '';
							if ( isset( $curr_fo['settings']['mode'] ) && $curr_fo['settings']['mode'] == 'subonly' ) {
								$curr_term_subonly = ' prdctfltr_subonly';
							}
							if ( isset( $curr_fo['settings']['mode'] ) && $curr_fo['settings']['mode'] == 'subcategories' ) {
								$curr_term_subonly = ' prdctfltr_subonly';
							}

							$curr_include = array_map( 'strtolower', $curr_fo['settings']['include'] );
							if ( !empty( $curr_include ) ) {
								$curr_include = array_map( 'strtolower', $curr_include );
							}
							else {
								foreach ( $catalog_categories as $term ) {
									$curr_include[] = strtolower( $term->slug );
								}
							}

							$curr_include = WC_Prdctfltr::prdctfltr_wpml_translate_terms( $curr_include, $curr_fo['filter'] );

							if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' ) {
								$catalog_categories_sorted = array();
								WC_Prdctfltr::prdctfltr_sort_terms_hierarchicaly( $catalog_categories, $catalog_categories_sorted );
								$catalog_categories = $catalog_categories_sorted;
							}

							if ( $curr_fo['settings']['customization'] !== '' ) {
								$language = WC_Prdctfltr::prdctfltr_wpml_language();

								if ( isset( $language ) && $language !== false ) {
									$get_customization = get_option( $curr_fo['settings']['customization'] . '_' . $language, '' );
									if ( $get_customization == '' ) {
										$get_customization = get_option( $curr_fo['settings']['customization'], '' );
									}
								}
								else {
									$get_customization = get_option( $curr_fo['settings']['customization'], '' );
								}
								

								if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
									$ctcid = $curr_fo['settings']['customization'];
									$curr_term_customization = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $ctcid;

									$customization = $get_customization;
									if ( $customization['style'] == 'text' ) {
										WC_Prdctfltr::add_customized_terms_css( $ctcid, $customization );
									}
								}
							}
							if ( !isset( $customization ) ) {
								$customization = array();
								$curr_term_customization = '';
								$curr_fo['settings']['customization'] = '';
							}

							if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' ) {
								$curr_term_style = 'pf_attr_text';
								if ( isset( $customization['style'] ) && $customization['style'] !== 'select' ) {
									$curr_fo['settings']['customization'] = '';
									$curr_term_customization = '';
									$customization = null;
								}
							}

							if ( empty( $customization ) && isset($curr_fo['settings']['style'] ) ) {
								$curr_term_style = $curr_fo['settings']['style'];
							}
							else {
								$curr_term_style = 'pf_attr_text';
							}

							$curr_term_multi = $curr_fo['settings']['multi'] == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single';
							$curr_term_adoptive = $curr_fo['settings']['adoptive'] == 'yes' ? ' prdctfltr_adoptive' : '';
							$curr_term_relation = $curr_fo['settings']['relation'] == 'AND' ? ' prdctfltr_merge_terms' : '';
							$curr_term_expand = isset( $curr_fo['settings']['hierarchy_mode'] ) && $curr_fo['settings']['hierarchy_mode'] == 'yes' ? ' prdctfltr_expand_parents' : '';
							$curr_limit = intval( $curr_fo['settings']['limit'] );

							$tax_val = '';
							$tax_val = isset( $prdctfltr_global['taxonomies_data'][$curr_fo['filter'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['taxonomies_data'][$curr_fo['filter'].'_string'] ) . '"' : '';
							if ( $tax_val == '' && !empty( $curr_cat_selected ) ) {
								$tax_val = isset( $prdctfltr_global['permalinks_data'][$curr_fo['filter'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['permalinks_data'][$curr_fo['filter'].'_string'] ) . '"' : '';
							}

						?>
							<div class="prdctfltr_filter prdctfltr_attributes prdctfltr_<?php echo $curr_el; ?> <?php echo $curr_term_style; ?><?php echo $curr_term_multi; ?><?php echo $curr_term_customization; ?><?php echo $curr_term_adoptive; ?><?php echo $curr_term_relation; ?><?php echo $curr_term_expand; ?><?php echo $curr_term_subonly; ?>" data-filter="<?php echo $curr_fo['filter']; ?>" data-limit="<?php echo $curr_limit !== 0 ? $curr_limit-1 : '0';?>">
								<input name="<?php echo $curr_fo['filter']; ?>" type="hidden"<?php echo ( !empty( $curr_cat_selected ) ? $tax_val : '' ); ?> />
								<?php
									if ( isset($prdctfltr_global['widget_search']) ) {
										$pf_before_title = $before_title . '<span class="prdctfltr_widget_title">';
										$pf_after_title = '</span>' . $after_title;
									}
									else {
										$pf_before_title = '<span class="prdctfltr_regular_title">';
										$pf_after_title = '</span>';
									}

									echo $pf_before_title;

									if ( $curr_options['wc_settings_prdctfltr_disable_showresults'] == 'no' && ( $curr_styles[5] == 'prdctfltr_disable_bar' || isset( $prdctfltr_global['widget_search'] ) ) ) {

										if ( !empty( $curr_cat_selected ) && array_intersect( $curr_cat_selected, $curr_include ) ) {
											$get_pf_titles = '';
											$i=0;
											foreach( $curr_cat_selected as $selected ) {
												if ( isset( $prdctfltr_global['sc_query'] ) && is_array( $prdctfltr_global['sc_query'] ) && isset( $prdctfltr_global['sc_query'][$curr_fo['filter']] ) && is_array( $prdctfltr_global['sc_query'][$curr_fo['filter']] ) ) {
													if ( array_key_exists( $curr_fo['filter'], $prdctfltr_global['sc_query'] ) && in_array( $selected, $prdctfltr_global['sc_query'][$curr_fo['filter']] ) ) {
														continue;
													}
												}

/*
												if ( isset( $prdctfltr_global['active_permalinks'] ) ) {
													if ( array_key_exists( $curr_fo['filter'], $prdctfltr_global['active_permalinks'] ) && in_array( $selected, $prdctfltr_global['active_permalinks'][$curr_fo['filter']] ) ) {
														continue;
													}
												}
*/

												$curr_selected = isset( $pf_activated[$curr_fo['filter']] ) ? $pf_activated[$curr_fo['filter']] : array();

												$pf_attr_title = '<a href="#" class="prdctfltr_title_remove" data-key="' . ( $curr_fo['filter'] == 'characteristics' ? 'char' : $curr_fo['filter'] ) . '"><i class="prdctfltr-delete"></i></a> <span>';

												$pf_i=0;
												$pf_attr_active = false;

												foreach( $curr_selected as $selected ) {

/*													if ( isset( $dont_show[$curr_fo['filter']] ) && in_array( $selected, $dont_show[$curr_fo['filter']] ) ) {
														continue;
													}*/

													if ( term_exists( $selected, $curr_fo['filter'] ) !== null ) {
														$curr_term = get_term_by( 'slug', $selected, $curr_fo['filter'] );

														$pf_attr_title .= ( $pf_i !== 0 ? ', ' : '' ) . $curr_term->name;

														$pf_i++;
														$pf_attr_active = true;
													}

												}

												$pf_attr_title .= '</span>';

											}

											if ( isset( $pf_attr_active ) ) {
												echo $pf_attr_title . ' / ';
											}

						/*						if ( in_array( $selected, $curr_include ) ) {
													$curr_term = get_term_by('slug', $selected, $curr_fo['filter']);
													$get_pf_titles .= ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
													$i++;
												}
											}
											if ( $get_pf_titles !== '' ) {
												echo '<a href="#" data-key="' . $curr_fo['filter'] . '"><i class="prdctfltr-delete"></i></a> <span>' . $get_pf_titles . '</span> / ';
											}*/
										}

									}

									if ( $curr_fo['settings']['title'] != '' ) {
										echo $curr_fo['settings']['title'];
									}
									else {
										if ( substr( $curr_fo['filter'], 0, 3 ) == 'pa_' ) {
											echo wc_attribute_label( $curr_fo['filter'] );
										}
										else {
											if ( $curr_fo['filter'] == 'product_cat' ) {
												_e( 'Categories', 'prdctfltr' );
											}
											else if ( $curr_fo['filter'] == 'product_tag') {
												_e( 'Tags', 'prdctfltr' );
											}
											else if ( $curr_fo['filter'] == 'characteristics' ) {
												_e( 'Characteristics', 'prdctfltr' );
											}
											else {
												$curr_term = get_taxonomy( $curr_fo['filter'] );
												echo $curr_term->label;
											}
										}
									}

								?>
								<i class="prdctfltr-down"></i>
								<?php echo $pf_after_title; ?>
								<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
								<?php
									if ( $curr_fo['settings']['none'] == 'no' ) {
										if ( $curr_fo['settings']['customization'] == '' ) {
											switch ( $curr_term_style ) {
												case 'pf_attr_text':
													$curr_blank_element = __('None' , 'prdctfltr');
												break;
												case 'pf_attr_imgtext':
													$curr_blank_element = '<img src="' . WC_Prdctfltr::$url_path . '/lib/images/pf-transparent.gif" />';
													$curr_blank_element .= __('None' , 'prdctfltr');
												break;
												case 'pf_attr_img':
													$curr_blank_element = '<img src="' . WC_Prdctfltr::$url_path . '/lib/images/pf-transparent.gif" />';
													$curr_blank_element .= '<span class="prdctfltr_tooltip"><span>' . __('None' , 'prdctfltr') . '</span></span>';
												break;
												default :
													$curr_blank_element = __('None' , 'prdctfltr');
												break;
											}
										}
										else {
											$curr_blank_element = WC_Prdctfltr::get_customized_term( '', apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) ), false, $customization );
										}

										printf('<label class="prdctfltr_ft_none"><input type="checkbox" value="" /><span>%1$s</span></label>', $curr_blank_element );
									}

									foreach ( $catalog_categories as $term ) {

										$decode_slug = $term->slug;

										if ( !empty( $curr_include ) && !in_array( $decode_slug, $curr_include ) ) {
											continue;
										}

										if ( isset( $term->children ) ) {
											$pf_children = $term->children;
										}
										else {
											$pf_children = array();
										}

										if ( $curr_fo['settings']['customization'] == '' ) {
											$term_count_real = WC_Prdctfltr::published_term_count( $term->term_id, $curr_fo['filter'] );

											$term_count = ( $curr_options['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? '' : ' <span class="prdctfltr_count">' . ( isset($output_terms[$curr_fo['filter']]) && isset($output_terms[$curr_fo['filter']][$term->slug]) && $output_terms[$curr_fo['filter']][$term->slug] != $term_count_real ? WC_Prdctfltr::get_term_count( $output_terms[$curr_fo['filter']][$term->slug], $term_count_real ) : $term_count_real ) . '</span>' );

											switch ( $curr_term_style ) {
												case 'pf_attr_text':
													$curr_insert = $term->name . $term_count;
												break;
												case 'pf_attr_imgtext':
													$curr_img = wp_get_attachment_image( get_woocommerce_term_meta($term->term_id, $curr_fo['filter'] . '_thumbnail_id_photo', true), 'shop_thumbnail' );

													$curr_insert = ( $curr_img !== '' ? $curr_img : '<img src="' . WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif" />');
													$curr_insert .= $term->name . $term_count;
												break;
												case 'pf_attr_img':
													$curr_img = wp_get_attachment_image( get_woocommerce_term_meta($term->term_id, $curr_fo['filter'] . '_thumbnail_id_photo', true), 'shop_thumbnail' );

													$curr_insert = ( $curr_img !== '' ? $curr_img : '<img src="' . WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif" />');
													$curr_insert .= '<span class="prdctfltr_tooltip"><span>' . $term->name . $term_count . '</span></span>';
												break;
												default :
													$curr_insert = $term->name;
												break;
											}
										}
										else {
											$term_count_real = WC_Prdctfltr::published_term_count( $term->term_id, $curr_fo['filter'] );

											$term_count = ( $curr_options['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? false : ( isset($output_terms[$curr_fo['filter']]) && isset($output_terms[$curr_fo['filter']][$term->slug]) && $output_terms[$curr_fo['filter']][$term->slug] != $term_count_real ? WC_Prdctfltr::get_term_count( $output_terms[$curr_fo['filter']][$term->slug], $term_count_real ) : $term_count_real ) );

											$curr_insert = WC_Prdctfltr::get_customized_term( $term->slug, $term->name, $term_count, $customization );
										}

										$pf_adoptive_class = '';

										if ( $curr_fo['settings']['adoptive'] == 'yes' && isset( $output_terms[$curr_fo['filter']] ) && !empty( $output_terms[$curr_fo['filter']] ) && !array_key_exists( $term->slug, $output_terms[$curr_fo['filter']] ) ) {
											$pf_adoptive_class = ' pf_adoptive_hide';
										}

										printf('<label class="%6$s%4$s%7$s%8$s"><input type="checkbox" value="%1$s"%3$s /><span>%2$s</span>%5$s</label>', $decode_slug, $curr_insert, ( in_array( $decode_slug, $curr_cat_selected ) ? ' checked' : '' ), ( in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), ( !empty($pf_children) ? '<i class="prdctfltr-plus"></i>' : '' ), $pf_adoptive_class, ( !empty($pf_children) && in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $term->slug ) );

										if ( isset( $curr_fo['settings']['hierarchy'] ) && $curr_fo['settings']['hierarchy'] == 'yes' && !empty( $pf_children ) ) {

											printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $term->slug );

											foreach( $pf_children as $sub ) {

												$term_count_real = WC_Prdctfltr::published_term_count( $sub->term_id, $curr_fo['filter'] );

												$pf_adoptive_class = '';
												if ( $curr_fo['settings']['adoptive'] == 'yes' && isset($output_terms[$curr_fo['filter']]) && !empty($output_terms[$curr_fo['filter']]) && !array_key_exists($sub->slug, $output_terms[$curr_fo['filter']]) ) {
													$pf_adoptive_class = ' pf_adoptive_hide';
												}

												$decode_slug = $sub->slug;

												$curr_insert = $sub->name . ( $curr_options['wc_settings_prdctfltr_show_counts'] == 'no' || $sub->count == '0' ? '' : ' <span class="prdctfltr_count">' . ( isset($output_terms[$curr_fo['filter']]) && isset($output_terms[$curr_fo['filter']][$sub->slug]) && $output_terms[$curr_fo['filter']][$sub->slug] != $term_count_real ? WC_Prdctfltr::get_term_count( $output_terms[$curr_fo['filter']][$sub->slug], $term_count_real ) : $term_count_real ) . '</span>' );

												printf('<label class="%6$s%4$s%7$s%8$s"><input type="checkbox" value="%1$s"%3$s /><span>%2$s</span>%5$s</label>', $decode_slug, $curr_insert, ( in_array( $decode_slug, $curr_cat_selected ) ? ' checked' : '' ), ( in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), ( !empty($sub->children) ? '<i class="prdctfltr-plus"></i>' : '' ), $pf_adoptive_class, ( !empty($sub->children) && in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $sub->slug ) );

												if ( !empty($sub->children) ) {

													printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $sub->slug );

													foreach( $sub->children as $subsub ) {

														$term_count_real = WC_Prdctfltr::published_term_count( $subsub->term_id, $curr_fo['filter'] );

														$pf_adoptive_class = '';
														if ( $curr_fo['settings']['adoptive'] == 'yes' && isset($output_terms[$curr_fo['filter']]) && !empty($output_terms[$curr_fo['filter']]) && !array_key_exists($subsub->slug, $output_terms[$curr_fo['filter']]) ) {
															$pf_adoptive_class = ' pf_adoptive_hide';
														}

														$decode_slug = $subsub->slug;

														$curr_insert = $subsub->name . ( $curr_options['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? '' : ' <span class="prdctfltr_count">' . ( isset($output_terms[$curr_fo['filter']]) && isset($output_terms[$curr_fo['filter']][$subsub->slug]) && $output_terms[$curr_fo['filter']][$subsub->slug] != $term_count_real ? WC_Prdctfltr::get_term_count( $output_terms[$curr_fo['filter']][$subsub->slug], $term_count_real ) : $term_count_real ) . '</span>' );

														printf('<label class="%6$s%4$s%7$s%8$s"><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span>%5$s</label>', $decode_slug, $curr_insert, ( in_array( $decode_slug, $curr_cat_selected ) ? 'checked' : '' ), ( in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), ( !empty($subsub->children) ? '<i class="prdctfltr-plus"></i>' : '' ), $pf_adoptive_class, ( !empty($subsub->children) && in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $subsub->slug ) );

														if ( !empty($subsub->children) ) {

															printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $subsub->slug );

															foreach( $subsub->children as $subsubsub ) {

																$term_count_real = WC_Prdctfltr::published_term_count( $subsubsub->term_id, $curr_fo['filter'] );

																$pf_adoptive_class = '';
																if ( $curr_fo['settings']['adoptive'] == 'yes' && isset($output_terms[$curr_fo['filter']]) && !empty($output_terms[$curr_fo['filter']]) && !array_key_exists($subsubsub->slug, $output_terms[$curr_fo['filter']]) ) {
																	$pf_adoptive_class = ' pf_adoptive_hide';
																}

																$decode_slug = $subsubsub->slug;

																$curr_insert = $subsubsub->name . ( $curr_options['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? '' : ' <span class="prdctfltr_count">' . ( isset($output_terms[$curr_fo['filter']]) && isset($output_terms[$curr_fo['filter']][$subsubsub->slug]) && $output_terms[$curr_fo['filter']][$subsubsub->slug] != $term_count_real ? WC_Prdctfltr::get_term_count( $output_terms[$curr_fo['filter']][$subsubsub->slug], $term_count_real ) : $term_count_real ) . '</span>' );

																printf('<label class="%5$s%4$s%6$s%7$s"><input type="checkbox" value="%1$s"%3$s /><span>%2$s</span></label>', $decode_slug, $curr_insert, ( in_array( $decode_slug, $curr_cat_selected ) ? ' checked' : '' ), ( in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), $pf_adoptive_class, ( !empty($subsubsub->children) && in_array( $decode_slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $subsubsub->slug ) );

															}

														echo '</div>';

														}

													}

													echo '</div>';

												}

											}

											echo '</div>';

										}
									}
								?>
								</div>
							</div>
					<?php
						}

						if ( $curr_el == 'advanced' ) {
							$n++;
						}

					break;

					endswitch;

				$q++;

				endforeach;

				if ( !isset( $prdctfltr_global['widget_search'] ) ) {
					echo '<div class="prdctfltr_clear"></div>';
				}
			?>
			</div>
			<div class="prdctfltr_clear"></div>
		</div>
		<?php do_action( 'prdctfltr_filter_form_after', $curr_options, $pf_activated ); ?>
		<div class="prdctfltr_add_inputs">
		<?php
			if ( !in_array( 'search', $curr_elements ) && isset( $pf_activated['s'] ) ) {
				echo '<input type="hidden" name="s" value="' . $pf_activated['s'] . '" />';
			}
			if ( isset($_GET['page_id']) ) {
				echo '<input type="hidden" name="page_id" value="' . $_GET['page_id'] . '" />';
			}
			if ( isset($_GET['lang']) ) {
				echo '<input type="hidden" name="lang" value="' . $_GET['lang'] . '" />';
			}
			$curr_posttype = get_option( 'wc_settings_prdctfltr_force_product', 'no' );
			if ( $curr_posttype == 'no' ) {
				if ( !isset( $pf_activated['s'] ) && $pf_structure == '' && ( is_shop() || is_product_taxonomy() ) ) {
					echo '<input type="hidden" name="post_type" value="product" />';
				}
			}
			else {
				echo '<input type="hidden" name="post_type" value="product" />';
			}

			if ( isset( $pf_activated['orderby'] ) && !in_array( 'sort', $curr_elements ) ) {
				echo '<input type="hidden" name="orderby" value="' . $pf_activated['orderby'] . '" />';
			}

			if ( !isset( $prdctfltr_global['sc_init'] ) && !empty( $prdctfltr_global['active_permalinks'] ) ) {
				foreach ( $prdctfltr_global['active_permalinks'] as $pf_k => $pf_v ) {
					/*if ( !in_array( $pf_k, $active_filters ) ) {*/
						echo '<input type="hidden" name="' . $pf_k . '" value="' . $prdctfltr_global['permalinks_data'][$pf_k . '_string'] . '" />';
					/*}*/
					$prdctfltr_global['filter_js'][$prdctfltr_id]['adds'][$pf_k] = $prdctfltr_global['permalinks_data'][$pf_k . '_string'];
				}
			}

		?>
		</div>
	</form>
	</div>
<?php

	do_action( 'prdctfltr_filter_after', $curr_options, $pf_activated );

	if ( isset( $prdctfltr_global['categories_active'] ) && $prdctfltr_global['categories_active'] === false ) {
		add_filter( 'woocommerce_is_filtered', create_function('', 'return true;') );
	}

	//$prdctfltr_global['unique_id'] = null;

?>