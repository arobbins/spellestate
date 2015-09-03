<img src="<?php the_field('global_header_image', 'options'); ?>" alt="" />
<div class="container">
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
</div>