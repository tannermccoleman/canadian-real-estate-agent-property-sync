<?php

define('WP_USE_THEMES', false);

require_once '../../../../../../wp-load.php';
require_once '../../../../../../wp-includes/class-phpmailer.php';


if (is_array($_POST) && !empty($_POST['post_id'])) {
    $agents = get_post_meta($_POST['post_id'], '_property_agents', TRUE);
    $author_email = get_the_author_meta('user_email');

    if (is_array($agents) || !empty($author_email)) {
        $message = '';

        if (!aviators_settings_get_value('properties', 'fields', 'hide_name')) {
            $message .= __('Name', 'aviators') . ': ' . $_POST['name'] . "\n\n";
        }

        if (!aviators_settings_get_value('properties', 'fields', 'hide_phone')) {
            $message .= __('Phone', 'aviators') . ': ' . $_POST['phone'] . "\n\n";
        }

        if (!aviators_settings_get_value('properties', 'fields', 'hide_date')) {
            $message .= __('Date', 'aviators') . ': ' . $_POST['date'] . "\n\n";
        }

        if (!aviators_settings_get_value('properties', 'fields', 'hide_email')) {
            $message .= __('E-mail', 'aviators') . ': ' . $_POST['email'] . "\n\n";
        }

        if (!aviators_settings_get_value('properties', 'fields', 'hide_message')) {
            $message .= __('Message', 'aviators') . ': ' . $_POST['message'] . "\n\n";
        }

        $message .= __('Location', 'aviators') . ': '. $_SERVER['HTTP_REFERER'];

        if (!empty($author_email)) {
            $headers = 'From: ' . aviators_settings_get_value('properties', 'enquire_form', 'name') . ' <' . aviators_settings_get_value('properties', 'enquire_form', 'email') . '>' . "\r\n";
            $is_sent = wp_mail($author_email, aviators_settings_get_value('properties', 'enquire_form', 'subject'), $message, $headers);
        }

        if (is_array($agents)) {
            foreach($agents as $agent_id) {
                $email = get_post_meta($agent_id, '_agent_email', TRUE);
                $headers = 'From: ' . aviators_settings_get_value('properties', 'enquire_form', 'name') . ' <' . aviators_settings_get_value('properties', 'enquire_form', 'email') . '>' . "\r\n";
                $is_sent = wp_mail($email, aviators_settings_get_value('properties', 'enquire_form', 'subject'), $message, $headers);
            }
        }


        if ($is_sent) {
            aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Your enquire was successfully sent.', 'aviators'));
        } else {
            aviators_flash_add_message(AVIATORS_FLASH_ERROR, __('An error occured. Your enquire can not be sent.', 'aviators'));
        }
    }

}

if (!empty($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . site_url());
}