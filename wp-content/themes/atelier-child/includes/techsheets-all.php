<section class="techsheets">
  <h1>Tech Sheets <small>(.pdf)</small></h1>
  <?php

    query_posts(array(
      'post_type' => 'product',
      'showposts' => -1
    ));

  ?>
  <ul>
  <?php

    while (have_posts()) : the_post();

      $techsheetURL = get_field('product_techsheet', get_the_id());

      if(get_field('product_techsheet', get_the_id())) { ?>
      <li>
        <a href="<?php echo $techsheetURL; ?>"><?php echo the_title(); ?></a>
      </li>
      <?php }

    endwhile;

  ?>
  </ul>
</section>

<?php wp_reset_query(); ?>