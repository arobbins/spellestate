<?php

  $productId = get_the_id();
  $reviews = get_reviews($productId);

?>

<?php if(isset($reviews) && $reviews) { ?>

<dl class="accordion product-reviews">
  <dt class="accordion-heading">Reviews <i class="fa fa-chevron-right"></i></dt>
  <dd class="accordion-content">
    <?php if(isset($reviews) && $reviews) { ?>
      <ul class="reviews-list">
        <?php foreach ($reviews as $review => $reviewId) { ?>
          <li>
            <dl>
              <dt class="review-wine">
                <?php the_field('reviews_organization', $reviewId); ?>
                <?php if(get_field('reviews_score', $reviewId)) { ?>
                  <small class="review-score">
                    <?php the_field('reviews_score', $reviewId); ?>
                  </small>
                <?php } ?>
              </dt>
              <dd class="review-description">
                <?php the_field('reviews_description', $reviewId); ?>
              </dd>
            </dl>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>

      <p>No reviews available</p>

    <?php } ?>
  </dd>
</dl>

<?php } ?>