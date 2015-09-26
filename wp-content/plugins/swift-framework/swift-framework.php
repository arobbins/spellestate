<?php

/**
	*
	*	Plugin Name: Swift Framework
	*	Plugin URI: http://www.swiftideas.com/swift-framework/
	*	Description: The Swift Framework plugin.
	*	Version: 1.63
	*	Author: Swift Ideas
	*	Author URI: http://swiftideas.com
	*	Requires at least: 3.6
	*	Tested up to: 4.3
	*
	*	Text Domain: swift-framework-plugin
	*	Domain Path: /languages/
	*
	*	@package Swift Framework
	*	@category Core
	*	@author Swift Ideas
	*
	**/

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-swiftframework-activator.php
	 */
	function activate_swiftframework() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-swiftframework-activator.php';
		SwiftFramework_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-swiftframework-deactivator.php
	 */
	function deactivate_swiftframework() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-swiftframework-deactivator.php';
		SwiftFramework_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_SwiftFramework' );
	register_deactivation_hook( __FILE__, 'deactivate_SwiftFramework' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-swiftframework.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function init_swiftframework() {

		$swiftframework = new SwiftFramework();
		$swiftframework->run();

		require_once plugin_dir_path( __FILE__ ) . 'includes/wp-updates-plugin.php';
		new WPUpdatesPluginUpdater_977( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

	}
	init_swiftframework();
