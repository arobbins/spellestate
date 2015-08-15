<?php

	/*
	*
	*	Atelier Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*/

	/* LOAD PARENT THEME STYLES
	================================================== */
	function child_assets() {
    wp_enqueue_style('styles-local', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('scripts-local', get_stylesheet_directory_uri() . '/assets/js/app.min.js', array(), '1.0.0', true);
	}

  add_action('wp_enqueue_scripts', 'child_assets', 15);

	/* LOAD THEME LANGUAGE
	================================================== */
	/*
	*	You can uncomment the line below to include your own translations
	*	into your child theme, simply create a "language" folder and add your po/mo files
	*/

	// load_theme_textdomain('swiftframework', get_stylesheet_directory().'/language');


	/* REMOVE PAGE BUILDER ASSETS
	================================================== */
	/*
	*	You can uncomment the line below to remove selected assets from the page builder
	*/

	// function spb_remove_assets( $pb_assets ) {
	//     unset($pb_assets['parallax']);
	//     return $pb_assets;
	// }
	// add_filter( 'spb_assets_filter', 'spb_remove_assets' );


	/* ADD/EDIT PAGE BUILDER TEMPLATES
	================================================== */
	function custom_prebuilt_templates($prebuilt_templates) {

		/*
		*	You can uncomment the lines below to add custom templates
		*/
		// $prebuilt_templates["custom"] = array(
		// 	'id' => "custom",
		// 	'name' => 'Custom',
		// 	'code' => 'your-code-here'
		// );

		/*
		*	You can uncomment the lines below to remove default templates
		*/
		// unset($prebuilt_templates['home-1']);
		// unset($prebuilt_templates['home-2']);

		// return templates array
	    return $prebuilt_templates;

	}
	//add_filter( 'spb_prebuilt_templates', 'custom_prebuilt_templates' );

//	function custom_post_thumb_image($thumb_img_url) {
//
//	    if ($thumb_img_url == "") {
//	    	global $post;
//	  		ob_start();
//	  		ob_end_clean();
//	  		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
//	  		if (!empty($matches) && isset($matches[1][0])) {
//	  		$thumb_img_url = $matches[1][0];
//	    	}
//	    }
//
//	    return $thumb_img_url;
//	}
//	add_filter( 'sf_post_thumb_image_url', 'custom_post_thumb_image' );

//	function dynamic_section( $sections ) {
//        //$sections = array();
//        $sections[] = array(
//            'title'  => __( 'Section via hook', 'redux-framework-demo' ),
//            'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo' ),
//            'icon'   => 'el-icon-paper-clip',
//            // Leave this as a blank section, no options just some intro text set above.
//            'fields' => array()
//        );
//        return $sections;
//    }
//

//	function custom_style_sheet() {
//	    echo '<link rel="stylesheet" href="'.get_stylesheet_directory_uri() . '/test.css'.'" type="text/css" media="all" />';
//	}
//	add_action('wp_head', 'custom_style_sheet', 100);


	// function custom_wishlist_icon() {
	// 	return '<i class="fa-heart"></i>';
	// }
	// add_filter('sf_wishlist_icon', 'custom_wishlist_icon', 100);
	// add_filter('sf_add_to_wishlist_icon', 'custom_wishlist_icon', 100);
	// add_filter('sf_wishlist_menu_icon', 'custom_wishlist_icon', 100);