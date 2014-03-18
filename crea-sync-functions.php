<?php

function property_contain($substring, $string) {
    $pos = strpos($string, $substring);

    if ($pos === false) {
        return 'false';
    } else {
        return 'true';
    }
}

function getCreasyncPropertyLatLong($dlocation) {
// Get lat and long by address      
    $address = $dlocation; // Google HQ
    $prepAddr = str_replace(' ', '+', $address);
    $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
    $output = json_decode($geocode);
    $latitude = $output->results[0]->geometry->location->lat;
    $longitude = $output->results[0]->geometry->location->lng;
    $gpsArray = array($latitude, $longitude);
    return $gpsArray;
}

function getCreasyncPropertyLocationID($term, $property_main_id) {
    global $wpdb, $creasync_plugin_dir;
    $creasync_terms = $wpdb->prefix . 'terms';
    $creasync_term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $creasync_term_rel_table = $wpdb->prefix . 'term_relationships';
    $select_pre_id = "select $creasync_terms.term_id as term_id,count from $creasync_terms,$creasync_term_taxonomy where $creasync_terms.term_id=$creasync_term_taxonomy.term_id and  taxonomy = 'locations' and name='" . $term . "'";
    $term_res = $wpdb->get_results($select_pre_id, ARRAY_A);
    if (empty($term_res)) {
        $wpdb->query("INSERT INTO `$creasync_terms`(`name`, `slug`, `term_group`) VALUES ('" . $term . "','" . sanitize_title_with_dashes(strtolower($term)) . "','0')");
        $property_term_id = $wpdb->insert_id;
        $wpdb->query(" INSERT INTO `$creasync_term_taxonomy`(`term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES ('" . $property_term_id . "','locations','','0','1')");
        $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $property_term_id . "'");
        return $property_term_id;
    } else {
        $term_id = $term_res[0]['term_id'];
        $term_count = $term_res[0]['count'];
        $term_count++;
//        $insert_type = $wpdb->query(" UPDATE `$creasync_term_taxonomy` SET `count`='$term_count' WHERE `term_id`='$term_id' and `taxonomy`='locations'");
        $insert_type = $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $term_id . "'");
        return $term_id;
    }
}

function getCreasyncPropertyTypeID($type, $property_main_id) {
    global $wpdb, $creasync_plugin_dir;
    $creasync_terms = $wpdb->prefix . 'terms';
    $creasync_term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $creasync_term_rel_table = $wpdb->prefix . 'term_relationships';
    $select_pre_id = "select $creasync_terms.term_id as term_id,count from $creasync_terms,$creasync_term_taxonomy where $creasync_terms.term_id=$creasync_term_taxonomy.term_id and  taxonomy = 'property_types' and name='" . $type . "'";
    $type_res = $wpdb->get_results($select_pre_id, ARRAY_A);
    if (empty($type_res)) {
        $wpdb->query("INSERT INTO `$creasync_terms`(`name`, `slug`, `term_group`) VALUES ('" . $type . "','" . sanitize_title_with_dashes(strtolower($type)) . "','0')");
        $property_type_id = $wpdb->insert_id;
        $wpdb->query(" INSERT INTO `$creasync_term_taxonomy`(`term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES ('" . $property_type_id . "','property_types','','0','1')");
        $insert_type = $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $property_type_id . "'");
        return $property_type_id;
    } else {
        $type_id = $type_res[0]['term_id'];
        $type_count = $type_res[0]['count'];
        $type_count++;
//        $insert_type = $wpdb->query(" UPDATE `$creasync_term_taxonomy` SET `count`='$type_count' WHERE `term_id`='$type_id' and `taxonomy`='property_types'");
        $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $type_id . "'");
        return $type_id;
    }
}

function getCreasyncPropertyAgentID($agent_name, $property_main_id) {
    global $wpdb, $creasync_plugin_dir;
    $creasync_posts = $wpdb->prefix . 'posts';
    $creasync_terms = $wpdb->prefix . 'terms';
    $creasync_term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $creasync_term_rel_table = $wpdb->prefix . 'term_relationships';
    $select_pre_id = "select ID from $creasync_posts where post_status='publish' and post_type = 'agent' and post_title='" . $agent_name . "'";
    $post_res = $wpdb->get_results($select_pre_id, ARRAY_A);
    if (empty($post_res)) {
        $wpdb->query("INSERT INTO `$creasync_posts`(`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES ('1',now(),now(),'','$agent_name','','publish','closed','closed','','$agent_name','','',now(),now(),'','0','url','0','agent','','0')");
        $property_agent_id = $wpdb->insert_id;
//        $wpdb->query(" INSERT INTO `$creasync_term_taxonomy`(`term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES ('" . $property_agent_id . "','agents','','0','1')");
//        $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $property_agent_id . "'");
        return $property_agent_id;
    } else {
        $property_agent_id = $post_res[0]['ID'];
        return $property_agent_id;
    }
}

?>