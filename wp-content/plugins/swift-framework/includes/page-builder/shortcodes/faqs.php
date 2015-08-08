<?php

class SwiftPageBuilderShortcode_spb_faqs extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

        $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
        	'title' => '',
        	'category' => '',
            'el_class' => '',
            'el_position' => '',
            'width' => '1/2'
        ), $atts));

        $output = $items_nav = $items = '';

        /* CATEGORY SLUG MODIFICATION
        ================================================== */
        if ( $category == "All" ) {
            $category = "all";
        }
        if ( $category == "all" ) {
            $category = '';
        }
        $category_slug = str_replace( '_', '-', $category );
        $category_id = $category_name = "";

        $up_icon = apply_filters( 'sf_up_icon' , '<i class="ss-up"></i>' );
        $rows_icon = apply_filters( 'sf_rows_icon' , '<i class="ss-rows"></i>' );

       	// get all the categories from the database
       	if ( $category_slug != "" ) {
       		$category_obj = get_term_by('slug', $category_slug, 'faqs-category');
       		$category_name = $category_obj->name;
       		$category_id = $category_obj->term_id;
       	}
       	$cat_args = array('taxonomy' => 'faqs-category', 'child_of' => $category_id);
       	$cats = get_categories( $cat_args );

       	// FAQ NAVIGATION
       	$items_nav .= '<ul class="faqs-nav clearfix">';
       	foreach ($cats as $cat) {
			if ( function_exists( 'icl_object_id' ) ) {
				if ( $cat->term_id != icl_object_id( $cat->term_id, 'faqs-category', false, ICL_LANGUAGE_CODE ) ) {
					return;
				}
			}
       		$items_nav .= '<li><a href="#'.$cat->slug.'" class="smooth-scroll-link">' . $rows_icon . $cat->name . '<span class="count"> ' .$cat->count . '</span></a></li>';
       	}
       	$items_nav .= '</ul>';


       	// FAQ LISTINGS

       	if ( empty($cats) ) {
       		$items .= '<h4>'.__("Please ensure you have child categories for the selected category.", 'swift-framework-plugin').'</h4>';
       	}

		foreach ($cats as $cat) {

			// setup the category ID
			$cat_id= $cat->term_id;

			if ( function_exists( 'icl_object_id' ) ) {
	       		if ( $cat_id != icl_object_id( $cat_id, 'faqs-category', false, ICL_LANGUAGE_CODE ) ) {
	       			return;
	       		}
       		}

			// Make a header for the cateogry
			$items .= '<h4 class="faq-section-title" id="'.$cat->slug.'">'.$cat->name.'</h4>';

			$faqs_args = array(
				'post_type' => 'faqs',
				'post_status' => 'publish',
				'faqs-category' => $cat->slug,
				'posts_per_page' => 100
				);

			$faqs = new WP_Query( $faqs_args );

			$items .= '<ul class="faqs-section clearfix">';

			// FAQS LOOP

			while ( $faqs->have_posts() ) : $faqs->the_post();

				$faq_title = get_the_title();
				$faq_text = apply_filters( 'the_content', get_the_content() );
        $faq_text = str_replace( ']]>', ']]&gt;', $faq_text );

				$items .= '<li class="faq-item">';
				$items .= '<h6>'.$faq_title.'</h6>';
				$items .= '<div class="faq-text">'.do_shortcode($faq_text).'</div>';
				$items .= '</li>';

			endwhile;

			$items .= '<div class="spb_divider go_to_top_icon1 spb_content_element "><a class="animate-top" href="#">' . $up_icon . '</a></div>';
			$items .= '</ul>';


			wp_reset_postdata();
		}

        $el_class = $this->getExtraClass($el_class);
        $width = spb_translateColumnWidthToSpan($width);

        $output .= "\n\t".'<div class="spb_content_element '.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="spb_wrapper faqs-wrap">';
        $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
        $output .= "\n\t\t\t". $items_nav;
        $output .= "\n\t\t\t". $items;
        $output .= "\n\t\t".'</div> ' . $this->endBlockComment('.spb_wrapper');
        $output .= "\n\t".'</div> ' . $this->endBlockComment($width);

        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}

SPBMap::map( 'spb_faqs', array(
    "name"		=> __("FAQs", 'swift-framework-plugin'),
    "base"		=> "spb_faqs",
    "class"		=> "",
    "icon"      => "spb-icon-faqs",
    "wrapper_class" => "clearfix",
    "controls"	=> "full",
    "params"	=> array(
    	array(
    	    "type"        => "textfield",
    	    "heading"     => __( "Widget title", 'swift-framework-plugin' ),
    	    "param_name"  => "title",
    	    "value"       => "",
    	    "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
    	),
    	array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "FAQ category", 'swift-framework-plugin' ),
    	    "param_name"  => "category",
    	    "value"       => sf_get_category_list( 'faqs-category' ),
    	    "description" => __( "Choose the category for the FAQ.", 'swift-framework-plugin' )
    	),
        array(
            "type" => "textfield",
            "heading" => __("Extra class", 'swift-framework-plugin'),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin')
        )
    )
) );

?>