<?php
    global $sf_options;
    $error_content = __( $sf_options['404_page_content'], 'swiftframework' );
?>

<div class="help-text">
    <?php echo $error_content; ?>
</div>
<form method="get" class="search-form" action="<?php echo home_url(); ?>/">
    <input type="text" placeholder="<?php _e( "Search", "swiftframework" ); ?>" name="s"/>
</form>