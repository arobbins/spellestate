<?php

	/**
	 * DEPRICATED
	 */
	class PF_Attribute_Images {

		private $taxonomy;
		private $pf_meta;
		private $image_size = 'shop_thumb';
		private $image_width = 32;
		private $image_height = 32;

		public function __construct($attribute_image_key = 'thumbnail_id', $image_size = 'shop_thumb') {
			$this->pf_meta = $attribute_image_key;
			$this->image_size = $image_size;

			if (is_admin()) {
				add_action('admin_enqueue_scripts', array(&$this, 'pf_admin_scripts'));
				add_action('current_screen', array(&$this, 'pf_init_attribute_image'));

				add_action('created_term', array(&$this, 'pf_attribute_image_save'), 10, 3);
				add_action('edit_term', array(&$this, 'pf_attribute_image_save'), 10, 3);
			}
		}


		public function pf_admin_scripts() {
			global $woocommerce_pf_thumbs;
			$screen = get_current_screen();
			if (strpos($screen->id, 'pa_') !== false) :
				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
				wp_enqueue_media();
			endif;
		}


		public function pf_init_attribute_image() {
			global $woocommerce, $_wp_additional_image_sizes;
			$screen = get_current_screen();

			if (strpos($screen->id, 'pa_') !== false) :

				$this->taxonomy = $_REQUEST['taxonomy'];

				if (taxonomy_exists($_REQUEST['taxonomy'])) {
					$term_id = term_exists(isset($_REQUEST['tag_ID']) ? $_REQUEST['tag_ID'] : 0, $_REQUEST['taxonomy']);
					$term = 0;
					if ($term_id) {
						$term = get_term($term_id, $_REQUEST['taxonomy']);
					}

					$this->image_size = apply_filters('woocommerce_get_pf_thumbs_image_size', $this->image_size, $_REQUEST['taxonomy'], $term_id);
				}

				$the_size = isset($_wp_additional_image_sizes[$this->image_size]) ? $_wp_additional_image_sizes[$this->image_size] : $_wp_additional_image_sizes['shop_thumbnail'];

				if (isset($the_size['width']) && isset($the_size['height'])) {
					$this->image_width = $the_size['width'];
					$this->image_height = $the_size['height'];
				} else {
					$this->image_width = 32;
					$this->image_height = 32;
				}


				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ($attribute_taxonomies) {
					foreach ($attribute_taxonomies as $tax) {

						add_action('pa_' . $tax->attribute_name . '_add_form_fields', array(&$this, 'pf_add_attribute_image'), 10, 2);
						add_action('pa_' . $tax->attribute_name . '_edit_form_fields', array(&$this, 'pf_edit_attribute_image'), 10, 2);

						add_filter('manage_edit-pa_' . $tax->attribute_name . '_columns', array(&$this, 'pf_attribute_columns'));
						add_filter('manage_pa_' . $tax->attribute_name . '_custom_column', array(&$this, 'pf_attribute_column'), 10, 3);
					}
				}

			endif;
		}

		public function pf_add_attribute_image() {
			global $woocommerce;
			?>
			<div class="form-field pf_thumb-field pf_thumb-field-photo" style="overflow:visible;">
				<div id="pf_thumb-photo" class="<?php echo sanitize_title($this->pf_meta); ?>-photo">
					<label><?php _e('Thumbnail', 'woocommerce'); ?> <small><?php _e( '(This function is depricated! Please use the Product Filter term style manager!)', 'prdctfltr'); ?></small></label><br/>
					<div id="pf_thumbnail_<?php echo $this->pf_meta; ?>" style="float:left;margin-right:10px;">
						<img src="<?php echo $woocommerce->plugin_url() . '/assets/images/placeholder.png' ?>" width="<?php echo $this->image_width; ?>px" height="<?php echo $this->image_height; ?>px" />
					</div>
					<div style="line-height:60px;">
						<input type="hidden" id="pf_<?php echo $this->pf_meta; ?>" name="pf_meta[<?php echo $this->pf_meta; ?>][photo]" />
						<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce'); ?></button>
						<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'woocommerce'); ?></button>
					</div>
					<script type="text/javascript">

						var file_frame;

						jQuery(document).on( 'click', '.upload_image_button', function( event ){

							event.preventDefault();

							if ( file_frame ) {
								file_frame.open();
								return;
							}

							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: 'Choose an image',
								button: {
									text: 'Use image',
								},
								multiple: false
							});

							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#pf_<?php echo $this->pf_meta; ?>').val(attachment.id);
								jQuery('#pf_thumbnail_<?php echo $this->pf_meta; ?> img').attr('src', attachment.url);
								jQuery('.remove_image_button').show();
							});

							file_frame.open();
						});

						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#pf_thumbnail_<?php echo $this->pf_meta; ?> img').attr('src', '<?php echo WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif'; ?>');
							jQuery('#pf_<?php echo $this->pf_meta; ?>').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});

					</script>
					<div class="clear"></div>
				</div>
			</div>
			<?php
		}

		public function pf_edit_attribute_image($term, $taxonomy) {
			global $woocommerce;

			$pf_thumb_term = new PF_Attribute_Image($this->pf_meta, $term->term_id, $taxonomy, false, $this->image_size);
			$image = '';
			?>
			<tr class="form-field pf_thumb-field pf_thumb-field-photo" style="overflow:visible;">
				<th scope="row" valign="top"><label><?php _e('Thumbnail', 'prdctfltr'); ?> <small><?php _e( '(This function is depricated! Please use the Product Filter term style manager!)', 'prdctfltr'); ?></small></label></th>
				<td>
					<div id="pf_thumbnail_<?php echo $this->pf_meta; ?>" style="float:left;margin-right:10px;">
						<img src="<?php echo $pf_thumb_term->pf_image_src(); ?>"  width="<?php echo $pf_thumb_term->pf_get_width(); ?>px" height="<?php echo $pf_thumb_term->pf_get_height(); ?>px" />
					</div>
					<div style="line-height:60px;">
						<input type="hidden" id="pf_<?php echo $this->pf_meta; ?>" name="pf_meta[<?php echo $this->pf_meta; ?>][photo]" value="<?php echo $pf_thumb_term->pf_image_id(); ?>" />
						<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce'); ?></button>
						<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'woocommerce'); ?></button>
					</div>
					<script type="text/javascript">

						var file_frame;

						jQuery(document).on( 'click', '.upload_image_button', function( event ){

							event.preventDefault();

							if ( file_frame ) {
								file_frame.open();
								return;
							}

							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: 'Choose an image',
								button: {
									text: 'Use image',
								},
								multiple: false
							});

							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#pf_<?php echo $this->pf_meta; ?>').val(attachment.id);
								jQuery('#pf_thumbnail_<?php echo $this->pf_meta; ?> img').attr('src', attachment.url);
								jQuery('.remove_image_button').show();
							});

							file_frame.open();
						});

						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#pf_thumbnail_<?php echo $this->pf_meta; ?> img').attr('src', '<?php echo WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif'; ?>');
							jQuery('#pf_<?php echo $this->pf_meta; ?>').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});


					</script>
					<div class="clear"></div>
				</td>
			</tr>
			<?php
		}


		public function pf_attribute_image_save($term_id, $tt_id, $taxonomy) {
			if (isset($_POST['pf_meta'])) {

				$metas = $_POST['pf_meta'];
				if (isset($metas[$this->pf_meta])) {
					$data = $metas[$this->pf_meta];

					$photo = isset($data['photo']) ? $data['photo'] : '';

					update_woocommerce_term_meta($term_id, $taxonomy . '_' . $this->pf_meta . '_photo', $photo);
				}
			}
		}


		public function pf_attribute_columns($columns) {
			$new_columns = array();
			$new_columns['cb'] = $columns['cb'];
			$new_columns[$this->pf_meta] = __('Thumbnail', 'prdctfltr');
			unset($columns['cb']);
			$columns = array_merge($new_columns, $columns);
			return $columns;
		}

		public function pf_attribute_column($columns, $column, $id) {
			if ($column == $this->pf_meta) :
				$pf_thumb_term = new PF_Attribute_Image($this->pf_meta, $id, $this->taxonomy, false, $this->image_size);
				$columns .= $pf_thumb_term->pf_get_output();
			endif;
			return $columns;
		}

	}

	class PF_Attribute_Image {

		public $attribute_pf_meta;
		public $term_id;
		public $term;
		public $term_label;
		public $term_slug;
		public $taxonomy_slug;
		public $selected;
		public $thumbnail_src;
		public $thumbnail_id;
		public $size;
		public $width = 32;
		public $height = 32;

		public function __construct($attribute_data_key, $term_id, $taxonomy, $selected = false, $size = 'shop_thumbnail') {

			$this->attribute_pf_meta = $attribute_data_key;
			$this->term_id = $term_id;
			$this->term = get_term($term_id, $taxonomy);
			$this->term_label = $this->term->name;
			$this->term_slug = $this->term->slug;
			$this->taxonomy_slug = $taxonomy;
			$this->selected = $selected;
			$this->size = $size;

			$this->pf_init();
		}

		public function pf_init() {
			global $woocommerce, $_wp_additional_image_sizes;

			$this->pf_init_size($this->size);

			$this->thumbnail_id = get_woocommerce_term_meta($this->term_id, $this->pf_meta() . '_photo', true);

			$this->thumbnail_src = WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif';


				if ($this->thumbnail_id) {
				$imgsrc = wp_get_attachment_image_src($this->thumbnail_id, $this->size);
					if ($imgsrc && is_array($imgsrc)) {
						$this->thumbnail_src = current($imgsrc);
					} else {
						$this->thumbnail_src = WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif';
					}
				} else {
					$this->thumbnail_src = WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif';
				}

		}

		public function pf_init_size($size) {
			global $woocommerce, $_wp_additional_image_sizes;
			$this->size = $size;
			$the_size = isset($_wp_additional_image_sizes[$size]) ? $_wp_additional_image_sizes[$size] : $_wp_additional_image_sizes['shop_thumbnail'];
			if (isset($the_size['width']) && isset($the_size['height'])) {
				$this->width = $the_size['width'];
				$this->height = $the_size['height'];
			} else {
				$this->width = 32;
				$this->height = 32;
			}
		}

		public function pf_get_output($placeholder = true, $placeholder_src = 'default') {
			global $woocommerce;

			$picker = '';

			$href = apply_filters('woocommerce_pf_thumbs_get_pf_thumb_href', '#', $this);
			$anchor_class = apply_filters('woocommerce_pf_thumbs_get_pf_thumb_anchor_css_class', 'pf_thumb-anchor', $this);
			$image_class = apply_filters('woocommerce_pf_thumbs_get_pf_thumb_image_css_class', 'pf_thumb-img', $this);
			$image_alt = apply_filters('woocommerce_pf_thumbs_get_pf_thumb_image_alt', 'thumbnail', $this);

			if ( isset($this->thumbnail_src)) {
				$picker .= '<a href="' . $href . '" title="' . $this->term_label . '" class="' . $anchor_class . '">';
				$picker .= '<img src="' . apply_filters('woocommerce_pf_thumbs_get_pf_thumb_image', $this->thumbnail_src, $this->term_slug, $this->taxonomy_slug, $this) . '" alt="' . $image_alt . '" class="wp-post-image pf_thumb-photo' . $this->pf_meta() . ' ' . $image_class . '" width="32" height="32"/>';
				$picker .= '</a>';
			} elseif ($placeholder) {
				if ($placeholder_src == 'default') {
					$src = WC_Prdctfltr::$url_path . '/lib/images/pf-placeholder.gif';
				} else {
					$src = $placeholder_src;
				}

				$picker .= '<a href="' . $href . '" style="width:' . $this->width . 'px;height:' . $this->height . 'px;" title="' . $this->term_label . '"  class="' . $anchor_class . '">';
				$picker .= '<img src="' . $src . '" alt="' . $image_alt . '" class="wp-post-image pf_thumb-photo' . $this->pf_meta() . ' ' . $image_class . '" width="' . $this->width . '" height="' . $this->height . '"/>';
				$picker .= '</a>';
			} else {
				return '';
			}

			$out = '<div class="select-option pf_thumb-wrapper" data-value="' . $this->term_slug . '" ' . ($this->selected ? 'data-default="true"' : '') . '>';
			$out .= apply_filters('woocommerce_pf_thumbs_picker_html', $picker, $this);
			$out .= '</div>';

			return $out;
		}

		public function pf_image_src() {
			return $this->thumbnail_src;
		}

		public function pf_image_id() {
			return $this->thumbnail_id;
		}

		public function pf_get_width() {
			return $this->width;
		}

		public function pf_get_height() {
			return $this->height;
		}

		public function pf_meta() {
			return $this->taxonomy_slug . '_' . $this->attribute_pf_meta;
		}

	}

	new PF_Attribute_Images();

?>