<?php

global $AVIATORS_DIR;
require_once $AVIATORS_DIR . '/plugins/properties/utils.php';
require_once $AVIATORS_DIR . '/plugins/properties/widgets.php';

/**
 * Meta options for custom post type
 */
$property_metabox = new Creasync_WPAlchemy_MetaBox(array(
    'id' => '_property_meta',
    'title' => __('Property Options', 'aviators'),
    'template' => $AVIATORS_DIR . '/plugins/properties/meta.php',
    'types' => array('property'),
    'prefix' => '_property_',
    'mode' => WPALCHEMY_MODE_EXTRACT,
        ));

/**
 * Register google map script for obtaining GPS locations from address
 */
function creasync_creasync_aviators_properties_load_styles() {
    if (is_admin()) {
        wp_register_script('gmap', 'http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=true');
        wp_enqueue_script('gmap');
    }
}

add_action('admin_head', 'creasync_creasync_aviators_properties_load_styles');

/**
 * Custom property link
 */
function creasync_creasync_aviators_properties_custom_tags() {
    add_rewrite_rule("^" . __('properties', 'aviators') . "/(?!page)([^/]+)/([^/]+)/?", 'index.php?post_type=property&location=$matches[1]&property=$matches[2]', 'top');
}

add_action('init', 'creasync_creasync_aviators_properties_custom_tags');

function creasync_aviators_properties_custom_post_link($post_link, $id = 0) {
    $post = get_post($id);

    if ($post->post_type == 'property') {
        $locations = get_the_terms($post, 'locations');

        if (is_array($locations) && count($locations) > 0) {
            $location = array_shift($locations);
            return home_url(user_trailingslashit(__('properties', 'aviators') . '/' . $location->slug . '/' . $post->post_name));
            //            return $location->slug . '.' . $post_link;
//            return str_replace('%location%', $location->slug, $post_link);
        }
    }
    return $post_link;
}

add_filter('post_type_link', 'creasync_aviators_properties_custom_post_link', 1, 3);

/**
 * Custom post type
 */
function creasync_aviators_properties_create_post_type() {
    global $AVIATORS_URL;
    $labels = array(
        'name' => __('Properties', 'aviators'),
        'singular_name' => __('Property', 'aviators'),
        'add_new' => __('Add New', 'aviators'),
        'add_new_item' => __('Add New Property', 'aviators'),
        'edit_item' => __('Edit Property', 'aviators'),
        'new_item' => __('New Property', 'aviators'),
        'all_items' => __('All Properties', 'aviators'),
        'view_item' => __('View Property', 'aviators'),
        'search_items' => __('Search Property', 'aviators'),
        'not_found' => __('No properties found', 'aviators'),
        'not_found_in_trash' => __('No properties found in Trash', 'aviators'),
        'parent_item_colon' => '',
        'menu_name' => __('Properties', 'aviators'),
    );

    register_post_type('property', array(
        'labels' => $labels,
        'supports' => array('title', 'editor','author', 'thumbnail'),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => __('properties', 'aviators')),
            'rewrite'       => array( 'slug' => '%location%' ),
        'menu_position' => 32,
        'categories' => array('property_types'),
        'menu_icon' => $AVIATORS_URL . '/plugins/properties/assets/img/properties.png',
            )
    );
}

add_action('init', 'creasync_aviators_properties_create_post_type');

/**
 * Custom taxonomies
 */
function creasync_aviators_properties_create_taxonomies() {
    $property_types_labels = array(
        'name' => __('Property Types', 'aviators'),
        'singular_name' => __('Property Type', 'aviators'),
        'search_items' => __('Search Property Types', 'aviators'),
        'all_items' => __('All Property Types', 'aviators'),
        'parent_item' => __('Parent Property Type', 'aviators'),
        'parent_item_colon' => __('Parent Property Type:', 'aviators'),
        'edit_item' => __('Edit Property Type', 'aviators'),
        'update_item' => __('Update Property Type', 'aviators'),
        'add_new_item' => __('Add New Property Type', 'aviators'),
        'new_item_name' => __('New Property Type', 'aviators'),
        'menu_name' => __('Property Type', 'aviators'),
    );

    register_taxonomy('property_types', 'property', array(
        'labels' => $property_types_labels,
        'hierarchical' => true,
        'query_var' => 'property_type',
        'rewrite' => array('slug' => __('property-type', 'aviators')),
        'public' => true,
        'show_ui' => true,
    ));

    $property_locations_labels = array(
        'name' => __('Locations', 'aviators'),
        'singular_name' => __('Location', 'aviators'),
        'search_items' => __('Search Location', 'aviators'),
        'all_items' => __('All Locations', 'aviators'),
        'parent_item' => __('Parent Location', 'aviators'),
        'parent_item_colon' => __('Parent Location:', 'aviators'),
        'edit_item' => __('Edit Location', 'aviators'),
        'update_item' => __('Update Location', 'aviators'),
        'add_new_item' => __('Add New Location', 'aviators'),
        'new_item_name' => __('New Location', 'aviators'),
        'menu_name' => __('Location', 'aviators'),
    );
    register_taxonomy('locations', 'property', array(
        'labels' => $property_locations_labels,
        'hierarchical' => true,
        'query_var' => 'location',
        'rewrite' => array('slug' => __('location', 'aviators')),
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
    ));

    $amenities_labels = array(
        'name' => __('Amenities', 'aviators'),
        'singular_name' => __('Amenity', 'aviators'),
        'search_items' => __('Search Amenity', 'aviators'),
        'all_items' => __('All Amenities', 'aviators'),
        'parent_item' => __('Parent Amenity', 'aviators'),
        'parent_item_colon' => __('Parent Amenity:', 'aviators'),
        'edit_item' => __('Edit Amenity', 'aviators'),
        'update_item' => __('Update Amenity', 'aviators'),
        'add_new_item' => __('Add New Amenity', 'aviators'),
        'new_item_name' => __('New Amenity', 'aviators'),
        'menu_name' => __('Amenity', 'aviators'),
    );

    register_taxonomy('amenities', 'property', array(
        'labels' => $amenities_labels,
        'hierarchical' => true,
        'query_var' => 'amenity',
        'rewrite' => array('slug' => __('amenity', 'aviators')),
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
    ));
}

add_action('init', 'creasync_aviators_properties_create_taxonomies', 0);

/**
 * Custom columns
 */
function creasync_aviators_properties_custom_post_columns() {
    return array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title', 'aviators'),
        'thumbnail' => __('Thumbnail', 'aviators'),
        'price' => __('Price', 'aviators'),
        'location' => __('Location', 'aviators'),
        'property_types' => __('Property Type', 'aviators'),
        'gps' => __('GPS', 'aviators'),
        'contract_type' => __('Contract Type', 'aviators'),
        'featured' => __('Featured', 'aviators'),
        'reduced' => __('Reduced', 'aviators'),
        'agents' => __('Agents', 'aviators'),
    );
}

add_filter('manage_edit-property_columns', 'creasync_aviators_properties_custom_post_columns');

function creasync_aviators_properties_custom_post_manage($column, $post_id) {
    global $post, $AVIATORS_URL;

    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, 'admin-thumb');
            } else {
                echo '<img width="100" height="100" style="width:100px !important;height:auto;" src="' . $AVIATORS_URL . '/plugins/properties/assets/img/property-tmp-small.png' . '" >';
            }
            break;
        case 'price':
            $price = get_post_meta($post_id, '_property_price', TRUE);
            if (empty($price)) {
                echo '<span style="color: red">' . __('Undefined', 'aviators') . '</span>';
            } else {
                echo $price;
            }
            break;
        case 'location':
            if (!is_array(get_the_terms($post, 'locations'))) {
                echo '<span style="color: red">' . __('Undefined', 'aviators') . '</span>';
            } else {
                $location = array_shift(get_the_terms($post, 'locations'));
                echo '<a href="?post_type=property&location=' . $location->slug . '">' . $location->name . '</a>';
            }
            break;
        case 'property_types':
            if (!is_array(get_the_terms($post, 'locations'))) {
                echo '<span style="color: red">' . __('Undefined', 'aviators') . '</span>';
            } else {
                $property_count = count(get_the_terms($post, 'property_types'));
                $property_type = array_shift(get_the_terms($post, 'property_types'));
                echo '<a href="?post_type=property&property_type=' . $property_type->slug . '">' . $property_type->name . '</a>';
            }
            break;
        case 'featured':
            $featured = get_post_meta($post_id, '_property_featured');
            if ($featured) {
                echo '<span style="color:green;">' . __('On', 'aviators') . '</span>';
            } else {
                echo '<span style="color:red;">' . __('Off', 'aviators') . '</span>';
            }
            break;
        case 'gps':
            $longitude = get_post_meta($post_id, '_property_longitude', TRUE);
            $latitude = get_post_meta($post_id, '_property_latitude', TRUE);
            if (!$longitude || !$latitude) {
                echo '<span style="color: red">' . __('Missing', 'aviators') . '</span>';
            } else {
                echo '[' . $latitude . ', ' . $longitude . ']';
            }
            break;
        case 'reduced':
            $reduced = get_post_meta($post_id, '_property_reduced');
            if ($reduced) {
                echo '<span style="color:green;">' . __('On', 'aviators') . '</span>';
            } else {
                echo '<span style="color:red;">' . __('Off', 'aviators') . '</span>';
            }
            break;
        case 'contract_type':
            $contract_type = get_post_meta($post_id, '_property_contract_type', TRUE);
            if ($contract_type == 'rent') {
                echo __('For rent', 'aviators');
            } elseif ($contract_type == 'sale') {
                echo __('For sale', 'aviators');
            } else {
                echo '<span style="color:red;">' . __('Not assigned', 'aviators') . '</span>';
            }
            break;
        case 'agents':
            $agent_id = get_post_meta($post_id, '_property_agents', TRUE);
//            if (!is_array($agents)) {
//                echo '<span style="color:red;">' . __('Not assigned', 'aviators') . '</span>';
//            } else {
//                foreach ($agents as $agent_id) {
                    echo get_post($agent_id)->post_title . '<br>';
//                }
//            }
            break;
    }
}

add_action('manage_property_posts_custom_column', 'creasync_aviators_properties_custom_post_manage', 10, 2);

/**
 * Change posts per page
 */
function creasync_aviators_modify_posts_per_properties_page() {
    add_filter('option_posts_per_page', 'creasync_aviators_modify_posts_per_properties_page');
}

add_action('init', 'creasync_aviators_modify_posts_per_properties_page2', 0);

function creasync_aviators_modify_posts_per_properties_page2($value) {
    if (is_post_type_archive('property') || is_tax('locations') || is_tax('amenities') || is_tax('property_types')) {
        return aviators_settings_get_value('properties', 'properties', 'per_page');
    }

    return $value;
}

function creasync_aviators_property_form() {
    global $current_user;
    $payment_gateway = aviators_settings_get_value('submission', 'common', 'payment_gateway');

    if (!is_user_logged_in()) {
        aviators_flash_add_message(AVIATORS_FLASH_ERROR, __('You need to login to access this page.', 'aviators'));
        wp_redirect(home_url());
        return;
    }

    if (!empty($_GET['id'])) {
        $post = get_post($_GET['id']);

        if ($post->post_author != $current_user->ID) {
            aviators_flash_add_message(AVIATORS_FLASH_ERROR, __('You are not post owner.', 'aviators'));
            wp_redirect(home_url());
            return;
        }
    }

    $metabox = new Submission_MetaBox(array(
        'id' => '_property_meta',
        'title' => __('Property Options', 'aviators'),
        'template' => $AVIATORS_DIR . '/plugins/properties/meta-submission.php',
        'types' => array('property'),
        'prefix' => '_property_',
        'mode' => WPALCHEMY_MODE_EXTRACT,
    ));

    // Edit action
    if (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_POST['post_title'])) {
        $post = get_post($_GET['id']);
        $post->post_title = $_POST['post_title'];
        $post->post_content = $_POST['content'];

        wp_set_post_terms($post->ID, $_POST['property_types'], 'property_types');
        wp_set_post_terms($post->ID, $_POST['property_locations'], 'locations');

        if (!empty($_POST['tax_input']) && is_array($_POST['tax_input']) && is_array($_POST['tax_input']['amenities'])) {
            $tags = '';
            foreach ($_POST['tax_input']['amenities'] as $amenity) {
                $tags .= $amenity . ',';
            }
            wp_set_post_terms($post->ID, $tags, 'amenities');
        }

        wp_update_post($post);

        $metabox->force_save($_GET['id']);
//        $metabox->_save($_GET['id']);

        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-submission-index.php',
        ));
        $submission_page = $pages[0];

        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post has been successfully updated.', 'aviators'));

        if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            if (!empty($_FILES['featured_image'])) {
                aviators_flash_add_message(AVIATORS_FLASH_ERROR, __('Image can not be uploaded', 'aviators'));
                wp_redirect(get_permalink($submission_page->ID));
                return;
            }
        } else {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attach_id = media_handle_upload('featured_image', $post->ID);
            update_post_meta($post->ID, '_thumbnail_id', $attach_id);
        }

        wp_redirect(get_permalink($submission_page->ID));
        return;
    }

    // Add action
    elseif (!empty($_GET['action']) && $_GET['action'] == 'add' && !empty($_POST['post_title'])) {
        $post = array();
        $post['post_type'] = 'property';
        $post['post_title'] = $_POST['post_title'];
        $post['post_content'] = $_POST['content'];
        $post['post_status'] = 'publish';
        if ($payment_gateway == 'paypal') {
            $post['post_status'] = 'private';
        }
        $post_id = wp_insert_post($post);

        $post = get_post($post_id);
        wp_set_post_terms($post->ID, $_POST['property_types'], 'property_types');
        wp_set_post_terms($post->ID, $_POST['property_locations'], 'locations');

        if (!empty($_POST['tax_input']) && is_array($_POST['tax_input']) && is_array($_POST['tax_input']['amenities'])) {
            $tags = '';
            foreach ($_POST['tax_input']['amenities'] as $amenity) {
                $tags .= $amenity . ',';
            }
            wp_set_post_terms($post->ID, $tags, 'amenities');
        }
        wp_update_post($post);
        $metabox->force_save($post->ID);

        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-submission-index.php',
        ));
        $submission_page = $pages[0];

        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post has been successfully added.', 'aviators'));

        if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            if (!empty($_FILES['featured_image']['error'])) {
                aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Image can not be uploaded', 'aviators'));
                wp_redirect(get_permalink($submission_page->ID));
                return;
            }
        } else {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $attach_id = media_handle_upload('featured_image', $post->ID);
            update_post_meta($post->ID, '_thumbnail_id', $attach_id);
        }

        wp_redirect(get_permalink($post->ID));
        return;
    }

    // Delete action
    elseif (!empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id'])) {
        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-submission-index.php',
        ));

        wp_delete_post($_GET['id']);
        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post has been successfully deleted.', 'aviators'));
        $submission_page = $pages[0];
        wp_redirect(get_permalink($submission_page->ID));
        return;
    }

    // Delete thumbnail
    elseif (!empty($_GET['action']) && $_GET['action'] == 'delete-thumbnail' && !empty($_GET['id'])) {
        update_post_meta($post->ID, '_thumbnail_id', '');
        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post\'s thumbnail has been successfully removed.', 'aviators'));
        wp_redirect(get_permalink($post->ID));
        return;
    }

    // Unpublish
    elseif (!empty($_GET['action']) && $_GET['action'] == 'unpublish' && !empty($_GET['id']) && $payment_gateway != 'paypal') {
        $post = get_post($_GET['id']);
        $post->post_status = 'private';
        wp_update_post($post);

        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-submission-index.php',
        ));
        $submission_page = $pages[0];
        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post has been successfully unpublished.', 'aviators'));
        wp_redirect(get_permalink($submission_page->ID));
        return;
    }

    // Publish
    elseif (!empty($_GET['action']) && $_GET['action'] == 'publish' && !empty($_GET['id']) && $payment_gateway != 'paypal') {
        $post = get_post($_GET['id']);
        $post->post_status = 'publish';
        wp_update_post($post);

        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-submission-index.php',
        ));
        $submission_page = $pages[0];
        aviators_flash_add_message(AVIATORS_FLASH_SUCCESS, __('Post has been successfully published.', 'aviators'));
        wp_redirect(get_permalink($submission_page->ID));
        return;
    }
    return creasync_aviators_properties_form_generate($metabox);
}

function creasync_aviators_properties_form_generate($metabox) {
    global $post;

    // Check if we are editing already existing post or adding new one
    if (isset($_GET['id'])) {
        $post = get_post($_GET['id']);
    } else {
        $post = new stdClass();
        $post->ID = 0;
    }

    // Include file rendering checboxes and combo boxes
    if (!function_exists('wp_terms_checklist')) {
        require_once ABSPATH . 'wp-admin/includes/template.php';
    }

    // Property types
    if (!empty($post->ID)) {
        $property_types_terms = wp_get_post_terms($post->ID, 'property_types');
        $property_types_selected_terms = $property_types_terms[0]->term_id;
    } else {
        $property_types_selected_terms = '';
    }
    $property_types = wp_dropdown_categories(array(
        'id' => 'property_types',
        'name' => 'property_types',
        'taxonomy' => 'property_types',
        'echo' => 0,
        'hide_empty' => 0,
        'selected' => $property_types_selected_terms,
    ));

    // Property locations
    if (!empty($post->ID)) {
        $property_locations_terms = wp_get_post_terms($post->ID, 'locations');
        $property_locations_selected_terms = $property_locations_terms[0]->term_id;
    } else {
        $property_locations_selected_terms = '';
    }

    $property_locations = wp_dropdown_categories(array(
        'id' => 'property_locations',
        'name' => 'property_locations',
        'taxonomy' => 'locations',
        'echo' => 0,
        'hide_empty' => 0,
        'selected' => $property_locations_selected_terms,
    ));

    ob_start();

    aviators_terms_checklist($post->ID, array(
        'taxonomy' => 'amenities',
    ));
    $amenities = ob_get_clean();

    return array(
        'content' => View::render('properties/form-content.twig', array(
            'post' => $post,
            'amenities' => $amenities,
            'property_types' => $property_types,
            'property_locations' => $property_locations,
        )),
        'metabox' => $metabox,
    );
}
