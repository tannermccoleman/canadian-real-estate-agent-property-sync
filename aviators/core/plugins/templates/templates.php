<?php

require_once dirname(__FILE__) . '/helpers.php';
require_once dirname(__FILE__) . '/extensions.php';


class Template {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance !== null) {
            
            return self::$instance;
        }
//$AVIATORS_DIR= WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
        CreasyncAutoloader::register();
        $templates = array();
        $templates[] = $AVIATORS_DIR . '/templates';
        $templates = array_merge($templates, aviators_templates_prepare_plugins_template_dirs());

        $loader = new CreasyncLoader_Filesystem($templates);

        $instance = new CreasyncEnvironment($loader, array(
            'cache' => $AVIATORS_DIR. '/templates/cache',
            'debug' => aviators_settings_get_value('templates', 'cache', 'debug'),
        ));

        $instance->addGlobal('wp', new TwigProxy());
        $instance->addGlobal('q', $_GET);
        $instance->addGlobal('p', $_POST);

        $instance->addFunction(new CreasyncSimpleFunction('wp_footer', 'aviators_templates_helpers_wp_footer'));
        $instance->addFunction(new CreasyncSimpleFunction('wp_head', 'aviators_templates_helpers_wp_head'));
        $instance->addFunction(new CreasyncSimpleFunction('comment_form', 'aviators_templates_helpers_comment_form'));
        $instance->addFunction(new CreasyncSimpleFunction('body_class', 'aviators_templates_body_class'));
        $instance->addFunction(new CreasyncSimpleFunction('wp_list_comments', 'aviators_templates_helpers_wp_list_comments'));
        $instance->addFunction(new CreasyncSimpleFunction('post_class', 'aviators_templates_helpers_post_class'));
        $instance->addFunction(new CreasyncSimpleFunction('dynamic_sidebar', 'aviators_templates_helpers_dynamic_sidebar'));
        $instance->addFunction(new CreasyncSimpleFunction('comments_template', 'aviators_templates_helpers_comments_template'));
        $instance->addFunction(new CreasyncSimpleFunction('paginate_comments_links', 'aviators_templates_helpers_paginate_comments_links'));
        $instance->addFunction(new CreasyncSimpleFunction('next_comments_link', 'aviators_templates_helpers_next_comments_link'));
        $instance->addFunction(new CreasyncSimpleFunction('previous_comments_link', 'aviators_templates_helpers_previous_comments_link'));
        $instance->addFunction(new CreasyncSimpleFunction('posts_nav_link', 'aviators_templates_helpers_posts_nav_link'));
        $instance->addFunction(new CreasyncSimpleFunction('paginate_links', 'aviators_templates_helpers_paginate_links'));
        $instance->addFunction(new CreasyncSimpleFunction('next_posts_link', 'aviators_templates_helpers_next_posts_link'));
        $instance->addFunction(new CreasyncSimpleFunction('previous_posts_link', 'aviators_templates_helpers_previous_posts_link'));

        $instance->addExtension(new HTMLDecodeTwigExtension());

        return $instance;
    }
}

function aviators_templates_init() {
    $clear = aviators_settings_get_value('templates', 'cache', 'clear');
    $debug = aviators_settings_get_value('templates', 'cache', 'debug');
    if ($clear == 'on') {
        $instance = Template::getInstance();
        $instance->clearCacheFiles();
        update_option('templates_cache_clear', FALSE);
    }

}
add_action('init', 'aviators_templates_init');