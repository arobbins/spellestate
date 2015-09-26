<?php
	/*
	*
	*	Swift Framework Overrides
	*	------------------------------------------------
	*	Atelier specific functionality
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*/


	/* HEAD FILTERS
	================================================== */
	function sf_atelier_viewport_content() {
		return "width=device-width, initial-scale=1.0, maximum-scale=1";
	}
	add_filter('sf_viewport_content', 'sf_atelier_viewport_content');

	function sf_atelier_naked_default_header() {
		return "header-4";
	}
	add_filter('sf_naked_default_header', 'sf_atelier_naked_default_header');



	/* ICON FILTERS
	================================================== */

	// Header cart icon
	function sf_atelier_header_cart_icon() {
		return '<i class="sf-icon-cart"></i>';
	}
	add_filter('sf_header_cart_icon', 'sf_atelier_header_cart_icon');
	add_filter('sf_mobile_cart_icon', 'sf_atelier_header_cart_icon');

	// Header search icon
	function sf_atelier_header_search_icon() {
		return '<i class="sf-icon-search"></i>';
	}
	add_filter('sf_header_search_icon', 'sf_atelier_header_search_icon');

	// Header SuperSearch icon
	function sf_atelier_header_superssearch_icon() {
		return '<i class="sf-icon-supersearch"></i>';
	}
	add_filter('sf_header_supersearch_icon', 'sf_atelier_header_superssearch_icon');

	// Header contact icon
	function sf_atelier_header_contact_icon() {
		return '<i class="sf-icon-contact"></i>';
	}
	add_filter('sf_header_contact_icon', 'sf_atelier_header_contact_icon');

	// Header view cart icon
	function sf_atelier_view_cart_icon() {
		return '<i class="sf-icon-search-quickview"></i>';
	}
	add_filter('sf_view_cart_icon', 'sf_atelier_view_cart_icon');

	// Header checkout icon
	function sf_atelier_checkout_icon() {
		return '<i class="fa-long-arrow-right"></i>';
	}
	add_filter('sf_checkout_icon', 'sf_atelier_checkout_icon');

	// Header go to shop icon
	function sf_atelier_go_to_shop_icon() {
		return '<i class="sf-icon-cart"></i>';
	}
	add_filter('sf_go_to_shop_icon', 'sf_atelier_go_to_shop_icon');

	// Header wishlist icon
	function sf_atelier_wishlist_icon() {
		return '<i class="sf-icon-wishlist"></i>';
	}
	add_filter('sf_wishlist_icon', 'sf_atelier_wishlist_icon');

	// Post icon
	function sf_atelier_port_post_icon() {
		return "sf-icon-chevron-next";
	}
	add_filter('sf_post_standard_icon', 'sf_atelier_port_post_icon');
	add_filter('sf_port_post_icon', 'sf_atelier_port_post_icon');

	// Post Link icon
	function sf_atelier_post_link_icon() {
		return "fa-link";
	}
	add_filter('sf_post_link_icon', "sf_atelier_post_link_icon");

	// Post Lightbox icon
	function sf_atelier_post_lightbox_icon() {
		return "sf-icon-search";
	}
	add_filter('sf_post_lightbox_icon', "sf_atelier_post_lightbox_icon");

	// Post Video icon
	function sf_atelier_post_video_icon() {
		return "fa-youtube-play";
	}
	add_filter('sf_post_video_icon', "sf_atelier_post_video_icon");

	function sf_atelier_gallery_lightbox_icon() {
		return 'sf-icon-search';
	}
	add_filter('sf_gallery_lightbox_icon', 'sf_atelier_gallery_lightbox_icon');

	function sf_atelier_gallery_page_icon() {
		return 'sf-icon-chevron-next';
	}
	add_filter('sf_gallery_page_icon', 'sf_atelier_gallery_page_icon');

	// Add to cart icon
	function sf_atelier_add_to_cart_icon() {
		return '<i class="sf-icon-add-to-cart"></i>';
	}
	add_filter('add_to_cart_icon', 'sf_atelier_add_to_cart_icon');

	// Add to wishlist icon
	function sf_atelier_add_to_wishlist_icon() {
		return '<i class="sf-icon-wishlist"></i>';
	}
	add_filter('sf_add_to_wishlist_icon', 'sf_atelier_add_to_wishlist_icon');

	// View wishlist icon
	function sf_atelier_view_wishlist_icon() {
		return '<i class="sf-icon-search-quickview"></i>';
	}
	add_filter('sf_view_wishlist_icon', 'sf_atelier_view_wishlist_icon');

	// Wishlist icon
	function sf_atelier_wishlist_menu_icon() {
		return '<i class="sf-icon-wishlist"></i>';
	}
	add_filter('sf_wishlist_menu_icon', 'sf_atelier_wishlist_menu_icon');

	// Added to Wishlist icon
	function sf_atelier_added_to_wishlist_icon() {
		return '<i class="sf-icon-tick"></i>';
	}
	add_filter('sf_added_to_wishlist_icon', 'sf_atelier_added_to_wishlist_icon');

	// Search icon
	function sf_atelier_search_icon() {
		return '<i class="sf-icon-search"></i>';
	}
	add_filter('sf_search_icon', 'sf_atelier_search_icon');

	// FS video play icon
	function sf_atelier_play_icon() {
		return '<i class="fa-youtube-play"></i>';
	}
	add_filter('sf_fs_video_icon', 'sf_atelier_play_icon');

	function sf_atelier_play_icon_alt() {
		return '<i class="fa-play"></i>';
	}
	add_filter('sf_fs_video_icon_alt', 'sf_atelier_play_icon_alt');

	function sf_atelier_play_icon_alt3() {
		return '<i class="fa-youtube-play"></i>';
	}
	add_filter('sf_fs_video_icon_alt3', 'sf_atelier_play_icon_alt3');

	function sf_atelier_fullscreen_close_icon() {
		return '<i class="sf-icon-close"></i>';
	}
	add_filter('sf_fullscreen_close_icon', 'sf_atelier_fullscreen_close_icon');

	function sf_atelier_back_to_top_icon() {
		return '<i class="sf-icon-chevron-up"></i>';
	}
	add_filter('sf_back_to_top_icon', 'sf_atelier_back_to_top_icon');

	function sf_atelier_default_heart_icon() {
		return '<i class="sf-icon-heart"></i>';
	}
	add_filter('sf_default_heart_icon', 'sf_atelier_default_heart_icon');

	function sf_atelier_prev_icon() {
		return '<i class="sf-icon-chevron-prev"></i>';
	}
	add_filter('sf_prev_icon', 'sf_atelier_prev_icon');

	function sf_atelier_next_icon() {
		return '<i class="sf-icon-chevron-next"></i>';
	}
	add_filter('sf_next_icon', 'sf_atelier_next_icon');

	function sf_atelier_close_icon() {
		return '<i class="sf-icon-close"></i>';
	}
	add_filter('sf_close_icon', 'sf_atelier_close_icon');

	function sf_atelier_up_icon() {
		return '<i class="sf-icon-chevron-up"></i>';
	}
	add_filter('sf_up_icon', 'sf_atelier_up_icon');

	function sf_atelier_view_icon() {
		return '<i class="sf-icon-search"></i>';
	}
	add_filter('sf_view_icon', 'sf_atelier_view_icon');

	function sf_atelier_view_all_icon() {
		return '<i class="sf-icon-atelier-shop-grid"></i>';
	}
	add_filter('sf_view_all_icon', 'sf_atelier_view_all_icon');

	function sf_atelier_video_icon() {
		return '<i class="fa-youtube-play"></i>';
	}
	add_filter('sf_video_icon', 'sf_atelier_video_icon');

	function sf_atelier_audio_icon() {
		return '<i class="fa-music"></i>';
	}
	add_filter('sf_audio_icon', 'sf_atelier_audio_icon');

	function sf_atelier_picture_icon() {
		return '<i class="fa-picture-o"></i>';
	}
	add_filter('sf_picture_icon', 'sf_atelier_picture_icon');

	function sf_atelier_post_icon() {
		return '<i class="fa-file-text-o"></i>';
	}
	add_filter('sf_post_icon', 'sf_atelier_post_icon');

	function sf_atelier_comments_icon() {
		return '<svg version="1.1" class="comments-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 width="30px" height="30px" viewBox="0 0 30 30" enable-background="new 0 0 30 30" xml:space="preserve">
		<path fill="none" class="stroke" stroke="#252525" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="
			M13.958,24H2.021C1.458,24,1,23.541,1,22.975V2.025C1,1.459,1.458,1,2.021,1h25.957C28.542,1,29,1.459,29,2.025v20.949
			C29,23.541,28.542,24,27.979,24H21v5L13.958,24z"/>
		</svg>';
	}
	add_filter('sf_comments_icon', 'sf_atelier_comments_icon');

	function sf_atelier_loved_icon() {
		return '<svg version="1.1" class="loveit-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 width="30px" height="30px" viewBox="0 0 30 30" enable-background="new 0 0 30 30" xml:space="preserve">
		<g>
			<path fill="none" class="stroke" stroke="#252525" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="
				M5.631,24H2.021C1.459,24,1,23.541,1,22.975V2.025C1,1.459,1.459,1,2.021,1h25.957C28.543,1,29,1.459,29,2.025v20.949
				C29,23.541,28.543,24,27.979,24h-3.316"/>
			<path fill="#252525" class="fill" d="M19.994,22.895c-0.053-0.888-0.436-1.71-1.043-2.214C18.438,20.253,17.756,20,17.074,20
				c-1.035,0-1.684,0.45-2.068,1.009C14.611,20.45,13.961,20,12.926,20c-0.682,0-1.363,0.253-1.875,0.681
				c-0.609,0.504-0.992,1.326-1.045,2.214c-0.043,0.757,0.139,1.908,1.248,3.082c1.875,2.007,3.367,3.618,3.389,3.629L15.006,30
				l0.361-0.395c0.012-0.011,1.504-1.622,3.381-3.629C19.857,24.803,20.037,23.651,19.994,22.895z"/>
		</g>
		</svg>';
	}
	add_filter('sf_loved_icon', 'sf_atelier_loved_icon');

	function sf_atelier_link_icon() {
		return '<i class="sf-icon-chevron-next"></i>';
	}
	add_filter('sf_link_icon', 'sf_atelier_link_icon');

	function sf_atelier_sticky_icon() {
		return '<i class="fa-bookmark"></i>';
	}
	add_filter('sf_sticky_icon', 'sf_atelier_sticky_icon');

	function sf_atelier_quote_icon() {
		return '<i class="sf-icon-quotation-mark-start"></i>';
	}
	add_filter('sf_quote_icon', 'sf_atelier_quote_icon');

	function sf_atelier_mail_icon() {
		return '<i class="fa-envelope-o"></i>';
	}
	add_filter('sf_mail_icon', 'sf_atelier_mail_icon');

	function sf_atelier_phone_icon() {
		return '<i class="fa-phone"></i>';
	}
	add_filter('sf_phone_icon', 'sf_atelier_phone_icon');

	function sf_atelier_rows_icon() {
		return '<i class="fa-bars"></i>';
	}
	add_filter('sf_rows_icon', 'sf_atelier_rows_icon');

	/* SWIFT SLIDER FILTERS
	================================================== */
	function atelier_swift_slider_prev_icon() {
		return '<i class="sf-icon-slider-chevron-prev"></i>';
	}
	add_filter('swift_slider_prev_icon', 'atelier_swift_slider_prev_icon');

	function atelier_swift_slider_next_icon() {
		return '<i class="sf-icon-slider-chevron-next"></i>';
	}
	add_filter('swift_slider_next_icon', 'atelier_swift_slider_next_icon');

	function atelier_swift_slider_continue_icon() {
		return '<i class="sf-icon-slider-chevron-down"></i>';
	}
	add_filter('swift_slider_continue_icon', 'atelier_swift_slider_continue_icon');
	

	/* PAGE BUILDER TEMPLATES FILTER
	================================================== */
	function sf_atelier_spb_templates($prebuilt_templates) {
		$prebuilt_templates = array();
		return $prebuilt_templates;
	}
	add_filter('spb_prebuilt_templates', 'sf_atelier_spb_templates');


	/* PAGE BUILDER CAROUSEL ARROWS
	================================================== */
	function spb_atelier_carousel_arrows_html() {
		$carousel_arrows = '<div class="carousel-arrows"><a href="#" class="carousel-prev"><i class="sf-icon-chevron-prev"></i></a><a href="#" class="carousel-next"><i class="sf-icon-chevron-next"></i></a></div>';
		return $carousel_arrows;
	}
	add_filter('spb_carousel_arrows_html', 'spb_atelier_carousel_arrows_html');


	/* PRODUCT PAGE
	================================================== */
	function sf_woo_remove_reviews_tab($tabs) {
		
		global $sf_options;
		$product_reviews_pos = "default";
		if ( isset( $sf_options['product_reviews_pos'] ) ) {
		$product_reviews_pos = $sf_options['product_reviews_pos'];
		}
		
		if ( $product_reviews_pos == "default" ) {
			unset($tabs['reviews']);
		}
		
		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'sf_woo_remove_reviews_tab', 98);

	/* RELATED PRODUCTS */
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
	add_action( 'sf_after_single_product_reviews', 'woocommerce_output_related_products', 20);

	/* UPSELL PRODUCTS */
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_upsell_display', 60 );


	/* DATE FIGURE OVERLAY
	================================================== */
	function sf_date_figure_overlay() {
		global $post, $sf_options;
		$remove_dates  = $sf_options['remove_dates'];

		if ($remove_dates) {
			return;
		}

		$post_date_month = get_the_date('M');
		$post_date_day = get_the_date('d');
		return '<div class="date-overlay narrow-date-block"><span class="month">'.$post_date_month.'</span><span class="day">'.$post_date_day.'</span></div>';

	}
	add_filter( 'sf_before_recent_post_thumb', 'sf_date_figure_overlay' );

	function sf_masonry_date_figure_overlay() {
		global $post, $sf_options;
		$remove_dates  = $sf_options['remove_dates'];

		if ($remove_dates) {
			return;
		}

		$post_date_month = get_the_date('M');
		$post_date_day = get_the_date('d');
		$post_date_year = get_the_date('Y');
		return '<div class="date-overlay narrow-date-block"><span class="month">'.$post_date_month.'</span><span class="day">'.$post_date_day.'</span><span class="year">'.$post_date_year.'</span></div>';

	}
	add_filter( 'sf_before_masonry_post_thumb', 'sf_masonry_date_figure_overlay' );
	
	
	/* WOOCOMMERCE PRODUCT IMAGE HTML
	================================================== */
	function sf_atelier_single_product_image_html( $html, $post_ID ) {
		$product_image_width = apply_filters('sf_product_image_width', 700);
		$image_id			= get_post_thumbnail_id();
		$image_meta 		= sf_get_attachment_meta( $image_id );
		
		$image_caption = $image_alt = $image_title = $caption_html = "";
		if ( isset($image_meta) ) {
			$image_caption 		= esc_attr( $image_meta['caption'] );
			$image_title 		= esc_attr( $image_meta['title'] );
			$image_alt 			= esc_attr( $image_meta['alt'] );
		}
		$image_link  		= wp_get_attachment_url( $image_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		$thumb_image = wp_get_attachment_url( $image_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
		
		if ( $image_caption != "" ) {
			$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
		}

		$image_html = '<img class="product-slider-image" data-zoom-image="'.$image_link.'" src="'.$image_link.'" alt="'.$image_alt.'" title="'.$image_title.'" />';
	
		return sprintf( '<li itemprop="image" data-thumb="%s">%s%s<a href="%s" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fa-search-plus"></i></a></li>', $thumb_image, $caption_html, $image_html, $image_link, $image_caption, $image_title, $image_alt );
		
	}
	add_filter('woocommerce_single_product_image_html', 'sf_atelier_single_product_image_html', 15, 2);

						
	/* WOOCOMMERCE PRODUCT IMAGE THUMBS HTML
	================================================== */
	function sf_atelier_single_product_image_thumbnail_html( $html, $attachment_id, $post_ID, $image_class ) {
		$image       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		$image_link  = wp_get_attachment_url( $attachment_id, 'full' );
		$thumb_image = wp_get_attachment_url( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
		$image_meta 		= sf_get_attachment_meta( $attachment_id );
		$image_caption = $image_alt = $image_title = $caption_html = "";
		if ( isset($image_meta) ) {
			$image_caption 		= esc_attr( $image_meta['caption'] );
			$image_title 		= esc_attr( $image_meta['title'] );
			$image_alt 			= esc_attr( $image_meta['alt'] );
		}
		if ( $image_caption != "" ) {
			$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
		}
		$image_html = '<img class="product-slider-image" data-zoom-image="'.$image_link.'" src="'.$image_link.'" alt="'.$image_alt.'" title="'.$image_title.'" />';
		return '<li itemprop="image" data-thumb="'.$thumb_image.'">' . $image_html . '' . $caption_html . '<a href="'.$image_link.'" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="'.$image_caption.'" title="'.$image_title.'" alt="'.$image_alt.'"><i class="fa-search-plus"></i></a></li>';
	}
	add_filter('woocommerce_single_product_image_thumbnail_html', 'sf_atelier_single_product_image_thumbnail_html', 15, 4);
?>
