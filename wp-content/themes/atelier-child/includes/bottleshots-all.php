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
        <?php foreach ($ids as $key => $id) { ?>
          <li class="bottleshots-list-item col-sm-3">
            <?php echo get_the_post_thumbnail($id, 'medium'); ?>
            <p class="bottleshots-title"><?php echo get_the_title($id); ?></p>
          </li>
        <?php } ?>
      </ul>
    </section>

  <?php } ?>

</section>