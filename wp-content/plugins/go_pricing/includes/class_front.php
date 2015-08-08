<?php
/**
 * Front core class
 */


// Prevent direct call
if ( !defined( 'WPINC' ) ) die;
if ( !class_exists( 'GW_GoPricing' ) ) die;	

// Class
class GW_GoPricing_Front {	

	protected static $instance = null;
	protected $globals;

	protected static $plugin_version;
	protected static $db_version;
	protected static $plugin_prefix;
	protected static $plugin_slug;
	protected $plugin_file;
	protected $plugin_base;
	protected $plugin_dir;
	protected $plugin_path;
	protected $plugin_url;


	/**
	 * Initialize the class
	 *
	 * @return void
	 */		  
	 
	public function __construct() {
		
		$this->globals = GW_GoPricing::instance();
		self::$plugin_version = $this->globals['plugin_version'];
		self::$db_version = $this->globals['db_version'];		
		self::$plugin_prefix = $this->globals['plugin_prefix'];
		self::$plugin_slug = $this->globals['plugin_slug'];
		$this->plugin_url = $this->globals['plugin_url'];

		// Enqueue frontend styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );
		
		// Enqueue frontend scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_scripts' ) );	
		
	}


	/**
	 * Return an instance of this class
	 *
	 * @return object
	 */
	 
	public static function instance() {
		
		if ( self::$instance == null ) self::$instance = new self;
		return self::$instance;
		
	}
	
	
	/**
	 * Enqueue public styles
	 *
	 * @return void
	 */
	 
	 
	public function enqueue_public_styles() {
		
		wp_enqueue_style( self::$plugin_slug . '-styles', $this->plugin_url . 'assets/css/go_pricing_styles.css', false, self::$plugin_version );

		$general_settings = get_option( self::$plugin_prefix . '_table_settings' );		
		if ( !empty( $general_settings['custom-css'] ) ) wp_add_inline_style( self::$plugin_slug . '-styles', $general_settings['custom-css'] );
		
	}
	
	
	/**
	 * Register public scripts
	 *
	 * @return void
	 */	
	
	public function register_public_scripts() {
		
		wp_register_script( self::$plugin_slug . '-scripts', $this->plugin_url . 'assets/js/go_pricing_scripts.js', 'jquery', self::$plugin_version, true );
		wp_register_script( self::$plugin_slug . '-map', 'http://maps.google.com/maps/api/js?sensor=false', false, self::$plugin_version, true );
		wp_register_script( self::$plugin_slug . '-gomap', $this->plugin_url . 'assets/lib/js/jquery.gomap-1.3.2.min.js', array( 'jquery', self::$plugin_slug . '-map' ), self::$plugin_version, true );

		global $wp_version;
		if ( version_compare( $wp_version, 3.6, "<" ) ) wp_register_script( self::$plugin_slug . '-mediaelementjs', $this->plugin_url . 'assets/plugins/js/mediaelementjs/mediaelement-and-player.min.js', 'jquery', self::$plugin_version, true );

	}	

}

 

?>