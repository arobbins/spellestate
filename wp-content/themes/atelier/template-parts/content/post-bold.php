<?php

	/*
	*
	*	Post - Bold
	*	------------------------------------------------
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	Output for bold type blog posts
	*
	*/
	
	global $sf_options;
	
	// Defaults
	$show_title = "yes";
	$show_details = "yes";
	$show_excerpt = "yes";
	$content_output = "excerpt";
	$excerpt_length = 60;
	$post_links_match_thumb = $sf_options['post_links_match_thumb'];	
	
	// Post Meta
	$post_id 	     = $post->ID;
	$post_format 	 = get_post_format();
	$post_title      = get_the_title();
	$post_permalink  = get_permalink();
	$custom_excerpt  = sf_get_post_meta( $post_id, 'sf_custom_excerpt', true );
	$post_excerpt    = '';
	if ( $content_output == "excerpt" ) {
	    if ( $custom_excerpt != '' ) {
	        $post_excerpt = sf_custom_excerpt( $custom_excerpt, $excerpt_length );
	    } else {
	        if ( $post_format == "quote" ) {
	            $post_excerpt = sf_get_the_content_with_formatting();
	        } else {
	            $post_excerpt = sf_excerpt( $excerpt_length );
	        }
	    }
	} else {
	    $post_excerpt = sf_get_the_content_with_formatting();
	}
	if ( $post_format == "chat" ) {
	    $post_excerpt = sf_content( 40 );
	} else if ( $post_format == "audio" ) {
	    $post_excerpt = do_shortcode( get_the_content() );
	} else if ( $post_format == "video" ) {
	    $content      = get_the_content();
	    $content      = apply_filters( 'the_content', $content );
	    $post_excerpt = $content;
	} else if ( $post_format == "link" ) {
	    $content      = get_the_content();
	    $content      = apply_filters( 'the_content', $content );
	    $post_excerpt = $content;
	}
	$post_permalink_config = 'href="' . $post_permalink . '" class="link-to-post"';
	if ( $post_links_match_thumb ) {
		$link_config = sf_post_item_link();
		$post_permalink_config = $link_config['config'];
	}
	
	
?>

<div class="bold-item-wrap">

	<?php if ( $show_title == "yes" && $post_format != "quote" && $post_format != "link" ) { ?>
		<h1 itemprop="name headline"><a <?php echo $post_permalink_config; ?>><?php echo $post_title; ?></a></h1>
	<?php } else if ( $post_format == "quote" ) { ?>
		<div class="quote-excerpt" itemprop="name headline"><a <?php echo $post_permalink_config; ?>><?php echo $post_excerpt; ?></a></div>
	<?php } else if ( $post_format == "link" ) { ?>
		<h3 itemprop="name headline"><a <?php echo $post_permalink_config; ?>><?php echo $post_title; ?></a></h3>
	<?php } ?>
	
	<?php if ( $show_excerpt == "yes" && $post_format != "quote" ) { ?>
	<div class="excerpt" itemprop="description"><?php echo $post_excerpt; ?></div>
	<?php } ?>
	
	<?php if ( $show_details == "yes" ) {
		sf_get_content_view( 'post', 'meta-details', false );
	} ?>

</div>