<?php

    /*
    *
    *	Swift Page Builder - Directory Users Listings Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_directory_user_listings extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $el_class = $el_position = $output = '';

            extract( shortcode_atts( array(
                'el_position'      => '',
                'width'            => '1/1',
                'el_class'         => ''
            ), $atts ) );


            $current_user = wp_get_current_user();
            $users_listings_output = sf_directory_user_listings($current_user->ID);

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_latest_tweets_widget directory-results user-listing-results ' . $width . $el_class . '"   data-ajax-url="' . admin_url('admin-ajax.php') . '" >';
            $output .= "\n\t\t" . '<div class="spb-asset-content spb_wrapper latest-tweets-wrap clearfix">';
            $output .= "\n\t\t\t" . '<ul class="tweet-wrap">' . $users_listings_output . "</ul>";
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_directory_user_listings', array(
        "name"   => __( "Directory User Listings", 'swift-framework-plugin' ),
        "base"   => "spb_directory_user_listings",
        "class"  => "spb-latest-tweets",
        "icon"   => "icon-directory-user-listing",
        "params" => array(
           
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        )
    ) );