<?php

  include('includes/user-account.php');
  get_header();

?>

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
	global $sf_options;
	$blog_type = $sf_options['archive_display_type'];

	if ($blog_type == "masonry" || $blog_type == "masonry-fw") {
		global $sf_include_imagesLoaded;
		$sf_include_imagesLoaded = true;
	}

	global $sf_has_blog;
	$sf_has_blog = true;
?>

<?php if ($blog_type != "masonry-fw") { ?>
<div class="container">
<?php } ?>

	<?php if( is_home() && get_option('page_for_posts') ) : ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php echo apply_filters('the_title',get_page( get_option('page_for_posts') )->post_title); ?></h1>
	</header>
	<?php endif; ?>


	<?php sf_base_layout('archive'); ?>

	<?php

	  if(have_rows('modules', get_option('page_for_posts'))):

	    while(have_rows('modules', get_option('page_for_posts'))) : the_row();

	      // Promo
	      if(get_row_layout() == 'module_promo'):

	        get_template_part('modules/promo/promo-view');

	      endif;

	      // Default
	      if(get_row_layout() == 'module_default'):

	        get_template_part('modules/default/default-view');

	      endif;

	    endwhile;

	  else:

	  endif;
	?>

<?php if ($blog_type != "masonry-fw") { ?>
</div>
<?php } ?>

<?php get_footer(); ?>
