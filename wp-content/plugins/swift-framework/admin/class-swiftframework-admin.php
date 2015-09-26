<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://swiftideas.com/swift-framework
 * @since      1.0.0
 *
 * @package    swift-framework
 * @subpackage swift-framework/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    swift-framework
 * @subpackage swift-framework/admin
 * @author     Swift Ideas
 */
class SwiftFramework_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $SwiftFramework    The ID of this plugin.
	 */
	private $SwiftFramework;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $SwiftFramework       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $SwiftFramework, $version ) {

		$this->SwiftFramework = $SwiftFramework;
		$this->version = $version;

	}

	public function add_swiftframework_menu() {
		//add_menu_page( 'Swift Framework', 'Swift Framework', 'manage_options', 'swift-framework/admin/swift-framework-admin-page.php', '', plugin_dir_url(__FILE__).'/img/logo.png', 100 );
		add_menu_page(
		    'Swift Framework',
		    'Swift Framework',
		    'manage_options',
		    'swift-framework',
		    array($this, 'swift_framework_about_content'),
		    plugin_dir_url(__FILE__).'/img/logo.png'
		);
	}

	public function swift_framework_about_content() {
	  ?>
		<div class="sf-about-wrap">
		<h1>Swift Framework</h1>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>/index.php?page=swift-framework">About</a>
			<a class="nav-tab" href="<?php echo admin_url() ?>/admin.php?page=swift_framework_opts_options">Options</a>
		</h2>

		<div class="about-content">
			<h3>Latest Update (v1.63)</h3>
			<p></p>
			<ul>
				<li>Updated Redux framework</li>
			</ul>
			<h3>Previous Update (v1.62)</h3>
			<p></p>
			<ul>
				<li>Team gallery display images are now clickable on mobile</li>
				<li>Updated minified files after previous update</li>
				<li>Updated Redux framework</li>
			</ul>
			<p></p>
		</div>

		</div>
	  <?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SwiftFramework_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SwiftFramework_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->SwiftFramework, plugin_dir_url( __FILE__ ) . 'css/swiftframework-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SwiftFramework_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SwiftFramework_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->SwiftFramework, plugin_dir_url( __FILE__ ) . 'js/swiftframework-admin.js', array( 'jquery' ), $this->version, false );

	}

}
