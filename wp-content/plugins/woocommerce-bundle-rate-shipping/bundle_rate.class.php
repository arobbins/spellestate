<?php 

class enda_woocommerce_bundlerate_shipping extends WC_Shipping_Method {

    /**
     * Class constructor
     */
    public function __construct() { 

        $this->id = 'enda_bundle_rate';
        $this->method_title = __('Bundle rate', 'woocommerce-bundle-rate-shipping');

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables
        $this->enabled = $this->settings['enabled'];
        $this->title = $this->settings['title'];
        $this->availability = $this->settings['availability'];
        $this->countries = $this->settings['countries'];
        $this->tax_status = $this->settings['tax_status'];
        $this->fee = $this->settings['fee']; 
        $this->apply_base_rate_once = (bool) $this->settings['apply_base_rate_once'];

        // Bundle rates
        $this->bundle_rates = self::get_bundle_rates();        
        
        // WooCommerce < 1.5.4
        add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_admin_options'));
        add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_bundle_rates'));
        
        // WooCommerce >= 1.5.4        
        add_action('woocommerce_update_options_shipping_enda_bundle_rate', array(&$this, 'process_admin_options'));
        add_action('woocommerce_update_options_shipping_enda_bundle_rate', array(&$this, 'process_bundle_rates'));        
    }   

    /**
     * Initialise Gateway Settings Form Fields
     */
    function init_form_fields() {
        global $woocommerce;

        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'woocommerce-bundle-rate-shipping' ), 
                'type'          => 'checkbox', 
                'label' 		=> __( 'Enable Bundle Rate shipping', 'woocommerce-bundle-rate-shipping' ), 
                'default' 		=> 'yes'
            ), 
            'title' => array(
                'title' 		=> __( 'Method Title', 'woocommerce-bundle-rate-shipping' ), 
                'type'          => 'text', 
                'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce-bundle-rate-shipping' ), 
                'default'		=> __( 'Bundle Rate', 'woocommerce-bundle-rate-shipping' )
            ),
            'availability' => array(
                'title' 		=> __( 'Method availability', 'woocommerce-bundle-rate-shipping' ), 
                'type'          => 'select', 
                'default' 		=> 'all',
                'class'         => 'availability',
                'options'		=> array(
                    'all' 		=> __('All allowed countries', 'woocommerce-bundle-rate-shipping'),
                    'specific'  => __('Specific Countries', 'woocommerce-bundle-rate-shipping')
                )
            ),
            'countries' => array(
                'title' 		=> __( 'Specific Countries', 'woocommerce-bundle-rate-shipping' ), 
                'type'          => 'multiselect', 
                'class'         => 'chosen_select',
                'css'           => 'width: 450px;',
                'default' 		=> '',
                'options'		=> $woocommerce->countries->countries
            ),
            'tax_status' => array(
                'title' 		=> __( 'Tax Status', 'woocommerce-bundle-rate-shipping' ), 
                'type'          => 'select', 
                'description'   => '', 
                'default' 		=> 'taxable',
                'options'		=> array(
                    'taxable'   => __('Taxable', 'woocommerce-bundle-rate-shipping'),
                    'none' 		=> __('None', 'woocommerce-bundle-rate-shipping')
                )
            ), 
            'fee' => array(
                'title' 		=> __( 'Handling Fee', 'woocommerce-bundle-rate-shipping' ), 
                'type' 			=> 'text', 
                'description'   => __('Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-bundle-rate-shipping'),
                'default'		=> ''
            ),
            'apply_base_rate_once' => array(
                'title'         => __( 'Only apply the base rate of most expensive configuration used', 'woocommerce-bundle-rate-shipping' ),
                'type'          => 'select',
                'description'   => __( 'If the shipping total for the cart is calculating using more than one of the shipping rate configurations below, only apply the base rate of the most expensive configuration.', 'woocommerce-bundle-rate-shippin' ), 
                'default'       => '1',
                'options'       => array(
                    '1'         => __( 'Yes', 'woocommerce-bundle-rate-shipping' ),
                    '0'         => __( 'No', 'woocommerce-bundle-rate-shipping' ) 
                ) 
            )
        );
    } 

    /**
     * Add the configuration options HTML to the Shipping settings page 
     *
     * @since 1.0.0
     */
    public function admin_options() {
        global $woocommerce;        

        $configurations = empty( $this->bundle_rates ) ? array( array() ) : $this->bundle_rates;
        ?>

        <h3><?php _e('Bundle Rates', 'woocommerce-bundle-rate-shipping'); ?></h3>
        <p><?php _e('Bundle rates can be defined to set up tiered shipping rates for items within the same category.', 'woocommerce-bundle-rate-shipping'); ?></p>
        <table class="form-table">
        <?php
            // Generate the HTML For the settings form.
            $this->generate_settings_html();
            ?>     
          
            <tr valign="top">
                <th scope="row" class="titledesc"><?php _e('Bundle Rates', 'woocommerce-bundle-rate-shipping') ?>:</th>
                <td class="forminp">
                    <table id="bundle_rate_configuration" cellspacing="0" class="widefat">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?php _e('Rates', 'woocommerce-bundle-rate-shipping') ?></th>
                                <th><?php _e('Shipping Destination', 'woocommerce-bundle-rate-shipping') ?></th>
                                <th><?php _e('Category', 'woocommerce-bundle-rate-shipping') ?></th>
                                <th><?php _e('Shipping Class', 'woocommerce-bundle-rate-shipping') ?></th>
                                <th><?php _e('Priority', 'woocommerce-bundle-rate-shipping') ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="5"><a class="add_configuration button-primary" href=""><?php _e('+ Add Configuration', 'woocommerce-bundle-rate-shipping') ?></a></td>
                                <td colspan="1"><button class="button" name="remove_configurations"><?php _e('Remove Selected', 'woocommerce-bundle-rate-shipping') ?></button></td>
                            </tr>
                        </tfoot>
                        <tbody> 

                        <?php foreach ( $configurations as $key => $configuration ) : ?>

                            <?php $this->display_configuration_layer( $key, $configuration ) ?>

                        <?php endforeach ?>

                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Return HTML for a new configuration layer. Also used in an Ajax hook.
     * @param int $key
     * @param array $configuration
     */
    function display_configuration_layer( $key = '', $configuration = array() ) {
        global $woocommerce;

        $is_post = false;
        if ( !empty( $_POST ) ) {
            $is_post = true;
            $key = $_POST['index'];
        }
        ?>
        <tr>                            
            <td class="remove">
                <input type="checkbox" name="remove[]" value="<?php echo $key ?>" />
            </td>
            <td class="shipping_rates">
                <table>
                    <thead>
                        <tr>
                            <th><?php _e('Number of products', 'woocommerce-bundle-rate-shipping') ?></th>
                            <th colspan="2"><?php _e('Cost per product', 'woocommerce-bundle-rate-shipping') ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $rates = array_key_exists( 'rates', $configuration ) ? $configuration['rates'] : array( 
                            0 => array('products', 'cost'), 
                            1 => array('products', 'cost') ) ?>

                        <?php for ( $i = 0; $i < count( $rates ); $i++ ) : ?>    
                        
                            <?php if ($i == 0) : ?>

                                <tr class="rate_row">
                                    <td>
                                        <?php _e('First', 'woocommerce-bundle-rate-shipping') ?>
                                        <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][products]" value="<?php echo array_key_exists( 'products', $rates[0] ) ? htmlentities($rates[0]['products']) : '' ?>" class="bundle_rate_products" />
                                        <?php _e('products', 'woocommerce-bundle-rate-shipping') ?>
                                    </td>
                                    <td>
                                        <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][cost]" value="<?php echo array_key_exists( 'cost', $rates[0] ) ? htmlentities($rates[0]['cost']) : '' ?>" />
                                    </td>
                                    <td></td>
                                </tr>            

                            <?php elseif ($i == count($rates) - 1) : ?>

                                <tr class="rate_row">
                                    <td>
                                        <?php _e('All subsequent products', 'woocommerce-bundle-rate-shipping') ?>
                                        <input type="hidden" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][products]" value="+" />
                                    </td>
                                    <td>
                                        <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][cost]" value="<?php echo array_key_exists( 'cost', $rates[$i] ) ? htmlentities($rates[$i]['cost']) : '' ?>" />
                                    </td>     
                                    <td></td>                                   
                                </tr>

                            <?php else : ?>

                                <tr class="rate_row">
                                    <td>
                                        <?php _e('From', 'woocommerce-bundle-rate-shipping') ?> <span class="start_count"><?php echo ((int) $rates[$i-1]['products'] + 1) ?></span> 
                                        <?php _e('to', 'woocommerce-bundle-rate-shipping') ?> <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][products]" value="<?php echo htmlentities($rates[$i]['products']) ?>" class="bundle_rate_products" /> 
                                        <?php _e('products', 'woocommerce-bundle-rate-shipping') ?>
                                    </td>
                                    <td>
                                        <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][rates][<?php echo $i ?>][cost]" value="<?php echo array_key_exists( 'cost', $rates[$i] ) ? htmlentities($rates[$i]['cost']) : '' ?>" />
                                    </td>
                                    <td>
                                        <button class="remove_bundle_layer button"><?php _e('Remove', 'woocommerce-bundle-rate-shipping') ?></button>
                                    </td>
                                </tr>             

                            <?php endif ?>

                        <?php endfor ?>

                        <tr>
                            <td colspan="3">
                                <a class="add_layer button" href=""><?php _e('+ Add Layer', 'woocommerce-bundle-rate-shipping') ?></a>
                            </td>
                        </tr>
                    </tbody>
                </table>                                                            
            </td>
            <td class="shipping_destination">
                <div>
                    <select name="woocommerce_enda_bundle_rates[<?php echo $key ?>][destination]" class="select destination">
                        <option value="all" <?php if ( array_key_exists('destination', $configuration) ) selected( 'all', $configuration['destination'] ) ?>><?php _e('Apply to all', 'woocommerce-bundle-rate-shipping') ?></option>
                        <option value="specific" <?php if ( array_key_exists('destination', $configuration) ) selected( 'specific', $configuration['destination'] ) ?>><?php _e('Apply to specific countries', 'woocommerce-bundle-rate-shipping') ?></option>                                    
                    </select>
                </div>
                <div class="specific_countries">
                    <label for=""><?php _e( 'Specific Countries', 'woocommerce-bundle-rate-shipping' ) ?></label>
                    <select class="multiselect chosen_select" multiple style="width: 200px;" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][countries][]">
                        <?php self::country_dropdown_options( $configuration ) ?>
                    </select>
                </div>
            </td>
            <td class="shipping_category">
                <select name="woocommerce_enda_bundle_rates[<?php echo $key ?>][category]">
                    <option value="all" <?php if ( array_key_exists('category', $configuration) ) selected( 'all', $configuration['category'] ) ?>><?php _e('Apply to all', 'woocommerce-bundle-rate-shipping') ?></option>
                    <?php foreach ( get_terms('product_cat', array('hide_empty' => 0)) as $category ) : ?>
                        <option value="<?php echo $category->term_id ?>" <?php if ( array_key_exists('category', $configuration) ) selected( $category->term_id, $configuration['category'] ) ?>><?php echo $category->name ?></option>
                    <?php endforeach ?>
                </select>
            </td>
            <td class="shipping_class">
                <select name="woocommerce_enda_bundle_rates[<?php echo $key ?>][shipping_class]">                
                    <option value="all" <?php if ( array_key_exists( 'shipping_class', $configuration ) ) selected( 'all', $configuration['shipping_class'] ) ?>><?php _e('Apply to all', 'woocommerce-bundle-rate-shipping') ?></option>
                    <?php foreach ( get_terms('product_shipping_class', array('hide_empty' => 0)) as $category ) : ?>
                    <option value="<?php echo $category->term_id ?>" <?php if ( array_key_exists( 'shipping_class', $configuration ) ) selected( $category->term_id, $configuration['shipping_class'] ) ?>><?php echo $category->name ?></option>
                    <?php endforeach ?>
                </select>
            </td>
            <td class="priority">
                <input type="text" name="woocommerce_enda_bundle_rates[<?php echo $key ?>][priority]" value="<?php echo array_key_exists('priority', $configuration) ? $configuration['priority'] : '0' ?>" />
            </td>
        </tr>
        <?php
        if ( $is_post ) {
            ?>
            <script>
                ( function($) {
                    $("select.chosen_select").chosen();
                } )( jQuery );                
            </script>
            <?php
            die();
        }
    }    

    /**
     * Add a new layer. Used by ajax.     
     */ 
    function display_layer() {        
        ?>

        <tr class="rate_row">
            <td><?php _e('From', 'woocommerce-bundle-rate-shipping') ?> <span class="start_count"><?php echo $_POST['start_count'] ?></span> 
                <?php _e('to', 'woocommerce-bundle-rate-shipping') ?> <input type="text" name="<?php echo $_POST['products_input'] ?>" class="bundle_rate_products" /> 
                <?php _e('products', 'woocommerce-bundle-rate-shipping') ?>
            </td>
            <td><input type="text" name="<?php echo $_POST['cost_input'] ?>" value="" />
            <td><button class="remove_bundle_layer button"><?php _e('Remove', 'woocommerce-bundle-rate-shipping') ?></button></td>
        </tr>        

        <?php
        
        die;
    }

    /**
     * Save submitted configuration options
     * @return void
     */
    function process_bundle_rates() {
        if ( isset( $_POST['remove_configurations'] ) ) {
            if ( isset( $_POST['remove'] ) ) {
                $bundle_rates = self::get_bundle_rates();
                foreach ( $_POST['remove'] as $key ) {
                    unset( $bundle_rates[$key] );
                }
                
                update_option('woocommerce_enda_bundle_rates', array_values( $bundle_rates ));
            }            
        }
        elseif ( isset( $_POST['woocommerce_enda_bundle_rates'] ) ) {
            update_option('woocommerce_enda_bundle_rates', array_values( $_POST['woocommerce_enda_bundle_rates'] ) );        
        }        

        $this->bundle_rates = self::get_bundle_rates();
    }
    
    /** 
     * Calculate shipping cost for cart
     * @return void
     */
    public function calculate_shipping() {
        global $woocommerce;
        
        $shipping_total = 0;

        $country = $woocommerce->customer->get_shipping_country();
        $state = $woocommerce->customer->get_shipping_state();
        $applicable_configurations = $this->get_destination_configurations( $country, $state );   

        // If there is no configuration that can be applied to this shipping destination, 
        // this method is not available
        if ( count( $applicable_configurations ) == 0 ) {
            return;
        }

        $rates = array();

        // Check whether each item in the cart has an applicable configuration
        foreach ($woocommerce->cart->get_cart() as $item ) {

            // Don't count virtual products
            if ( $item['data']->is_virtual() === false ) {
            
                $item_configurations = $this->get_item_configurations( $item, $applicable_configurations );

                if ( count( $item_configurations ) == 0 ) {
                    return;
                }

                $configuration_id = $this->get_pricing_configuration_id( $item, $item_configurations, $country, $state );

                if ( array_key_exists( $configuration_id, $rates ) ) {
                    $rates[$configuration_id] += $item['quantity'];
                }
                else {
                    $rates[$configuration_id] = $item['quantity'];
                }            
            }
        }        

        // Get applicable configurations
        $configurations = array();
        
        foreach ( $rates as $configuration_id => $quantity ) {
            $configuration = $applicable_configurations[$configuration_id];
            $configuration['quantity'] = $quantity;
            $configurations[] = $configuration;
        }

        // If we are only applying the base rate once, sort configurations by base rate
        if ( $this->apply_base_rate_once ) {
            usort( $configurations, array( &$this, 'sort_configurations_by_price' ) );
        }        
        // Start adding together cost of shipping, one configuration at a time
        $first = true;
        foreach ( $configurations as $configuration ) {
            if ( $first === true || $this->apply_base_rate_once === false ) {
                $shipping_total += $this->get_configuration_subtotal( $configuration['rates'], $configuration['quantity'] );
                $first = false;
            }            
            else {
                $shipping_total += $this->get_configuration_subtotal( $configuration['rates'], $configuration['quantity'], 1 );
            }
        }        

        // Add handling fee
        if ($this->fee > 0) {
            $shipping_total += $this->get_fee( $this->fee, $woocommerce->cart->cart_contents_total );
        }

        // WooCommerce v1.4+
        if (method_exists($this, 'add_rate'))
        {
            $args = array(
              'id' => $this->id,
              'label' => $this->title,
              'cost' => $shipping_total
            );

            $this->add_rate($args);
        }
        // Older versions of WooCommerce
        else
        {
            $this->shipping_total = $shipping_total;

            // Calculate tax
            $_tax = &new woocommerce_tax(); 
            $this->shipping_tax = 0;

            // Calculate tax if required
            if ( get_option('woocommerce_calc_taxes') == 'yes' && $this->tax_status=='taxable' ) {
                $rate = $_tax->get_shipping_tax_rate();
                if ( $rate > 0 ) {
                    $tax_amount = $_tax->calc_shipping_tax( $this->shipping_total, $rate );
                    $this->shipping_tax = $this->shipping_tax + $tax_amount;
                }
            }                                
        }  

        return;   
    }

    /**
     * Return subtotal for configuration
     * @param array $configuration
     * @param int $count
     * @param int $i
     * @param float $subtotal
     * @param int $counted
     * @return string
     */
    function get_configuration_subtotal($configuration, $count, $i=0, $subtotal = 0, $counted = 0) {        

        // All remaining products will be counted in this round
        if ($configuration[$i]['products'] == '+') {
            $to_count = $count - $counted;
        }
        else {
            $to_count = $configuration[$i]['products'] > $count 
                        ? $count - $counted 
                        : $configuration[$i]['products'] - $counted;
        }        

        $subtotal = $subtotal + ($configuration[$i]['cost'] * $to_count);
        $counted = $counted + $to_count;

        if ($count > $counted) {
            $i += 1;               
            return $this->get_configuration_subtotal($configuration, $count, $i, $subtotal, $counted);
        }

        return $subtotal;   
    }    

    /**
     * Return whether the bundle rate shipping method is enabled
     * @static
     * @return bool
     */
    static function is_enabled() {
        $settings = get_option('woocommerce_enda_bundle_rate_settings');
        return $settings['enabled'] == 'yes' ? true : false;
    }

    /**
     * Get bundle rates
     * @static
     * @return array
     */
    static function get_bundle_rates() {
        return array_filter((array) get_option('woocommerce_enda_bundle_rates'));
    }

    /**
     * Check whether method is available 
     */  
    function is_available( $package = array() ) {
        global $woocommerce;        

        if ( $this->enabled == "no" ) 
            return false;
        
        if ( isset( $woocommerce->cart->cart_contents_total ) && isset( $this->min_amount ) && $this->min_amount && $this->min_amount > $woocommerce->cart->cart_contents_total ) 
            return false;

        // Make sure method is available for shipping destination
        $country = isset( $package['destination']['country'] ) ? $package['destination']['country'] : $woocommerce->customer->get_shipping_country();
        $state = $woocommerce->customer->get_shipping_state();

        $ship_to_countries = '';

        if ( $this->availability == 'specific' ) {
            $ship_to_countries = $this->countries;
        } 
        else {
            if ( get_option( 'woocommerce_allowed_countries' ) == 'specific' ) {
                $ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
            }
        } 
        
        if ( is_array( $ship_to_countries ) ) {
            if ( ! in_array( $country, $ship_to_countries ) ) {
                return false;  
            } 
        }
        
        $applicable_configurations = $this->get_destination_configurations( $country, $state );   

        // If there is no configuration that can be applied to this shipping destination, 
        // this method is not available
        if ( count( $applicable_configurations ) == 0 ) {
            return false;
        }

        // Check whether each item in the cart has an applicable configuration
        foreach ($woocommerce->cart->get_cart() as $item ) {

            $item_configurations = $this->get_item_configurations( $item, $applicable_configurations );

            if ( count( $item_configurations ) == 0 ) {
                return false;
            }
        }

        return true;
    }    

    /**
     * Return configurations that can be applied to the shipping destination
     * @param string $country
     * @return array
     */
    function get_destination_configurations( $country, $state = '' ) {
        $applicable_configs = array();        

        foreach ( self::get_bundle_rates() as $configuration ) {
            if ( $configuration['destination'] == 'all' ) {
                $applicable_configs[base64_encode( serialize( $configuration ) )] = $configuration;
            }
            elseif ( $configuration['destination'] == 'specific' ) {
                if ( isset( $configuration['countries'] ) && is_array( $configuration['countries'] ) ) {

                    if ( in_array( $country, $configuration['countries'] ) ) {
                        $applicable_configs[base64_encode( serialize( $configuration ) )] = $configuration;
                    }

                    if ( $state && in_array( $country .':' . $state, $configuration['countries'] ) ) {
                        $applicable_configs[base64_encode( serialize( $configuration ) )] = $configuration;
                    }
                }                
            }
        }         

        return $applicable_configs;
    }

    /**
     * Return configurations that can be applied to the item
     * @param array $item
     * @param array $configurations
     * @return array
     */
    function get_item_configurations( $item, $configurations ) {
        $categories = wp_get_object_terms( $item['product_id'], 'product_cat' );            
        $shipping_classes = $item['data']->get_shipping_class_id();
        $shipping_classes = is_array( $shipping_classes ) ? $shipping_classes : array( $shipping_classes );

        $applicable_configurations = array();

        foreach ( $configurations as $configuration ) {

            $applicable = false;
            if ( $configuration['category'] == 'all' ) {
                $applicable = true;
            }            
            else {
                foreach ( $categories as $category ) {
                    if ( $category->term_id == $configuration['category'] ) {
                        $applicable = true;
                    }
                }                
            }

            if ( $applicable === true ) {
                if ( $configuration['shipping_class'] == 'all' ) {
                    $applicable_configurations[] = $configuration;
                }            
                else {                    
                    foreach ( $shipping_classes as $shipping_class ) {
                        if ( $shipping_class == $configuration['shipping_class'] ) {
                            $applicable_configurations[] = $configuration;
                        }
                    }                
                }
            }            
        }

        return $applicable_configurations;
    }

    /**
     * Get pricing configuration to apply to product
     * @param array $item
     * @param array $configurations
     * @return int
     */
    protected function get_pricing_configuration_id( $item, $configurations, $country, $state = "" ) {
        // If there is only one configuration available, return it
        if ( count( $configurations ) == 1 ) {
            $configuration = $configurations[0];
        }

        // Order by priority, with highest priority first in the array (0 is higher priority than 1)
        usort( $configurations, array( &$this, 'sort_configurations_by_priority' ) );        
        $top_priority = $configurations[0]['priority'];
        $prioritized = array();

        // Get only configurations with priority matching the top priority
        foreach ( $configurations as $configuration ) {
            if ( $configuration['priority'] == $top_priority ) {
                $prioritized[] = $configuration;
            }
        }

        // If still more than one, see if one is destination-specific        
        if ( count( $prioritized ) > 1 ) {

            // Reuse this variable
            $configurations = array();
            foreach ( $prioritized as $configuration ) {
                if ( $configuration['destination'] == 'specific' ) {
                    $configurations[] = $configuration;
                }
            }

            // Now select either the first of the prioritized configurations (if no destination-specific configurations), 
            // or the first of the destination-specific configurations
            if ( count( $configurations ) ) {

                // Check if any of the destination-specific configurations are state-specific
                if ( count( $configurations ) > 1 && strlen( $state ) ) {
                    $state_configurations = array();
                    $destination_key = $country.":".$state;
                    foreach ( $configurations as $configuration ) {
                        if ( in_array( $destination_key, $configuration['countries'] ) ) {
                            $state_configurations[] = $configuration;
                        }
                    }

                    // If there are some state-specific configurations, choose the first
                    if ( count( $state_configurations ) ) {
                        $configuration = $state_configurations[0];
                    }
                    // If there are none, just pick the first of the country-specific ones
                    else {
                        $configuration = $configurations[0];    
                    }
                }    
                // If there is no state set, just pick the first one            
                else {
                    $configuration = $configurations[0];
                }                
            }
            else {
                $configuration = $prioritized[0];
            }
        }
        else {
            $configuration = $prioritized[0];
        }

        $configuration_id = base64_encode( serialize( $configuration ) );
        return $configuration_id;
    }

    /**
     * Sort configurations by priority
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function sort_configurations_by_priority( $a, $b ) {
        $a = $a['priority'];
        $b = $b['priority'];

        if ( $a == $b ) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    /**
     * Sort configurations by price
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function sort_configurations_by_price( $a, $b ) {
        $a = $a['rates'][0]['cost'];
        $b = $b['rates'][0]['cost'];

        if ( $a == $b ) {
            return 0;
        }

        return $a < $b ? 1 : -1;
    }    

    /**
     * Outputs the list of countries and states for use in dropdown boxes.
     *
     * @access public
     * @param array $configuration
     * @param bool $escape (default: false)
     * @return void
     */
    public static function country_dropdown_options( $configuration, $escape = false ) {
        global $woocommerce;

        // Get the WC_Countries class
        $countries = $woocommerce->countries;

        $selected = array_key_exists('countries', $configuration) ? $configuration['countries'] : array();

        if ( apply_filters('woocommerce_sort_countries', true ) )
            asort( $countries->countries );

        if ( $countries->countries ) foreach ( $countries->countries as $key=>$value) :
            if ( $states =  $countries->get_states($key) ) :                
                echo '<optgroup label="'.$value.'">';

                    // Add the country as a whole as a setting
                    echo '<option value="'.$key.'"';
                    selected( in_array( $key, $selected ) ); 
                    echo '>'.$value.'</option>';

                    foreach ($states as $state_key => $state_value) :
                        echo '<option value="'.$key.':'.$state_key.'"';

                        selected( in_array( $key.':'.$state_key, $selected ) ); 

                        echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
                    endforeach;
                echo '</optgroup>';
            else :
                echo '<option';
                selected( in_array( $key, $selected ) ); 
                echo ' value="'.$key.'">'. ($escape ? esc_js( $value ) : $value) .'</option>';
            endif;
        endforeach;
    }
}  