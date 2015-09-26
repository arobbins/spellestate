<?php

	/*
	*
	*	Atelier Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*/

	/* LOAD PARENT THEME STYLES
	================================================== */
	function child_assets() {
    wp_enqueue_style('styles-local', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('scripts-stackable', get_stylesheet_directory_uri() . '/assets/js/vendor/stacktable.min.js', array(), '1.0.0', true);
    wp_enqueue_script('scripts-local', get_stylesheet_directory_uri() . '/assets/js/app.min.js', array(), '1.0.0', true);
	}

  add_action('wp_enqueue_scripts', 'child_assets', 15);

	/* LOAD THEME LANGUAGE
	================================================== */
	/*
	*	You can uncomment the line below to include your own translations
	*	into your child theme, simply create a "language" folder and add your po/mo files
	*/

	// load_theme_textdomain('swiftframework', get_stylesheet_directory().'/language');


	/* REMOVE PAGE BUILDER ASSETS
	================================================== */
	/*
	*	You can uncomment the line below to remove selected assets from the page builder
	*/

	// function spb_remove_assets( $pb_assets ) {
	//     unset($pb_assets['parallax']);
	//     return $pb_assets;
	// }
	// add_filter( 'spb_assets_filter', 'spb_remove_assets' );


	/* ADD/EDIT PAGE BUILDER TEMPLATES
	================================================== */
	function custom_prebuilt_templates($prebuilt_templates) {

		/*
		*	You can uncomment the lines below to add custom templates
		*/
		// $prebuilt_templates["custom"] = array(
		// 	'id' => "custom",
		// 	'name' => 'Custom',
		// 	'code' => 'your-code-here'
		// );

		/*
		*	You can uncomment the lines below to remove default templates
		*/
		// unset($prebuilt_templates['home-1']);
		// unset($prebuilt_templates['home-2']);

		// return templates array
	    return $prebuilt_templates;

	}
	//add_filter( 'spb_prebuilt_templates', 'custom_prebuilt_templates' );

//
// ACF Options page
//
if(function_exists('acf_add_options_page')) {

  $page = array(
    'page_title' => 'Global Options',
    'menu_title' => 'Global Options'
  );

  acf_add_options_page();
}

//
// Growers Shortcode
//
function show_growers() {

  get_template_part('includes/growers-all');

}
add_shortcode('growers', 'show_growers');

//
// Press Shortcode
//
function show_press() {

  get_template_part('includes/press-all');

}
add_shortcode('press', 'show_press');

//
// Reviews Shortcode
//
function show_reviews() {

  get_template_part('includes/reviews-all');

}
add_shortcode('reviews', 'show_reviews');

//
// Tech Sheets Shortcode
//
function show_techsheets() {

  get_template_part('includes/techsheets-all');

}
add_shortcode('techsheets', 'show_techsheets');

//
// Point of sale Shortcode
//
function show_point_of_sale() {

  get_template_part('includes/pointofsale-all');

}
add_shortcode('pointofsale', 'show_point_of_sale');

//
// Bottle Shots Shortcode
//
function show_bottle_shots() {

  get_template_part('includes/bottleshots-all');

}
add_shortcode('bottleshots', 'show_bottle_shots');

//
// Changing pricing copy for subscriptions
//
// function my_subs_price_string($subscription_string, $product, $include) {

//   $productData = get_post_meta($product->post->ID);
//   $productData = unserialize($productData['_subscription_payment_sync_date'][0]);

//   $month = $productData["month"];
//   $year  = date("Y");
//   $subscriptionStartDate = mktime(0,0,0, $month, 1, $year);
//   $now = strtotime(date('Y/m/d H:i:s'));

//   //
//   // If the the subscription for the current year has passed
//   //
//   if($now > $subscriptionStartDate) {
//     $date = $productData["month"] . '/' . $productData["day"] . '/' . date("Y", strtotime('+1 year'));

//   } else {
//     $date = $productData["month"] . '/' . $productData["day"] . '/' . date("Y");
//   }

//   $timestamp = strtotime($date);
//   $date = date("F j, Y", $timestamp);

//   return wc_price($product->subscription_price) . '<span class="sub-label"> per ' . $product->subscription_period . ' starting on ' . $date . '</span>';

// }
// add_filter('woocommerce_subscriptions_product_price_string', 'my_subs_price_string', 10, 3);

function get_reviews($productId) {

  $reviews = array();

  $args = array(
    'post_type' => 'reviews',
    'post_status' => 'publish',
    'posts_per_page' => -1
  );

  $query = new WP_Query($args);

  if($query->have_posts()) {

    while ($query->have_posts()) : $query->the_post();

      $postId = get_the_id();
      $reviewWine = get_field('reviews_wine', $postId);
      $reviewWineId = $reviewWine[0]->ID;

      if($reviewWineId === $productId) {
        $reviews[] = $postId;
      }

    endwhile;
  }

  wp_reset_query();

  return $reviews;

}

function get_wines($current_vineyard_id = '') {

  $wines = [];

  $args = array(
    'post_type' => 'product',
    'posts_per_page' => -1
  );

  $products = new WP_Query($args);

  if ( $products->have_posts() ) {
    while ( $products->have_posts() ) : $products->the_post();
      $product_id = get_the_ID();

      if(get_field('product_vineyard', $product_id)) {
        $vineyard = get_field('product_vineyard', $product_id);
        $vineyardId = $vineyard[0]->ID;

        if($vineyardId === $current_vineyard_id) {
          $wines[] = $product_id;
        }

      }

    endwhile;
  }

  wp_reset_postdata();

  return $wines;
}

//
// Hide Shipping based on the group
//
function hide_shipping_based_on_group($rates, $package) {

  if (is_user_logged_in()) {

    global $current_user;
    $current_user = wp_get_current_user();
    $userid = $current_user->ID;

    $user = new Groups_User($userid);
    $groups = $user->__get('groups');

    if ($groups[1] != NULL) {
      $groupObj = get_object_vars($groups[1]);
      $groupObjTarget = get_object_vars($groupObj[group]);
      $groupName = $groupObjTarget['name'];
      $sixName = "6 Bottle Club Level";
      $twelveName = "12 Bottle Club Level";

    } else {
      $groupName = "Registered";
    }

    if ($groupName == $sixName) {

      $myRate = $rates['6_bottle_club'];
        $rates  = array();
        $rates['6_bottle_club'] = $myRate;

    } else if ($groupName == $twelveName) {

      $myRate = $rates['12_bottle_club'];
        $rates  = array();
        $rates['12_bottle_club'] = $myRate;

    } else {

      unset($rates['6_bottle_club']);
      unset($rates['12_bottle_club']);
    }

  } else {

    unset($rates['6_bottle_club']);
    unset($rates['12_bottle_club']);

  }

  return $rates;
}

add_filter('woocommerce_package_rates', 'hide_shipping_based_on_group', 10, 2);


/**
 * 6 Bottle Wine Club Shipping method
 *
 * @class     WC_6_Bottle_Club_Shipping
 * @version   2.2.0
 * @package   WooCommerce/Classes/Shipping
 * @author    Andrew Robbins (https://simpleblend.net)
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    // 6 Bottle Club
    if (! class_exists('WC_6_Bottle_Club_Shipping_Method')) {

      function six_bottle_club_shipping_method_init() {

        class WC_6_Bottle_Club_Shipping_Method extends WC_Shipping_Method {
          /**
           * Constructor for your shipping class
           *
           * @access public
           * @return void
           */
          public function __construct() {

            global $woocommerce;

            // Id for your shipping method. Should be unique.
            $this->id  = '6_bottle_club';
            // Title shown in admin
            $this->title = __('6 Bottle Club Shipping Method');
            $this->six_bottle_rate_option = '6_bottle_club';
            // Description shown in admin
            $this->method_description = __('');
            $this->title = "6 Bottle Club Rate";
            // Actions
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

            $this->init();

          }

          /**
            * Init your settings
            *
            * @access public
            * @return void
          */
          public function init() {

            // Load the form fields.
            $this->init_form_fields();

            // Load the form settings.
            $this->init_settings();

            $this->enabled  = $this->get_option('enabled');
            $this->type     = $this->settings['type'];
            $this->cost     = $this->settings['cost'];
          }

          function init_form_fields() {

            global $woocommerce;

            $this->form_fields = array(
                'enabled'   => array(
                'title'     => __( 'Enable', 'woocommerce' ),
                'type'      => 'checkbox',
                'label'     => __( 'Enable 6 Bottle Club Shipping', 'woocommerce' ),
                'default'   => 'no'
              ),
              'amount'      => array(
                'title'       => 'Shipping amount',
                'description' => '',
                'type'        => 'price',
                'placeholder' => wc_format_localized_price(0),
                'class'       => 'Test'
              )
            );
          }

          /**
            * calculate_shipping function.
            *
            * @access public
            * @param mixed $package
            * @return void
          */
          public function calculate_shipping($package) {
            $rate = array(
              'id' => $this->id,
              'label' => $this->title,
              'cost' => $this->settings['amount'],
              'calc_tax' => 'per_item'
            );

            // Register the rate
            $this->add_rate($rate);
          }
        }
      }
    }

    add_action('woocommerce_shipping_init', 'six_bottle_club_shipping_method_init');

    function add_6_bottle_club_method( $methods ) {
      $methods[] = 'WC_6_Bottle_Club_Shipping_Method';
      return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'add_6_bottle_club_method' );
}


/**
 *
 * 12 Bottle Wine Club Shipping method
 *
 * @class     WC_12_Bottle_Club_Shipping
 * @version   2.2.0
 * @package   WooCommerce/Classes/Shipping
 * @author    Andrew Robbins (http://simpleblend.net)
 */
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    // 12 Bottle Club
    if (! class_exists('WC_12_Bottle_Club_Shipping_Method')) {

      function twelve_bottle_club_shipping_method_init() {

        class WC_12_Bottle_Club_Shipping_Method extends WC_Shipping_Method {
          /**
           * Constructor for your shipping class
           *
           * @access public
           * @return void
           */
          public function __construct() {

            global $woocommerce;

            // ID for the shipping method. Should be unique.
            $this->id  = '12_bottle_club';

            // Title shown in admin
            $this->title = __('12 Bottle Club Shipping Method');
            $this->twelve_bottle_rate_option = '12_bottle_club';

            // Description shown in admin
            $this->method_description = __('');
            $this->title = "12 Bottle Club Rate";

            // Actions
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options') );

            $this->init();

          }

          /**
            * Init your settings
            *
            * @access public
            * @return void
          */
          public function init() {

            // Load the form fields.
            $this->init_form_fields();

            // Load the form settings.
            $this->init_settings();

            $this->enabled  = $this->get_option('enabled');
            $this->type     = $this->settings['type'];
            $this->cost     = $this->settings['cost'];

          }

          function init_form_fields() {

            global $woocommerce;

            $this->form_fields = array(
              'enabled'   => array(
              'title'     => __( 'Enable', 'woocommerce' ),
              'type'      => 'checkbox',
              'label'     => __( 'Enable 12 Bottle Club Shipping', 'woocommerce' ),
              'default'   => 'no'
            ),
            'amount'  => array(
                'title'       => 'Shipping amount',
                'description' => '',
                'type'        => 'price',
                'placeholder' => wc_format_localized_price(0),
                'class'       => 'Test'
              )
            );
          }

          /**
            * calculate_shipping function.
            *
            * @access public
            * @param mixed $package
            * @return void
          */
          public function calculate_shipping( $package ) {
            $rate = array(
              'id' => $this->id,
              'label' => $this->title,
              'cost' => $this->settings['amount'],
              'calc_tax' => 'per_item'
            );

            // Register the rate
            $this->add_rate( $rate );
          }
        }
      }
    }

    add_action('woocommerce_shipping_init', 'twelve_bottle_club_shipping_method_init');

    function add_12_bottle_club_method( $methods ) {
      $methods[] = 'WC_12_Bottle_Club_Shipping_Method';
      return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'add_12_bottle_club_method' );
}


add_action('wp', 'init');

function init() {

  if (is_cart() || is_checkout()) {

    //
    // Changing pricing copy for subscriptions
    //
    function my_subs_price_string($subscription_string, $product, $include) {
      return wc_price($product->subscription_price) . '<small> / year</small>';
    }

    add_filter('woocommerce_subscriptions_product_price_string', 'my_subs_price_string', 10, 3);

  } else {

    //
    // Changing pricing copy for subscriptions
    //
    function my_subs_price_string($subscription_string, $product, $include) {

      $productData = get_post_meta($product->post->ID);
      $productData = unserialize($productData['_subscription_payment_sync_date'][0]);

      $month = $productData["month"];
      $year  = date("Y");
      $subscriptionStartDate = mktime(0,0,0, $month, 1, $year);
      $now = strtotime(date('Y/m/d H:i:s'));

      //
      // If the the subscription for the current year has passed
      //
      if($now > $subscriptionStartDate) {
        $date = $productData["month"] . '/' . $productData["day"] . '/' . date("Y", strtotime('+1 year'));

      } else {
        $date = $productData["month"] . '/' . $productData["day"] . '/' . date("Y");
      }

      $timestamp = strtotime($date);
      $date = date("F j, Y", $timestamp);

      // return wc_price($product->subscription_price) . '<span class="sub-label"> per ' . $product->subscription_period . ' starting on ' . $date . '</span>';

      return wc_price($product->subscription_price) . '<span class="sub-label">per year</span>';

    }

    add_filter('woocommerce_subscriptions_product_price_string', 'my_subs_price_string', 10, 3);


  }


}