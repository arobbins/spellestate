<img src="<?php the_field('global_header_image', 'options'); ?>" alt="" />

<?php

  $current_vineyard_id = get_the_id();
  $wines = get_wines($current_vineyard_id);

?>

<div class="container grower-single">
  <h1><?php the_field('grower_title'); ?></h1>
  <img src="<?php the_field('grower_image_full'); ?>" alt="<?php the_field('grower_title'); ?>">
  <dl class="grower-details">
    <dt>Owner:</dt>
    <dd><?php the_field('grower_owner'); ?></dd>
    <dt>Location/Region/AVA:</dt>
    <dd><?php the_field('grower_location'); ?></dd>
    <dt>Acres:</dt>
    <dd><?php the_field('grower_acres'); ?></dd>
    <dt>Varietals Planted:</dt>
    <dd><?php the_field('grower_varietals'); ?></dd>
    <dt>Clones:</dt>
    <dd><?php the_field('grower_clones'); ?></dd>
    <dt>Soil Type:</dt>
    <dd><?php the_field('grower_soil'); ?></dd>
    <dt>Aspect:</dt>
    <dd><?php the_field('grower_aspect'); ?></dd>
    <dt>Climate/Microclimate:</dt>
    <dd><?php the_field('grower_climate'); ?></dd>
    <dt>Farming Practices:</dt>
    <dd><?php the_field('grower_farming_practices'); ?></dd>
  </dl>
  <blockquote><?php the_field('grower_quote'); ?></blockquote>
  <div class="grower-description">
    <?php the_field('grower_description'); ?>
  </div>

  <?php if(isset($wines) && $wines) { ?>
  <ul class="grower-wines row">

    <h2>Wines from this Vineyard:</h2>
    <?php
      foreach ($wines as $wine => $wineID) {

        $location = get_field('product_location', $wineID);
        $name = get_field('product_name', $wineID);
        $category = wp_get_post_terms( $wineID, 'product_cat' );

        $title = get_the_title($wineID);
        $link = get_the_permalink($wineID);
        $image = get_the_post_thumbnail($wineID, 'medium');

        echo '<li class="col-sm-3 grower-wine">';
          echo '<a href="' . $link .'" class="grower-wine-link">';
            echo $image;
            echo '<h3 class="grower-wine-title product-name">' . $name;
              echo '<p class="product-info product-category">' . $category[0]->name . '</p>';
              echo '<p class="product-info product-location">' . $location . '</p>';
            echo '</h3>';
          echo '</a>';
        echo "</li>";

      }
    ?>
  </ul>
  <?php } ?>

</div>