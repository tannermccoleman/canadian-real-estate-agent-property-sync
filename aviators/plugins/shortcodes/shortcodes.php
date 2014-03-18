<?php
global $AVIATORS_DIR;

require_once $AVIATORS_DIR . '/plugins/shortcodes/helpers/columns.php';
require_once $AVIATORS_DIR . '/plugins/shortcodes/helpers/boxes.php';
require_once $AVIATORS_DIR . '/plugins/shortcodes/helpers/faq.php';
require_once $AVIATORS_DIR . '/plugins/shortcodes/helpers/pricing.php';


function creasync_aviators_buttons() {
    add_filter('mce_external_plugins', 'creasync_aviators_add_buttons');
    add_filter('mce_buttons_3', 'creasync_aviators_register_buttons');
}
add_action('init', 'creasync_aviators_buttons');


function creasync_aviators_add_buttons($plugin_array) {
    global $AVIATORS_DIR;
    $plugin_array['aviators'] = $AVIATORS_DIR . '/plugins/shortcodes/shortcodes.js';
    return $plugin_array;
}


function creasync_aviators_register_buttons($buttons) {
    array_push($buttons, 'row');
    array_push($buttons, 'span3');
    array_push($buttons, 'span4');
    array_push($buttons, 'span6');
    array_push($buttons, 'span8');
    array_push($buttons, 'content_box');
    array_push($buttons, 'faq');
    array_push($buttons, 'pricing');

    return $buttons;
}