<?php

/*
  Plugin Name: CREA Property synchronizer
  Plugin URI: http://www.w3bdesign.ca/
  Description: CREA Property synchronizer, sync realestate properties from CREA realator.ca to our own website. It automatically keeptrack propertes updates as well as sales, sold listing automatically remove from our website property listings.
  Author: Purple Turtle Productions
  Version: 1.0.2
  Author URI: http://www.w3bdesign.ca/
 */

global $creasync_version, $creasync_plugin_dir, $creasync_plugin_url, $AVIATORS_DIR;
define('PURPLE_XMLS_PATH', dirname(__FILE__));      // /.../wp-content/plugins/crea-sync
define('PURPLE_XMLS_NAME', basename(dirname(__FILE__)));   // crea-sync
define('PURPLE_XMLS_URL', plugins_url() . '/' . PURPLE_XMLS_NAME);
$creasync_plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
$creasync_plugin_url = plugins_url() . "/crea-sync/";
$AVIATORS_DIR = $creasync_plugin_dir . '/aviators';
$AVIATORS_URL = $creasync_plugin_url . '/aviators';


//action hook for plugin activation
register_activation_hook(__FILE__, 'creasync_activate_plugin');

//action hook for plugin admin menu
add_action('admin_menu', 'creasync_admin_menu');

add_filter('query_vars', 'creasync_query_vars');

//require_once(dirname(__FILE__) . "/crea-sync-required-plugins.php");
require_once(dirname(__FILE__) . "/class/crea-sync-api.php");
require_once(dirname(__FILE__) . "/crea-sync-shortcodes.php");
include_once(dirname(__FILE__) . '/crea-sync-settings.php');
require_once $AVIATORS_DIR . '/core/core.php';

creasync_aviators_core_load_plugins();

require_once $AVIATORS_DIR . '/customization.php';

add_filter('cron_schedules', 'add_per_min');



if (!wp_next_scheduled('crea_property_syncher')) {
    wp_schedule_event(time(), 'perminute', 'crea_property_syncher');
}

add_action('crea_property_syncher', 'crea_property_syncher_sync_all');

register_deactivation_hook(__FILE__, creasync_deactivate);
