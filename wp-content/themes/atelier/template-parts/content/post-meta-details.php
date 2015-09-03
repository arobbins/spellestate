<?php 
	global $sf_options;
    $single_author = $sf_options['single_author'];
    $remove_dates  = $sf_options['remove_dates'];
    	
    $post_author     = get_the_author();
    $post_categories = get_the_category_list( ', ' );
    $post_date       = get_the_date();
    $post_date_str   = get_the_date('Y-m-d');
    $post_comments   = get_comments_number();
?>

<?php if ( $single_author && ! $remove_dates ) { ?>
    <div class="blog-item-details">
    	<?php printf(
    		__( 'In %1$s on <time datetime="%2$s">%3$s</time>', 'swiftframework' ),
    		$post_categories,
    		$post_date_str,
    		$post_date
    	); ?>
    </div>
<?php } else if ( ! $remove_dates ) { ?>
    <div class="blog-item-details">
    	<?php printf( 
    		__( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> in %3$s on <time datetime="%4$s">%5$s</time>', 'swiftframework' ),
    		$post_author,
    		get_author_posts_url( get_the_author_meta( 'ID' ) ),
    		$post_categories,
    		$post_date_str,
    		$post_date
	    ); ?>
    </div>
<?php } else if ( ! $single_author ) { ?>
    <div class="blog-item-details">
    	<?php printf(
    		__( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> in %3$s', 'swiftframework' ),
    		$post_author,
    		get_author_posts_url( get_the_author_meta( 'ID' ) ),
    		$post_categories
    	); ?>
    </div>
<?php } ?>