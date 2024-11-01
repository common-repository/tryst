<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://matteus.dev
 * @since             2.0.0
 * @package           Tryst
 *
 * @wordpress-plugin
 * Plugin Name:       Tryst
 * Plugin URI:        https://matteus.dev/tryst
 * Description:       Meetings
 * Version:           1.0
 * Author:            Matteus Barbosa
 * Author URI:        https://matteus.dev
 * License: 		  BSD-3-Clause
 * License URI:       https://opensource.org/licenses/BSD-3-Clause
 * Text Domain:       tryst
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('TRYST_LIST_COUNTRY_SUPPORT', ['en-US', 'pt-BR']);

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TRYST_VERSION', '1.0' );


//loads composer and dependencies
require __DIR__ . '/vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tryst-activator.php
 */
function activate_tryst() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tryst-activator.php';
	Tryst_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tryst-deactivator.php
 */
function deactivate_tryst() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tryst-deactivator.php';
	Tryst_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tryst' );
register_deactivation_hook( __FILE__, 'deactivate_tryst' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tryst.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tryst() {
	global $tryst_plugin;
	$tryst_plugin = new Tryst();
	$tryst_plugin->run();

}
run_tryst();