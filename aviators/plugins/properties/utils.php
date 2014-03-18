<?php
/**
 * Get featured properties
 *
 * @param int
 * @return array()
 */
function creasync_aviators_properties_get_featured($count = 3, $shuffle = FALSE) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $count,
        'meta_query' => array(
            array(
                'key' => '_property_featured',
                'value' => '1',
                'compare' => '=',
            ),
        ),
    );

    if ($shuffle) {
        $args['orderby'] = 'rand';
    }

    $query = new WP_Query($args);
    return _creasync_aviators_properties_prepare($query);
}


/**
 * Get most recent properties
 *
 * @return array()
 */
function creasync_aviators_properties_get_most_recent($count = 3, $shuffle = FALSE) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $count,
    );

    if ($shuffle) {
        $args['orderby'] = 'rand';
    }

    $query = new WP_Query($args);
    return _creasync_aviators_properties_prepare($query);
}


/**
 * Get reduced properties
 *
 * @return array()
 */
function creasync_aviators_properties_get_reduced($count = 3, $shuffle = FALSE) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $count,
        'meta_query' => array(
            array(
                'key' => '_property_reduced',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    if ($shuffle) {
        $args['orderby'] = 'rand';
    }

    $query = new WP_Query($args);
    return _creasync_aviators_properties_prepare($query);
}

/**
 * Get all agent's properties
 */
function creasync_aviators_properties_get_by_agent($id, $count = -1) {    
    $query = new WP_Query(array(
        'post_type' => 'property',
        'posts_per_page' => $count,
        'meta_query' => array(
            array(
                'key' => '_property_agents',
                'value' => '"' + $id + '"',
                'compare' => 'LIKE',                
            )
        )
    ));

    return _creasync_aviators_properties_prepare($query);
}

/**
 * Prepare meta information for properties
 *
 * @return array()
 */
function _creasync_aviators_properties_prepare(WP_Query $query) {
    $results = array();

    foreach($query->posts as $property) {
        $property->meta = get_post_meta($property->ID, '', true);
        $property->location = wp_get_post_terms($property->ID, 'locations');
        $property->property_types = wp_get_post_terms($property->ID, 'property_types');
        $property->slides = get_post_meta($property->ID, '_property_slides', TRUE);
        $property->slider_image = get_post_meta($property->ID, '_property_slider_image', TRUE);
        $results[] = $property;
    }
    return $results;
}

function _creasync_aviators_properties_prepare_single($post) {
    $post->meta = get_post_meta($post->ID, '', true);
    $post->location = wp_get_post_terms($post->ID, 'locations');
    $post->property_types = wp_get_post_terms($post->ID, 'property_types');
    $post->slides = get_post_meta($post->ID, '_property_slides', TRUE);
    $post->slider_image = get_post_meta($post->ID, '_property_slider_image', TRUE);

    return $post;
}