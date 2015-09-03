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
    <div class="mini-item-details">
    	<?php printf(
    		__( 'in %1$s / <time datetime="%2$s">%3$s</time> / %4$s comments', 'swiftframework' ),
    		$post_categories,
    		$post_date_str,
    		$post_date,
    		$post_comments
    	); ?>
    </div>
<?php } else if ( ! $remove_dates ) { ?>
    <div class="mini-item-details">
    	<?php printf(
    		__( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> in %3$s / <time datetime="%4$s">%5$s</time> / %6$s comments', 'swiftframework' ),
    		$post_author,
    		get_author_posts_url( get_the_author_meta( 'ID' ) ),
    		$post_categories,
    		$post_date_str,
    		$post_date,
    		$post_comments
    	); ?>
    </div>
<?php } else if ( ! $single_author ) { ?>
    <div class="mini-item-details">
    	<?php printf(
    		__( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> / %3$s / %4$s comments', 'swiftframework' ),
    		$post_author,
    		get_author_posts_url( get_the_author_meta( 'ID' ) ),
    		$post_categories,
    		$post_comments
    	); ?>
    </div>
<?php }