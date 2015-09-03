<?php

    /*
    *
    *	Swift Page Builder - Post Format Output Functions
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    *	sf_get_post_media()
    *	sf_get_post_format_image_src()
    *	sf_image_post()
    *	sf_video_post()
    *	sf_gallery_post()
    *	sf_audio_post()
    *	sf_link_post()
    *	sf_chat_post()
    *	sf_get_post_item()
    *	sf_get_search_item()
    *	sf_get_campaign_item()
    *
    */


    /* MAIN GET MEDIA FUNCTION
    ================================================== */
    if ( ! function_exists( 'sf_get_post_media' ) ) {
        function sf_get_post_media( $postID, $media_width, $media_height, $video_height, $use_thumb_content ) {

            $format     = get_post_format( $postID );
            $post_media = "";

            if ( $format == "image" ) {
                $post_media = sf_image_post( $postID, $media_width, $media_height, $use_thumb_content );
            } else if ( $format == "video" ) {
                $post_media = sf_video_post( $postID, $media_width, $video_height, $use_thumb_content );
            } else if ( $format == "gallery" ) {
                $post_media = sf_gallery_post( $postID, $use_thumb_content );
            } else if ( $format == "audio" ) {
                $post_media = sf_audio_post( $postID );
            } else if ( $format == "link" ) {
                $post_media = sf_link_post( $postID );
            } else if ( $format == "chat" ) {
                $post_media = sf_chat_post( $postID );
            }

            return $post_media;

        }
    }


    /* GET IMAGE MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_get_post_format_image_src' ) ) {
        function sf_get_post_format_image_src( $post_id ) {
            $format_meta = get_post_format_meta( $post_id );
            $match       = array();
            if ( $format_meta['image'] != "" ) {
                preg_match( '/<img.*?src="([^"]+)"/s', $format_meta['image'], $match );

                return $match[1];
            }
        }
    }

    if ( ! function_exists( 'sf_image_post' ) ) {
        function sf_image_post( $postID, $media_width, $media_height, $use_thumb_content, $return_url = false ) {

            $image = $media_image_url = $image_id = "";

            if ( $use_thumb_content ) {
                $media_image = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full', $postID );
            } else {
                $media_image = rwmb_meta( 'sf_detail_image', 'type=image&size=full', $postID );
            }

            foreach ( $media_image as $detail_image ) {
                $image_id        = $detail_image['ID'];
                $media_image_url = $detail_image['url'];
                break;
            }

            if ( ! $media_image ) {
                $media_image     = get_post_thumbnail_id();
                $image_id        = $media_image;
                $media_image_url = wp_get_attachment_url( $media_image, 'full' );
            }

            $detail_image = sf_aq_resize( $media_image_url, $media_width, $media_height, true, false );
                        
            $image_meta 		= sf_get_attachment_meta( $image_id );
            $image_caption = $image_alt = $image_title = $caption_html = "";
            if ( isset($image_meta) ) {
            	$image_caption 		= esc_attr( $image_meta['caption'] );
            	$image_title 		= esc_attr( $image_meta['title'] );
            	$image_alt 			= esc_attr( $image_meta['alt'] );
            }
            
            if ( $detail_image ) {
                $image = '<img itemprop="image" src="' . $detail_image[0] . '" width="' . $detail_image[1] . '" height="' . $detail_image[2] . '" alt="' . $image_alt . '" />';
            }

            if ( $return_url && $detail_image ) {
                return $detail_image[0];
            } else {
                return $image;
            }

        }
    }


    /* GET VIDEO MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_video_post' ) ) {
        function sf_video_post( $postID, $media_width, $video_height, $use_thumb_content ) {

            $video = $media_video = "";

            if ( $use_thumb_content ) {
                $media_video = sf_get_post_meta( $postID, 'sf_thumbnail_video_url', true );
            } else {
                $media_video = sf_get_post_meta( $postID, 'sf_detail_video_url', true );
            }

            $video = sf_video_embed( $media_video, $media_width, $video_height );

            return $video;
        }
    }


    /* GET GALLERY MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_gallery_post' ) ) {
        function sf_gallery_post( $postID, $use_thumb_content ) {

            $gallery = '<div class="flexslider item-slider">' . "\n";
            $gallery .= '<ul class="slides">' . "\n";

            if ( $use_thumb_content ) {
                $media_gallery = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=full-width-image-gallery', $postID );
            } else {
                $media_gallery = rwmb_meta( 'sf_detail_gallery', 'type=image&size=full-width-image-gallery', $postID );
            }

            foreach ( $media_gallery as $image ) {
                $gallery .= "<li><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' /></li>";
            }

            $gallery .= '</ul>' . "\n";
            $gallery .= '</div>' . "\n";

            return $gallery;
        }
    }


    /* GET STACKED GALLERY MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_gallery_stacked_post' ) ) {
        function sf_gallery_stacked_post( $postID, $use_thumb_content ) {

            $media_gallery = rwmb_meta( 'sf_detail_gallery', 'type=image&size=full-width-image-gallery', $postID );

            $gallery_stacked = '' . "\n";

            foreach ( $media_gallery as $image ) {
                $gallery_stacked .= "<img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' />";
            }

            return $gallery_stacked;
        }
    }


    /* GET AUDIO MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_audio_post' ) ) {
        function sf_audio_post( $postID, $use_thumb_content ) {
            $media_audio = "";
            if ( $use_thumb_content ) {
                $media_audio = sf_get_post_meta( $postID, 'sf_thumbnail_audio_url', true );
            } else {
                $media_audio = sf_get_post_meta( $postID, 'sf_detail_audio_url', true );
            }

            $audio = do_shortcode( '[audio src="' . $media_audio . '"]' );

            return $audio;
        }
    }


    /* GET SELF HOSTED VIDEO MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_sh_video_post' ) ) {
        function sf_sh_video_post( $postID, $video_width = null, $video_height = null, $use_thumb_content = false ) {
            $media_mp4 = $media_ogg = $media_webm = "";
            $poster    = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'large', true );
            if ( isset( $poster ) & $poster != "" ) {
                $poster = 'poster="' . $poster[0] . '"';
            }

            if ( $use_thumb_content ) {
                $media_mp4  = sf_get_post_meta( $postID, 'sf_thumbnail_video_mp4', true );
                $media_ogg  = sf_get_post_meta( $postID, 'sf_thumbnail_video_ogg', true );
                $media_webm = sf_get_post_meta( $postID, 'sf_thumbnail_video_webm', true );
            } else {
                $media_mp4  = sf_get_post_meta( $postID, 'sf_detail_video_mp4', true );
                $media_ogg  = sf_get_post_meta( $postID, 'sf_detail_video_ogg', true );
                $media_webm = sf_get_post_meta( $postID, 'sf_detail_video_webm', true );
            }

            $video = '<div class="video-thumb">';
            $video .= '<video preload="auto" width="' . $video_width . '" height="' . $video_height . '" ' . $poster . ' controls>';
            if ( $media_webm != "" ) {
                $video .= '<source src="' . $media_webm . '" type="video/webm">';
            }
            if ( $media_mp4 != "" ) {
                $video .= '<source src="' . $media_mp4 . '" type="video/mp4">';
            }
            if ( $media_ogg != "" ) {
                $video .= '<source src="' . $media_ogg . '" type="video/ogv">';
            }
            $video .= '</video>';
            $video .= '</div>';

            return $video;
        }
    }


    /* GET LINK MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_link_post' ) ) {
        function sf_link_post( $postID ) {

            $link = "";
            $link_icon = apply_filters( 'sf_link_icon', '<i class="ss-link"></i>' );

            if ( function_exists( 'get_the_post_format_url' ) ) {
                $link = get_the_post_format_url();
                $link = '<a href="' . esc_url( $link ) . '" target="_blank" class="link-post-link">'. $link_icon . $link . '</a>';
            }

            return $link;
        }
    }


    /* GET CHAT MEDIA
    ================================================== */
    if ( ! function_exists( 'sf_chat_post' ) ) {
        function sf_chat_post( $postID ) {

            $chat = "";

            if ( function_exists( 'get_the_post_format_chat' ) ) {

                $chat    = '<dl class="chat">';
                $stanzas = get_the_post_format_chat();

                foreach ( $stanzas as $stanza ) {
                    foreach ( $stanza as $row ) {
                        $time = '';
                        if ( ! empty( $row['time'] ) ) {
                            $time = sprintf( '<time class="chat-timestamp">%s</time>', esc_html( $row['time'] ) );
                        }

                        $chat .= sprintf(
                            '<dt class="chat-author chat-author-%1$s vcard">%2$s <cite class="fn">%3$s</cite>: </dt>
								<dd class="chat-text">%4$s</dd>
							',
                            esc_attr( sanitize_title_with_dashes( $row['author'] ) ), // Slug.
                            $time,
                            esc_html( $row['author'] ),
                            $row['message']
                        );
                    }
                }

                $chat .= '</dl><!-- .chat -->';

            }

            return $chat;
        }
    }

    /* GET POST ITEM
    ================================================== */
    if ( ! function_exists( 'sf_get_post_item' ) ) {
        function sf_get_post_item( $postID, $blog_type, $show_title = "yes", $show_excerpt = "yes", $show_details = "yes", $excerpt_length = "20", $content_output = "excerpt", $show_read_more = "yes", $fullwidth = "no" ) {
				
			$post_item = sf_get_content_view( 'post', $blog_type, true );

            return $post_item;
        }
    }


    /* POST THUMBNAIL
    ================================================== */
    if ( ! function_exists( 'sf_post_thumbnail' ) ) {
        function sf_post_thumbnail( $blog_type = "", $fullwidth = "no" ) {

            global $post, $sf_sidebar_config;

            $thumb_width = $thumb_height = $video_height = $gallery_size = $item_figure = '';

            if ( $blog_type == "mini" ) {
                if ( $sf_sidebar_config == "no-sidebars" ) {
                    $thumb_width  = 446;
                    $thumb_height = null;
                    $video_height = 335;
                } else {
                    $thumb_width  = 370;
                    $thumb_height = null;
                    $video_height = 260;
                }
                $gallery_size = 'thumb-image';
            } else if ( $blog_type == "masonry" ) {
                if ( $sf_sidebar_config == "both-sidebars" || $fullwidth == "yes" ) {
                    $item_class = "col-sm-3";
                } else {
                    $item_class = "col-sm-4";
                }
                $thumb_width  = 480;
                $thumb_height = null;
                $video_height = 360;
                $gallery_size = 'thumb-image';
            } else {
                $thumb_width  = 970;
                $thumb_height = null;
                $video_height = 728;
                $gallery_size = 'blog-image';
            }


            $thumb_type               = sf_get_post_meta( $post->ID, 'sf_thumbnail_type', true );
            $thumb_image              = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
            $thumb_video              = sf_get_post_meta( $post->ID, 'sf_thumbnail_video_url', true );
            $thumb_gallery            = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=' . $gallery_size );
            $thumb_link_type          = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_type', true );
            $thumb_link_url           = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_url', true );
            $thumb_lightbox_thumb     = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=large' );
            $thumb_lightbox_image     = rwmb_meta( 'sf_thumbnail_link_image', 'type=image&size=large' );
            $thumb_lightbox_video_url = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_video_url', true );
            $thumb_lightbox_video_url = sf_get_embed_src( $thumb_lightbox_video_url );
            $image_id                 = 0;
            $item_link                = sf_post_item_link();

            foreach ( $thumb_image as $detail_image ) {
                $image_id      = $detail_image['ID'];
                $thumb_img_url = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image   = get_post_thumbnail_id();
                $image_id      = $thumb_image;
                $thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
            }

            if ( $thumb_type == "" ) {
            	$thumb_type = "none";
            }

            $item_figure .= '<figure class="animated-overlay overlay-style thumb-media-' . $thumb_type . '">';

            if ( $thumb_type == "video" ) {

                $video = sf_video_embed( $thumb_video, $thumb_width, $video_height );

                $item_figure .= $video;

            } else if ( $thumb_type == "audio" ) {

                $image        = sf_aq_resize( $thumb_img_url, $thumb_width, $thumb_height, true, false );
                $thumbnail_id = get_post_thumbnail_id( $post->ID );
                $image_alt    = sf_get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                if ( $image ) {
                    $item_figure .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" />';
                }

                $item_figure .= sf_audio_post( $post->ID, true );

            } else if ( $thumb_type == "sh-video" ) {

                $item_figure .= sf_sh_video_post( $post->ID, $thumb_width, $video_height, true );

            } else if ( $thumb_type == "slider" ) {

                $item_figure .= '<div class="flexslider thumb-slider"><ul class="slides">';

                foreach ( $thumb_gallery as $image ) {
                    $item_figure .= "<li><a " . $item_link['config'] . "><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' /></a></li>";
                }

                $item_figure .= '</ul></div>';

            } else {

                $thumb_img_url = apply_filters( 'sf_post_thumb_image_url', $thumb_img_url );

                if ( $thumb_type == "image" && $thumb_img_url == "" ) {
                    $thumb_img_url = "default";
                }

                $image        = sf_aq_resize( $thumb_img_url, $thumb_width, $thumb_height, true, false );
                $thumbnail_id = get_post_thumbnail_id( $post->ID );
                $image_alt    = sf_get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                if ( $thumb_img_url != "" ) {
                    if ( $image ) {
                        $item_figure .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_alt . '" />';
                    } else {
                        //$item_figure .= '<img itemprop="image" src="'.$thumb_img_url.'" alt="'.$image_alt.'" />';
                    }
                    $item_figure .= '<a ' . $item_link['config'] . '></a>';
                    $item_figure .= '<div class="figcaption-wrap"></div>';
                    $item_figure .= '<figcaption><div class="thumb-info thumb-info-alt">';
                    $item_figure .= '<i class="' . $item_link['icon'] . '"></i>';
                    $item_figure .= '</div></figcaption>';
                }
            }

            $item_figure .= '</figure>';

            return $item_figure;
        }
    }


    /* POST LINK CONFIG
    ================================================== */
    if ( ! function_exists( 'sf_post_item_link' ) ) {
        function sf_post_item_link() {

            $link_config = $item_icon = $thumb_img_url = "";

            global $post;

            $thumb_image              = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
            $thumb_link_type          = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_type', true );
            $thumb_link_url           = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_url', true );
            $thumb_lightbox_image     = rwmb_meta( 'sf_thumbnail_link_image', 'type=image&size=large' );
            $thumb_lightbox_video_url = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_video_url', true );
            $thumb_lightbox_video_url = sf_get_embed_src( $thumb_lightbox_video_url );

            $permalink = get_permalink();

            foreach ( $thumb_image as $detail_image ) {
                $thumb_img_url = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image   = get_post_thumbnail_id();
                $thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
            }


            if ( $thumb_link_type == "link_to_url" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url"';
                $item_icon   = apply_filters( 'sf_post_link_icon', "ss-link" );
            } else if ( $thumb_link_type == "link_to_url_nw" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url" target="_blank"';
                $item_icon   = apply_filters( 'sf_post_link_icon', "ss-link" );
            } else if ( $thumb_link_type == "lightbox_thumb" ) {
            	$lightbox_id = rand();
                if ( $thumb_img_url != "" ) {
                    $link_config = 'href="' . $thumb_img_url . '" class="lightbox" data-rel="ilightbox['.$lightbox_id.']"';
                }
                $item_icon   = apply_filters( 'sf_post_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_image" ) {
                $lightbox_image_url = '';
                foreach ( $thumb_lightbox_image as $image ) {
                    $lightbox_image_url = $image['full_url'];
                }
                $lightbox_id = rand();
                if ( $lightbox_image_url != "" ) {
                    $link_config = 'href="' . $lightbox_image_url . '" class="lightbox" data-rel="ilightbox['.$lightbox_id.']"';
                }
                $item_icon   = apply_filters( 'sf_post_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_video" ) {
                $link_config = 'data-video="' . $thumb_lightbox_video_url . '" href="#" class="fw-video-link"';
                $item_icon   = apply_filters( 'sf_post_video_icon', "ss-video" );
            } else {
                $link_config = 'href="' . $permalink . '" class="link-to-post"';
                $item_icon   = apply_filters( 'sf_post_standard_icon', "ss-navigateright" );
            }

            $item_link = array(
                "icon"   => $item_icon,
                "config" => $link_config
            );

            return $item_link;
        }
    }


    /* GET RECENT POST ITEM
    ================================================== */
    if ( ! function_exists( 'sf_get_recent_post_item' ) ) {
        function sf_get_recent_post_item( $post, $display_type = "bold", $excerpt_length = 20, $item_class = "" ) {

            $recent_post   = $recent_post_figure = $link_config = $item_icon = "";
            $thumb_type    = sf_get_post_meta( $post->ID, 'sf_thumbnail_type', true );
            $thumb_image   = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=full' );
            $thumb_video   = sf_get_post_meta( $post->ID, 'sf_thumbnail_video_url', true );
            $thumb_gallery = rwmb_meta( 'sf_thumbnail_gallery', 'type=image&size=thumb-image' );

            foreach ( $thumb_image as $detail_image ) {
                $thumb_img_url = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image   = get_post_thumbnail_id();
                $thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
            }

            // POST META
            global $sf_options;
            $single_author = $sf_options['single_author'];
            $remove_dates  = $sf_options['remove_dates'];
            $post_links_match_thumb = false;
            if ( isset( $sf_options['post_links_match_thumb'] ) ) {
            	$post_links_match_thumb = $sf_options['post_links_match_thumb'];	
            }

            $post_author    = get_the_author_link();
            $post_date      = get_the_date();
            $post_date_str  = get_the_date('Y-m-d');
            $item_title     = get_the_title();
            $post_permalink = get_permalink();
            $post_comments  = get_comments_number();
            $custom_excerpt = sf_get_post_meta( $post->ID, 'sf_custom_excerpt', true );
            $post_excerpt   = '';
            if ( $custom_excerpt != '' ) {
                $post_excerpt = sf_custom_excerpt( $custom_excerpt, $excerpt_length );
            } else {
                $post_excerpt = sf_excerpt( $excerpt_length );
            }
            $post_permalink_config = 'href="' . $post_permalink . '" class="link-to-post"';
            if ( $post_links_match_thumb ) {
            	$link_config = sf_post_item_link();
            	$post_permalink_config = $link_config['config'];
            }

            $thumb_width = apply_filters('sf_recent_post_item_thumb_width', 360);
            $thumb_height = apply_filters('sf_recent_post_item_thumb_height', 270);
			
			if ( $display_type == "standard-row" ) {
				$thumb_width = apply_filters('sf_recent_post_item_thumb_width', 400);
				$thumb_height = apply_filters('sf_recent_post_item_thumb_height', 300);
			}
			
            // MEDIA CONFIG
            $thumb_link_type          = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_type', true );
            $thumb_link_url           = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_url', true );
            $thumb_lightbox_thumb     = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=large' );
            $thumb_lightbox_image     = rwmb_meta( 'sf_thumbnail_link_image', 'type=image&size=large' );
            $thumb_lightbox_video_url = sf_get_post_meta( $post->ID, 'sf_thumbnail_link_video_url', true );
            $thumb_lightbox_video_url = sf_get_embed_src( $thumb_lightbox_video_url );
            $thumb_lightbox_img_url   = wp_get_attachment_url( $thumb_lightbox_image, 'full' );


            // LINK CONFIG
            if ( $thumb_link_type == "link_to_url" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url"';
                $item_icon   = apply_filters( 'sf_post_link_icon', "ss-link" );
            } else if ( $thumb_link_type == "link_to_url_nw" ) {
                $link_config = 'href="' . $thumb_link_url . '" class="link-to-url" target="_blank"';
                $item_icon   = apply_filters( 'sf_post_link_icon', "ss-link" );
            } else if ( $thumb_link_type == "lightbox_thumb" ) {
            	$lightbox_id = rand();
                if ( $thumb_img_url != "" ) {
                    $link_config = 'href="' . $thumb_img_url . '" class="lightbox" data-rel="ilightbox['.$lightbox_id.']"';
                }
                $item_icon   = apply_filters( 'sf_post_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_image" ) {
                $lightbox_image_url = '';
                foreach ( $thumb_lightbox_image as $image ) {
                    $lightbox_image_url = $image['full_url'];
                }
                $lightbox_id = rand();
                if ( $lightbox_image_url != "" ) {
                    $link_config = 'href="' . $lightbox_image_url . '" class="lightbox" data-rel="ilightbox['.$lightbox_id.']"';
                }
                $item_icon   = apply_filters( 'sf_post_lightbox_icon', "ss-view" );
            } else if ( $thumb_link_type == "lightbox_video" ) {
                $link_config = 'data-video="' . $thumb_lightbox_video_url . '" href="#" class="fw-video-link"';
                $item_icon   = apply_filters( 'sf_post_video_icon', "ss-video" );
            } else {
                $link_config = 'href="' . $post_permalink . '" class="link-to-post"';
                $item_icon   = apply_filters( 'sf_post_standard_icon', "ss-navigateright" );
            }


            if ( $thumb_type == "none" ) {
                $recent_post .= '<div itemscope class="recent-post no-thumb ' . $item_class . ' clearfix">';
            } else {
                $recent_post .= '<div itemscope class="recent-post has-thumb ' . $item_class . ' clearfix">';
            }

			$recent_post_figure .= '<div class="figure-wrap">';

            $recent_post_figure .= apply_filters( 'sf_before_recent_post_thumb' , '');

            $recent_post_figure .= '<figure class="animated-overlay overlay-alt">';

            if ( $thumb_type == "video" ) {

                $video = sf_video_embed( $thumb_video, 400, 225 );
                $recent_post_figure .= '<div class="video-thumb">' . $video . '</div>';

            } else if ( $thumb_type == "slider" ) {

                $recent_post_figure .= '<div class="flexslider thumb-slider"><ul class="slides">';

                foreach ( $thumb_gallery as $image ) {
                    $alt = $image['alt'];
                    if ( ! $alt ) {
                        $alt = $image['title'];
                    }
                    $recent_post_figure .= "<li><a " . $link_config . "><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$alt}' /></a></li>";
                }

                $recent_post_figure .= '</ul></div>';

            } else {

                if ( $thumb_img_url == "" && $thumb_type != "none" ) {
                    $thumb_img_url = "default";
                }

                $image = sf_aq_resize( $thumb_img_url, $thumb_width, $thumb_height, true, false );

                if ( $image ) {
                    $recent_post_figure .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $item_title . '" />';
                    $recent_post_figure .= '<a ' . $link_config . '></a>';
                    $recent_post_figure .= '<div class="figcaption-wrap"></div>';
                    $recent_post_figure .= '<figcaption><div class="thumb-info thumb-info-alt">';
                    $recent_post_figure .= '<i class="' . $item_icon . '"></i>';
                    $recent_post_figure .= '</div></figcaption>';
                }
            }

            $recent_post_figure .= '</figure>';

            $recent_post_figure .= '</div>';

            if ( $display_type == "bold" ) {

                $recent_post .= $recent_post_figure;
                $recent_post .= '<div class="details-wrap">';
                if ( $thumb_type == "none" ) {
                    $recent_post .= '<h2><a ' . $post_permalink_config . '>' . $item_title . '</a></h2>';
                } else {
                    $recent_post .= '<h3><a ' . $post_permalink_config . '>' . $item_title . '</a></h3>';
                }
                $recent_post .= sf_get_post_details($post->ID, true);
                $recent_post .= '</div>';

            } else if ( $display_type == "list" ) {

                $recent_post .= '<a class="list-post-link" href="' . $post_permalink . '"></a>';
                if ( $image ) {
                    $recent_post_figure .= '<figure class="animated-overlay">';
                    $recent_post_figure .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $item_title . '" />';
                    $recent_post_figure .= '<a ' . $link_config . '></a>';
                    $recent_post_figure .= '<div class="figcaption-wrap"></div>';
                    $recent_post_figure .= '<figcaption><div class="thumb-info thumb-info-alt">';
                    $recent_post_figure .= '<i class="' . $item_icon . '"></i>';
                    $recent_post_figure .= '</div></figcaption>';
                    $recent_post_figure .= '</figure>';
                }
                $recent_post .= '<div class="details-wrap">';
                $recent_post .= '<h4>' . $item_title . '</h4>';
                $recent_post .= '<div class="post-item-details">';
                $recent_post .= '<span class="post-date">' . $post_date . '</span>';
                $recent_post .= '</div>';
                $recent_post .= '</div>';

            } else if ( $display_type == "bright" ) {

                $recent_post .= '<div class="details-wrap">';
                $recent_post .= '<div class="author-avatar">' . get_avatar( get_the_author_meta( 'ID' ), '140' ) . '</div>';
                $recent_post .= '<h6 class="post-item-author"><span class="author">' . sprintf( '<a href="%2$s" rel="author" itemprop="author">%1$s</a>', $post_author, get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '</span></h6>';
                $recent_post .= '<h2><a ' . $post_permalink_config . '>' . $item_title . '</a></h2>';
                $recent_post .= '<div class="post-item-details">';
                $recent_post .= '<span class="post-date">' . $post_date . '</span>';
                $recent_post .= '</div>';
                $recent_post .= '</div>';

			} else if ( $display_type == "bold" ) {

                $recent_post .= $recent_post_figure;
                $recent_post .= '<div class="details-wrap">';
                if ( $thumb_type == "none" ) {
                    $recent_post .= '<h2><a ' . $post_permalink_config . '>' . $item_title . '</a></h2>';
                } else {
                    $recent_post .= '<h3><a ' . $post_permalink_config . '>' . $item_title . '</a></h3>';
                }
                $recent_post = sf_get_post_details($post->ID, true);
                $recent_post .= '</div>';

            } else {

                $recent_post .= $recent_post_figure;
                $recent_post .= '<div class="details-wrap">';
                $recent_post .= '<h5><a ' . $post_permalink_config . '>' . $item_title . '</a></h5>';
                $recent_post .= sf_get_post_details($post->ID, true);
                if ( $excerpt_length != "0" && $excerpt_length != "" ) {
                    $recent_post .= '<div class="excerpt">' . $post_excerpt . '</div>';
                }

                if ( sf_theme_opts_name() == "sf_atelier_options" && $display_type == "standard-row" ) {
                	$recent_post .= '<a class="read-more-button" href="' . get_permalink() . '">' . __( "Read more", "swiftframework" ) . '</a>';
                }

                $recent_post .= '</div>';

            }

            $recent_post .= '</div>';

            return $recent_post;
        }
    }

    /* GET DETAILS
    ================================================== */
    if ( ! function_exists( 'sf_get_post_details' ) ) {
        function sf_get_post_details( $postID ) {

        	global $sf_options;
        	$single_author = $sf_options['single_author'];
        	$remove_dates  = $sf_options['remove_dates'];

        	$post_author    = get_the_author_link();
        	$post_date      = get_the_date();
        	$post_date_str  = get_the_date('Y-m-d');
        	$post_details = "";

        	if ( $single_author && ! $remove_dates ) {
        	    $post_details .= '<div class="blog-item-details"><time class="post-date" datetime="' . $post_date_str . '">' . sprintf( __( '%1$s', 'swiftframework' ), $post_date ) . '</time></div>';
        	} else if ( ! $remove_dates ) {
        	    $post_details .= '<div class="post-item-details"><time class="post-date" datetime="' . $post_date_str . '">' . $post_date . '</time><span class="author"> ' . sprintf( __( 'by <a href="%2$s" rel="author" itemprop="author">%1$s</a>', 'swiftframework' ), $post_author, get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '</span></div>';
        	} else if ( ! $single_author ) {
        	    $post_details .= '<div class="post-item-details"><span class="author">' . sprintf( __( 'By <a href="%2$s" rel="author" itemprop="author">%1$s</a>', 'swiftframework' ), $post_author, get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '</span></div>';
        	}

        	return $post_details;
        }
    }

    /* GET SEARCH ITEM
    ================================================== */
    if ( ! function_exists( 'sf_get_search_item' ) ) {
        function sf_get_search_item( $postID ) {

            $search_item = $thumb_img_url = $post_excerpt = $img_icon = "";

            $post_format = get_post_format( $postID );
            if ( $post_format == "" ) {
                $post_format = 'standard';
            }
            $post_type = get_post_type( $postID );

            if ( $post_type == "post" ) {
                if ( $post_format == "quote" || $post_format == "status" ) {
                    $img_icon = "ss-quote";
                } else if ( $post_format == "image" ) {
                    $img_icon = "ss-picture";
                } else if ( $post_format == "chat" ) {
                    $img_icon = "ss-chat";
                } else if ( $post_format == "audio" ) {
                    $img_icon = "ss-music";
                } else if ( $post_format == "video" ) {
                    $img_icon = "ss-video";
                } else if ( $post_format == "link" ) {
                    $img_icon = "ss-link";
                } else {
                    $img_icon = "ss-pen";
                }
            } else if ( $post_type == "product" ) {
                $img_icon = "ss-cart";
            } else if ( $post_type == "portfolio" ) {
                $img_icon = "ss-picture";
            } else if ( $post_type == "team" ) {
                $img_icon = "ss-user";
            } else if ( $post_type == "galleries" ) {
                $img_icon = "ss-picture";
            } else {
                $img_icon = "ss-file";
            }

            $post_title     = get_the_title();
            $post_date      = get_the_date();
            $post_permalink = get_permalink();
            $custom_excerpt = sf_get_post_meta( $postID, 'sf_custom_excerpt', true );
            $post_excerpt   = strip_shortcodes( get_the_excerpt() );

            $thumb_image = rwmb_meta( 'sf_thumbnail_image', 'type=image&size=thumbnail' );

            foreach ( $thumb_image as $detail_image ) {
                $thumb_img_url = $detail_image['url'];
                break;
            }

            if ( ! $thumb_image ) {
                $thumb_image   = get_post_thumbnail_id();
                $thumb_img_url = wp_get_attachment_url( $thumb_image, 'thumbnail' );
            }

            $image       = sf_aq_resize( $thumb_img_url, 70, 70, true, false );
            $image_title = sf_featured_img_title();

            if ( $image ) {
                $search_item .= '<div class="search-item-img"><a href="' . $post_permalink . '"><img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_title . '" /></a></div>';
            } else {
                $search_item .= '<div class="search-item-img"><a href="' . $post_permalink . '" class="img-holder"><i class="' . $img_icon . '"></i></a></div>';
            }

            if ( $post_excerpt == "<p></p>" ) {
                $search_item .= '<div class="search-item-content no-excerpt">';
                $search_item .= '<h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3>';
                $search_item .= '<time>' . $post_date . '</time>';
                $search_item .= '</div>';
            } else {
                $search_item .= '<div class="search-item-content">';
                $search_item .= '<h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3>';
                $search_item .= '<time>' . $post_date . '</time>';
                $search_item .= '<div class="excerpt">' . $post_excerpt . '</div>';
                $search_item .= '</div>';
            }

            return $search_item;

        }
    }


    /* GET CAMPAIGN ITEM
    ================================================== */
    if ( ! function_exists( 'sf_get_campaign_item' ) ) {
        function sf_get_campaign_item( $item_class ) {
            global $post, $sf_has_progress_bar;
            $sf_has_progress_bar = true;

            $campaign_item = "";

            if ( class_exists( 'ATCF_Campaigns' ) ) {
                $campaign        = new ATCF_Campaign( $post->ID );
                $post_title      = get_the_title();
                $post_author     = get_the_author_link();
                $post_date       = get_the_date();
                $post_date_str   = get_the_date('Y-m-d');
                $post_categories = get_the_category_list( ', ' );
                $post_comments   = get_comments_number();
                $post_permalink  = get_permalink();
                $post_excerpt    = sf_excerpt( 20 );
                $percent         = $campaign->percent_completed();
                $percent_num     = str_replace( '%', '', $percent );

                $campaign_item .= '<li class="campaign-item ' . $item_class . '">';
                $campaign_item .= sf_post_thumbnail();
                $campaign_item .= '<div class="details-wrap">';
                $campaign_item .= '<h3 itemprop="name headline"><a href="' . $post_permalink . '">' . $post_title . '</a></h3>';
                $campaign_item .= '<div class="excerpt" itemprop="description">' . $post_excerpt . '</div>';
                $campaign_item .= '<div class="campaign-details-mini clearfix">';
                $campaign_item .= '<div class="funded-bar progress standard"><div class="bar" data-value="' . $percent_num . '"></div></div>';
                $campaign_item .= '<div class="detail">';
                $campaign_item .= '<data>' . $percent . '</data>';
                $campaign_item .= '<span>' . __( "Funded", "swiftframework" ) . '</span>';
                $campaign_item .= '</div>';
                $campaign_item .= '<div class="detail pledged">';
                $campaign_item .= '<data>' . $campaign->current_amount() . '</data>';
                $campaign_item .= '<span>' . __( "Pledged", "swiftframework" ) . '</span>';
                $campaign_item .= '</div>';
                if ( ! $campaign->is_endless() ) {
                    $campaign_item .= '<div class="detail">';
                    $campaign_item .= '<data>' . $campaign->days_remaining() . '</data>';
                    $campaign_item .= '<span>' . _n( "Day to Go", "Days to Go", $campaign->days_remaining(), "swiftframework" ) . '</span>';
                    $campaign_item .= '</div>';
                }
                $campaign_item .= '</div>';
                $campaign_item .= '</div>';
                $campaign_item .= '</li>';
            }

            return $campaign_item;

        }
    }

?>
