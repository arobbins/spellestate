<?php
    sf_set_sidebar_global( 'no-sidebars' );
?>

<?php if ( have_posts() ) : the_post(); ?>

    <!-- OPEN article -->
    <article <?php post_class( 'clearfix' ); ?> id="<?php the_ID(); ?>">

        <section class="page-content clearfix container">

            <div class="content-wrap <?php echo esc_attr($content_wrap_class); ?> clearfix">
                <?php echo atcf_shortcode_submit( array(
                    'editing'    => is_preview() ? false : true,
                    'previewing' => is_preview() ? true : false
                ) ); ?>
            </div>

        </section>

        <!-- CLOSE article -->
    </article>

<?php endif; ?>