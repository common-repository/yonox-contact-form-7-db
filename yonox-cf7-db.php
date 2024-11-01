<?php
/**
 * Plugin Name:       Yonox Contact Form 7 DB
 * Plugin URI:        https://wordpress.org/plugins/yonox-cf7-db
 * Description:       Saves contact form 7 submissions to your WordPress database.
 * Version:           1.0.0
 * Author:            Yonox
 * Author URI:        https://profiles.wordpress.org/yonox/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       yonox-cf7-db
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define currently plugin version.
define( 'YCF7DB_VERSION', '1.0.0' );

// Define plugin absolute path
define( 'YCF7DB_PLUGIN', __FILE__ );

// Define plugin basename
define( 'YCF7DB_PLUGIN_BASENAME', plugin_basename( YCF7DB_PLUGIN ) );

// Define the plugin folder url.
define( 'YCF7DB_PLUGIN_URL', plugin_dir_url( YCF7DB_PLUGIN ) );

// Define the plugin folder dir.
define ( 'YCF7DB_PLUGIN_DIR', plugin_dir_path( YCF7DB_PLUGIN ) );

// Define the plugin images url.
define( 'YCF7DB_IMAGES_URL', plugin_dir_url( YCF7DB_PLUGIN ) . 'assets/img/' );

// The code that runs during plugin activation.
function activate_yonox_cf7_db()
{
	require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-activator.php';
	Yonox_Cf7_Db_Activator::activate();
}

register_activation_hook( YCF7DB_PLUGIN, 'activate_yonox_cf7_db' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and front-end hooks.
 */
require YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yonox_cf7_db()
{
	$plugin = new Yonox_Cf7_Db();
	$plugin->run();
}
run_yonox_cf7_db();
