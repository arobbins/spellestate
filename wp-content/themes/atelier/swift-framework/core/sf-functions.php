<?php

    /*
    *
    *	Swift Framework Functions
    *	------------------------------------------------
    *	Swift Framework v3.0
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    *	sf_output_container_open()
    *	sf_output_container_close()
    *	sf_output_container_row_open()
    *	sf_output_container_row_close()
    *	sf_get_post_meta()
    *	sf_get_option()
    *	sf_theme_supports()
    *	sf_global_include_classes()
    *	sf_content_filter()
    *	sf_layerslider_overrides()
    *	sf_envato_toolkit_admin_init()
    *	sf_widget_area_filter()
    *	sf_get_tweets()
    *	sf_hyperlinks()
    *	sf_twitter_users()
    *	sf_encode_tweet()
    *	sf_latest_tweet()
    *	sf_posts_custom_columns()
    *	sf_list_galleries()
    *	sf_portfolio_related_posts()
    *	sf_has_previous_posts()
    *	sf_has_next_posts()
    *	sf_bwm_filter()
    *	sf_bwm_filter_script()
    *	sf_filter_wp_title()
    *	sf_maintenance_mode()
    *	sf_custom_login_logo()
    *	sf_language_flags()
    *	sf_hex2rgb()
    *	sf_get_comments_number()
    *	sf_get_menus_list()
    *	sf_get_category_list()
    *	sf_get_category_list_key_array()
    *	sf_get_woo_product_filters_array()
    *	sf_add_nofollow_cat()
    *	sf_remove_head_links()
    *	sf_current_page_url()
    *	sf_woocommerce_activated()
    *	sf_wpml_activated()
    *	sf_gravityforms_activated()
    *	sf_gopricing_activated()
    *	sf_gravityforms_list()
    *	sf_gopricing_list()
    *	sf_global_include_classes()
    *	sf_admin_bar_menu()
    *	sf_admin_css()
    *   sf_woo_list_parent_categories()
	*   sf_get_woo_product_parent_category_array()
    */

    /* LAYOUT OUTPUT
    ================================================== */
    function sf_output_container_open() {
        echo apply_filters( 'sf_container_open', '<div class="container">' );
    }

    function sf_output_container_close() {
        echo apply_filters( 'sf_container_close', '</div><!-- CLOSE .container -->' );
    }

    function sf_output_container_row_open() {
        echo apply_filters( 'sf_container_row_open', '<div class="container"><div class="row">' );
    }

    function sf_output_container_row_close() {
        echo apply_filters( 'sf_container_row_close', '</div></div><!-- CLOSE .container -->' );
    }


    /* PERFORMANCE FRIENDLY GET META FUNCTION
    ================================================== */
    if ( !function_exists( 'sf_get_post_meta' ) ) {
	    function sf_get_post_meta( $id, $key = "", $single = false ) {

	        $GLOBALS['sf_post_meta'] = isset( $GLOBALS['sf_post_meta'] ) ? $GLOBALS['sf_post_meta'] : array();
	        if ( ! isset( $id ) ) {
	            return;
	        }
	        if ( ! is_array( $id ) ) {
	            if ( ! isset( $GLOBALS['sf_post_meta'][ $id ] ) ) {
	                //$GLOBALS['sf_post_meta'][ $id ] = array();
	                $GLOBALS['sf_post_meta'][ $id ] = get_post_meta( $id );
	            }
	            if ( ! empty( $key ) && isset( $GLOBALS['sf_post_meta'][ $id ][ $key ] ) && ! empty( $GLOBALS['sf_post_meta'][ $id ][ $key ] ) ) {
	                if ( $single ) {
	                    return maybe_unserialize( $GLOBALS['sf_post_meta'][ $id ][ $key ][0] );
	                } else {
	                    return array_map( 'maybe_unserialize', $GLOBALS['sf_post_meta'][ $id ][ $key ] );
	                }
	            }

	            if ( $single ) {
	                return '';
	            } else {
	                return array();
	            }

	        }

	        return get_post_meta( $id, $key, $single );
	    }
    }


    /* PERFORMANCE FRIENDLY GET OPTION FUNCTION
    ================================================== */
    if ( !function_exists( 'sf_get_option' ) ) {
	    function sf_get_option( $key, $default = "" ) {
	        // Original calls
	        //return get_option($key, $default);

	        // Optimised calls
	        if ( isset( $GLOBALS['sf_customizer'][ $key ] ) ) {
	            return $GLOBALS['sf_customizer'][ $key ];
	        } else if ( isset( $default ) ) {
	            return $default;
	        }

	        return '';
	    }
    }


    /* CHECK THEME FEATURE SUPPORT
    ================================================== */
    if ( !function_exists( 'sf_theme_supports' ) ) {
        function sf_theme_supports( $feature ) {
        	$supports = get_theme_support( 'swiftframework' );
        	$supports = $supports[0];
    		if ($supports[ $feature ] == "") {
    			return false;
    		} else {
        		return isset( $supports[ $feature ] );
        	}
        }
    }


    /* EDITOR STYLES
    ================================================== */
    if ( ! function_exists( 'sf_custom_mce_styles' ) ) {
        function sf_custom_mce_styles( $args ) {

            $style_formats = array(
                array( 'title' => 'Impact Text', 'selector' => 'p', 'classes' => 'impact-text' ),
                array( 'title' => 'Impact Text Large', 'selector' => 'p', 'classes' => 'impact-text-large' )
            );

            $args['style_formats'] = json_encode( $style_formats );

            return $args;
        }

        add_filter( 'tiny_mce_before_init', 'sf_custom_mce_styles' );
    }

    if ( ! function_exists( 'sf_mce_add_buttons' ) ) {
        function sf_mce_add_buttons( $buttons ) {
            array_splice( $buttons, 1, 0, 'styleselect' );

            return $buttons;
        }

        add_filter( 'mce_buttons_2', 'sf_mce_add_buttons' );
    }

    function sf_add_editor_styles() {
        add_editor_style( '/css/editor-style.css' );
    }
    add_action( 'init', 'sf_add_editor_styles' );



    /* SHORTCODE GENERATOR SETUP
    ================================================== */
    // Create TinyMCE's editor button & plugin for Swift Framework Shortcodes
    add_action( 'init', 'sf_sc_button' );

    function sf_sc_button() {
        if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
            add_filter( 'mce_external_plugins', 'sf_add_tinymce_plugin' );
            add_filter( 'mce_buttons', 'sf_register_shortcode_button' );
        }
    }

    function sf_register_shortcode_button( $button ) {
        array_push( $button, 'separator', 'swiftframework_shortcodes' );

        return $button;
    }

    function sf_add_tinymce_plugin( $plugins ) {
        $plugins['swiftframework_shortcodes'] = get_template_directory_uri() . '/swift-framework/shortcodes/tinymce.editor.plugin.js';

        return $plugins;
    }


    /* LANGUAGE SPECIFIC POST ID
    ================================================== */
    function sf_post_id( $id, $type = 'post' ) {
        if ( function_exists( 'icl_object_id' ) ) {
            return icl_object_id( $id, $type, true );
        } else {
            return $id;
        }
    }


    /* DYNAMIC GLOBAL INCLUDE CLASSES
    ================================================== */
    function sf_global_include_classes() {

        // INCLUDED FUNCTIONALITY SETUP
        global $post, $sf_has_portfolio, $sf_has_blog, $sf_has_products, $sf_include_maps, $sf_include_isotope, $sf_include_carousel, $sf_include_parallax, $sf_include_infscroll, $sf_has_progress_bar, $sf_has_chart, $sf_has_countdown, $sf_has_imagebanner, $sf_has_team, $sf_has_portfolio_showcase, $sf_has_gallery, $sf_has_galleries, $sf_include_ml_parallax;

        $sf_inc_class = "";

        if ( $sf_has_portfolio ) {
            $sf_inc_class .= "has-portfolio ";
        }
        if ( $sf_has_blog ) {
            $sf_inc_class .= "has-blog ";
        }
        if ( $sf_has_products ) {
            $sf_inc_class .= "has-products ";
        }

        if ( $post ) {
            $content = $post->post_content;
            if ( function_exists( 'has_shortcode' ) && $content != "" ) {
                if ( has_shortcode( $content, 'related_products' ) ||
                	 has_shortcode( $content, 'best_selling_products' ) ||
                	 has_shortcode( $content, 'top_rated_products' ) ||
                	 has_shortcode( $content, 'sale_products' ) ||
                	 has_shortcode( $content, 'recent_products' ) ||
                	 has_shortcode( $content, 'product_attribute' ) ||
                	 has_shortcode( $content, 'product_category' ) ||
                	 has_shortcode( $content, 'featured_products' ) ||
                	 has_shortcode( $content, 'products' ) ||
                	 has_shortcode( $content, 'product_page' ) ||
                	 is_active_widget( false, false, 'woocommerce_top_rated_products', true ) ||
                	 is_active_widget( false, false, 'woocommerce_recently_viewed_products', true ) ||
                	 is_active_widget( false, false, 'woocommerce_recent_reviews', true ) ||
                	 is_active_widget( false, false, 'woocommerce_products', true ) ||
                	 is_active_widget( false, false, 'woocommerce_product_categories', true ) ||
                	 is_active_widget( false, false, 'woocommerce_widget_cart', true )
                	) {
                    $sf_inc_class .= "has-products ";
                    $sf_include_isotope = true;
                }
            }
        }

        if ( $sf_include_maps ) {
            $sf_inc_class .= "has-map ";
        }
        if ( $sf_include_carousel ) {
            $sf_inc_class .= "has-carousel ";
        }
        if ( $sf_include_parallax ) {
            $sf_inc_class .= "has-parallax ";
        }
        if ( $sf_include_ml_parallax ) {
            $sf_inc_class .= "has-ml-parallax ";
        }
        if ( $sf_has_progress_bar ) {
            $sf_inc_class .= "has-progress-bar ";
        }
        if ( $sf_has_chart ) {
            $sf_inc_class .= "has-chart ";
        }
        if ( $sf_has_countdown ) {
            $sf_inc_class .= "has-countdown ";
        }
        if ( $sf_has_imagebanner ) {
            $sf_inc_class .= "has-imagebanner ";
        }
        if ( $sf_has_team ) {
            $sf_inc_class .= "has-team ";
        }
        if ( $sf_has_portfolio_showcase ) {
            $sf_inc_class .= "has-portfolio-showcase ";
        }
        if ( $sf_has_gallery ) {
            $sf_inc_class .= "has-gallery ";
        }
        if ( $sf_has_galleries ) {
            $sf_inc_class .= "has-galleries ";
        }
        if ( $sf_include_infscroll ) {
            $sf_inc_class .= "has-infscroll ";
        }

        global $sf_options;

        $enable_product_zoom = $sf_options['enable_product_zoom'];
        if ( $enable_product_zoom || isset($_GET['product_zoom']) ) {
            $sf_inc_class .= "has-productzoom ";
        }

		if (isset($sf_options['enable_stickysidebars'])) {
			$enable_stickysidebars = $sf_options['enable_stickysidebars'];
			if ($enable_stickysidebars) {
				$sf_inc_class .= "stickysidebars ";
			}
		}

        if ( isset( $sf_options['disable_megamenu'] ) ) {
            $disable_megamenu = $sf_options['disable_megamenu'];
            if ( $disable_megamenu ) {
                $sf_inc_class .= "disable-megamenu ";
            }
        }

        return $sf_inc_class;
    }


	/* CONTENT CHECK FOR SCRIPT REQUIREMENT
    ================================================== */
    if ( ! function_exists( 'sf_savepost_content_script_check' ) ) {
        function sf_savepost_content_script_check( $post_id ) {

			 // Make sure meta is added to the post, not a revision
            if ( $the_post = wp_is_post_revision( $post_id ) ) {
                $post_id = $the_post;
            }

			$post = get_post($post_id);

	        if ( $post ) {

	            $content = $post->post_content;

	            $page_slider = sf_get_post_meta( $post->ID, 'sf_page_slider', true );

	            if ( function_exists( 'has_shortcode' ) ) {

		            // Google Maps Script
	                if ( has_shortcode( $content , 'spb_gmaps' ) || has_shortcode( $content , 'spb_directory' ) ) {
						update_post_meta( $post_id , 'sf_page_has_map' , 1 );
	                } else {
		                delete_post_meta( $post_id , 'sf_page_has_map' );
	                }

	                // Go Pricing
	                if ( has_shortcode( $content , 'spb_gopricing' ) || has_shortcode( $content , 'go_pricing' ) ) {
						update_post_meta( $post_id , 'sf_page_has_gopricing' , 1 );
	                } else {
		                delete_post_meta( $post_id , 'sf_page_has_gopricing' );
	                }

	                // Products
	                if ( has_shortcode( $content, 'related_products' ) || has_shortcode( $content, 'best_selling_products' ) || has_shortcode( $content, 'top_rated_products' ) || has_shortcode( $content, 'sale_products' ) || has_shortcode( $content, 'recent_products' ) || has_shortcode( $content, 'product_attribute' ) || has_shortcode( $content, 'product_category' ) || has_shortcode( $content, 'featured_products' ) || has_shortcode( $content, 'products' ) || has_shortcode( $content , 'spb_products' ) || has_shortcode( $content , 'spb_products_mini' ) || has_shortcode( $content , 'sf_addtocart_button' ) ) {
						update_post_meta( $post_id , 'sf_page_has_products' , 1 );
	                } else {
		                delete_post_meta( $post_id , 'sf_page_has_products' );
	                }

					// Swift Slider
					if ( $page_slider == "swift-slider" || has_shortcode( $content, 'swift_slider' ) || has_shortcode( $content, 'spb_swift_slider' ) ) {
						update_post_meta( $post_id , 'sf_page_has_swiftslider' , 1 );
	                } else {
		                delete_post_meta( $post_id , 'sf_page_has_swiftslider' );
	                }

					// Revolution Slider
					if ( $page_slider == "revslider" || has_shortcode( $content, 'rev_slider' ) || has_shortcode( $content, 'spb_slider' ) ) {
						update_post_meta( $post_id , 'sf_page_has_revslider' , 1 );
	                } else {
		                delete_post_meta( $post_id , 'sf_page_has_revslider' );
	                }
	            }
	        }

	    }
		add_action( 'save_post', 'sf_savepost_content_script_check' );
	}


	/* REMOVE PLUGIN SCRIPT IF NOT NEEDED
    ================================================== */
    if ( ! function_exists( 'sf_remove_plugin_scripts' ) ) {
        function sf_remove_plugin_scripts() {

	        global $sf_options;

	        if ( !sf_theme_supports('swift-smartscript') || is_admin() ) {
		        return;
	        }

	        $enable_swift_smartscript = $sf_options['enable_swift_smartscript'];

	        if ( !$enable_swift_smartscript ) {
		        return;
	        }

			global $post;
			$page_has_swiftslider = $page_has_gopricing = false;
			$page_slider = "";

			if ( $post ) {
				$page_has_swiftslider 	= sf_get_post_meta( $post->ID, 'sf_page_has_swiftslider', true );
				$page_slider = sf_get_post_meta( $post->ID, 'sf_page_slider', true );

				$page_has_gopricing     = sf_get_post_meta( $post->ID, 'sf_page_has_gopricing', true );
			}

			// Swift Slider
			if ( !$page_has_swiftslider && $page_slider != "swift-slider" ) {
				wp_dequeue_style( 'swift-slider' );
				wp_dequeue_style( 'swift-slider-min' );
				wp_dequeue_script( 'swift-slider' );
				wp_dequeue_script( 'swift-slider-min' );
			}

			// GoPricing
			if ( sf_gopricing_activated() && !$page_has_gopricing ) {
				wp_dequeue_style( 'go_pricing_styles' );
				wp_dequeue_style( 'go_pricing_jqplugin-mediaelementjs' );
				wp_dequeue_style( 'go_pricing_jqplugin-mediaelementjs-skin' );
				wp_dequeue_script( 'go_pricing_scripts' );
				wp_dequeue_script( 'go_pricing_jqplugin-mediaelementjs' );
				wp_dequeue_script( 'wp-mediaelement' );
			}


	    }
		add_action( 'wp_enqueue_scripts', 'sf_remove_plugin_scripts', 99 );
	}


    /* SHORTCODE FIX
    ================================================== */
    if ( ! function_exists( 'sf_content_filter' ) ) {
        function sf_content_filter( $content ) {
            // array of custom shortcodes requiring the fix
            $block = join( "|", array(
                    "alert",
                    "sf_button",
                    "icon",
                    "sf_iconbox",
                    "sf_imagebanner",
                    "social",
                    "sf_social_share",
                    "highlight",
                    "decorative_ampersand",
                    "blockquote1",
                    "blockquote2",
                    "blockquote3",
                    "pullquote",
                    "one_half",
                    "one_half_last",
                    "one_third",
                    "one_third_last",
                    "two_third",
                    "two_third_last",
                    "one_fourth",
                    "one_fourth_last",
                    "three_fourth",
                    "three_fourth_last",
                    "one_half",
                    "one_half_last",
                    "progress_bar",
                    "chart",
                    "sf_count",
                    "sf_countdown",
                    "sf_tooltip",
                    "sf_modal",
                    "sf_fullscreenvideo",
                    "sf_visibility",
                    "table",
                    "trow",
                    "thcol",
                    "tcol",
                    "list",
                    "list_item",
                    "hr",
                    "accordion",
                    "panel",
                    "tabs",
                    "tab",
                    "sf_supersearch",
                    "gallery",
                    "spb_accordion",
                    "spb_accordion_tab",
                    "spb_blog",
                    "spb_boxed_content",
                    "spb_clients",
                    "spb_codesnippet",
                    "spb_divider",
                    "spb_faqs",
                    "spb_gallery",
                    "spb_googlechart",
                    "spb_gmaps",
                    "spb_latest_tweets",
                    "spb_message",
                    "spb_parallax",
                    "spb_portfolio",
                    "spb_portfolio_carousel",
                    "spb_portfolio_showcase",
                    "spb_posts_carousel",
                    "spb_products",
                    "spb_products_mini",
                    "spb_recent_posts",
                    "spb_slider",
                    "spb_sitemap",
                    "spb_search",
                    "spb_supersearch",
                    "spb_tabs",
                    "spb_tab",
                    "spb_text_block",
                    "spb_team",
                    "spb_testimonial",
                    "spb_testimonial_carousel",
                    "spb_testimonial_slider",
                    "spb_toggle",
                    "spb_tour",
                    "spb_tweets_slider",
                    "spb_video",
                    "spb_blank_spacer",
                    "spb_image",
                    "spb_blog_grid",
                    "spb_promo_bar",
                    "spb_gravityforms",
                    "spb_campaigns",
                    "spb_column",
                    "spb_row",
                    "spb_icon_box",
                    "spb_multilayer_parallax",
                    "spb_multilayer_parallax_layer",
                    "spb_image_banner"
                ) );
            // opening tag
            $rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );
            // closing tag
            $rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );

            return $rep;
        }

        add_filter( "the_content", "sf_content_filter" );
    }


    /* LAYERSLIDER OVERRIDES
    ================================================== */
    function sf_layerslider_overrides() {
        // Disable auto-updates
        $GLOBALS['lsAutoUpdateBox'] = false;
    }

    add_action( 'layerslider_ready', 'sf_layerslider_overrides' );


    /* ENVATO TOOLKIT
    ================================================== */
    function sf_envato_toolkit_admin_init() {

        // Include the Toolkit Library
        include_once( SF_INCLUDES_PATH . '/envato-wordpress-toolkit-library/class-envato-wordpress-theme-upgrader.php' );

        // Display a notice in the admin to remind the user to enter their credentials
        function sf_envato_toolkit_credentials_admin_notices() {
            $message = sprintf( __( "To enable theme update notifications, please enter your Envato Marketplace credentials in the %s", "swiftframework" ),
                "<a href='" . admin_url() . "admin.php?page=envato-wordpress-toolkit'>Envato WordPress Toolkit Plugin</a>" );
            echo "<div id='message' class='updated below-h2'><p>{$message}</p></div>";
        }

        // Use credentials used in toolkit plugin so that we don't have to show our own forms anymore
        $credentials = get_option( 'envato-wordpress-toolkit' );
        if ( empty( $credentials['user_name'] ) || empty( $credentials['api_key'] ) ) {
            add_action( 'admin_notices', 'sf_envato_toolkit_credentials_admin_notices' );

            return;
        }

        // Check updates only after a while
        $lastCheck = get_option( 'toolkit-last-toolkit-check' );
        if ( false === $lastCheck ) {
            update_option( 'toolkit-last-toolkit-check', time() );

            return;
        }

        // Check for an update every 3 hours
        if ( ( time() - $lastCheck ) < 10800 ) {
            return;
        }

        // Update the time we last checked
        update_option( 'toolkit-last-toolkit-check', time() );

        // Check for updates
        $upgrader = new Envato_WordPress_Theme_Upgrader( $credentials['user_name'], $credentials['api_key'] );
        $updates  = $upgrader->check_for_theme_update();

        // Add update alert, to update the theme
        if ( $updates->updated_themes_count ) {
            add_action( 'admin_notices', 'sf_envato_toolkit_admin_notices' );
        }

        // Display a notice in the admin that an update is available
        function sf_envato_toolkit_admin_notices() {
            $message = sprintf( __( "A theme update is available! Head over to %s to update it now.", "swiftframework" ),
                "<a href='" . admin_url() . "admin.php?page=envato-wordpress-toolkit'>Envato WordPress Toolkit Plugin</a>" );
            echo "<div id='message' class='updated below-h2'><p>{$message}</p></div>";
        }

    }

    if ( class_exists( 'Envato_WP_Toolkit' ) ) {
        add_action( 'admin_init', 'sf_envato_toolkit_admin_init' );
    }


    /* FEATURED IMAGE IN RSS FEED
    ================================================== */
    if ( ! function_exists( 'sf_featured_image_rss' ) ) {
        function sf_featured_image_rss( $content ) {
            global $post;
            if ( is_feed() ) {
                if ( has_post_thumbnail( $post->ID ) ) {
                    $output  = get_the_post_thumbnail( $post->ID, 'large', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );
                    $content = $output . $content;
                }
            }

            return $content;
        }

        add_filter( 'the_content', 'sf_featured_image_rss' );
    }


    /* FEED CONTENT WHEN PB ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_custom_feed_content' ) ) {
	    function sf_custom_feed_content( $content ) {
	    	global $post;
	    	$pb_status = get_post_meta($post->ID, '_spb_js_status', true);

	    	if ($pb_status == "true") {
	    		$custom_excerpt = get_post_meta( $post->ID, 'sf_custom_excerpt', true );
	    		return $custom_excerpt;
	    	} else {
	        	return $content;
	        }
	    }
	    add_filter( 'the_content_feed', 'sf_custom_feed_content' );
	    add_filter( 'the_excerpt_rss', 'sf_custom_feed_content' );
    }


    /* ATTACHMENT PAGE IMAGE SIZE
    ================================================== */
    if ( ! function_exists( 'sf_alter_attachment_image' ) ) {
        function sf_alter_attachment_image( $p ) {
            return '<p>' . wp_get_attachment_link( 0, 'full', false ) . '</p>';
        }

        add_filter( 'prepend_attachment', 'sf_alter_attachment_image' );
    }


    /* WIDGET AREA FILTER
    ================================================== */
    if ( ! function_exists( 'sf_widget_area_filter' ) ) {
        function sf_widget_area_filter( $options ) {
            $options = array(
                'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
                'after_widget'  => '</section>',
                'before_title'  => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
                'after_title'   => '</span></h4></div>',
            );

            return $options;
        }

        add_filter( 'redux_custom_widget_args', 'sf_widget_area_filter' );
    }


    /* TWEET FUNCTIONS
    ================================================== */
    if ( ! function_exists( 'sf_get_tweets' ) ) {
        function sf_get_tweets( $twitterID, $count, $type = "", $item_class = "col-sm-4" ) {

            global $sf_options;
            $enable_twitter_rts = false;
            if ( isset( $sf_options['enable_twitter_rts'] ) ) {
                $enable_twitter_rts = $sf_options['enable_twitter_rts'];
            }

            $content         = "";
            $blog_grid_count = 0;

            if ( function_exists( 'getTweets' ) ) {

                $options = array(
                    'trim_user'       => true,
                    'exclude_replies' => false,
                    'include_rts'     => $enable_twitter_rts
                );

                $tweets = getTweets( $twitterID, $count, $options );

                if ( is_array( $tweets ) ) {

                    if ( isset( $tweets["error"] ) && $tweets["error"] != "" ) {

                        return '<li>' . $tweets["error"] . '</li>';

                    } else {

                        foreach ( $tweets as $tweet ) {

                            if ( $type == "blog-grid" ) {

                                $content .= '<li class="blog-item col-sm-sf-5" data-date="' . strtotime( $tweet['created_at'] ) . '" data-sortid="' . $blog_grid_count . '">';
                                $content .= '<a class="grid-link" href="https://twitter.com/' . $twitterID . '/status/' . $tweet['id_str'] . '" target="_blank"></a>';
                                $content .= '<div class="grid-no-image">';
                                $content .= '<h6>' . __( "Twitter", "swiftframework" ) . '</h6>';

                                $blog_grid_count = $blog_grid_count + 2;

                            } else if ( $type == "blog" ) {

                                $content .= '<li class="blog-item ' . $item_class . '" data-date="' . strtotime( $tweet['created_at'] ) . '">';
                                $content .= '<a class="grid-link" href="https://twitter.com/' . $twitterID . '/status/' . $tweet['id_str'] . '" target="_blank"></a>';
                                $content .= '<div class="details-wrap">';
                                $content .= '<h6>' . __( "Twitter", "swiftframework" ) . '</h6>';

                            } else if ( $type == "blog-fw" ) {

                                $content .= '<li class="blog-item ' . $item_class . '" data-date="' . strtotime( $tweet['created_at'] ) . '">';
                                $content .= '<a class="grid-link" href="https://twitter.com/' . $twitterID . '/status/' . $tweet['id_str'] . '" target="_blank"></a>';
                                $content .= '<div class="details-wrap">';
                                $content .= '<h6>' . __( "Twitter", "swiftframework" ) . '</h6>';

                            } else {

                                $content .= '<li>';

                            }

                            if ( isset( $tweet['text'] ) && $tweet['text'] ) {

                                if ( $type == "blog" || $type == "blog-grid" || $type == "blog-fw" ) {
                                    $content .= '<h2 class="tweet-text">';
                                } else {
                                    $content .= '<div class="tweet-text slide-content-wrap">';
                                }

                                $the_tweet = apply_filters( 'sf_tweet_text', $tweet['text'] );

                                /*
                                Twitter Developer Display Requirements
                                https://dev.twitter.com/terms/display-requirements

                                2.b. Tweet Entities within the Tweet text must be properly linked to their appropriate home on Twitter. For example:
                                  i. User_mentions must link to the mentioned user's profile.
                                 ii. Hashtags must link to a twitter.com search with the hashtag as the query.
                                iii. Links in Tweet text must be displayed using the display_url
                                     field in the URL entities API response, and link to the original t.co url field.
                                */

                                // i. User_mentions must link to the mentioned user's profile.
                                if ( isset( $tweet['entities']['user_mentions'] ) && is_array( $tweet['entities']['user_mentions'] ) ) {
                                    foreach ( $tweet['entities']['user_mentions'] as $key => $user_mention ) {
                                        $the_tweet = preg_replace(
                                            '/@' . $user_mention['screen_name'] . '/i',
                                            '<a href="http://www.twitter.com/' . $user_mention['screen_name'] . '" target="_blank">@' . $user_mention['screen_name'] . '</a>',
                                            $the_tweet );
                                    }
                                }

                                // ii. Hashtags must link to a twitter.com search with the hashtag as the query.
                                if ( isset( $tweet['entities']['hashtags'] ) && is_array( $tweet['entities']['hashtags'] ) ) {
                                    foreach ( $tweet['entities']['hashtags'] as $key => $hashtag ) {
                                        $the_tweet = preg_replace(
                                            '/#' . $hashtag['text'] . '/i',
                                            '<a href="https://twitter.com/search?q=%23' . $hashtag['text'] . '&amp;src=hash" target="_blank">#' . $hashtag['text'] . '</a>',
                                            $the_tweet );
                                    }
                                }

                                // iii. Links in Tweet text must be displayed using the display_url
                                //      field in the URL entities API response, and link to the original t.co url field.
                                if ( isset( $tweet['entities']['urls'] ) && is_array( $tweet['entities']['urls'] ) ) {
                                    foreach ( $tweet['entities']['urls'] as $key => $link ) {

                                        $link_url = "";

                                        if ( isset( $link['expanded_url'] ) ) {
                                            $link_url = $link['expanded_url'];
                                        } else {
                                            $link_url = $link['url'];
                                        }

                                        $the_tweet = preg_replace(
                                            '`' . $link['url'] . '`',
                                            '<a href="' . $link_url . '" target="_blank">' . $link_url . '</a>',
                                            $the_tweet );
                                    }
                                }

                                // Custom code to link to media
                                if ( isset( $tweet['entities']['media'] ) && is_array( $tweet['entities']['media'] ) ) {
                                    foreach ( $tweet['entities']['media'] as $key => $media ) {

                                        $the_tweet = preg_replace(
                                            '`' . $media['url'] . '`',
                                            '<a href="' . $media['url'] . '" target="_blank">' . $media['url'] . '</a>',
                                            $the_tweet );
                                    }
                                }

                                $content .= $the_tweet;

                                if ( $type == "blog" || $type == "blog-grid" || $type == "blog-fw" ) {
                                    $content .= '</h2>';
                                } else {
                                    $content .= '</div>';
                                }

                                // 3. Tweet Actions
                                //    Reply, Retweet, and Favorite action icons must always be visible for the user to interact with the Tweet. These actions must be implemented using Web Intents or with the authenticated Twitter API.
                                //    No other social or 3rd party actions similar to Follow, Reply, Retweet and Favorite may be attached to a Tweet.
                                // 4. Tweet Timestamp
                                //    The Tweet timestamp must always be visible and include the time and date. e.g., "3:00 PM - 31 May 12".
                                // 5. Tweet Permalink
                                //    The Tweet timestamp must always be linked to the Tweet permalink.

                                $content .= '<div class="twitter_intents">' . "\n";
                                $content .= '<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to=' . $tweet['id_str'] . '"><i class="fa-reply"></i></a>' . "\n";
                                $content .= '<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id=' . $tweet['id_str'] . '"><i class="fa-retweet"></i></a>' . "\n";
                                $content .= '<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id=' . $tweet['id_str'] . '"><i class="fa-star"></i></a>' . "\n";

                                $date     = strtotime( $tweet['created_at'] ); // retrives the tweets date and time in Unix Epoch terms
                                $blogtime = current_time( 'U' ); // retrives the current browser client date and time in Unix Epoch terms
                                $dago     = human_time_diff( $date, $blogtime ) . ' ' . sprintf( __( 'ago', 'swiftframework' ) ); // calculates and outputs the time past in human readable format
                                $content .= '<a class="timestamp" href="https://twitter.com/' . $twitterID . '/status/' . $tweet['id_str'] . '" target="_blank">' . $dago . '</a>' . "\n";
                                $content .= '<a class="twitter-id" href="http://twitter.com/' . $twitterID . '" target="_blank">@' . $twitterID . '</a>';
                                $content .= '</div>' . "\n";
                            } else {
                                $content .= '<a href="http://twitter.com/' . $twitterID . '" target="_blank">@' . $twitterID . '</a>';
                            }

                            if ( $type == "blog" || $type == "blog-grid" || $type == "blog-fw" ) {
                                $content .= '<data class="date" data-date="' . $date . '" value="' . $date . '">' . $dago . '</data>';
                                $content .= '<div class="author"><span>@' . $twitterID . '</span></div>';
                                $content .= '<div class="tweet-icon"><i class="fa-twitter"></i></div>' . "\n";
                                $content .= '</div>';
                            }

                            $content .= '</li>';
                        }
                    }

                    return $content;

                }
            } else {
                return '<li><div class="tweet-text">Please install the oAuth Twitter Feed Plugin and follow the theme documentation to set it up.</div></li>';
            }

        }
    }

    function sf_hyperlinks( $text ) {
        $text = preg_replace( '/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"$1\" class=\"twitter-link\">$1</a>", $text );
        $text = preg_replace( '/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text );
        // match name@address
        $text = preg_replace( "/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i", "<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text );
        //mach #trendingtopics. Props to Michael Voigt
        $text = preg_replace( '/([\.|\,|\:|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text );

        return $text;
    }

    function sf_twitter_users( $text ) {
        $text = preg_replace( '/([\.|\,|\:|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text );

        return $text;
    }

    function sf_encode_tweet( $text ) {
        $text = mb_convert_encoding( $text, "HTML-ENTITIES", "UTF-8" );

        return $text;
    }


    /* LATEST TWEET FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_latest_tweet' ) ) {
        function sf_latest_tweet( $count, $twitterID ) {

            global $sf_options;
            $enable_twitter_rts = false;
            if ( isset( $sf_options['enable_twitter_rts'] ) ) {
                $enable_twitter_rts = $sf_options['enable_twitter_rts'];
            }

            $content = "";

            if ( $twitterID == "" ) {
                return __( "Please provide your Twitter username", "swiftframework" );
            }

            if ( function_exists( 'getTweets' ) ) {

                $options = array(
                    'trim_user'       => true,
                    'exclude_replies' => false,
                    'include_rts'     => $enable_twitter_rts
                );

                $tweets = getTweets( $twitterID, $count, $options );

                if ( is_array( $tweets ) ) {

                    foreach ( $tweets as $tweet ) {

                        $content .= '<li>';

                        if ( is_array( $tweet ) && $tweet['text'] ) {

                            $content .= '<div class="tweet-text">';

                            $the_tweet = apply_filters( 'sf_tweet_text', $tweet['text'] );

                            /*
                            Twitter Developer Display Requirements
                            https://dev.twitter.com/terms/display-requirements

                            2.b. Tweet Entities within the Tweet text must be properly linked to their appropriate home on Twitter. For example:
                              i. User_mentions must link to the mentioned user's profile.
                             ii. Hashtags must link to a twitter.com search with the hashtag as the query.
                            iii. Links in Tweet text must be displayed using the display_url
                                 field in the URL entities API response, and link to the original t.co url field.
                            */

                            // i. User_mentions must link to the mentioned user's profile.
                            if ( is_array( $tweet['entities']['user_mentions'] ) ) {
                                foreach ( $tweet['entities']['user_mentions'] as $key => $user_mention ) {
                                    $the_tweet = preg_replace(
                                        '/@' . $user_mention['screen_name'] . '/i',
                                        '<a href="http://www.twitter.com/' . $user_mention['screen_name'] . '" target="_blank">@' . $user_mention['screen_name'] . '</a>',
                                        $the_tweet );
                                }
                            }

                            // ii. Hashtags must link to a twitter.com search with the hashtag as the query.
                            if ( is_array( $tweet['entities']['hashtags'] ) ) {
                                foreach ( $tweet['entities']['hashtags'] as $key => $hashtag ) {
                                    $the_tweet = preg_replace(
                                        '/#' . $hashtag['text'] . '/i',
                                        '<a href="https://twitter.com/search?q=%23' . $hashtag['text'] . '&amp;src=hash" target="_blank">#' . $hashtag['text'] . '</a>',
                                        $the_tweet );
                                }
                            }

                            // iii. Links in Tweet text must be displayed using the display_url
                            //      field in the URL entities API response, and link to the original t.co url field.
                            if ( is_array( $tweet['entities']['urls'] ) ) {
                                foreach ( $tweet['entities']['urls'] as $key => $link ) {

                                    $link_url = "";

                                    if ( isset( $link['expanded_url'] ) ) {
                                        $link_url = $link['expanded_url'];
                                    } else {
                                        $link_url = $link['url'];
                                    }

                                    $the_tweet = preg_replace(
                                        '`' . $link['url'] . '`',
                                        '<a href="' . $link_url . '" target="_blank">' . $link_url . '</a>',
                                        $the_tweet );
                                }
                            }

                            // Custom code to link to media
                            if ( isset( $tweet['entities']['media'] ) && is_array( $tweet['entities']['media'] ) ) {
                                foreach ( $tweet['entities']['media'] as $key => $media ) {
                                    $the_tweet = preg_replace(
                                        '`' . $media['url'] . '`',
                                        '<a href="' . $media['url'] . '" target="_blank">' . $media['url'] . '</a>',
                                        $the_tweet );
                                }
                            }

                            $content .= $the_tweet;

                            $content .= '</div>';

                            // 3. Tweet Actions
                            //    Reply, Retweet, and Favorite action icons must always be visible for the user to interact with the Tweet. These actions must be implemented using Web Intents or with the authenticated Twitter API.
                            //    No other social or 3rd party actions similar to Follow, Reply, Retweet and Favorite may be attached to a Tweet.
                            // 4. Tweet Timestamp
                            //    The Tweet timestamp must always be visible and include the time and date. e.g., "3:00 PM - 31 May 12".
                            // 5. Tweet Permalink
                            //    The Tweet timestamp must always be linked to the Tweet permalink.

                            $content .= '<div class="twitter_intents">' . "\n";
                            $content .= '<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to=' . $tweet['id_str'] . '"><i class="fa-reply"></i></a>' . "\n";
                            $content .= '<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id=' . $tweet['id_str'] . '"><i class="fa-retweet"></i></a>' . "\n";
                            $content .= '<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id=' . $tweet['id_str'] . '"><i class="fa-star"></i></a>' . "\n";

                            $date     = strtotime( $tweet['created_at'] ); // retrives the tweets date and time in Unix Epoch terms
                            $blogtime = current_time( 'U' ); // retrives the current browser client date and time in Unix Epoch terms
                            $dago     = human_time_diff( $date, $blogtime ) . ' ' . sprintf( __( 'ago', 'swiftframework' ) ); // calculates and outputs the time past in human readable format
                            $content .= '<a class="timestamp" href="https://twitter.com/' . $twitterID . '/status/' . $tweet['id_str'] . '" target="_blank">' . $dago . '</a>' . "\n";
                            $content .= '</div>' . "\n";
                        } else {
                            $content .= '<a href="http://twitter.com/' . $twitterID . '" target="_blank">@' . $twitterID . '</a>';
                        }
                        $content .= '</li>';
                    }
                }

                return $content;
            } else {
                return '<li><div class="tweet-text">Please install the oAuth Twitter Feed Plugin and follow the theme documentation to set it up.</div></li>';
            }
        }
    }


    /* GET INSTAGRAMS FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_get_instagrams' ) ) {
        function sf_get_instagrams() {

            if ( class_exists( 'PhotoTileForInstagramBot' ) ) {

                $bot = new PhotoTileForInstagramBot();

                $optiondetails = $bot->option_defaults();
                $options       = array();
                foreach ( $optiondetails as $opt => $details ) {
                    $options[ $opt ] = $details['default'];
                    if ( isset( $details['short'] ) && isset( $atts[ $details['short'] ] ) ) {
                        $options[ $opt ] = $atts[ $details['short'] ];
                    }
                }
                $id = rand( 100, 1000 );
                $bot->set_private( 'wid', 'id' . $id );
                $bot->set_private( 'options', $options );
                $bot->do_alpine_method( 'update_global_options' );
                $bot->do_alpine_method( 'enqueue_style_and_script' );
                // Do the photo search
                $bot->do_alpine_method( 'photo_retrieval' );

                $return = '<div id="' . $bot->get_private( 'id' ) . '-by-shortcode-' . $id . '" class="AlpinePhotoTiles_inpost_container">';
                $return .= $bot->get_active_result( 'hidden' );
                if ( $bot->check_active_result( 'success' ) ) {
                    if ( 'vertical' == $options['style_option'] ) {
                        $bot->do_alpine_method( 'display_vertical' );
                    } elseif ( 'cascade' == $options['style_option'] ) {
                        $bot->do_alpine_method( 'display_cascade' );
                    } else {
                        $bot->do_alpine_method( 'display_hidden' );
                    }
                    $return .= $bot->get_private( 'output' );
                }
                // If user does not have necessary extensions
                // or error occured before content complete, report such...
                elseif ( $bot->check_active_option( 'general_hide_message' ) ) {
                    $return .= '<!-- Sorry:<br>' . $bot->get_active_result( 'message' ) . '-->';
                } else {
                    $return .= 'Sorry:<br>' . $bot->get_active_result( 'message' );
                }
                $return .= '</div>';

                return $return;
            }
        }
    }


    /* CHECK IF BUDDYPRESS PAGE
    ================================================== */
    function sf_is_buddypress() {
        $bp_component = "";
        if ( function_exists( 'bp_current_component' ) ) {
            $bp_component = bp_current_component();
        }

        return $bp_component;
    }


    /* CHECK IF BBPRESS PAGE
    ================================================== */
    function sf_is_bbpress() {
        $bbpress = false;
        if ( function_exists( 'is_bbpress' ) ) {
            $bbpress = is_bbpress();
        }

        return $bbpress;
    }


    /* CUSTOM POST TYPE COLUMNS
    ================================================== */
    function sf_posts_custom_columns( $column ) {
        global $post;
        switch ( $column ) {
            case "description":
                the_excerpt();
                break;
            case "thumbnail":
                the_post_thumbnail( 'thumbnail' );
                break;
            case "portfolio-category":
                echo get_the_term_list( $post->ID, 'portfolio-category', '', ', ', '' );
                break;
            case "swift-slider-category":
                echo get_the_term_list( $post->ID, 'swift-slider-category', '', ', ', '' );
                break;
            case "spb-section-category":
                echo get_the_term_list( $post->ID, 'spb-section-category', '', ', ', '' );
                break;
            case "gallery-category":
                echo get_the_term_list( $post->ID, 'gallery-category', '', ', ', '' );
                break;
            case "testimonials-category":
                echo get_the_term_list( $post->ID, 'testimonials-category', '', ', ', '' );
                break;
            case "team-category":
                echo get_the_term_list( $post->ID, 'team-category', '', ', ', '' );
                break;
            case "clients-category":
                echo get_the_term_list( $post->ID, 'clients-category', '', ', ', '' );
                break;
            case "directory-category":
                echo get_the_term_list( $post->ID, 'directory-category', '', ', ', '' );
                break;
            case "directory-location":
                echo get_the_term_list( $post->ID, 'directory-location', '', ', ', '' );
                break;
            case "faqs-category":
                echo get_the_term_list( $post->ID, 'faqs-category', '', ', ', '' );
                break;
        }
    }
    add_action( "manage_posts_custom_column", "sf_posts_custom_columns" );
    

    /* GALLERY LIST FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_list_galleries' ) ) {
        function sf_list_galleries() {
            $galleries_list  = array();
            $galleries_query = new WP_Query( array( 'post_type' => 'galleries', 'posts_per_page' => - 1 ) );
            while ( $galleries_query->have_posts() ) : $galleries_query->the_post();
                $galleries_list[ get_the_title() ] = get_the_ID();
            endwhile;
            wp_reset_query();

            if ( empty( $galleries_list ) ) {
                $galleries_list[] = "No galleries found";
            }

            return $galleries_list;
        }
    }


    /* PORTFOLIO RELATED POSTS
    ================================================== */
    function sf_portfolio_related_posts( $post_id, $item_count = 3 ) {
        $query = new WP_Query();
        $terms = wp_get_object_terms( $post_id, 'portfolio-category' );
        if ( count( $terms ) ) {
            $post_ids = get_objects_in_term( $terms[0]->term_id, 'portfolio-category' );

            $index = array_search( $post_id, $post_ids );
            if ( $index !== false ) {
                unset( $post_ids[ $index ] );
            }

            $args  = array(
                'post_type'      => 'portfolio',
                'post__in'       => $post_ids,
                'taxonomy'       => 'portfolio-category',
                'term'           => $terms[0]->slug,
                'posts_per_page' => $item_count
            );
            $query = new WP_Query( $args );
        }

        // Return our results in query form
        return $query;
    }


    /* REVIEW CALCULATION FUNCTIONS
    ================================================== */
    function sf_review_barpercent( $value, $format ) {
       	$barpercentage = $value;
        return $barpercentage;
    }

    function sf_review_value_adjust( $value, $format ) {
    	$adjusted_value = 0;
    	if ($format == "points" && intval($value, 10) > 10) {
    	$adjusted_value = intval($value, 10) / 10;
    	} else {
       	$adjusted_value = $value;
       	}
        return $adjusted_value;
    }

    if ( ! function_exists( 'sf_review_overall' ) ) {
        function sf_review_overall( $arr, $format ) {
            $total = $average = "";
            $count = count( $arr ); //total numbers in array
            if ( $count > 0 ) {
                foreach ( $arr as $value ) {
                	if ( $format == "points" && $value > 10) {
                		$total = $total + ($value / 10); // total value of array numbers
                	} else {
	                    $total = $total + $value; // total value of array numbers
                    }
                }
                $average = floor( ( $total / $count ) ); // get average value
            }

            return $average;
        }
    }


    /* LOADING ANIMATION
    ================================================== */
    if ( ! function_exists( 'sf_loading_animation' ) ) {
        function sf_loading_animation( $id = '', $el_class = "" ) {

            global $sf_options;
            $style = $sf_options['page_transition'];

            if ( $el_class == "preloader" && $style == "loading-bar" ) {
                $style = "circle-bar";
            }

            if ( $style == "loading-bar" ) {
            	return;
            }

            $animation = "";

            if ( $id != "" ) {
                $animation .= '<div id="' . $id . '" class="' . $style . '">';
            } else {
                $animation .= '<div class="' . $style . '">';
            }

            $animation .= '<div class="spinner ' . $el_class . '">';
            if ( $style == "wave" ) {
                $animation .= '<div class="rect1"></div>';
                $animation .= '<div class="rect2"></div>';
                $animation .= '<div class="rect3"></div>';
                $animation .= '<div class="rect4"></div>';
                $animation .= '<div class="rect5"></div>';
            } else if ( $style == "circle-bar" ) {
                $animation .= '<div class="circle"></div>';
            } else if ( $style == "orbit-bars" ) {
                $animation .= '<div></div>';
            } else if ( $style == "circle" ) {
                $animation .= '<div class="spinner-container container1">';
                $animation .= '<div class="circle1"></div>';
                $animation .= '<div class="circle2"></div>';
                $animation .= '<div class="circle3"></div>';
                $animation .= '<div class="circle4"></div>';
                $animation .= '</div>';
                $animation .= '<div class="spinner-container container2">';
                $animation .= '<div class="circle1"></div>';
                $animation .= '<div class="circle2"></div>';
                $animation .= '<div class="circle3"></div>';
                $animation .= '<div class="circle4"></div>';
                $animation .= '</div>';
                $animation .= '<div class="spinner-container container3">';
                $animation .= '<div class="circle1"></div>';
                $animation .= '<div class="circle2"></div>';
                $animation .= '<div class="circle3"></div>';
                $animation .= '<div class="circle4"></div>';
                $animation .= '</div>';
            } else if ( $style == "three-bounce" ) {
                $animation .= '<div class="bounce1"></div>';
                $animation .= '<div class="bounce2"></div>';
                $animation .= '<div class="bounce3"></div>';
            } else if ( $style == "chasing-circle" ) {
	            $animation .= '<svg class="circular" height="50" width="50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="6" stroke-miterlimit="10" /></svg>';
            }
            $animation .= '</div>';

            $animation .= '</div>';

            return $animation;

        }
    }


    /* NAVIGATION CHECK
    ================================================== */
    //functions tell whether there are previous or next 'pages' from the current page
    //returns 0 if no 'page' exists, returns a number > 0 if 'page' does exist
    //ob_ functions are used to suppress the previous_posts_link() and next_posts_link() from printing their output to the screen
    function sf_has_previous_posts() {
        ob_start();
        previous_posts_link();
        $result = strlen( ob_get_contents() );
        ob_end_clean();

        return $result;
    }

    function sf_has_next_posts() {
        ob_start();
        next_posts_link();
        $result = strlen( ob_get_contents() );
        ob_end_clean();

        return $result;
    }


    /* BETTER WORDPRESS MINIFY FILTER
    ================================================== */
    function sf_bwm_filter( $excluded ) {
        global $is_IE;

        $excluded = array( 'fontawesome', 'ssgizmo' );

        if ( $is_IE ) {
            $excluded = array(
                'bootstrap',
                'sf-main',
                'sf-responsive',
                'fontawesome',
                'ssgizmo',
                'woocommerce_frontend_styles'
            );
        }

        return $excluded;
    }

    add_filter( 'bwp_minify_style_ignore', 'sf_bwm_filter' );

    function sf_bwm_filter_script( $excluded ) {

        global $is_IE;

        $excluded = array();

        if ( $is_IE ) {
            $excluded = array( 'jquery', 'sf-bootstrap-js', 'sf-functions' );
        }

        return $excluded;

    }

    add_filter( 'bwp_minify_script_ignore', 'sf_bwm_filter_script' );


    /* BETTER SEO PAGE TITLE
    ================================================== */
    if ( ! function_exists( 'sf_filter_wp_title' ) ) {
        function sf_filter_wp_title( $title ) {
            global $page, $paged;

            if ( is_feed() ) {
                return $title;
            }

            $site_description = get_bloginfo( 'description' );

            $filtered_title = $title . get_bloginfo( 'name' );
            $filtered_title .= ( ! empty( $site_description ) && ( is_home() || is_front_page() ) ) ? ' | ' . $site_description : '';
            $filtered_title .= ( 2 <= $paged || 2 <= $page ) ? ' | ' . sprintf( __( 'Page %s', 'swiftframework' ), max( $paged, $page ) ) : '';

            return $filtered_title;
        }

        add_filter( 'wp_title', 'sf_filter_wp_title' );
    }


    /* MAINTENANCE MODE
    ================================================== */
    if ( ! function_exists( 'sf_maintenance_mode' ) ) {
        function sf_maintenance_mode() {
            global $sf_options;
            $custom_logo        = array();
            $custom_logo_output = $maintenance_mode = "";
            if ( isset( $sf_options['custom_admin_login_logo'] ) ) {
                $custom_logo = $sf_options['custom_admin_login_logo'];
            }
            if ( isset( $custom_logo['url'] ) ) {
                $custom_logo_output = '<img src="' . $custom_logo['url'] . '" alt="maintenance" style="margin: 0 auto; display: block;" />';
            } else {
                $custom_logo_output = '<img src="' . get_template_directory_uri() . '/images/custom-login-logo.png" alt="maintenance" style="margin: 0 auto; display: block;" />';
            }

            if ( isset( $sf_options['enable_maintenance'] ) ) {
                $maintenance_mode = $sf_options['enable_maintenance'];
            } else {
                $maintenance_mode = false;
            }

            if ( $maintenance_mode == 2 ) {

                $holding_page     = __( $sf_options['maintenance_mode_page'], 'swiftframework' );
                $current_page_URL = sf_current_page_url();
                $holding_page_URL = get_permalink( $holding_page );

                if ( $current_page_URL != $holding_page_URL ) {
                    if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
                        wp_redirect( $holding_page_URL );
                        exit;
                    }
                }

            } else if ( $maintenance_mode == 1 ) {
                if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
                    wp_die( $custom_logo_output . '<p style="text-align:center">' . __( 'We are currently in maintenance mode, please check back shortly.', 'swiftframework' ) . '</p>', get_bloginfo( 'name' ) );
                }
            }
        }

        add_action( 'get_header', 'sf_maintenance_mode' );
    }


    /* CUSTOM LOGIN LOGO
    ================================================== */
    if ( ! function_exists( 'sf_custom_login_logo' ) ) {
        function sf_custom_login_logo() {
            global $sf_options;
            $custom_logo = "";
            if ( isset( $sf_options['custom_admin_login_logo']['url'] ) ) {
                $custom_logo = $sf_options['custom_admin_login_logo']['url'];
            }
            if ( $custom_logo ) {
                echo '<style type="text/css">
			    .login h1 a { background-image:url(' . $custom_logo . ') !important; height: 95px!important; width: 100%!important; background-size: auto!important; }
			</style>';
            } else {
                echo '<style type="text/css">
			    .login h1 a { background-image:url(' . get_template_directory_uri() . '/images/custom-login-logo.png) !important; height: 95px!important; width: 100%!important; background-size: auto!important; }
			</style>';
            }
        }

        add_action( 'login_head', 'sf_custom_login_logo' );
    }


    /* LANGUAGE FLAGS
    ================================================== */
    if ( ! function_exists( 'sf_language_flags' ) ) {
	    function sf_language_flags() {

	        $language_output = "";

	        if ( function_exists( 'pll_the_languages' ) ) {
	            $languages = pll_the_languages(array('raw' =>1 ));
	            if ( !empty( $languages ) ) {
	                foreach( $languages as $l ) {
	                    $language_output .= '<li>';
	                    if ( $l['flag'] ) {
	                        if ( !$l['current_lang'] ) {
	                        	$language_output .= '<a href="'.$l['url'].'"><img src="'.$l['flag'].'" height="12" alt="'.$l['slug'].'" width="18" /><span class="language name">'.$l['name'].'</span></a>'."\n";
	                        } else {
	                        	$language_output .= '<div class="current-language"><img src="'.$l['flag'].'" height="12" alt="'.$l['slug'].'" width="18" /><span class="language name">'.$l['name'].'</span></div>'."\n";
	                        }
	                    }
	                    $language_output .= '</li>';
	                 }
	            }
	        } else if ( function_exists( 'icl_get_languages' ) ) {
	            $languages = icl_get_languages( 'skip_missing=0&orderby=code' );
	            if ( ! empty( $languages ) ) {
	                foreach ( $languages as $l ) {
	                    $language_output .= '<li>';
	                    if ( $l['country_flag_url'] ) {
	                        if ( ! $l['active'] ) {
	                            $language_output .= '<a href="' . $l['url'] . '"><img src="' . $l['country_flag_url'] . '" height="12" alt="' . $l['language_code'] . '" width="18" /><span class="language name">' . $l['translated_name'] . '</span></a>' . "\n";
	                        } else {
	                            $language_output .= '<div class="current-language"><img src="' . $l['country_flag_url'] . '" height="12" alt="' . $l['language_code'] . '" width="18" /><span class="language name">' . $l['translated_name'] . '</span></div>' . "\n";
	                        }
	                    }
	                    $language_output .= '</li>';
	                }
	            }
	        } else {
	            //echo '<li><div>No languages set.</div></li>';
	            $flags_url = get_template_directory_uri() . '/images/flags';
	            $language_output .= '<li><a href="#">DEMO - EXAMPLE PURPOSES</a></li><li><a href="#"><span class="language name">German</span></a></li><li><div class="current-language"><span class="language name">English</span></div></li><li><a href="#"><span class="language name">Spanish</span></a></li><li><a href="#"><span class="language name">French</span></a></li>' . "\n";
	        }

	        return $language_output;
	    }
    }


    /* HEX TO RGB COLOR
    ================================================== */
    if ( ! function_exists( 'sf_hex2rgb' ) ) {
	    function sf_hex2rgb( $colour ) {
	        if ( $colour[0] == '#' ) {
	            $colour = substr( $colour, 1 );
	        }
	        if ( strlen( $colour ) == 6 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	        } elseif ( strlen( $colour ) == 3 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	        } else {
	            return false;
	        }
	        $r = hexdec( $r );
	        $g = hexdec( $g );
	        $b = hexdec( $b );

	        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	    }
    }


    /* GET COMMENTS COUNT TEXT
    ================================================== */
    function sf_get_comments_number( $post_id ) {
        $num_comments  = get_comments_number( $post_id ); // get_comments_number returns only a numeric value
        $comments_text = "";

        if ( $num_comments == 0 ) {
            $comments_text = __( '0 Comments', 'swiftframework' );
        } elseif ( $num_comments > 1 ) {
            $comments_text = $num_comments . __( ' Comments', 'swiftframework' );
        } else {
            $comments_text = __( '1 Comment', 'swiftframework' );
        }

        return $comments_text;
    }


    /* SET AUTHOR PAGE TO SHOW CAMPAIGNS
    ================================================== */
    function sf_post_author_archive( $query ) {
        if ( class_exists( 'ATCF_Campaigns' ) ) {
            if ( $query->is_author ) {
                $query->set( 'post_type', 'download' );
            }
        }
    }

    add_action( 'pre_get_posts', 'sf_post_author_archive' );


    /* GET USER MENU LIST
    ================================================== */
    function sf_get_menu_list() {

	    if ( !is_admin() ) {
			return;
		}

        $menu_list  = array( '' => '' );
        $user_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

        foreach ( $user_menus as $menu ) {
            $menu_list[ $menu->term_id ] = $menu->name;
        }

        return $menu_list;
    }


    /* GET CUSTOM POST TYPE TAXONOMY LIST
    ================================================== */
    if ( ! function_exists( 'sf_get_category_list' ) ) {
        function sf_get_category_list( $category_name, $filter = 0, $category_child = "", $frontend_display = false ) {

    		if ( !$frontend_display && !is_admin() ) {
    			return;
    		}

    		if ( $category_name == "product-category" ) {
    			$category_name = "product_cat";
    		}

            if ( ! $filter ) {

                $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->slug ) ) {
                        $category_list[] = $category->slug;
                    }
                }

                return $category_list;

            } else if ( $category_child != "" && $category_child != "All" ) {

                $childcategory = get_term_by( 'slug', $category_child, $category_name );
                $get_category  = get_categories( array(
                        'taxonomy' => $category_name,
                        'child_of' => $childcategory->term_id
                    ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->cat_name ) ) {
                        $category_list[] = $category->slug;
                    }
                }

                return $category_list;

            } else {

                $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
                $category_list = array( '0' => 'All' );

                foreach ( $get_category as $category ) {
                    if ( isset( $category->cat_name ) ) {
                        $category_list[] = $category->cat_name;
                    }
                }

                return $category_list;
            }
    	}
    }

    if ( ! function_exists( 'sf_get_category_list_key_array' ) ) {
        function sf_get_category_list_key_array( $category_name ) {

    	    if ( !is_admin() ) {
    			return;
    		}

            $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
            $category_list = array( 'all' => 'All' );

            foreach ( $get_category as $category ) {
                if ( isset( $category->slug ) ) {
                    $category_list[ $category->slug ] = $category->cat_name;
                }
            }

            return $category_list;
        }
    }
    
    if ( ! function_exists( 'sf_get_woo_product_filters_array' ) ) {
        function sf_get_woo_product_filters_array() {

    	    if ( !is_admin() ) {
    			return;
    		}

            global $woocommerce;

            $attribute_array = array();

            $transient_name = 'wc_attribute_taxonomies';

            if ( sf_woocommerce_activated() ) {

                if ( false === ( $attribute_taxonomies = get_transient( $transient_name ) ) ) {
                    global $wpdb;

                    $attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );
                    set_transient( $transient_name, $attribute_taxonomies );
                }

                $attribute_taxonomies = apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );

                $attribute_array['product_cat'] = __( 'Product Category', 'swiftframework' );
                $attribute_array['price']       = __( 'Price', 'swiftframework' );

                if ( $attribute_taxonomies ) {
                    foreach ( $attribute_taxonomies as $tax ) {
                        $attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
                    }
                }

            }

            return $attribute_array;
        }
    }

    if ( ! function_exists( 'sf_get_woo_product_parent_category_array' ) ) {
    	function sf_get_woo_product_parent_category_array() {

    		if ( !is_admin() ) {
    			return;
    		}

    		$get_category  = get_categories( array( 'taxonomy' => 'product_cat', 'parent' => 0,  'hide_empty' => false ) );

    		$category_list = array( 'All' => 'All' );

    		foreach ( $get_category as $category ) {
    			if ( isset( $category->slug ) ) {
    				$category_list[$category->term_id] = $category->slug;
    			}
    		}

            return $category_list;

        }
    }

    /* POST FILTER
    ================================================== */
    if ( ! function_exists( 'sf_post_filter' ) ) {
        function sf_post_filter( $style = "basic", $post_type = "post", $parent_category = "" ) {

            $filter_output = $tax_terms = "";

			$taxonomy_name = 'category';

			if ( $post_type != "post") {
				$taxonomy_name = $post_type . '-category';
			}

            if ( $parent_category == "" || $parent_category == "All" ) {
                $tax_terms = sf_get_category_list( $taxonomy_name, 1, '', true );
            } else {
                $tax_terms = sf_get_category_list( $taxonomy_name, 1, $parent_category, true );
            }

            $filter_output .= '<div class="filter-wrap clearfix">' . "\n";
            $filter_output .= '<ul class="post-filter-tabs filtering clearfix">' . "\n";
            $filter_output .= '<li class="all selected"><a data-filter="*" href="#"><span class="item-name">' . __( "Show all", "swiftframework" ) . '</span></a></li>' . "\n";
            foreach ( $tax_terms as $tax_term ) {
                $term = get_term_by( 'slug', $tax_term, $taxonomy_name );
                if ( $term ) {
                	$slug = strtolower($term->slug);
                    $filter_output .= '<li><a href="#" title="' . $term->name . '" class="' . $slug . '" data-filter=".' . $slug . '"><span class="item-name">' . $term->name . '</span></a></li>' . "\n";
                } else {
                    $filter_output .= '<li><a href="#" title="' . $tax_term . '" class="' . $tax_term . '" data-filter=".' . $tax_term . '"><span class="item-name">' . $tax_term . '</span></a></li>' . "\n";
                }
            }
            $filter_output .= '</ul></div>' . "\n";

            return $filter_output;
        }
    }


    /* CATEGORY REL FIX
    ================================================== */
    function sf_add_nofollow_cat( $text ) {
        $text = str_replace( 'rel="category tag"', "", $text );

        return $text;
    }

    add_filter( 'the_category', 'sf_add_nofollow_cat' );


    /* GET CURRENT PAGE URL
    ================================================== */
    function sf_current_page_url() {
        $pageURL = 'http';
        $defaultPort = "80";
        if ( isset( $_SERVER["HTTPS"] ) ) {
            if ( $_SERVER["HTTPS"] == "on" ) {
                $pageURL .= "s";
                $defaultPort = "443";
            }
        }
        $pageURL .= "://";
        if ( $_SERVER["SERVER_PORT"] != $defaultPort ) {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }


    /* CHECK WOOCOMMERCE IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_woocommerce_activated' ) ) {
        function sf_woocommerce_activated() {
            if ( class_exists( 'woocommerce' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK WPML IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_wpml_activated' ) ) {
    	function sf_wpml_activated() {
    		if ( function_exists('icl_object_id') ) {
    			return true;
    		} else {
    			return false;
    		}
    	}
    }


    /* CHECK GRAVITY FORMS IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_gravityforms_activated' ) ) {
        function sf_gravityforms_activated() {
            if ( class_exists( 'GFForms' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK NINJA FORMS IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_ninjaforms_activated' ) ) {
        function sf_ninjaforms_activated() {
            if ( function_exists( 'ninja_forms_shortcode' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* CHECK GP PRICING IS ACTIVE
    ================================================== */
    if ( ! function_exists( 'sf_gopricing_activated' ) ) {
        function sf_gopricing_activated() {
            if ( class_exists( 'GW_GoPricing' ) ) {
                return true;
            } else {
                return false;
            }
        }
    }


    /* GET GRAVITY FORMS LIST
    ================================================== */
    if ( ! function_exists( 'sf_gravityforms_list' ) ) {
        function sf_gravityforms_list() {

	        if ( !is_admin() ) {
		        return;
	        }

            $forms       = RGFormsModel::get_forms( null, 'title' );
            $forms_array = array();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ):
                    $forms_array[ $form->id ] = $form->title;
                endforeach;
            }

            return $forms_array;
        }
    }


    /* GET NINJA FORMS LIST
    ================================================== */
    if ( ! function_exists( 'sf_ninjaforms_list' ) ) {
        function sf_ninjaforms_list() {

	        if ( !is_admin() ) {
		        return;
	        }

            $forms       = ninja_forms_get_all_forms();
            $forms_array = array();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ):
                    $forms_array[ $form['id'] ] = $form['data']['form_title'];
                endforeach;
            }

            return $forms_array;
        }
    }


    /* GET GO PRICING TABLES LIST
    ================================================== */
    if ( ! function_exists( 'sf_gopricing_list' ) ) {
        function sf_gopricing_list() {

	        if ( !is_admin() || !defined( 'GW_GO_PREFIX') ) {
		        return;
	        }

            $pricing_tables = get_option( GW_GO_PREFIX . 'tables' );
            $ptables_array  = array();

            if ( ! empty( $pricing_tables ) ) {
                foreach ( $pricing_tables as $pricing_table ) {
                    $ptables_array[ $pricing_table['table-id'] ] = esc_attr( $pricing_table['table-name'] );
                }
            }

            return $ptables_array;
        }
    }

    /* UPLOAD ATTACHMENTS
    ================================================== */
    if ( ! function_exists( 'sf_insert_attachment' ) ) {
        function sf_insert_attachment( $file_handler, $post_id ) {
            // check to make sure its a successful upload
            if ( $_FILES[ $file_handler ]['error'] !== UPLOAD_ERR_OK ) {
                __return_false();
            }

            require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
            require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
            require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

            $attach_id = media_handle_upload( $file_handler, $post_id );

            return $attach_id;
        }
    }

    /* SPB TEMPLATE LIST FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_list_spb_sections' ) ) {
        function sf_list_spb_sections() {

	        if ( !is_admin() ) {
		        return;
	        }

            $spb_sections_list  = array();
            $spb_sections_query = new WP_Query( array( 'post_type' => 'spb-section', 'posts_per_page' => - 1 ) );
            while ( $spb_sections_query->have_posts() ) : $spb_sections_query->the_post();
                $spb_sections_list[ get_the_title() ] = get_the_ID();
            endwhile;
            wp_reset_query();

            if ( empty( $spb_sections_list ) ) {
                $spb_sections_list[] = "No SPB Templates found";
            }

            return $spb_sections_list;
        }
    }

    /* ICON LIST
    ================================================== */
    if ( ! function_exists( 'sf_get_icons_list' ) ) {
        function sf_get_icons_list( $type = "" ) {

            // VARIABLES
            $icon_list = $fa_list = $gizmo_list = "";

            // FONT AWESOME
            $fa_list = '<li><i class="fa-500px"></i><span class="icon-name">fa-500px</span></li><li><i class="fa-adjust"></i><span class="icon-name">fa-adjust</span></li><li><i class="fa-adn"></i><span class="icon-name">fa-adn</span></li><li><i class="fa-align-center"></i><span class="icon-name">fa-align-center</span></li><li><i class="fa-align-justify"></i><span class="icon-name">fa-align-justify</span></li><li><i class="fa-align-left"></i><span class="icon-name">fa-align-left</span></li><li><i class="fa-align-right"></i><span class="icon-name">fa-align-right</span></li><li><i class="fa-amazon"></i><span class="icon-name">fa-amazon</span></li><li><i class="fa-ambulance"></i><span class="icon-name">fa-ambulance</span></li><li><i class="fa-anchor"></i><span class="icon-name">fa-anchor</span></li><li><i class="fa-android"></i><span class="icon-name">fa-android</span></li><li><i class="fa-angellist"></i><span class="icon-name">fa-angellist</span></li><li><i class="fa-angle-double-down"></i><span class="icon-name">fa-angle-double-down</span></li><li><i class="fa-angle-double-left"></i><span class="icon-name">fa-angle-double-left</span></li><li><i class="fa-angle-double-right"></i><span class="icon-name">fa-angle-double-right</span></li><li><i class="fa-angle-double-up"></i><span class="icon-name">fa-angle-double-up</span></li><li><i class="fa-angle-down"></i><span class="icon-name">fa-angle-down</span></li><li><i class="fa-angle-left"></i><span class="icon-name">fa-angle-left</span></li><li><i class="fa-angle-right"></i><span class="icon-name">fa-angle-right</span></li><li><i class="fa-angle-up"></i><span class="icon-name">fa-angle-up</span></li><li><i class="fa-apple"></i><span class="icon-name">fa-apple</span></li><li><i class="fa-archive"></i><span class="icon-name">fa-archive</span></li><li><i class="fa-area-chart"></i><span class="icon-name">fa-area-chart</span></li><li><i class="fa-arrow-circle-down"></i><span class="icon-name">fa-arrow-circle-down</span></li><li><i class="fa-arrow-circle-left"></i><span class="icon-name">fa-arrow-circle-left</span></li><li><i class="fa-arrow-circle-o-down"></i><span class="icon-name">fa-arrow-circle-o-down</span></li><li><i class="fa-arrow-circle-o-left"></i><span class="icon-name">fa-arrow-circle-o-left</span></li><li><i class="fa-arrow-circle-o-right"></i><span class="icon-name">fa-arrow-circle-o-right</span></li><li><i class="fa-arrow-circle-o-up"></i><span class="icon-name">fa-arrow-circle-o-up</span></li><li><i class="fa-arrow-circle-right"></i><span class="icon-name">fa-arrow-circle-right</span></li><li><i class="fa-arrow-circle-up"></i><span class="icon-name">fa-arrow-circle-up</span></li><li><i class="fa-arrow-down"></i><span class="icon-name">fa-arrow-down</span></li><li><i class="fa-arrow-left"></i><span class="icon-name">fa-arrow-left</span></li><li><i class="fa-arrow-right"></i><span class="icon-name">fa-arrow-right</span></li><li><i class="fa-arrow-up"></i><span class="icon-name">fa-arrow-up</span></li><li><i class="fa-arrows"></i><span class="icon-name">fa-arrows</span></li><li><i class="fa-arrows-alt"></i><span class="icon-name">fa-arrows-alt</span></li><li><i class="fa-arrows-h"></i><span class="icon-name">fa-arrows-h</span></li><li><i class="fa-arrows-v"></i><span class="icon-name">fa-arrows-v</span></li><li><i class="fa-asterisk"></i><span class="icon-name">fa-asterisk</span></li><li><i class="fa-at"></i><span class="icon-name">fa-at</span></li><li><i class="fa-automobile"></i><span class="icon-name">fa-automobile</span></li><li><i class="fa-backward"></i><span class="icon-name">fa-backward</span></li><li><i class="fa-balance-scale"></i><span class="icon-name">fa-balance-scale</span></li><li><i class="fa-ban"></i><span class="icon-name">fa-ban</span></li><li><i class="fa-bank"></i><span class="icon-name">fa-bank</span></li><li><i class="fa-bar-chart"></i><span class="icon-name">fa-bar-chart</span></li><li><i class="fa-bar-chart-o"></i><span class="icon-name">fa-bar-chart-o</span></li><li><i class="fa-barcode"></i><span class="icon-name">fa-barcode</span></li><li><i class="fa-bars"></i><span class="icon-name">fa-bars</span></li><li><i class="fa-battery-0"></i><span class="icon-name">fa-battery-0</span></li><li><i class="fa-battery-1"></i><span class="icon-name">fa-battery-1</span></li><li><i class="fa-battery-2"></i><span class="icon-name">fa-battery-2</span></li><li><i class="fa-battery-3"></i><span class="icon-name">fa-battery-3</span></li><li><i class="fa-battery-4"></i><span class="icon-name">fa-battery-4</span></li><li><i class="fa-battery-empty"></i><span class="icon-name">fa-battery-empty</span></li><li><i class="fa-battery-full"></i><span class="icon-name">fa-battery-full</span></li><li><i class="fa-battery-half"></i><span class="icon-name">fa-battery-half</span></li><li><i class="fa-battery-quarter"></i><span class="icon-name">fa-battery-quarter</span></li><li><i class="fa-battery-three-quarters"></i><span class="icon-name">fa-battery-three-quarters</span></li><li><i class="fa-bed"></i><span class="icon-name">fa-bed</span></li><li><i class="fa-beer"></i><span class="icon-name">fa-beer</span></li><li><i class="fa-behance"></i><span class="icon-name">fa-behance</span></li><li><i class="fa-behance-square"></i><span class="icon-name">fa-behance-square</span></li><li><i class="fa-bell"></i><span class="icon-name">fa-bell</span></li><li><i class="fa-bell-o"></i><span class="icon-name">fa-bell-o</span></li><li><i class="fa-bell-slash"></i><span class="icon-name">fa-bell-slash</span></li><li><i class="fa-bell-slash-o"></i><span class="icon-name">fa-bell-slash-o</span></li><li><i class="fa-bicycle"></i><span class="icon-name">fa-bicycle</span></li><li><i class="fa-binoculars"></i><span class="icon-name">fa-binoculars</span></li><li><i class="fa-birthday-cake"></i><span class="icon-name">fa-birthday-cake</span></li><li><i class="fa-bitbucket"></i><span class="icon-name">fa-bitbucket</span></li><li><i class="fa-bitbucket-square"></i><span class="icon-name">fa-bitbucket-square</span></li><li><i class="fa-bitcoin"></i><span class="icon-name">fa-bitcoin</span></li><li><i class="fa-black-tie"></i><span class="icon-name">fa-black-tie</span></li><li><i class="fa-bold"></i><span class="icon-name">fa-bold</span></li><li><i class="fa-bolt"></i><span class="icon-name">fa-bolt</span></li><li><i class="fa-bomb"></i><span class="icon-name">fa-bomb</span></li><li><i class="fa-book"></i><span class="icon-name">fa-book</span></li><li><i class="fa-bookmark"></i><span class="icon-name">fa-bookmark</span></li><li><i class="fa-bookmark-o"></i><span class="icon-name">fa-bookmark-o</span></li><li><i class="fa-briefcase"></i><span class="icon-name">fa-briefcase</span></li><li><i class="fa-btc"></i><span class="icon-name">fa-btc</span></li><li><i class="fa-bug"></i><span class="icon-name">fa-bug</span></li><li><i class="fa-building"></i><span class="icon-name">fa-building</span></li><li><i class="fa-building-o"></i><span class="icon-name">fa-building-o</span></li><li><i class="fa-bullhorn"></i><span class="icon-name">fa-bullhorn</span></li><li><i class="fa-bullseye"></i><span class="icon-name">fa-bullseye</span></li><li><i class="fa-bus"></i><span class="icon-name">fa-bus</span></li><li><i class="fa-buysellads"></i><span class="icon-name">fa-buysellads</span></li><li><i class="fa-cab"></i><span class="icon-name">fa-cab</span></li><li><i class="fa-calculator"></i><span class="icon-name">fa-calculator</span></li><li><i class="fa-calendar"></i><span class="icon-name">fa-calendar</span></li><li><i class="fa-calendar-check-o"></i><span class="icon-name">fa-calendar-check-o</span></li><li><i class="fa-calendar-minus-o"></i><span class="icon-name">fa-calendar-minus-o</span></li><li><i class="fa-calendar-o"></i><span class="icon-name">fa-calendar-o</span></li><li><i class="fa-calendar-plus-o"></i><span class="icon-name">fa-calendar-plus-o</span></li><li><i class="fa-calendar-times-o"></i><span class="icon-name">fa-calendar-times-o</span></li><li><i class="fa-camera"></i><span class="icon-name">fa-camera</span></li><li><i class="fa-camera-retro"></i><span class="icon-name">fa-camera-retro</span></li><li><i class="fa-car"></i><span class="icon-name">fa-car</span></li><li><i class="fa-caret-down"></i><span class="icon-name">fa-caret-down</span></li><li><i class="fa-caret-left"></i><span class="icon-name">fa-caret-left</span></li><li><i class="fa-caret-right"></i><span class="icon-name">fa-caret-right</span></li><li><i class="fa-caret-square-o-down"></i><span class="icon-name">fa-caret-square-o-down</span></li><li><i class="fa-caret-square-o-left"></i><span class="icon-name">fa-caret-square-o-left</span></li><li><i class="fa-caret-square-o-right"></i><span class="icon-name">fa-caret-square-o-right</span></li><li><i class="fa-caret-square-o-up"></i><span class="icon-name">fa-caret-square-o-up</span></li><li><i class="fa-caret-up"></i><span class="icon-name">fa-caret-up</span></li><li><i class="fa-cart-arrow-down"></i><span class="icon-name">fa-cart-arrow-down</span></li><li><i class="fa-cart-plus"></i><span class="icon-name">fa-cart-plus</span></li><li><i class="fa-cc"></i><span class="icon-name">fa-cc</span></li><li><i class="fa-cc-amex"></i><span class="icon-name">fa-cc-amex</span></li><li><i class="fa-cc-diners-club"></i><span class="icon-name">fa-cc-diners-club</span></li><li><i class="fa-cc-discover"></i><span class="icon-name">fa-cc-discover</span></li><li><i class="fa-cc-jcb"></i><span class="icon-name">fa-cc-jcb</span></li><li><i class="fa-cc-mastercard"></i><span class="icon-name">fa-cc-mastercard</span></li><li><i class="fa-cc-paypal"></i><span class="icon-name">fa-cc-paypal</span></li><li><i class="fa-cc-stripe"></i><span class="icon-name">fa-cc-stripe</span></li><li><i class="fa-cc-visa"></i><span class="icon-name">fa-cc-visa</span></li><li><i class="fa-certificate"></i><span class="icon-name">fa-certificate</span></li><li><i class="fa-chain"></i><span class="icon-name">fa-chain</span></li><li><i class="fa-chain-broken"></i><span class="icon-name">fa-chain-broken</span></li><li><i class="fa-check"></i><span class="icon-name">fa-check</span></li><li><i class="fa-check-circle"></i><span class="icon-name">fa-check-circle</span></li><li><i class="fa-check-circle-o"></i><span class="icon-name">fa-check-circle-o</span></li><li><i class="fa-check-square"></i><span class="icon-name">fa-check-square</span></li><li><i class="fa-check-square-o"></i><span class="icon-name">fa-check-square-o</span></li><li><i class="fa-chevron-circle-down"></i><span class="icon-name">fa-chevron-circle-down</span></li><li><i class="fa-chevron-circle-left"></i><span class="icon-name">fa-chevron-circle-left</span></li><li><i class="fa-chevron-circle-right"></i><span class="icon-name">fa-chevron-circle-right</span></li><li><i class="fa-chevron-circle-up"></i><span class="icon-name">fa-chevron-circle-up</span></li><li><i class="fa-chevron-down"></i><span class="icon-name">fa-chevron-down</span></li><li><i class="fa-chevron-left"></i><span class="icon-name">fa-chevron-left</span></li><li><i class="fa-chevron-right"></i><span class="icon-name">fa-chevron-right</span></li><li><i class="fa-chevron-up"></i><span class="icon-name">fa-chevron-up</span></li><li><i class="fa-child"></i><span class="icon-name">fa-child</span></li><li><i class="fa-chrome"></i><span class="icon-name">fa-chrome</span></li><li><i class="fa-circle"></i><span class="icon-name">fa-circle</span></li><li><i class="fa-circle-o"></i><span class="icon-name">fa-circle-o</span></li><li><i class="fa-circle-o-notch"></i><span class="icon-name">fa-circle-o-notch</span></li><li><i class="fa-circle-thin"></i><span class="icon-name">fa-circle-thin</span></li><li><i class="fa-clipboard"></i><span class="icon-name">fa-clipboard</span></li><li><i class="fa-clock-o"></i><span class="icon-name">fa-clock-o</span></li><li><i class="fa-clone"></i><span class="icon-name">fa-clone</span></li><li><i class="fa-close"></i><span class="icon-name">fa-close</span></li><li><i class="fa-cloud"></i><span class="icon-name">fa-cloud</span></li><li><i class="fa-cloud-download"></i><span class="icon-name">fa-cloud-download</span></li><li><i class="fa-cloud-upload"></i><span class="icon-name">fa-cloud-upload</span></li><li><i class="fa-cny"></i><span class="icon-name">fa-cny</span></li><li><i class="fa-code"></i><span class="icon-name">fa-code</span></li><li><i class="fa-code-fork"></i><span class="icon-name">fa-code-fork</span></li><li><i class="fa-codepen"></i><span class="icon-name">fa-codepen</span></li><li><i class="fa-coffee"></i><span class="icon-name">fa-coffee</span></li><li><i class="fa-cog"></i><span class="icon-name">fa-cog</span></li><li><i class="fa-cogs"></i><span class="icon-name">fa-cogs</span></li><li><i class="fa-columns"></i><span class="icon-name">fa-columns</span></li><li><i class="fa-comment"></i><span class="icon-name">fa-comment</span></li><li><i class="fa-comment-o"></i><span class="icon-name">fa-comment-o</span></li><li><i class="fa-commenting"></i><span class="icon-name">fa-commenting</span></li><li><i class="fa-commenting-o"></i><span class="icon-name">fa-commenting-o</span></li><li><i class="fa-comments"></i><span class="icon-name">fa-comments</span></li><li><i class="fa-comments-o"></i><span class="icon-name">fa-comments-o</span></li><li><i class="fa-compass"></i><span class="icon-name">fa-compass</span></li><li><i class="fa-compress"></i><span class="icon-name">fa-compress</span></li><li><i class="fa-connectdevelop"></i><span class="icon-name">fa-connectdevelop</span></li><li><i class="fa-contao"></i><span class="icon-name">fa-contao</span></li><li><i class="fa-copy"></i><span class="icon-name">fa-copy</span></li><li><i class="fa-copyright"></i><span class="icon-name">fa-copyright</span></li><li><i class="fa-creative-commons"></i><span class="icon-name">fa-creative-commons</span></li><li><i class="fa-credit-card"></i><span class="icon-name">fa-credit-card</span></li><li><i class="fa-crop"></i><span class="icon-name">fa-crop</span></li><li><i class="fa-crosshairs"></i><span class="icon-name">fa-crosshairs</span></li><li><i class="fa-css3"></i><span class="icon-name">fa-css3</span></li><li><i class="fa-cube"></i><span class="icon-name">fa-cube</span></li><li><i class="fa-cubes"></i><span class="icon-name">fa-cubes</span></li><li><i class="fa-cut"></i><span class="icon-name">fa-cut</span></li><li><i class="fa-cutlery"></i><span class="icon-name">fa-cutlery</span></li><li><i class="fa-dashboard"></i><span class="icon-name">fa-dashboard</span></li><li><i class="fa-dashcube"></i><span class="icon-name">fa-dashcube</span></li><li><i class="fa-database"></i><span class="icon-name">fa-database</span></li><li><i class="fa-dedent"></i><span class="icon-name">fa-dedent</span></li><li><i class="fa-delicious"></i><span class="icon-name">fa-delicious</span></li><li><i class="fa-desktop"></i><span class="icon-name">fa-desktop</span></li><li><i class="fa-deviantart"></i><span class="icon-name">fa-deviantart</span></li><li><i class="fa-diamond"></i><span class="icon-name">fa-diamond</span></li><li><i class="fa-digg"></i><span class="icon-name">fa-digg</span></li><li><i class="fa-dollar"></i><span class="icon-name">fa-dollar</span></li><li><i class="fa-dot-circle-o"></i><span class="icon-name">fa-dot-circle-o</span></li><li><i class="fa-download"></i><span class="icon-name">fa-download</span></li><li><i class="fa-dribbble"></i><span class="icon-name">fa-dribbble</span></li><li><i class="fa-dropbox"></i><span class="icon-name">fa-dropbox</span></li><li><i class="fa-drupal"></i><span class="icon-name">fa-drupal</span></li><li><i class="fa-edit"></i><span class="icon-name">fa-edit</span></li><li><i class="fa-eject"></i><span class="icon-name">fa-eject</span></li><li><i class="fa-ellipsis-h"></i><span class="icon-name">fa-ellipsis-h</span></li><li><i class="fa-ellipsis-v"></i><span class="icon-name">fa-ellipsis-v</span></li><li><i class="fa-empire"></i><span class="icon-name">fa-empire</span></li><li><i class="fa-envelope"></i><span class="icon-name">fa-envelope</span></li><li><i class="fa-envelope-o"></i><span class="icon-name">fa-envelope-o</span></li><li><i class="fa-envelope-square"></i><span class="icon-name">fa-envelope-square</span></li><li><i class="fa-eraser"></i><span class="icon-name">fa-eraser</span></li><li><i class="fa-eur"></i><span class="icon-name">fa-eur</span></li><li><i class="fa-euro"></i><span class="icon-name">fa-euro</span></li><li><i class="fa-exchange"></i><span class="icon-name">fa-exchange</span></li><li><i class="fa-exclamation"></i><span class="icon-name">fa-exclamation</span></li><li><i class="fa-exclamation-circle"></i><span class="icon-name">fa-exclamation-circle</span></li><li><i class="fa-exclamation-triangle"></i><span class="icon-name">fa-exclamation-triangle</span></li><li><i class="fa-expand"></i><span class="icon-name">fa-expand</span></li><li><i class="fa-expeditedssl"></i><span class="icon-name">fa-expeditedssl</span></li><li><i class="fa-external-link"></i><span class="icon-name">fa-external-link</span></li><li><i class="fa-external-link-square"></i><span class="icon-name">fa-external-link-square</span></li><li><i class="fa-eye"></i><span class="icon-name">fa-eye</span></li><li><i class="fa-eye-slash"></i><span class="icon-name">fa-eye-slash</span></li><li><i class="fa-eyedropper"></i><span class="icon-name">fa-eyedropper</span></li><li><i class="fa-facebook"></i><span class="icon-name">fa-facebook</span></li><li><i class="fa-facebook-f"></i><span class="icon-name">fa-facebook-f</span></li><li><i class="fa-facebook-official"></i><span class="icon-name">fa-facebook-official</span></li><li><i class="fa-facebook-square"></i><span class="icon-name">fa-facebook-square</span></li><li><i class="fa-fast-backward"></i><span class="icon-name">fa-fast-backward</span></li><li><i class="fa-fast-forward"></i><span class="icon-name">fa-fast-forward</span></li><li><i class="fa-fax"></i><span class="icon-name">fa-fax</span></li><li><i class="fa-feed"></i><span class="icon-name">fa-feed</span></li><li><i class="fa-female"></i><span class="icon-name">fa-female</span></li><li><i class="fa-fighter-jet"></i><span class="icon-name">fa-fighter-jet</span></li><li><i class="fa-file"></i><span class="icon-name">fa-file</span></li><li><i class="fa-file-archive-o"></i><span class="icon-name">fa-file-archive-o</span></li><li><i class="fa-file-audio-o"></i><span class="icon-name">fa-file-audio-o</span></li><li><i class="fa-file-code-o"></i><span class="icon-name">fa-file-code-o</span></li><li><i class="fa-file-excel-o"></i><span class="icon-name">fa-file-excel-o</span></li><li><i class="fa-file-image-o"></i><span class="icon-name">fa-file-image-o</span></li><li><i class="fa-file-movie-o"></i><span class="icon-name">fa-file-movie-o</span></li><li><i class="fa-file-o"></i><span class="icon-name">fa-file-o</span></li><li><i class="fa-file-pdf-o"></i><span class="icon-name">fa-file-pdf-o</span></li><li><i class="fa-file-photo-o"></i><span class="icon-name">fa-file-photo-o</span></li><li><i class="fa-file-picture-o"></i><span class="icon-name">fa-file-picture-o</span></li><li><i class="fa-file-powerpoint-o"></i><span class="icon-name">fa-file-powerpoint-o</span></li><li><i class="fa-file-sound-o"></i><span class="icon-name">fa-file-sound-o</span></li><li><i class="fa-file-text"></i><span class="icon-name">fa-file-text</span></li><li><i class="fa-file-text-o"></i><span class="icon-name">fa-file-text-o</span></li><li><i class="fa-file-video-o"></i><span class="icon-name">fa-file-video-o</span></li><li><i class="fa-file-word-o"></i><span class="icon-name">fa-file-word-o</span></li><li><i class="fa-file-zip-o"></i><span class="icon-name">fa-file-zip-o</span></li><li><i class="fa-files-o"></i><span class="icon-name">fa-files-o</span></li><li><i class="fa-film"></i><span class="icon-name">fa-film</span></li><li><i class="fa-filter"></i><span class="icon-name">fa-filter</span></li><li><i class="fa-fire"></i><span class="icon-name">fa-fire</span></li><li><i class="fa-fire-extinguisher"></i><span class="icon-name">fa-fire-extinguisher</span></li><li><i class="fa-firefox"></i><span class="icon-name">fa-firefox</span></li><li><i class="fa-flag"></i><span class="icon-name">fa-flag</span></li><li><i class="fa-flag-checkered"></i><span class="icon-name">fa-flag-checkered</span></li><li><i class="fa-flag-o"></i><span class="icon-name">fa-flag-o</span></li><li><i class="fa-flash"></i><span class="icon-name">fa-flash</span></li><li><i class="fa-flask"></i><span class="icon-name">fa-flask</span></li><li><i class="fa-flickr"></i><span class="icon-name">fa-flickr</span></li><li><i class="fa-floppy-o"></i><span class="icon-name">fa-floppy-o</span></li><li><i class="fa-folder"></i><span class="icon-name">fa-folder</span></li><li><i class="fa-folder-o"></i><span class="icon-name">fa-folder-o</span></li><li><i class="fa-folder-open"></i><span class="icon-name">fa-folder-open</span></li><li><i class="fa-folder-open-o"></i><span class="icon-name">fa-folder-open-o</span></li><li><i class="fa-font"></i><span class="icon-name">fa-font</span></li><li><i class="fa-fonticons"></i><span class="icon-name">fa-fonticons</span></li><li><i class="fa-forumbee"></i><span class="icon-name">fa-forumbee</span></li><li><i class="fa-forward"></i><span class="icon-name">fa-forward</span></li><li><i class="fa-foursquare"></i><span class="icon-name">fa-foursquare</span></li><li><i class="fa-frown-o"></i><span class="icon-name">fa-frown-o</span></li><li><i class="fa-futbol-o"></i><span class="icon-name">fa-futbol-o</span></li><li><i class="fa-gamepad"></i><span class="icon-name">fa-gamepad</span></li><li><i class="fa-gavel"></i><span class="icon-name">fa-gavel</span></li><li><i class="fa-gbp"></i><span class="icon-name">fa-gbp</span></li><li><i class="fa-ge"></i><span class="icon-name">fa-ge</span></li><li><i class="fa-gear"></i><span class="icon-name">fa-gear</span></li><li><i class="fa-gears"></i><span class="icon-name">fa-gears</span></li><li><i class="fa-genderless"></i><span class="icon-name">fa-genderless</span></li><li><i class="fa-get-pocket"></i><span class="icon-name">fa-get-pocket</span></li><li><i class="fa-gg"></i><span class="icon-name">fa-gg</span></li><li><i class="fa-gg-circle"></i><span class="icon-name">fa-gg-circle</span></li><li><i class="fa-gift"></i><span class="icon-name">fa-gift</span></li><li><i class="fa-git"></i><span class="icon-name">fa-git</span></li><li><i class="fa-git-square"></i><span class="icon-name">fa-git-square</span></li><li><i class="fa-github"></i><span class="icon-name">fa-github</span></li><li><i class="fa-github-alt"></i><span class="icon-name">fa-github-alt</span></li><li><i class="fa-github-square"></i><span class="icon-name">fa-github-square</span></li><li><i class="fa-gittip"></i><span class="icon-name">fa-gittip</span></li><li><i class="fa-glass"></i><span class="icon-name">fa-glass</span></li><li><i class="fa-globe"></i><span class="icon-name">fa-globe</span></li><li><i class="fa-google"></i><span class="icon-name">fa-google</span></li><li><i class="fa-google-plus"></i><span class="icon-name">fa-google-plus</span></li><li><i class="fa-google-plus-square"></i><span class="icon-name">fa-google-plus-square</span></li><li><i class="fa-google-wallet"></i><span class="icon-name">fa-google-wallet</span></li><li><i class="fa-graduation-cap"></i><span class="icon-name">fa-graduation-cap</span></li><li><i class="fa-gratipay"></i><span class="icon-name">fa-gratipay</span></li><li><i class="fa-group"></i><span class="icon-name">fa-group</span></li><li><i class="fa-h-square"></i><span class="icon-name">fa-h-square</span></li><li><i class="fa-hacker-news"></i><span class="icon-name">fa-hacker-news</span></li><li><i class="fa-hand-grab-o"></i><span class="icon-name">fa-hand-grab-o</span></li><li><i class="fa-hand-lizard-o"></i><span class="icon-name">fa-hand-lizard-o</span></li><li><i class="fa-hand-o-down"></i><span class="icon-name">fa-hand-o-down</span></li><li><i class="fa-hand-o-left"></i><span class="icon-name">fa-hand-o-left</span></li><li><i class="fa-hand-o-right"></i><span class="icon-name">fa-hand-o-right</span></li><li><i class="fa-hand-o-up"></i><span class="icon-name">fa-hand-o-up</span></li><li><i class="fa-hand-paper-o"></i><span class="icon-name">fa-hand-paper-o</span></li><li><i class="fa-hand-peace-o"></i><span class="icon-name">fa-hand-peace-o</span></li><li><i class="fa-hand-pointer-o"></i><span class="icon-name">fa-hand-pointer-o</span></li><li><i class="fa-hand-rock-o"></i><span class="icon-name">fa-hand-rock-o</span></li><li><i class="fa-hand-scissors-o"></i><span class="icon-name">fa-hand-scissors-o</span></li><li><i class="fa-hand-spock-o"></i><span class="icon-name">fa-hand-spock-o</span></li><li><i class="fa-hand-stop-o"></i><span class="icon-name">fa-hand-stop-o</span></li><li><i class="fa-hdd-o"></i><span class="icon-name">fa-hdd-o</span></li><li><i class="fa-header"></i><span class="icon-name">fa-header</span></li><li><i class="fa-headphones"></i><span class="icon-name">fa-headphones</span></li><li><i class="fa-heart"></i><span class="icon-name">fa-heart</span></li><li><i class="fa-heart-o"></i><span class="icon-name">fa-heart-o</span></li><li><i class="fa-heartbeat"></i><span class="icon-name">fa-heartbeat</span></li><li><i class="fa-history"></i><span class="icon-name">fa-history</span></li><li><i class="fa-home"></i><span class="icon-name">fa-home</span></li><li><i class="fa-hospital-o"></i><span class="icon-name">fa-hospital-o</span></li><li><i class="fa-hotel"></i><span class="icon-name">fa-hotel</span></li><li><i class="fa-hourglass"></i><span class="icon-name">fa-hourglass</span></li><li><i class="fa-hourglass-1"></i><span class="icon-name">fa-hourglass-1</span></li><li><i class="fa-hourglass-2"></i><span class="icon-name">fa-hourglass-2</span></li><li><i class="fa-hourglass-3"></i><span class="icon-name">fa-hourglass-3</span></li><li><i class="fa-hourglass-end"></i><span class="icon-name">fa-hourglass-end</span></li><li><i class="fa-hourglass-half"></i><span class="icon-name">fa-hourglass-half</span></li><li><i class="fa-hourglass-o"></i><span class="icon-name">fa-hourglass-o</span></li><li><i class="fa-hourglass-start"></i><span class="icon-name">fa-hourglass-start</span></li><li><i class="fa-houzz"></i><span class="icon-name">fa-houzz</span></li><li><i class="fa-html5"></i><span class="icon-name">fa-html5</span></li><li><i class="fa-i-cursor"></i><span class="icon-name">fa-i-cursor</span></li><li><i class="fa-ils"></i><span class="icon-name">fa-ils</span></li><li><i class="fa-image"></i><span class="icon-name">fa-image</span></li><li><i class="fa-inbox"></i><span class="icon-name">fa-inbox</span></li><li><i class="fa-indent"></i><span class="icon-name">fa-indent</span></li><li><i class="fa-industry"></i><span class="icon-name">fa-industry</span></li><li><i class="fa-info"></i><span class="icon-name">fa-info</span></li><li><i class="fa-info-circle"></i><span class="icon-name">fa-info-circle</span></li><li><i class="fa-inr"></i><span class="icon-name">fa-inr</span></li><li><i class="fa-instagram"></i><span class="icon-name">fa-instagram</span></li><li><i class="fa-institution"></i><span class="icon-name">fa-institution</span></li><li><i class="fa-internet-explorer"></i><span class="icon-name">fa-internet-explorer</span></li><li><i class="fa-intersex"></i><span class="icon-name">fa-intersex</span></li><li><i class="fa-ioxhost"></i><span class="icon-name">fa-ioxhost</span></li><li><i class="fa-italic"></i><span class="icon-name">fa-italic</span></li><li><i class="fa-joomla"></i><span class="icon-name">fa-joomla</span></li><li><i class="fa-jpy"></i><span class="icon-name">fa-jpy</span></li><li><i class="fa-jsfiddle"></i><span class="icon-name">fa-jsfiddle</span></li><li><i class="fa-key"></i><span class="icon-name">fa-key</span></li><li><i class="fa-keyboard-o"></i><span class="icon-name">fa-keyboard-o</span></li><li><i class="fa-krw"></i><span class="icon-name">fa-krw</span></li><li><i class="fa-language"></i><span class="icon-name">fa-language</span></li><li><i class="fa-laptop"></i><span class="icon-name">fa-laptop</span></li><li><i class="fa-lastfm"></i><span class="icon-name">fa-lastfm</span></li><li><i class="fa-lastfm-square"></i><span class="icon-name">fa-lastfm-square</span></li><li><i class="fa-leaf"></i><span class="icon-name">fa-leaf</span></li><li><i class="fa-leanpub"></i><span class="icon-name">fa-leanpub</span></li><li><i class="fa-legal"></i><span class="icon-name">fa-legal</span></li><li><i class="fa-lemon-o"></i><span class="icon-name">fa-lemon-o</span></li><li><i class="fa-level-down"></i><span class="icon-name">fa-level-down</span></li><li><i class="fa-level-up"></i><span class="icon-name">fa-level-up</span></li><li><i class="fa-life-bouy"></i><span class="icon-name">fa-life-bouy</span></li><li><i class="fa-life-buoy"></i><span class="icon-name">fa-life-buoy</span></li><li><i class="fa-life-ring"></i><span class="icon-name">fa-life-ring</span></li><li><i class="fa-life-saver"></i><span class="icon-name">fa-life-saver</span></li><li><i class="fa-lightbulb-o"></i><span class="icon-name">fa-lightbulb-o</span></li><li><i class="fa-line-chart"></i><span class="icon-name">fa-line-chart</span></li><li><i class="fa-link"></i><span class="icon-name">fa-link</span></li><li><i class="fa-linkedin"></i><span class="icon-name">fa-linkedin</span></li><li><i class="fa-linkedin-square"></i><span class="icon-name">fa-linkedin-square</span></li><li><i class="fa-linux"></i><span class="icon-name">fa-linux</span></li><li><i class="fa-list"></i><span class="icon-name">fa-list</span></li><li><i class="fa-list-alt"></i><span class="icon-name">fa-list-alt</span></li><li><i class="fa-list-ol"></i><span class="icon-name">fa-list-ol</span></li><li><i class="fa-list-ul"></i><span class="icon-name">fa-list-ul</span></li><li><i class="fa-location-arrow"></i><span class="icon-name">fa-location-arrow</span></li><li><i class="fa-lock"></i><span class="icon-name">fa-lock</span></li><li><i class="fa-long-arrow-down"></i><span class="icon-name">fa-long-arrow-down</span></li><li><i class="fa-long-arrow-left"></i><span class="icon-name">fa-long-arrow-left</span></li><li><i class="fa-long-arrow-right"></i><span class="icon-name">fa-long-arrow-right</span></li><li><i class="fa-long-arrow-up"></i><span class="icon-name">fa-long-arrow-up</span></li><li><i class="fa-magic"></i><span class="icon-name">fa-magic</span></li><li><i class="fa-magnet"></i><span class="icon-name">fa-magnet</span></li><li><i class="fa-mail-forward"></i><span class="icon-name">fa-mail-forward</span></li><li><i class="fa-mail-reply"></i><span class="icon-name">fa-mail-reply</span></li><li><i class="fa-mail-reply-all"></i><span class="icon-name">fa-mail-reply-all</span></li><li><i class="fa-male"></i><span class="icon-name">fa-male</span></li><li><i class="fa-map"></i><span class="icon-name">fa-map</span></li><li><i class="fa-map-marker"></i><span class="icon-name">fa-map-marker</span></li><li><i class="fa-map-o"></i><span class="icon-name">fa-map-o</span></li><li><i class="fa-map-pin"></i><span class="icon-name">fa-map-pin</span></li><li><i class="fa-map-signs"></i><span class="icon-name">fa-map-signs</span></li><li><i class="fa-mars"></i><span class="icon-name">fa-mars</span></li><li><i class="fa-mars-double"></i><span class="icon-name">fa-mars-double</span></li><li><i class="fa-mars-stroke"></i><span class="icon-name">fa-mars-stroke</span></li><li><i class="fa-mars-stroke-h"></i><span class="icon-name">fa-mars-stroke-h</span></li><li><i class="fa-mars-stroke-v"></i><span class="icon-name">fa-mars-stroke-v</span></li><li><i class="fa-maxcdn"></i><span class="icon-name">fa-maxcdn</span></li><li><i class="fa-meanpath"></i><span class="icon-name">fa-meanpath</span></li><li><i class="fa-medium"></i><span class="icon-name">fa-medium</span></li><li><i class="fa-medkit"></i><span class="icon-name">fa-medkit</span></li><li><i class="fa-meh-o"></i><span class="icon-name">fa-meh-o</span></li><li><i class="fa-mercury"></i><span class="icon-name">fa-mercury</span></li><li><i class="fa-microphone"></i><span class="icon-name">fa-microphone</span></li><li><i class="fa-microphone-slash"></i><span class="icon-name">fa-microphone-slash</span></li><li><i class="fa-minus"></i><span class="icon-name">fa-minus</span></li><li><i class="fa-minus-circle"></i><span class="icon-name">fa-minus-circle</span></li><li><i class="fa-minus-square"></i><span class="icon-name">fa-minus-square</span></li><li><i class="fa-minus-square-o"></i><span class="icon-name">fa-minus-square-o</span></li><li><i class="fa-mobile"></i><span class="icon-name">fa-mobile</span></li><li><i class="fa-mobile-phone"></i><span class="icon-name">fa-mobile-phone</span></li><li><i class="fa-money"></i><span class="icon-name">fa-money</span></li><li><i class="fa-moon-o"></i><span class="icon-name">fa-moon-o</span></li><li><i class="fa-mortar-board"></i><span class="icon-name">fa-mortar-board</span></li><li><i class="fa-motorcycle"></i><span class="icon-name">fa-motorcycle</span></li><li><i class="fa-mouse-pointer"></i><span class="icon-name">fa-mouse-pointer</span></li><li><i class="fa-music"></i><span class="icon-name">fa-music</span></li><li><i class="fa-navicon"></i><span class="icon-name">fa-navicon</span></li><li><i class="fa-neuter"></i><span class="icon-name">fa-neuter</span></li><li><i class="fa-newspaper-o"></i><span class="icon-name">fa-newspaper-o</span></li><li><i class="fa-object-group"></i><span class="icon-name">fa-object-group</span></li><li><i class="fa-object-ungroup"></i><span class="icon-name">fa-object-ungroup</span></li><li><i class="fa-odnoklassniki"></i><span class="icon-name">fa-odnoklassniki</span></li><li><i class="fa-odnoklassniki-square"></i><span class="icon-name">fa-odnoklassniki-square</span></li><li><i class="fa-opencart"></i><span class="icon-name">fa-opencart</span></li><li><i class="fa-openid"></i><span class="icon-name">fa-openid</span></li><li><i class="fa-opera"></i><span class="icon-name">fa-opera</span></li><li><i class="fa-optin-monster"></i><span class="icon-name">fa-optin-monster</span></li><li><i class="fa-outdent"></i><span class="icon-name">fa-outdent</span></li><li><i class="fa-pagelines"></i><span class="icon-name">fa-pagelines</span></li><li><i class="fa-paint-brush"></i><span class="icon-name">fa-paint-brush</span></li><li><i class="fa-paper-plane"></i><span class="icon-name">fa-paper-plane</span></li><li><i class="fa-paper-plane-o"></i><span class="icon-name">fa-paper-plane-o</span></li><li><i class="fa-paperclip"></i><span class="icon-name">fa-paperclip</span></li><li><i class="fa-paragraph"></i><span class="icon-name">fa-paragraph</span></li><li><i class="fa-paste"></i><span class="icon-name">fa-paste</span></li><li><i class="fa-pause"></i><span class="icon-name">fa-pause</span></li><li><i class="fa-paw"></i><span class="icon-name">fa-paw</span></li><li><i class="fa-paypal"></i><span class="icon-name">fa-paypal</span></li><li><i class="fa-pencil"></i><span class="icon-name">fa-pencil</span></li><li><i class="fa-pencil-square"></i><span class="icon-name">fa-pencil-square</span></li><li><i class="fa-pencil-square-o"></i><span class="icon-name">fa-pencil-square-o</span></li><li><i class="fa-phone"></i><span class="icon-name">fa-phone</span></li><li><i class="fa-phone-square"></i><span class="icon-name">fa-phone-square</span></li><li><i class="fa-photo"></i><span class="icon-name">fa-photo</span></li><li><i class="fa-picture-o"></i><span class="icon-name">fa-picture-o</span></li><li><i class="fa-pie-chart"></i><span class="icon-name">fa-pie-chart</span></li><li><i class="fa-pied-piper"></i><span class="icon-name">fa-pied-piper</span></li><li><i class="fa-pied-piper-alt"></i><span class="icon-name">fa-pied-piper-alt</span></li><li><i class="fa-pinterest"></i><span class="icon-name">fa-pinterest</span></li><li><i class="fa-pinterest-p"></i><span class="icon-name">fa-pinterest-p</span></li><li><i class="fa-pinterest-square"></i><span class="icon-name">fa-pinterest-square</span></li><li><i class="fa-plane"></i><span class="icon-name">fa-plane</span></li><li><i class="fa-play"></i><span class="icon-name">fa-play</span></li><li><i class="fa-play-circle"></i><span class="icon-name">fa-play-circle</span></li><li><i class="fa-play-circle-o"></i><span class="icon-name">fa-play-circle-o</span></li><li><i class="fa-plug"></i><span class="icon-name">fa-plug</span></li><li><i class="fa-plus"></i><span class="icon-name">fa-plus</span></li><li><i class="fa-plus-circle"></i><span class="icon-name">fa-plus-circle</span></li><li><i class="fa-plus-square"></i><span class="icon-name">fa-plus-square</span></li><li><i class="fa-plus-square-o"></i><span class="icon-name">fa-plus-square-o</span></li><li><i class="fa-power-off"></i><span class="icon-name">fa-power-off</span></li><li><i class="fa-print"></i><span class="icon-name">fa-print</span></li><li><i class="fa-puzzle-piece"></i><span class="icon-name">fa-puzzle-piece</span></li><li><i class="fa-qq"></i><span class="icon-name">fa-qq</span></li><li><i class="fa-qrcode"></i><span class="icon-name">fa-qrcode</span></li><li><i class="fa-question"></i><span class="icon-name">fa-question</span></li><li><i class="fa-question-circle"></i><span class="icon-name">fa-question-circle</span></li><li><i class="fa-quote-left"></i><span class="icon-name">fa-quote-left</span></li><li><i class="fa-quote-right"></i><span class="icon-name">fa-quote-right</span></li><li><i class="fa-ra"></i><span class="icon-name">fa-ra</span></li><li><i class="fa-random"></i><span class="icon-name">fa-random</span></li><li><i class="fa-rebel"></i><span class="icon-name">fa-rebel</span></li><li><i class="fa-recycle"></i><span class="icon-name">fa-recycle</span></li><li><i class="fa-reddit"></i><span class="icon-name">fa-reddit</span></li><li><i class="fa-reddit-square"></i><span class="icon-name">fa-reddit-square</span></li><li><i class="fa-refresh"></i><span class="icon-name">fa-refresh</span></li><li><i class="fa-registered"></i><span class="icon-name">fa-registered</span></li><li><i class="fa-remove"></i><span class="icon-name">fa-remove</span></li><li><i class="fa-renren"></i><span class="icon-name">fa-renren</span></li><li><i class="fa-reorder"></i><span class="icon-name">fa-reorder</span></li><li><i class="fa-repeat"></i><span class="icon-name">fa-repeat</span></li><li><i class="fa-reply"></i><span class="icon-name">fa-reply</span></li><li><i class="fa-reply-all"></i><span class="icon-name">fa-reply-all</span></li><li><i class="fa-retweet"></i><span class="icon-name">fa-retweet</span></li><li><i class="fa-rmb"></i><span class="icon-name">fa-rmb</span></li><li><i class="fa-road"></i><span class="icon-name">fa-road</span></li><li><i class="fa-rocket"></i><span class="icon-name">fa-rocket</span></li><li><i class="fa-rotate-left"></i><span class="icon-name">fa-rotate-left</span></li><li><i class="fa-rotate-right"></i><span class="icon-name">fa-rotate-right</span></li><li><i class="fa-rouble"></i><span class="icon-name">fa-rouble</span></li><li><i class="fa-rss"></i><span class="icon-name">fa-rss</span></li><li><i class="fa-rss-square"></i><span class="icon-name">fa-rss-square</span></li><li><i class="fa-rub"></i><span class="icon-name">fa-rub</span></li><li><i class="fa-ruble"></i><span class="icon-name">fa-ruble</span></li><li><i class="fa-rupee"></i><span class="icon-name">fa-rupee</span></li><li><i class="fa-safari"></i><span class="icon-name">fa-safari</span></li><li><i class="fa-save"></i><span class="icon-name">fa-save</span></li><li><i class="fa-scissors"></i><span class="icon-name">fa-scissors</span></li><li><i class="fa-search"></i><span class="icon-name">fa-search</span></li><li><i class="fa-search-minus"></i><span class="icon-name">fa-search-minus</span></li><li><i class="fa-search-plus"></i><span class="icon-name">fa-search-plus</span></li><li><i class="fa-sellsy"></i><span class="icon-name">fa-sellsy</span></li><li><i class="fa-send"></i><span class="icon-name">fa-send</span></li><li><i class="fa-send-o"></i><span class="icon-name">fa-send-o</span></li><li><i class="fa-server"></i><span class="icon-name">fa-server</span></li><li><i class="fa-share"></i><span class="icon-name">fa-share</span></li><li><i class="fa-share-alt"></i><span class="icon-name">fa-share-alt</span></li><li><i class="fa-share-alt-square"></i><span class="icon-name">fa-share-alt-square</span></li><li><i class="fa-share-square"></i><span class="icon-name">fa-share-square</span></li><li><i class="fa-share-square-o"></i><span class="icon-name">fa-share-square-o</span></li><li><i class="fa-shekel"></i><span class="icon-name">fa-shekel</span></li><li><i class="fa-sheqel"></i><span class="icon-name">fa-sheqel</span></li><li><i class="fa-shield"></i><span class="icon-name">fa-shield</span></li><li><i class="fa-ship"></i><span class="icon-name">fa-ship</span></li><li><i class="fa-shirtsinbulk"></i><span class="icon-name">fa-shirtsinbulk</span></li><li><i class="fa-shopping-cart"></i><span class="icon-name">fa-shopping-cart</span></li><li><i class="fa-sign-in"></i><span class="icon-name">fa-sign-in</span></li><li><i class="fa-sign-out"></i><span class="icon-name">fa-sign-out</span></li><li><i class="fa-signal"></i><span class="icon-name">fa-signal</span></li><li><i class="fa-simplybuilt"></i><span class="icon-name">fa-simplybuilt</span></li><li><i class="fa-sitemap"></i><span class="icon-name">fa-sitemap</span></li><li><i class="fa-skyatlas"></i><span class="icon-name">fa-skyatlas</span></li><li><i class="fa-skype"></i><span class="icon-name">fa-skype</span></li><li><i class="fa-slack"></i><span class="icon-name">fa-slack</span></li><li><i class="fa-sliders"></i><span class="icon-name">fa-sliders</span></li><li><i class="fa-slideshare"></i><span class="icon-name">fa-slideshare</span></li><li><i class="fa-smile-o"></i><span class="icon-name">fa-smile-o</span></li><li><i class="fa-soccer-ball-o"></i><span class="icon-name">fa-soccer-ball-o</span></li><li><i class="fa-sort"></i><span class="icon-name">fa-sort</span></li><li><i class="fa-sort-alpha-asc"></i><span class="icon-name">fa-sort-alpha-asc</span></li><li><i class="fa-sort-alpha-desc"></i><span class="icon-name">fa-sort-alpha-desc</span></li><li><i class="fa-sort-amount-asc"></i><span class="icon-name">fa-sort-amount-asc</span></li><li><i class="fa-sort-amount-desc"></i><span class="icon-name">fa-sort-amount-desc</span></li><li><i class="fa-sort-asc"></i><span class="icon-name">fa-sort-asc</span></li><li><i class="fa-sort-desc"></i><span class="icon-name">fa-sort-desc</span></li><li><i class="fa-sort-down"></i><span class="icon-name">fa-sort-down</span></li><li><i class="fa-sort-numeric-asc"></i><span class="icon-name">fa-sort-numeric-asc</span></li><li><i class="fa-sort-numeric-desc"></i><span class="icon-name">fa-sort-numeric-desc</span></li><li><i class="fa-sort-up"></i><span class="icon-name">fa-sort-up</span></li><li><i class="fa-soundcloud"></i><span class="icon-name">fa-soundcloud</span></li><li><i class="fa-space-shuttle"></i><span class="icon-name">fa-space-shuttle</span></li><li><i class="fa-spinner"></i><span class="icon-name">fa-spinner</span></li><li><i class="fa-spoon"></i><span class="icon-name">fa-spoon</span></li><li><i class="fa-spotify"></i><span class="icon-name">fa-spotify</span></li><li><i class="fa-square"></i><span class="icon-name">fa-square</span></li><li><i class="fa-square-o"></i><span class="icon-name">fa-square-o</span></li><li><i class="fa-stack-exchange"></i><span class="icon-name">fa-stack-exchange</span></li><li><i class="fa-stack-overflow"></i><span class="icon-name">fa-stack-overflow</span></li><li><i class="fa-star"></i><span class="icon-name">fa-star</span></li><li><i class="fa-star-half"></i><span class="icon-name">fa-star-half</span></li><li><i class="fa-star-half-empty"></i><span class="icon-name">fa-star-half-empty</span></li><li><i class="fa-star-half-full"></i><span class="icon-name">fa-star-half-full</span></li><li><i class="fa-star-half-o"></i><span class="icon-name">fa-star-half-o</span></li><li><i class="fa-star-o"></i><span class="icon-name">fa-star-o</span></li><li><i class="fa-steam"></i><span class="icon-name">fa-steam</span></li><li><i class="fa-steam-square"></i><span class="icon-name">fa-steam-square</span></li><li><i class="fa-step-backward"></i><span class="icon-name">fa-step-backward</span></li><li><i class="fa-step-forward"></i><span class="icon-name">fa-step-forward</span></li><li><i class="fa-stethoscope"></i><span class="icon-name">fa-stethoscope</span></li><li><i class="fa-sticky-note"></i><span class="icon-name">fa-sticky-note</span></li><li><i class="fa-sticky-note-o"></i><span class="icon-name">fa-sticky-note-o</span></li><li><i class="fa-stop"></i><span class="icon-name">fa-stop</span></li><li><i class="fa-street-view"></i><span class="icon-name">fa-street-view</span></li><li><i class="fa-strikethrough"></i><span class="icon-name">fa-strikethrough</span></li><li><i class="fa-stumbleupon"></i><span class="icon-name">fa-stumbleupon</span></li><li><i class="fa-stumbleupon-circle"></i><span class="icon-name">fa-stumbleupon-circle</span></li><li><i class="fa-subscript"></i><span class="icon-name">fa-subscript</span></li><li><i class="fa-subway"></i><span class="icon-name">fa-subway</span></li><li><i class="fa-suitcase"></i><span class="icon-name">fa-suitcase</span></li><li><i class="fa-sun-o"></i><span class="icon-name">fa-sun-o</span></li><li><i class="fa-superscript"></i><span class="icon-name">fa-superscript</span></li><li><i class="fa-support"></i><span class="icon-name">fa-support</span></li><li><i class="fa-table"></i><span class="icon-name">fa-table</span></li><li><i class="fa-tablet"></i><span class="icon-name">fa-tablet</span></li><li><i class="fa-tachometer"></i><span class="icon-name">fa-tachometer</span></li><li><i class="fa-tag"></i><span class="icon-name">fa-tag</span></li><li><i class="fa-tags"></i><span class="icon-name">fa-tags</span></li><li><i class="fa-tasks"></i><span class="icon-name">fa-tasks</span></li><li><i class="fa-taxi"></i><span class="icon-name">fa-taxi</span></li><li><i class="fa-television"></i><span class="icon-name">fa-television</span></li><li><i class="fa-tencent-weibo"></i><span class="icon-name">fa-tencent-weibo</span></li><li><i class="fa-terminal"></i><span class="icon-name">fa-terminal</span></li><li><i class="fa-text-height"></i><span class="icon-name">fa-text-height</span></li><li><i class="fa-text-width"></i><span class="icon-name">fa-text-width</span></li><li><i class="fa-th"></i><span class="icon-name">fa-th</span></li><li><i class="fa-th-large"></i><span class="icon-name">fa-th-large</span></li><li><i class="fa-th-list"></i><span class="icon-name">fa-th-list</span></li><li><i class="fa-thumb-tack"></i><span class="icon-name">fa-thumb-tack</span></li><li><i class="fa-thumbs-down"></i><span class="icon-name">fa-thumbs-down</span></li><li><i class="fa-thumbs-o-down"></i><span class="icon-name">fa-thumbs-o-down</span></li><li><i class="fa-thumbs-o-up"></i><span class="icon-name">fa-thumbs-o-up</span></li><li><i class="fa-thumbs-up"></i><span class="icon-name">fa-thumbs-up</span></li><li><i class="fa-ticket"></i><span class="icon-name">fa-ticket</span></li><li><i class="fa-times"></i><span class="icon-name">fa-times</span></li><li><i class="fa-times-circle"></i><span class="icon-name">fa-times-circle</span></li><li><i class="fa-times-circle-o"></i><span class="icon-name">fa-times-circle-o</span></li><li><i class="fa-tint"></i><span class="icon-name">fa-tint</span></li><li><i class="fa-toggle-down"></i><span class="icon-name">fa-toggle-down</span></li><li><i class="fa-toggle-left"></i><span class="icon-name">fa-toggle-left</span></li><li><i class="fa-toggle-off"></i><span class="icon-name">fa-toggle-off</span></li><li><i class="fa-toggle-on"></i><span class="icon-name">fa-toggle-on</span></li><li><i class="fa-toggle-right"></i><span class="icon-name">fa-toggle-right</span></li><li><i class="fa-toggle-up"></i><span class="icon-name">fa-toggle-up</span></li><li><i class="fa-trademark"></i><span class="icon-name">fa-trademark</span></li><li><i class="fa-train"></i><span class="icon-name">fa-train</span></li><li><i class="fa-transgender"></i><span class="icon-name">fa-transgender</span></li><li><i class="fa-transgender-alt"></i><span class="icon-name">fa-transgender-alt</span></li><li><i class="fa-trash"></i><span class="icon-name">fa-trash</span></li><li><i class="fa-trash-o"></i><span class="icon-name">fa-trash-o</span></li><li><i class="fa-tree"></i><span class="icon-name">fa-tree</span></li><li><i class="fa-trello"></i><span class="icon-name">fa-trello</span></li><li><i class="fa-tripadvisor"></i><span class="icon-name">fa-tripadvisor</span></li><li><i class="fa-trophy"></i><span class="icon-name">fa-trophy</span></li><li><i class="fa-truck"></i><span class="icon-name">fa-truck</span></li><li><i class="fa-try"></i><span class="icon-name">fa-try</span></li><li><i class="fa-tty"></i><span class="icon-name">fa-tty</span></li><li><i class="fa-tumblr"></i><span class="icon-name">fa-tumblr</span></li><li><i class="fa-tumblr-square"></i><span class="icon-name">fa-tumblr-square</span></li><li><i class="fa-turkish-lira"></i><span class="icon-name">fa-turkish-lira</span></li><li><i class="fa-tv"></i><span class="icon-name">fa-tv</span></li><li><i class="fa-twitch"></i><span class="icon-name">fa-twitch</span></li><li><i class="fa-twitter"></i><span class="icon-name">fa-twitter</span></li><li><i class="fa-twitter-square"></i><span class="icon-name">fa-twitter-square</span></li><li><i class="fa-umbrella"></i><span class="icon-name">fa-umbrella</span></li><li><i class="fa-underline"></i><span class="icon-name">fa-underline</span></li><li><i class="fa-undo"></i><span class="icon-name">fa-undo</span></li><li><i class="fa-university"></i><span class="icon-name">fa-university</span></li><li><i class="fa-unlink"></i><span class="icon-name">fa-unlink</span></li><li><i class="fa-unlock"></i><span class="icon-name">fa-unlock</span></li><li><i class="fa-unlock-alt"></i><span class="icon-name">fa-unlock-alt</span></li><li><i class="fa-unsorted"></i><span class="icon-name">fa-unsorted</span></li><li><i class="fa-upload"></i><span class="icon-name">fa-upload</span></li><li><i class="fa-usd"></i><span class="icon-name">fa-usd</span></li><li><i class="fa-user"></i><span class="icon-name">fa-user</span></li><li><i class="fa-user-md"></i><span class="icon-name">fa-user-md</span></li><li><i class="fa-user-plus"></i><span class="icon-name">fa-user-plus</span></li><li><i class="fa-user-secret"></i><span class="icon-name">fa-user-secret</span></li><li><i class="fa-user-times"></i><span class="icon-name">fa-user-times</span></li><li><i class="fa-users"></i><span class="icon-name">fa-users</span></li><li><i class="fa-venus"></i><span class="icon-name">fa-venus</span></li><li><i class="fa-venus-double"></i><span class="icon-name">fa-venus-double</span></li><li><i class="fa-venus-mars"></i><span class="icon-name">fa-venus-mars</span></li><li><i class="fa-viacoin"></i><span class="icon-name">fa-viacoin</span></li><li><i class="fa-video-camera"></i><span class="icon-name">fa-video-camera</span></li><li><i class="fa-vimeo"></i><span class="icon-name">fa-vimeo</span></li><li><i class="fa-vimeo-square"></i><span class="icon-name">fa-vimeo-square</span></li><li><i class="fa-vine"></i><span class="icon-name">fa-vine</span></li><li><i class="fa-vk"></i><span class="icon-name">fa-vk</span></li><li><i class="fa-volume-down"></i><span class="icon-name">fa-volume-down</span></li><li><i class="fa-volume-off"></i><span class="icon-name">fa-volume-off</span></li><li><i class="fa-volume-up"></i><span class="icon-name">fa-volume-up</span></li><li><i class="fa-warning"></i><span class="icon-name">fa-warning</span></li><li><i class="fa-wechat"></i><span class="icon-name">fa-wechat</span></li><li><i class="fa-weibo"></i><span class="icon-name">fa-weibo</span></li><li><i class="fa-weixin"></i><span class="icon-name">fa-weixin</span></li><li><i class="fa-whatsapp"></i><span class="icon-name">fa-whatsapp</span></li><li><i class="fa-wheelchair"></i><span class="icon-name">fa-wheelchair</span></li><li><i class="fa-wifi"></i><span class="icon-name">fa-wifi</span></li><li><i class="fa-wikipedia-w"></i><span class="icon-name">fa-wikipedia-w</span></li><li><i class="fa-windows"></i><span class="icon-name">fa-windows</span></li><li><i class="fa-won"></i><span class="icon-name">fa-won</span></li><li><i class="fa-wordpress"></i><span class="icon-name">fa-wordpress</span></li><li><i class="fa-wrench"></i><span class="icon-name">fa-wrench</span></li><li><i class="fa-xing"></i><span class="icon-name">fa-xing</span></li><li><i class="fa-xing-square"></i><span class="icon-name">fa-xing-square</span></li><li><i class="fa-y-combinator"></i><span class="icon-name">fa-y-combinator</span></li><li><i class="fa-y-combinator-square"></i><span class="icon-name">fa-y-combinator-square</span></li><li><i class="fa-yahoo"></i><span class="icon-name">fa-yahoo</span></li><li><i class="fa-yc"></i><span class="icon-name">fa-yc</span></li><li><i class="fa-yc-square"></i><span class="icon-name">fa-yc-square</span></li><li><i class="fa-yelp"></i><span class="icon-name">fa-yelp</span></li><li><i class="fa-yen"></i><span class="icon-name">fa-yen</span></li><li><i class="fa-youtube"></i><span class="icon-name">fa-youtube</span></li><li><i class="fa-youtube-play"></i><span class="icon-name">fa-youtube-play</span></li><li><i class="fa-youtube-square"></i><span class="icon-name">fa-youtube-square</span></li>';

            // GIZMO
            $gizmo_list = '<li><i class="ss-cursor"></i><span class="icon-name">ss-cursor</span></li><li><i class="ss-crosshair"></i><span class="icon-name">ss-crosshair</span></li><li><i class="ss-search"></i><span class="icon-name">ss-search</span></li><li><i class="ss-zoomin"></i><span class="icon-name">ss-zoomin</span></li><li><i class="ss-zoomout"></i><span class="icon-name">ss-zoomout</span></li><li><i class="ss-view"></i><span class="icon-name">ss-view</span></li><li><i class="ss-attach"></i><span class="icon-name">ss-attach</span></li><li><i class="ss-link"></i><span class="icon-name">ss-link</span></li><li><i class="ss-unlink"></i><span class="icon-name">ss-unlink</span></li><li><i class="ss-move"></i><span class="icon-name">ss-move</span></li><li><i class="ss-write"></i><span class="icon-name">ss-write</span></li><li><i class="ss-writingdisabled"></i><span class="icon-name">ss-writingdisabled</span></li><li><i class="ss-erase"></i><span class="icon-name">ss-erase</span></li><li><i class="ss-compose"></i><span class="icon-name">ss-compose</span></li><li><i class="ss-lock"></i><span class="icon-name">ss-lock</span></li><li><i class="ss-unlock"></i><span class="icon-name">ss-unlock</span></li><li><i class="ss-key"></i><span class="icon-name">ss-key</span></li><li><i class="ss-backspace"></i><span class="icon-name">ss-backspace</span></li><li><i class="ss-ban"></i><span class="icon-name">ss-ban</span></li><li><i class="ss-smoking"></i><span class="icon-name">ss-smoking</span></li><li><i class="ss-nosmoking"></i><span class="icon-name">ss-nosmoking</span></li><li><i class="ss-trash"></i><span class="icon-name">ss-trash</span></li><li><i class="ss-target"></i><span class="icon-name">ss-target</span></li><li><i class="ss-tag"></i><span class="icon-name">ss-tag</span></li><li><i class="ss-bookmark"></i><span class="icon-name">ss-bookmark</span></li><li><i class="ss-flag"></i><span class="icon-name">ss-flag</span></li><li><i class="ss-like"></i><span class="icon-name">ss-like</span></li><li><i class="ss-dislike"></i><span class="icon-name">ss-dislike</span></li><li><i class="ss-heart"></i><span class="icon-name">ss-heart</span></li><li><i class="ss-star"></i><span class="icon-name">ss-star</span></li><li><i class="ss-sample"></i><span class="icon-name">ss-sample</span></li><li><i class="ss-crop"></i><span class="icon-name">ss-crop</span></li><li><i class="ss-layers"></i><span class="icon-name">ss-layers</span></li><li><i class="ss-layergroup"></i><span class="icon-name">ss-layergroup</span></li><li><i class="ss-pen"></i><span class="icon-name">ss-pen</span></li><li><i class="ss-bezier"></i><span class="icon-name">ss-bezier</span></li><li><i class="ss-pixels"></i><span class="icon-name">ss-pixels</span></li><li><i class="ss-phone"></i><span class="icon-name">ss-phone</span></li><li><i class="ss-phonedisabled"></i><span class="icon-name">ss-phonedisabled</span></li><li><i class="ss-touchtonephone"></i><span class="icon-name">ss-touchtonephone</span></li><li><i class="ss-mail"></i><span class="icon-name">ss-mail</span></li><li><i class="ss-inbox"></i><span class="icon-name">ss-inbox</span></li><li><i class="ss-outbox"></i><span class="icon-name">ss-outbox</span></li><li><i class="ss-chat"></i><span class="icon-name">ss-chat</span></li><li><i class="ss-user"></i><span class="icon-name">ss-user</span></li><li><i class="ss-users"></i><span class="icon-name">ss-users</span></li><li><i class="ss-usergroup"></i><span class="icon-name">ss-usergroup</span></li><li><i class="ss-businessuser"></i><span class="icon-name">ss-businessuser</span></li><li><i class="ss-man"></i><span class="icon-name">ss-man</span></li><li><i class="ss-male"></i><span class="icon-name">ss-male</span></li><li><i class="ss-woman"></i><span class="icon-name">ss-woman</span></li><li><i class="ss-female"></i><span class="icon-name">ss-female</span></li><li><i class="ss-raisedhand"></i><span class="icon-name">ss-raisedhand</span></li><li><i class="ss-hand"></i><span class="icon-name">ss-hand</span></li><li><i class="ss-pointup"></i><span class="icon-name">ss-pointup</span></li><li><i class="ss-pointupright"></i><span class="icon-name">ss-pointupright</span></li><li><i class="ss-pointright"></i><span class="icon-name">ss-pointright</span></li><li><i class="ss-pointdownright"></i><span class="icon-name">ss-pointdownright</span></li><li><i class="ss-pointdown"></i><span class="icon-name">ss-pointdown</span></li><li><i class="ss-pointdownleft"></i><span class="icon-name">ss-pointdownleft</span></li><li><i class="ss-pointleft"></i><span class="icon-name">ss-pointleft</span></li><li><i class="ss-pointupleft"></i><span class="icon-name">ss-pointupleft</span></li><li><i class="ss-cart"></i><span class="icon-name">ss-cart</span></li><li><i class="ss-creditcard"></i><span class="icon-name">ss-creditcard</span></li><li><i class="ss-calculator"></i><span class="icon-name">ss-calculator</span></li><li><i class="ss-barchart"></i><span class="icon-name">ss-barchart</span></li><li><i class="ss-piechart"></i><span class="icon-name">ss-piechart</span></li><li><i class="ss-box"></i><span class="icon-name">ss-box</span></li><li><i class="ss-home"></i><span class="icon-name">ss-home</span></li><li><i class="ss-globe"></i><span class="icon-name">ss-globe</span></li><li><i class="ss-navigate"></i><span class="icon-name">ss-navigate</span></li><li><i class="ss-compass"></i><span class="icon-name">ss-compass</span></li><li><i class="ss-signpost"></i><span class="icon-name">ss-signpost</span></li><li><i class="ss-location"></i><span class="icon-name">ss-location</span></li><li><i class="ss-floppydisk"></i><span class="icon-name">ss-floppydisk</span></li><li><i class="ss-database"></i><span class="icon-name">ss-database</span></li><li><i class="ss-hdd"></i><span class="icon-name">ss-hdd</span></li><li><i class="ss-microchip"></i><span class="icon-name">ss-microchip</span></li><li><i class="ss-music"></i><span class="icon-name">ss-music</span></li><li><i class="ss-headphones"></i><span class="icon-name">ss-headphones</span></li><li><i class="ss-discdrive"></i><span class="icon-name">ss-discdrive</span></li><li><i class="ss-volume"></i><span class="icon-name">ss-volume</span></li><li><i class="ss-lowvolume"></i><span class="icon-name">ss-lowvolume</span></li><li><i class="ss-mediumvolume"></i><span class="icon-name">ss-mediumvolume</span></li><li><i class="ss-highvolume"></i><span class="icon-name">ss-highvolume</span></li><li><i class="ss-airplay"></i><span class="icon-name">ss-airplay</span></li><li><i class="ss-camera"></i><span class="icon-name">ss-camera</span></li><li><i class="ss-picture"></i><span class="icon-name">ss-picture</span></li><li><i class="ss-video"></i><span class="icon-name">ss-video</span></li><li><i class="ss-webcam"></i><span class="icon-name">ss-webcam</span></li><li><i class="ss-film"></i><span class="icon-name">ss-film</span></li><li><i class="ss-playvideo"></i><span class="icon-name">ss-playvideo</span></li><li><i class="ss-videogame"></i><span class="icon-name">ss-videogame</span></li><li><i class="ss-play"></i><span class="icon-name">ss-play</span></li><li><i class="ss-pause"></i><span class="icon-name">ss-pause</span></li><li><i class="ss-stop"></i><span class="icon-name">ss-stop</span></li><li><i class="ss-record"></i><span class="icon-name">ss-record</span></li><li><i class="ss-rewind"></i><span class="icon-name">ss-rewind</span></li><li><i class="ss-fastforward"></i><span class="icon-name">ss-fastforward</span></li><li><i class="ss-skipback"></i><span class="icon-name">ss-skipback</span></li><li><i class="ss-skipforward"></i><span class="icon-name">ss-skipforward</span></li><li><i class="ss-eject"></i><span class="icon-name">ss-eject</span></li><li><i class="ss-repeat"></i><span class="icon-name">ss-repeat</span></li><li><i class="ss-replay"></i><span class="icon-name">ss-replay</span></li><li><i class="ss-shuffle"></i><span class="icon-name">ss-shuffle</span></li><li><i class="ss-index"></i><span class="icon-name">ss-index</span></li><li><i class="ss-storagebox"></i><span class="icon-name">ss-storagebox</span></li><li><i class="ss-book"></i><span class="icon-name">ss-book</span></li><li><i class="ss-notebook"></i><span class="icon-name">ss-notebook</span></li><li><i class="ss-newspaper"></i><span class="icon-name">ss-newspaper</span></li><li><i class="ss-gridlines"></i><span class="icon-name">ss-gridlines</span></li><li><i class="ss-rows"></i><span class="icon-name">ss-rows</span></li><li><i class="ss-columns"></i><span class="icon-name">ss-columns</span></li><li><i class="ss-thumbnails"></i><span class="icon-name">ss-thumbnails</span></li><li><i class="ss-mouse"></i><span class="icon-name">ss-mouse</span></li><li><i class="ss-usb"></i><span class="icon-name">ss-usb</span></li><li><i class="ss-desktop"></i><span class="icon-name">ss-desktop</span></li><li><i class="ss-laptop"></i><span class="icon-name">ss-laptop</span></li><li><i class="ss-tablet"></i><span class="icon-name">ss-tablet</span></li><li><i class="ss-smartphone"></i><span class="icon-name">ss-smartphone</span></li><li><i class="ss-cell"></i><span class="icon-name">ss-cell</span></li><li><i class="ss-battery"></i><span class="icon-name">ss-battery</span></li><li><i class="ss-highbattery"></i><span class="icon-name">ss-highbattery</span></li><li><i class="ss-mediumbattery"></i><span class="icon-name">ss-mediumbattery</span></li><li><i class="ss-lowbattery"></i><span class="icon-name">ss-lowbattery</span></li><li><i class="ss-chargingbattery"></i><span class="icon-name">ss-chargingbattery</span></li><li><i class="ss-lightbulb"></i><span class="icon-name">ss-lightbulb</span></li><li><i class="ss-washer"></i><span class="icon-name">ss-washer</span></li><li><i class="ss-downloadcloud"></i><span class="icon-name">ss-downloadcloud</span></li><li><i class="ss-download"></i><span class="icon-name">ss-download</span></li><li><i class="ss-downloadbox"></i><span class="icon-name">ss-downloadbox</span></li><li><i class="ss-uploadcloud"></i><span class="icon-name">ss-uploadcloud</span></li><li><i class="ss-upload"></i><span class="icon-name">ss-upload</span></li><li><i class="ss-uploadbox"></i><span class="icon-name">ss-uploadbox</span></li><li><i class="ss-fork"></i><span class="icon-name">ss-fork</span></li><li><i class="ss-merge"></i><span class="icon-name">ss-merge</span></li><li><i class="ss-refresh"></i><span class="icon-name">ss-refresh</span></li><li><i class="ss-sync"></i><span class="icon-name">ss-sync</span></li><li><i class="ss-loading"></i><span class="icon-name">ss-loading</span></li><li><i class="ss-file"></i><span class="icon-name">ss-file</span></li><li><i class="ss-files"></i><span class="icon-name">ss-files</span></li><li><i class="ss-addfile"></i><span class="icon-name">ss-addfile</span></li><li><i class="ss-removefile"></i><span class="icon-name">ss-removefile</span></li><li><i class="ss-checkfile"></i><span class="icon-name">ss-checkfile</span></li><li><i class="ss-deletefile"></i><span class="icon-name">ss-deletefile</span></li><li><i class="ss-exe"></i><span class="icon-name">ss-exe</span></li><li><i class="ss-zip"></i><span class="icon-name">ss-zip</span></li><li><i class="ss-doc"></i><span class="icon-name">ss-doc</span></li><li><i class="ss-pdf"></i><span class="icon-name">ss-pdf</span></li><li><i class="ss-jpg"></i><span class="icon-name">ss-jpg</span></li><li><i class="ss-png"></i><span class="icon-name">ss-png</span></li><li><i class="ss-mp3"></i><span class="icon-name">ss-mp3</span></li><li><i class="ss-rar"></i><span class="icon-name">ss-rar</span></li><li><i class="ss-gif"></i><span class="icon-name">ss-gif</span></li><li><i class="ss-folder"></i><span class="icon-name">ss-folder</span></li><li><i class="ss-openfolder"></i><span class="icon-name">ss-openfolder</span></li><li><i class="ss-downloadfolder"></i><span class="icon-name">ss-downloadfolder</span></li><li><i class="ss-uploadfolder"></i><span class="icon-name">ss-uploadfolder</span></li><li><i class="ss-quote"></i><span class="icon-name">ss-quote</span></li><li><i class="ss-unquote"></i><span class="icon-name">ss-unquote</span></li><li><i class="ss-print"></i><span class="icon-name">ss-print</span></li><li><i class="ss-copier"></i><span class="icon-name">ss-copier</span></li><li><i class="ss-fax"></i><span class="icon-name">ss-fax</span></li><li><i class="ss-scanner"></i><span class="icon-name">ss-scanner</span></li><li><i class="ss-printregistration"></i><span class="icon-name">ss-printregistration</span></li><li><i class="ss-shredder"></i><span class="icon-name">ss-shredder</span></li><li><i class="ss-expand"></i><span class="icon-name">ss-expand</span></li><li><i class="ss-contract"></i><span class="icon-name">ss-contract</span></li><li><i class="ss-help"></i><span class="icon-name">ss-help</span></li><li><i class="ss-info"></i><span class="icon-name">ss-info</span></li><li><i class="ss-alert"></i><span class="icon-name">ss-alert</span></li><li><i class="ss-caution"></i><span class="icon-name">ss-caution</span></li><li><i class="ss-logout"></i><span class="icon-name">ss-logout</span></li><li><i class="ss-login"></i><span class="icon-name">ss-login</span></li><li><i class="ss-scaleup"></i><span class="icon-name">ss-scaleup</span></li><li><i class="ss-scaledown"></i><span class="icon-name">ss-scaledown</span></li><li><i class="ss-plus"></i><span class="icon-name">ss-plus</span></li><li><i class="ss-hyphen"></i><span class="icon-name">ss-hyphen</span></li><li><i class="ss-check"></i><span class="icon-name">ss-check</span></li><li><i class="ss-delete"></i><span class="icon-name">ss-delete</span></li><li><i class="ss-notifications"></i><span class="icon-name">ss-notifications</span></li><li><i class="ss-notificationsdisabled"></i><span class="icon-name">ss-notificationsdisabled</span></li><li><i class="ss-clock"></i><span class="icon-name">ss-clock</span></li><li><i class="ss-stopwatch"></i><span class="icon-name">ss-stopwatch</span></li><li><i class="ss-alarmclock"></i><span class="icon-name">ss-alarmclock</span></li><li><i class="ss-egg"></i><span class="icon-name">ss-egg</span></li><li><i class="ss-eggs"></i><span class="icon-name">ss-eggs</span></li><li><i class="ss-cheese"></i><span class="icon-name">ss-cheese</span></li><li><i class="ss-chickenleg"></i><span class="icon-name">ss-chickenleg</span></li><li><i class="ss-pizzapie"></i><span class="icon-name">ss-pizzapie</span></li><li><i class="ss-pizza"></i><span class="icon-name">ss-pizza</span></li><li><i class="ss-cheesepizza"></i><span class="icon-name">ss-cheesepizza</span></li><li><i class="ss-frenchfries"></i><span class="icon-name">ss-frenchfries</span></li><li><i class="ss-apple"></i><span class="icon-name">ss-apple</span></li><li><i class="ss-carrot"></i><span class="icon-name">ss-carrot</span></li><li><i class="ss-broccoli"></i><span class="icon-name">ss-broccoli</span></li><li><i class="ss-cucumber"></i><span class="icon-name">ss-cucumber</span></li><li><i class="ss-orange"></i><span class="icon-name">ss-orange</span></li><li><i class="ss-lemon"></i><span class="icon-name">ss-lemon</span></li><li><i class="ss-onion"></i><span class="icon-name">ss-onion</span></li><li><i class="ss-bellpepper"></i><span class="icon-name">ss-bellpepper</span></li><li><i class="ss-peas"></i><span class="icon-name">ss-peas</span></li><li><i class="ss-grapes"></i><span class="icon-name">ss-grapes</span></li><li><i class="ss-strawberry"></i><span class="icon-name">ss-strawberry</span></li><li><i class="ss-bread"></i><span class="icon-name">ss-bread</span></li><li><i class="ss-mug"></i><span class="icon-name">ss-mug</span></li><li><i class="ss-mugs"></i><span class="icon-name">ss-mugs</span></li><li><i class="ss-espresso"></i><span class="icon-name">ss-espresso</span></li><li><i class="ss-macchiato"></i><span class="icon-name">ss-macchiato</span></li><li><i class="ss-cappucino"></i><span class="icon-name">ss-cappucino</span></li><li><i class="ss-latte"></i><span class="icon-name">ss-latte</span></li><li><i class="ss-icedcoffee"></i><span class="icon-name">ss-icedcoffee</span></li><li><i class="ss-coffeebean"></i><span class="icon-name">ss-coffeebean</span></li><li><i class="ss-coffeemilk"></i><span class="icon-name">ss-coffeemilk</span></li><li><i class="ss-coffeefoam"></i><span class="icon-name">ss-coffeefoam</span></li><li><i class="ss-coffeesugar"></i><span class="icon-name">ss-coffeesugar</span></li><li><i class="ss-sugarpackets"></i><span class="icon-name">ss-sugarpackets</span></li><li><i class="ss-capsule"></i><span class="icon-name">ss-capsule</span></li><li><i class="ss-capsulerecycling"></i><span class="icon-name">ss-capsulerecycling</span></li><li><i class="ss-insertcapsule"></i><span class="icon-name">ss-insertcapsule</span></li><li><i class="ss-tea"></i><span class="icon-name">ss-tea</span></li><li><i class="ss-teabag"></i><span class="icon-name">ss-teabag</span></li><li><i class="ss-jug"></i><span class="icon-name">ss-jug</span></li><li><i class="ss-pitcher"></i><span class="icon-name">ss-pitcher</span></li><li><i class="ss-kettle"></i><span class="icon-name">ss-kettle</span></li><li><i class="ss-wineglass"></i><span class="icon-name">ss-wineglass</span></li><li><i class="ss-sugar"></i><span class="icon-name">ss-sugar</span></li><li><i class="ss-oven"></i><span class="icon-name">ss-oven</span></li><li><i class="ss-stove"></i><span class="icon-name">ss-stove</span></li><li><i class="ss-vent"></i><span class="icon-name">ss-vent</span></li><li><i class="ss-exhaust"></i><span class="icon-name">ss-exhaust</span></li><li><i class="ss-steam"></i><span class="icon-name">ss-steam</span></li><li><i class="ss-dishwasher"></i><span class="icon-name">ss-dishwasher</span></li><li><i class="ss-toaster"></i><span class="icon-name">ss-toaster</span></li><li><i class="ss-microwave"></i><span class="icon-name">ss-microwave</span></li><li><i class="ss-electrickettle"></i><span class="icon-name">ss-electrickettle</span></li><li><i class="ss-refrigerator"></i><span class="icon-name">ss-refrigerator</span></li><li><i class="ss-freezer"></i><span class="icon-name">ss-freezer</span></li><li><i class="ss-utensils"></i><span class="icon-name">ss-utensils</span></li><li><i class="ss-cookingutensils"></i><span class="icon-name">ss-cookingutensils</span></li><li><i class="ss-whisk"></i><span class="icon-name">ss-whisk</span></li><li><i class="ss-pizzacutter"></i><span class="icon-name">ss-pizzacutter</span></li><li><i class="ss-measuringcup"></i><span class="icon-name">ss-measuringcup</span></li><li><i class="ss-colander"></i><span class="icon-name">ss-colander</span></li><li><i class="ss-eggtimer"></i><span class="icon-name">ss-eggtimer</span></li><li><i class="ss-platter"></i><span class="icon-name">ss-platter</span></li><li><i class="ss-plates"></i><span class="icon-name">ss-plates</span></li><li><i class="ss-steamplate"></i><span class="icon-name">ss-steamplate</span></li><li><i class="ss-cups"></i><span class="icon-name">ss-cups</span></li><li><i class="ss-steamglass"></i><span class="icon-name">ss-steamglass</span></li><li><i class="ss-pot"></i><span class="icon-name">ss-pot</span></li><li><i class="ss-steampot"></i><span class="icon-name">ss-steampot</span></li><li><i class="ss-chef"></i><span class="icon-name">ss-chef</span></li><li><i class="ss-weathervane"></i><span class="icon-name">ss-weathervane</span></li><li><i class="ss-thermometer"></i><span class="icon-name">ss-thermometer</span></li><li><i class="ss-thermometerup"></i><span class="icon-name">ss-thermometerup</span></li><li><i class="ss-thermometerdown"></i><span class="icon-name">ss-thermometerdown</span></li><li><i class="ss-droplet"></i><span class="icon-name">ss-droplet</span></li><li><i class="ss-sunrise"></i><span class="icon-name">ss-sunrise</span></li><li><i class="ss-sunset"></i><span class="icon-name">ss-sunset</span></li><li><i class="ss-sun"></i><span class="icon-name">ss-sun</span></li><li><i class="ss-cloud"></i><span class="icon-name">ss-cloud</span></li><li><i class="ss-clouds"></i><span class="icon-name">ss-clouds</span></li><li><i class="ss-partlycloudy"></i><span class="icon-name">ss-partlycloudy</span></li><li><i class="ss-rain"></i><span class="icon-name">ss-rain</span></li><li><i class="ss-rainheavy"></i><span class="icon-name">ss-rainheavy</span></li><li><i class="ss-lightning"></i><span class="icon-name">ss-lightning</span></li><li><i class="ss-thunderstorm"></i><span class="icon-name">ss-thunderstorm</span></li><li><i class="ss-umbrella"></i><span class="icon-name">ss-umbrella</span></li><li><i class="ss-rainumbrella"></i><span class="icon-name">ss-rainumbrella</span></li><li><i class="ss-rainbow"></i><span class="icon-name">ss-rainbow</span></li><li><i class="ss-rainbowclouds"></i><span class="icon-name">ss-rainbowclouds</span></li><li><i class="ss-fog"></i><span class="icon-name">ss-fog</span></li><li><i class="ss-wind"></i><span class="icon-name">ss-wind</span></li><li><i class="ss-tornado"></i><span class="icon-name">ss-tornado</span></li><li><i class="ss-snowflake"></i><span class="icon-name">ss-snowflake</span></li><li><i class="ss-snowcrystal"></i><span class="icon-name">ss-snowcrystal</span></li><li><i class="ss-lightsnow"></i><span class="icon-name">ss-lightsnow</span></li><li><i class="ss-snow"></i><span class="icon-name">ss-snow</span></li><li><i class="ss-heavysnow"></i><span class="icon-name">ss-heavysnow</span></li><li><i class="ss-hail"></i><span class="icon-name">ss-hail</span></li><li><i class="ss-crescentmoon"></i><span class="icon-name">ss-crescentmoon</span></li><li><i class="ss-waxingcrescentmoon"></i><span class="icon-name">ss-waxingcrescentmoon</span></li><li><i class="ss-firstquartermoon"></i><span class="icon-name">ss-firstquartermoon</span></li><li><i class="ss-waxinggibbousmoon"></i><span class="icon-name">ss-waxinggibbousmoon</span></li><li><i class="ss-waninggibbousmoon"></i><span class="icon-name">ss-waninggibbousmoon</span></li><li><i class="ss-lastquartermoon"></i><span class="icon-name">ss-lastquartermoon</span></li><li><i class="ss-waningcrescentmoon"></i><span class="icon-name">ss-waningcrescentmoon</span></li><li><i class="ss-fan"></i><span class="icon-name">ss-fan</span></li><li><i class="ss-bike"></i><span class="icon-name">ss-bike</span></li><li><i class="ss-wheelchair"></i><span class="icon-name">ss-wheelchair</span></li><li><i class="ss-briefcase"></i><span class="icon-name">ss-briefcase</span></li><li><i class="ss-hanger"></i><span class="icon-name">ss-hanger</span></li><li><i class="ss-comb"></i><span class="icon-name">ss-comb</span></li><li><i class="ss-medicalcross"></i><span class="icon-name">ss-medicalcross</span></li><li><i class="ss-up"></i><span class="icon-name">ss-up</span></li><li><i class="ss-upright"></i><span class="icon-name">ss-upright</span></li><li><i class="ss-right"></i><span class="icon-name">ss-right</span></li><li><i class="ss-downright"></i><span class="icon-name">ss-downright</span></li><li><i class="ss-down"></i><span class="icon-name">ss-down</span></li><li><i class="ss-downleft"></i><span class="icon-name">ss-downleft</span></li><li><i class="ss-left"></i><span class="icon-name">ss-left</span></li><li><i class="ss-upleft"></i><span class="icon-name">ss-upleft</span></li><li><i class="ss-navigateup"></i><span class="icon-name">ss-navigateup</span></li><li><i class="ss-navigateright"></i><span class="icon-name">ss-navigateright</span></li><li><i class="ss-navigatedown"></i><span class="icon-name">ss-navigatedown</span></li><li><i class="ss-navigateleft"></i><span class="icon-name">ss-navigateleft</span></li><li><i class="ss-retweet"></i><span class="icon-name">ss-retweet</span></li><li><i class="ss-share"></i><span class="icon-name">ss-share</span></li>';

			// IconMind
			$icon_mind_list = '<li><i class="sf-im-gear"></i><span class="icon-name">sf-im-gear</span></li><li><i class="sf-im-gears"></i><span class="icon-name">sf-im-gears</span></li><li><i class="sf-im-information"></i><span class="icon-name">sf-im-information</span></li><li><i class="sf-im-magnifi-glass-"></i><span class="icon-name">sf-im-magnifi-glass-</span></li><li><i class="sf-im-magnifi-glass"></i><span class="icon-name">sf-im-magnifi-glass</span></li><li><i class="sf-im-magnifi-glass2"></i><span class="icon-name">sf-im-magnifi-glass2</span></li><li><i class="sf-im-preview"></i><span class="icon-name">sf-im-preview</span></li><li><i class="sf-im-pricing"></i><span class="icon-name">sf-im-pricing</span></li><li><i class="sf-im-repair"></i><span class="icon-name">sf-im-repair</span></li><li><i class="sf-im-support"></i><span class="icon-name">sf-im-support</span></li><li><i class="sf-im-user"></i><span class="icon-name">sf-im-user</span></li><li><i class="sf-im-equalizer"></i><span class="icon-name">sf-im-equalizer</span></li><li><i class="sf-im-microphone-2"></i><span class="icon-name">sf-im-microphone-2</span></li><li><i class="sf-im-rock-androll"></i><span class="icon-name">sf-im-rock-androll</span></li><li><i class="sf-im-sound-wave"></i><span class="icon-name">sf-im-sound-wave</span></li><li><i class="sf-im-close-window"></i><span class="icon-name">sf-im-close-window</span></li><li><i class="sf-im-network-window"></i><span class="icon-name">sf-im-network-window</span></li><li><i class="sf-im-settings-window"></i><span class="icon-name">sf-im-settings-window</span></li><li><i class="sf-im-two-windows"></i><span class="icon-name">sf-im-two-windows</span></li><li><i class="sf-im-upload-window"></i><span class="icon-name">sf-im-upload-window</span></li><li><i class="sf-im-url-window"></i><span class="icon-name">sf-im-url-window</span></li><li><i class="sf-im-width-window"></i><span class="icon-name">sf-im-width-window</span></li><li><i class="sf-im-windows-2"></i><span class="icon-name">sf-im-windows-2</span></li><li><i class="sf-im-drop"></i><span class="icon-name">sf-im-drop</span></li><li><i class="sf-im-clapperboard-open"></i><span class="icon-name">sf-im-clapperboard-open</span></li><li><i class="sf-im-video-3"></i><span class="icon-name">sf-im-video-3</span></li><li><i class="sf-im-hand-touch2"></i><span class="icon-name">sf-im-hand-touch2</span></li><li><i class="sf-im-thumb"></i><span class="icon-name">sf-im-thumb</span></li><li><i class="sf-im-clock"></i><span class="icon-name">sf-im-clock</span></li><li><i class="sf-im-watch"></i><span class="icon-name">sf-im-watch</span></li><li><i class="sf-im-normal-text"></i><span class="icon-name">sf-im-normal-text</span></li><li><i class="sf-im-text-box"></i><span class="icon-name">sf-im-text-box</span></li><li><i class="sf-im-text-effect"></i><span class="icon-name">sf-im-text-effect</span></li><li><i class="sf-im-archery-2"></i><span class="icon-name">sf-im-archery-2</span></li><li><i class="sf-im-medal-3"></i><span class="icon-name">sf-im-medal-3</span></li><li><i class="sf-im-skate-shoes"></i><span class="icon-name">sf-im-skate-shoes</span></li><li><i class="sf-im-trophy"></i><span class="icon-name">sf-im-trophy</span></li><li><i class="sf-im-speach-bubbleasking"></i><span class="icon-name">sf-im-speach-bubbleasking</span></li><li><i class="sf-im-speach-bubbledialog"></i><span class="icon-name">sf-im-speach-bubbledialog</span></li><li><i class="sf-im-inifity"></i><span class="icon-name">sf-im-inifity</span></li><li><i class="sf-im-quotes"></i><span class="icon-name">sf-im-quotes</span></li><li><i class="sf-im-ribbon"></i><span class="icon-name">sf-im-ribbon</span></li><li><i class="sf-im-venn-diagram"></i><span class="icon-name">sf-im-venn-diagram</span></li><li><i class="sf-im-car-coins"></i><span class="icon-name">sf-im-car-coins</span></li><li><i class="sf-im-cash-register2"></i><span class="icon-name">sf-im-cash-register2</span></li><li><i class="sf-im-password-shopping"></i><span class="icon-name">sf-im-password-shopping</span></li><li><i class="sf-im-tag-5"></i><span class="icon-name">sf-im-tag-5</span></li><li><i class="sf-im-coding"></i><span class="icon-name">sf-im-coding</span></li><li><i class="sf-im-consulting"></i><span class="icon-name">sf-im-consulting</span></li><li><i class="sf-im-testimonal"></i><span class="icon-name">sf-im-testimonal</span></li><li><i class="sf-im-lock-2"></i><span class="icon-name">sf-im-lock-2</span></li><li><i class="sf-im-unlock-2"></i><span class="icon-name">sf-im-unlock-2</span></li><li><i class="sf-im-atom"></i><span class="icon-name">sf-im-atom</span></li><li><i class="sf-im-chemical"></i><span class="icon-name">sf-im-chemical</span></li><li><i class="sf-im-plaster"></i><span class="icon-name">sf-im-plaster</span></li><li><i class="sf-im-camera-2"></i><span class="icon-name">sf-im-camera-2</span></li><li><i class="sf-im-flash-2"></i><span class="icon-name">sf-im-flash-2</span></li><li><i class="sf-im-photo"></i><span class="icon-name">sf-im-photo</span></li><li><i class="sf-im-photos"></i><span class="icon-name">sf-im-photos</span></li><li><i class="sf-im-sport-mode"></i><span class="icon-name">sf-im-sport-mode</span></li><li><i class="sf-im-business-man"></i><span class="icon-name">sf-im-business-man</span></li><li><i class="sf-im-business-woman"></i><span class="icon-name">sf-im-business-woman</span></li><li><i class="sf-im-speak-2"></i><span class="icon-name">sf-im-speak-2</span></li><li><i class="sf-im-talk-man"></i><span class="icon-name">sf-im-talk-man</span></li><li><i class="sf-im-chair"></i><span class="icon-name">sf-im-chair</span></li><li><i class="sf-im-footprint"></i><span class="icon-name">sf-im-footprint</span></li><li><i class="sf-im-gift-box"></i><span class="icon-name">sf-im-gift-box</span></li><li><i class="sf-im-key"></i><span class="icon-name">sf-im-key</span></li><li><i class="sf-im-light-bulb"></i><span class="icon-name">sf-im-light-bulb</span></li><li><i class="sf-im-luggage-2"></i><span class="icon-name">sf-im-luggage-2</span></li><li><i class="sf-im-paper-plane"></i><span class="icon-name">sf-im-paper-plane</span></li><li><i class="sf-im-environmental-3"></i><span class="icon-name">sf-im-environmental-3</span></li><li><i class="sf-im-compass-4"></i><span class="icon-name">sf-im-compass-4</span></li><li><i class="sf-im-globe"></i><span class="icon-name">sf-im-globe</span></li><li><i class="sf-im-map-marker"></i><span class="icon-name">sf-im-map-marker</span></li><li><i class="sf-im-map2"></i><span class="icon-name">sf-im-map2</span></li><li><i class="sf-im-satelite-2"></i><span class="icon-name">sf-im-satelite-2</span></li><li><i class="sf-im-add"></i><span class="icon-name">sf-im-add</span></li><li><i class="sf-im-close"></i><span class="icon-name">sf-im-close</span></li><li><i class="sf-im-cursor-click2"></i><span class="icon-name">sf-im-cursor-click2</span></li><li><i class="sf-im-download-2"></i><span class="icon-name">sf-im-download-2</span></li><li><i class="sf-im-link"></i><span class="icon-name">sf-im-link</span></li><li><i class="sf-im-upload-2"></i><span class="icon-name">sf-im-upload-2</span></li><li><i class="sf-im-yes"></i><span class="icon-name">sf-im-yes</span></li><li><i class="sf-im-old-camera"></i><span class="icon-name">sf-im-old-camera</span></li><li><i class="sf-im-mouse-4"></i><span class="icon-name">sf-im-mouse-4</span></li><li><i class="sf-im-coffee"></i><span class="icon-name">sf-im-coffee</span></li><li><i class="sf-im-doughnut"></i><span class="icon-name">sf-im-doughnut</span></li><li><i class="sf-im-glass-water"></i><span class="icon-name">sf-im-glass-water</span></li><li><i class="sf-im-hot-dog"></i><span class="icon-name">sf-im-hot-dog</span></li><li><i class="sf-im-juice"></i><span class="icon-name">sf-im-juice</span></li><li><i class="sf-im-pizza-slice"></i><span class="icon-name">sf-im-pizza-slice</span></li><li><i class="sf-im-pizza"></i><span class="icon-name">sf-im-pizza</span></li><li><i class="sf-im-wine-glass"></i><span class="icon-name">sf-im-wine-glass</span></li><li><i class="sf-im-box-open"></i><span class="icon-name">sf-im-box-open</span></li><li><i class="sf-im-box-withfolders"></i><span class="icon-name">sf-im-box-withfolders</span></li><li><i class="sf-im-add-file"></i><span class="icon-name">sf-im-add-file</span></li><li><i class="sf-im-delete-file"></i><span class="icon-name">sf-im-delete-file</span></li><li><i class="sf-im-file-download"></i><span class="icon-name">sf-im-file-download</span></li><li><i class="sf-im-file-horizontaltext"></i><span class="icon-name">sf-im-file-horizontaltext</span></li><li><i class="sf-im-file-link"></i><span class="icon-name">sf-im-file-link</span></li><li><i class="sf-im-file-love"></i><span class="icon-name">sf-im-file-love</span></li><li><i class="sf-im-file-pictures"></i><span class="icon-name">sf-im-file-pictures</span></li><li><i class="sf-im-file-zip"></i><span class="icon-name">sf-im-file-zip</span></li><li><i class="sf-im-files"></i><span class="icon-name">sf-im-files</span></li><li><i class="sf-im-remove-file"></i><span class="icon-name">sf-im-remove-file</span></li><li><i class="sf-im-thumbs-upsmiley"></i><span class="icon-name">sf-im-thumbs-upsmiley</span></li><li><i class="sf-im-letter-open"></i><span class="icon-name">sf-im-letter-open</span></li><li><i class="sf-im-mail"></i><span class="icon-name">sf-im-mail</span></li><li><i class="sf-im-mailbox-full"></i><span class="icon-name">sf-im-mailbox-full</span></li><li><i class="sf-im-notepad"></i><span class="icon-name">sf-im-notepad</span></li><li><i class="sf-im-computer"></i><span class="icon-name">sf-im-computer</span></li><li><i class="sf-im-laptop"></i><span class="icon-name">sf-im-laptop</span></li><li><i class="sf-im-monitor-2"></i><span class="icon-name">sf-im-monitor-2</span></li><li><i class="sf-im-monitor-5"></i><span class="icon-name">sf-im-monitor-5</span></li><li><i class="sf-im-monitor-phone"></i><span class="icon-name">sf-im-monitor-phone</span></li><li><i class="sf-im-phone-2"></i><span class="icon-name">sf-im-phone-2</span></li><li><i class="sf-im-smartphone-4"></i><span class="icon-name">sf-im-smartphone-4</span></li><li><i class="sf-im-tablet-3"></i><span class="icon-name">sf-im-tablet-3</span></li><li><i class="sf-im-aa"></i><span class="icon-name">sf-im-aa</span></li><li><i class="sf-im-brush"></i><span class="icon-name">sf-im-brush</span></li><li><i class="sf-im-fountain-pen"></i><span class="icon-name">sf-im-fountain-pen</span></li><li><i class="sf-im-idea"></i><span class="icon-name">sf-im-idea</span></li><li><i class="sf-im-marker"></i><span class="icon-name">sf-im-marker</span></li><li><i class="sf-im-note"></i><span class="icon-name">sf-im-note</span></li><li><i class="sf-im-pantone"></i><span class="icon-name">sf-im-pantone</span></li><li><i class="sf-im-pencil"></i><span class="icon-name">sf-im-pencil</span></li><li><i class="sf-im-scissor"></i><span class="icon-name">sf-im-scissor</span></li><li><i class="sf-im-vector-3"></i><span class="icon-name">sf-im-vector-3</span></li><li><i class="sf-im-address-book"></i><span class="icon-name">sf-im-address-book</span></li><li><i class="sf-im-megaphone"></i><span class="icon-name">sf-im-megaphone</span></li><li><i class="sf-im-newspaper"></i><span class="icon-name">sf-im-newspaper</span></li><li><i class="sf-im-wifi"></i><span class="icon-name">sf-im-wifi</span></li><li><i class="sf-im-download-fromcloud"></i><span class="icon-name">sf-im-download-fromcloud</span></li><li><i class="sf-im-upload-tocloud"></i><span class="icon-name">sf-im-upload-tocloud</span></li><li><i class="sf-im-blouse"></i><span class="icon-name">sf-im-blouse</span></li><li><i class="sf-im-boot"></i><span class="icon-name">sf-im-boot</span></li><li><i class="sf-im-bow-2"></i><span class="icon-name">sf-im-bow-2</span></li><li><i class="sf-im-bra"></i><span class="icon-name">sf-im-bra</span></li><li><i class="sf-im-cap"></i><span class="icon-name">sf-im-cap</span></li><li><i class="sf-im-coat"></i><span class="icon-name">sf-im-coat</span></li><li><i class="sf-im-dress"></i><span class="icon-name">sf-im-dress</span></li><li><i class="sf-im-hanger"></i><span class="icon-name">sf-im-hanger</span></li><li><i class="sf-im-heels"></i><span class="icon-name">sf-im-heels</span></li><li><i class="sf-im-jacket"></i><span class="icon-name">sf-im-jacket</span></li><li><i class="sf-im-jeans"></i><span class="icon-name">sf-im-jeans</span></li><li><i class="sf-im-shirt"></i><span class="icon-name">sf-im-shirt</span></li><li><i class="sf-im-suit"></i><span class="icon-name">sf-im-suit</span></li><li><i class="sf-im-sunglasses-w3"></i><span class="icon-name">sf-im-sunglasses-w3</span></li><li><i class="sf-im-t-shirt"></i><span class="icon-name">sf-im-t-shirt</span></li><li><i class="sf-im-present"></i><span class="icon-name">sf-im-present</span></li><li><i class="sf-im-tactic"></i><span class="icon-name">sf-im-tactic</span></li><li><i class="sf-im-bar-chart3"></i><span class="icon-name">sf-im-bar-chart3</span></li><li><i class="sf-im-calculator-2"></i><span class="icon-name">sf-im-calculator-2</span></li><li><i class="sf-im-calendar-4"></i><span class="icon-name">sf-im-calendar-4</span></li><li><i class="sf-im-credit-card2"></i><span class="icon-name">sf-im-credit-card2</span></li><li><i class="sf-im-diamond"></i><span class="icon-name">sf-im-diamond</span></li><li><i class="sf-im-financial"></i><span class="icon-name">sf-im-financial</span></li><li><i class="sf-im-handshake"></i><span class="icon-name">sf-im-handshake</span></li><li><i class="sf-im-line-chart4"></i><span class="icon-name">sf-im-line-chart4</span></li><li><i class="sf-im-money-2"></i><span class="icon-name">sf-im-money-2</span></li><li><i class="sf-im-pie-chart3"></i><span class="icon-name">sf-im-pie-chart3</span></li><li><i class="sf-im-home"></i><span class="icon-name">sf-im-home</span></li><li><i class="sf-im-bones"></i><span class="icon-name">sf-im-bones</span></li><li><i class="sf-im-brain"></i><span class="icon-name">sf-im-brain</span></li><li><i class="sf-im-ear"></i><span class="icon-name">sf-im-ear</span></li><li><i class="sf-im-eye-visible"></i><span class="icon-name">sf-im-eye-visible</span></li><li><i class="sf-im-face-style"></i><span class="icon-name">sf-im-face-style</span></li><li><i class="sf-im-fingerprint-2"></i><span class="icon-name">sf-im-fingerprint-2</span></li><li><i class="sf-im-heart"></i><span class="icon-name">sf-im-heart</span></li><li><i class="sf-im-arrow-downincircle"></i><span class="icon-name">sf-im-arrow-downincircle</span></li><li><i class="sf-im-arrow-left"></i><span class="icon-name">sf-im-arrow-left</span></li><li><i class="sf-im-arrow-right"></i><span class="icon-name">sf-im-arrow-right</span></li><li><i class="sf-im-arrow-up"></i><span class="icon-name">sf-im-arrow-up</span></li><li><i class="sf-im-download"></i><span class="icon-name">sf-im-download</span></li><li><i class="sf-im-fit-to"></i><span class="icon-name">sf-im-fit-to</span></li><li><i class="sf-im-full-screen"></i><span class="icon-name">sf-im-full-screen</span></li><li><i class="sf-im-full-screen2"></i><span class="icon-name">sf-im-full-screen2</span></li><li><i class="sf-im-left"></i><span class="icon-name">sf-im-left</span></li><li><i class="sf-im-repeat-2"></i><span class="icon-name">sf-im-repeat-2</span></li><li><i class="sf-im-right"></i><span class="icon-name">sf-im-right</span></li><li><i class="sf-im-up"></i><span class="icon-name">sf-im-up</span></li><li><i class="sf-im-upload"></i><span class="icon-name">sf-im-upload</span></li><li><i class="sf-im-arrow-around"></i><span class="icon-name">sf-im-arrow-around</span></li><li><i class="sf-im-arrow-loop"></i><span class="icon-name">sf-im-arrow-loop</span></li><li><i class="sf-im-arrow-outleft"></i><span class="icon-name">sf-im-arrow-outleft</span></li><li><i class="sf-im-arrow-outright"></i><span class="icon-name">sf-im-arrow-outright</span></li><li><i class="sf-im-arrow-shuffle"></i><span class="icon-name">sf-im-arrow-shuffle</span></li><li><i class="sf-im-maximize"></i><span class="icon-name">sf-im-maximize</span></li><li><i class="sf-im-minimize"></i><span class="icon-name">sf-im-minimize</span></li><li><i class="sf-im-resize"></i><span class="icon-name">sf-im-resize</span></li><li><i class="sf-im-bird"></i><span class="icon-name">sf-im-bird</span></li><li><i class="sf-im-cat"></i><span class="icon-name">sf-im-cat</span></li><li><i class="sf-im-dog"></i><span class="icon-name">sf-im-dog</span></li><li><i class="sf-im-align-center"></i><span class="icon-name">sf-im-align-center</span></li><li><i class="sf-im-align-left"></i><span class="icon-name">sf-im-align-left</span></li><li><i class="sf-im-align-right"></i><span class="icon-name">sf-im-align-right</span></li>';

            // OUTPUT
            if ( $type == "font-awesome" || $type == "" ) {
                $icon_list .= $fa_list;
            }
            if ( sf_theme_supports('gizmo-icon-font') && ($type == "gizmo" || $type == "") ) {
                $icon_list .= $gizmo_list;
            }
            if ( sf_theme_supports('icon-mind-font') && ($type == "icon-mind" || $type == "") ) {
                $icon_list .= $icon_mind_list;
            }

            // APPLY FILTERS
            $icon_list = apply_filters( 'sf_icons_list', $icon_list );

            return $icon_list;
        }
    }

	/* ANIMATIONS LIST
	================================================== */
	if ( ! function_exists( 'sf_get_animations_list' ) ) {
		function sf_get_animations_list($return_array = false) {
		    $anim_array = array(
		        __( "None", "swiftframework" )              	=> "none",
		        __( "Fade In", "swiftframework" )            => "fade-in",
		        __( "Fade from Left", "swiftframework" )             	=> "fade-from-left",
		        __( "Fade from Right", "swiftframework" )             	=> "fade-from-right",
		        __( "Fade from Bottom", "swiftframework" )             	=> "fade-from-bottom",
		        __( "Move up", "swiftframework" )             	=> "move-up",
		        __( "Grow", "swiftframework" )              	=> "grow",
		        __( "Helix", "swiftframework" )            	=> "helix",
		        __( "Flip", "swiftframework" )         	=> "flip",
		        __( "Pop up", "swiftframework" )     => "pop-up",
		        __( "Spin", "swiftframework" )     => "spin",
		        __( "Flip-X", "swiftframework" )    => "flip-x",
		        __( "Flip-Y", "swiftframework" )       => "flip-y",
		    );

		    if ( $return_array ) {
		    	return $anim_array;
		    } else {
			    $anim_opts = "";

			    foreach ($anim_array as $anim_name => $anim_class) {
			    	$anim_opts .= 'option value="'.$anim_class.'">'.$anim_name.'</option>';
			    }

			    return $anim_opts;
		    }
		}
	}


    /* DIRECTORY FRONT END SUBMISSION
    ================================================== */
    if ( ! function_exists( 'sf_create_directory_item' ) ) {
        function sf_create_directory_item() {

            if ( ! is_admin() ) {
                if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['dirtype'] == 'directory' ) {

                    // Do some minor form validation to make sure there is content
                    if ( isset ( $_POST['directory_title'] ) ) {
                        $title = $_POST['directory_title'];
                    }
                    if ( isset ( $_POST['directory_description'] ) ) {
                        $description = $_POST['directory_description'];
                    }

                    $sf_directory_address         = trim( $_POST['sf_directory_address'] );
                    $sf_directory_lat_coord       = trim( $_POST['sf_directory_lat_coord'] );
                    $sf_directory_lng_coord       = trim( $_POST['sf_directory_lng_coord'] );
                    $sf_directory_pin_link        = trim( $_POST['sf_directory_pin_link'] );
                    $sf_directory_pin_button_text = trim( $_POST['sf_directory_pin_button_text'] );

                    // Get the array of selected categories as multiple cats can be selected
                    $category = $_POST['directory-cat'];
                    $location = $_POST['directory-loc'];

                    // Add the content of the form to $post as an array
                    $post    = array(
                        'post_title'   => wp_strip_all_tags( $title ),
                        'post_content' => $description,
                        'post_status'  => 'pending', // Choose: publish, preview, future, etc.
                        'post_type'    => 'directory' // Set the post type based on the IF is post_type X
                    );
                    $post_id = wp_insert_post( $post );  // Pass  the value of $post to WordPress the insert function

                    // Add Custom meta fields
                    add_post_meta( $post_id, 'sf_directory_address', $sf_directory_address );
                    add_post_meta( $post_id, 'sf_directory_lat_coord', $sf_directory_lat_coord );
                    add_post_meta( $post_id, 'sf_directory_lng_coord', $sf_directory_lng_coord );
                    add_post_meta( $post_id, 'sf_directory_pin_link', $sf_directory_pin_link );
                    add_post_meta( $post_id, 'sf_directory_pin_button_text', $sf_directory_pin_button_text );

                    //Add Taxonomy terms(Location/Category)
                    wp_set_object_terms( $post_id, (int) $category, 'directory-category', true );
                    wp_set_object_terms( $post_id, (int) $location, 'directory-location', true );

                    //Proccess Images
                    if ( $_FILES ) {

                        foreach ( $_FILES as $file => $array ) {
                            $newupload = sf_insert_attachment( $file, $post_id );

                            if ( $file == 'pin_image' ) {
                                update_post_meta( $post_id, 'sf_directory_map_pin', $newupload );
                            } else {
                                update_post_meta( $post_id, '_thumbnail_id', $newupload );
                            }
                        }
                    }

                    //Send notification email to admin
                    $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
                    $itemlink = get_edit_post_link( $post_id, "" );
                    $message  = sprintf( __( 'There is a new directory entry pending review, you can view it here:', 'swiftframework' ) . ' %s', $itemlink ) . "\r\n\r\n";

                    @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New directory entry pending review.', 'swiftframework' ), $blogname ), $message );

                    //Success Message
                    echo "<h3>" . __( "Thank you for your submission, your entry is pending review.", "swiftframework" ) . "</h3>";
                    exit();

                } else {

                    //Dropdown translation text
                    $choosecatmsg = __( 'Choose a Category', 'swiftframework' );
                    $chooselocmsg = __( 'Choose a Location', 'swiftframework' );

                    //Directory Category
                    $argscat = array(
                        'id'               => 'directory-cat',
                        'name'             => 'directory-cat',
                        'show_option_none' => $choosecatmsg,
                        'tab_index'        => 4,
                        'taxonomy'         => 'directory-category'
                    );

                    //Directory Location
                    $argsloc = array(
                        'id'               => 'directory-loc',
                        'name'             => 'directory-loc',
                        'show_option_none' => $chooselocmsg,
                        'tab_index'        => 4,
                        'taxonomy'         => 'directory-location'
                    );
                    ?>

                    <!--  Front End Form display   -->
                    <div class="directory-submit-wrap">
                        <form id="add-directory-entry" name="add-directory-entry" method="post" action=""
                              enctype="multipart/form-data">
                            <p class="directory-error">
                                <label
                                    class="directory_error_form"><span> <?php _e( "Please fill all the fields", "swiftframework" ); ?></span></label>
                            </p>

                            <!--   Title  -->
                            <p><label for="directory_title"><?php _e( "Title", "swiftframework" ); ?></label><br/>
                                <input type="text" id="directory_title" value="" tabindex="1" size="20"
                                       name="directory_title"/></p>

                            <!--   Description  -->
                            <p><label for="description"><?php _e( "Description", "swiftframework" ); ?></label><br/>
                                <textarea id="directory_description" tabindex="3" name="directory_description" cols="50"
                                          rows="6"></textarea></p>

                            <!--   Directory Category  -->
                            <p>
                                <label for="description"><?php _e( "Category", "swiftframework" ); ?></label>
                                <?php wp_dropdown_categories( $argscat ); ?>
                            </p>

                            <!--   Directory Location  -->
                            <p>
                                <label for="description"><?php _e( "Location", "swiftframework" ); ?></label>
                                <?php wp_dropdown_categories( $argsloc ); ?>
                            </p>

                            <!--   Address  -->
                            <p>
                                <label for="sf_directory_address"><?php _e( "Address", "swiftframework" ); ?></label>
                                <input type="text" value="" tabindex="5" size="16" name="sf_directory_address"
                                       id="sf_directory_address"/>
                                <a href="#" id="sf_directory_calculate_coordinates"
                                   class="read-more-button hide-if-no-js"><?php _e( "Generate Coordinates", "swiftframework" ); ?></a>
                            </p>

                            <!--   Latitude Coordinate  -->
                            <p><label
                                    for="sf_directory_lat_coord"><?php _e( "Latitude Coordinate", "swiftframework" ); ?></label>
                                <input type="text" value="" tabindex="5" size="16" name="sf_directory_lat_coord"
                                       id="sf_directory_lat_coord"/></p>

                            <!--   Longitude Coordinate  -->
                            <p><label
                                    for="sf_directory_lng_coord"><?php _e( "Longitude Coordinate", "swiftframework" ); ?></label>
                                <input type="text" value="" tabindex="5" size="16" name="sf_directory_lng_coord"
                                       id="sf_directory_lng_coord"/></p>

                            <!--   Pin Image  -->
                            <label for="file"><?php _e( "Pin Image", "swiftframework" ); ?></label>

                            <p><input type="file" name="pin_image" id="pin_image"></p>

                            <!--   Directory Featured Image  -->
                            <label for="file"><?php _e( "Featured Image", "swiftframework" ); ?></label>

                            <p><input type="file" name="featured_image" id="featured_image"></p>

                            <!--   Pin Link Button  -->
                            <p><label for="sf_directory_pin_link"><?php _e( "Pin Link", "swiftframework" ); ?></label>
                                <input type="text" value="" tabindex="5" size="16" name="sf_directory_pin_link"
                                       id="sf_directory_pin_link"/></p>

                            <!--   Pin Button Text  -->
                            <p><label
                                    for="sf_directory_pin_button_text"><?php _e( "Pin Button Text", "swiftframework" ); ?></label>
                                <input type="text" value="" tabindex="5" size="16" name="sf_directory_pin_button_text"
                                       id="sf_directory_pin_button_text"/></p>

                            <!--   Post type  -->
                            <input type="hidden" name="dirtype" id="dirtype" value="directory"/>
                            <input type="hidden" name="action" value="postdirectory"/>

                            <p><input type="submit" value="<?php _e( "Create", "swiftframework" ); ?>"
                                      id="directory-submit" name="directory-submit"/></p>
                        </form>
                    </div>


                <?php
                }
            }
        }
    }

    add_action( 'sf_insert_directory', 'sf_create_directory_item' );


    /* ADMIN CUSTOM POST TYPE ICONS
    ================================================== */
    if ( ! function_exists( 'sf_admin_css' ) ) {
        function sf_admin_css() {
            ?>
            <style type="text/css" media="screen">

            #adminmenu #toplevel_page_admin-import-swiftdemo .wp-menu-image img {
                padding: 7px 0 0;
            }

            .sf-plugin-note-wrap {
                padding: 20px;
                background: #fff;
                margin: 20px 0;
                border: 1px solid #e3e3e3;
            }

            .sf-plugin-note-wrap h3 {
                margin-top: 0;
            }

            /* REVSLIDER HIDE ACTIVATION */
            a[name="activateplugin"] + div, a[name="activateplugin"] + div + div, a[name="activateplugin"] + div + div + div, a[name="activateplugin"] + div + div + div + div {
                display: none;
            }

            #redux_demo-preset_bg_image.redux-container-image_select .redux-image-select img {
                width: 50px;
                height: 50px;
                min-height: 50px;
            }

            #toplevel_page_sf_theme_options .wp-menu-image img {
                width: 11px;
                margin-top: -2px;
                margin-left: 3px;
            }

            .toplevel_page_sf_theme_options #adminmenu li#toplevel_page_sf_theme_options.wp-has-current-submenu a.wp-has-current-submenu, .toplevel_page_sf_theme_options #adminmenu #toplevel_page_sf_theme_options .wp-menu-arrow div, .toplevel_page_sf_theme_options #adminmenu #toplevel_page_sf_theme_options .wp-menu-arrow {
                background: #222;
                border-color: #222;
            }

            #wpbody-content {
                min-height: 815px;
            }

            .wp-list-table th#thumbnail, .wp-list-table td.thumbnail {
                width: 80px;
            }

            .wp-list-table td.thumbnail img {
                max-width: 100%;
                height: auto;
            }

            .sf-menu-options {
                clear: both;
                height: auto;
                overflow: hidden;
                margin-bottom: 20px;
            }

            .sf-menu-options h4 {
                margin-top: 20px;
                margin-bottom: 5px;
                border-bottom: 1px solid #e3e3e3;
                margin-right: 15px;
                padding-bottom: 5px;
            }

            .sf-menu-options p label input[type=checkbox] {
                margin-left: 10px;
            }

            .sf-menu-options p label input[type=text] {
                margin-top: 5px;
            }

            .sf-menu-options p label textarea {
                margin-top: 5px;
                width: 100%;
            }

            /* THEME OPTIONS */
            .redux-container {
                position: relative;
            }

            #redux-header h2 {
                color: #666 !important;
            }

            .redux_field_search {
                right: 20px;
                top: 7px;
            }

            .redux-container-custom_font {
            	margin-bottom: 30px;
            }

            .admin-color-fresh #redux-header {
                background: #fff;
                border-color: #ff6666;
            }

            .admin-color-fresh .redux-sidebar .redux-group-menu li.active {
                border-left-color: #ff6666;
            }

            .admin-color-fresh .redux-sidebar .redux-group-menu li.active.hasSubSections a, .admin-color-fresh .redux-sidebar .redux-group-menu li.activeChild.hasSubSections a {
                background: #ff6666;
            }

            .admin-color-fresh .redux-sidebar .redux-group-menu li.active.hasSubSections ul.subsection li a, .admin-color-fresh .redux-sidebar .redux-group-menu li.activeChild.hasSubSections ul.subsection li a {
                padding: 12px 10px;
            }

            .redux-container-image_select ul.redux-image-select li, .redux-container-image_select ul.redux-image-select label {
                width: 50px;
                height: 50px;
                margin: 0 10px 10px 0 !important;
            }

            fieldset[id*="page_layout"] ul.redux-image-select li, fieldset[id*="page_layout"] ul.redux-image-select li label {
                width: 100px;
                height: 100px;
                margin: 0 10px 25px 0 !important;
            }

            fieldset[id*="footer_layout"] ul.redux-image-select li, fieldset[id*="footer_layout"] ul.redux-image-select li label, fieldset[id*="global_banner_layout"] ul.redux-image-select li, fieldset[id*="global_banner_layout"] ul.redux-image-select li label {
                width: 128px;
                height: 60px;
                margin-bottom: 20px!important;
            }

            fieldset[id*="header_layout"] ul.redux-image-select li, fieldset[id*="header_layout"] ul.redux-image-select label {
                width: 98%;
                height: auto;
            }

            fieldset[id*="header_layout"] ul.redux-image-select img {
                height: auto !important;
            }

            fieldset[id*="thumbnail_type"] ul.redux-image-select li {
                width: 30%;
                height: auto;
            }

            fieldset[id*="thumbnail_type"] ul.redux-image-select li label {
                width: 100%;
                height: auto;
            }

            fieldset[id*="thumbnail_type"].redux-container-image_select ul.redux-image-select li img {
                height: auto;
                margin-bottom: 6px;
            }

            .redux-container-image_select ul.redux-image-select li img {
                width: 100%;
                height: 100%;
            }

            .redux_field_th .scheme-buttons {
                margin-top: 20px;
            }

            .redux_field_th .scheme-buttons .save-this-scheme-name {
                margin-right: 8px;
                padding: 6px 8px 5px;
                line-height: 15px;
                border-radius: 2px;
            }

            #sf-export-scheme-name, .delete-this-scheme {
                margin-right: 8px !important;
            }

            #header_left_config_enabled, #header_left_config_disabled, #header_right_config_enabled, #header_right_config_disabled {
                width: 90%;
                margin: 0 0 20px 0;
            }

            .redux-container-sorter ul li {
                width: auto;
                float: left;
                margin-right: 10px;
            }

            .redux-container-sorter ul li.placeholder {
                width: 120px;
            }

            /* META BOX CUSTOM */
            .rwmb-input .rwmb-slider {
                background: #f7f7f7;
                border: 1px solid #e3e3e3;
            }

            .rwmb-slider.ui-slider-horizontal .ui-slider-range-min {
                background: #fe504f!important;
            }

            .rwmb-slider-value-label {
                vertical-align: 0;
            }

            .rwmb-images img {
                max-width: 150px;
                max-height: 150px;
                width: auto;
                height: auto;
            }

            h2.meta-box-section {
                border-bottom: 1px solid #e4e4e4;
                padding-bottom: 10px !important;
                margin-top: 20px !important;
                font-size: 18px !important;
                color: #444;
            }

            .rwmb-meta-box div:first-child h2.meta-box-section {
                margin-top: 0 !important;
            }

            /* META BOX TABS */
            
            #sf_meta_box > .inside {
            	margin: 0;
            	padding: 0;
            }
            .sf-meta-tabs-wrap {
                height: auto;
                overflow: hidden;
            }

            .rwmb-meta-box {
                padding: 20px 10px;
            }

            .sf-meta-tabs-wrap.all-hidden {
                display: none;
            }

            #sf-tabbed-meta-boxes {
                position: relative;
                z-index: 1;
                float: right;
                width: 80%;
                border-left: 1px solid #e3e3e3;
            }


            #sf-tabbed-meta-boxes .inside {
                display: block !important;
            }

            #sf-tabbed-meta-boxes > div {
                border-left: 0;
                border-right: 0;
                border-bottom: 0;
                margin-bottom: 0;
                padding-bottom: 20px;
                border-top: 0;
            }
            
            #sf-tabbed-meta-boxes .handlediv, #sf-tabbed-meta-boxes .hndle {
            	display: none!important;
            }

            /*#sf-tabbed-meta-boxes > div.hide-if-js {
                   display: none!important;
            }*/
            #sf-meta-box-tabs {
                margin: 0;
                width: 20%;
                position: relative;
                z-index: 2;
                float: left;
                margin-right: -1px;
            }

            #sf-meta-box-tabs li {
                margin-bottom: -1px;
            }

            #sf-meta-box-tabs li.user-hidden {
                display: none !important;
            }
			
			#sf-meta-box-tabs li:first-child > a {
				border-top: 0;
			}
			
            #sf-meta-box-tabs li > a {
                display: block;
                background: #f7f7f7;
                padding: 12px;
                line-height: 150%;
                border: 1px solid #e5e5e5;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                text-decoration: none;
            }

            #sf-meta-box-tabs li > a:hover {
                color: #222;
                background: #fff;
            }

            #sf-meta-box-tabs li > a.active {
                border-right-color: #fff;
                background: #fff;
                box-shadow: none;
            }

            .closed #sf-meta-box-tabs, .closed #sf-tabbed-meta-boxes {
                display: none;
            }

            /* Events plugin fix */
            .wp-admin .rhc-extra-info-cell {
                display: block;
                width: auto;
            }
            </style>

        <?php
        }

        add_action( 'admin_head', 'sf_admin_css' );
    }
?>
