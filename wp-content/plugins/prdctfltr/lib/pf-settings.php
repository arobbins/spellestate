<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Settings_Prdctfltr {

		public static function init() {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::prdctfltr_admin_scripts' );
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::prdctfltr_add_settings_tab', 49 );
			add_action( 'woocommerce_settings_tabs_settings_products_filter', __CLASS__ . '::prdctfltr_settings_tab' );
			add_action( 'woocommerce_update_options_settings_products_filter', __CLASS__ . '::prdctfltr_update_settings' );
			add_action( 'woocommerce_admin_field_pf_filter', __CLASS__ . '::prdctfltr_pf_filter', 10 );
			add_action( 'woocommerce_admin_field_pf_filter_analytics', __CLASS__ . '::prdctfltr_pf_filter_analytics', 10 );

			add_action( 'wp_ajax_prdctfltr_admin_save', __CLASS__ . '::prdctfltr_admin_save' );
			add_action( 'wp_ajax_prdctfltr_admin_load', __CLASS__ . '::prdctfltr_admin_load' );
			add_action( 'wp_ajax_prdctfltr_admin_delete', __CLASS__ . '::prdctfltr_admin_delete' );
			add_action( 'wp_ajax_prdctfltr_or_add', __CLASS__ . '::prdctfltr_or_add' );
			add_action( 'wp_ajax_prdctfltr_or_remove', __CLASS__ . '::prdctfltr_or_remove' );
			add_action( 'wp_ajax_prdctfltr_c_fields', __CLASS__ . '::prdctfltr_c_fields' );
			add_action( 'wp_ajax_prdctfltr_c_terms', __CLASS__ . '::prdctfltr_c_terms' );
			add_action( 'wp_ajax_prdctfltr_r_fields', __CLASS__ . '::prdctfltr_r_fields' );
			add_action( 'wp_ajax_prdctfltr_r_terms', __CLASS__ . '::prdctfltr_r_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms', __CLASS__ . '::set_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_new_style', __CLASS__ . '::set_terms_new' );
			add_action( 'wp_ajax_prdctfltr_set_terms_save_style', __CLASS__ . '::save_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_remove_style', __CLASS__ . '::remove_terms' );
			add_action( 'wp_ajax_prdctfltr_set_filters', __CLASS__ . '::set_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_add', __CLASS__ . '::add_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_new_style', __CLASS__ . '::set_filters_new' );
			add_action( 'wp_ajax_prdctfltr_set_filters_save_style', __CLASS__ . '::save_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_remove_style', __CLASS__ . '::remove_filters' );
			add_action( 'wp_ajax_prdctfltr_analytics_reset', __CLASS__ . '::analytics_reset' );
		}

		public static function prdctfltr_admin_scripts($hook) {

			if ( isset( $_GET['page'], $_GET['tab'] ) && ($_GET['page'] == 'wc-settings' || $_GET['page'] == 'woocommerce_settings' ) && $_GET['tab'] == 'settings_products_filter' ) {

				wp_register_style( 'prdctfltr-font', WC_Prdctfltr::$url_path .'lib/font/styles.css', false, WC_Prdctfltr::$version );
				wp_enqueue_style( 'prdctfltr-font' );

				wp_register_style( 'prdctfltr-admin', WC_Prdctfltr::$url_path .'lib/css/admin.css', false, WC_Prdctfltr::$version );
				wp_enqueue_style( 'prdctfltr-admin' );

				wp_register_script( 'prdctfltr-settings', WC_Prdctfltr::$url_path . 'lib/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), WC_Prdctfltr::$version, true );
				wp_enqueue_script( 'prdctfltr-settings' );

				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('wp-color-picker');

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				$dec_separator = get_option( 'woocommerce_price_decimal_sep' );

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'decimal_separator' => $dec_separator,
					'characteristics' => WC_Prdctfltr::$settings['wc_settings_prdctfltr_custom_tax'],
					'localization' => array(
						'activate' => __( 'Activate?', 'prdctfltr' ),
						'deactivate' => __( 'Deactivate?', 'prdctfltr' ),
						'delete' => __( 'Delete?', 'prdctfltr' ),
						'remove' => __( 'Remove?', 'prdctfltr' ),
						'remove_key' => __( 'Remove key from database?', 'prdctfltr' ),
						'add_override' => __( 'Add override?', 'prdctfltr' ),
						'remove_override' => __( 'Remove override?', 'prdctfltr' ),
						'override_notice' => __( 'Please select both term and filter preset.', 'prdctfltr' ),
						'added' => __( 'Added!', 'prdctfltr' ),
						'load' => __( 'Load?', 'prdctfltr' ),
						'saved' => __( 'Saved!', 'prdctfltr' ),
						'ajax_error' => __( 'AJAX Error!', 'prdctfltr' ),
						'missing_settings' => __( 'Missing name or settings.', 'prdctfltr' ),
						'not_selected' => __( 'Not selected!', 'prdctfltr' ),
						'deleted' => __( 'Deleted!', 'prdctfltr' ),
						'customization_save' => __( 'Customization saved! Please save your preset or the default filter!', 'prdctfltr' ),
						'customization_removed' => __( 'Removed! Please save your preset or the default filter!', 'prdctfltr' ),
						'delete_analytics' => __( 'Analytics data deleted!', 'prdctfltr' ),
						'adv_filter' => __( 'Advanced Filter', 'prdctfltr' ),
						'rng_filter' => __( 'Range Filter', 'prdctfltr' ),
						'decimal_error' =>  __( 'Use only numbers and the decimal separator!', 'prdctfltr' ) . ' ( ' . $dec_separator . ' )',
						'remove_override_single' =>  __( 'Remove Override', 'prdctfltr' ),
						'term_slug' => __( 'Term slug', 'prdctfltr' ),
						'filter_preset' => __( 'Filter Preset', 'prdctfltr' ),
						'loaded' => __( 'Loaded!', 'prdctfltr' ),
						'removed' => __( 'Removed!', 'prdctfltr' ),
						'invalid_key' => __( 'Invalid key! Cannot be removed from database! Please save your settings.', 'prdctfltr')
					)
				);
				wp_localize_script( 'prdctfltr-settings', 'prdctfltr', $curr_args );
			}

		}

		public static function prdctfltr_pf_filter_analytics($field) {

		if ( get_option( 'wc_settings_prdctfltr_use_analytics', 'no' ) == 'no' ) {
			return '';
		}

		global $woocommerce;
?>
		<tr valign="top" class="">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<div class="prdctfltr_filtering_analytics_wrapper">
			<?php
				$stats = get_option( 'wc_settings_prdctfltr_filtering_analytics_stats', array() );

				if ( empty( $stats ) ) {
					_e( 'Filtering Analytics are empty! Please enable the filtering analytics and wait for the results! Thank you!', 'prdctfltr' );
				}
				else {
					?>
					<div class="prdctfltr_filtering_analytics_settings">
						<a href="#" class="button-primary prdctfltr_filtering_analytics_reset"><?php _e( 'Reset Analytics', 'prdctfltr' ); ?></a>
					</div>
					<?php

					foreach( $stats as $k => $v ) {
						$total_count = 0
					?>
						<div class="prdctfltr_filtering_analytics">
							<h3 class="prdctfltr_filtering_analytics_title">
							<?php
								$mode = 'default';
								if ( substr( $k, 0, 3 ) == 'pa_' ) {
									$label = wc_attribute_label( $k );
								}
								else {
									if ( $k == 'product_cat' ) {
										$label = __( 'Categories', 'prdctfltr' );
									}
									else if ( $k == 'product_tag') {
										$label = __( 'Tags', 'prdctfltr' );
									}
									else if ( $k == 'characteristics' ) {
										$label = __( 'Characteristics', 'prdctfltr' );
									}
									else if ( is_taxonomy( $k ) ) {
										$curr_term = get_taxonomy( $k );
										$label = $curr_term->name;
									}
								}

								if ( !empty( $v ) && is_array( $v ) ) {
									foreach( $v as $vk => $vv ) {
										$term = get_term_by('slug', $vk, $k);
										$term_name = ucfirst( $term->name ) . ' (' . $v[$vk] .')';

										$v[$term_name] = $v[$vk];
										$total_count = $total_count + $v[$vk];
										unset($v[$vk]);
									}
									echo __( 'Filter', 'prdctfltr' ) . ' <em>' . ucfirst( $label ) . '</em> - ' . __( 'Total hits count:') . ' ' . $total_count;
								}

							?>
							</h3>
							<div id="prdctfltr_filtering_analytics_<?php echo sanitize_title( $k ); ?>" class="prdctfltr_filtering_analytics_chart" data-chart-title="<?php echo esc_attr( __( 'Filtering data for taxonomy', 'prdctfltr') . ': ' . $k ); ?>" data-chart="<?php echo esc_attr( json_encode( $v ) ); ?>"></div>
						</div>
					<?php
					}
			?>
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						(function($){
						"use strict";

							google.load('visualization', '1.0', {'packages':['corechart']});

							google.setOnLoadCallback(drawCharts);

							function drawCharts() {

								$('.prdctfltr_filtering_analytics_chart').each( function() {

									var el = $(this).attr('id');
									var chartData = $.parseJSON($(this).attr('data-chart'));
									var chartDataTitle = $(this).attr('data-chart-title');

									var chartArray = [];
									for (var key in chartData) {
										if (chartData.hasOwnProperty(key)) {
											chartArray.push([key, chartData[key]]);
										}
									};

									var data = new google.visualization.DataTable();
									data.addColumn('string', 'Term');
									data.addColumn('number', 'Count');
									data.addRows(chartArray);

									var options = {'title':chartDataTitle,'is3D':true,'chartArea':{'width':'100%','height':'80%'},'legend':{'position':'bottom'}};

									var chart = new google.visualization.PieChart(document.getElementById(el));
									chart.draw(data, options);

								});

							}
						})(jQuery);
					</script>
			<?php
				}
			?>
				</div>
			</td>
		</tr>
<?php
		}

		public static function prdctfltr_pf_filter($field) {

		global $woocommerce;
	?>
		<tr valign="top">
			<th scope="row" class="titledesc" style="display:none;">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<?php

					$pf_filters_selected = get_option( 'wc_settings_prdctfltr_active_filters', array() );

					if ( empty( $pf_filters_selected ) ) {
						$curr_selected = get_option( 'wc_settings_prdctfltr_selected', array( 'sort','price','cat' ) );
						$curr_selected_attr = get_option( 'wc_settings_prdctfltr_attributes', array() );
						$pf_filters_selected = array_merge( $curr_selected, $curr_selected_attr );
					}

					$curr_filters = array(
						'sort' => __('Sort By', 'prdctfltr'),
						'price' => __('By Price', 'prdctfltr'),
						'cat' => __('By Categories', 'prdctfltr'),
						'tag' => __('By Tags', 'prdctfltr'),
						'char' => __('By Characteristics', 'prdctfltr'),
						'instock' => __('In Stock Filter', 'prdctfltr'),
						'per_page' => __('Products Per Page', 'prdctfltr'),
						'search' => __('Search Fitler', 'prdctfltr')
					);

					if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'no' ) {
						unset( $curr_filters['char'] );
					}

					$curr_attr = array();
					if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
					foreach ( $attribute_taxonomies as $tax ) {
						$curr_label = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
						$curr_attr['pa_' . $tax->attribute_name] = ucfirst($curr_label);
						}
					}

					$pf_filters = ( is_array( $curr_filters ) ? $curr_filters : array() ) + $curr_attr;

				?>
				<div class="form-field prdctfltr_customizer_static">
					<div class="pf_element" data-filter="basic">
						<span><?php _e( 'General Settings', 'prdctfltr'); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
					<div class="pf_element" data-filter="style">
						<span><?php _e( 'Filter Style', 'prdctfltr'); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
				</div>
				<h3><?php _e( 'Available Filters', 'prdctfltr' ); ?></h3>
				<p class="form-field prdctfltr_customizer_fields">
				<?php
					foreach ( $pf_filters as $k => $v ) {
						if ( in_array($k, $pf_filters_selected ) ) {
							$add['class'] = ' pf_active';
							$add['icon'] = '<i class="prdctfltr-eye"></i>';
						}
						else {
							$add['class'] = '';
							$add['icon'] = '<i class="prdctfltr-eye-disabled"></i>';
						}
				?>
					<a href="#" class="prdctfltr_c_add_filter<?php echo $add['class']; ?>" data-filter="<?php echo $k; ?>">
						<?php echo $add['icon']; ?> 
						<span><?php echo $v; ?></span>
					</a>
				<?php
					}
				?>
					<a href="#" class="prdctfltr_c_add pf_advanced"><i class="prdctfltr-plus"></i> <span><?php _e('Add advanced filter', 'prdctfltr'); ?></span></a>
					<a href="#" class="prdctfltr_c_add pf_range"><i class="prdctfltr-plus"></i> <span><?php _e('Add range filter', 'prdctfltr'); ?></span></a>
				</p>
				<div class="form-field prdctfltr_customizer">
				<?php

					if ( isset($_POST['pfa_taxonomy']) ) {

						$pf_filters_advanced = array();

						for($i = 0; $i < count($_POST['pfa_taxonomy']); $i++ ) {
							$pf_filters_advanced['pfa_title'][$i] = $_POST['pfa_title'][$i];
							$pf_filters_advanced['pfa_taxonomy'][$i] = $_POST['pfa_taxonomy'][$i];
							$pf_filters_advanced['pfa_include'][$i] = ( isset($_POST['pfa_include'][$i]) ? $_POST['pfa_include'][$i] : array() );
							$pf_filters_advanced['pfa_orderby'][$i] = ( isset($_POST['pfa_orderby'][$i]) ? $_POST['pfa_orderby'][$i] : '' );
							$pf_filters_advanced['pfa_order'][$i] = ( isset($_POST['pfa_order'][$i]) ? $_POST['pfa_order'][$i] : '' );
							$pf_filters_advanced['pfa_multiselect'][$i] = ( isset($_POST['pfa_multiselect'][$i]) ? $_POST['pfa_multiselect'][$i] : 'no' );
							$pf_filters_advanced['pfa_relation'][$i] = ( isset($_POST['pfa_relation'][$i]) ? $_POST['pfa_relation'][$i] : 'IN' );
							$pf_filters_advanced['pfa_adoptive'][$i] = ( isset($_POST['pfa_adoptive'][$i]) ? $_POST['pfa_adoptive'][$i] : 'no' );
							$pf_filters_advanced['pfa_none'][$i] = ( isset($_POST['pfa_none'][$i]) ? $_POST['pfa_none'][$i] : 'no' );
							$pf_filters_advanced['pfa_limit'][$i] = ( isset($_POST['pfa_limit'][$i]) ? $_POST['pfa_limit'][$i] : '' );
							$pf_filters_advanced['pfa_hierarchy'][$i] = ( isset($_POST['pfa_hierarchy'][$i]) ? $_POST['pfa_hierarchy'][$i] : 'no' );
							$pf_filters_advanced['pfa_hierarchy_mode'][$i] = ( isset($_POST['pfa_hierarchy_mode'][$i]) ? $_POST['pfa_hierarchy_mode'][$i] : 'no' );
							$pf_filters_advanced['pfa_mode'][$i] = ( isset($_POST['pfa_mode'][$i]) ? $_POST['pfa_mode'][$i] : 'showall' );
							$pf_filters_advanced['pfa_style'][$i] = ( isset($_POST['pfa_style'][$i]) ? $_POST['pfa_style'][$i] : 'pf_attr_text' );
							$pf_filters_advanced['pfa_term_customization'][$i] = ( isset($_POST['pfa_term_customization'][$i]) ? $_POST['pfa_term_customization'][$i] : '' );
						}

					}
					else {
						$pf_filters_advanced = get_option('wc_settings_prdctfltr_advanced_filters');
					}

					if ( isset($_POST['pfr_taxonomy']) ) {

						$pf_filters_range = array();

						for($i = 0; $i < count($_POST['pfr_taxonomy']); $i++ ) {
							$pf_filters_range['pfr_title'][$i] = $_POST['pfr_title'][$i];
							$pf_filters_range['pfr_taxonomy'][$i] = $_POST['pfr_taxonomy'][$i];
							$pf_filters_range['pfr_include'][$i] = ( isset($_POST['pfr_include'][$i]) ? $_POST['pfr_include'][$i] : array() );
							$pf_filters_range['pfr_orderby'][$i] = ( isset($_POST['pfr_orderby'][$i]) ? $_POST['pfr_orderby'][$i] : '' );
							$pf_filters_range['pfr_order'][$i] = ( isset($_POST['pfr_order'][$i]) ? $_POST['pfr_order'][$i] : '' );
							$pf_filters_range['pfr_style'][$i] = ( isset($_POST['pfr_style'][$i]) ? $_POST['pfr_style'][$i] : 'flat' );
							$pf_filters_range['pfr_grid'][$i] = ( isset($_POST['pfr_grid'][$i]) ? $_POST['pfr_grid'][$i] : 'no' );
							$pf_filters_range['pfr_adoptive'][$i] = ( isset($_POST['pfr_adoptive'][$i]) ? $_POST['pfr_adoptive'][$i] : 'no' );
							$pf_filters_range['pfr_custom'][$i] = ( isset($_POST['pfr_custom'][$i]) ? stripslashes( $_POST['pfr_custom'][$i] ) : '' );
						}

					}
					else {
						$pf_filters_range = get_option('wc_settings_prdctfltr_range_filters');
					}

					if ( $pf_filters_advanced === false ) {
						$pf_filters_advanced = array();
					}

					if ( $pf_filters_range === false ) {
						$pf_filters_range = array();
					}

					$i=0;$q=0;

					foreach ( $pf_filters_selected as $v ) {
						if ( $v == 'advanced' && !empty( $pf_filters_advanced ) && isset( $pf_filters_advanced['pfa_taxonomy'][$i] ) ) {
					?>
							<div class="pf_element adv" data-filter="advanced" data-id="<?php echo $i; ?>">
								<span><?php _e( 'Advanced Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h3><?php _e( 'Advanced Fitler', 'prdctfltr' ); ?></h3>
									<p><?php _e( 'Setup advanced filter.', 'prdctfltr' ); ?></p>
									<table cass="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_title_%1$s">%2$s</label>', $i, __( 'Override Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, isset( $pf_filters_advanced['pfa_title'][$i] ) ? $pf_filters_advanced['pfa_title'][$i] : '' );
													?>
													<span class="description"><?php _e( 'Enter title for the current advanced filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													$taxonomies = get_object_taxonomies( 'product', 'object' );
													printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $i, __( 'Select Taxonomy', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $i) ;
														foreach ( $taxonomies as $k => $v ) {
															if ( in_array( $k, array( 'product_type' ) ) ) {
																continue;
															}
															echo '<option value="' . $k . '"' . ( $pf_filters_advanced['pfa_taxonomy'][$i] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php _e( 'Select current advanced filter product taxonomy.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_include_%1$s">%2$s</label>', $i, __( 'Include Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
													<?php
														$catalog_attrs = get_terms( $pf_filters_advanced['pfa_taxonomy'][$i], array( 'hide_empty' => 0 ) );
														$curr_options = '';
														if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
															foreach ( $catalog_attrs as $term ) {
																$decode_slug = WC_Prdctfltr::prdctfltr_utf8_decode($term->slug);
																$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $decode_slug, $term->name, ( in_array($decode_slug, $pf_filters_advanced['pfa_include'][$i]) ? ' selected="selected"' : '' ) );
															}
														}
														printf( '<select name="pfa_include[%2$s][]" id="pfa_include_%2$s" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select terms to include. Use CTRL+Click to select terms or deselect all.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_style_%1$s">%2$s</label>', $i, __( 'Appearance', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'pf_attr_text' => __( 'Text', 'prdctfltr' ),
															'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
															'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset($pf_filters_advanced['pfa_style'][$i]) && $pf_filters_advanced['pfa_style'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $i, __( 'Term Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$orderby_params = array(
															'' => __( 'None', 'prdctfltr' ),
															'id' => __( 'ID', 'prdctfltr' ),
															'name' => __( 'Name', 'prdctfltr' ),
															'number' => __( 'Number', 'prdctfltr' ),
															'slug' => __( 'Slug', 'prdctfltr' ),
															'count' => __( 'Count', 'prdctfltr' )
														);

														foreach ( $orderby_params as $k => $v ) {
															$selected = ( isset($pf_filters_advanced['pfa_orderby'][$i]) && $pf_filters_advanced['pfa_orderby'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select current advanced terms order by.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_order_%1$s">%2$s</label>', $i, __( 'Term Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$order_params = array(
															'ASC' => __( 'ASC', 'prdctfltr' ),
															'DESC' => __( 'DESC', 'prdctfltr' )
														);

														foreach ( $order_params as $k => $v ) {
															$selected = ( isset($pf_filters_advanced['pfa_order'][$i]) && $pf_filters_advanced['pfa_order'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_limit_%1$s">%2$s</label>', $i, __( 'Limit Terms', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-number">
													<?php
														printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $i, isset( $pf_filters_advanced['pfa_limit'][$i] ) ? $pf_filters_advanced['pfa_limit'][$i] : '' ); ?>
													<span class="description"><?php _e( 'Limit number of terms to be shown. If limit is set, terms with most posts will be shown first.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy'][$i] ) && $pf_filters_advanced['pfa_hierarchy'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable hierarchy on current advanced filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_mode_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy_mode'][$i] ) && $pf_filters_advanced['pfa_hierarchy_mode'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_mode_%1$s">%2$s</label>', $i, __( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'showall' => __( 'Show all', 'prdctfltr' ),
															'subonly' => __( 'Keep only child terms', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset($pf_filters_advanced['pfa_mode'][$i]) && $pf_filters_advanced['pfa_mode'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Multi Select', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Multi Select', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_multiselect_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_multiselect'][$i] ) && $pf_filters_advanced['pfa_multiselect'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable multi-select on current advanced filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_relation_%1$s">%2$s</label>', $i, __( 'Term Relation', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
															'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset($pf_filters_advanced['pfa_relation'][$i]) && $pf_filters_advanced['pfa_relation'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_adoptive_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_adoptive'][$i] ) && $pf_filters_advanced['pfa_adoptive'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to enable adoptive filtering on current advanced filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Disable None', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Disable None', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_none_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset($pf_filters_advanced['pfa_none'][$i]) && $pf_filters_advanced['pfa_none'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															_e( 'Check this option to hide none on current advanced filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $i, __( 'Style Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" class="pf_term_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, ( isset($pf_filters_advanced['pfa_term_customization'][$i]) ? $pf_filters_advanced['pfa_term_customization'][$i] : '' ) );
													?>
													<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customizations.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$i++;
						}
						else if ( $v == 'range' && !empty( $pf_filters_range ) && isset( $pf_filters_range['pfr_taxonomy'][$q] ) ) {
					?>
							<div class="pf_element rng" data-filter="range" data-id="<?php echo $q; ?>">
								<span><?php _e('Range Filter', 'prdctfltr'); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h3><?php _e( 'Range Fitler', 'prdctfltr' ); ?></h3>
									<p><?php _e( 'Setup advanced filter.', 'prdctfltr' ); ?></p>
									<table cass="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_title_%1$s">%2$s</label>', $q, __( 'Override Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $q, $pf_filters_range['pfr_title'][$q] );
													?>
													<span class="description"><?php _e( 'Enter title for the current advanced filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $q, __( 'Select Range', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$taxonomies = get_object_taxonomies( 'product', 'object' );
														printf('<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $q );
														echo '<option value="price"' . ( $pf_filters_range['pfr_taxonomy'][$q] == 'price' ? ' selected="selected"' : '' ) . '>' . __( 'Price range', 'prdctfltr' ) . '</option>';
														foreach ( $taxonomies as $k => $v ) {
															if ( substr( $k, 0, 3 ) == 'pa_' && $k !== 'product_type' ) {
																$curr_label = wc_attribute_label( $v->name );
																$curr_value = $v->name;
															}
															else {
																$curr_label = $v->label;
																$curr_value = $k;
															}
															echo '<option value="' . $curr_value . '"' . ( $pf_filters_range['pfr_taxonomy'][$q] == '' . $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php _e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_include_%1$s">%2$s</label>', $q, __( 'Include Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
													<?php

														if ( $pf_filters_range['pfr_taxonomy'][$q] !== 'price' ) {

															$catalog_attrs = get_terms( $pf_filters_range['pfr_taxonomy'][$q], array( 'hide_empty' => 0 ) );
															$curr_options = '';
															if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
																foreach ( $catalog_attrs as $term ) {
																	$decode_slug = WC_Prdctfltr::prdctfltr_utf8_decode($term->slug);
																	$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $decode_slug, $term->name, ( in_array($decode_slug, $pf_filters_range['pfr_include'][$q]) ? ' selected="selected"' : '' ) );
																}
															}

															printf( '<select name="pfr_include[%2$s][]" id="pfr_include_%2$s" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q );
															$add_disabled = '';

														}
														else {

															printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $q );
															$add_disabled = ' disabled';

														}
													?>
													<span class="description"><?php _e( 'Select terms to include. Use CTRL+Click to select terms or deselect all.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $q, __( 'Term Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$orderby_params = array(
														'' => __( 'None', 'prdctfltr' ),
														'id' => __( 'ID', 'prdctfltr' ),
														'name' => __( 'Name', 'prdctfltr' ),
														'number' => __( 'Number', 'prdctfltr' ),
														'slug' => __( 'Slug', 'prdctfltr' ),
														'count' => __( 'Count', 'prdctfltr' )
													);
													foreach ( $orderby_params as $k => $v ) {
														$selected = ( isset($pf_filters_range['pfr_orderby'][$q]) && $pf_filters_range['pfr_orderby'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}
													printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php _e( 'Select current range terms order by.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_order_%1$s">%2$s</label>', $q, __( 'Term Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$order_params = array(
														'ASC' => __( 'ASC', 'prdctfltr' ),
														'DESC' => __( 'DESC', 'prdctfltr' )
													);
													foreach ( $order_params as $k => $v ) {
														$selected = ( isset($pf_filters_range['pfr_order'][$q]) && $pf_filters_range['pfr_order'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_style_%1$s">%2$s</label>', $q, __( 'Select Style', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$catalog_style = array(
														'flat' => __( 'Flat', 'prdctfltr' ),
														'modern' => __( 'Modern', 'prdctfltr' ),
														'html5' => __( 'HTML5', 'prdctfltr' ),
														'white' => __( 'White', 'prdctfltr' )
													);
													foreach ( $catalog_style as $k => $v ) {
														$selected = ( $pf_filters_range['pfr_style'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q );
												?>
													<span class="description"><?php _e( 'Select current range style.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Grid', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Grid', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_grid_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( $pf_filters_range['pfr_grid'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															_e( 'Check this option to use grid in current range.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_adoptive_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $pf_filters_range['pfr_adoptive'][$q] ) && $pf_filters_range['pfr_adoptive'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															_e( 'Check this option to enable adoptive filtering on current range filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_custom_%1$s">%2$s</label>', $q, __( 'Custom Settings', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php _e( 'Enter custom settings for the range filter. Visit this page for more information ', 'prdctfltr' ); ?> <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a></p>
													<?php
														printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="wmin-width:600px;margin-top:12px;min-height:150px;">%2$s</textarea>', $q, ( isset( $pf_filters_range['pfr_custom'][$q] ) ? $pf_filters_range['pfr_custom'][$q] : '' ) );
													?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$q++;
						}
						else if ( !in_array( $v, array( 'advanced', 'range' ) ) ) {
							if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
								continue;
							}
						?>
							<div class="pf_element" data-filter="<?php echo $v; ?>">
								<span><?php echo $pf_filters[$v]; ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder"></div>
							</div>
						<?php
						}
					}
				?>
				</div>

				<p class="form-field prdctfltr_hidden">
					<select name="wc_settings_prdctfltr_active_filters[]" id="wc_settings_prdctfltr_active_filters" class="hidden" multiple="multiple">
					<?php
						foreach ( $pf_filters_selected as $v ) {
							if ( $v == 'advanced') {
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php _e('Advanced Filter', 'prdctfltr'); ?></option>
							<?php
							}
							else if ( $v == 'range' ) {
							?>
									<option value="<?php echo $v; ?>" selected="selected"><?php _e('Range Filter', 'prdctfltr'); ?></option>
							<?php
							}
							else {
								if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
									continue;
								}
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php echo $pf_filters[$v]; ?></option>
							<?php
							}
						}
					?>
					</select>
				</p>

			</td>
		</tr><?php
		}

		public static function prdctfltr_add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_products_filter'] = __( 'Product Filter', 'prdctfltr' );
			return $settings_tabs;
		}

		public static function prdctfltr_settings_tab() {
			woocommerce_admin_fields( self::prdctfltr_get_settings( 'get' ) );
		}

		public static function prdctfltr_update_settings() {

			if ( isset($_POST['pfa_taxonomy']) ) {

				$adv_filters = array();

				for($i = 0; $i < count($_POST['pfa_taxonomy']); $i++ ) {
					$adv_filters['pfa_title'][$i] = $_POST['pfa_title'][$i];
					$adv_filters['pfa_taxonomy'][$i] = $_POST['pfa_taxonomy'][$i];
					$adv_filters['pfa_include'][$i] = ( isset($_POST['pfa_include'][$i]) ? $_POST['pfa_include'][$i] : array() );
					$adv_filters['pfa_orderby'][$i] = ( isset($_POST['pfa_orderby'][$i]) ? $_POST['pfa_orderby'][$i] : '' );
					$adv_filters['pfa_order'][$i] = ( isset($_POST['pfa_order'][$i]) ? $_POST['pfa_order'][$i] : '' );
					$adv_filters['pfa_multiselect'][$i] = ( isset($_POST['pfa_multiselect'][$i]) ? $_POST['pfa_multiselect'][$i] : 'no' );
					$adv_filters['pfa_relation'][$i] = ( isset($_POST['pfa_relation'][$i]) ? $_POST['pfa_relation'][$i] : 'IN' );
					$adv_filters['pfa_adoptive'][$i] = ( isset($_POST['pfa_adoptive'][$i]) ? $_POST['pfa_adoptive'][$i] : 'no' );
					$adv_filters['pfa_none'][$i] = ( isset($_POST['pfa_none'][$i]) ? $_POST['pfa_none'][$i] : 'no' );
					$adv_filters['pfa_limit'][$i] = ( isset($_POST['pfa_limit'][$i]) ? $_POST['pfa_limit'][$i] : '' );
					$adv_filters['pfa_hierarchy'][$i] = ( isset($_POST['pfa_hierarchy'][$i]) ? $_POST['pfa_hierarchy'][$i] : 'no' );
					$adv_filters['pfa_hierarchy_mode'][$i] = ( isset($_POST['pfa_hierarchy_mode'][$i]) ? $_POST['pfa_hierarchy_mode'][$i] : 'no' );
					$adv_filters['pfa_mode'][$i] = ( isset($_POST['pfa_mode'][$i]) ? $_POST['pfa_mode'][$i] : 'showall' );
					$adv_filters['pfa_style'][$i] = ( isset($_POST['pfa_style'][$i]) ? $_POST['pfa_style'][$i] : 'pf_attr_text' );
					$adv_filters['pfa_term_customization'][$i] = ( isset($_POST['pfa_term_customization'][$i]) ? $_POST['pfa_term_customization'][$i] : '' );
				}

				update_option('wc_settings_prdctfltr_advanced_filters', $adv_filters);

			}

			if ( isset($_POST['pfr_taxonomy']) ) {

				$rng_filters = array();

				for($i = 0; $i < count($_POST['pfr_taxonomy']); $i++ ) {
					$rng_filters['pfr_title'][$i] = $_POST['pfr_title'][$i];
					$rng_filters['pfr_taxonomy'][$i] = $_POST['pfr_taxonomy'][$i];
					$rng_filters['pfr_include'][$i] = ( isset($_POST['pfr_include'][$i]) ? $_POST['pfr_include'][$i] : array() );
					$rng_filters['pfr_orderby'][$i] = ( isset($_POST['pfr_orderby'][$i]) ? $_POST['pfr_orderby'][$i] : '' );
					$rng_filters['pfr_order'][$i] = ( isset($_POST['pfr_order'][$i]) ? $_POST['pfr_order'][$i] : '' );
					$rng_filters['pfr_style'][$i] = ( isset($_POST['pfr_style'][$i]) ? $_POST['pfr_style'][$i] : 'flat' );
					$rng_filters['pfr_grid'][$i] = ( isset($_POST['pfr_grid'][$i]) ? $_POST['pfr_grid'][$i] : 'no' );
					$rng_filters['pfr_adoptive'][$i] = ( isset($_POST['pfr_adoptive'][$i]) ? $_POST['pfr_adoptive'][$i] : 'no' );
					$rng_filters['pfr_custom'][$i] = ( isset($_POST['pfr_custom'][$i]) ? $_POST['pfr_custom'][$i] : 'no' );
				}

				update_option('wc_settings_prdctfltr_range_filters', $rng_filters);

			}

			if ( isset($_POST['wc_settings_prdctfltr_active_filters']) ) {
				update_option('wc_settings_prdctfltr_active_filters', $_POST['wc_settings_prdctfltr_active_filters']);
			}

			woocommerce_update_options( self::prdctfltr_get_settings( 'update' ) );

			delete_transient( 'prdctfltr_default' );

		}

		public static function prdctfltr_get_settings( $action = 'get' ) {

			$catalog_categories = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
			$curr_cats = array();
			if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ){
				foreach ( $catalog_categories as $term ) {
					$curr_cats[WC_Prdctfltr::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$catalog_tags = get_terms( 'product_tag', array( 'hide_empty' => 0 ) );
			$curr_tags = array();
			if ( !empty( $catalog_tags ) && !is_wp_error( $catalog_tags ) ){
				foreach ( $catalog_tags as $term ) {
					$curr_tags[WC_Prdctfltr::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$catalog_chars = ( taxonomy_exists('characteristics') ? get_terms( 'characteristics', array( 'hide_empty' => 0 ) ) : array() );
			$curr_chars = array();
			if ( !empty( $catalog_chars ) && !is_wp_error( $catalog_chars ) ){
				foreach ( $catalog_chars as $term ) {
					$curr_chars[WC_Prdctfltr::prdctfltr_utf8_decode( $term->slug )] = $term->name;
				}
			}

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			$product_taxonomies = get_object_taxonomies( 'product' );

			$ready_tax = array();
			foreach( $product_taxonomies as $product_tax ) {
				if ( $product_tax == 'product_type' ) {
					continue;
				}

				$ready_tax[$product_tax] = $product_tax;
			}

			if ( $action == 'get' ) {
		?>
		<ul class="subsubsub<?php echo ( isset($_GET['section']) ? ' wcpf_mode_' . $_GET['section'] : ' wcpf_mode_presets' ); ?>">
		<?php
			$sections = array(
				'presets' => array(
					'title' => __( 'Default Filter and Filter Presets', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-filter"></i>'
				),
				'overrides' => array(
					'title' => __( 'Filter Overrides and Restrictions', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-overrides"></i>'
				),
				'advanced' => array(
					'title' => __( 'Advanced Options', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-terms"></i>'
				),
				'analytics' =>array(
					'title' => __( 'Filter Analytics', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-analytics"></i>'
				),
				'register' =>array(
					'title' => __( 'Register and Automatic Updates', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-update"></i>'
				)
			);

			$i=0;
			foreach ( $sections as $k => $v ) {

				$curr_class = ( isset( $_GET['section'] ) && $_GET['section'] == $k ) || ( !isset($_GET['section'] ) && $k == 'presets' ) ? true : false;

				printf( '<li class="button-primary%5$s"><a href="%1$s"%3$s>%4$s %2$s</a></li>', admin_url( 'admin.php?page=wc-settings&tab=settings_products_filter&section=' . $k ), $v['title'], $curr_class !== false ? ' class="current"' : '', $v['icon'], $curr_class !== false ? ' active' : '' );

				$i++;
			}
			printf( '<li class="button-primary pink"><i class="prdctfltr-check"></i> <a href="%1$s" target="_blank">%2$s</a></li>', 'http://codecanyon.net/user/dzeriho/portfolio?ref=dzeriho', __( 'Get more awesome plugins for WooCommerce!', 'prdctfltr' ) );
		?>
		</ul>
		<br class="clear" />
		<?php
			}
			if ( isset($_GET['section']) && $_GET['section'] == 'register' ) {

				$settings = array();

				$settings = array(
					'section_register_title' => array(
						'name' => __( 'Product Filter Registration', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'By entering your purchase code you will unlock the Automatic Updates option! Use one license per domain please!', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_register_title'
					),
					'prdctfltr_purchase_code' => array(
						'name' => __( 'Register Product Filter', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter your purchase code to get instant updated even before the codecanyon.net releases!', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_purchase_code',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_register_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_register_end'
					)
				);

			}
			else if ( isset($_GET['section']) && $_GET['section'] == 'analytics' ) {

				$settings = array();

				$settings = array(
					'section_analytics_title' => array(
						'name' => __( 'Product Filter Analytics Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Follow your customers filtering data. BETA VERSION Please note, this section and its features will be extended in the future updates. Do not attach yourself too much with the data as it will change.', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_analytics_title'
					),
					'prdctfltr_use_analytics' => array(
						'name' => __( 'Use Filtering Analytics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use filtering analytics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_analytics',
						'default' => 'no'
					),
					'prdctfltr_filtering_analytics' => array(
						'name' => __( 'Filtering Analytics', 'prdctfltr' ),
						'type' => 'pf_filter_analytics',
						'desc' => __( 'See what your customers are searching for.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_filtering_analytics',
						'default' => 'no'
					),
					'section_analytics_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_analytics_end'
					)
				);

			}
			else if ( isset($_GET['section']) && $_GET['section'] == 'advanced' ) {
				$curr_theme = wp_get_theme();

				$settings = array(
					'section_general_title' => array(
						'name' => __( 'Product Filter Shop and Product Archive Installation Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'General installation settings for Shop and Product Archive pages.', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_general_title'
					),
					'prdctfltr_enable' => array(
						'name' => __( 'Product Filter Shop/Product Archives Installation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select method for installing the Product Filter template in your Shop and Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable',
						'options' => array(
							'yes' => __( 'Override Default WooCommerce Templates', 'prdctfltr' ),
							'no' => __( 'Use Widget', 'prdctfltr' ),
							'action' => __( 'Custom Action', 'prdctfltr' )
						),
						'default' => 'yes',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_enable_overrides' => array(
						'name' => __( 'Select Filtering Templates', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select which WooCommerce templates should the Product Filter replace. Use CTRL+Click to select multiple templates or deselect all. This option is used if the Product Filter Shop/Product Archives Installation is set to Override Default WooCommerce Templates option.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_overrides',
						'options' => array(
							'orderby' => __( 'Order By', 'prdctfltr' ),
							'result-count' => __( 'Result Count', 'prdctfltr' )
						),
						'default' => array( 'orderby', 'result-count' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_enable_action' => array(
						'name' => __( 'Product Filter Custom Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom products action to initiate the Product Filter template. This option is used if the Product Filter Shop/Product Archives Installation is set to Custom Action option. Use actions from your theme archive-product.php template. Please enter action name in following format action_name:priority. E.G. woocommerce_before_shop_loop:40', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_action',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_default_templates' => array(
						'name' => __( 'Enable/Disable Default Filter Templates', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'If you have disabled the Product Filter Override Templates option at the top, then your default WooCommerce or', 'prdctfltr') . ' ' . $curr_theme->get('Name') . ' ' . __('filter templates will be shown. If you want do disable these default templates too, check this option. This option can be usefull for the widget version of the Product Filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_default_templates',
						'default' => 'no'
					),
					'prdctfltr_filtering_mode' => array(
						'name' => __( 'Pretty Permalinks Filtering Mode (Only Shop and Archives)', 'prdctfltr' ),
						'type' => 'select',
						'desc' => '<br/><br/>' . __( 'Select filtering method when using pretty permalinks. Simple Mode - When browsing taxonomies (categoires and such) filters of the same taxonomy type will change the taxonomy. Taxonomy filters will work as permalink switches. In Depth Mode - When browsing taxonomies (categoires and such) filters of the same taxonomy type will be added to the filter query. Taxonomy filters will not be able to switch permalink terms. Please read the documentation for more information.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_filtering_mode',
						'options' => array(
							'simple' => __( 'Simple Mode', 'prdctfltr' ),
							'indepth' => __( 'In Depth Mode', 'prdctfltr' )
						),
						'default' => 'simple',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_general_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_general_end'
					),

					'section_ajax_title' => array(
						'name' => __( 'Product Filter AJAX Product Archives Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'AJAX Product Archives Settings - Setup this section to use AJAX on shop and product archive pages.', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_ajax_title'
					),
					'prdctfltr_use_ajax' => array(
						'name' => __( 'Use AJAX On Product Archives', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use AJAX load on shop and product archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_ajax',
						'default' => 'no'
					),
					'prdctfltr_ajax_class' => array(
						'name' => __( 'Override AJAX Wrapper Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom wrapper class if the default setting is not working. Default class: .products', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_category_class' => array(
						'name' => __( 'Override AJAX Category Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom category class if the default setting is not working. Default class: .product-category', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_category_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_product_class' => array(
						'name' => __( 'Override AJAX Product Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom products class if the default setting is not working. Default class: .type-product', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_product_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_pagination_class' => array(
						'name' => __( 'Override AJAX Pagination Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom pagination class if the default setting is not working. Default class: .woocommerce-pagination', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_pagination_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_count_class' => array(
						'name' => __( 'Override AJAX Result Count Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom result count class if the default setting is not working. Default class: .woocommerce-result-count', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_count_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_orderby_class' => array(
						'name' => __( 'Override AJAX Order By Class', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter custom order by class if the default setting is not working. Default class: .woocommerce-ordering', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_orderby_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_ajax_columns' => array(
						'name' => __( 'AJAX Product Columns', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'In how many columns are your product displayed on the shop and product archive pages by default?', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_columns',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_rows' => array(
						'name' => __( 'AJAX Product Rows', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'In how many rows are your product displayed on the shop and product archive pages by default?', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_rows',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_pagination_type' => array(
						'name' => __( 'Select Pagination Type', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select pagination template to use.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pagination_type',
						'options' => array(
							'default' => __( 'Default (In Theme)', 'prdctfltr' ),
							'prdctfltr-pagination-default' => __( 'Product Filter Pagination', 'prdctfltr' ),
							'prdctfltr-pagination-load-more' => __( 'Product Filter Load More', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_product_animation' => array(
						'name' => __( 'Select Product Loading Animation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select animation when showing new products.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_product_animation',
						'options' => array(
							'none' => __( 'No Animation', 'prdctfltr' ),
							'default' => __( 'Fade Each Product', 'prdctfltr' ),
							'slide' => __( 'Slide Each Product', 'prdctfltr' ),
							'random' => __( 'Fade Random Products', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_after_ajax_scroll' => array(
						'name' => __( 'AJAX Pagination Scroll', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select type of scrolling animation after using the AJAX pagination.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_after_ajax_scroll',
						'options' => array(
							'none' => __( 'No Animation', 'prdctfltr' ),
							'products' => __( 'Scroll to Products', 'prdctfltr' ),
							'top' => __( 'Scroll to Top', 'prdctfltr' )
						),
						'default' => 'products',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_js' => array(
						'name' => __( 'AJAX jQuery and JS Refresh', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Input jQuery or JS code to execute after AJAX calls. This option is usefull if the JS is broken after these calls.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_js',
						'default' => '',
						'css' 		=> 'min-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_ajax_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_ajax_end'
					),

					'section_advanced_title' => array(
						'name' => __( 'Product Filter Advanced Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Advanced Settings - These settings will affect all filters.', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_advanced_title'
					),

					'prdctfltr_custom_tax' => array(
						'name' => __( 'Use Characteristics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Enable this option to get custom characteristics product meta box.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax',
						'default' => 'yes',
					),
					'prdctfltr_instock' => array(
						'name' => __( 'Show In Stock Products by Default', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show the In Stock products by default.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock',
						'default' => 'no'
					),
					'prdctfltr_clearall' => array(
						'name' => __( 'Default Clear All Action', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select your Shop/Product Archives Clear All button action.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_clearall',
						'options' => array(
							'all' => __( 'Clear All Filters', 'prdctfltr' ),
							'category' => __( 'Keep Taxonomy Permalinks', 'prdctfltr' )
						),
						'default' => 'all',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_hideempty' => array(
						'name' => __( 'Hide Empty Terms in Filters', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this checkbox to hide empty terms in filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_hideempty',
						'default' => 'no',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_use_variable_images' => array(
						'name' => __( 'Use Variable Images', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use variable images override on shop and archive pages. CAUTION This setting does not work on all servers by default. Additional server setup might be needed.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_variable_images',
						'default' => 'no'
					),
					'prdctfltr_disable_scripts' => array(
						'name' => __( 'Disable JavaScript Libraries', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select JavaScript libraries to disable. Use CTRL+Click to select multiple libraries or deselect all. Selected libraries will not be loaded.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_scripts',
						'options' => array(
							'ionrange' => __( 'Ion Range Slider', 'prdctfltr' ),
							'isotope' => __( 'Isotope', 'prdctfltr' ),
							'mcustomscroll' => __( 'Malihu jQuery Scrollbar', 'prdctfltr' )
						),
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
/*					'prdctfltr_force_categories' => array(
						'name' => __( 'Force Filtering thru Categories', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the redirects. This options should never be checked unless something is wrong with the template you are using. This option also limits your categories filter. The categories filter should not be used if this option is activated. (This option has changed since the 2.3.0 release. Now all installations should be compatible with the redirects by default. Test your installation before activating the option again)', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_categories',
						'default' => 'no'
					),*/

					'section_advanced_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_advanced_end'
					),
					'section_noneajax_title' => array(
						'name' => __( 'Product Filter Product Archives Settings (disabled AJAX)', 'prdctfltr' ),
						'type' => 'title',
						'desc' => __( 'Setup options when AJAX is disabled on Shop and Product Archives.', 'prdctfltr' ),
						'id' => 'wc_settings_prdctfltr_noneajax_title'
					),
					'prdctfltr_force_product' => array(
						'name' => __( 'Force Post Type Variable', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the searches. This options should never be checked unless something is wrong with the template you are using. Option will add the ?post_type=product parameter when filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_product',
						'default' => 'no'
					),
					'prdctfltr_force_redirects' => array(
						'name' => __( 'Disable Product Filter Redirects', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the shop page redirects.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_redirects',
						'default' => 'no'
					),
					'prdctfltr_force_emptyshop' => array(
						'name' => __( 'Disable Empty Shop Redirects', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the shop page redirects.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_emptyshop',
						'default' => 'no'
					),
					'prdctfltr_force_search' => array(
						'name' => __( 'Disable Search Redirects', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option if you are having issues with the search redirects.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_search',
						'default' => 'no'
					),
					'prdctfltr_remove_single_redirect' => array(
						'name' => __( 'Single Product Redirect', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Uncheck to enable single product page redirect when only one product is found.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_remove_single_redirect',
						'default' => 'yes'
					),
					'section_noneajax_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_noneajax_end'
					),
				);
			}
			else if ( ( isset($_GET['section']) && $_GET['section'] == 'presets' ) || !isset($_GET['section']) ) {
				if ( $action == 'get' ) {

					printf( '<h3>%1$s</h3><p>%2$s</p><p>', __( 'Product Filter Preset Manager', 'prdctfltr' ), __( 'Manage filter presets. Load, delete and save presets. Saved filter presets can be used with shortcodes, filter overrides and widgets. Default filter preset will always be used unless the preset is specified by shortcode, filter override or the widget parameter.', 'prdctfltr' ) );
			?>
							<select id="prdctfltr_filter_presets">
								<option value="default"><?php _e( 'Default', 'wcwar' ); ?></option>
								<?php
									$curr_presets = get_option( 'prdctfltr_templates', array() );

									if ( !empty($curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
			<?php
					printf( '<a href="#" id="prdctfltr_save" class="button-primary">%1$s</a> <a href="#" id="prdctfltr_load" class="button-primary">%2$s</a> <a href="#" id="prdctfltr_delete" class="button-primary">%3$s</a> <a href="#" id="prdctfltr_reset_default" class="button-primary">%4$s</a> <a href="#" id="prdctfltr_save_default" class="button-primary">%5$s</a></p>', __( 'Save as preset', 'prdctfltr' ), __( 'Load', 'prdctfltr' ), __( 'Delete', 'prdctfltr' ), __( 'Reset to default', 'prdctfltr' ), __( 'Save as default preset', 'prdctfltr' ) );
				}

				$settings = array(
					'section_basic_title' => array(
						'name'     => __( 'Filter Basic Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup Product Filter appearance and basic settings.', 'prdctfltr' ) . '<span class="wcpff_basic"></span>',
						'id'       => 'wc_settings_prdctfltr_basic_title'
					),
					'prdctfltr_always_visible' => array(
						'name' => __( 'Always Visible', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'This option will make Product Filter visible without the slide up/down animation at all times.', 'prdctfltr' ) . ' <em>' . __( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_always_visible',
						'default' => 'no',
					),
					'prdctfltr_click_filter' => array(
						'name' => __( 'Instant Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to disable the filter button and use instant product filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_click_filter',
						'default' => 'no',
					),
					'prdctfltr_show_counts' => array(
						'name' => __( 'Show Term Products Count', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show products count with the terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts',
						'default' => 'no',
					),
					'prdctfltr_show_counts_mode' => array(
						'name' => __( 'Term Products Count Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select how to display the product count.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts_mode',
						'options' => array(
							'default' => __( 'Filtered Count / Total', 'prdctfltr' ),
							'count' => __( 'Filtered Count', 'prdctfltr' ),
							'total' => __( 'Total', 'prdctfltr')
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_show_search' => array(
						'name' => __( 'Show Term Search Fields', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to show search fields on supported terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_search',
						'default' => 'no',
					),
					'prdctfltr_adoptive' => array(
						'name' => __( 'Enable/Disable Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable the adoptive filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive',
						'default' => 'no',
					),
					'prdctfltr_adoptive_mode' => array(
						'name' => __( 'Select Adoptive Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select more to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_mode',
						'options' => array(
							'always' => __( 'Always Active', 'prdctfltr' ),
							'permalink' => __( 'Active on Permalinks and Filters', 'prdctfltr' ),
							'filter' => __( 'Active on Filters', 'prdctfltr' )
						),
						'default' => 'permalink',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_style' => array(
						'name' => __( 'Select Adoptive Filtering Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_style',
						'options' => array(
							'pf_adptv_default' => __( 'Hide Terms', 'prdctfltr' ),
							'pf_adptv_unclick' => __( 'Disabled and Unclickable', 'prdctfltr' ),
							'pf_adptv_click' => __( 'Disabled but Clickable', 'prdctfltr' )
						),
						'default' => 'pf_adptv_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_depend' => array(
						'name' => __( 'Select Adoptive Filtering Dependencies', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Adoptive filters can depend only on ceratin taxonomies. Select taxonomies to include. Use CTRL+Click to select multiple taxonomies or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_depend',
						'options' => $ready_tax,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),

					'prdctfltr_disable_bar' => array(
						'name' => __( 'Disable Top Bar', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter top bar. This option will also make the filter always visible.', 'prdctfltr' ) . ' <em>' . __( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_disable_bar',
						'default' => 'no',
					),
					'prdctfltr_disable_showresults' => array(
						'name' => __( 'Disable Show Results Title', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the show results text from the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_showresults',
						'default' => 'no',
					),
					'prdctfltr_disable_sale' => array(
						'name' => __( 'Disable Sale Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter sale button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_sale',
						'default' => 'no',
					),
					'prdctfltr_disable_instock' => array(
						'name' => __( 'Disable In Stock Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter in stock button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_instock',
						'default' => 'no',
					),
					'prdctfltr_disable_reset' => array(
						'name' => __( 'Disable Reset Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide the Product Filter reset button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_reset',
						'default' => 'no',
					),
					'prdctfltr_custom_action' => array(
						'name' => __( 'Override Filter Form Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Advanced users can override filter form action. Please check documentation for more details.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_action',
						'default' => '',
						'css' 		=> 'width:300px;margin-right:12px;',
					),
					'prdctfltr_noproducts' => array(
						'name' => __( 'Override No Products Action', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => __( 'Input HTML/Shortcode to override the default action when no products are found. Default action means that random products will be shown when there are no products within the filter query.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_noproducts',
						'default' => '',
						'css' 		=> 'min-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_basic_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_enable_end'
					),
					'section_style_title' => array(
						'name'     => __( 'Filter Style', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup Product Filter style settings.', 'prdctfltr' ) . '<span class="wcpff_style"></span>',
						'id'       => 'wc_settings_prdctfltr_style_title'
					),
					'prdctfltr_style_preset' => array(
						'name' => __( 'Select Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style. This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_preset',
						'options' => array(
							'pf_arrow' => __( 'Arrow', 'prdctfltr' ),
							'pf_arrow_inline' => __( 'Arrow Inline', 'prdctfltr' ),
							'pf_default' => __( 'Default', 'prdctfltr' ),
							'pf_default_inline' => __( 'Default Inline', 'prdctfltr' ),
							'pf_select' => __( 'Use Select Box', 'prdctfltr' ),
							'pf_sidebar' => __( 'Fixed Sidebar Left', 'prdctfltr' ),
							'pf_sidebar_right' => __( 'Fixed Sidebar Right', 'prdctfltr' ),
							'pf_sidebar_css' => __( 'Fixed Sidebar Left With Overlay', 'prdctfltr' ),
							'pf_sidebar_css_right' => __( 'Fixed Sidebar Right With Overlay', 'prdctfltr' ),
							'pf_fullscreen' => __( 'Full Screen', 'prdctfltr' ),
						),
						'default' => 'pf_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_mode' => array(
						'name' => __( 'Select Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select mode to use with the filter. This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_mode',
						'options' => array(
							'pf_mod_row' => __( 'One Row', 'prdctfltr' ),
							'pf_mod_multirow' => __( 'Multiple Rows', 'prdctfltr' ),
							'pf_mod_masonry' => __( 'Masonry Filters', 'prdctfltr' )
						),
						'default' => 'pf_mod_multirow',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_max_columns' => array(
						'name' => __( 'Max Columns', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'This option sets the number of columns for the filter. This option does not work with the widget version or the fixed sidebar layouts.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_columns',
						'default' => 3,
						'custom_attributes' => array(
							'min' 	=> 1,
							'max' 	=> 10,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_limit_max_height' => array(
						'name' => __( 'Limit Max Height', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to limit the Max Height of for the filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_limit_max_height',
						'default' => 'no',
					),
					'prdctfltr_max_height' => array(
						'name' => __( 'Max Height', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Set the Max Height value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_height',
						'default' => 150,
						'custom_attributes' => array(
							'min' 	=> 100,
							'max' 	=> 300,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_custom_scrollbar' => array(
						'name' => __( 'Use Custom Scroll Bars', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to override default browser scroll bars with javascrips scrollbars in Max Height mode.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_scrollbar',
						'default' => 'yes',
					),
					'prdctfltr_style_checkboxes' => array(
						'name' => __( 'Select Checkbox Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style for the term checkboxes.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_checkboxes',
						'options' => array(
							'prdctfltr_round' => __( 'Round', 'prdctfltr' ),
							'prdctfltr_square' => __( 'Square', 'prdctfltr' ),
							'prdctfltr_checkbox' => __( 'Checkbox', 'prdctfltr' ),
							'prdctfltr_system' => __( 'System Checkboxes', 'prdctfltr' )
						),
						'default' => 'pf_round',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_hierarchy' => array(
						'name' => __( 'Select Hierarchy Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style for hierarchy terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_hierarchy',
						'options' => array(
							'prdctfltr_hierarchy_circle' => __( 'Circle', 'prdctfltr' ),
							'prdctfltr_hierarchy_filled' => __( 'Circle Solid', 'prdctfltr' ),
							'prdctfltr_hierarchy_lined' => __( 'Lined', 'prdctfltr' ),
							'prdctfltr_hierarchy_arrow' => __( 'Arrows', 'prdctfltr' )
						),
						'default' => 'prdctfltr_hierarchy_circle',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_button_position' => array(
						'name' => __( 'Select Filter Buttons Position', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select position of the filter buttons (Filter selected, Sale button..).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_button_position',
						'options' => array(
							'bottom' => __( 'Bottom', 'prdctfltr' ),
							'top' => __( 'Top', 'prdctfltr' )
						),
						'default' => 'bottom',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_icon' => array(
						'name' => __( 'Override Filter Icon', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Input the icon class to override the default Product Filter icon. Use icon class e.g. prdctfltr-filter or FontAwesome fa fa-shopping-cart or any other.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_icon',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_title' => array(
						'name' => __( 'Override Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override Filter products, the default filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_submit' => array(
						'name' => __( 'Override Filter Submit Text', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Override Filter selected, the default filter submit button text.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_submit',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_loader' => array(
						'name' => __( 'Select AJAX Loader Icon', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select AJAX loader icon.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_loader',
						'options' => array(
							'audio' => __( 'Audio', 'prdctfltr' ),
							'ball-triangle' => __( 'Ball Triangle', 'prdctfltr' ),
							'bars' => __( 'Bars', 'prdctfltr' ),
							'circles' => __( 'Circles', 'prdctfltr' ),
							'grid' => __( 'Grid', 'prdctfltr' ),
							'hearts' => __( 'Hearts', 'prdctfltr' ),
							'oval' => __( 'Oval', 'prdctfltr' ),
							'puff' => __( 'Puff', 'prdctfltr' ),
							'rings' => __( 'Rings', 'prdctfltr' ),
							'spinning-circles' => __( 'Spining Circles', 'prdctfltr' ),
							'tail-spin' => __( 'Tail Spin', 'prdctfltr' ),
							'circles' => __( 'Circles', 'prdctfltr' ),
							'three-dots' => __( 'Three Dots', 'prdctfltr' )
						),
						'default' => 'oval',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_style_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_style_end'
					),
					'section_title' => array(
						'name'     => __( 'Filter Manager', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Create filters! Greens are active, reds are not, blue buttons add as many filters as you need. Setup basic general settings and filter styles. Click the arrow down icon to customize each filter options. Click on the paint icon to customize the filter terms appearance if you do not like the default display options. In here you can add images, colors, custom styles. Click the cogs icon on supporting filters to customize filtering terms. Click the move icon to reorder filters, or use the X to remove them.', 'prdctfltr' ),
						'id'       => 'wc_settings_prdctfltr_section_title'
					),
					'prdctfltr_filters' => array(
						'name' => __( 'Select Filters', 'prdctfltr' ),
						'type' => 'pf_filter',
						'desc' => __( 'Select filters. Click on a filter to activate or create advanced filters. Click and drag to reorder filters.', 'prdctfltr' )
					),
					'section_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_section_end'
					),

					'section_perpage_filter_title' => array(
						'name'     => __( 'Products Per Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup products per page filter.', 'prdctfltr' ) . '<span class="wcpfs_per_page"></span>',
						'id'       => 'wc_settings_prdctfltr_perpage_filter_title'
					),
					'prdctfltr_perpage_title' => array(
						'name' => __( 'Override Products Per Page Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the products per page filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_label' => array(
						'name' => __( 'Override Products Per Page Filter Label', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter label for the products per page filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_label',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_range' => array(
						'name' => __( 'Per Page Filter Initial', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Initial products per page value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range',
						'default' => 20,
						'custom_attributes' => array(
							'min' 	=> 3,
							'max' 	=> 999,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_range_limit' => array(
						'name' => __( 'Per Page Filter Values', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Number of product per page values.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range_limit',
						'default' => 5,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_perpage_filter_customization' => array(
						'name' => __( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_perpage_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_perpage_filter_end'
					),
					'section_instock_filter_title' => array(
						'name'     => __( 'In Stock Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup in stock filter.', 'prdctfltr' ) . '<span class="wcpfs_instock"></span>',
						'id'       => 'wc_settings_prdctfltr_instock_filter_title'
					),
					'prdctfltr_instock_title' => array(
						'name' => __( 'Override In Stock Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the in stock filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_instock_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_instock_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_instock_filter_end'
					),
					'section_orderby_filter_title' => array(
						'name'     => __( 'Sort By Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup sort by filter.', 'prdctfltr' ) . '<span class="wcpfs_sort"></span>',
						'id'       => 'wc_settings_prdctfltr_orderby_filter_title'
					),
					'prdctfltr_orderby_title' => array(
						'name' => __( 'Override Sort By Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the sort by filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_include_orderby' => array(
						'name' => __( 'Select Sort By Terms', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select Sort by terms to include. Use CTRL+Click to select multiple Sort by terms or deselect all to use all Sort by terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_orderby',
						'options' => array(
								'menu_order'    => __( 'Default', 'prdctfltr' ),
								'comment_count' => __( 'Review Count', 'prdctfltr' ),
								'popularity'    => __( 'Popularity', 'prdctfltr' ),
								'rating'        => __( 'Average rating', 'prdctfltr' ),
								'date'          => __( 'Newness', 'prdctfltr' ),
								'price'         => __( 'Price: low to high', 'prdctfltr' ),
								'price-desc'    => __( 'Price: high to low', 'prdctfltr' ),
								'rand'          => __( 'Random Products', 'prdctfltr' ),
								'title'         => __( 'Product Name', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_orderby_none' => array(
						'name' => __( 'Order By Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide None on order by filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_none',
						'default' => 'no',
					),
					'prdctfltr_orderby_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_orderby_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_orderby_filter_end'
					),

					'section_search_filter_title' => array(
						'name'     => __( 'Search Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup in search filter.', 'prdctfltr' ) . '<span class="wcpfs_search"></span>',
						'id'       => 'wc_settings_prdctfltr_search_filter_title'
					),
					'prdctfltr_search_title' => array(
						'name' => __( 'Override Search Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the search filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_search_placeholder' => array(
						'name' => __( 'Override Search Filter Placeholder', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the search filter placeholder. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_placeholder',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_search_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_search_filter_end'
					),

					'section_price_filter_title' => array(
						'name'     => __( 'By Price Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup by price filter.', 'prdctfltr' ) . '<span class="wcpfs_price"></span>',
						'id'       => 'wc_settings_prdctfltr_price_filter_title'
					),
					'prdctfltr_price_title' => array(
						'name' => __( 'Override Price Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the price filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_price_range' => array(
						'name' => __( 'Price Range Filter Initial Price', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Initial price for the filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_add' => array(
						'name' => __( 'Price Range Filter Price Add', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Price to add.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_add',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_limit' => array(
						'name' => __( 'Price Range Filter Intervals', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Number of price intervals to use. E.G. You have set the initial price to 99.9, and the add price is set to 100, you will achieve filtering like 0-99.9, 99.9-199.9, 199.9- 299.9 for the number of times as set in the price intervals setting.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_limit',
						'default' => 6,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_none' => array(
						'name' => __( 'Price Range Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide None on price filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_none',
						'default' => 'no',
					),
					'prdctfltr_price_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_price_filter_customization' => array(
						'name' => __( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_price_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_price_filter_end'
					),
					'section_cat_filter_title' => array(
						'name'     => __( 'By Category Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup by category filter.', 'prdctfltr' ) . '<span class="wcpfs_cat"></span>',
						'id'       => 'wc_settings_prdctfltr_cat_filter_title'
					),
					'prdctfltr_cat_title' => array(
						'name' => __( 'Override Category Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the category filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_include_cats' => array(
						'name' => __( 'Select Categories', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select categories to include. Use CTRL+Click to select multiple categories or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_cats',
						'options' => $curr_cats,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_orderby' => array(
						'name' => __( 'Categories Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select the categories order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_order' => array(
						'name' => __( 'Categories Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_limit' => array(
						'name' => __( 'Limit Categories', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of categories to be shown. If limit is set, categories with most posts will be shown first.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_cat_hierarchy' => array(
						'name' => __( 'Use Category Hierarchy', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable category hierarchy.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy',
						'default' => 'no',
					),
					'prdctfltr_cat_hierarchy_mode' => array(
						'name' => __( 'Categories Hierarchy Mode', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to expand parent categories on load.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy_mode',
						'default' => 'no',
					),
					'prdctfltr_cat_mode' => array(
						'name' => __( 'Categories Hierarchy Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select how to show categories upon filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_mode',
						'options' => array(
								'showall' => __( 'Show all', 'prdctfltr' ),
								'subonly' => __( 'Keep only child terms', 'prdctfltr' )
							),
						'default' => 'showall',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi-select on categories.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_multi',
						'default' => 'no',
					),
					'prdctfltr_cat_relation' => array(
						'name' => __( 'Multi Select Categories Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select categories relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use adoptive filtering on categories.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_adoptive',
						'default' => 'no',
					),
					'prdctfltr_cat_none' => array(
						'name' => __( 'Categories Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide None on categories.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_none',
						'default' => 'no',
					),
					'prdctfltr_cat_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_cat_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_cat_filter_end'
					),
					'section_tag_filter_title' => array(
						'name'     => __( 'By Tag Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup by tag filter.', 'prdctfltr' ) . '<span class="wcpfs_tag"></span>',
						'id'       => 'wc_settings_prdctfltr_tag_filter_title'
					),
					'prdctfltr_tag_title' => array(
						'name' => __( 'Override Tag Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the tag filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_include_tags' => array(
						'name' => __( 'Select Tags', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select tags to include. Use CTRL+Click to select multiple tags or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_tags',
						'options' => $curr_tags,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_orderby' => array(
						'name' => __( 'Tags Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select the tags order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_order' => array(
						'name' => __( 'Tags Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_limit' => array(
						'name' => __( 'Limit Tags', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of tags to be shown. If limit is set, tags with most posts will be shown first.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_tag_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi-select on tags.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_multi',
						'default' => 'no',
					),
					'prdctfltr_tag_relation' => array(
						'name' => __( 'Multi Select Tags Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select tags relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use adoptive filtering on tags.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_adoptive',
						'default' => 'no',
					),
					'prdctfltr_tag_none' => array(
						'name' => __( 'Tags Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide None on tags.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_none',
						'default' => 'no',
					),
					'prdctfltr_tag_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_tag_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_tag_filter_end'
					),
					'section_char_filter_title' => array(
						'name'     => __( 'By Characteristics Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Setup by characteristics filter.', 'prdctfltr' ) . '<span class="wcpfs_char"></span>',
						'id'       => 'wc_settings_prdctfltr_char_filter_title'
					),
					'prdctfltr_custom_tax_title' => array(
						'name' => __( 'Override Characteristics Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Enter title for the characteristics filter. If you leave this field blank default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_include_chars' => array(
						'name' => __( 'Select Characteristics', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select characteristics to include. Use CTRL+Click to select multiple characteristics or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_chars',
						'options' => $curr_chars,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_orderby' => array(
						'name' => __( 'Characteristics Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select the characteristics order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_orderby',
						'options' => array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_order' => array(
						'name' => __( 'Characteristics Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_order',
						'options' => array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_limit' => array(
						'name' => __( 'Limit Characteristics', 'prdctfltr' ),
						'type' => 'number',
						'desc' => __( 'Limit number of characteristics to be shown. If limit is set, characteristics with most posts will be shown first.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_chars_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi-select on characteristics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_multi',
						'default' => 'no',
					),
					'prdctfltr_custom_tax_relation' => array(
						'name' => __( 'Multi Select Characteristics Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select characteristics relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_relation',
						'options' => array(
								'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_chars_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use adoptive filtering on characteristics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_adoptive',
						'default' => 'no',
					),
					'prdctfltr_chars_none' => array(
						'name' => __( 'Characteristics Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to hide None on characteristics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_none',
						'default' => 'no',
					),
					'prdctfltr_chars_term_customization' => array(
						'name' => __( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_char_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_char_filter_end'
					),

				);

				if ($attribute_taxonomies) {
					$settings = $settings + array (
						
					);
					foreach ($attribute_taxonomies as $tax) {

						$catalog_attrs = get_terms( 'pa_' . $tax->attribute_name, array( 'hide_empty' => 0 ) );
						$curr_attrs = array();
						if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
							foreach ( $catalog_attrs as $term ) {
								$curr_attrs[ WC_Prdctfltr::prdctfltr_utf8_decode( $term->slug ) ] = $term->name;
							}
						}

						$tax->attribute_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

						$settings = $settings + array(
							'section_pa_'.$tax->attribute_name.'_title' => array(
								'name'     => __( 'By', 'prdctfltr' ) . ' ' . $tax->attribute_label . ' ' . __( 'Filter Settings', 'prdctfltr' ),
								'type'     => 'title',
								'desc'     => __( 'Select options for the current attribute.', 'prdctfltr' ) . '<span class="wcpfs_pa_' . $tax->attribute_name . '"></span>',
								'id'       => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name . '_title'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_title' => array(
								'name' => __( 'Override ' . $tax->attribute_label . ' Filter Title', 'prdctfltr' ),
								'type' => 'text',
								'desc' => __( 'Enter title for the characteristics filter. If you leave this field blank default will be used.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_title',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_include_pa_'.$tax->attribute_name => array(
								'name' => __( 'Include Terms', 'prdctfltr' ),
								'type' => 'multiselect',
								'desc' => __( 'Select terms to include. Use CTRL+Click to select multiple terms or deselect all.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_include_pa_'.$tax->attribute_name,
								'options' => $curr_attrs,
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name => array(
								'name' => __( 'Appearance', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select style preset to use with the current attribute.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name,
								'options' => array(
									'pf_attr_text' => __( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
								),
								'default' => 'pf_attr_text',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_orderby' => array(
								'name' => __( 'Terms Order By', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select the term order.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_orderby',
								'options' => array(
										'' => __( 'None', 'prdctfltr' ),
										'id' => __( 'ID', 'prdctfltr' ),
										'name' => __( 'Name', 'prdctfltr' ),
										'number' => __( 'Number', 'prdctfltr' ),
										'slug' => __( 'Slug', 'prdctfltr' ),
										'count' => __( 'Count', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_order' => array(
								'name' => __( 'Terms Order', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select ascending or descending order.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_order',
								'options' => array(
										'ASC' => __( 'ASC', 'prdctfltr' ),
										'DESC' => __( 'DESC', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_limit' => array(
								'name' => __( 'Limit Terms', 'prdctfltr' ),
								'type' => 'number',
								'desc' => __( 'Limit number of terms to be shown. If limit is set, terms with most posts will be shown first.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_limit',
								'default' => 0,
								'custom_attributes' => array(
									'min' 	=> 0,
									'max' 	=> 100,
									'step' 	=> 1
								),
								'css' => 'width:100px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_hierarchy' => array(
								'name' => __( 'Use Attribute Hierarchy', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to enable attribute hierarchy.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_hierarchy',
								'default' => 'no',
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_hierarchy_mode' => array(
								'name' => __( 'Attribute Hierarchy Mode', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( ' Check this option to expand parent attributes on load.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_hierarchy_mode',
								'default' => 'no',
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_mode' => array(
								'name' => __( 'Attribute Hierarchy Filtering Mode', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select how to show attributes upon filtering.', 'prdctfltr' ),
								'id'   => 'wc_settings_pa_'.$tax->attribute_name.'_mode',
								'options' => array(
										'showall' => __( 'Show all', 'prdctfltr' ),
										'subonly' => __( 'Keep only child terms', 'prdctfltr' )
									),
								'default' => 'showall',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_multi' => array(
								'name' => __( 'Use Multi Select', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to enable multi-select on current attribute.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_multi',
								'default' => 'no',
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_relation' => array(
								'name' => __( 'Multi Select Terms Relation', 'prdctfltr' ),
								'type' => 'select',
								'desc' => __( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_relation',
								'options' => array(
										'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
										'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_adoptive' => array(
								'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to use adoptive filtering on current attribute.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_adoptive',
								'default' => 'no',
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_none' => array(
								'name' => __( 'Hide None', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => __( 'Check this option to hide None on current attribute.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_none',
								'default' => 'no',
							),
							'prdctfltr_pa_'.$tax->attribute_name.'_term_customization' => array(
								'name' => __( 'Style Customization Key', 'prdctfltr' ),
								'type' => 'text',
								'desc' => __( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_term_customization',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;',
								'class' => 'pf_term_customization'
							),
							'section_pa_'.$tax->attribute_name.'_end' => array(
								'type' => 'sectionend',
								'id' => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_end'
							),

						);
					}
				}

			}
			else if ( isset($_GET['section']) && $_GET['section'] == 'overrides' ) {

				$curr_presets = get_option( 'prdctfltr_templates', array() );
				$curr_theme = wp_get_theme();

				$curr_presets_set = array();
				foreach( $curr_presets as $q => $w ) {
					$curr_presets_set[$q] = $q;
				}

				$settings = array(
					'section_overrides_filter_title' => array(
						'name'     => __( 'Shop and Archives Appearance', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Override default filter on the shop page.', 'prdctfltr' ) . '<span class="wcpfs_instock"></span>',
						'id'       => 'wc_settings_prdctfltr_overrides_filter_title'
					),
					'prdctfltr_shop_disable' => array(
						'name' => __( 'Enable/Disable Shop Page Product Filter', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option in order to disable the Product Filter on Shop page. This option can be useful for themes with custom Shop pages, if checked the default WooCommerce or', 'prdctfltr') . ' ' . $curr_theme->get('Name') . ' ' . __('filter template will be overriden only on product archives that support it.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_disable',
						'default' => 'no'
					),
					'prdctfltr_shop_page_override' => array(
						'name' => __( 'Shop Page Override', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Override default template on the shop page.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_page_override',
						'options' => array( '' => __( 'Default') ) + $curr_presets_set,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_disable_display' => array(
						'name' => __( 'Shop/Category Display Types And Product Filter', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select what display types will not show the Product Filter.  Use CTRL+Click to select multiple display types or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_display',
						'options' => array(
							'subcategories' => __( 'Show Categories', 'prdctfltr' ),
							'both' => __( 'Show Both', 'prdctfltr' )
						),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_overrides_filter_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_overrides_filter_end'
					),
					'section_restrictions_title' => array(
						'name'     => __( 'Product Filter Restrictions', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Limit filter appearance with Product Filter Restrictions.', 'prdctfltr' ) . '<span class="wcpfs_instock"></span>',
						'id'       => 'wc_settings_prdctfltr_restrictions_title'
					),
					'prdctfltr_showon_product_cat' => array(
						'name' => __( 'Show Filter Only On Categories', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'To show filter only on certain categories in Shop and Product Archives, select them from the list. Use CTRL+Click to select terms or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_showon_product_cat',
						'options' => $curr_cats,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_restrictions_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_restrictions_end'
					)
				);
				if ( $action == 'get' ) {
					$curr_or_settings = get_option( 'prdctfltr_overrides', array() );
				?>
					<h3><?php _e( 'Product Filter Overrides and Restrictions', 'prdctfltr' ); ?></h3>
					<p><?php _e( 'Override default filters. Select the term you wish and the desired filter preset and click Add Override to add a filter preset override when filtering or browsing this term.', 'prdctfltr' ); ?></p>
				<?php

					$curr_overrides = array(
						'product_cat' => array( 'text' => __( 'Product Categories Overrides', 'prdctfltr' ), 'values' => $curr_cats ),
						'product_tag' => array( 'text' => __( 'Product Tags Overrides', 'prdctfltr' ), 'values' => $curr_tags ),
						'characteristics' => array( 'text' => __( 'Product Characteristics Overrides', 'prdctfltr' ), 'values' => $curr_chars )
					);

					foreach ( $curr_overrides as $n => $m ) {
						if ( empty($m['values']) ) {
							continue;
						}
				?>
						<h3><?php echo $m['text']; ?></h3>
						<p class="<?php echo $n; ?>">
						<?php
							if ( isset($curr_or_settings[$n]) ) {
								foreach ( $curr_or_settings[$n] as $k => $v ) {
							?>
							<span class="prdctfltr_override"><input type="checkbox" class="pf_override_checkbox" /> <?php echo __('Term slug', 'prdctfltr') . ' : <span class="slug">' . $k . '</span>'; ?> <?php echo __('Filter Preset', 'prdctfltr') . ' : <span class="preset">' . $v; ?></span> <a href="#" class="button prdctfltr_or_remove"><?php _e('Remove Override', 'prdctfltr'); ?></a><span class="clearfix"></span></span>
							<?php
								}
							}
						?>
							<span class="prdctfltr_override_controls">
								<a href="#" class="button prdctfltr_or_remove_selected"><?php _e('Remove Selected Overrides', 'prdctfltr'); ?></a> <a href="#" class="button prdctfltr_or_remove_all"><?php _e('Remove All Overrides', 'prdctfltr'); ?></a>
							</span>
							<select class="prdctfltr_or_select">
						<?php
							foreach ( $m['values'] as $k => $v ) {
								printf( '<option value="%1$s">%2$s</option>', $k, $v );
							}
						?>
							</select>
							<select class="prdctfltr_filter_presets">
								<option value="default"><?php _e('Default', 'wcwar'); ?></option>
								<?php
									if ( !empty($curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
							<a href="#" class="button-primary prdctfltr_or_add"><?php _e( 'Add Override', 'prdctfltr' ); ?></a>
						</p>
				<?php
					}
				}
			}

			return apply_filters( 'wc_settings_products_filter_settings', $settings );
		}

		public static function prdctfltr_admin_save() {

			$curr_name = $_POST['curr_name'];

			$curr_data = array();
			$curr_data[$curr_name] = $_POST['curr_settings'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );

			if ( isset($curr_presets) && is_array($curr_presets) ) {
				if ( array_key_exists($curr_name, $curr_presets) ) {
					unset($curr_presets[$curr_name]);
				}

				$curr_presets = $curr_presets + $curr_data;

				update_option('prdctfltr_templates', $curr_presets);

				delete_transient( 'prdctfltr_' . $curr_name );

				die($curr_presets);
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_admin_load() {

			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );
			if ( isset( $curr_presets ) && !empty( $curr_presets ) && is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {
					die( stripslashes( $curr_presets[$curr_name] ) );
					exit;
				}
				die('1');
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_admin_delete() {

			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option( 'prdctfltr_templates', array() );
			if ( isset( $curr_presets ) && !empty( $curr_presets ) && is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {
					unset( $curr_presets[$curr_name] );
					update_option( 'prdctfltr_templates', $curr_presets );
				}

				delete_transient( 'prdctfltr_' . $curr_name );

				die('1');
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_add() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_override = $_POST['curr_override'];

			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			$curr_data = array(
				$curr_tax => array( $curr_term => $curr_override )
			);

			if ( isset($curr_overrides) && is_array($curr_overrides) ) {
				if ( isset($curr_overrides[$curr_tax]) && isset($curr_overrides[$curr_tax][$curr_term])) {
					unset($curr_overrides[$curr_tax][$curr_term]);
				}
				$curr_overrides = array_merge_recursive($curr_overrides, $curr_data);
				update_option('prdctfltr_overrides', $curr_overrides);
				die('1');
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_remove() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			if ( isset( $curr_overrides ) && is_array( $curr_overrides ) ) {
				if ( isset( $curr_overrides[$curr_tax] ) && isset( $curr_overrides[$curr_tax][$curr_term] ) ) {
					unset( $curr_overrides[$curr_tax][$curr_term] );
					update_option( 'prdctfltr_overrides', $curr_overrides );
					die('1');
					exit;
				}
			}

			die();
			exit;

		}

		public static function prdctfltr_c_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h3><?php _e( 'Advanced Fitler', 'prdctfltr' ); ?></h3>
			<p><?php _e( 'Setup advanced filter.', 'prdctfltr' ); ?></p>
			<table cass="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_title_%1$s">%2$s</label>', $pf_id, __( 'Override Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_title'] ) ? $_POST['pfa_title'] : '' );
							?>
							<span class="description"><?php _e( 'Enter title for the current advanced filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							$taxonomies = get_object_taxonomies( 'product', 'object' );
							printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $pf_id, __( 'Select Taxonomy', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $pf_id) ;
								foreach ( $taxonomies as $k => $v ) {
									if ( in_array( $k, array( 'product_type' ) ) ) {
										continue;
									}
									echo '<option value="' . $k . '"' . ( isset( $_POST['pfa_taxonomy'] ) && $_POST['pfa_taxonomy'] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php _e( 'Select current advanced filter product taxonomy.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_include_%1$s">%2$s</label>', $pf_id, __( 'Include Terms', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-multiselect">
							<?php
								if ( isset( $_POST['pfa_taxonomy'] ) ) {
									$catalog_attrs = get_terms( $_POST['pfa_taxonomy'], array( 'hide_empty' => 0 ) );
								}
								$curr_options = '';
								if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
									foreach ( $catalog_attrs as $term ) {
										$decode_slug = WC_Prdctfltr::prdctfltr_utf8_decode($term->slug);
										$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $decode_slug, $term->name, ( is_array( $_POST['pfa_include'] ) && in_array( $decode_slug, $_POST['pfa_include'] ) ? ' selected="selected"' : '' ) );
									}
								}
								printf( '<select name="pfa_include[%2$s][]" id="pfa_include_%2$s" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select terms to include. Use CTRL+Click to select terms or deselect all.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_style_%1$s">%2$s</label>', $pf_id, __( 'Appearance', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'pf_attr_text' => __( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset($_POST['pfa_style']) && $_POST['pfa_style'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $pf_id, __( 'Term Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$orderby_params = array(
									'' => __( 'None', 'prdctfltr' ),
									'id' => __( 'ID', 'prdctfltr' ),
									'name' => __( 'Name', 'prdctfltr' ),
									'number' => __( 'Number', 'prdctfltr' ),
									'slug' => __( 'Slug', 'prdctfltr' ),
									'count' => __( 'Count', 'prdctfltr' )
								);

								foreach ( $orderby_params as $k => $v ) {
									$selected = ( isset($_POST['pfa_orderby']) && $_POST['pfa_orderby'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select current advanced terms order by.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_order_%1$s">%2$s</label>', $pf_id, __( 'Term Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$order_params = array(
									'ASC' => __( 'ASC', 'prdctfltr' ),
									'DESC' => __( 'DESC', 'prdctfltr' )
								);

								foreach ( $order_params as $k => $v ) {
									$selected = ( isset($_POST['pfa_order']) && $_POST['pfa_order'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_limit_%1$s">%2$s</label>', $pf_id, __( 'Limit Terms', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-number">
							<?php
								printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $pf_id, isset( $_POST['pfa_limit'] ) ? $_POST['pfa_limit'] : '' ); ?>
							<span class="description"><?php _e( 'Limit number of terms to be shown. If limit is set, terms with most posts will be shown first.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy'] ) && $_POST['pfa_hierarchy'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable hierarchy on current advanced filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Taxonomy Hierarchy Mode', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_mode_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy_mode'] ) && $_POST['pfa_hierarchy_mode'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_mode_%1$s">%2$s</label>', $pf_id, __( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'showall' => __( 'Show all', 'prdctfltr' ),
									'subonly' => __( 'Keep only child terms', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset($_POST['pfa_mode']) && $_POST['pfa_mode'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Multi Select', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Multi Select', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_multiselect_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_multiselect'] ) && $_POST['pfa_multiselect'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable multi-select on current advanced filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_relation_%1$s">%2$s</label>', $pf_id, __( 'Term Relation', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'IN' => __( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
									'AND' => __( 'Filtered products have selected terms (AND)', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset($_POST['pfa_relation']) && $_POST['pfa_relation'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php _e( 'Select terms relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_adoptive'] ) && $_POST['pfa_adoptive'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to enable adoptive filtering on current advanced filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Disable None', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Disable None', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_none_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset($_POST['pfa_none']) && $_POST['pfa_none'] == 'yes' ? ' checked="checked"' : '' ) );
									_e( 'Check this option to hide none on current advanced filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $pf_id, __( 'Style Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" type="text" value="%2$s" class="pf_term_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_term_customization'] ) ? $_POST['pfa_term_customization'] : '' );
							?>
							<span class="description"><?php _e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die($html);
			exit;

		}

		public static function prdctfltr_c_terms() {

			$curr_tax = ( isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '' );

			if ( $curr_tax == '' ) {
				die();
				exit;
			}

			$html = '';

			$catalog_attrs = get_terms( $curr_tax, array( 'hide_empty' => 0 ) );
			$curr_options = '';
			if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
				foreach ( $catalog_attrs as $term ) {
					$curr_options .= sprintf( '<option value="%1$s">%2$s</option>', $term->slug, $term->name );
				}
			}

			$html .= sprintf( '<select name="pfa_include[%%%%][]" id="pfa_include_%%%%" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options );

			die($html);
			exit;

		}

		public static function prdctfltr_r_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h3><?php _e( 'Range Fitler', 'prdctfltr' ); ?></h3>
			<p><?php _e( 'Setup range filter.', 'prdctfltr' ); ?></p>
			<table cass="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_title_%1$s">%2$s</label>', $pf_id, __( 'Override Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfr_title'] ) ? $_POST['pfr_title'] : '' );
							?>
							<span class="description"><?php _e( 'Enter title for the current advanced filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $pf_id, __( 'Select Range', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$taxonomies = get_object_taxonomies( 'product', 'object' );
								printf('<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $pf_id );
								echo '<option value="price"' . ( !isset( $_POST['pfr_taxonomy'] ) || $_POST['pfr_taxonomy'] == 'price' ? ' selected="selected"' : '' ) . '>' . __( 'Price range', 'prdctfltr' ) . '</option>';
								foreach ( $taxonomies as $k => $v ) {
									if ( substr( $k, 0, 3 ) == 'pa_' && $k !== 'product_type' ) {
										$curr_label = wc_attribute_label( $v->name );
										$curr_value = $v->name;
									}
									else {
										$curr_label = $v->label;
										$curr_value = $k;
									}
									echo '<option value="' . $curr_value . '"' . ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] == $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php _e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_include_%1$s">%2$s</label>', $pf_id, __( 'Include Terms', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-multiselect">
							<?php

								if ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] !== 'price' ) {

									$catalog_attrs = get_terms( $_POST['pfr_taxonomy'], array( 'hide_empty' => 0 ) );
									$curr_options = '';
									if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
										foreach ( $catalog_attrs as $term ) {
											$decode_slug = WC_Prdctfltr::prdctfltr_utf8_decode($term->slug);
											$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $decode_slug, $term->name, ( is_array( $_POST['pfr_include'] ) && in_array( $decode_slug, $_POST['pfr_include'] ) ? ' selected="selected"' : '' ) );
										}
									}

									printf( '<select name="pfr_include[%2$s][]" id="pfr_include_%2$s" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
									$add_disabled = '';

								}
								else {

									printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $pf_id );
									$add_disabled = ' disabled';

								}
							?>
							<span class="description"><?php _e( 'Select terms to include. Use CTRL+Click to select terms or deselect all.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $pf_id, __( 'Term Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$orderby_params = array(
								'' => __( 'None', 'prdctfltr' ),
								'id' => __( 'ID', 'prdctfltr' ),
								'name' => __( 'Name', 'prdctfltr' ),
								'number' => __( 'Number', 'prdctfltr' ),
								'slug' => __( 'Slug', 'prdctfltr' ),
								'count' => __( 'Count', 'prdctfltr' )
							);
							foreach ( $orderby_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_orderby'] ) && $_POST['pfr_orderby'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}
							printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php _e( 'Select current range terms order by.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_order_%1$s">%2$s</label>', $pf_id, __( 'Term Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$order_params = array(
								'ASC' => __( 'ASC', 'prdctfltr' ),
								'DESC' => __( 'DESC', 'prdctfltr' )
							);
							foreach ( $order_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_order'] ) && $_POST['pfr_order'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php _e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_style_%1$s">%2$s</label>', $pf_id, __( 'Select Style', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$catalog_style = array(
								'flat' => __( 'Flat', 'prdctfltr' ),
								'modern' => __( 'Modern', 'prdctfltr' ),
								'html5' => __( 'HTML5', 'prdctfltr' ),
								'white' => __( 'White', 'prdctfltr' )
							);
							foreach ( $catalog_style as $k => $v ) {
								$selected = ( isset( $_POST['pfr_style'] ) && $_POST['pfr_style'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php _e( 'Select current range style.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Grid', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Grid', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_grid_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_grid'] ) && $_POST['pfr_grid'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									_e( 'Check this option to use grid in current range.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_adoptive'] ) && $_POST['pfr_adoptive'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									_e( 'Check this option to enable adoptive filtering on current range filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_custom_%1$s">%2$s</label>', $pf_id, __( 'Custom Settings', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php _e( 'Enter custom settings for the range filter. Visit this page for more information ', 'prdctfltr' ); ?> <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a></p>
							<?php
								printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="wmin-width:600px;margin-top:12px;min-height:150px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfr_custom'] ) ? $_POST['pfr_custom'] : '' ) );
							?>
						</td>
					</tr>

				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die($html);
			exit;

		}

		public static function prdctfltr_r_terms() {

			$curr_tax = ( isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '' );

			if ( $curr_tax == '' ) {
				die();
				exit;
			}

			$html = '';

			if ( !in_array( $curr_tax, array( 'price' ) ) ) {

				$catalog_attrs = get_terms( $curr_tax, array( 'hide_empty' => 0 ) );
				$curr_options = '';
				if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
					foreach ( $catalog_attrs as $term ) {
						$curr_options .= sprintf( '<option value="%1$s">%2$s</option>', $term->slug, $term->name );
					}
				}

				$html .= sprintf( '<select name="pfr_include[%%%%][]" id="pfr_include_%%%%" multiple="multiple" style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options );

			}
			else {
				$html .= sprintf( '<select name="pfr_include[%%%%][]" id="pfr_include_%%%%" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>' );
			}

			die($html);
			exit;

		}

		public static function set_terms() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$addkey = isset( $_POST['addkey'] ) ? $_POST['addkey'] : '';

			if ( $filter == '' ) {
				die();
				exit;
			}

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( $key !== '' ) {
				if ( isset( $language ) && $language !== false ) {
					$get_customization = get_option( $key . '_' . $lanugage, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
					$customization = $get_customization;
				}

			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => 'text',
					'settings' => array()
				);
				$key = 'wc_settings_prdctfltr_term_customization_' . uniqid();
			}

			if ( $filter == 'advanced' ) {
				$advanced = isset($_POST['advanced']) ? $_POST['advanced'] : '';

				if ( $filter == '' ) {
					die();
					exit;
				}

			}

			$curr_filter = $filter;

			switch ( $filter ) {

				case 'price' :
				case 'per_page' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;
				case 'sort' :
				case 'instock' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;

				default :

					if ( $filter == 'cat' ) {
						$curr_filter = 'product_cat';
					}
					else if ( $filter == 'tag' ) {
						$curr_filter = 'product_tag';
					}
					else if ( $filter == 'char' ) {
						$curr_filter = 'characteristics';
					}
					else if ( $filter == 'advanced' ) {
						$curr_filter = $advanced;
					}
					else if ( substr( $filter, 0, 3) == 'pa_' ) {
						$curr_filter = $filter;
					}
					else {
						$curr_filter = '';
					}

					if ( $curr_filter == '' ) {
						die();
						exit;
					}

					$baked_filters = self::get_terms( $curr_filter, $customization, $addkey );

				break;

			}

			if ( isset( $baked_filters ) ) {

				ob_start();
?>
				<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>"<?php echo $addkey !== '' ? ' data-addkey="' . $addkey . '"' : ''; ?>>
					<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php _e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
					<div class="prdctfltr_quickview_terms_inner">
						<div class="prdctfltr_quickview_terms_settings">
							<span class="prdctfltr_set_terms" data-taxonomy="<?php echo $curr_filter; ?>"><?php _e( 'Taxonomy', 'prdctfltr' ); ?>: <code><?php echo $curr_filter; ?></code></span>
							<a href="#" class="button-primary prdctfltr_set_terms_save"><?php _e( 'Save Customization', 'prdctfltr' ); ?></a>
<?php

							$select_style = '<label class="pf_wpml"><span>' . __( 'Select Style', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_terms_attr_select" name="style">';

							$styles = array(
								'text' => __( 'Text', 'prdctfltr' ),
								'color' => __( 'Color', 'prdctfltr' ),
								'image' => __( 'Thumbnail', 'prdctfltr' ),
								'image-text' => __( 'Thumbnail and Text', 'prdctfltr' ),
								'html' => __( 'HTML', 'prdctfltr' ),
								'select' => __( 'Select Box', 'prdctfltr' )
							);

							foreach ( $styles as $k => $v ) {
								$selected = $customization['style'] == $k ? ' selected="selected"' : '';
								$select_style .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
							}

							$select_style .= '</select></label>';

							echo $select_style;

							if ( function_exists( 'icl_get_languages' ) ) {
								$languages = icl_get_languages();

								$select_languages = '<label><span>' . __( 'Select Language', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_terms_attr_select" name="lang">';

								foreach ( $languages as $k => $v ) {
									$selected = $language == $k ? ' selected="selected"' : '';
									$select_languages .= '<option value="' . $k . '" ' . $selected . '>' . $v['native_name'] . '</option>';

								}

								$select_languages .= '</select></label>';

								echo $select_languages;


							}
?>
						</div>
						<div class="prdctfltr_quickview_terms_manager">
							<?php echo $baked_filters; ?>
						</div>
					</div>
				</div>
<?php
				$html = ob_get_clean();
			}

			if ( isset( $html ) ) {
				die( $html );
				exit;
			}

			die();
			exit;

		}

		public static function set_terms_new() {

			$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
			$style = isset($_POST['style']) ? $_POST['style'] : '';
			$key = isset($_POST['key']) ? $_POST['key'] : '';
			$addkey = isset($_POST['addkey']) ? $_POST['addkey'] : '';

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( $filter == '' || $style == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				if ( $language !== false ) {
					$get_customization = get_option( $key . '_' . $language, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['style'] ) && $get_customization['style'] = $style ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => $style,
					'settings' => array()
				);
			}

			$html = self::get_terms( $filter, $customization, $addkey );

			die( $html );
			exit;

		}

		public static function get_terms( $filter, $customization, $addkey ) {

			if ( $filter == '' ) {
				return '';
			}

			$curr_style = $customization['style'];
			$settings = $customization['settings'];

			if ( taxonomy_exists( $filter ) && !in_array( $filter, array( 'price', 'per_page' ) ) ) {
				$catalog_attrs = get_terms( $filter, array( 'hide_empty' => 0 ) );
			}
			else {
				switch ( $filter ) {
					case 'instock' :
						$curr_set = apply_filters( 'prdctfltr_catalog_instock', array(
							'both'    => __( 'All Products', 'prdctfltr' ),
							'in'      => __( 'In Stock', 'prdctfltr' ),
							'out'     => __( 'Out Of Stock', 'prdctfltr' )
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'sort' :
						$curr_set = apply_filters( 'prdctfltr_catalog_orderby', array(
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
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'price' :
						$filter_customization = WC_Prdctfltr::get_filter_customization( 'price', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $k => $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
							}
						}
						else {

							$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', 100 );
							$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', 100 );
							$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', 6 );

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

							if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
								$catalog_ready_price = array(
									'-' => apply_filters( 'prdctfltr_none_text', __( 'None', 'prdctfltr' ) )
								);
							}

							for ($i = 0; $i < $curr_price_limit; $i++) {

								if ( $i == 0 ) {
									$min_price = $min;
									$max_price = $curr_price_set;
								}
								else {
									$min_price = $curr_price_set+($i-1)*$curr_price_add;
									$max_price = $curr_price_set+$i*$curr_price_add;
								}

								$slug = $min_price . '-' . ( ($i+1) == $curr_price_limit ? '' : $max_price );
								$name = wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					case 'per_page' :
						$filter_customization = WC_Prdctfltr::get_filter_customization( 'price', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $v['value'], 'name' => $v['text'] );
							}
						}
						else {
							$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', 20 );
							$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range', 5 );

							$curr_perpage = array();

							for ($i = 1; $i <= $curr_perpage_limit; $i++) {

								$slug = $curr_perpage_set*$i;
								$name = $curr_perpage_set*$i . ' ' . ( get_option( 'wc_settings_prdctfltr_perpage_label', '' ) == '' ? __( 'Products', 'prdctfltr' ) : get_option( 'wc_settings_prdctfltr_perpage_label', '' ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					default :
						$catalog_attrs = array();
					break;
				}
			}

			if ( !empty( $catalog_attrs ) ) {

				ob_start();

				switch ( $curr_style ) {

					case 'text' :

						?>
							<div class="prdctfltr_st_term_style">
								<span class="prdctfltr_st_option">
									<em><?php _e('Type', 'prdctfltr'); ?></em>
									<select name="type">
								<?php
									$styles = array(
										'border' => __( 'Border', 'prdctfltr_set_termswoo' ),
										'background' => __( 'Background', 'prdctfltr_set_termswoo' ),
										'round' => __( 'Round', 'prdctfltr_set_termswoo' )
									);
									$selected = isset( $settings['type'] ) ? $settings['type'] : 'border';

									$c=0;
									foreach ( $styles as $k => $v ) {
										
								?>
										<option value="<?php echo $k; ?>"<?php echo $selected == $k ? ' selected="selected"' : ''; ?>><?php echo $v; ?></option>
								<?php
										$c++;
									}
								?>
									</select>
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e('Normal', 'prdctfltr'); ?></em> <input class="prdctfltr_st_color" type="text" name="normal" value="<?php echo isset( $settings['normal'] ) ? $settings['normal'] : '#bbbbbb'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e('Active', 'prdctfltr'); ?></em> <input class="prdctfltr_st_color" type="text" name="active" value="<?php echo isset( $settings['active'] ) ? $settings['active'] : '#333333'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php _e('Disabled', 'prdctfltr'); ?></em> <input class="prdctfltr_st_color" type="text" name="disabled" value="<?php echo isset( $settings['disabled'] ) ? $settings['disabled'] : '#eeeeee'; ?>"/>
								</span>

							</div>
						<?php

							foreach ( $catalog_attrs as $term ) {

							?>
								<div class="prdctfltr_st_term prdctfltr_style_text" data-term="<?php echo $term->slug; ?>">
									<span class="prdctfltr_st_option prdctfltr_st_option_plaintext">
										<em><?php echo $term->name . ' ' . __('Tooltip', 'prdctfltr'); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
									</span>
								</div>
							<?php
							}

					break;


					case 'color' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_color" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_color">
									<em><?php echo $term->name . ' ' . __('Color', 'prdctfltr'); ?></em> <input class="prdctfltr_st_color" type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : '#cccccc'; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . __('Tooltip', 'prdctfltr'); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'image' :
					case 'image-text' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_image" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_imgurl">
									<em><?php echo $term->name . ' ' . __('Image URL', 'prdctfltr'); ?></em> <input type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : ''; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_button">
									<em><?php _e( 'Add/Upload image', 'prdctfltr_set_termswoo' ); ?></em> <a href="#" class="prdctfltr_st_upload_media button"><?php _e('Image Gallery', 'prdctfltr'); ?></a>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . ( $curr_style == 'image' ? __( 'Tooltip', 'prdctfltr' ) : __( 'Text', 'prdctfltr' ) ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'html' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_html" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_html">
									<em><?php echo $term->name . ' ' . __('HTML', 'prdctfltr'); ?></em> <textarea type="text" name="term_<?php echo $term->slug; ?>"><?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : ''; ?></textarea>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . __('Tooltip', 'prdctfltr'); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;

					case 'select' :
					?>
						<div class="prdctfltr_select">
							<?php _e( 'Select Box currently has no special options. !Important Do not use select boxes inside the select box mode!', 'prdctfltr' ); ?>
						</div>
					<?php
					break;

					default :
					break;

				}

				$html = ob_get_clean();

				return $html;

			}
			else {
				return '';
			}

		}

		public static function save_terms() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $settings == '' ) {
				die();
				exit;
			}

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( isset($settings['style']) ) {
				if ( $language !== false ) {
					$key = $key . '_' . $language;
				}

				$alt['style'] = $settings['style'];
				unset($settings['style']);
				$alt['settings'] = $settings;

				update_option( $key, $alt );

				die( 'Updated!' );
				exit;
			}

			die();
			exit;

		}

		public static function remove_terms() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function add_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			switch ( $filter ) {
				case 'price' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_min">
							<em><?php _e( 'Minimum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_min" value="" />
						</span>
						<span class="pf_max">
							<em><?php _e( 'Maximum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_max" value="" />
						</span>
						<span class="pf_text">
							<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;

				break;
				case 'per_page' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_value">
							<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
							<input type="number" min="1" name="pf_value" value="" />
						</span>
						<span class="pf_text">
							<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;
				break;
				default :
				break;
			}

		}

		public static function set_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( $key !== '' ) {
				if ( isset( $language ) && $language !== false ) {
					$get_customization = get_option( $key . '_' . $lanugage, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
				$key = 'wc_settings_prdctfltr_filter_customization_' . uniqid();
			}

			ob_start();
?>
			<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>">
				<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php _e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
				<div class="prdctfltr_quickview_terms_inner">
					<div class="prdctfltr_quickview_filters_settings">
						<span class="prdctfltr_set_filters_type" data-filter="<?php echo $filter; ?>"><?php _e( 'Type', 'prdctfltr' ); ?>: <code><?php echo $filter; ?></code></span>
						<a href="#" class="button-primary prdctfltr_set_filters_save"><?php _e( 'Save Customization', 'prdctfltr' ); ?></a>
						<a href="#" class="button prdctfltr_set_filters_add"><?php _e( 'Add Filter', 'prdctfltr' ); ?></a>
<?php

						if ( function_exists( 'icl_get_languages' ) ) {
							$languages = icl_get_languages();

							$select_languages = '<label class="pf_wpml"><span>' . __( 'Select Language', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_filters_attr_select" name="lang">';

							foreach ( $languages as $k => $v ) {
								$selected = $language == $k ? ' selected="selected"' : '';
								$select_languages .= '<option value="' . $k . '" ' . $selected . '>' . $v['native_name'] . '</option>';

							}

							$select_languages .= '</select></label>';

							echo $select_languages;


						}
?>
					</div>
					<div class="prdctfltr_quickview_filters_manager prdctfltr_quickview_filter_<?php echo $filter; ?>">
<?php
						self::get_filters( $filter, $customization );
?>
					</div>
				</div>
			</div>
<?php
			$html = ob_get_clean();

			die( $html );
			exit;

		}

		public static function set_filters_new() {

			$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
			$key = isset($_POST['key']) ? $_POST['key'] : '';

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( $filter == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				if ( $language !== false ) {
					$get_customization = get_option( $key . '_' . $language, '' );
				}
				else {
					$get_customization = get_option( $key, '' );
				}

				if ( $get_customization !== '' && isset( $get_customization['filter'] ) && $get_customization['filter'] = $filter ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
			}

			$html = self::get_filters( $filter, $customization );

			die( $html );
			exit;

		}

		public static function get_filters( $filter, $customization ) {

			switch ( $filter ) {

				case 'price' :

					if ( empty( $customization ) ) {
						global $wpdb;

						$curr_prices = array();
						$curr_prices_currency = array();
						$catalog_ready_price = array();

						$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', '100' );
						$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', '100' );
						$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', '6' );

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

						if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
							$catalog_ready_price = array(
								'-' => __( 'None', 'prdctfltr' )
							);
						}
					}
					else {
						foreach( $customization['settings'] as $k => $v ) {
							$prices[] = array(
								'value' => $k,
								'text' => $v
							);
						}
						$curr_price_limit = count( $customization['settings'] );
					}

					for ( $i = 0; $i < $curr_price_limit; $i++ ) {

						if ( empty( $customization ) ) {

							if ( $i == 0 ) {
								$min_price = $min;
								$max_price = $curr_price_set;
							}
							else {
								$min_price = $curr_price_set+($i-1)*$curr_price_add;
								$max_price = $curr_price_set+$i*$curr_price_add;
							}

							$curr_text = strip_tags( wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) ) );

						}
						else {
							$vals = explode( '-', $prices[$i]['value'] );
							$min_price = ( isset( $vals[0] ) ? $vals[0] : '' );
							$max_price = ( isset( $vals[1] ) ? $vals[1] : '' );
							$curr_text = ( isset( $prices[$i]['text'] ) ? $prices[$i]['text'] : '' );
						}
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_min">
								<em><?php _e( 'Minimum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_min" value="<?php echo $min_price; ?>" />
							</span>
							<span class="pf_max">
								<em><?php _e( 'Maximum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_max" value="<?php echo ( ($i+1) == $curr_price_limit ? '' : $max_price ); ?>" />
							</span>
							<span class="pf_text">
								<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo $curr_text; ?></textarea>
							</span>
							<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
						</div>
<?php
					}

				break;

				case 'per_page' :

					if ( empty( $customization ) ) {

						$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', '20' );
						$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range_limit', '5' );

						$curr_perpage = array();

						for ( $i = 1; $i <= $curr_perpage_limit; $i++ ) {
							$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . ( $curr_options['wc_settings_prdctfltr_perpage_label'] == '' ? __( 'Products', 'prdctfltr' ) : $curr_options['wc_settings_prdctfltr_perpage_label'] );
						}

					}
					else {
						$curr_perpage_limit = count( $customization['settings'] );

						for ( $i = 0; $i < $curr_perpage_limit; $i++ ) {
							$curr_perpage[$customization['settings'][$i]['value']] = $customization['settings'][$i]['text'];
						}
					}

					foreach( $curr_perpage as $k => $v ) {
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_value">
								<em><?php _e( 'Value', 'prdctfltr' ); ?></em>
								<input type="number" name="pf_value" min="1" value="<?php echo $k; ?>" />
							</span>
							<span class="pf_text">
								<em><?php _e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo $v; ?></textarea>
							</span>
							<a href="#" class="button prdctfltr_filter_remove"><?php _e( 'Remove', 'prdctfltr' ); ?></a>
						</div>
<?php

					}

				break;

				default :
				break;

			}

		}

		public static function save_filters() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $filter == '' || $settings == '' ) {
				die();
				exit;
			}

			$language = WC_Prdctfltr::prdctfltr_wpml_language();

			if ( $language !== false ) {
				$key = $key . '_' . $language;
			}

			$alt['filter'] = $filter;

			if ( $filter == 'price' ) {
				foreach ( $settings as $set ) {
					$alt['settings'][$set['min'] . '-' . $set['max']] = $set['text'];
				}
			}
			else {
				$alt['settings'] = $settings;
			}

			update_option( $key, $alt );

			die( 'Updated!' );
			exit;

		}

		public static function remove_filters() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function analytics_reset() {

			delete_option( 'wc_settings_prdctfltr_filtering_analytics_stats' );
			die( 'Updated!' );
			exit;

		}

	}

	add_action( 'init', 'WC_Settings_Prdctfltr::init');

?>