<?php
/**
 * Plugin Name: WooCommerce Customer/Order CSV Import Suite
 * Plugin URI: http://www.woothemes.com/extension/customerorder-csv-import-suite/
 * Description: Import customers, orders and coupons straight from the WordPress admin
 * Author: WooThemes / SkyVerge
 * Author URI: http://www.woothemes.com
 * Version: 2.9.0
 * Text Domain: woocommerce-customer-order-csv-import
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2016 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Customer-CSV-Import-Suite
 * @author    SkyVerge
 * @category  Importer
 * @copyright Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'eb00ca8317a0f64dbe185c995e5ea3df', '18709' );

// WC active check/is admin
if ( ! is_woocommerce_active() || ! is_admin() ) {
	return;
}

// Required library classss
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.2.0', __( 'WooCommerce Customer/Order CSV Import', 'woocommerce-customer-order-csv-import' ), __FILE__, 'init_woocommerce_customer_order_csv_import', array( 'minimum_wc_version' => '2.3.6', 'backwards_compatible' => '4.2.0' ) );

function init_woocommerce_customer_order_csv_import() {

/**
 * Customer/Order/Coupon CSV Import Suite Main Class.  This class is responsible
 * for registering the importers and setting up the admin start page/menu
 * items.  The actual import process is handed off to the various parse
 * and import classes.
 *
 * Adapted from the WordPress post importer by the WordPress team
 */
class WC_Customer_CSV_Import_Suite extends SV_WC_Plugin {


	/** version number */
	const VERSION = '2.9.0';

	/** @var WC_Customer_CSV_Import_Suite single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'customer_csv_import_suite';

	/** plugin text domain, DEPRECATED as of 2.9.0 */
	const TEXT_DOMAIN = 'woocommerce-customer-order-csv-import';


	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION );

		// register importers
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// add the menu item
		add_action( 'admin_menu',         array( $this, 'admin_menu' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_scripts' ) );
	}


	/**
	 * Load plugin text domain.
	 *
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-customer-order-csv-import', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/**
	 * Admin Init - register the customer and order importers, handle the
	 */
	public function admin_init() {

		register_importer( 'woocommerce_customer_csv',
							'WooCommerce Customer (CSV)',
							__( 'Import <strong>customers</strong> to your store via a csv file.', 'woocommerce-customer-order-csv-import' ),
							array( $this, 'customer_importer' ) );

		register_importer( 'woocommerce_order_csv',
							'WooCommerce Order (CSV)',
							__( 'Import <strong>orders</strong> to your store via a csv file.', 'woocommerce-customer-order-csv-import' ),
							array( $this, 'order_importer' ) );

		register_importer( 'woocommerce_coupon_csv',
							'WooCommerce Coupon (CSV)',
							__( 'Import <strong>coupons</strong> to your store via a csv file.', 'woocommerce-customer-order-csv-import' ),
							array( $this, 'coupon_importer' ) );
	}


	/**
	 * Returns the "Import" plugin action link to go directly to the plugin
	 * settings page (if any)
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::get_settings_link()
	 * @param string $plugin_id the plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin configure link
	 */
	public function get_settings_link( $plugin_id = null ) {

		$settings_url = $this->get_settings_url( $plugin_id );

		if ( $settings_url ) {
			return sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Import', 'woocommerce-customer-order-csv-import' ) );
		}

		// no settings
		return '';
	}


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $plugin_id the plugin identifier.
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		// link to the import page
		return admin_url( 'admin.php?page=' . self::PLUGIN_ID );
	}


	/**
	 * Gets the plugin documentation url, which is non-standard for this plugin
	 *
	 * @since 2.3.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/customer-order-csv-import-suite/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since VERSION
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'http://support.woothemes.com/';
	}


	/**
	 * Returns true if on the Customer/Order Import page
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the plugin admin settings page
	 */
	public function is_plugin_settings() {
		return isset( $_GET['page'] ) && self::PLUGIN_ID == $_GET['page'];
	}


	/**
	 * Add a submenu item to the WooCommerce menu
	 */
	public function admin_menu() {

		add_submenu_page(
			'woocommerce',
			__( 'CSV Customer Import Suite', 'woocommerce-customer-order-csv-import' ),
			__( 'CSV Customer Import Suite', 'woocommerce-customer-order-csv-import' ),
			'manage_woocommerce',
			self::PLUGIN_ID,
			array( $this, 'admin_page' )
		);

	}


	/**
	 * Include admin scripts
	 */
	public function admin_scripts() {

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
		wp_register_style( 'woocommerce-csv_importer', $this->get_plugin_url() . '/assets/css/admin/wc-customer-order-csv-import.min.css', '', '1.0.0', 'screen' );
		wp_enqueue_style( 'woocommerce-csv_importer' );
	}


	/**
	 * Render the admin page which includes links to the documentation,
	 * sample import files, and buttons to perform the imports
	 */
	public function admin_page() {
		?>

		<div class="wrap woocommerce">
			<div class="icon32" id="icon-woocommerce-importer"><br></div>
			<h2><?php _e( 'Import Customers &amp; Orders', 'woocommerce-customer-order-csv-import' ); ?></h2>

			<?php
				$this->admin_import_page();
			?>

		</div>
		<?php
	}


	/**
	 * Render the body of the admin starting page
	 */
	private function admin_import_page() {

		?>
		<div id="message" class="updated woocommerce-message wc-connect">
			<div class="squeezer">
				<h4><?php _e( '<strong>Customer CSV Import Suite</strong> &#8211; Before getting started prepare your CSV files', 'woocommerce-customer-order-csv-import' ); ?></h4>

				<p class="submit"><a href="<?php echo $this->get_documentation_url(); ?>" class="button-primary"><?php _e( 'Documentation', 'woocommerce-customer-order-csv-import' ); ?></a>
					<a class="docs button-primary" href="http://docs.woothemes.com/wp-content/uploads/2012/07/woocommerce-csv-import-sample-customers.csv"><?php _e( 'Sample Customer CSV', 'woocommerce-customer-order-csv-import' ); ?></a>
					<a class="docs button-primary" href="http://docs.woothemes.com/wp-content/uploads/2012/07/woocommerce-csv-import-sample-orders.csv"><?php _e( 'Sample Order CSV', 'woocommerce-customer-order-csv-import' ); ?></a>
					<a class="docs button-primary" href="http://docs.woothemes.com/wp-content/uploads/2012/07/woocommerce-csv-import-sample-coupons.csv"><?php _e( 'Sample Coupon CSV', 'woocommerce-customer-order-csv-import' ); ?></a>
				<p>
			</div>
		</div>

		<div class="tool-box">

			<h3 class="title"><?php _e( 'Import Customer CSV', 'woocommerce-customer-order-csv-import' ); ?></h3>
			<p><?php _e('Import customers into WooCommerce using this tool.', 'woocommerce-customer-order-csv-import'); ?></p>
			<p class="description"><?php _e( 'Upload a CSV from your computer. Click import to import your CSV as new customers (existing customers will be skipped), or click merge to merge customers. Importing requires the <code>email</code> column, whilst merging requires <code>email</code>, <code>username</code> or <code>id</code>.', 'woocommerce-customer-order-csv-import' ); ?></p>

			<p class="submit"><a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_customer_csv' ); ?>"><?php _e( 'Import Customers', 'woocommerce-customer-order-csv-import' ); ?></a> <a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_customer_csv&merge=1' ); ?>"><?php _e( 'Merge Customers', 'woocommerce-customer-order-csv-import' ); ?></a></p>

		</div>

		<div class="tool-box">

			<h3 class="title"><?php _e( 'Import Orders CSV', 'woocommerce-customer-order-csv-import' ); ?></h3>
			<p><?php _e( 'Import and add orders using this tool.', 'woocommerce-customer-order-csv-import' ); ?></p>
			<p class="description"><?php _e( 'Upload a CSV from your computer to import previous orders.', 'woocommerce-customer-order-csv-import'); ?></p>
			<p class="submit"><a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_order_csv' ); ?>"><?php _e( 'Import Orders', 'woocommerce-customer-order-csv-import' ); ?></a></p>

		</div>

		<div class="tool-box">

			<h3 class="title"><?php _e( 'Import Coupons CSV', 'woocommerce-customer-order-csv-import' ); ?></h3>
			<p><?php _e( 'Import and add coupons using this tool.', 'woocommerce-customer-order-csv-import' ); ?></p>
			<p class="description"><?php _e( 'Import a CSV from your computer. Click import to import your CSV as new coupons (existing coupons will be skipped), or click merge to merge coupons.', 'woocommerce-customer-order-csv-import'); ?></p>
			<p class="submit"><a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_coupon_csv' ); ?>"><?php _e( 'Import Coupons', 'woocommerce-customer-order-csv-import' ); ?></a>  <a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_coupon_csv&merge=1' ); ?>"><?php _e( 'Merge Coupons', 'woocommerce-customer-order-csv-import' ); ?></a></p></p>

		</div>
		<?php
	}


	/**
	 * Customer Importer Tool
	 *
	 * Registered callback function for the WordPress Importer
	 */
	public function customer_importer() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) return;

		// Load Importer API
		require_once( ABSPATH . 'wp-admin/includes/import.php' );

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require $class_wp_importer;
			}
		}

		// includes
		require_once( $this->get_plugin_path() . '/classes/class-wc-customer-import.php' );
		require_once( $this->get_plugin_path() . '/classes/class-wc-csv-parser.php' );
		require_once( $this->get_plugin_path() . '/classes/class-wc-csv-log.php' );

		// Dispatch
		global $WC_CSV_Import;

		$WC_CSV_Import = new WC_CSV_Customer_Import();

		$WC_CSV_Import->dispatch();
	}


	/**
	 * Order Importer Tool
	 *
	 * Registered callback function for the WordPress Importer
	 */
	public function order_importer() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) return;

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require $class_wp_importer;
			}
		}

		// includes
		require_once( $this->get_plugin_path() . '/classes/class-wc-order-import.php' );
		require_once( $this->get_plugin_path() . '/classes/class-wc-csv-parser.php' );
		require_once( $this->get_plugin_path() . '/classes/class-wc-csv-log.php' );

		// Dispatch
		global $WC_CSV_Import;

		$WC_CSV_Import = new WC_CSV_Order_Import();

		$WC_CSV_Import->dispatch();

	}


	/**
	 * Coupons Importer Tool
	 *
	 * Registered callback function for the WordPress Importer
	 */
	public function coupon_importer() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) return;

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) require $class_wp_importer;
		}

		// includes
		require_once( $this->get_plugin_path() . '/classes/class-wc-coupon-import.php' );
		require_once(  $this->get_plugin_path() . '/classes/class-wc-csv-parser.php' );
		require_once(  $this->get_plugin_path() . '/classes/class-wc-csv-log.php' );

		// Dispatch
		global $WC_CSV_Import;

		$WC_CSV_Import = new WC_CSV_Coupon_Import();

		$WC_CSV_Import->dispatch();

	}


	/** Helper methods ******************************************************/


	/**
	* Main Customer/Order CSV Import Suite Instance, ensures only one instance is/can be loaded
	*
	* @since 2.7.0
	* @see wc_customer_csv_import()
	* @return WC_Customer_CSV_Import_Suite
	*/
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 2.3
	 * @see SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Customer/Order CSV Import', 'woocommerce-customer-order-csv-import' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 2.3
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


} // class WC_Customer_CSV_Import_Suite


/**
 * Returns the One True Instance of Customer/Order CSV Import Suite
 *
 * @since 2.7.0
 * @return WC_Customer_CSV_Import_Suite
*/
function wc_customer_csv_import() {
	return WC_Customer_CSV_Import_Suite::instance();
}


// fire it up!
wc_customer_csv_import();

} // init_woocommerce_customer_order_csv_import()
