<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://maheshthorat.web.app
 * @since             0.1
 * @package           PagePulse
 *
 * Plugin Name: PagePulse
 * Plugin URI: https://wordpress.org/plugins/PagePulse/
 * Description: PagePulse adds dynamic loading animations to your WordPress website, keeping visitors engaged during page transitions. Choose from a variety of sleek animations to enhance user experience.
 * Version: 0.1
 * License:     GPL v3
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 * Author: Mahesh Thorat
 * Text Domain: pagepulse
 * Author URI: https://maheshthorat.web.app
 * Playground: true
 **/

/**
 * Prevent file to be called directly
 */
if ((!defined('ABSPATH'))) {
   die;
}

/**
 * Define Constants
 */
define('PagePulse_PLUGIN_FULLNAME', 'PagePulse');
define('PagePulse_PLUGIN_IDENTIFIER', 'PagePulse');
define('PagePulse_PLUGIN_VERSION', '0.1');
define('PagePulse_PLUGIN_LAST_RELEASE', '2023/08/15');
define('PagePulse_PLUGIN_LANGUAGES', 'English');
define('PagePulse_PLUGIN_ABS_PATH', plugin_dir_path(__FILE__));
define('PagePulse_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PagePulse_DEFAULT_COLOR', "#CCCCCC");

/**
 * The core plugin class that is used to define internationalization
 * admin-specific hooks and public-facing site hooks
 */
require PagePulse_PLUGIN_ABS_PATH . 'includes/class-PagePulse-core.php';


/**
 * Begins secution of the plugin
 */
if (!function_exists('run_PagePulse')) {
   function run_PagePulse()
   {
      $plugin = new PagePulse_Core();
      $plugin->run();
   }
   run_PagePulse();
}
