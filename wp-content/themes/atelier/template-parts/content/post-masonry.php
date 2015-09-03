<?php

	/*
	*
	*	Post - Masonry
	*	------------------------------------------------
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	Output for masonry type blog posts
	*
	*/
	
	global $sf_options, $sf_sidebar_config;
	
	$fullwidth = "no";
	$single_author = $sf_options['single_author'];
	$remove_dates  = $sf_options['remove_dates'];
	$post_links_match_thumb = $sf_options['post_links_match_thumb'];	
	$content_output = "excerpt";
	$excerpt_length = 60;
	$comments_icon 	 = apply_filters( 'sf_comments_icon', '<i class="ss-chat"></i>' );
	$link_icon		 = apply_filters( 'sf_link_icon', '<i class="ss-link"></i>' );
	$sticky_icon   	 = apply_filters( 'sf_sticky_icon', '<i class="ss-bookmark"></i>' );
	
	// Show/Hide
	$show_title = "yes";
	$show_details = "yes";
	$show_excerpt = "yes";
	$show_read_more = "yes";
	
	// Post Meta
	$post_id 	     = $post->ID;
	$post_format 	 = get_post_format();
	$post_title      = get_the_title();
	$post_author     = get_the_author();
	$post_date       = get_the_date();
	$post_date_str   = get_the_date('Y-m-d');
	$post_date_month = get_the_date('M');
	$post_date_day = get_the_date('d');
	$post_date_year = get_the_date('Y');
	$post_categories = get_the_category_list( ', ' );
	$post_comments   = get_comments_number();
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
	$thumb_type         = sf_get_post_meta( $post_id, 'sf_thumbnail_type', true );
	$download_button    = sf_get_post_meta( $post_id, 'sf_download_button', true );
	$download_file      = sf_get_post_meta( $post_id, 'sf_download_file', true );
	$download_text      = apply_filters( 'sf_post_download_text', __( "Download", "swiftframework" ) );
	$download_shortcode = sf_get_post_meta( $post_id, 'sf_download_shortcode', true );
	
	// Media
	$item_figure = "";
	if ( $thumb_type != "none" ) {
	    $item_figure = sf_post_thumbnail( 'masonry', $fullwidth );
	}
?>

<?php echo sf_masonry_date_figure_overlay(); ?>

<?php if ( $item_figure != "" ) {
	echo $item_figure;
} ?>

<div class="details-wrap">
	<a <?php echo $post_permalink_config; ?>></a>
	
	<?php if ( $post_type == "post" ) { ?>
		<?php if ( $post_format == "standard" ) { ?>
	        <h6><?php _e( "Article", "swiftframework" ); ?></h6>
	    <?php } else { ?>
	        <h6><?php echo $post_format; ?></h6>
	    <?php } ?>
	<?php } else { ?>
	    <h6><?php echo $post_type; ?></h6>
	<?php } ?>
	
	
	<?php 
		/* Post Title
		================================================== */
		if ( $show_title == "yes" && $post_format != "quote" && $post_format != "link" ) { ?>
	    <h2 itemprop="name headline"><?php echo $post_title; ?></h2>
	<?php } else if ( $post_format == "quote" ) { ?>
	    <div class="quote-excerpt" itemprop="name headline"><?php echo $post_excerpt; ?></div>
	<?php } else if ( $post_format == "link" ) { ?>
	    <h3 itemprop="name headline"><?php echo $post_title; ?></h3>
	<?php } ?>
	

    <?php if ( $show_details == "yes" ) {
   		echo sf_get_post_details($post_id);
	} ?>

	<?php  
		/* Post Excerpt
		================================================== */
		if ( $show_excerpt == "yes" && $post_format != "quote" ) { ?>
        <div class="excerpt" itemprop="description"><?php echo $post_excerpt; ?></div>
    <?php } ?>

	<?php 
		/* Post Read More
		================================================== */
		if ( $show_read_more == "yes" ) { ?>
		<?php if ( $download_button ) { ?>
			<?php if ( $download_shortcode != "" ) { ?>
				<?php echo do_shortcode( $download_shortcode ); ?>
			<?php } else { ?>
				<a href="<?php echo wp_get_attachment_url( $download_file ); ?>" class="download-button read-more-button"><?php echo $download_text; ?></a>
			<?php } ?>
		<?php } ?>
		
		<a class="read-more-button" href="<?php echo $post_permalink; ?>"><?php _e( "Read more", "swiftframework" ); ?></a>
	<?php } ?>

    <?php if ( $show_details == "yes" ) { ?>
        <div class="comments-likes">
        	        	
        	<?php if ( comments_open() ) { ?>
        	<div class="comments-wrapper"><a href="<?php echo $post_permalink; ?>'#comment-area"><?php echo $comments_icon; ?><span><?php echo $post_comments; ?></span></a></div>
        	<?php } ?>
        	
        	<?php if ( function_exists( 'lip_love_it_link' ) ) {
        		echo lip_love_it_link( $post_id, false );
        	} ?>
        	
        </div>
    <?php } ?>

</div><!-- CLOSE .details-wrap -->
