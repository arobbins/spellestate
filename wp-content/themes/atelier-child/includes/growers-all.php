<div class="growers row">

  <?php
    query_posts(array(
      'post_type' => 'growers',
      'showposts' => -1
    ));
  ?>

  <?php while (have_posts()) : the_post(); ?>
    <section class="grower col-sm-4">
      <a href="<?php the_permalink(); ?>" class="grower-link">
        <img src="<?php the_field('grower_thumbnail', get_the_id()); ?>" alt="" class="grower-image">
        <h1 class="grower-title"><?php the_field('grower_title', get_the_id()); ?></h1>
      </a>
    </section>
  <?php endwhile; ?>

</div>

<?php wp_reset_query(); ?>