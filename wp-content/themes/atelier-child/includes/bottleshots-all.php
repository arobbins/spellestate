<section class="bottleshots">
  <h1>Bottleshots</h1>

  <?php

    $startingYear = 2007;
    $endingYear = date("Y");

    $years = range($startingYear, $endingYear);

    $mainList = [];
    $listofIDs = [];

    query_posts(array(
      'post_type' => 'product',
      'showposts' => -1
    ));

  ?>

  <?php

    while (have_posts()) : the_post();

      $productId = get_the_id();
      $productVintage = get_field('product_vintage', get_the_id());

      if(in_array($productVintage, $years)) {
        $mainList[$productVintage][] = $productId;
      }

    endwhile;

    //
    // Sorting descendingly
    //
    krsort($mainList);

  ?>

  <?php foreach ($mainList as $year => $ids) { ?>

    <section class="row">
      <h2 class="bottleshots-vintage"><?php echo $year; ?></h2>
      <ul class="bottleshots-list">
        <?php
          foreach ($ids as $key => $id) {
            global $post;
            $categories = wp_get_post_terms($id, 'product_cat', array('taxonomy' => 'product_cat'));
        ?>
        <li class="bottleshots-list-item col-sm-3">
          <?php echo get_the_post_thumbnail($id, 'medium'); ?>
          <p class="product-info product-name"><?php the_field('product_name', $id); ?></p>
          <p class="product-info product-category"><?php echo $categories[0]->name; ?></p>
          <p class="product-info product-location"><?php the_field('product_location', $id); ?></p>
        </li>
        <?php } ?>
      </ul>
    </section>

  <?php } ?>

</section>