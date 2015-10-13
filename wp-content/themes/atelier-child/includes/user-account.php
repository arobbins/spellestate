<div class="container">
  <section class="user-account">
    <?php if (is_user_logged_in()) {

      $current_user = wp_get_current_user();

       echo '<span>Welcome, ' . $current_user->user_login . ' <a href="'. wp_logout_url(home_url()) .'" class="user-account-link">Logout <i class="fa fa-sign-out"></i></a></span>';

    } else {

      echo '<span><a href="'. site_url('/my-account') .'" class="user-account-link">Login or register</a></span>';


    } ?>
  </section>
</div>