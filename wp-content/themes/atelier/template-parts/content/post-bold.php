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
	
	$header_right_output = sf_header_aux( 'right' );
?>

<div class="bold-item-wrap">

<?php if ( $show_title == "yes" && $post_format != "quote" && $post_format != "link" ) { ?>
	<h1 itemprop="name headline"><a ' . $post_permalink_config . '>' . $post_title . '</a></h1>
<?php } else if ( $post_format == "quote" ) { ?>
	<div class="quote-excerpt" itemprop="name headline"><a ' . $post_permalink_config . '>' . $post_excerpt . '</a></div>
<?php } else if ( $post_format == "link" ) { ?>
	<h3 itemprop="name headline"><a ' . $post_permalink_config . '>' . $post_title . '</a></h3>
<?php } ?>

<?php if ( $show_excerpt == "yes" && $post_format != "quote" ) { ?>
<div class="excerpt" itemprop="description">' . $post_excerpt . '</div>
<?php } ?>

<?php if ( $show_details == "yes" ) { ?>
	<?php if ( $single_author && !$remove_dates ) { ?>
	    <div class="blog-item-details">' . sprintf( __( '<span>In %1$s</span> <time class="date" datetime="%2$s">%3$s</time>', 'swiftframework' ), $post_categories, $post_date_str, $post_date ) . '</div>
	<?php } else if ( ! $remove_dates ) { ?>
	    <div class="blog-item-details">' . sprintf( __( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> <span>in %3$s</span> <time class="date" datetime="%4$s">%5$s</time>', 'swiftframework' ), $post_author, get_author_posts_url( get_the_author_meta( 'ID' ) ), $post_categories, $post_date_str, $post_date ) . '</div>
	<?php } else if ( ! $single_author ) { ?>
	    <div class="blog-item-details">' . sprintf( __( '<span class="author">By <a href="%2$s" rel="author" itemprop="author">%1$s</a></span> <span>in %3$s</span>', 'swiftframework' ), $post_author, get_author_posts_url( get_the_author_meta( 'ID' ) ), $post_categories ) . '</div>
	<?php } ?>
<?php } ?>

</div>