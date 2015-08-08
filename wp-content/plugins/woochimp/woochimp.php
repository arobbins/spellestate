<?php

/**
 * Plugin Name: WooChimp
 * Plugin URI: http://www.rightpress.net/woochimp
 * Description: MailChimp WooCommerce Integration
 * Version: 1.4.3
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 * Requires at least: 3.5
 * Tested up to: 4.1
 * 
 * Text Domain: woochimp
 * Domain Path: /languages
 * 
 * @package WooChimp
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('WOOCHIMP_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('WOOCHIMP_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('WOOCHIMP_VERSION', '1.4.3');

if (!class_exists('WooChimp')) {

    /**
     * Main plugin class
     * 
     * @package WooChimp
     * @author RightPress
     */
    class WooChimp
    {

        /**
         * Class constructor
         * 
         * @access public
         * @return void
         */
        public function __construct()
        {
            $this->mailchimp = null;

            // Load translation
            load_plugin_textdomain('woochimp', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            // Load classes/includes
            require WOOCHIMP_PLUGIN_PATH . '/classes/woochimp-mailchimp-subscription.class.php';
            require WOOCHIMP_PLUGIN_PATH . '/includes/woochimp-plugin-structure.inc.php';
            require WOOCHIMP_PLUGIN_PATH . '/includes/woochimp-form.inc.php';

            // Load configuration and current settings
            $this->get_config();
            $this->opt = $this->get_options();

            // Hook into WordPress
            if (is_admin()) {
                add_action('admin_menu', array($this, 'add_admin_menu'));
                add_action('admin_init', array($this, 'admin_construct'));
                add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugin_settings_link'));

                if (preg_match('/page=woochimp/i', $_SERVER['QUERY_STRING'])) {
                    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
                }
            }
            else {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
            }

            // Widgets
            add_action('widgets_init', create_function('', 'return register_widget("WooChimp_MailChimp_Signup");'));

            // Shortcodes
            add_shortcode('woochimp_form', array($this, 'subscription_shortcode'));

            // Hook into WooCommerce
            add_action('woocommerce_checkout_update_order_meta', array($this, 'on_checkout'));
            add_action('woocommerce_order_status_completed', array($this, 'on_completed'));

            $checkbox_position = (isset($this->opt['woochimp_checkbox_position']) && !empty($this->opt['woochimp_checkbox_position'])) ? $this->opt['woochimp_checkbox_position'] : 'woocommerce_checkout_after_customer_details';
            add_action($checkbox_position, array($this, 'add_permission_question'));

            // Delete settings on plugin removal
            register_uninstall_hook(__FILE__, array('WooChimp', 'uninstall'));

            // Define Ajax handlers
            add_action('wp_ajax_woochimp_mailchimp_status', array($this, 'ajax_mailchimp_status'));
            add_action('wp_ajax_woochimp_get_lists_with_multiple_groups_and_fields', array($this, 'ajax_lists_for_checkout'));
            add_action('wp_ajax_woochimp_get_lists', array($this, 'ajax_lists_in_array'));
            add_action('wp_ajax_woochimp_update_groups_and_tags', array($this, 'ajax_groups_and_tags_in_array'));
            add_action('wp_ajax_woochimp_update_checkout_groups_and_tags', array($this, 'ajax_groups_and_tags_in_array_for_checkout'));
            add_action('wp_ajax_woochimp_subscribe_shortcode', array($this, 'ajax_subscribe_shortcode'));
            add_action('wp_ajax_woochimp_subscribe_widget', array($this, 'ajax_subscribe_widget'));
            add_action('wp_ajax_nopriv_woochimp_subscribe_shortcode', array($this, 'ajax_subscribe_shortcode'));
            add_action('wp_ajax_nopriv_woochimp_subscribe_widget', array($this, 'ajax_subscribe_widget'));

            // Catch mc_cid & mc_eid (MailChimp Campaign ID and MailChimp Email ID)
            add_action('init', array($this, 'track_campaign'));

            // Intercept Webhook call
            if (isset($_GET['woochimp-webhook-call'])) {
                add_action('init', array($this, 'process_webhook'));
            }

            // Define form styles
            $this->form_styles = array(
                '2' => 'woochimp_skin_general',
            );

            // Load selected skins
            add_action('wp_enqueue_scripts', array($this, 'enqueue_form_skins'));

            // Define all properties available on checkout
            $this->checkout_properties = array(
                'order_billing_first_name' => __('Billing First Name', 'woochimp'),
                'order_billing_last_name' => __('Billing Last Name', 'woochimp'),
                'order_billing_company' => __('Billing Company', 'woochimp'),
                'order_billing_address_1' => __('Billing Address 1', 'woochimp'),
                'order_billing_address_2' => __('Billing Address 2', 'woochimp'),
                'order_billing_city' => __('Billing City', 'woochimp'),
                'order_billing_state' => __('Billing State', 'woochimp'),
                'order_billing_postcode' => __('Billing Postcode', 'woochimp'),
                'order_billing_country' => __('Billing Country', 'woochimp'),
                'order_billing_phone' => __('Billing Phone', 'woochimp'),
                'order_shipping_first_name' => __('Shipping First Name', 'woochimp'),
                'order_shipping_last_name' => __('Shipping Last Name', 'woochimp'),
                'order_shipping_address_1' => __('Shipping Address 1', 'woochimp'),
                'order_shipping_address_2' => __('Shipping Address 2', 'woochimp'),
                'order_shipping_city' => __('Shipping City', 'woochimp'),
                'order_shipping_state' => __('Shipping State', 'woochimp'),
                'order_shipping_postcode' => __('Shipping Postcode', 'woochimp'),
                'order_shipping_country' => __('Shipping Country', 'woochimp'),
                'order_shipping_method_title' => __('Shipping Method Title', 'woochimp'),
                'order_payment_method_title' => __('Payment Method Title ', 'woochimp'),
                'order_user_id' => __('User ID', 'woochimp'),
                'user_first_name' => __('User First Name', 'woochimp'),
                'user_last_name' => __('User Last Name', 'woochimp'),
                'user_nickname' => __('User Nickname', 'woochimp'),
                'user_paying_customer' => __('User Is Paying Customer', 'woochimp'),
                'user__order_count' => __('User Completed Order Count', 'woochimp'),
            );
        }

        /**
         * Loads/sets configuration values from structure file and database
         * 
         * @access public
         * @return void
         */
        public function get_config()
        {
            // Settings tree
            $this->settings = woochimp_plugin_settings();

            // Load some data from config
            $this->hints = $this->options('hint');
            $this->validation = $this->options('validation', true);
            $this->titles = $this->options('title');
            $this->options = $this->options('values');
            $this->section_info = $this->get_section_info();
            $this->default_tabs = $this->get_default_tabs();
        }

        /**
         * Get settings options: default, hint, validation, values
         * 
         * @access public
         * @param string $name
         * @param bool $split_by_page
         * @return array
         */
        public function options($name, $split_by_subpage = false)
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page => $page_value) {
                $page_options = array();

                foreach ($page_value['children'] as $subpage => $subpage_value) {
                    foreach ($subpage_value['children'] as $section => $section_value) {
                        foreach ($section_value['children'] as $field => $field_value) {
                            if (isset($field_value[$name])) {
                                $page_options['woochimp_' . $field] = $field_value[$name];
                            }
                        }
                    }

                    $results[preg_replace('/_/', '-', $subpage)] = $page_options;
                    $page_options = array();
                }
            }

            $final_results = array();

            // Do we need to split results per page?
            if (!$split_by_subpage) {
                foreach ($results as $value) {
                    $final_results = array_merge($final_results, $value);
                }
            }
            else {
                $final_results = $results;
            }

            return $final_results;
        }

        /**
         * Get default tab for each page
         * 
         * @access public
         * @return array
         */
        public function get_default_tabs()
        {
            $tabs = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page => $page_value) {
                reset($page_value['children']);
                $tabs[$page] = key($page_value['children']);
            }

            return $tabs;
        }

        /**
         * Get array of section info strings
         * 
         * @access public
         * @return array
         */
        public function get_section_info()
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page_value) {
                foreach ($page_value['children'] as $subpage => $subpage_value) {
                    foreach ($subpage_value['children'] as $section => $section_value) {
                        if (isset($section_value['info'])) {
                            $results[$section] = $section_value['info'];
                        }
                    }
                }
            }

            return $results;
        }

        /*
         * Get plugin options set by user
         * 
         * @access public
         * @return array
         */
        public function get_options()
        {
            $default_options = array_merge(
                $this->options('default'),
                array(
                    'woochimp_checkout_fields' => array(),
                    'woochimp_widget_fields' => array(),
                    'woochimp_shortcode_fields' => array(),
                )
            );

            $overrides = array(
                'woochimp_webhook_url' => site_url('/?woochimp-webhook-call'),
            );

            return array_merge(
                       $default_options,
                       get_option('woochimp_options', $this->options('default')),
                       $overrides
                   );
        }

        /*
         * Update options
         * 
         * @access public
         * @return bool
         */
        public function update_options($args = array())
        {
            return update_option('woochimp_options', array_merge($this->get_options(), $args));
        }

        /**
         * Add link to admin page
         * 
         * @access public
         * @return void
         */
        public function add_admin_menu()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            global $submenu;

            if (isset($submenu['woocommerce'])) {
                add_submenu_page(
                    'woocommerce',
                     $this->settings['woochimp']['page_title'],
                     $this->settings['woochimp']['title'],
                     $this->settings['woochimp']['capability'],
                     $this->settings['woochimp']['slug'],
                     array($this, 'set_up_admin_page')
                );
            }
        }

        /*
         * Set up admin page
         * 
         * @access public
         * @return void
         */
        public function set_up_admin_page()
        {
            // Print notices
            settings_errors();

            // Print page tabs
            $this->render_tabs();

            // Check for general warnings
            if (!$this->curl_enabled()) {
                add_settings_error(
                    'error_type',
                    'general',
                    sprintf(__('Warning: PHP cURL extension is not enabled on this server. cURL is required for this plugin to function correctly. You can read more about cURL <a href="%s">here</a>.', 'woochimp'), 'http://php.net/manual/en/book.curl.php')
                );
            }

            // Print page content
            $this->render_page();
        }

        /**
         * Admin interface constructor
         * 
         * @access public
         * @return void
         */
        public function admin_construct()
        {
            // Iterate subpages
            foreach ($this->settings['woochimp']['children'] as $subpage => $subpage_value) {

                register_setting(
                    'woochimp_opt_group_' . $subpage,            // Option group
                    'woochimp_options',                          // Option name
                    array($this, 'options_validate')             // Sanitize
                );

                // Iterate sections
                foreach ($subpage_value['children'] as $section => $section_value) {

                    add_settings_section(
                        $section,
                        $section_value['title'],
                        array($this, 'render_section_info'),
                        'woochimp-admin-' . str_replace('_', '-', $subpage)
                    );

                    // Iterate fields
                    foreach ($section_value['children'] as $field => $field_value) {

                        add_settings_field(
                            'woochimp_' . $field,                                     // ID
                            $field_value['title'],                                      // Title 
                            array($this, 'render_options_' . $field_value['type']),     // Callback
                            'woochimp-admin-' . str_replace('_', '-', $subpage), // Page
                            $section,                                                   // Section
                            array(                                                      // Arguments
                                'name' => 'woochimp_' . $field,
                                'options' => $this->opt,
                            )
                        );

                    }
                }
            }
        }

        /**
         * Render admin page navigation tabs
         * 
         * @access public
         * @param string $current_tab
         * @return void
         */
        public function render_tabs()
        {
            // Get current page and current tab
            $current_page = preg_replace('/woocommerce_page_/', '', $this->get_current_page_slug());
            $current_tab = $this->get_current_tab();

            // Output admin page tab navigation
            echo '<div class="woochimp-container">';
            echo '<div id="icon-woochimp" class="icon32 icon32-woochimp"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($this->settings as $page => $page_value) {
                if ($page != $current_page) {
                    continue;
                }

                foreach ($page_value['children'] as $subpage => $subpage_value) {
                    $class = ($subpage == $current_tab) ? ' nav-tab-active' : '';
                    echo '<a class="nav-tab'.$class.'" href="?page='.preg_replace('/_/', '-', $page).'&tab='.$subpage.'">'.((isset($subpage_value['icon']) && !empty($subpage_value['icon'])) ? $subpage_value['icon'] . '&nbsp;' : '').$subpage_value['title'].'</a>';
                }
            }
            echo '</h2>';
            echo '</div>';
        }

        /**
         * Get current tab (fallback to default)
         * 
         * @access public
         * @param bool $is_dash
         * @return string
         */
        public function get_current_tab($is_dash = false)
        {
            $tab = (isset($_GET['tab']) && $this->page_has_tab($_GET['tab'])) ? preg_replace('/-/', '_', $_GET['tab']) : $this->get_default_tab();

            return (!$is_dash) ? $tab : preg_replace('/_/', '-', $tab);
        }

        /**
         * Get default tab
         * 
         * @access public
         * @return string
         */
        public function get_default_tab()
        {
            $current_page_slug = $this->get_current_page_slug();
            return $this->default_tabs[$current_page_slug];
        }

        /**
         * Get current page slug
         * 
         * @access public
         * @return string
         */
        public function get_current_page_slug()
        {
            $current_screen = get_current_screen();
            $current_page = $current_screen->base;
            $current_page_slug = preg_replace('/woocommerce_page_/', '', $current_page);
            return preg_replace('/-/', '_', $current_page_slug);
        }

        /**
         * Check if current page has requested tab
         * 
         * @access public
         * @param string $tab
         * @return bool
         */
        public function page_has_tab($tab)
        {
            $current_page_slug = $this->get_current_page_slug();

            if (isset($this->settings[$current_page_slug]['children'][$tab]))
                return true;

            return false;
        }

        /**
         * Render settings page
         * 
         * @access public
         * @param string $page
         * @return void
         */
        public function render_page(){

            $current_tab = $this->get_current_tab(true);

            ?>
                <div class="wrap woochimp">
                    <div class="woochimp-container">
                        <div class="woochimp-left">
                            <form method="post" action="options.php" enctype="multipart/form-data">
                                <input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />

                                <?php
                                    settings_fields('woochimp_opt_group_'.preg_replace('/-/', '_', $current_tab));
                                    do_settings_sections('woochimp-admin-' . $current_tab);
                                ?>

                                <?php
                                    if ($current_tab == 'integration') {
                                        echo '<div class="woochimp-status" id="woochimp-status"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span>'.__('Connecting to MailChimp...', 'woochimp').'</p></div>';
                                    }
                                    else if ($current_tab == 'widget') {
                                        ?>
                                        <div class="woochimp-usage" id="woochimp-usage">
                                            <p><?php _e('To activate a singup widget:', 'woochimp'); ?>
                                                <ul style="">
                                                    <li><?php printf(__('go to <a href="%s">Widgets</a> page', 'woochimp'), site_url('/wp-admin/widgets.php')); ?></li>
                                                    <li><?php _e('locate a widget named MailChimp Signup', 'woochimp'); ?></li>
                                                    <li><?php _e('drag and drop it to the sidebar of your choise', 'woochimp'); ?></li>
                                                </ul>
                                            </p>
                                            <p>
                                                <?php _e('Widget will not be displayed to customers if it is not enabled here or if the are issues with configuration.', 'woochimp'); ?>
                                            </p>
                                            <p>
                                                <?php _e('To avoid potential conflicts, we recommend to use at most one MailChimp Signup widget per page.', 'woochimp'); ?>
                                            </p>
                                        </div>
                                        <?php
                                    }
                                    else if ($current_tab == 'shortcode') {
                                        ?>
                                        <div class="woochimp-usage" id="woochimp-usage">
                                            <p><?php _e('You can display a signup form anywhere in your pages, posts and WooCommerce product descriptions.', 'woochimp'); ?></p>
                                            <p><?php _e('To do this, simply insert the following shortcode to the desired location:', 'woochimp'); ?></p>
                                            <div class="woochimp-code">[woochimp_form]</div>
                                            <p>
                                                <?php _e('Shorcode will not be displayed to customers if it is not enabled here or if there are issues with configuration.', 'woochimp'); ?>
                                            </p>
                                            <p>
                                                <?php _e('To avoid potential conflicts, we recommend to place at most one shortcode per page.', 'woochimp'); ?>
                                            </p>
                                        </div>
                                        <?php
                                    }
                                ?>

                                <?php
                                    submit_button();
                                ?>

                            </form>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
            <?php

            /**
             * Pass data on selected lists, groups and merge tags
             */
            if (isset($this->opt['sets']) && is_array($this->opt['sets']) && !empty($this->opt['sets'])) {

                $woochimp_checkout_sets = array();

                foreach ($this->opt['sets'] as $set_key => $set) {
                    $woochimp_checkout_sets[$set_key] = array(
                        'list'      => $set['list'],
                        'groups'    => $set['groups'],
                        'merge'     => $set['fields'],
                        'condition' => $set['condition']
                    );
                }
            }
            else {
                $woochimp_checkout_sets = array();
            }

            // Pass variables to JavaScript
            ?>
                <script>
                    var woochimp_hints = <?php echo json_encode($this->hints); ?>;
                    var woochimp_home_url = '<?php echo site_url(); ?>';
                    var woochimp_enabled = '<?php echo $this->opt['woochimp_enabled']; ?>';
                    var woochimp_enabled_checkout = '<?php echo $this->opt['woochimp_enabled_checkout']; ?>';
                    var woochimp_enabled_widget = '<?php echo $this->opt['woochimp_enabled_widget']; ?>';
                    var woochimp_enabled_shortcode = '<?php echo $this->opt['woochimp_enabled_shortcode']; ?>';
                    var woochimp_selected_list = {
                        'widget': '<?php echo $this->opt['woochimp_list_widget']; ?>',
                        'shortcode': '<?php echo $this->opt['woochimp_list_shortcode']; ?>'
                    };
                    var woochimp_selected_groups = {
                        'widget': <?php echo json_encode($this->opt['woochimp_groups_widget']); ?>,
                        'shortcode': <?php echo json_encode($this->opt['woochimp_groups_shortcode']); ?>
                    };
                    var woochimp_label_no_results_match = '<?php _e('No results match', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_list = '<?php _e('There are no lists named', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_tags = '<?php _e('Selected list does not have tags named', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_checkout_fields = '<?php _e('No such field', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_groups = '<?php _e('Selected list does not have groups named', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_products = '<?php _e('There are no products named', 'woochimp'); ?>';
                    var woochimp_label_no_results_match_categories = '<?php _e('There are no product categories named', 'woochimp'); ?>';
                    var woochimp_label_select_mailing_list = '<?php _e('Select a mailing list', 'woochimp'); ?>';
                    var woochimp_label_select_tag = '<?php _e('Select a tag', 'woochimp'); ?>';
                    var woochimp_label_select_checkout_field = '<?php _e('Select a checkout field', 'woochimp'); ?>';
                    var woochimp_label_select_some_groups = '<?php _e('Select some groups (optional)', 'woochimp'); ?>';
                    var woochimp_label_select_some_products = '<?php _e('Select some products', 'woochimp'); ?>';
                    var woochimp_label_select_some_categories = '<?php _e('Select some categories', 'woochimp'); ?>';
                    var woochimp_label_connecting_to_mailchimp = '<?php _e('Connecting to MailChimp...', 'woochimp'); ?>';
                    var woochimp_label_still_connecting_to_mailchimp = '<?php _e('Still connecting to MailChimp...', 'woochimp'); ?>';
                    var woochimp_label_fields_field = '<?php _e('Field Name', 'woochimp'); ?>';
                    var woochimp_label_fields_tag = '<?php _e('MailChimp Tag', 'woochimp'); ?>';
                    var woochimp_label_add_new = '<?php _e('Add Field', 'woochimp'); ?>';
                    var woochimp_label_add_new_set = '<?php _e('Add Set', 'woochimp'); ?>';
                    var woochimp_label_mailing_list = '<?php _e('Mailing list', 'woochimp'); ?>';
                    var woochimp_label_groups = '<?php _e('Groups', 'woochimp'); ?>';
                    var woochimp_label_set_no = '<?php _e('Set #', 'woochimp'); ?>';
                    var woochimp_webhook_enabled = '<?php echo $this->opt['woochimp_enable_webhooks']; ?>';
                    var woochimp_label_bad_ajax_response = '<?php printf(__('%s Response received from your server is <a href="%s" target="_blank">malformed</a>.', 'woochimp'), '<i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;', 'http://support.rightpress.net/hc/en-us/articles/201236957'); ?>';

                    <?php if ($current_tab == 'checkout'): ?>
                    var woochimp_checkout_sets = <?php echo json_encode($woochimp_checkout_sets); ?>;
                    <?php endif; ?>

                </script>
            <?php
        }

        /**
         * Render section info
         * 
         * @access public
         * @param array $section
         * @return void
         */
        public function render_section_info($section)
        {
            if (isset($this->section_info[$section['id']])) {
                echo $this->section_info[$section['id']];
            }

            // Subscription widget fields
            if ($section['id'] == 'subscription_widget_fields') {

                // Get current fields
                $current_fields = $this->opt['woochimp_widget_fields'];

                ?>
                <div class="woochimp-fields">
                    <p><?php printf(__('Email address field is always displayed, however, you may wish to set up additional fields here and associate them with MailChimp\'s <a href="%s" target="_blank">merge tags</a>.', 'woochimp'), 'http://kb.mailchimp.com/article/getting-started-with-merge-tags/'); ?></p>
                    <div class="woochimp-status" id="woochimp_widget_fields"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span><?php _e('Connecting to MailChimp...', 'woochimp'); ?></p></div>
                </div>
                <?php
            }

            // Subscription shortcode fields
            else if ($section['id'] == 'subscription_shortcode_fields') {

                // Get current fields
                $current_fields = $this->opt['woochimp_shortcode_fields'];

                ?>
                <div class="woochimp-fields">
                    <p><?php printf(__('Email address field is always displayed, however, you may wish to set up additional fields here and associate them with MailChimp\'s <a href="%s" target="_blank">merge tags</a>.', 'woochimp'), 'http://kb.mailchimp.com/article/getting-started-with-merge-tags/'); ?></p>
                    <div class="woochimp-status" id="woochimp_shortcode_fields"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span><?php _e('Connecting to MailChimp...', 'woochimp'); ?></p></div>
                </div>
                <?php
            }

            // Subscription on checkout list, groups and fields
            else if ($section['id'] == 'subscription_checkout_list_groups') {

                /**
                 * Load list of all products
                 * TBD: We shouldn't load full lists here - need an Ajax solution
                 */
                $posts = array('' => '');

                $posts_raw = get_posts(array(
                    'posts_per_page'    => -1,
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'fields'            => 'ids',
                ));

                foreach ($posts_raw as $post_id) {
                    $posts[$post_id] = get_the_title($post_id);
                }

                /**
                 * Load list of all product categories
                 */
                $post_categories = array('' => '');

                $post_categories_raw = get_terms(array('product_cat'), array('hide_empty' => 0));
                $post_categories_raw_count = count($post_categories_raw);

                foreach ($post_categories_raw as $post_cat_key => $post_cat) {
                    $category_name = $post_cat->name;

                    if ($post_cat->parent) {
                        $parent_id = $post_cat->parent;
                        $has_parent = true;

                        // Make sure we don't have an infinite loop here (happens with some kind of "ghost" categories)
                        $found = false;
                        $i = 0;

                        while ($has_parent && ($i < $post_categories_raw_count || $found)) {

                            // Reset each time
                            $found = false;
                            $i = 0;

                            foreach ($post_categories_raw as $parent_post_cat_key => $parent_post_cat) {

                                $i++;

                                if ($parent_post_cat->term_id == $parent_id) {
                                    $category_name = $parent_post_cat->name . ' &rarr; ' . $category_name;
                                    $found = true;

                                    if ($parent_post_cat->parent) {
                                        $parent_id = $parent_post_cat->parent;
                                    }
                                    else {
                                        $has_parent = false;
                                    }

                                    break;
                                }
                            }
                        }
                    }

                    $post_categories[$post_cat->term_id] = $category_name;
                }

                /**
                 * Load list of all countries
                 */

                /**
                 * Available conditions
                 */
                $condition_options = array(
                    'always'          => __('No condition', 'woochimp'),
                    'products'        => __('Products in cart', 'woochimp'),
                    'categories'      => __('Product categories in cart', 'woochimp'),
                    'amount'          => __('Order total', 'woochimp'),
                    'custom'          => __('Custom field value', 'woochimp'),
                );

                /**
                 * Load saved forms
                 */
                if (isset($this->opt['sets']) && is_array($this->opt['sets']) && !empty($this->opt['sets'])) {

                    // Real forms
                    $saved_sets = $this->opt['sets'];

                    // Pass selected properties to Javascript
                    $woochimp_selected_lists = array();

                    foreach ($saved_sets as $set_key => $set) {
                        $woochimp_selected_lists[$set_key] = array(
                            'list'      => $set['list'],
                            'groups'    => $set['groups'],
                            'merge'     => $set['fields']
                        );
                    }
                }
                else {

                    // Mockup
                    $saved_sets[1] = array(
                        'list'      => '',
                        'groups'    => array(),
                        'fields'    => array(),
                        'condition' => array(
                            'key'       => '',
                            'operator'  => '',
                            'value'     => '',
                        ),
                    );

                    // Pass selected properties to Javascript
                    $woochimp_selected_lists = array();
                }

                ?>
                <div class="woochimp-list-groups">
                    <p><?php _e('Select mailing list and groups that customers will be added to. Multiple sets of list and groups with conditional selection are supported.<br />Please note that if criteria of more than one set is matched, user will be subscribed multiple times to multiple lists/groups.', 'woochimp'); ?></p>
                    <div id="woochimp_list_groups_list">

                        <?php foreach ($saved_sets as $set_key => $set): ?>

                        <div id="woochimp_list_groups_list_<?php echo $set_key; ?>">
                            <h4 class="woochimp_list_groups_handle"><span class="woochimp_list_groups_title" id="woochimp_list_groups_title_<?php echo $set_key; ?>"><?php _e('Set #', 'woochimp'); ?><?php echo $set_key; ?></span><span class="woochimp_list_groups_remove" id="woochimp_list_groups_remove_<?php echo $set_key; ?>" title="<?php _e('Remove', 'woochimp'); ?>"><i class="fa fa-times"></i></span></h4>
                            <div style="clear:both;">

                                <div class="woochimp_list_groups_section">List & Groups</div>
                                <p id="woochimp_list_checkout_<?php echo $set_key; ?>" class="woochimp_loading_checkout woochimp_list_checkout">
                                    <span class="woochimp_loading_icon"></span>
                                    <?php _e('Connecting to MailChimp...', 'woochimp'); ?>
                                </p>

                                <div class="woochimp_list_groups_section">Fields</div>
                                <p id="woochimp_fields_table_<?php echo $set_key; ?>" class="woochimp_loading_checkout woochimp_fields_checkout">
                                    <span class="woochimp_loading_icon"></span>
                                    <?php _e('Connecting to MailChimp...', 'woochimp'); ?>
                                </p>

                                <div class="woochimp_list_groups_section">Conditions</div>
                                <table class="form-table"><tbody>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Condition', 'woochimp'); ?></th>
                                        <td><select id="woochimp_sets_condition_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition]" class="woochimp-field set_condition_key">

                                            <?php
                                                foreach ($condition_options as $cond_value => $cond_title) {
                                                    $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == $cond_value) ? 'selected="selected"' : '';
                                                    echo '<option value="' . $cond_value . '" ' . $is_selected . '>' . $cond_title . '</option>';
                                                }
                                            ?>

                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                        <td><select id="woochimp_sets_condition_operator_products_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_products]" class="woochimp-field set_condition_operator set_condition_operator_products">
                                            <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'products') ? true : false; ?>
                                            <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                            <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                        <td><select id="woochimp_sets_condition_operator_categories_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_categories]" class="woochimp-field set_condition_operator set_condition_operator_categories">
                                            <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'categories') ? true : false; ?>
                                            <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                            <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                        <td><select id="woochimp_sets_condition_operator_amount_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_amount]" class="woochimp-field set_condition_operator set_condition_operator_amount">
                                            <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'amount') ? true : false; ?>
                                            <option value="lt" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'lt') ? 'selected="selected"' : ''); ?>><?php _e('Less than', 'woochimp'); ?></option>
                                            <option value="le" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'le') ? 'selected="selected"' : ''); ?>><?php _e('Less than or equal to', 'woochimp'); ?></option>
                                            <option value="eq" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'eq') ? 'selected="selected"' : ''); ?>><?php _e('Equal to', 'woochimp'); ?></option>
                                            <option value="ge" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'ge') ? 'selected="selected"' : ''); ?>><?php _e('Greater than or equal to', 'woochimp'); ?></option>
                                            <option value="gt" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'gt') ? 'selected="selected"' : ''); ?>><?php _e('Greater than', 'woochimp'); ?></option>
                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Products', 'woochimp'); ?></th>
                                        <td><select multiple id="woochimp_sets_condition_products_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_products][]" class="woochimp-field set_condition_value set_condition_value_products">

                                            <?php
                                                foreach ($posts as $key => $name) {
                                                    $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'products' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && in_array($key, $set['condition']['value']['value'])) ? 'selected="selected"' : '';
                                                    echo '<option value="' . $key . '" ' . $is_selected . '>' . $name . '</option>';
                                                }
                                            ?>

                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Product categories', 'woochimp'); ?></th>
                                        <td><select multiple id="woochimp_sets_condition_categories_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_categories][]" class="woochimp-field set_condition_value set_condition_value_categories">

                                            <?php
                                                foreach ($post_categories as $key => $name) {
                                                    $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'categories' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && in_array($key, $set['condition']['value']['value'])) ? 'selected="selected"' : '';
                                                    echo '<option value="' . $key . '" ' . $is_selected . '>' . $name . '</option>';
                                                }
                                            ?>

                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Order total', 'woochimp'); ?></th>
                                        <td><input type="text" id="woochimp_sets_condition_amount_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_amount]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'amount' && isset($set['condition']['value']) && isset($set['condition']['value']['value'])) ? $set['condition']['value']['value'] : ''); ?>" class="woochimp-field set_condition_value set_condition_value_amount"></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Custom field key', 'woochimp'); ?></th>
                                        <td><input type="text" id="woochimp_sets_condition_key_custom_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_key_custom]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'custom' && isset($set['condition']['value']) && isset($set['condition']['value']['key'])) ? $set['condition']['value']['key'] : ''); ?>" class="woochimp-field set_condition_custom_key set_condition_custom_key_custom"></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                        <td><select id="woochimp_sets_condition_operator_custom_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_custom]" class="woochimp-field set_condition_operator set_condition_operator_custom">
                                            <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'custom') ? true : false; ?>
                                            <optgroup label="String">
                                                <option value="is" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'is') ? 'selected="selected"' : ''); ?>><?php _e('Is', 'woochimp'); ?></option>
                                                <option value="is_not" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'is_not') ? 'selected="selected"' : ''); ?>><?php _e('Is not', 'woochimp'); ?></option>
                                                <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                                <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                            </optgroup>
                                            <optgroup label="Number">
                                                <option value="lt" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'lt') ? 'selected="selected"' : ''); ?>><?php _e('Less than', 'woochimp'); ?></option>
                                                <option value="le" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'le') ? 'selected="selected"' : ''); ?>><?php _e('Less than or equal to', 'woochimp'); ?></option>
                                                <option value="eq" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'eq') ? 'selected="selected"' : ''); ?>><?php _e('Equal to', 'woochimp'); ?></option>
                                                <option value="ge" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'ge') ? 'selected="selected"' : ''); ?>><?php _e('Greater than or equal to', 'woochimp'); ?></option>
                                                <option value="gt" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'gt') ? 'selected="selected"' : ''); ?>><?php _e('Greater than', 'woochimp'); ?></option>
                                            </optgroup>
                                        </select></td>
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row"><?php _e('Custom field value', 'woochimp'); ?></th>
                                        <td><input type="text" id="woochimp_sets_condition_custom_value_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_custom_value]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'custom' && isset($set['condition']['value']) && isset($set['condition']['value']['value'])) ? $set['condition']['value']['value'] : ''); ?>" class="woochimp-field set_condition_value set_condition_value_custom"></td>
                                    </tr>
                                </tbody></table>

                            </div>
                            <div style="clear: both;"></div>
                        </div>

                        <?php endforeach; ?>

                    </div>
                    <div>
                        <button type="button" name="woochimp_add_set" id="woochimp_add_set" disabled="disabled" class="button button-primary" value="<?php _e('Add Set', 'woochimp'); ?>" title="<?php _e('Still connecting to MailChimp...', 'woochimp'); ?>"><i class="fa fa-plus">&nbsp;&nbsp;<?php _e('Add Set', 'woochimp'); ?></i></button>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <?php
            }
        }

        /*
         * Render a text field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_text($args = array())
        {
            printf(
                '<input type="text" id="%s" name="woochimp_options[%s]" value="%s" class="woochimp-field" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a text area
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_textarea($args = array())
        {
            printf(
                '<textarea id="%s" name="woochimp_options[%s]" class="woochimp-textarea">%s</textarea>',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a checkbox
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_checkbox($args = array())
        {
            printf(
                '<input type="checkbox" id="%s" name="%soptions[%s]" value="1" %s />',
                $args['name'],
                'woochimp_',
                $args['name'],
                checked($args['options'][$args['name']], true, false)
            );
        }

        /*
         * Render a dropdown
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_dropdown($args = array())
        {
            // Handle MailChimp lists dropdown differently
            if (in_array($args['name'], array('woochimp_list_checkout', 'woochimp_list_widget', 'woochimp_list_shortcode'))) {
                echo '<p id="' . $args['name'] . '" class="woochimp_loading"><span class="woochimp_loading_icon"></span>' . __('Connecting to MailChimp...', 'woochimp') . '</p>';
            }
            // Handle MailChimp groups multiselect differently
            else if (in_array($args['name'], array('woochimp_groups_checkout', 'woochimp_groups_widget', 'woochimp_groups_shortcode'))) {
                echo '<p id="' . $args['name'] . '" class="woochimp_loading"><span class="woochimp_loading_icon"></span>' . __('Connecting to MailChimp...', 'woochimp') . '</p>';
            }
            else {

                printf(
                    '<select id="%s" name="woochimp_options[%s]" class="woochimp-field">',
                    $args['name'],
                    $args['name']
                );

                foreach ($this->options[$args['name']] as $key => $name) {
                    printf(
                        '<option value="%s" %s %s>%s</option>',
                        $key,
                        selected($key, $args['options'][$args['name']], false),
                        ($key === 0 ? 'disabled="disabled"' : ''),
                        $name
                    );
                }
                echo '</select>';
            }
        }

        /*
         * Render a password field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_password($args = array())
        {
            printf(
                '<input type="password" id="%s" name="woochimp_options[%s]" value="%s" class="woochimp-field" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /**
         * Validate admin form input
         * 
         * @access public
         * @param array $input
         * @return array
         */
        public function options_validate($input)
        {
            $current_tab = isset($_POST['current_tab']) ? $_POST['current_tab'] : 'general-settings';
            $output = $original = $this->get_options();

            $revert = array();
            $errors = array();

            // Handle checkout tab differently
            if ($current_tab == 'checkout') {

                // First check shared settings

                // Enabled
                $output['woochimp_enabled_checkout'] = (isset($input['woochimp_enabled_checkout']) && in_array($input['woochimp_enabled_checkout'], array('1', '2', '3'))) ? $input['woochimp_enabled_checkout'] : '1';

                // Subscribe on
                $output['woochimp_checkout_subscribe_on'] = (isset($input['woochimp_checkout_subscribe_on']) && in_array($input['woochimp_checkout_subscribe_on'], array('1', '2'))) ? $input['woochimp_checkout_subscribe_on'] : '1';

                // Do not resubscribe unsubscribed
                $output['woochimp_do_not_resubscribe'] = (isset($input['woochimp_do_not_resubscribe']) && $input['woochimp_do_not_resubscribe'] == '1') ? '1' : '0';

                // Replace groups on MailChimp
                $output['woochimp_replace_groups_checkout'] = (isset($input['woochimp_replace_groups_checkout']) && $input['woochimp_replace_groups_checkout'] == '1') ? '1' : '0';

                // Double opt-in
                $output['woochimp_double_checkout'] = (isset($input['woochimp_double_checkout']) && $input['woochimp_double_checkout'] == '1') ? '1' : '0';

                // Send welcome email
                $output['woochimp_welcome_checkout'] = (isset($input['woochimp_welcome_checkout']) && $input['woochimp_welcome_checkout'] == '1') ? '1' : '0';

                // Label
                $output['woochimp_text_checkout'] = (isset($input['woochimp_text_checkout']) && !empty($input['woochimp_text_checkout'])) ? $input['woochimp_text_checkout'] : '';

                // Checkbox position
                $output['woochimp_checkbox_position'] = (in_array($input['woochimp_checkbox_position'], array('woocommerce_checkout_before_customer_details', 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_billing', 'woocommerce_checkout_shipping', 'woocommerce_checkout_order_review', 'woocommerce_review_order_after_submit', 'woocommerce_review_order_before_submit', 'woocommerce_review_order_before_order_total', 'woocommerce_after_checkout_billing_form'))) ? $input['woochimp_checkbox_position'] : 'woocommerce_checkout_after_customer_details';

                // Default state
                $output['woochimp_default_state'] = (isset($input['woochimp_default_state']) && $input['woochimp_default_state'] == '1') ? '1' : '2';

                $new_sets = array();

                if (isset($input['sets']) && !empty($input['sets'])) {

                    $set_number = 0;

                    foreach ($input['sets'] as $set) {

                        $set_number++;

                        $new_sets[$set_number] = array();

                        // List
                        $new_sets[$set_number]['list'] = (isset($set['list']) && !empty($set['list'])) ? $set['list']: '';

                        // Groups
                        $new_sets[$set_number]['groups'] = array();

                        if (isset($set['groups']) && is_array($set['groups'])) {
                            foreach ($set['groups'] as $group) {
                                $new_sets[$set_number]['groups'][] = $group;
                            }
                        }

                        // Fields
                        $new_sets[$set_number]['fields'] = array();

                        if (isset($set['field_names']) && is_array($set['field_names'])) {

                            $field_number = 0;

                            foreach ($set['field_names'] as $field) {

                                if (!is_array($field) || !isset($field['name']) || !isset($field['tag']) || empty($field['name']) || empty($field['tag'])) {
                                    continue;
                                }

                                $field_number++;

                                $new_sets[$set_number]['fields'][$field_number] = array(
                                    'name'  => $field['name'],
                                    'tag'   => $field['tag']
                                );
                            }
                        }

                        // Condition
                        $new_sets[$set_number]['condition'] = array();
                        $new_sets[$set_number]['condition']['key'] = (isset($set['condition']) && !empty($set['condition'])) ? $set['condition']: 'always';

                        // Condition value
                        if ($new_sets[$set_number]['condition']['key'] == 'products') {
                            if (isset($set['operator_products']) && !empty($set['operator_products']) && isset($set['condition_products']) && is_array($set['condition_products']) && !empty($set['condition_products'])) {

                                // Operator
                                $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_products'];

                                // Value
                                foreach ($set['condition_products'] as $condition_item) {
                                    if (empty($condition_item)) {
                                        continue;
                                    }

                                    $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                                }
                            }
                            else {
                                $new_sets[$set_number]['condition']['key'] = 'always';
                                $new_sets[$set_number]['condition']['value'] = array();
                            }
                        }
                        else if ($new_sets[$set_number]['condition']['key'] == 'categories') {
                            if (isset($set['operator_categories']) && !empty($set['operator_categories']) && isset($set['condition_categories']) && is_array($set['condition_categories']) && !empty($set['condition_categories'])) {

                                // Operator
                                $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_categories'];

                                // Value
                                foreach ($set['condition_categories'] as $condition_item) {
                                    if (empty($condition_item)) {
                                        continue;
                                    }

                                    $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                                }
                            }
                            else {
                                $new_sets[$set_number]['condition']['key'] = 'always';
                                $new_sets[$set_number]['condition']['value'] = array();
                            }
                        }
                        else if ($new_sets[$set_number]['condition']['key'] == 'amount') {
                            if (isset($set['operator_amount']) && !empty($set['operator_amount']) && isset($set['condition_amount']) && !empty($set['condition_amount'])) {

                                // Operator
                                $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_amount'];

                                // Value
                                $new_sets[$set_number]['condition']['value']['value'] = $set['condition_amount'];
                            }
                            else {
                                $new_sets[$set_number]['condition']['key'] = 'always';
                                $new_sets[$set_number]['condition']['value'] = array();
                            }
                        }
                        else if ($new_sets[$set_number]['condition']['key'] == 'custom') {
                            if (isset($set['condition_key_custom']) && !empty($set['condition_key_custom']) && isset($set['operator_custom']) && !empty($set['operator_custom']) && isset($set['condition_custom_value']) && !empty($set['condition_custom_value'])) {

                                // Field key
                                $new_sets[$set_number]['condition']['value']['key'] = $set['condition_key_custom'];

                                // Operator
                                $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_custom'];

                                // Value
                                $new_sets[$set_number]['condition']['value']['value'] = $set['condition_custom_value'];
                            }
                            else {
                                $new_sets[$set_number]['condition']['key'] = 'always';
                                $new_sets[$set_number]['condition']['value'] = array();
                            }
                        }
                        else {
                            $new_sets[$set_number]['condition']['value'] = array();
                        }

                    }

                }

                $output['sets'] = $new_sets;
            }

            // Handle all other settings as usual
            else {

                // Handle field names (if any)
                if (isset($input['field_names'])) {

                    $new_field_names = array();
                    $fields_page = null;

                    if (is_array($input['field_names']) && !empty($input['field_names'])) {
                        foreach ($input['field_names'] as $key => $page) {

                            $fields_page = $key;

                            if (is_array($page) && !empty($page)) {

                                $merge_field_key = 1;

                                foreach ($page as $merge_field) {
                                    if (isset($merge_field['name']) && !empty($merge_field['name']) && isset($merge_field['tag']) && !empty($merge_field['tag'])) {

                                        $new_field_names[$merge_field_key] = array(
                                            'name' => $merge_field['name'],
                                            'tag' => $merge_field['tag'],
                                        );

                                        $merge_field_key++;
                                    }
                                }
                            }

                        }
                    }

                    if (!empty($page)) {
                        $output['woochimp_'.$fields_page.'_fields'] = $new_field_names;
                    }
                }

                // Iterate over fields and validate/sanitize input
                foreach ($this->validation[$current_tab] as $field => $rule) {

                    $allow_empty = true;

                    // Conditional validation
                    if (is_array($rule['empty']) && !empty($rule['empty'])) {
                        if (isset($input['woochimp_' . $rule['empty'][0]]) && ($input['woochimp_' . $rule['empty'][0]] != '0')) {
                            $allow_empty = false;
                        }
                    }
                    else if ($rule['empty'] == false) {
                        $allow_empty = false;
                    }

                    // Different routines for different field types
                    switch($rule['rule']) {

                        // Validate numbers
                        case 'number':
                            if (is_numeric($input[$field]) || ($input[$field] == '' && $allow_empty)) {
                                $output[$field] = $input[$field];
                            }
                            else {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'number'));
                            }
                            break;

                        // Validate boolean values (actually 1 and 0)
                        case 'bool':
                            $input[$field] = (isset($input[$field]) && $input[$field] != '') ? $input[$field] : '0';
                            if (in_array($input[$field], array('0', '1')) || ($input[$field] == '' && $allow_empty)) {
                                $output[$field] = $input[$field];
                            }
                            else {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'bool'));
                            }
                            break;

                        // Validate predefined options
                        case 'option':

                            // Check if this call is for mailing lists
                            if ($field == 'woochimp_list_checkout') {
                                //$this->options[$field] = $this->get_lists();
                                if (is_array($rule['empty']) && !empty($rule['empty']) && $input['woochimp_'.$rule['empty'][0]] != '1' && (empty($input[$field]) || $input[$field] == '0')) {
                                    if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                        $revert[$rule['empty'][0]] = '1';
                                    }
                                    array_push($errors, array('setting' => $field, 'code' => 'option'));
                                }
                                else {
                                    $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                                }

                                break;
                            }
                            else if (in_array($field, array('woochimp_list_widget', 'woochimp_list_shortcode'))) {
                                //$this->options[$field] = $this->get_lists();
                                if (is_array($rule['empty']) && !empty($rule['empty']) && $input['woochimp_'.$rule['empty'][0]] != '0' && (empty($input[$field]) || $input[$field] == '0')) {
                                    if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                        $revert[$rule['empty'][0]] = '0';
                                    }
                                    array_push($errors, array('setting' => $field, 'code' => 'option'));
                                }
                                else {
                                    $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                                }

                                break;
                            }

                            if (isset($this->options[$field][$input[$field]]) || ($input[$field] == '' && $allow_empty)) {
                                $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                            }
                            else {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'option'));
                            }
                            break;

                        // Multiple selections
                        case 'multiple_any':
                            if (empty($input[$field]) && !$allow_empty) {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'multiple_any'));
                            }
                            else {
                                if (is_array($input[$field]) && !empty($input[$field])) {
                                    $temporary_output = array();

                                    foreach ($input[$field] as $field_val) {
                                        $temporary_output[] = htmlspecialchars($field_val);
                                    }

                                    $output[$field] = $temporary_output;
                                }
                                else {
                                    $output[$field] = array();
                                }
                            }
                            break;

                        // Validate emails
                        case 'email':
                            if (filter_var(trim($input[$field]), FILTER_VALIDATE_EMAIL) || ($input[$field] == '' && $allow_empty)) {
                                $output[$field] = esc_attr(trim($input[$field]));
                            }
                            else {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'email'));
                            }
                            break;

                        // Validate URLs
                        case 'url':
                            // FILTER_VALIDATE_URL for filter_var() does not work as expected
                            if (($input[$field] == '' && !$allow_empty)) {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'url'));
                            }
                            else {
                                $output[$field] = esc_attr(trim($input[$field]));
                            }
                            break;

                        // Custom validation function
                        case 'function':
                            $function_name = 'validate_' . $field;
                            $validation_results = $this->$function_name($input[$field]);

                            // Check if parent is disabled - do not validate then and reset to ''
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                if (empty($input['woochimp_'.$rule['empty'][0]])) {
                                    $output[$field] = '';
                                    break;
                                }
                            }

                            if (($input[$field] == '' && $allow_empty) || $validation_results === true) {
                                $output[$field] = $input[$field];
                            }
                            else {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'option', 'custom' => $validation_results));
                            }
                            break;

                        // Default validation rule (text fields etc)
                        default:
                            if (((!isset($input[$field]) || $input[$field] == '') && !$allow_empty)) {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'string'));
                            }
                            else {
                                $output[$field] = isset($input[$field]) ? esc_attr(trim($input[$field])) : '';
                            }
                            break;
                    }
                }

                // Revert parent fields if needed
                if (!empty($revert)) {
                    foreach ($revert as $key => $value) {
                        $output['woochimp_'.$key] = $value;
                    }
                }

            }

            // Display settings updated message
            add_settings_error(
                'woochimp_settings_updated',
                'woochimp_settings_updated',
                __('Your settings have been saved.', 'woochimp'),
                'updated'
            );

            // Define error messages
            $messages = array(
                'number' => __('must be numeric', 'woochimp'),
                'bool' => __('must be either 0 or 1', 'woochimp'),
                'option' => __('is not allowed', 'woochimp'),
                'email' => __('is not a valid email address', 'woochimp'),
                'url' => __('is not a valid URL', 'woochimp'),
                'string' => __('is not a valid text string', 'woochimp'),
            );

            // Display errors
            foreach ($errors as $error) {

                $message = (!isset($error['custom']) ? $messages[$error['code']] : $error['custom']) . '. ' . __('Reverted to a previous state.', 'woochimp');

                add_settings_error(
                    $error['setting'],
                    $error['code'],
                    __('Value of', 'woochimp') . ' "' . $this->titles[$error['setting']] . '" ' . $message
                );
            }

            return $output;
        }

        /**
         * Custom validation for service provider API key
         * 
         * @access public
         * @param string $key
         * @return mixed
         */
        public function validate_woochimp_api_key($key)
        {
            if (empty($key)) {
                return 'is empty';
            }

            $test_results = $this->test_mailchimp($key);

            if ($test_results === true) {
                return true;
            }
            else {
                return ' is not valid or something went wrong. More details: ' . $test_results;
            }
        }

        /**
         * Load scripts required for admin
         * 
         * @access public
         * @return void
         */
        public function enqueue_scripts()
        {
            // Chosen scripts and styles (advanced form fields)
            wp_register_script('jquery-woochimp-chosen', WOOCHIMP_PLUGIN_URL . '/assets/js/chosen.jquery.js', array('jquery'), '1.0.0');
            wp_register_style('jquery-woochimp-chosen-css', WOOCHIMP_PLUGIN_URL . '/assets/css/chosen.min.css', array(), WOOCHIMP_VERSION);

            // Font awesome (icons)
            wp_register_style('woochimp-font-awesome', WOOCHIMP_PLUGIN_URL . '/assets/css/font-awesome/css/font-awesome.min.css', array(), '4.0.3');

            // Our own scripts and styles
            wp_register_script('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/js/woochimp-admin.js', array('jquery'), WOOCHIMP_VERSION);
            wp_register_style('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/css/style.css', array(), WOOCHIMP_VERSION);

            // Scripts
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script('jquery-woochimp-chosen');
            wp_enqueue_script('woochimp');

            // Styles
            wp_enqueue_style('thickbox');
            wp_register_style('jquery-ui', WOOCHIMP_PLUGIN_URL . '/assets/jquery-ui/css/jquery-ui-1.10.3.custom.min.css', array(), WOOCHIMP_VERSION);
            wp_enqueue_style('jquery-ui');
            wp_enqueue_style('jquery-woochimp-chosen-css');
            wp_enqueue_style('woochimp-font-awesome');
            wp_enqueue_style('woochimp');
        }

        /**
         * Load scripts required for frontend
         * 
         * @access public
         * @return void
         */
        public function enqueue_frontend_scripts()
        {
            wp_register_script('woochimp-frontend', WOOCHIMP_PLUGIN_URL . '/assets/js/woochimp-frontend.js', array('jquery'), WOOCHIMP_VERSION);
            wp_register_style('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/css/style.css', array(), WOOCHIMP_VERSION);
            wp_enqueue_script('woochimp-frontend');
            wp_enqueue_style('woochimp');
        }

        /**
         * Load CSS for selected skins
         */
        public function enqueue_form_skins()
        {
            foreach ($this->form_styles as $key => $class) {
                if (in_array(strval($key), array($this->opt['woochimp_widget_skin'], $this->opt['woochimp_shortcode_skin']))) {
                    wp_register_style('woochimp_skin_' . $key, WOOCHIMP_PLUGIN_URL . '/assets/css/skins/woochimp_skin_' . $key . '.css');
                    wp_enqueue_style('woochimp_skin_' . $key);
                }
            }
        }

        /**
         * Add settings link on plugins page
         * 
         * @access public
         * @return void
         */
        public function plugin_settings_link($links)
        {
            $settings_link = '<a href="http://support.rightpress.net/" target="_blank">'.__('Support', 'woochimp').'</a>';
            array_unshift($links, $settings_link);
            $settings_link = '<a href="admin.php?page=woochimp">'.__('Settings', 'woochimp').'</a>'; 
            array_unshift($links, $settings_link); 
            return $links; 
        }

        /**
         * Check if WooCommerce is enabled
		 * 
		 * @access public
		 * @return void
         */
        public function woocommerce_is_enabled()
        {
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                return true;
            }

            return false;
        }

        /**
         * Handle plugin uninstall
         * 
         * @access public
         * @return void
         */
        public function uninstall()
        {
            if (defined('WP_UNINSTALL_PLUGIN')) {
                delete_option('woochimp_options');
            }
        }

        /**
         * Return all lists from MailChimp to be used in select fields
         * 
         * @access public
         * @return array
         */
        public function get_lists()
        {
            $this->load_mailchimp();

            try {
                if (!$this->mailchimp) {
                    throw new Exception(__('Unable to load lists', 'woochimp'));
                }

                $lists = $this->mailchimp->lists_get_list();

                if ($lists['total'] < 1) {
                    throw new Exception(__('No lists found', 'woochimp'));
                }

                $results = array('' => '');

                foreach ($lists['data'] as $list) {
                    $results[$list['id']] = $list['name'];
                }

                return $results;
            }
            catch (Exception $e) {
                return array('' => '');
            }
        }

        /**
         * Return all groupings/groups from MailChimp to be used in select fields
         * 
         * @access public
         * @param mixed $list_id
         * @return array
         */
        public function get_groups($list_id)
        {
            $this->load_mailchimp();

            try {

                if (!$this->mailchimp) {
                    throw new Exception(__('Unable to load groups', 'woochimp'));
                }

                // Single list?
                if (in_array(gettype($list_id), array('integer', 'string'))) {
                    $groupings = $this->mailchimp->lists_interest_groupings($list_id);

                    if (!$groupings || empty($groupings)) {
                        throw new Exception(__('No groups found', 'woochimp'));
                    }

                    $results = array('' => '');

                    foreach ($groupings as $grouping) {
                        foreach ($grouping['groups'] as $group) {
                            $results[$grouping['id'] . ':' . htmlspecialchars($group['name'])] = htmlspecialchars($grouping['name']) . ': ' . htmlspecialchars($group['name']);
                        }
                    }
                }

                // Multiple lists...
                else {

                    $results = array();

                    foreach ($list_id as $list_id_key => $list_id_value) {

                        $results[$list_id_value['list']] = array('' => '');

                        try {
                            $groupings = $this->mailchimp->lists_interest_groupings($list_id_value['list']);
                        }
                        catch (Exception $e) {
                            continue;
                        }

                        if (!$groupings || empty($groupings)) {
                            continue;
                        }

                        foreach ($groupings as $grouping) {
                            foreach ($grouping['groups'] as $group) {
                                $results[$list_id_value['list']][$grouping['id'] . ':' . htmlspecialchars($group['name'])] = htmlspecialchars($grouping['name']) . ': ' . htmlspecialchars($group['name']);
                            }
                        }
                    }

                }

                return $results;
            }
            catch (Exception $e) {
                return array();
            }
        }

        /**
         * Return all merge vars for all available lists
         * 
         * @access public
         * @param array $lists
         * @return array
         */
        public function get_merge_vars($lists)
        {
            $this->load_mailchimp();

            // Unset blank list
            unset($lists['']);

            // Pre-populate results array with list ids as keys
            $results = array();

            foreach (array_keys($lists) as $list) {
                $results[$list] = array();
            }

            try {

                if (!$this->mailchimp) {
                    throw new Exception(__('Unable to load merge vars', 'woochimp'));
                }

                $merge_vars = $this->mailchimp->lists_merge_vars(array_keys($lists));

                if (!$merge_vars || empty($merge_vars) || !isset($merge_vars['data'])) {
                    throw new Exception(__('No merge vars found', 'woochimp'));
                }

                foreach ($merge_vars['data'] as $merge_var) {
                    foreach ($merge_var['merge_vars'] as $var) {
                        // Skip standard email var
                        if ($var['tag'] == 'EMAIL') {
                            continue;
                        }

                        $results[$merge_var['id']][$var['tag']] = $var['name'];
                    }
                }

                return $results;
            }
            catch (Exception $e) {
                return $results;
            }
        }

        /**
         * Test MailChimp key and connection
         * 
         * @access public
         * @return bool
         */
        public function test_mailchimp($key = null)
        {
            // Try to get key from options if not set
            if ($key == null) {
                $key = $this->opt['woochimp_api_key'];
            }

            // Check if api key is set now
            if (empty($key)) {
                return __('No API key provided', 'woochimp');
            }

            // Check if curl extension is loaded
            if (!function_exists('curl_exec')) {
                return __('PHP Curl extension not loaded on your server', 'woochimp');
            }

            // Load MailChimp class if not yet loaded
            if (!class_exists('WooChimp_Mailchimp')) {
                require_once WOOCHIMP_PLUGIN_PATH . '/includes/woochimp-mailchimp.class.php';
            }

            // Try to initialize MailChimp
            $this->mailchimp = new WooChimp_Mailchimp($key);

            if (!$this->mailchimp) {
                return __('Unable to initialize MailChimp class', 'woochimp');
            }

            // Ping
            try {
                $results = $this->mailchimp->helper_ping();

                if ($results['msg'] == 'Everything\'s Chimpy!') {
                    return true;
                }

                throw new Exception($results['msg']);  
            }
            catch (Exception $e) {
                return $e->getMessage();
            }

            return __('Something went wrong...', 'woochimp');
        }

        /**
         * Get MailChimp account details
         * 
         * @access public
         * @return mixed
         */
        public function get_mailchimp_account_info()
        {
            if ($this->load_mailchimp()) {
                try {
                    $results = $this->mailchimp->helper_account_details();
                    return $results;
                }
                catch (Exception $e) {
                    return false;
                }
            }

            return false;
        }

        /**
         * Load MailChimp object
         * 
         * @access public
         * @return mixed
         */
        public function load_mailchimp()
        {
            if ($this->mailchimp) {
                return true;
            }

            // Load MailChimp class if not yet loaded
            if (!class_exists('WooChimp_Mailchimp')) {
                require_once WOOCHIMP_PLUGIN_PATH . '/includes/woochimp-mailchimp.class.php';
            }

            try {
                $this->mailchimp = new WooChimp_Mailchimp($this->opt['woochimp_api_key']);
                return true;
            }
            catch (Exception $e) {
                return false;
            }
        }

        /**
         * Ajax - Render MailChimp status
         * 
         * @access public
         * @return void
         */
        public function ajax_mailchimp_status()
        {
            if (!$this->opt['woochimp_enabled'] || empty($this->opt['woochimp_api_key'])) {
                $message = '<h4><i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;' . __('Integration not enabled or API key not set', 'woochimp') . '</h4>';
            }
            else if ($account_info = $this->get_mailchimp_account_info()) {

                // Check if Ecommerce360 is enabled
                if (isset($account_info['modules']) && is_array($account_info['modules'])) {
                    foreach ($account_info['modules'] as $module) {
                        if (isset($module['name']) && $module['name'] == 'MailChimp Ecomm 360') {
                            $ecomm_enabled = true;
                        }
                    }

                    $ecomm_enabled = (isset($ecomm_enabled) && $ecomm_enabled) ? true : false;
                }

                $message =  '<div style="width:400px;float:left;"><h4><i class="fa fa-check" style="font-size: 1.5em; color: green;"></i>&nbsp;&nbsp;&nbsp;' . __('Successfully connected to MailChimp', 'woochimp') . '</h4>' .
                            '<table class="woochimp-account-info"><tbody><tr>' .
                            '<td><strong>' . __('Account name', 'woochimp') . ':</strong></td><td>' . $account_info['username'] . '</td>' .
                            '</tr><tr>' .
                            '<td><strong>' . __('Industry', 'woochimp') . ':</strong></td><td>' . $account_info['industry'] . '</td>' .
                            '</tr><tr>' .
                            '<td><strong>' . __('Plan type', 'woochimp') . ':</strong></td><td>' . $account_info['plan_type'] . '</td>' .
                            '</tr><tr>' .
                            '<td><strong>' . __('Is trial', 'woochimp') . ':</strong></td><td>' . ($account_info['is_trial'] ? __('Yes', 'woochimp') : __('No', 'woochimp')) . '</td>' .
                            '</tr><tr>' .
                            '<td><strong>' . __('Ecommerce360 enabled', 'woochimp') . ':</strong></td><td>' . (!isset($ecomm_enabled) ? __('Unknown', 'woochimp') : ($ecomm_enabled ? __('Yes', 'woochimp') : __('No', 'woochimp'))) . '</td>' .
                            '</tr></tbody></table></div><div style="width:100px;float:left;"><img style="width:300px;height:auto;margin-top:60px;" src="'.WOOCHIMP_PLUGIN_URL.'/assets/img/mailchimp_logo.png" /></div><div style="clear:both;"></div>';
            }
            else {
                $message = '<h4><i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;' . __('Connection to MailChimp failed.', 'woochimp') . '</h4>';
                $mailchimp_error = $this->test_mailchimp();

                if ($mailchimp_error !== true) {
                    $message .= '<p><strong>' . __('Reason', 'woochimp') . ':</strong> '. $mailchimp_error .'</p>';
                }
            }

            echo json_encode(array('message' => $message));
            die();
        }

        /**
         * Ajax - Return MailChimp lists as array for select field
         * 
         * @access public
         * @return void
         */
        public function ajax_lists_in_array()
        {
            $lists = $this->get_lists();

            // Get merge vars
            $merge = $this->get_merge_vars($lists);

            // Get selected merge vars
            if (isset($_POST['data']) && isset($_POST['data']['page']) && in_array($_POST['data']['page'], array('checkout', 'widget', 'shortcode'))) {
                if (isset($this->opt['woochimp_'.$_POST['data']['page'].'_fields']) && !empty($this->opt['woochimp_'.$_POST['data']['page'].'_fields'])) {
                    $selected_merge = $this->opt['woochimp_'.$_POST['data']['page'].'_fields'];
                }
            }

            $selected_merge = isset($selected_merge) ? $selected_merge : array();

            // Do we know which list is selected?
            if (isset($_POST['data']) && isset($_POST['data']['page']) && in_array($_POST['data']['page'], array('checkout', 'widget', 'shortcode')) && $this->opt['woochimp_list_'.$_POST['data']['page']]) {
                $groups = $this->get_groups($this->opt['woochimp_list_'.$_POST['data']['page']]);

                $selected_groups = array();

                if (is_array($this->opt['woochimp_groups_'.$_POST['data']['page']])) {
                    foreach ($this->opt['woochimp_groups_'.$_POST['data']['page']] as $group_val) {
                        $selected_groups[] = htmlspecialchars($group_val);
                    }
                }
            }
            else {
                $groups = array('' => '');
                $selected_groups = array('' => '');
            }

            // Add all checkout properties
            $checkout_properties = array();

            if (isset($_POST['data']) && isset($_POST['data']['page']) && $_POST['data']['page'] == 'checkout') {
                $checkout_properties = $this->checkout_properties;
            }

            echo json_encode(array('message' => array('lists' => $lists, 'groups' => $groups, 'selected_groups' => $selected_groups, 'merge' => $merge, 'selected_merge' => $selected_merge, 'checkout_properties' => $checkout_properties)));
            die();
        }

        /**
         * Ajax - Return MailChimp groups and tags as array for multiselect field
         */
        public function ajax_groups_and_tags_in_array()
        {
            // Check if we have received required data
            if (isset($_POST['data']) && isset($_POST['data']['list'])) {
                $groups = $this->get_groups($_POST['data']['list']);

                $selected_groups = array();

                if (is_array($this->opt['woochimp_groups_'.$_POST['data']['page']])) {
                    foreach ($this->opt['woochimp_groups_'.$_POST['data']['page']] as $group_val) {
                        $selected_groups[] = htmlspecialchars($group_val);
                    }
                }

                $merge_vars = $this->get_merge_vars(array($_POST['data']['list'] => ''));
            }
            else {
                $groups = array('' => '');
                $selected_groups = array('' => '');
                $merge_vars = array('' => '');
            }

            // Add all checkout properties
            $checkout_properties = array();

            if (isset($_POST['data']) && isset($_POST['data']['page']) && $_POST['data']['page'] == 'checkout') {
                $checkout_properties = $this->checkout_properties;
            }

            echo json_encode(array('message' => array('groups' => $groups, 'selected_groups' => $selected_groups, 'merge' => $merge_vars, 'selected_merge' => array(), 'checkout_properties' => $checkout_properties)));
            die();
        }

        /**
         * Ajax - Return MailChimp groups and tags as array for multiselect field for checkout page
         */
        public function ajax_groups_and_tags_in_array_for_checkout()
        {
            // Check if we have received required data
            if (isset($_POST['data']) && isset($_POST['data']['list'])) {
                $groups = $this->get_groups($_POST['data']['list']);
                $merge_vars = $this->get_merge_vars(array($_POST['data']['list'] => ''));
            }
            else {
                $groups = array('' => '');
                $merge_vars = array('' => '');
            }

            $checkout_properties = $this->checkout_properties;

            echo json_encode(array('message' => array('groups' => $groups, 'merge' => $merge_vars, 'checkout_properties' => $checkout_properties)));
            die();
        }

        /**
         * Send Ecommerce 360 data
         * 
         * @access public
         * @return void
         */
        public function on_completed($order_id)
        {
            // Check if functionality is enabled
            if (!$this->opt['woochimp_enabled']) {
                return;
            }

            // Check if WC order class is available and MailChimp is loaded
            if (class_exists('WC_Order') && $this->load_mailchimp()) {

                // Do we need to subscribe user on completed?
                $subscribe_on_completed = get_post_meta($order_id, 'woochimp_subscribe_on_completed', true);

                if ($subscribe_on_completed == '1') {
                    $this->subscribe_checkout($order_id);
                }

                // Do we need to send order data?
                if (!$this->opt['woochimp_send_order_data']) {
                    return;
                }

                // Initialize order object
                $order = new WC_Order($order_id);

                // Get domain name from site url
                $parse = parse_url(site_url());

                // Define arguments
                $args = array(
                    'id'            => $order->id,
                    'email'         => $order->billing_email,
                    'total'         => floatval($order->order_total),
                    'order_date'    => date('Y-m-d', strtotime($order->order_date)),
                    'store_id'      => substr(preg_replace('/[^a-zA-Z0-9]+/', '', $parse['host']), 0, 32),
                    'store_name'    => $parse['host'],
                    'items'         => array(),
                );

                // Check if we have campaign ID and email ID for this user/order
                $woochimp_mc_cid = get_post_meta($order->id, 'woochimp_mc_cid', true);
                $woochimp_mc_eid = get_post_meta($order->id, 'woochimp_mc_eid', true);
                if (!empty($woochimp_mc_cid) && !empty($woochimp_mc_eid)) {

                    // Pass campaign tracking properties to argument list
                    $args['campaign_id'] = $woochimp_mc_cid;
                    $args['email_id'] = $woochimp_mc_eid;

                    // Unset email when we are passing email id
                    unset($args['email']);
                }

                // Get order items
                $items = $order->get_items();

                // Populate items
                foreach ($items as $item) {

                    // Load actual product
                    $_product = $order->get_product_from_item($item);

                    // Load main category
                    $_categories = get_the_terms($_product->id, 'product_cat');
                    $_category = null;

                    if ($_categories) {
                        $_category = $_categories[0];
                    }

                    // Define properties
                    $item_properties = array(
                        'product_id' => intval($item['product_id']),
                        'product_name' => $item['name'],
                        'qty' => $item['qty'],
                    );

                    // Add category data if set or dummy data otherwise
                    if ($_category) {
                        $item_properties['category_id'] = $_category->term_id;
                        $item_properties['category_name'] = $_category->name;
                    }
                    else {
                        $item_properties['category_id'] = 1;
                        $item_properties['category_name'] = 'Uncategorized';
                    }

                    // Add sku and price (if available)
                    if ($_product) {
                        $item_sku = $_product->get_sku();
                        $item_price = $_product->get_price();

                        if ($item_sku) {
                            $item_properties['sku'] = substr($item_sku, 0, 30);
                        }

                        if ($item_price) {
                            $item_properties['cost'] = $item_price;
                        }
                    }

                    $args['items'][] = $item_properties;
                }

                // Send order data
                try {
                    $this->mailchimp->ecomm_order_add($args);
                }
                catch (Exception $e) {

                }
            }
        }

        /**
         * Subscribe user on checkout
         * 
         * @access public
         * @return void
         */
        public function on_checkout($order_id)
        {
            // Firstly - move mc_cid & mc_eid cookies if exist to order data
            if (isset($_COOKIE['woochimp_mc_cid'])) {
                add_post_meta($order_id, 'woochimp_mc_cid', $_COOKIE['woochimp_mc_cid'], true);
            }
            if (isset($_COOKIE['woochimp_mc_eid'])) {
                add_post_meta($order_id, 'woochimp_mc_eid', $_COOKIE['woochimp_mc_eid'], true);
            }

            if (!$this->opt['woochimp_enabled'] || !in_array($this->opt['woochimp_enabled_checkout'], array('2', '3'))) {
                return;
            }

            // Check if user preference is required
            $preference = $_POST['woochimp_data']['woochimp_user_preference'] ? true : false;

            if ($this->opt['woochimp_enabled_checkout'] == 3 && !$preference) {
                return;
            }

            // Either subscribe now or mark it to be subscribed later
            if (isset($this->opt['woochimp_checkout_subscribe_on']) && $this->opt['woochimp_checkout_subscribe_on'] == '2') {
                add_post_meta($order_id, 'woochimp_subscribe_on_completed', '1', true);
            }
            else {
                $this->subscribe_checkout($order_id);
            }

        }

        /**
         * Subscribe user on checkout or order completed
         * 
         * @access public
         * @param int $order_id
         * @return void
         */
        public function subscribe_checkout($order_id)
        {
            $order = new WC_Order($order_id);

            if (!$order) {
                return;
            }

            // Get user meta
            $user_meta = get_user_meta($order->user_id);

            // Get user email
            $email = isset($order->billing_email) ? $order->billing_email : '';

            if (empty($email)) {
                return;
            }

            // Subscribe to lists that match criteria
            if (isset($this->opt['sets']) && is_array($this->opt['sets'])) {
                foreach ($this->opt['sets'] as $set) {

                    // Check if there's no "do not resubscribe" flag
                    if ($this->opt['woochimp_do_not_resubscribe']) {
                        if (isset($user_meta['woochimp_unsubscribed_lists'])) {
                            $skip_current_set = false;

                            foreach ($user_meta['woochimp_unsubscribed_lists'][0] as $unsub_list) {
                                if ($unsub_list == $set['list']) {
                                    $skip_current_set = true;
                                    break;
                                }
                            }

                            if ($skip_current_set) {
                                continue;
                            }
                        }
                    }

                    $proceed_subscription = false;

                    // Always
                    if ($set['condition']['key'] == 'always') {
                        $proceed_subscription = true;
                    }

                    // Products
                    else if ($set['condition']['key'] == 'products') {
                        if ($set['condition']['value']['operator'] == 'contains') {
                            foreach ($order->get_items() as $item) {
                                if (in_array($item['product_id'], $set['condition']['value']['value'])) {
                                    $proceed_subscription = true;
                                    break;
                                }
                            }
                        }
                        else if ($set['condition']['value']['operator'] == 'does_not_contain') {
                            $contains_item = false;

                            foreach ($order->get_items() as $item) {
                                if (in_array($item['product_id'], $set['condition']['value']['value'])) {
                                    $contains_item = true;
                                    break;
                                }
                            }

                            $proceed_subscription = !$contains_item;
                        }
                    }

                    // Categories
                    else if ($set['condition']['key'] == 'categories') {

                        $categories = array();

                        foreach ($order->get_items() as $item) {
                            $item_categories = get_the_terms($item['product_id'], 'product_cat');

                            if (is_array($item_categories)) {
                                foreach ($item_categories as $item_category) {
                                    $categories[] = $item_category->term_id;
                                }
                            }
                        }

                        if ($set['condition']['value']['operator'] == 'contains') {
                            foreach ($categories as $category) {
                                if (in_array($category, $set['condition']['value']['value'])) {
                                    $proceed_subscription = true;
                                    break;
                                }
                            }
                        }
                        else if ($set['condition']['value']['operator'] == 'does_not_contain') {
                            $contains_item = false;

                            foreach ($categories as $category) {
                                if (in_array($category, $set['condition']['value']['value'])) {
                                    $contains_item = true;
                                    break;
                                }
                            }

                            $proceed_subscription = !$contains_item;
                        }
                    }

                    // Amount
                    else if ($set['condition']['key'] == 'amount') {
                        if (($set['condition']['value']['operator'] == 'lt' && $order->order_total < $set['condition']['value']['value']) 
                         || ($set['condition']['value']['operator'] == 'le' && $order->order_total <= $set['condition']['value']['value']) 
                         || ($set['condition']['value']['operator'] == 'eq' && $order->order_total == $set['condition']['value']['value']) 
                         || ($set['condition']['value']['operator'] == 'ge' && $order->order_total >= $set['condition']['value']['value'])
                         || ($set['condition']['value']['operator'] == 'gt' && $order->order_total > $set['condition']['value']['value'])) {
                            $proceed_subscription = true;
                        }
                    }

                    // Custom field
                    else if ($set['condition']['key'] == 'custom') {

                        $custom_field_value = null;

                        // Get the custom field value
                        if (isset($order->order_custom_fields[$set['condition']['value']['key']])) {
                            $custom_field_value = is_array($order->order_custom_fields[$set['condition']['value']['key']]) ? $order->order_custom_fields[$set['condition']['value']['key']][0] : $order->order_custom_fields[$set['condition']['value']['key']];
                        }
                        else if (isset($order->order_custom_fields['_'.$set['condition']['value']['key']])) {
                            $custom_field_value = is_array($order->order_custom_fields['_'.$set['condition']['value']['key']]) ? $order->order_custom_fields['_'.$set['condition']['value']['key']][0] : $order->order_custom_fields['_'.$set['condition']['value']['key']];
                        }

                        // Should we check in order post meta?
                        if ($custom_field_value == null) {
                            $order_meta = get_post_meta($order->id, $set['condition']['value']['key'], true);

                            if ($order_meta == '') {
                                $order_meta = get_post_meta($order->id, '_'.$set['condition']['value']['key'], true);
                            }

                            if ($order_meta != '') {
                                $custom_field_value = is_array($order_meta) ? $order_meta[0] : $order_meta;
                            }
                        }

                        // Should we check in $_POST data?
                        if ($custom_field_value == null && isset($_POST[$set['condition']['value']['key']])) {
                            $custom_field_value = $_POST[$set['condition']['value']['key']];
                        }

                        // Proceed?
                        if ($custom_field_value != null) {
                            if (($set['condition']['value']['operator'] == 'is' && $set['condition']['value']['value'] == $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'is_not' && $set['condition']['value']['value'] != $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'contains' && $set['condition']['value']['value'] != $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'does_not_contain' && $set['condition']['value']['value'] != $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'lt' && $set['condition']['value']['value'] < $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'le' && $set['condition']['value']['value'] <= $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'eq' && $set['condition']['value']['value'] == $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'ge' && $set['condition']['value']['value'] >= $custom_field_value) 
                             || ($set['condition']['value']['operator'] == 'gt' && $set['condition']['value']['value'] > $custom_field_value)) {
                                $proceed_subscription = true;
                            }
                        }

                    }

                    // So, should we proceed with this set?
                    if ($proceed_subscription) {

                        // Get custom fields
                        $custom_fields = array();

                        foreach ($set['fields'] as $custom_field) {
                            if (preg_match('/^order_user_id/', $custom_field['name'])) {
                                $custom_fields[$custom_field['tag']] = $order->user_id;
                            }
                            else if (preg_match('/^order_/', $custom_field['name'])) {
                                $real_field_key = preg_replace('/^order_/', '', $custom_field['name']);
                                if (isset($order->$real_field_key)) {
                                    $custom_fields[$custom_field['tag']] = $order->$real_field_key;
                                }
                            }
                            else if (preg_match('/^user_/', $custom_field['name'])) {
                                $real_field_key = preg_replace('/^user_/', '', $custom_field['name']);
                                if (isset($user_meta[$real_field_key])) {
                                    $custom_fields[$custom_field['tag']] = $user_meta[$real_field_key][0];
                                }
                            }
                        }

                        $this->subscribe($set['list'], $email, $this->opt['woochimp_double_checkout'], $this->opt['woochimp_welcome_checkout'], $set['groups'], ($this->opt['woochimp_replace_groups_checkout'] ? true : false), $custom_fields);
                    }

                }
            }


        }

        /**
         * Subscribe user to mailing list
         * 
         * @access public
         * @param string $list_id
         * @param string $email
         * @return bool
         */
        public function subscribe($list_id, $email, $double_optin = false, $send_welcome = false, $groups = array(), $replace_groups = false, $custom_fields)
        {
            // Load MailChimp
            if (!$this->load_mailchimp()) {
                return false;
            }

            $groupings = array();

            // Any groups to be set?
            if (!empty($groups)) {

                // First make an acceptable structure
                $groups_parent_children = array();

                foreach ($groups as $group) {
                    $parts = preg_split('/:/', htmlspecialchars_decode($group), 2);

                    if (count($parts) == 2) {
                        $groups_parent_children[$parts[0]][] = $parts[1];
                    }
                }

                // Now populate groupings array
                foreach ($groups_parent_children as $parent => $child) {
                    $groupings[] = array(
                        'id' => $parent,
                        'groups' => $child
                    );
                }
            }

            // All merge vars
            $merge_vars = array(
                'groupings' => $groupings,
            );

            foreach ($custom_fields as $key => $value) {
                $merge_vars[$key] = $value;
            }

            // Subscribe
            try {
                $results = $this->mailchimp->lists_subscribe(
                    $list_id,
                    array('email' => $email),
                    $merge_vars,
                    'html',
                    $double_optin,
                    true,
                    $replace_groups,
                    $send_welcome
                );

                return true;
            }
            catch (Exception $e) {
                if (preg_match('/.+is already subscribed to list.+/', $e->getMessage())) {
                    return true;
                }

                return false;
            }
        }

        /**
         * Track campaign
         * 
         * @access public
         * @return void
         */
        public function track_campaign()
        {
            // Prepare site address for use in cookies
            // Note: if site domain is set with www, then this will not work if accessed without it, we can't do this differently
            // because someone might have multiple websites on different subdomains and installation of WooChimp on parent domain
            // would polute subdomain installations with these cookies
            if (isset($_GET['mc_cid']) || isset($_GET['mc_eid'])) {
                $site_domain = get_site_url(get_current_blog_id(), '', 'http');
                $site_domain = str_replace('http://', '', $site_domain);

                $site_domain_parts = explode('/', $site_domain);
                reset($site_domain_parts);

                $domain = array_shift($site_domain_parts);
                $path = empty($site_domain_parts) ? '/' : '/' . implode('/', $site_domain_parts);
                $path = rtrim($path, '/') . '/';
            }

            // Check if mc_cid is set
            if (isset($_GET['mc_cid'])) {
                setcookie('woochimp_mc_cid', $_GET['mc_cid'], time()+7776000, $path, $domain);
            }

            // Check if mc_eid is set
            if (isset($_GET['mc_eid'])) {
                setcookie('woochimp_mc_eid', $_GET['mc_eid'], time()+7776000, $path, $domain);
            }
        }

        /**
         * Add permission checkbox to checkout page
         * 
         * @access public
         * @return void
         */
        public function add_permission_question()
        {
            // Check if functionality is enabled
            if (!$this->opt['woochimp_enabled'] || $this->opt['woochimp_enabled_checkout'] != 3) {
                return;
            }

            echo '<p class="woochimp_checkout_checkbox" style="padding:15px 0;"><input id="woochimp_user_preference" name="woochimp_data[woochimp_user_preference]" type="checkbox" ' . ($this->opt['woochimp_default_state'] == '1' ? 'checked="checked"' : '') . '> ' . $this->opt['woochimp_text_checkout'] . '</p>';
        }

        /**
         * Display subscription form in place of shortcode
         * 
         * @access public
         * @param mixed $attributes
         * @return string
         */
        public function subscription_shortcode($attributes)
        {
            // Check if functionality is enabled
            if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_shortcode']) {
                return '';
            }

            // Prepare form
            $form = woochimp_prepare_form($this->opt, 'shortcode');

            return $form;
        }

        /**
         * Subscribe user from shortcode form
         * 
         * @access public
         * @return void
         */
        public function ajax_subscribe_shortcode()
        {
            // Check if feature is enabled
            if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_shortcode']) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            // Check if data was received
            if (!isset($_POST['data'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $data = array();
            parse_str($_POST['data'], $data);

            // Check if our vars were received
            if (!isset($data['woochimp_shortcode_subscription']) || empty($data['woochimp_shortcode_subscription'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $data = $data['woochimp_shortcode_subscription'];

            // Check if email was received
            if (!isset($data['email']) || empty($data['email'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $email = $data['email'];

            // Parse custom fields
            $custom_fields = array();

            if (isset($data['custom']) && !empty($data['custom'])) {
                foreach ($data['custom'] as $key => $value) {
                    $field_ok = false;

                    foreach ($this->opt['woochimp_shortcode_fields'] as $custom_field) {
                        if ($key == $custom_field['tag']) {
                            $field_ok = true;
                            break;
                        }
                    }

                    if ($field_ok) {
                        $custom_fields[$key] = $value;
                    }
                }
            }

            // Subscribe user
            if ($this->subscribe($this->opt['woochimp_list_shortcode'], $email, $this->opt['woochimp_double_shortcode'], $this->opt['woochimp_welcome_shortcode'], $this->opt['woochimp_groups_shortcode'], ($this->opt['woochimp_replace_groups_shortcode'] ? true : false), $custom_fields)) {
                echo json_encode(array('error' => 0, 'message' => $this->opt['woochimp_label_success']));
                die();
            }

            echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
            die();
        }

        /**
         * Subscribe user from widget form
         * 
         * @access public
         * @return void
         */
        public function ajax_subscribe_widget()
        {
            // Check if feature is enabled
            if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_widget']) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            // Check if data was received
            if (!isset($_POST['data'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $data = array();
            parse_str($_POST['data'], $data);

            // Check if our vars were received
            if (!isset($data['woochimp_widget_subscription']) || empty($data['woochimp_widget_subscription'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $data = $data['woochimp_widget_subscription'];

            // Check if email was received
            if (!isset($data['email']) || empty($data['email'])) {
                echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
                die();
            }

            $email = $data['email'];

            // Parse custom fields
            $custom_fields = array();

            if (isset($data['custom']) && !empty($data['custom'])) {
                foreach ($data['custom'] as $key => $value) {
                    $field_ok = false;

                    foreach ($this->opt['woochimp_widget_fields'] as $custom_field) {
                        if ($key == $custom_field['tag']) {
                            $field_ok = true;
                            break;
                        }
                    }

                    if ($field_ok) {
                        $custom_fields[$key] = $value;
                    }
                }
            }

            // Subscribe user
            if ($this->subscribe($this->opt['woochimp_list_widget'], $email, $this->opt['woochimp_double_widget'], $this->opt['woochimp_welcome_widget'], $this->opt['woochimp_groups_widget'], ($this->opt['woochimp_replace_groups_widget'] ? true : false), $custom_fields)) {
                echo json_encode(array('error' => 0, 'message' => $this->opt['woochimp_label_success']));
                die();
            }

            echo json_encode(array('error' => 1, 'message' => $this->opt['woochimp_label_error']));
            die();
        }

        /**
         * Check if curl is enabled
         * 
         * @access public
         * @return void
         */
        public function curl_enabled()
        {
            if (function_exists('curl_version')) {
                return true;
            }

            return false;
        }

        /**
         * Process MailChimp Webhook call
         * 
         * @access public
         * @return void
         */
        public function process_webhook() {

            // Handle unsubsribe event
            if (!empty($_POST) && isset($_POST['type'])) {
                switch($_POST['type']){

                    // Unsubscribe
                    case 'unsubscribe':

                        // Load user
                        if ($user = get_user_by('email', $_POST['data']['email'])) {

                            $new_flag = array($_POST['data']['list_id']);

                            $existing_flags = get_user_meta($user->ID, 'woochimp_unsubscribed_lists', true);

                            if ($existing_flags != '') {
                                $new_flag = array_merge($existing_flags, $new_flag);
                            }

                            update_user_meta($user->ID, 'woochimp_unsubscribed_lists', $new_flag);

                        }

                        break;

                    // Other available:
                    // case 'subscribe'
                    // case 'cleaned'
                    // case 'upemail'
                    // case 'profile'
                    // case 'campaign'

                    // Default
                    default:
                        break;
                }
            }

            die();
        }

        /**
         * Get all lists plus groups and fields for selected lists in array
         * 
         * @access public
         * @return void
         */
        public function ajax_lists_for_checkout()
        {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
            }
            else {
                $data = array();
            }

            // Get lists
            $lists = $this->get_lists();

            // Check if we have something pre-selected
            if (!empty($data)) {

                // Get merge vars
                $merge = $this->get_merge_vars($lists);

                // Get groups
                $groups = $this->get_groups($this->opt['sets']);

            }
            else {

                $merge = array();
                $groups = array();

                foreach ($lists as $list_key => $list_value) {

                    if ($list_key == '') {
                        continue;
                    }

                    // Blank merge vars
                    $merge[$list_key] = array('' => '');

                    // Blank groups
                    $groups[$list_key] = array('' => '');
                }
            }

            // Add all checkout properties
            $checkout_properties = $this->checkout_properties;

            echo json_encode(array('message' => array('lists' => $lists, 'groups' => $groups, 'merge' => $merge, 'checkout_properties' => $checkout_properties)));
            die();
        }

    }

    $GLOBALS['WooChimp'] = new WooChimp();

}

?>
