<?php get_header(); ?>

<!--
  Showing custom header
-->

<?php

  //
  // Mobile Detect
  //
  require_once 'mobile-detect/Mobile_Detect.php';
  $detect = new Mobile_Detect;

  if ($detect->isMobile() && !$detect->isTablet()) {

    if(get_field('show_custom_header')) { ?>
      <img src="<?php the_field('global_header_image_mobile', 'options'); ?>" alt="Spellestate" />
    <?php }

  } else {

    if(get_field('show_custom_header')) { ?>
      <img src="<?php the_field('global_header_image', 'options'); ?>" alt="Spellestate" />
    <?php }
  }

?>

<?php
  $sidebar_config = sf_get_post_meta($post->ID, 'sf_sidebar_config', true);
  if (isset($_GET['sidebar'])) {
    $sidebar_config = $_GET['sidebar'];
  }
  $pb_fw_mode = true;
  $pb_active = sf_get_post_meta($post->ID, '_spb_js_status', true);
  if ($sidebar_config != "no-sidebars" || $pb_active != "true" || post_password_required() ) {
    $pb_fw_mode = false;
  }

?>

<?php
  // Check if page should be enabled in full width mode
  if (!$pb_fw_mode) { ?>
  <div class="container">
<?php } ?>

  <?php sf_base_layout('page'); ?>

<?php
  // Check if page should be enabled in full width mode
  if (!$pb_fw_mode) { ?>
  </div>
<?php } ?>

<?php
  if(have_rows('modules')):

    while(have_rows('modules')) : the_row();

      // Promo
      if(get_row_layout() == 'module_promo'):

        get_template_part('modules/promo/promo-view');

      endif;

    endwhile;

  else:

  endif;
?>

<?php get_footer(); ?>