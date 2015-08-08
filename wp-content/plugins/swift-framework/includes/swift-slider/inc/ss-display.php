<?php
    /*
    *
    *	Swift Slider - Display
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    function swift_slider( $atts, $content = null ) {
        extract( shortcode_atts( array(
            "parallax"    => 'false',
            "type"        => 'slider',
            "fullscreen"  => 'false',
            "max_height"  => '',
            "slide_count" => '-1',
            "autoplay"    => '',
            "category"    => '',
            "transition"  => 'slide',
            "loop"        => 'yes',
            "nav"         => '1',
            "pagination"  => 'yes',
            "continue"    => 'true',
        ), $atts ) );

        /* GLOBAL VARIABLES
        ================================================== */
        global $post, $wp_query, $swift_slider_count;


        /* SLIDE CATEGORY
        ================================================== */
        if ( $category == "All" || $category == "all" ) {
            $category = "";
        }
        $slide_category = str_replace( '_', '-', $category );


        /* SLIDE QUERY
        ================================================== */
        $slide_count         = intval( $slide_count );
        $ss_args             = array(
            'post_type'             => 'swift-slider',
            'post_status'           => 'publish',
            'swift-slider-category' => $slide_category,
            'posts_per_page'        => $slide_count,
        );
        $swift_slider_slides = new WP_Query( $ss_args );
        $slides_count        = $swift_slider_slides->found_posts;
        if ( $slides_count < $slide_count ) {
            $slide_count = $slides_count;
        } else if ( $slide_count === 0 ) {
            $slide_count = $slides_count;
        }

        /* SLIDER VARIABLES
        ================================================== */
        $swift_slider_output = "";
        if ( ! $swift_slider_count ) {
            $swift_slider_count = 1;
        } else {
            $swift_slider_count ++;
        }
        $sliderID = 'swift-slider-' . $swift_slider_count;
        $slide_ID = 0;
        if ( $fullscreen == "yes" || $fullscreen == "1" ) {
            $fullscreen = "true";
        }
        if ( $loop == "yes" || $loop == "1" ) {
            $loop = "true";
        }

        if ( $slide_count <= 1 || $slides_count <= 1 ) {
            $loop = "false";
        }


        ?>

        <?php if ( $swift_slider_slides->have_posts() ) {

	        $swift_slider_output .= '<div class="swift-slider-outer">';
            $swift_slider_output .= '<div id="' . $sliderID . '" class="swift-slider swiper-container" data-type="' . $type . '" data-fullscreen="' . $fullscreen . '" data-max-height="' . $max_height . '" data-transition="' . $transition . '" data-loop="' . $loop . '" data-slide-count="' . $slide_count . '" data-autoplay="' . $autoplay . '" data-continue="' . $continue . '">';
            $swift_slider_output .= '<div class="swiper-wrapper">';

            while ( $swift_slider_slides->have_posts() ) : $swift_slider_slides->the_post();

                // Increase Slide ID
                $slide_ID ++;

                // Slide variables
                $bg_image_url  = $background_valign = $bg_mp4_id = $bg_webm_id = $bg_ogg_id = $bg_mp4_url = $bg_webm_url = $bg_ogg_url = "";
                $bg_video_size = array();

                // Get the meta values
                $slide_title       = get_the_title();
                $bg_type           = sf_get_post_meta( $post->ID, 'ss_bg_type', true );
                $bg_color          = sf_get_post_meta( $post->ID, 'ss_bg_color', true );
                $bg_opacity        = sf_get_post_meta( $post->ID, 'ss_bg_opacity', true );
                $bg_image          = rwmb_meta( 'ss_background_image', 'type=image&size=full' );
                $background_valign = sf_get_post_meta( $post->ID, 'ss_background_valign', true );
                $bg_mp4            = rwmb_meta( 'ss_background_video_mp4', 'type=file' );
                $bg_webm           = rwmb_meta( 'ss_background_video_webm', 'type=file' );
                $bg_ogg            = rwmb_meta( 'ss_background_video_ogg', 'type=file' );
                $video_loop        = sf_get_post_meta( $post->ID, 'ss_video_loop', true );
                $video_mute        = sf_get_post_meta( $post->ID, 'ss_video_mute', true );
                $video_overlay     = sf_get_post_meta( $post->ID, 'ss_video_overlay', true );
                $slide_style       = sf_get_post_meta( $post->ID, 'ss_slide_style', true );
                $caption_title     = sf_get_post_meta( $post->ID, 'ss_caption_title', true );
                $caption_text      = sf_content_filter( sf_get_post_meta( $post->ID, 'ss_caption_text', true ) );
                $caption_x         = sf_get_post_meta( $post->ID, 'ss_caption_x', true );
                $caption_y         = sf_get_post_meta( $post->ID, 'ss_caption_y', true );
                $caption_size      = sf_get_post_meta( $post->ID, 'ss_caption_size', true );
                $slide_link        = sf_get_post_meta( $post->ID, 'ss_slide_link', true );
                $slide_link_url    = sf_get_post_meta( $post->ID, 'ss_slide_link_url', true );

                // Background Image
                foreach ( $bg_image as $image ) {
                    $bg_image_url = $image['url'];
                    break;
                }

                // Background Image V-Align Default
                if ( $background_valign == "" ) {
                    $background_valign = "center";
                }

                // MP4
                foreach ( $bg_mp4 as $video ) {
                    $bg_mp4_id  = $video['ID'];
                    $bg_mp4_url = $video['url'];
                    break;
                }
                if ( $bg_mp4_url == "" ) {
                    $bg_mp4_url = sf_get_post_meta( $post->ID, 'ss_background_video_mp4_url', true );
                }

                // WEBM
                foreach ( $bg_webm as $video ) {
                    $bg_webm_id  = $video['ID'];
                    $bg_webm_url = $video['url'];
                    break;
                }
                if ( $bg_webm_url == "" ) {
                    $bg_webm_url = sf_get_post_meta( $post->ID, 'ss_background_video_webm_url', true );
                }

                // OGG
                foreach ( $bg_ogg as $video ) {
                    $bg_ogg_id  = $video['ID'];
                    $bg_ogg_url = $video['url'];
                    break;
                }
                if ( $bg_ogg_url == "" ) {
                    $bg_ogg_url = sf_get_post_meta( $post->ID, 'ss_background_video_ogg_url', true );
                }

                // Get video size from .mp4 or .webm file
                if ( $bg_mp4_id != "" ) {
                    $bg_video_size = wp_get_attachment_metadata( $bg_mp4_id );
                } else if ( $bg_webm_id != "" ) {
                    $bg_video_size = wp_get_attachment_metadata( $bg_webm_id );
                } else if ( $bg_ogg_id != "" ) {
                    $bg_video_size = wp_get_attachment_metadata( $bg_ogg_id );
                }

                // Set video height/width if not set
                if ( empty( $bg_video_size ) ) {
                    $bg_video_size['width']  = 0;
                    $bg_video_size['height'] = 0;
                }

                if ( $bg_image_url != "" ) {

                    $swift_slider_output .= '<div class="swiper-slide ' . $bg_type . '-slide" data-slide-id="' . $slide_ID . '" data-slide-title="' . $slide_title . '" style="background-image: url(' . $bg_image_url . ');background-color: ' . $bg_color . '" data-bg-align="' . $background_valign . '" data-slide-img="' . $bg_image_url . '" data-style="' . $slide_style . '">';

                    if ( ! empty( $bg_opacity ) && $bg_opacity > 0 ) {
                        $bg_opacity_d = $bg_opacity / 100; // Decimal value
                        $bg_c         = sf_hex2rgb( $bg_color ); // RGB array
                        $bg_c         = $bg_c['red'] . "," . $bg_c['green'] . "," . $bg_c['blue']; // rgba string
                        $swift_slider_output .= '<div class="overlay" style="-ms-filter: \'progid:DXImageTransform.Microsoft.Alpha(Opacity=' . $bg_opacity . ')\';filter: alpha(opacity=' . $bg_opacity . ');-moz-opacity: ' . $bg_opacity_d . ';-khtml-opacity: ' . $bg_opacity_d . ';opacity: ' . $bg_opacity_d . ';background-color: ' . $bg_color . ';"></div>';
                    }

                } else {

                    $swift_slider_output .= '<div class="swiper-slide ' . $bg_type . '-slide" data-slide-id="' . $slide_ID . '" data-slide-title="' . $slide_title . '" style="background-color: ' . $bg_color . '" data-style="' . $slide_style . '">';

                }

                if ( $caption_title != "" || $caption_text != "" ) {

                    $swift_slider_output .= '<div class="caption-wrap container">';
                    $swift_slider_output .= '<div class="caption-content" data-caption-color="" data-caption-x="' . $caption_x . '" data-caption-y="' . $caption_y . '" data-caption-size="' . $caption_size . '">';
                    if ( $caption_title != "" ) {
                        $swift_slider_output .= '<h2 class="caption-title">' . $caption_title . '</h2>';
                    }
                    if ( $caption_text != "" ) {
                        $swift_slider_output .= '<div class="caption-excerpt">';
                        $swift_slider_output .= do_shortcode( $caption_text );
                        $swift_slider_output .= '</div>';
                    }
                    $swift_slider_output .= '</div>';                
                    if ( $slide_link == "link-nw" ) {
                        $swift_slider_output .= '<a href="'.$slide_link_url.'" target="_blank" class="ss-slide-link"></a>';
                    } else if ( $slide_link == "link-sw" ) {
                        $swift_slider_output .= '<a href="'.$slide_link_url.'" target="_self" class="ss-slide-link"></a>';
                    }
                    $swift_slider_output .= '</div>';

                } else {                    
                    if ( $slide_link == "link-nw" ) {
                        $swift_slider_output .= '<a href="'.$slide_link_url.'" target="_blank" class="ss-slide-link"></a>';
                    } else if ( $slide_link == "link-sw" ) {
                        $swift_slider_output .= '<a href="'.$slide_link_url.'" target="_self" class="ss-slide-link"></a>';
                    }
                }

                if ( $bg_type == "video" ) {
                    $swift_slider_output .= '<div class="video-wrap">';
                    if ( $video_overlay != "none" ) {
                        $swift_slider_output .= '<div class="video-overlay overlay-' . $video_overlay . '"></div>';
                    }
                    $swift_slider_output .= '<video class="video" poster="' . $bg_image_url . '" autoplay ' . $video_loop . ' ' . $video_mute . ' preload="auto" data-width="' . $bg_video_size['width'] . '" data-height="' . $bg_video_size['height'] . '">';

                    if ( $bg_webm_url != "" ) {
                        $swift_slider_output .= '<source src="' . $bg_webm_url . '" type="video/webm">';
                    }

                    if ( $bg_mp4_url != "" ) {
                        $swift_slider_output .= '<source src="' . $bg_mp4_url . '" type="video/mp4">';
                    }

                    if ( $bg_ogg_url != "" ) {
                        $swift_slider_output .= '<source src="' . $bg_ogg_url . '" type="video/ogg">';
                    }

                    $swift_slider_output .= '</video>';
                    $swift_slider_output .= '</div>';
                }

                $swift_slider_output .= '</div><!-- .swiper-slide -->';

            endwhile;

            // Reset Query & Postdata
            wp_reset_query();
            wp_reset_postdata();

            $swift_slider_output .= '</div><!-- .swiper-wrapper -->';

            if ( $type == "slider" && $slide_count > 1 && ( $nav == "1" || $nav == "yes" || $nav == "true" ) ) {
            	$ss_prev_icon = apply_filters('swift_slider_prev_icon', '<i class="ss-navigateleft"></i>');
            	$ss_next_icon = apply_filters('swift_slider_next_icon', '<i class="ss-navigateright"></i>');
                $swift_slider_output .= '<a class="swift-slider-prev" href="#">'.$ss_prev_icon.'<h4>' . __( 'Previous', 'swift-framework-plugin' ) . '</h4></a>';
                $swift_slider_output .= '<a class="swift-slider-next" href="#">'.$ss_next_icon.'<h4>' . __( 'Next', 'swift-framework-plugin' ) . '</h4></a>';
            }

            if ( $slide_count > 1 && ( $pagination == "1" || $pagination == "yes" || $pagination == "true" ) ) {
                $swift_slider_output .= '<div class="swift-slider-pagination">';
            } else {
                $swift_slider_output .= '<div class="swift-slider-pagination pagination-hidden">';
            }
            for ( $i = 0; $i < $slide_count; $i ++ ) {
                $swift_slider_output .= '<div class="dot"><span class=""></span></div>';
            }
            $swift_slider_output .= '</div>';

            if ( $continue == "true" || $continue == 1 ) {
                $swift_slider_output .= '<div class="swift-scroll-indicator">';
                $swift_slider_output .= '<span></span><span></span><span></span>';
                $swift_slider_output .= '</div>';
                $ss_continue_icon = apply_filters('swift_slider_continue_icon', '<i class="ss-navigatedown"></i>');
                $swift_slider_output .= '<a href="#" class="swift-slider-continue continue-hidden">'.$ss_continue_icon.'</a>';
            }

            // LOADER
            $swift_slider_output .= sf_loading_animation( 'swift-slider-loader' );

			// FULLSCREEN
            if ( $fullscreen == "true" ) {
                $swift_slider_output .= "<script>jQuery(document).ready(function() {
					var windowHeight = parseInt(jQuery(window).height(), 10);

					if (jQuery('#wpadminbar').length > 0) {
						windowHeight = windowHeight - jQuery('#wpadminbar').height();
					}
					jQuery('#" . $sliderID . "').css('height', windowHeight);
				});</script>";
            }

            $swift_slider_output .= '</div><!-- .swift-slider -->';
            $swift_slider_output .= '</div><!-- .swift-slider-outer -->';

        } else {

            $swift_slider_output .= '<div id="' . $sliderID . '" class="swift-slider no-slides">';
            $swift_slider_output .= __( "No slides found, please add some!", 'swift-framework-plugin' );
            $swift_slider_output .= '</div>';

        }

        // Ouptut
        return $swift_slider_output;

    }

    add_shortcode( "swift_slider", "swift_slider" );
?>
