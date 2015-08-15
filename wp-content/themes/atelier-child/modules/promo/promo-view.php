<section class="module module-promo">
  <div class="grid promo-wrapper">
    <?php if(have_rows('promo_content')):
      while (have_rows('promo_content')) : the_row(); ?>
      <div class="unit one-third promo">
        <img src="<?php the_sub_field('promo_image'); ?>" alt="" class="promo-img">
        <a href="<?php the_sub_field('promo_link'); ?>">
          <h1 class="promo-title"><?php the_sub_field('promo_title'); ?></h1>
        </a>
        <p class="promo-copy"><?php the_sub_field('promo_copy'); ?></p>
      </div>
      <?php endwhile;
    endif; ?>
  </div>
</section>