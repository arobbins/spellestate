<section class="reviews">

  <h1>Reviews <small>(by vintage)</small></h1>

  <?php

    $startingYear = 2007;
    $endingYear = date("Y");

    $years = range($startingYear, $endingYear);

    $reviewList = [];
    $listofIDs = [];

    query_posts(array(
      'post_type' => 'reviews',
      'showposts' => -1
    ));

  ?>

  <?php

    while (have_posts()) : the_post();

      $reviewId = get_the_id();
      $reviewVintage = get_field('reviews_vintage', get_the_id());
      $reviewWine = get_field('reviews_wine', get_the_id());

      if(in_array($reviewVintage, $years)) {
        $reviewList[$reviewVintage][] = $reviewId;
      }

    endwhile;

    //
    // Sorting descendingly
    //
    krsort($reviewList);

    //
    // Creating our new array to store the items
    //
    $finalArray = [];

    foreach ($reviewList as $year => $array) {

      $finalArray[$year] = [];

      foreach ($array as $key => $reviewID) {

        $wine = get_field('reviews_wine', $reviewID);
        $wineId = $wine[0]->ID;

        $finalArray[$year][$wineId][] = $reviewID;

      }
    }

  ?>

  <ul class="reviews-list">

    <?php foreach ($finalArray as $year => $wine) { ?>
    <li class="accordion review">

      <span class="accordion-heading review-vintage"><i class="fa fa-chevron-right"></i> <?php echo $year; ?></span>
      <dl class="accordion-content">

        <?php foreach ($wine as $wineID => $reviews) { ?>

          <dt class="review-wine"><?php echo get_the_title($wineID); ?></dt>

          <?php foreach ($reviews as $review => $reviewID) { ?>
            <dd class="review-description">
              <p class="review-organization"><?php the_field('reviews_organization', $reviewID); ?>
                <small class="review-score">
                <?php
                  if(get_field('reviews_score', $reviewID)) {
                    the_field('reviews_score', $reviewID);
                  }
                ?>
                </small>
              </p>

              <?php the_field('reviews_description', $reviewID); ?>

            </dd>
          <?php } ?>

        <?php } ?>

      </dl>

    </li>
    <?php } ?>

  </ul>

  <?php



  ?>

</section>

<?php wp_reset_query(); ?>