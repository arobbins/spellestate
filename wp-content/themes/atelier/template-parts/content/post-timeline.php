<?php

	/*
	*
	*	Post - Timeline
	*	------------------------------------------------
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	Output for timeline type blog posts
	*
	*/
	
	// Show/Hide
	$show_details = "yes";
	
	// Post Date
	$post_date_month = get_the_date('M');
	$post_date_day = get_the_date('d');
	$post_date_year = get_the_date('Y');
	
?>


<?php if (sf_theme_opts_name() == "sf_atelier_options" && $show_details == "yes" ) { ?>
	
	<?php
	/* Post Details
	================================================== */
	?>
	<div class="side-details">
	
		<?php if ( !$remove_dates ) { ?>
	        <div class="side-post-date narrow-date-block" itemprop="datePublished">
	        	<span class="month"><?php echo $post_date_month; ?></span>
	        	<span class="day"><?php echo $post_date_day; ?></span>
	        	<span class="year"><?php echo $post_date_year; ?></span>
	        </div>
       	<?php } ?>
            		
		<?php if ( comments_open() ) { ?>
		    <div class="comments-wrapper narrow-date-block">
		    	<a href="<?php echo $post_permalink; ?>#comment-area">
				    <svg version="1.1" class="comments-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
				    	 width="30px" height="30px" viewBox="0 0 30 30" enable-background="new 0 0 30 30" xml:space="preserve">
				  		<path fill="none" class="stroke" stroke="#252525" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="
				    	M13.958,24H2.021C1.458,24,1,23.541,1,22.975V2.025C1,1.459,1.458,1,2.021,1h25.957C28.542,1,29,1.459,29,2.025v20.949
				    	C29,23.541,28.542,24,27.979,24H21v5L13.958,24z"/>
				    </svg>
			    	<span><?php echo $post_comments; ?></span>
		    	</a>
		    </div>
		<?php } ?>

		<?php if ( function_exists( 'lip_love_it_link' ) ) {
		    echo lip_love_it_link( get_the_ID(), false, '', 'narrow-date-block' );
		} ?>

	</div>

    <?php
    /* Post Content Wrap
    ================================================== */
    ?>
    <div class="post-content-wrap">

<?php } ?>

<?php if ( $show_details == "yes" ) { ?>
    <span class="standard-post-date" itemprop="datePublished"><?php echo $post_date; ?></span>
<?php } ?>

    $item_figure;

<?php if ( $item_figure == "" ) { ?>
    <div class="standard-post-content no-thumb clearfix"><!-- open standard-post-content -->
<?php } else { ?>
    <div class="standard-post-content clearfix"><!-- open standard-post-content -->
<?php } ?>

<?php if ( $show_title == "yes" && $post_format != "link" && $post_format != "quote" ) { ?>
    <h1 itemprop="name headline"><a ' . $post_permalink_config . '>' . $post_title . '</a></h1>';
<?php } ?>

<?php if ($show_details == "yes" && $post_format != "quote" && $post_format != "link" ) { ?>
	<?php if ( sf_theme_opts_name() == "sf_atelier_options" ) { ?>
		<?php if ( ! $single_author ) { ?>
		    <div class="blog-item-details">
		    	<?php echo sprintf(
		    		__( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> in %3$s', 'swiftframework' ),
		    		$post_author,
		    		get_author_posts_url( get_the_author_meta( 'ID' ) ),
		    		$post_categories
		    	); ?>
		    </div>
		<?php } ?>
	<?php } else { ?>
    	$item_details;
	<?php } ?>
<?php } ?>

<?php if ( $show_excerpt == "yes" ) { ?>
    <div class="excerpt" itemprop="description">' . $post_excerpt . '</div>';
<?php } else if ( $post_format == "quote" ) { ?>
    <div class="quote-excerpt heading-font" itemprop="description">' . $post_excerpt . '</div>';
<?php } else if ( $post_format == "link" ) { ?>
    <div class="link-excerpt heading-font" itemprop="description">' . $link_icon . $post_excerpt . '</div>';
<?php } ?>

<?php if ( is_sticky() ) { ?>
    <div class="sticky-post-icon"><?php echo $sticky_icon; ?></div>
<?php } ?>


<?php if ( $download_button ) { ?>
    <?php if ( $download_shortcode != "" ) { ?>
        <?php echo do_shortcode( $download_shortcode ); ?>
    <?php } else { ?>
        <a href="<?php echo wp_get_attachment_url( $download_file ); ?>" class="download-button read-more-button"><?php echo $download_text; ?></a>
    <?php } ?>
<?php } ?>

<?php if ( $show_read_more == "yes" && $post_format != "quote" && $post_format != "link" ) { ?>
    <a class="read-more-button" href="<?php echo get_permalink(); ?>"><?php _e( "Read more", "swiftframework" ); ?></a>
<?php } ?>

<?php if ( $show_details == "yes" ) { ?>

    <div class="comments-likes">';

    <?php if ( $post_format == "quote" || $post_format == "link" ) { ?>
        $item_details;
    <?php } ?>

    <?php if ( comments_open() ) { ?>
        <div class="comments-wrapper">
        	<a href="<?php echo $post_permalink; ?>#comment-area"><?php echo $comments_icon; ?><span><?php echo $post_comments; ?></span></a>
        </div>
    <?php } ?>

    <?php if ( function_exists( 'lip_love_it_link' ) ) {
        echo lip_love_it_link( get_the_ID(), false );
    } ?>

    </div>
<?php } ?>

</div><!-- close standard-post-content -->

<?php if ( sf_theme_opts_name() == "sf_atelier_options" && $show_details == "yes" && $blog_type != "timeline" ) { ?>
	</div><!-- close post-content-wrap -->
<?php } ?>
