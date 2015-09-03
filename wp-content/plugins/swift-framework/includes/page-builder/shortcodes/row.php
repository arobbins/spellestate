<?php

    /*
    *
    *	Swift Page Builder - Row Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_row extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $row_el_class = $width = $row_bg_color = $row_top_style = $row_bottom_style = $row_padding_vertical = $row_padding_horizontal = $row_margin_vertical = $remove_element_spacing = $el_position = $animation_output = '';

            extract( shortcode_atts( array(
                'wrap_type'               => 'content-width',
                'row_bg_color'            => '',
                'color_row_height'		  => '',
                'inner_column_height'	  => '',
                'row_id'                  => '',
                'row_name'                => '',
                'row_top_style'			  => '',
                'row_bottom_style'		  => '',
                'row_padding_vertical'    => '',
                'row_padding_horizontal'  => '',
                'row_margin_vertical'     => '30',
                'row_overlay_opacity'     => '0',
                'remove_element_spacing'  => '',
                'vertical_center'         => 'true',
                'row_bg_type'             => '',
                'bg_image'                => '',
                'bg_video_mp4'            => '',
                'bg_video_webm'           => '',
                'bg_video_ogg'            => '',
                'bg_video_loop'           => 'yes',
                'parallax_video_height'   => 'window-height',
                'parallax_image_height'   => 'content-height',
                'parallax_video_overlay'  => 'none',
                'parallax_image_movement' => 'fixed',
                'parallax_image_speed'    => '0.5',
                'bg_type'                 => '',
                'row_expanding'			  => '',
                'row_expading_text_closed' => '',
                'row_expading_text_open'  => '',
                'row_animation'        	  => '',
                'row_animation_delay'  	  => '',
                'responsive_vis'          => '',
                'row_responsive_vis'      => '',
                'row_el_class'            => '',
                'el_position'             => '',
                'width'                   => '1/1'
            ), $atts ) );

            $output = $inline_style = $inner_inline_style = $rowId = '';

            if ( $row_id != "" ) {
                $rowId = 'id="' . $row_id . '" data-rowname="' . $row_name . '"';
            }

            if ($row_responsive_vis == "" && $responsive_vis != "") {
            	$row_responsive_vis = $responsive_vis;
            }

            $responsive_vis = str_replace( "_", " ", $row_responsive_vis );
            $row_el_class   = $this->getExtraClass( $row_el_class ) . ' ' . $responsive_vis;
            $orig_width     = $width;
            $width          = spb_translateColumnWidthToSpan( $width );
            $img_url        = wp_get_attachment_image_src( $bg_image, 'full' );

            if ( $remove_element_spacing == "yes" ) {
                $row_el_class .= ' remove-element-spacing';
            }

            if ( $row_bg_color != "" ) {
                $inline_style .= 'background-color:' . $row_bg_color . ';';
            }
            if ( $row_padding_vertical != "" ) {
                $inner_inline_style .= 'padding-top:' . $row_padding_vertical . 'px;padding-bottom:' . $row_padding_vertical . 'px;';
            }
            if ( $row_padding_horizontal != "" ) {
            	$inline_style .= 'padding-left:' . $row_padding_horizontal . '%;padding-right:' . $row_padding_horizontal . '%;';
            }
            if ( $row_margin_vertical != "" ) {
                $inline_style .= 'margin-top:' . $row_margin_vertical . 'px;margin-bottom:' . $row_margin_vertical . 'px;';
            }

            if ( $row_bg_type != "color" && isset( $img_url ) && $img_url[0] != "" ) {
                $inline_style .= 'background-image: url(' . $img_url[0] . ');';
            }

            if ( $row_animation != "" && $row_animation != "none" ) {
            	$row_el_class .= ' sf-animation';
                $animation_output = 'data-animation="' . $row_animation . '" data-delay="' . $row_animation_delay . '"';
            }

            if ( $row_expanding == "yes" ) {
            	$row_el_class .= ' spb-row-expanding';
            	$output .= "\n\t\t" . '<a href="#" class="spb-row-expand-text container" data-closed-text="'.$row_expading_text_closed.'" data-open-text="'.$row_expading_text_open.'"><span>'.$row_expading_text_closed.'</span></a>';
            }

            $row_el_class .= ' ' . $inner_column_height;

            $data_atts = 'data-v-center="' . $vertical_center . '" data-top-style="' . $row_top_style . '" data-bottom-style="' . $row_bottom_style . '" '.$animation_output;


            if ( $row_bg_type == "video" ) {
                if ( $img_url[0] != "" ) {
                    $output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax sf-parallax-video parallax-' . $parallax_video_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
                } else {
                    $output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax sf-parallax-video parallax-' . $parallax_video_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
                }
            } else if ( $row_bg_type == "image" ) {
                if ( $img_url[0] != "" ) {
                    if ( $parallax_image_movement == "stellar" ) {
                        $output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' parallax-' . $parallax_image_movement . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $row_el_class . '" '.$data_atts.' data-stellar-background-ratio="' . $parallax_image_speed . '" style="' . $inline_style . '">';
                    } else {
                        $output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' parallax-' . $parallax_image_movement . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
                    }
                } else {
                    $output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
                }
            } else {
            	if ($color_row_height == "window-height") {
            		$output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' spb_parallax_asset sf-parallax parallax-window-height ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
            	} else {
                	$output .= "\n\t" . '<div class="spb-row-container spb-row-' . $wrap_type . ' ' . $width . $row_el_class . '" '.$data_atts.' style="' . $inline_style . '">';
            	}
            }

            $output .= "\n\t\t" . '<div class="spb_content_element" style="' . $inner_inline_style . '">';
            $output .= "\n\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $row_bg_type == "video" ) {
                $loop = 'loop';
                if ( $bg_video_loop == "no" ) {
                    $loop = '';
                }
                if ( $img_url ) {
                    $output .= '<video class="parallax-video" poster="' . $img_url[0] . '" preload="auto" autoplay ' . $loop . ' muted>';
                } else {
                    $output .= '<video class="parallax-video" preload="auto" autoplay ' . $loop . ' muted>';
                }
                if ( $bg_video_mp4 != "" ) {
                    $output .= '<source src="' . $bg_video_mp4 . '" type="video/mp4">';
                }
                if ( $bg_video_webm != "" ) {
                    $output .= '<source src="' . $bg_video_webm . '" type="video/webm">';
                }
                if ( $bg_video_ogg != "" ) {
                    $output .= '<source src="' . $bg_video_ogg . '" type="video/ogg">';
                }
                $output .= '</video>';
                if ( $parallax_video_overlay != "color" ) {
                    $output .= '<div class="video-overlay overlay-' . $parallax_video_overlay . '"></div>';
                }
            }

            if ( $row_overlay_opacity != "0" && $parallax_video_overlay == "color" ) {
                $opacity = intval( $row_overlay_opacity, 10 ) / 100;
                $output .= '<div class="row-overlay" style="background-color:' . $row_bg_color . ';opacity:' . $opacity . ';"></div>';
            } else if ( $row_overlay_opacity != "0" ) {
	            $output .= '<div class="row-overlay overlay-' . $parallax_video_overlay . '"></div>';
            }

            $output .= "\n\t" . '</div>';

            $output = $this->startRow( $el_position, '', true, $rowId ) . $output . $this->endRow( $el_position, '', true );

            if ( $row_bg_type == "image" || $row_bg_type == "video" || ($row_bg_type == "color" && $color_row_height == "window-height") ) {
                global $sf_include_parallax;
                $sf_include_parallax = true;
            }

            return $output;
        }

        public function contentAdmin( $atts, $content = null ) {
            $width = $row_el_class = $bg_color = $padding_vertical = '';
            extract( shortcode_atts( array(
                'wrap_type'               => 'content-width',
                'row_el_class'            => '',
                'row_bg_color'            => '',
                'color_row_height'		  => '',
                'inner_column_height'	  => '',
                'row_top_style'			  => '',
                'row_bottom_style'		  => '',
                'row_padding_vertical'    => '',
                'row_padding_horizontal'  => '',
                'row_margin_vertical'     => '',
                'row_overlay_opacity'     => '0',
                'remove_element_spacing'  => '',
                'vertical_center'         => 'true',
                'row_id'                  => '',
                'row_name'                => '',
                'row_bg_type'             => '',
                'bg_image'                => '',
                'bg_video_mp4'            => '',
                'bg_video_webm'           => '',
                'bg_video_ogg'            => '',
                'bg_video_loop'           => 'yes',
                'parallax_video_height'   => 'window-height',
                'parallax_image_height'   => 'content-height',
                'parallax_video_overlay'  => 'none',
                'parallax_image_movement' => 'fixed',
                'parallax_image_speed'    => '0.5',
                'bg_type'                 => '',
                'row_expanding'			  => '',
                'row_expading_text_closed' => '',
                'row_expading_text_open'  => '',
                'row_animation'        	  => '',
                'row_animation_delay'  	  => '',
                'responsive_vis'          => '',
                'row_responsive_vis'          => '',
                'el_position'             => '',
                'width'                   => 'span12'
            ), $atts ) );

            $output = '';

            $output .= '<div data-element_type="spb_row" class="spb_row spb_sortable span12 spb_droppable not-column-inherit">';
            $output .= '<input type="hidden" class="spb_sc_base" name="element_name-spb_row" value="spb_row">';
            $output .= '<div class="controls sidebar-name"><span class="asset-name">' . __( "Row", 'swift-framework-plugin' ) . '</span><div class="controls_right"><a class="element-save" href="#" title="Save"></a><a class="column_edit" href="#" title="Edit"></a> <a class="column_clone" href="#" title="Clone"></a> <a class="column_delete" href="#" title="Delete"></a></div></div>';
            $output .= '<div class="spb_element_wrapper">';
            $output .= '<div class="row-fluid spb_column_container spb_sortable_container not-column-inherit">';
            $output .= do_shortcode( shortcode_unautop( $content ) );
            $output .= SwiftPageBuilder::getInstance()->getLayout()->getContainerHelper();
            $output .= '</div>';
            if ( isset( $this->settings['params'] ) ) {
                $inner = '';
                foreach ( $this->settings['params'] as $param ) {
                    $param_value = isset( $$param['param_name'] ) ? $$param['param_name'] : '';
                    //var_dump($param_value);
                    if ( is_array( $param_value ) ) {
                        // Get first element from the array
                        reset( $param_value );
                        $first_key   = key( $param_value );
                        $param_value = $param_value[ $first_key ];
                    }
                    $inner .= $this->singleParamHtmlHolder( $param, $param_value );
                }
                $output .= $inner;
            }
            $output .= '</div>';
            $output .= '</div>';

            return $output;
        }
    }

    /* PARAMS
    ================================================== */
    $params = array(
        array(
            "type"       => "section",
            "param_name" => "section_row_options",
            "heading"    => __( "Row Type Options", 'swift-framework-plugin' ),
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Wrap type", 'swift-framework-plugin' ),
            "param_name"  => "wrap_type",
            "value"       => array(
                __( 'Standard Width Content', 'swift-framework-plugin' ) => "content-width",
                __( 'Full Width Content', 'swift-framework-plugin' )     => "full-width"
            ),
            "description" => __( "Select if you want to row to wrap the content to the grid, or if you want the content to be edge to edge.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Row Background Type", 'swift-framework-plugin' ),
            "param_name"  => "row_bg_type",
            "value"       => array(
                __( "Color", 'swift-framework-plugin' ) => "color",
                __( "Image", 'swift-framework-plugin' ) => "image",
                __( "Video", 'swift-framework-plugin' ) => "video"
            ),
            "description" => __( "Choose whether you want to use an image or video for the background of the parallax. This will decide what is used from the options below.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "colorpicker",
            "heading"     => __( "Background color", 'swift-framework-plugin' ),
            "param_name"  => "row_bg_color",
            "value"       => "",
            "description" => __( "Select a background colour for the row here.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Color Row Height", 'swift-framework-plugin' ),
            "param_name"  => "color_row_height",
            "value"       => array(
                __( "Content Height", 'swift-framework-plugin' ) => "content-height",
                __( "Window Height", 'swift-framework-plugin' )  => "window-height"
            ),
            "required"       => array("row_bg_type", "=", "color"),
            "description" => __( "If you are using this as a coloured row asset, then please choose whether you'd like asset to sized based on the content height or the window height.", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "section",
            "param_name" => "section_bg_image_options",
            "heading"    => __( "Row Background Image Options", 'swift-framework-plugin' ),
            "required"       => array("row_bg_type", "=", "image"),
        ),
        array(
            "type"       => "section",
            "param_name" => "section_bg_video_options",
            "heading"    => __( "Row Background Video Options", 'swift-framework-plugin' ),
            "required"       => array("row_bg_type", "=", "video"),
        ),
        array(
            "type"        => "attach_image",
            "heading"     => __( "Background Image", 'swift-framework-plugin' ),
            "param_name"  => "bg_image",
            "value"       => "",
            "required"       => array("row_bg_type", "!=", "color"),
            "description" => "Choose an image to use as the background for the parallax area. This is also used as the fallback if using the video display."
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Background Image Type", 'swift-framework-plugin' ),
            "param_name"  => "bg_type",
            "value"       => array(
                __( "Cover", 'swift-framework-plugin' )   => "cover",
                __( "Pattern", 'swift-framework-plugin' ) => "pattern"
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "If you're uploading an image that you want to spread across the whole asset, then choose cover. Else choose pattern for an image you want to repeat.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Parallax Image Height", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_height",
            "value"       => array(
                __( "Content Height", 'swift-framework-plugin' ) => "content-height",
                __( "Window Height", 'swift-framework-plugin' )  => "window-height"
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "If you are using this as an image parallax asset, then please choose whether you'd like asset to sized based on the content height or the height of the viewport window.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Background Image Movement", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_movement",
            "value"       => array(
                __( "Fixed", 'swift-framework-plugin' )             => "fixed",
                __( "Scroll", 'swift-framework-plugin' )            => "scroll",
                __( "Stellar (dynamic)", 'swift-framework-plugin' ) => "stellar",
            ),
            "required"       => array("row_bg_type", "=", "image"),
            "description" => __( "Choose the type of movement you would like the parallax image to have. Fixed means the background image is fixed on the page, Scroll means the image will scroll will the page, and stellar makes the image move at a seperate speed to the page, providing a layered effect.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Parallax Image Speed (Stellar Only)", 'swift-framework-plugin' ),
            "param_name"  => "parallax_image_speed",
            "value"       => "0.5",
            "required"       => array("row_bg_type", "=", "image"),
            "description" => "The speed at which the parallax image moves in relation to the page scrolling. For example, 0.5 would mean the image scrolls at half the speed of the standard page scroll. (Default 0.5)."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (MP4)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_mp4",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in MP4 format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (WebM)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_webm",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in WebM format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Background Video (Ogg)", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_ogg",
            "value"       => "",
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Provide a video URL in OGG format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
        ),
        array(
            "type"        => "buttonset",
            "heading"     => __( "Background Video Loop", 'swift-framework-plugin' ),
            "param_name"  => "bg_video_loop",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' )  => "yes",
                __( "No", 'swift-framework-plugin' ) => "no"
            ),
            "std"         => 'yes',
            "required"       => array("row_bg_type", "=", "video"),
            "description" => "Choose if you would like the background video to be looped."
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Parallax Video Height", 'swift-framework-plugin' ),
            "param_name"  => "parallax_video_height",
            "value"       => array(
                __( "Window Height", 'swift-framework-plugin' )  => "window-height",
                __( "Content Height", 'swift-framework-plugin' ) => "content-height"
            ),
            "required"       => array("row_bg_type", "=", "video"),
            "description" => __( "If you are using this as a video parallax asset, then please choose whether you'd like asset to sized based on the content height or the video height.", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "section",
            "param_name" => "section_display_options",
            "heading"    => __( "Row Display Options", 'swift-framework-plugin' ),
        ),
    );

    if ( sf_theme_supports('advanced-row-styling') ) {
    	$params[] = array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "Row top style", 'swift-framework-plugin' ),
    	    "param_name"  => "row_top_style",
    	    "value"       => array(
    	        __( "None", 'swift-framework-plugin' )             => "none",
    	        __( "Slant Left-to-right", 'swift-framework-plugin' ) => "slant-ltr",
    	        __( "Slant Right-to-left", 'swift-framework-plugin' ) => "slant-rtl",
    	    ),
    	    "description" => __( "Choose the top style for the row, or none.", 'swift-framework-plugin' )
    	);
    	$params[] = array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "Row bottom style", 'swift-framework-plugin' ),
    	    "param_name"  => "row_bottom_style",
    	    "value"       => array(
    	        __( "None", 'swift-framework-plugin' )             => "none",
    	        __( "Slant Left-to-right", 'swift-framework-plugin' ) => "slant-ltr",
    	        __( "Slant Right-to-left", 'swift-framework-plugin' ) => "slant-rtl",
    	    ),
    	    "description" => __( "Choose the bottom style for the row, or none.", 'swift-framework-plugin' )
    	);
    }

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Row Overlay style", 'swift-framework-plugin' ),
        "param_name"  => "parallax_video_overlay",
        "value"       => array(
            __( "None", 'swift-framework-plugin' )             => "none",
            __( "Color", 'swift-framework-plugin' )            => "color",
            __( "Light Grid", 'swift-framework-plugin' )       => "lightgrid",
            __( "Dark Grid", 'swift-framework-plugin' )        => "darkgrid",
            __( "Light Grid (Fat)", 'swift-framework-plugin' ) => "lightgridfat",
            __( "Dark Grid (Fat)", 'swift-framework-plugin' )  => "darkgridfat",
            __( "Light Diagonal", 'swift-framework-plugin' )   => "diaglight",
            __( "Dark Diagonal", 'swift-framework-plugin' )    => "diagdark",
            __( "Light Vertical", 'swift-framework-plugin' )   => "vertlight",
            __( "Dark Vertical", 'swift-framework-plugin' )    => "vertdark",
            __( "Light Horizontal", 'swift-framework-plugin' ) => "horizlight",
            __( "Dark Horizontal", 'swift-framework-plugin' )  => "horizdark",
        ),
        "description" => __( "If you would like an overlay to appear on top of the image/video, then you can select it here.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "uislider",
        "heading"     => __( "Row Overlay Opacity", 'swift-framework-plugin' ),
        "param_name"  => "row_overlay_opacity",
        "value"       => "30",
        "step"        => "5",
        "min"         => "0",
        "max"         => "100",
        "description" => __( "Adjust the overlay capacity if using an image or video option. This only has effect for the color overlay style option, and shows an overlay over the image/video at the desired opacity. Percentage.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "uislider",
        "heading"     => __( "Padding - Vertical", 'swift-framework-plugin' ),
        "param_name"  => "row_padding_vertical",
        "value"       => "0",
        "description" => __( "Adjust the vertical padding for the row. (px)", 'swift-framework-plugin' )
    );

    if ( sf_theme_supports('advanced-row-styling') ) {
    	$params[] = array(
    	    "type"        => "uislider",
    	    "heading"     => __( "Padding - Horizontal", 'swift-framework-plugin' ),
    	    "param_name"  => "row_padding_horizontal",
    	    "value"       => "0",
    	    "min"         => "0",
    	    "step"        => "1",
    	    "max"         => "20",
    	    "description" => __( "Adjust the horizontal padding for the row. (%)", 'swift-framework-plugin' )
    	);
    }

    $params[] = array(
        "type"        => "uislider",
        "heading"     => __( "Margin - Vertical", 'swift-framework-plugin' ),
        "param_name"  => "row_margin_vertical",
        "value"       => "0",
        "description" => __( "Adjust the margin above and below the row. (px)", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Remove Element Spacing", 'swift-framework-plugin' ),
        "param_name"  => "remove_element_spacing",
        "value"       => array(
            __( 'No', 'swift-framework-plugin' )  => "no",
            __( 'Yes', 'swift-framework-plugin' ) => "yes"
        ),
        "description" => __( "Enable this option if you wish to remove all spacing from the elements within the row.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Vertically Center Elements Within", 'swift-framework-plugin' ),
        "param_name"  => "vertical_center",
        "value"       => array(
            __( 'No', 'swift-framework-plugin' )  => "false",
            __( 'Yes', 'swift-framework-plugin' ) => "true"
        ),
        "description" => __( "Enable this option if you wish to center the elements within the row.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Inner Column Height", 'swift-framework-plugin' ),
        "param_name"  => "inner_column_height",
        "value"       => array(
            __( 'Natural', 'swift-framework-plugin' )  => "col-natural",
            __( 'Window Height', 'swift-framework-plugin' ) => "col-window-height"
        ),
        "description" => __( "If you have the Window Height option selected for the row, and would like inner column assets to be 100% height, then please select the Window Height option here.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"       => "section",
        "param_name" => "section_reveal_options",
        "heading"    => __( "Row Content Expand Options", 'swift-framework-plugin' ),
    );
    $params[] = array(
        "type"        => "buttonset",
        "heading"     => __( "Expanding Row", 'swift-framework-plugin' ),
        "param_name"  => "row_expanding",
        "value"       => array(
            __( "No", 'swift-framework-plugin' )             => "no",
            __( "Yes", 'swift-framework-plugin' )             => "yes",
        ),
        "description" => __( "If you would like the content to be hidden on load, and have a text link to expand the content, then select Yes.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Expanding Link Text (Content Closed)", 'swift-framework-plugin' ),
        "param_name"  => "row_expading_text_closed",
        "value"       => "",
        "description" => __( "This is the text that is shown when the expanding row is closed.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Expanding Link Text (Content Open)", 'swift-framework-plugin' ),
        "param_name"  => "row_expading_text_open",
        "value"       => "",
        "description" => __( "This is the text that is shown when the expanding row is open.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"       => "section",
        "param_name" => "row_animation_options",
        "heading"    => __( "Animation Options", 'swift-framework-plugin' ),
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
        "param_name"  => "row_animation",
        "value"       => spb_animations_list(),
        "description" => __( "Select an intro animation for the row which will show it when it appears within the viewport.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
        "param_name"  => "row_animation_delay",
        "value"       => "0",
        "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"       => "section",
        "param_name" => "section_misc_options",
        "heading"    => __( "Row Misc/ID Options", 'swift-framework-plugin' ),
    );
    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Responsive Visiblity", 'swift-framework-plugin' ),
        "param_name"  => "row_responsive_vis",
        "holder"      => 'indicator',
        "value"       => spb_responsive_vis_list(),
        "description" => __( "Set the responsive visiblity for the row, if you would only like it to display on certain display sizes.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Row ID", 'swift-framework-plugin' ),
        "param_name"  => "row_id",
        "value"       => "",
        "description" => __( "If you wish to add an ID to the row, then add it here. You can then use the id to deep link to this section of the page. This is also used for one page navigation. NOTE: Make sure this is unique to the page!!", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Row Section Name", 'swift-framework-plugin' ),
        "param_name"  => "row_name",
        "value"       => "",
        "description" => __( "This is used for the one page navigation, to identify the row. If this is left blank, then the row will be left off of the one page navigation.", 'swift-framework-plugin' )
    );
    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Extra class", 'swift-framework-plugin' ),
        "param_name"  => "row_el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );


	/* SHORTCODE MAP
	================================================== */
    SPBMap::map( 'spb_row', array(
        "name"            => __( "Row", 'swift-framework-plugin' ),
        "base"            => "spb_row",
        "controls"        => "edit_delete",
        "content_element" => false,
        "params"          => $params
    ) );