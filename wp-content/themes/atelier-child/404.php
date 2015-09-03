<?php get_header(); ?>

<?php

  //
  // Mobile Detect
  //
  require_once 'mobile-detect/Mobile_Detect.php';
  $detect = new Mobile_Detect;

  if ($detect->isMobile() && !$detect->isTablet()) { ?>
    <img src="<?php the_field('global_header_image_mobile', 'options'); ?>" alt="Spellestate" />

  <?php } else { ?>
    <img src="<?php the_field('global_header_image', 'options'); ?>" alt="Spellestate" />

  <?php }

?>

<div class="container">

  <?php sf_base_layout('404'); ?>

</div>

<?php get_footer(); ?>