<?php

    /*
    *
    *	Swift Page Builder - Blog Grid Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_blog_grid extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $width = $el_class = $output = $items_output = $el_position = '';

            extract( shortcode_atts( array(
                'title'            => '',
                'fullwidth'        => 'yes',
                "item_count"       => '5',
                "instagram_id"     => '',
                "instagram_token"  => '',
                "twitter_username" => '',
                'el_position'      => '',
                'width'            => '1/1',
                'el_class'         => ''
            ), $atts ) );


            $width = spb_translateColumnWidthToSpan( $width );


            /* SIDEBAR CONFIG
            ================================================== */
            $sidebar_config = sf_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            $items_output = "";

            global $sf_options, $sf_sidebar_config;


            /* COUNT CALC
            ================================================== */
            $item_class = "col-sm-4";
            if ( $fullwidth == "yes" ) {
                $item_class = "col-sm-sf-5";
                $item_count = $item_count * 5;
            } else {
                $item_count = $item_count * 3;
            }

            $tweet_count = $instagram_count = floor( $item_count / 2 );

            if ( $tweet_count + $instagram_count < $item_count ) {
                $tweet_count = $item_count - $instagram_count;
            }

            if ( $twitter_username == "" ) {
                $tweet_count     = 0;
                $instagram_count = $item_count;
            }
            if ( $instagram_id == "" || $instagram_token == "" ) {
                $instagram_count = 0;
                $tweet_count     = $item_count;
            }


            /* ITEMS OUTPUT
            ================================================== */
            $items_output .= '<div class="blog-items blog-grid-items">';
            $items_output .= '<ul class="grid-items row clearfix">';
            $items_output .= '</ul>';


            /* TWEETS
            ================================================== */
            if ( $twitter_username != "" ) {
                $items_output .= '<ul class="blog-tweets">' . sf_get_tweets( $twitter_username, $tweet_count, 'blog-grid', $item_class ) . '</ul>';
            }


            /* INSTAGRAMS
            ================================================== */
            if ( $instagram_id != "" && $instagram_token != "" ) {
                $items_output .= '<ul class="blog-instagrams" data-title="' . __( "Instagram", 'swift-framework-plugin' ) . '" data-count="' . $instagram_count . '" data-userid="' . $instagram_id . '" data-token="' . $instagram_token . '" data-itemclass="'.$item_class.'"></ul>';
            }


            $items_output .= '</div>';


            /* FINAL OUTPUT
            ================================================== */
            if ( $fullwidth == "yes" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }
            $el_class = $this->getExtraClass( $el_class );

            $output .= "\n\t" . '<div class="spb_blog_grid_widget blog-wrap spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '', $fullwidth ) : '';
            $output .= "\n\t\t" . $items_output;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $fullwidth ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            global $sf_has_blog, $sf_include_imagesLoaded;
            $sf_include_imagesLoaded = true;
            $sf_has_blog             = true;

            return $output;

        }
    }

    SPBMap::map( 'spb_blog_grid', array(
        "name"   => __( "Social Grid", 'swift-framework-plugin' ),
        "base"   => "spb_blog_grid",
        "class"  => "spb_blog_grid spb_tab_media",
        "icon"   => "icon-social-grid",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "class"       => "",
                "heading"     => __( "Number of rows", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => apply_filters( 'sf_blog_grid_item_counts', array( "1", "2", "3", "4", "5" ) ),
                "description" => __( "The number of grid rows to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Twitter Username", 'swift-framework-plugin' ),
                "param_name"  => "twitter_username",
                "value"       => "",
                "description" => __( "Enter your twitter username here to include tweets in the blog grid. Ensure you have the Twitter oAuth plugin installed and your details added.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Instagram ID", 'swift-framework-plugin' ),
                "param_name"  => "instagram_id",
                "value"       => "",
                "description" => __( "Enter your Instagram ID here to include your instagrams in the blog grid. You can find your instagram ID here - <a href='http://jelled.com/instagram/lookup-user-id' target='_blank'>http://jelled.com/instagram/lookup-user-id</a> You will also need to enter your token below.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Instagram Token", 'swift-framework-plugin' ),
                "param_name"  => "instagram_token",
                "value"       => "",
                "description" => __( "Enter your Instagram Token here to include your instagrams in the blog grid. You can generate your instagram access token here - <a href='http://www.pinceladasdaweb.com.br/instagram/access-token/' target='_blank'>http://www.pinceladasdaweb.com.br/instagram/access-token/</a>. NOTE: This is REQUIRED.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full Width", 'swift-framework-plugin' ),
                "param_name"  => "fullwidth",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
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
