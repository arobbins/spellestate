<section class="techsheets">
  <h1>Tech Sheets <small>(.pdf)</small></h1>

  <?php

    $args = array(
      'post_type' => 'product',
      'posts_per_page' => -1
    );

    $loop = new WP_Query($args);
    $ids = array();

    if ( $loop->have_posts() ) {
      while ( $loop->have_posts() ) : $loop->the_post();

        if(get_post_status(get_the_id()) != 'private') {
          $ids[] = get_the_id();
        }

      endwhile;
    };

    wp_reset_postdata();



  ?>

  <ul>
    <?php

      $store = array();

      foreach ($ids as $key => $id) {

        $store[get_the_title($id)] = $id;

      }

      krsort($store);

      foreach ($store as $key => $val) {

        $techsheetURL = get_field('product_tech_sheet', $val);

        if($techsheetURL) { ?>

          <li>
            <a href="<?php echo $techsheetURL; ?>"><?php echo get_the_title($val); ?></a>
          </li>

        <?php }

      }


    ?>
  </ul>
</section>