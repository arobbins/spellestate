<?php if(get_field('product_tech_sheet', get_the_id()) && get_field('product_tech_sheet', get_the_id())) { ?>

<dl class="accordion product-techsheet">
  <dt class="accordion-heading">Techsheet <i class="fa fa-chevron-right"></i></dt>
  <dd class="accordion-content">
    <?php if(get_field('product_tech_sheet', get_the_id())) { ?>
      <a href="<?php the_field('product_tech_sheet', get_the_id()); ?>"><?php the_title(); ?> (.PDF)</a>
    <?php } else { ?>
      <p>No Techsheet available.</p>
    <?php } ?>
  </dd>
</dl>

<?php } ?>