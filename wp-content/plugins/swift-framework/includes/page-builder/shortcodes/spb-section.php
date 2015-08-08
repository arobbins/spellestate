<?php

    /*
    *
    *	Swift Page Builder - SPB Template Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_section extends SwiftPageBuilderShortcode {

        public function contentAdmin( $atts, $content = null ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/1', 'spb_template' => '' );
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {

                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );

            $output = '';

            $tmp = '';

            $output .= do_shortcode( $content );
            $elem = $this->getElementHolder( $width );

            $iner = '';
            foreach ( $this->settings['params'] as $param ) {
                $custom_markup = '';
                $param_value   = isset( $$param['param_name'] ) ? $$param['param_name'] : null;

                if ( is_array( $param_value ) ) {
                    // Get first element from the array
                    reset( $param_value );
                    $first_key   = key( $param_value );
                    $param_value = $param_value[ $first_key ];
                }
                $iner .= $this->singleParamHtmlHolder( $param, $param_value );
            }

            /*
                    if ( isset($this->settings["custom_markup"]) &&$this->settings["custom_markup"] != '' ) {
                        if ( $content != '' ) {
                            $custom_markup = str_ireplace("%content%", $tmp.$content, $this->settings["custom_markup"]);
                        } else if ( $content == '' && isset($this->settings["default_content"]) && $this->settings["default_content"] != '' ) {
                            $custom_markup = str_ireplace("%content%",$this->settings["default_content"],$this->settings["custom_markup"]);
                        }

                        $iner .= do_shortcode($custom_markup);
                    }
            */

            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;

            return $output;
        }

        public function content( $atts, $content = null ) {

            $title = $size = $width = $spb_section_id = $el_position = $el_class = '';
            extract( shortcode_atts( array(
                'width'          => '1/1',
                'spb_section_id' => '',
                'el_position'    => '',
                'el_class'       => ''
            ), $atts ) );
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );

            if ( $spb_section_id == "" ) {
                return;
            }

            $content_section = get_post( $spb_section_id );

            $output .= "\n\t" . '<div class="spb-section spb_content_element ' . $el_class . '">';
            if ( isset( $content_section->post_content ) ) {
            	$content = $content_section->post_content;
            	$content = apply_filters( 'the_content', $content );
                $output .= "\n\t\t\t" . do_shortcode( $content );
            }
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            return $output;
        }

    }

    SPBMap::map( 'spb_section', array(
        "name"     => __( "SPB Section", 'swift-framework-plugin' ),
        "base"     => "spb_section",
        "controls" => "full",
        "class"    => "spb_section",
        "icon"     => "spb-icon-Section",
        "params"   => array(
            array(
	            "type"        => "dropdown",
	            "holder"	  => "page_name",
	            "heading"     => __( "SPB Section", 'swift-framework-plugin' ),
	            "param_name"  => "spb_section_id",
	            "value"       => sf_list_spb_sections(),
	            "description" => __( "Choose the SPB Section to display.", 'swift-framework-plugin' )
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
