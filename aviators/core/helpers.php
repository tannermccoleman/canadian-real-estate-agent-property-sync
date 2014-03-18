<?php

/**
 * Gets home URL
 */
function creasync_aviators_get_home_url() {
    global $sitepress;

    $home = get_bloginfo('wpurl');

    if (defined('ICL_LANGUAGE_CODE')) {
        if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE) {
            return $home;
        }
        return $home . '/' . ICL_LANGUAGE_CODE;
    }

    return $home;
}
/**
 * Loads all plugins from plugins.json
 */
function creasync_aviators_core_load_plugins() {
     global $AVIATORS_DIR;
    $content = file_get_contents($AVIATORS_DIR . '/plugins.json');
    $options = json_decode($content);

    foreach($options->plugins as $plugin) {
        creasync_aviators_core_require_plugin($plugin);
    }
}

/**
 * Get list of all available plugins
 */
function creasync_aviators_core_plugins_list() {
    global $AVIATORS_DIR;
    $content = file_get_contents($AVIATORS_DIR . '/plugins.json');
    $options = json_decode($content);
    $plugins = array();

    foreach($options->plugins as $plugin) {
        $plugins[$plugin] = array(
            'path' => $AVIATORS_DIR . '/plugins/' . $plugin,
        );
    }

    return $plugins;
}

/**
 * Get list of all required core plugins
 */
function creasync_aviators_core_required_plugins_list() {
    global $AVIATORS_DIR;
    $plugins = array();
    $dirs = glob($AVIATORS_DIR . '/core/plugins/*', GLOB_ONLYDIR);

    foreach($dirs as $dir) {
        $parts = explode('/', $dir);
        $name = $parts[count($parts)-1];
        $plugins[$name] = array(
            'path' => $AVIATORS_DIR . '/core/plugins/' . $name,
        );
    }

    return $plugins;
}

function creasync_aviators_core_get_core_plugins_list() {
    global $AVIATORS_DIR;
    return glob($AVIATORS_DIR . '/core/plugins/*', GLOB_ONLYDIR);
}

function creasync_aviator_core_get_all_plugins_list() {
    $optional_plugins = creasync_aviators_core_plugins_list();
    $required_plugins = creasync_aviators_core_required_plugins_list();
    return array_merge($optional_plugins, $required_plugins);
}

/**
 * Better plugin loader
 */
function creasync_aviators_core_require_plugin($plugin_name) {
    global $AVIATORS_DIR;
    require_once $AVIATORS_DIR . '/plugins/' . $plugin_name . '/'. $plugin_name . '.php';
}


function creasync_aviators_core_get_post_teaser($id) {
	$post = get_post($id);	

	if (preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
		$parts = explode($matches[0], $post->post_content);
		return $parts[0];
	}

    return FALSE;
}

/**
 * Print full list of queries
 */
function creasync_aviators_core_debug_queries() {
    global $wpdb;

    echo "<pre>"; print_r($wpdb->queries); echo "</pre>";
    die;	
}