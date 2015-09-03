<section class="press">

  <h1>Press</h1>

  <?php
    query_posts(array(
      'post_type' => 'press',
      'showposts' => -1
    ));
  ?>

  <ul class="press-group">

  <?php while (have_posts()) : the_post(); ?>
    <li class="press-single">
      <p class="press-title">
        <span class="press-organization"><?php the_field('press_organization', get_the_id()); ?></span>
        <a href="<?php the_field('press_link', get_the_id()); ?>" class="press-link"><?php the_field('press_title', get_the_id()); ?></a>
      </p>
      <p class="press-author">
        <?php the_field('press_author', get_the_id()); ?>
        <?php
          if(get_field('press_date', get_the_id())) {
            echo "(" . get_field('press_date', get_the_id()) . ")";
          }
        ?>
      </p>
    </li>
  <?php endwhile; ?>

  </ul>

</section>

<?php wp_reset_query(); ?>