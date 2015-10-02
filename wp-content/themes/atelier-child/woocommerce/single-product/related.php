<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $sf_options, $woocommerce_loop, $sf_carouselID;

$related = $product->get_related(12);

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters('woocommerce_related_products_args', array(
	'post_type'				=> 'product',
	'ignore_sticky_posts'	=> 1,
	'no_found_rows' 		=> 1,
	'posts_per_page' 		=> 12,
	'orderby' 				=> $orderby,
	'post__in' 				=> $related,
	'post__not_in'			=> array($product->id)
) );

$products = new WP_Query( $args );

//$woocommerce_loop['columns'] = $columns;
$woocommerce_loop['columns'] = 4;

if ($sf_carouselID == "") {
$sf_carouselID = 1;
} else {
$sf_carouselID++;
}

$product_display_type = $sf_options['product_display_type'];
$product_display_gutters = $sf_options['product_display_gutters'];

$gutter_class = "";

if (!$product_display_gutters && $product_display_type == "gallery") {
	$gutter_class = 'no-gutters';
} else {
	$gutter_class = 'gutters';
}

$related_heading = __( $sf_options['related_heading_text'] , 'swiftframework' );

if ( $products->have_posts() ) : ?>

	<div class="product-carousel related-products spb_content_element">

		<div class="title-wrap clearfix">
			<h3 class="spb-heading"><?php echo esc_attr($related_heading); ?></h3>
			<div class="carousel-arrows"><a href="#" class="carousel-prev"><i class="sf-icon-chevron-prev"></i></a><a href="#" class="carousel-next"><i class="sf-icon-chevron-next"></i></a></div>
		</div>

		<div class="related products carousel-items <?php echo esc_attr($gutter_class); ?> product-type-<?php echo esc_attr($product_display_type); ?>" id="carousel-<?php echo esc_attr($sf_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php
          $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID));

          global $product;
          global $post;

          $categories = wp_get_post_terms($post->ID, 'product_cat', array('taxonomy' => 'product_cat'));

        ?>
        <a href="<?php the_permalink(); ?>" class="product-link">
          <img src="<?php echo $feat_image; ?>" alt="<?php the_title(); ?>">

          <h3 class="product-title">
            <p class="product-info product-name"><?php the_field('product_name'); ?></p>
            <p class="product-info product-category"><?php echo $categories[0]->name; ?></p>
            <p class="product-info product-location"><?php the_field('product_location'); ?></p>
          </h3>

          <?php
            if($price_html = $product->get_price_html()) :
              echo $price_html;
            endif;
          ?>
        </a>

			<?php endwhile; // end of the loop. ?>

		</div>

	</div>

<?php endif;

global $sf_include_carousel, $sf_include_isotope;
$sf_include_carousel = true;
$sf_include_isotope = true;

wp_reset_postdata();
