<section class="pointofsale">
  <h1>Point of sale <small>(.PDF)</small></h1>

  <?php if( have_rows('global_shelf_talkers', 'option') ): ?>
    <ul>
    <?php while( have_rows('global_shelf_talkers', 'option') ): the_row(); ?>
      <li>
        <a href="<?php the_sub_field('global_shelf_talker_pdf'); ?>"><?php the_sub_field('global_shelf_talker_name'); ?></a>
      </li>
    <?php endwhile; ?>
      <li>
        <a href="<?php the_field('global_coasters', 'option'); ?>"><?php the_field('global_coasters_title', 'option'); ?></a>
      </li>
    </ul>
  <?php endif; ?>

</section>