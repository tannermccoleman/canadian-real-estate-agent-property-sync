<?php

function creasync_activate_plugin() {

    if (get_option('creasync_api_username') == "") {
        add_option('creasync_api_username', "CXLHfDVrziCfvwgCuL8nUahC");
    }
    if (get_option('creasync_api_password') == "") {
        add_option('creasync_api_password', "mFqMsCSPdnb5WO1gpEEtDCHH");
    }
    if (get_option('creasync_environment_url') == "") {
        add_option('creasync_environment_url', "http://sample.data.crea.ca/Login.svc/Login");
    }
    if (get_option('creasync_sync_delay') == "") {
        add_option('creasync_sync_delay', "1500");
    }
    if (get_option('creasync_licensekey') == "") {
        add_option('creasync_licensekey', "creasync");
    }
    if (get_option('creasync_localkey') == "") {
        add_option('creasync_localkey', "creasync");
    }
    if (get_option('creasync_status') == "") {
        add_option('creasync_status', "Enable");
    }
}

function creasync_admin_menu() {
    add_options_page('CREA Property synchronizer Plugin Options', 'CREA Property synchronizer', 'manage_options', 'creasync', 'creasync_account_options');
    add_action('admin_init', 'creasync_api_settings');
}

function creasync_deactivate() {
//    delete_option('creasync_api_username');
//    delete_option('creasync_api_password');
//    delete_option('creasync_environment_url');
//    delete_option('creasync_sync_delay');
//    delete_option('creasync_licensekey');
//    delete_option('creasync_localkey');
}

function creasync_query_vars($vars) {
    array_push($vars, 'listingid');
    array_push($vars, 'listingkey');
    return $vars;
}

function creasync_api_settings() {
    global $creasync_version, $creasync_plugin_dir, $creasync_plugin_url;
    define('PURPLE_XMLS_PATH', dirname(__FILE__));      // /.../wp-content/plugins/crea-sync
    define('PURPLE_XMLS_NAME', basename(dirname(__FILE__)));   // crea-sync
    define('PURPLE_XMLS_URL', plugins_url() . '/' . PURPLE_XMLS_NAME);
    $creasync_plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
    $creasync_plugin_url = plugins_url() . "/crea-sync/";
    $creasync_version = "1.0.2";
  }
function creasync_account_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }


    require_once(dirname(__FILE__) . "/templates/crea-sync-api-settings.php");
}

function creasync_account_settings_update_options() {
    if (isset($_POST["creasync_api_username"])) {
        update_option("creasync_api_username", $_POST["creasync_api_username"]);
        update_option("creasync_api_password", $_POST["creasync_api_password"]);
        update_option("creasync_environment_url", $_POST["creasync_environment_url"]);
        echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Property synchronizer settings updated successfully!</strong></p></div>';
        $adapter = new creasync_api();
        if ($adapter->connect()) {
            return $adapter->connectionTest();
        }
    }
    if (isset($_POST["creasync_status"])) {
        update_option("creasync_status", $_POST["creasync_status"]);
        echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Property synching status updated successfully!</strong></p></div>';
    }
    if (isset($_POST["sync_now"])) {
        crea_property_syncher_sync_all();
    }
}

function add_per_min() {
    $current_delay = get_option('creasync_sync_delay');
    return array(
        'perminute' => array('interval' => $current_delay, 'display' => 'Every Minute'),
    );
}

function crea_property_syncher_sync_all() {
    $connection_status = true;
    if (get_option('creasync_status') == "Enable") {
        global $wpdb, $creasync_plugin_dir;

        $creasync_postmeta_table = $wpdb->prefix . 'postmeta';
        $creasync_posts_table = $wpdb->prefix . 'posts';
        $creasync_term_rel_table = $wpdb->prefix . 'term_relationships';
        $select_pre_id = "select count(*) as count from $creasync_postmeta_table where meta_key = '_property_key'";
        $num_pre = $wpdb->get_results($select_pre_id, ARRAY_A);
        $num_pre = $num_pre[0]['count'];
        if ($num_pre < 10) {
            require_once("libraries/phrets.php");
            include("$creasync_plugin_dir/crea-sync-functions.php");
            ini_set('max_execution_time', 6000);
            $rets_login_url = get_option('creasync_environment_url');
            $rets_username = get_option('creasync_api_username');
            $rets_password = get_option('creasync_api_password');
            $rets_user_agent = "Emergentsoft/1.0";
            $rets_user_agent_password = "Emergent123";
// start rets connection
            $rets = new phRETS;
//$rets->FirewallTest();
            $rets->AddHeader("RETS-Version", "RETS/1.7.2");
            $rets->AddHeader("User-Agent", $rets_user_agent);
            $connect = $rets->Connect($rets_login_url, $rets_username, $rets_password);
// check for errors
            if ($connect) {
                echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Syncher Connected successfully!</strong></p></div>';
            } else {
                $connection_status = FALSE;
                echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Property syncher not connected, try agin later!</strong></p></div>';
            }
            if ($connection_status) {
                $search = $rets->SearchQuery("Property", "Property", "(LastUpdated=2011-05-08T22:00:17Z)", array("Count" => 1, "Limit" => "10", "Offset" => "1"));
                $try = array();
                $loop = 0;
                $val = 'a:8:{i:0;s:15:"_property_price";i:1;s:19:"_property_bathrooms";i:2;s:18:"_property_bedrooms";i:3;s:14:"_property_area";i:4;s:18:"_property_latitude";i:5;s:19:"_property_longitude";i:6;s:19:"_property_slides";i:7;s:19:"_property_agents";}';
                while ($listing = $rets->FetchRow($search)) {
                    $loop++;
//                    echo "<pre>";
//echo(print_r($listing));
// die("</pre>");
                    $address = $listing['UnparsedAddress'] . ", " . $listing['Country'];
                    $gps = getCreasyncPropertyLatLong($address);
                    $ListingRid = $listing['ListingId'];
                    $ListingRimgid = $listing['ListingKey'];
                    $select_pre_id = "select count(*) as count from $creasync_postmeta_table where meta_key = '_property_key' and meta_value = '" . $ListingRid . "'";
                    $num_pre = $wpdb->get_results($select_pre_id, ARRAY_A);
                    $num_pre = $num_pre[0]['count'];
                    if ($num_pre == 0) {
                        $property_details = "<p>" . $listing['PublicRemarks'] . "</p>";
                        $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";

                        if ($listing['View'] != "") {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Property</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>View : </strong>" . $listing['View'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                        }
                        if ($listing['ParkingTotal'] != '') {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Parking</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            if ($listing['AttachedGarageYN'] != 'False') {
                                $attached = "Attached garage";
                            } else {
                                $attached = "N/A";
                            }
                            $property_details .= "<strong>Parking Type : </strong>" . $attached;
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>Space(s) : </strong>" . $listing['ParkingTotal'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                        }
                        if ($listing['PropertyType'] != '' || $listing['Flooring'] != '' || $listing['Sewer'] != '' || $listing['YearBuilt'] != '') {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Building</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            if ($listing['PropertyType'] != '') {
                                $property_details .= "<strong>Building Type : </strong>" . $listing['PropertyType'];
                                $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            }
                            if ($listing['Flooring'] != '') {
                                $property_details .= "<strong>Flooring Type : </strong>" . $listing['Flooring'];
                                $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            }
                            if ($listing['Sewer'] != '') {
                                $property_details .= "<strong>Sewer Type : </strong>" . $listing['Sewer'];
                                $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            }
                            if ($listing['YearBuilt'] != '') {
                                $property_details .= "<strong>Built In : </strong>" . $listing['YearBuilt'];
                                $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            }
                            $property_details .= "<strong>Building Area : </strong>" . $listing['BuildingAreaTotal'] . " " . $listing['BuildingAreaUnits'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                        }
                        if ($listing['RoomType1'] != '') {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Rooms</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<table style='width:50%'>";
                            $property_details .= "<tr>";
                            $property_details .= "<td style='width:50%'><strong>Type</strong></td>";
                            $property_details .= "<td style='width:50%'><strong>Level</strong></td>";
                            $property_details .= "<td style='width:50%'><strong>Dimensions</strong></td>";
                            $property_details .= "</tr>";
                            for ($room_loop = 1; $room_loop <= 20; $room_loop++) {
                                if ($listing['RoomType' . $room_loop] != '') {
                                    $property_details .= "<tr>";
                                    $property_details .= "<td>";
                                    $property_details .= $listing['RoomType' . $room_loop];
                                    $property_details .= "</td>";
                                    $property_details .= "<td>";
                                    $property_details .= $listing['RoomLevel' . $room_loop];
                                    $property_details .= "</td>";
                                    $property_details .= "<td>";
                                    $property_details .= $listing['RoomDimensions' . $room_loop];
                                    $property_details .= "</td>";
                                    $property_details .= "</tr>";
                                }
                            }
                            $property_details .= "</table>";
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                        }
                        if ($listing['FireplaceFeatures'] != '') {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Fireplace</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>Type : </strong>" . $listing['FireplaceFeatures'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>Fuel : </strong>" . $listing['FireplaceFuel'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                        }
                        if ($listing['Heating'] != '') {
                            $property_details .= "<div style='border:1px solid #000; float:left; padding:2px;'>Heating</div>";
                            $property_details .= "<div style='border-bottom: 1px solid; float: left; margin-top: 25px; width: 30%;'></div>";
                            $property_details .= "<p style='line-height:30px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>Heating : </strong>" . $listing['Heating'];
                            $property_details .= "<p style='line-height:10px; margin:0px;'>&nbsp;</p>";
                            $property_details .= "<strong>Heating Fuel : </strong>" . $listing['HeatingFuel'];
                        }

                        $post_name = str_replace(" ", "-", $listing['UnparsedAddress']);
                        $post_name = str_replace("-#", "", $post_name);
                        $post_name = str_replace("--", "-", $post_name);
                        $post_name = strtolower($post_name);

                        $insert_property = $wpdb->prepare("insert into $creasync_posts_table set post_author = %s, post_date = %s, post_date_gmt = %s, post_content = %s, post_title = %s, post_status = %s, comment_status = %s, ping_status  = %s, post_name = %s, post_type = %s", array('1', date("Y-m-d h:i:s"), date("Y-m-d h:i:s"), $property_details, $listing['UnparsedAddress'], 'publish', 'closed', 'closed', $post_name, 'property'));
                        $res_ins = $wpdb->query($insert_property);
                        $property_main_id = $wpdb->insert_id;

                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_id', '" . $ListingRimgid . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_key', '" . $ListingRid . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_title', '" . $listing['UnparsedAddress'] . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_price', '" . $listing['ListPrice'] . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_bathrooms', '" . $listing['BathroomsTotal'] . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_bedrooms', '" . $listing['BedroomsTotal'] . "')");
                        $insert_meta_id = $wpdb->query("INSERT INTO `$creasync_postmeta_table`(`post_id`, `meta_key`, `meta_value`) VALUES ('" . $property_main_id . "','_property_area', '" . $listing['BuildingAreaTotal'] . "')");


                        $get_agent_id = getCreasyncPropertyAgentID($listing['ListAgentFullName'], $property_main_id);
                        $agent_id_meta = $get_agent_id;

                        $insert_meta_agent = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $property_main_id . "', meta_key = '_property_agents', meta_value = '" . $agent_id_meta . "'");

                        $insert_meta_field = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $property_main_id . "', meta_key = '_property_meta_fields', meta_value = '" . $val . "'");
                        if ($gps[0] != '') {
                            $insert_meta_long = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $property_main_id . "', meta_key = '_property_longitude', meta_value = '" . $gps[1] . "'");
                            $insert_meta_long = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $property_main_id . "', meta_key = '_property_latitude', meta_value = '" . $gps[0] . "'");
                        }
                        if ($listing['City'] != '') {
                            $location_name = getCreasyncPropertyLocationID($listing['City'], $property_main_id);
//                            $insert_loc = $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $location_name . "'");
                        }
                        if ($listing['PropertyType'] != '') {
                            $get_type_id = getCreasyncPropertyTypeID($listing['PropertyType'], $property_main_id);
//                            $insert_type = $wpdb->query("insert into $creasync_term_rel_table set object_id = '" . $property_main_id . "', term_taxonomy_id = '" . $get_type_id . "'");
                        }
                        $upload_dir = wp_upload_dir();
                        $upload_url = $upload_dir['baseurl'];
                        $dir = $upload_dir['basedir'] . "/";
                        if (!is_dir($dir)) {
                            mkdir($dir);
                        }
                        $dir.= "property/";
                        if (!is_dir($dir)) {
                            mkdir($dir);
                        }
                        $dir.= "$ListingRimgid/";
                        if (!is_dir($dir)) {
                            mkdir($dir);
                        }
                        $outputDir = $upload_dir['basedir'] . "/property/" . $ListingRimgid . "/";
                        $photos = $rets->GetObject("Property", "LargePhoto", $ListingRimgid);
                        /* for($i=0;$i<=count($photos); $i++){
                          echo $i;
                          } */
                        $count_image = 0;
                        foreach ($photos as $photo) {
                            $count_image++;
                            if ($photo['Success'] == true) {
                                file_put_contents("$outputDir/{$ListingRimgid}_{$count_image}.jpg", $photo['Data']);
                                if ($count_image == 1) {
                                    $insert_image = "insert into $creasync_posts_table set post_author = '1', post_date = '" . date("Y-m-d h:i:s") . "',  post_date_gmt = '" . date("Y-m-d h:i:s") . "',  post_title = '" . $ListingRimgid . "_" . $count_image . "', post_status = 'inherit', comment_status = 'open', ping_status = 'open', post_name = '" . $ListingRimgid . "_" . $count_image . "-" . $count_image . "', post_parent = '" . $property_main_id . "', guid = '$upload_url" . "/property/" . $ListingRimgid . "/" . $ListingRimgid . "_" . $count_image . ".jpg', post_mime_type = 'image/jpeg', post_type = 'attachment'";
                                    $res_image_ins = $wpdb->query($insert_image);
                                    $image_main = $wpdb->insert_id;
                                    $insert_thumnail_id = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $property_main_id . "', meta_key = '_thumbnail_id', meta_value = '" . $image_main . "'");
                                    $insert_attached = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $image_main . "', meta_key = '_wp_attached_file', meta_value = '" . "property/" . $ListingRimgid . "/" . $ListingRimgid . "_" . $count_image . ".jpg'");
                                } else {
                                    $insert_image = "insert into $creasync_posts_table set post_author = '1', post_date = '" . date("Y-m-d h:i:s") . "',  post_date_gmt = '" . date("Y-m-d h:i:s") . "',  post_title = '" . $ListingRimgid . "_" . $count_image . "', post_status = 'inherit', comment_status = 'open', ping_status = 'open', post_name = '" . $ListingRimgid . "_" . $count_image . "-" . $count_image . "', post_parent = '" . $property_main_id . "', guid = '$upload_url" . "/property/" . $ListingRimgid . "/" . $ListingRimgid . "_" . $count_image . ".jpg', post_mime_type = 'image/jpeg'";
                                    $res_image_ins = $wpdb->query($insert_image);
                                    $image_main = $wpdb->insert_id;
                                    $insert_attached = $wpdb->query("insert into $creasync_postmeta_table set post_id = '" . $image_main . "', meta_key = '_wp_attached_file', meta_value = '" . "property/" . $ListingRimgid . "/" . $ListingRimgid . "_" . $count_image . ".jpg'");
                                }
                            }
                        }
                        echo "<br> + Added a Property #ID => $ListingRimgid<br>\n";
                    }
                }
            }
        } else {
            echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Free version only sync 10 properties, please upgrade for unlimited number of properties</strong></p></div>';
        }
    } else {
        echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Property Synching desabled now, Please enable it and try again later!</strong></p></div>';
    }
}

?>
