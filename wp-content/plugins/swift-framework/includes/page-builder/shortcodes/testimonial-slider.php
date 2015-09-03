<?php

    /*
    *
    *	Swift Page Builder - Testimonial Slider Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_testimonial_slider extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $order = $text_size = $items = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'text_size'   => '',
                'item_count'  => '',
                'order'       => '',
                'category'    => 'all',
                'animation'   => 'fade',
                'autoplay'    => 'yes',
                'el_class'    => '',
                'el_position' => '',
                'width'       => '1/1'
            ), $atts ) );

            $output = '';

            // CATEGORY SLUG MODIFICATION
            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );


            // TESTIMONIAL QUERY SETUP

            global $post, $wp_query;

            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

            $testimonials_args = array(
                'orderby'               => $order,
                'post_type'             => 'testimonials',
                'post_status'           => 'publish',
                'paged'                 => $paged,
                'testimonials-category' => $category_slug,
                'posts_per_page'        => $item_count,
                'no_found_rows'         => 1,
            );

            $testimonials = new WP_Query( $testimonials_args );

            if ( $autoplay == "yes" ) {
                $items .= '<div class="flexslider testimonials-slider content-slider" data-animation="' . $animation . '" data-autoplay="yes"><ul class="slides cS-hidden">';
            } else {
                $items .= '<div class="flexslider testimonials-slider content-slider" data-animation="' . $animation . '" data-autoplay="no"><ul class="slides cS-hidden">';
            }

            // TESTIMONIAL LOOP

            while ( $testimonials->have_posts() ) : $testimonials->the_post();

                $testimonial_text         = get_the_content();
                $testimonial_cite         = sf_get_post_meta( $post->ID, 'sf_testimonial_cite', true );
                $testimonial_cite_subtext = sf_get_post_meta( $post->ID, 'sf_testimonial_cite_subtext', true );

                $items .= '<li class="testimonial">';
                $items .= '<div class="slide-content-wrap">';
                $items .= '<div class="testimonial-text text-' . $text_size . '">' . do_shortcode( $testimonial_text ) . '</div>';
                if ( $testimonial_cite_subtext != "" ) {
                    $items .= '<cite>' . $testimonial_cite . '<span>' . $testimonial_cite_subtext . '</span></cite>';
                } else {
                    $items .= '<cite>' . $testimonial_cite . '</cite>';
                }
                $items .= '</div>';
                $items .= '</li>';

            endwhile;

            wp_reset_query();
            wp_reset_postdata();

            $items .= '</ul></div>';

			$quote_icon = apply_filters( 'sf_quote_icon', '<i class="ss-quote"></i>' );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $sidebar_config = sf_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            $el_class .= ' testimonial';

            $output .= "\n\t" . '<div class="spb_testimonial_slider_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t" . '<div class="spb-bg-color-wrap">';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper slider-wrap">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="heading-wrap"><h3 class="spb-heading spb-center-heading">' . $title . '</h3></div>' : '';
            if ( $title == "" ) {
            $output .= "\n\t\t\t" . '<div class="testimonial-icon">'.$quote_icon.'</div>';
            }
            $output .= "\n\t\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $sidebars == "no-sidebars" ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            global $sf_include_carousel;
            $sf_include_carousel = true;

            return $output;
        }
    }

    SPBMap::map( 'spb_testimonial_slider', array(
        "name"          => __( "Testimonials Slider", 'swift-framework-plugin' ),
        "base"          => "spb_testimonial_slider",
        "class"         => "spb_testimonial_slider",
        "icon"          => "spb-icon-testimonial_slider",
        "wrapper_class" => "clearfix",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Text size", 'swift-framework-plugin' ),
                "param_name"  => "text_size",
                "value"       => array(
                    __( 'Large', 'swift-framework-plugin' )    => "large",
                    __( 'Standard', 'swift-framework-plugin' ) => "standard"
                ),
                "description" => __( "Choose the size of the text.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "6",
                "description" => __( "The number of testimonials to show. Leave blank to show ALL testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Testimonials Order", 'swift-framework-plugin' ),
                "param_name"  => "order",
                "value"       => array(
                    __( 'Random', 'swift-framework-plugin' ) => "rand",
                    __( 'Latest', 'swift-framework-plugin' ) => "date"
                ),
                "description" => __( "Choose the order of the testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "select-multiple",
                "heading"     => __( "Testimonials category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => sf_get_category_list( 'testimonials-category' ),
                "description" => __( "Choose the category for the testimonials.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Slider autoplay", 'swift-framework-plugin' ),
                "param_name"  => "autoplay",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "description" => __( "Select if you want the slider to autoplay or not.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        )
    ) );