<?php

// Handle AJAX requests
add_action('wp_ajax_add_property_location', 'handle_add_property_location');
add_action('wp_ajax_sync_locations', 'handle_sync_property_locations');
add_action('wp_ajax_sync_property_data_rega', 'handle_sync_property_data_rega');
add_action('wp_ajax_fetch_agency_users', 'fetch_agency_users');
/**
 * Summary of handle_add_property_location
 * @return void
 */
function handle_add_property_location() {
    $location_type = sanitize_text_field($_POST['location_type']);
    
    if ($location_type === 'REGION') {
        $json_file          = AG_DIR . 'admin/locations/Region.json';
        $property_provinces = json_decode(file_get_contents($json_file), true);
        foreach ($property_provinces as $province) {
            $terms = get_terms(array(
                'taxonomy'   => 'property_state',
                'hide_empty' => false,
            ));
            
            $exists = false;
            foreach ($terms as $term) {
                $province_Id = get_option('_houzez_property_state_' . $term->term_id, true);
                if (isset($province_Id['REGION_ID']) && $province_Id['REGION_ID'] === $province['LKRegionId']) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $nameAr     = $province['LKRegionAr'];
                $provinc_id = $province['LKRegionId'];
                $slug       = str_replace(' ', '-', $nameAr) . '-' . $provinc_id;

                $inserted_term = wp_insert_term($slug, 'property_state', [
                    'slug' => $slug,
                ]);

                if (is_wp_error($inserted_term)) {
                    $new_term_id = $inserted_term->error_data['term_exists'];
                } else {
                    $new_term_id = $inserted_term['term_id'];
                    update_term_meta($new_term_id, 'term_from_file', 'CSV-SYNC');
                    update_term_meta($new_term_id, 'REGION_ID', $provinc_id);
                }

                $houzez_meta['parent_country'] = 'saudi-arabia';
                $houzez_meta['REGION_ID'] = $provinc_id;

                update_option('_houzez_property_state_' . $new_term_id, $houzez_meta);
            }
        }
        echo 'All regions have been added successfully!';
    } elseif ($location_type === 'CITY') {
        $file_number     = intval($_POST['file_number']);
        $part            = intval($_POST['part']);
        $json_file       = AG_DIR . 'admin/locations/city_' . $file_number . '.json';
        $property_cities = json_decode(file_get_contents($json_file), true);
        $property_state = get_terms(array(
            'taxonomy'   => 'property_state',
            'hide_empty' => false,
        ));

        // Split the array into four parts accurately
        $total_cities = count($property_cities);
        $chunk_size = ceil($total_cities / 4);
        $cities_to_process = array_slice($property_cities, ($part - 1) * $chunk_size, $chunk_size);
        $houzez_meta['parent_state'] = '';
        foreach ($cities_to_process as $city) {
            $provinceId = $city['LKRegionId'];
            $nameAr     = $city['LKCityAr'];
            $_id        = $city['LKCityId'];
            $slug       = str_replace(' ', '-',$nameAr) . '-' . $_id;

            foreach ($property_state as $term) {
                $province_Id = get_option('_houzez_property_state_' . $term->term_id, true);
                if ($provinceId == $province_Id['REGION_ID']) {
                    $houzez_meta['parent_state'] = $term->slug;
                }
            }

            $inserted_term = wp_insert_term($slug, 'property_city', [
                'slug' => $slug,
            ]);

            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
                update_term_meta($new_term_id, 'term_from_file', 'CSV-SYNC');
                update_term_meta($new_term_id, 'CITY_ID', $_id);
            }

            $houzez_meta['CITY_ID'] = $_id;

            update_option('_houzez_property_city_' . $new_term_id, $houzez_meta);
        }
        echo 'City file ' . $file_number . ' part ' . $part . ' processed successfully!';
    } elseif ($location_type === 'DISTRICT') {
        $file_number        = intval($_POST['file_number']);
        $part               = intval($_POST['part']);
        $json_file          = AG_DIR . 'admin/locations/district_' . $file_number . '.json';
        $property_districts = json_decode(file_get_contents($json_file), true);

        // Split the array into four parts accurately
        $total_districts = count($property_districts);
        $chunk_size = ceil($total_districts / 4);
        $districts_to_process = array_slice($property_districts, ($part - 1) * $chunk_size, $chunk_size);
        $houzez_meta['parent_city'] = '';
        foreach ($districts_to_process as $district) {
            $cityId  = $district['LKCityId'];
            $nameAr  = $district['LKDistrictAr'];
            $_id     = $district['LKDistrictId'];
            $slug    = str_replace(' ', '-',$nameAr) . '-' . $_id;

            // Directly query the city term with the CITY_ID
            $term_id = _get_term_id_by_meta('CITY_ID', $cityId, 'property_city');
            if ($term_id !== null) {
                $city_term = get_term($term_id);
                $houzez_meta['parent_city'] = $city_term->slug;
            }

            $inserted_term = wp_insert_term($slug, 'property_area', [
                'slug' => $slug,
            ]);

            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
                update_term_meta($new_term_id, 'term_from_file', 'CSV-SYNC');
                update_term_meta($new_term_id, 'DISTRICT_ID', $_id);
            }

            $houzez_meta['DISTRICT_ID'] = $_id;

            update_option('_houzez_property_area_' . $new_term_id, $houzez_meta);
        }
        echo 'District file ' . $file_number . ' part ' . $part . ' processed successfully!';
    } else {
        echo 'هناك خطأ ما!';
    }
    wp_die();
}

/**
 * Summary of handle_sync_property_locations
 * @return void
 */
function handle_sync_property_locations() {
    //@ini_set( 'display_errors', 1 );
    $properties = get_posts(array(
        'post_type'      => 'property',
        'posts_per_page' => -1,
        'meta_key'       => 'advertisement_response',
        'meta_compare'   => 'EXISTS'
    ));

    $total_properties = count($properties);
    $processed = 0;
    $log_entries = [];

    foreach ($properties as $property) {
        $prop_id = $property->ID;
        $property_name = get_the_title($prop_id);
        $advertisement_response = get_post_meta($prop_id, 'advertisement_response', true);
        
        if (is_array($advertisement_response)) {
            $advertisement_response = json_encode($advertisement_response);
        }

        $data = json_decode($advertisement_response);
        $property_log = [
            'ID' => $prop_id,
            'Property' => $property_name,
            'State' => '',
            'City' => '',
            'Area' => ''
        ];

        if ($data && isset($data->location)) {
            // Sync state
            if (isset($data->location->region) && !empty($data->location->region)) {
                $state_code     = removeLeadingZero($data->location->regionCode);
                $property_state = str_replace(' ', '-', $data->location->region) . '-' . $state_code;
                
                $term_id = _get_term_id_by_meta('REGION_ID', $state_code, 'property_state');
                if ($term_id !== null) {
                    wp_set_object_terms($prop_id, $term_id, 'property_state');
                    $state_id = $term_id;
                    $property_log['State'] = _get_term_name_by_id($state_id, 'property_state');
                } else {
                    $state_id = wp_set_object_terms($prop_id, $property_state, 'property_state');
                    $state_id = $state_id[0];
                    update_term_meta($state_id, 'REGION_ID', $state_code);
                    update_term_meta($state_id, 'term_from_file', 'CSV-SYNC');
                    $property_log['State'] = _get_term_name_by_id($state_id, 'property_state');
                }
            }

            // Sync city
            if (isset($data->location->city) && !empty($data->location->cityCode)) {
                $city_code     = removeLeadingZero($data->location->cityCode);
                $property_city = str_replace(' ', '-',$data->location->city) . '-' . $city_code;
                $term_id = _get_term_id_by_meta('CITY_ID', $city_code, 'property_city');
                if ($term_id !== null) {
                    wp_set_object_terms($prop_id, $term_id, 'property_city');
                    $city_id = $term_id;
                    $property_log['City'] = _get_term_name_by_id($city_id, 'property_city');
                } else {
                    $city_id = wp_set_object_terms($prop_id, $property_city, 'property_city');
                    $city_id = $city_id[0];
                    $property_log['City'] = _get_term_name_by_id($city_id, 'property_city');
                }
                $term_object = get_term($state_id);
                $parent_state = $term_object->slug ?? '';
                $houzez_meta = array();
                $houzez_meta['parent_state'] = $parent_state;
                if (!empty($city_id) && !empty($houzez_meta['parent_state'])) {
                    update_option('_houzez_property_city_' . $city_id, $houzez_meta);
                    update_term_meta($city_id, 'CITY_ID', $city_code);
                    update_term_meta($city_id, 'term_from_file', 'CSV-SYNC');

                }
            }

            // Sync area
            if (isset($data->location->district) && !empty($data->location->districtCode)) {
                $area_code     = removeLeadingZero($data->location->districtCode);
                $property_area = str_replace(' ', '-',$data->location->district) . '-' . $area_code;
                $term_id = _get_term_id_by_meta('DISTRICT_ID', $area_code, 'property_area');
                if ($term_id !== null) {
                    wp_set_object_terms($prop_id, $term_id, 'property_area');
                    $area_id = $term_id;
                    $property_log['Area'] = _get_term_name_by_id($area_id, 'property_area');
                } else {
                    $area_id = wp_set_object_terms($prop_id, $property_area, 'property_area');
                    $area_id = $area_id[0];
                    $property_log['Area'] = _get_term_name_by_id($area_id, 'property_area');
                }
                $term_object = get_term($city_id);
                $parent_city = $term_object->slug ?? '';
                $houzez_meta = array();
                $houzez_meta['parent_city'] = $parent_city;
                if (!empty($area_id) && !empty($houzez_meta['parent_city'])) {
                    update_option('_houzez_property_area_' . $area_id, $houzez_meta);
                    update_term_meta($area_id, 'DISTRICT_ID', $area_code);
                    update_term_meta($area_id, 'term_from_file', 'CSV-SYNC');

                }
            }
        }

        $processed++;
        $log_entries[] = $property_log;
    }

    echo json_encode(array(
        'progress' => 100,
        'message' => 'All properties ' . $total_properties . ' have been synchronized successfully!',
        'log' => $log_entries
    ));
    wp_die();
}

/**
 * Summary of handle_sync_property_data_rega
 * @return void
 */
function handle_sync_property_data_rega() {
    // Sync property data to REGA logic (this is just an example, adjust as needed)
    echo 'Property data synced to REGA successfully!';
    wp_die();
}

/**
 * Summary of fetch_agency_users
 * @return void
 */
function fetch_agency_users() {
    
    $role = 'houzez_agency'; // Specify the role
    $args = array(
        'role'    => $role,
        'orderby' => 'display_name',
        'order'   => 'ASC'
    );
    $user_query = new WP_User_Query($args);

    $users = array();
    if (!empty($user_query->get_results())) {
        foreach ($user_query->get_results() as $user) {
            $fave_author_title = get_user_meta($user->ID, 'fave_author_title', true);
            if (!empty($fave_author_title)) {
                list($first_name, $last_name) = split_full_name($fave_author_title);

                wp_update_user(array(
                    'ID' => $user->ID,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $fave_author_title,
                ));
            } else {
                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name = get_user_meta($user->ID, 'last_name', true);
            }

            $users[] = array(
                'ID' => $user->ID,
                'display_name' => $user->display_name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'role' => implode(', ', $user->roles)
            );
        }
    }

    echo json_encode(array('users' => $users));
    wp_die();
}

// Helper function to get term ID by meta
/**
 * Summary of _get_term_id_by_meta
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param mixed $taxonomy
 * @return int|null
 */
function _get_term_id_by_meta($meta_key, $meta_value, $taxonomy) {
    global $wpdb;
    $term_id = $wpdb->get_var($wpdb->prepare("
        SELECT term_id 
        FROM $wpdb->termmeta 
        WHERE meta_key = %s AND meta_value = %s
    ", $meta_key, $meta_value));
    
    return $term_id ? intval($term_id) : null;
}

/**
 * Summary of _get_term_name_by_id
 * @param mixed $term_id
 * @param mixed $taxonomy
 * @return string|null
 */
function _get_term_name_by_id($term_id, $taxonomy) {
    $term = get_term($term_id, $taxonomy);
    if (!is_wp_error($term) && $term) {
        return $term->name;
    }
    return null;
}

/**
 * Summary of removeLeadingZero
 * @param mixed $string
 * @return mixed
 */
function removeLeadingZero($string) {
    // Check if the string starts with '0' and the second character is a digit
    if (substr($string, 0, 1) === '0' && ctype_digit(substr($string, 1, 1))) {
        // Remove the leading '0'
        return substr($string, 1);
    }
    // Return the original string if no leading zero to remove
    return $string;
}

add_action('wp_ajax_sync_properties', 'handle_sync_properties');

function handle_sync_properties() {
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_size = 10;

    $properties = get_posts(array(
        'post_type'      => 'property',
        'posts_per_page' => $batch_size,
        'offset'         => $offset,
        'meta_key'       => 'advertisement_response',
        'meta_compare'   => 'EXISTS',
        'post_status'    => 'publish'
    ));

    $total_properties = count($properties);

    $processed = 0;
    $log = [];

    foreach ($properties as $property) {
        $prop_id = $property->ID;
        $property_name = get_the_title($prop_id);

        // Prepare the form data for each property
        $formDataArray = [
            'prop_price' => get_post_meta($prop_id, 'fave_property_price', true),
            // Add more fields as necessary
        ];

        // Serialize the form data
        $serializedData = http_build_query($formDataArray);

        // Mimic the $_POST array
        $_POST['propID'] = $prop_id;
        $_POST['formData'] = $serializedData;

        // Call the function
        $output = PlatformCompliance_property('UpdateAd');

        // Decode the response
        $response = json_decode($output, true);

        $log[] = [
            'ID' => $prop_id,
            'Property' => wp_trim_words($property_name, 10, '...'),
            'Status' => $response['success'] ? 'Success' : 'Failed',
            'Message' => $response['reason']
        ];
        $processed++;
    }

    $remaining_properties = wp_count_posts('property')->publish - ($offset + $batch_size);
    $progress = round((($offset + $batch_size) / wp_count_posts('property')->publish) * 100);

    echo json_encode(array(
        'progress' => $progress,
        'message' => $remaining_properties > 0 ? 'Synchronizing...' : 'All properties have been synchronized successfully!',
        'log' => $log,
        'next_offset' => $remaining_properties > 0 ? $offset + $batch_size : null
    ));
    wp_die();
}

add_action('wp_ajax_sync_expire_properties', 'handle_sync_expire_properties');

function handle_sync_expire_properties() {
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_size = 10;

    // Get the count of expired properties with the specified meta key
    $query = new WP_Query([
        'post_type' => 'property',
        'posts_per_page' => -1, // Retrieve all matching posts
        'meta_key' => 'advertisement_response',
        'meta_compare' => 'EXISTS',
        'post_status' => 'expired',
        'fields' => 'ids' // Only get the IDs to save memory
    ]);

    $total_properties = $query->found_posts; // Total number of matching properties

    $properties = get_posts([
        'post_type' => 'property',
        'posts_per_page' => $batch_size,
        'offset' => $offset,
        'meta_key' => 'advertisement_response',
        'meta_compare' => 'EXISTS',
        'post_status' => 'expired',
    ]);

    $log = [];

    foreach ($properties as $property) {
        $prop_id = $property->ID;
        $property_name = get_the_title($prop_id);

        // Prepare the form data for each property
        $formDataArray = [
            'prop_price' => get_post_meta($prop_id, 'fave_property_price', true),
            // Add more fields as necessary
        ];

        // Serialize the form data
        $serializedData = http_build_query($formDataArray);

        // Mimic the $_POST array
        $_POST['propID'] = $prop_id;
        $_POST['formData'] = $serializedData;

        // Call the function
        $output = PlatformCompliance_property('CancelAd');

        // Decode the response
        $response = json_decode($output, true);

        $log[] = [
            'ID' => $prop_id,
            'Property' => wp_trim_words($property_name, 10, '...'),
            'Status' => $response['success'] ? 'Success' : 'Failed',
            'Message' => $response['reason']
        ];
    }

    $remaining_properties = $total_properties - ($offset + $batch_size);
    $progress = round((($offset + $batch_size) / $total_properties) * 100);

    echo json_encode(array(
        'progress' => $progress,
        'message' => $remaining_properties > 0 ? 'Synchronizing...' : 'All expired properties have been synchronized successfully!',
        'log' => $log,
        'next_offset' => $remaining_properties > 0 ? $offset + $batch_size : null
    ));
    wp_die();
}

/*-----------------------------------------------------------------------------------*/
// Add custom post status Hold
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists('aqar_custom_post_status_canceld') ) {
    function aqar_custom_post_status_canceld() {

        $args = array(
            'label'                     => _x( 'Canceld', 'Status General Name', 'houzez' ),
            'label_count'               => _n_noop( 'Canceld (%s)',  'Canceld (%s)', 'houzez' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'canceld', $args );

    }
    add_action( 'init', 'aqar_custom_post_status_canceld', 1 );
}
function handle_property_status_change($new_status, $old_status, $post) {
    // Ensure the post type is 'property' and status changes from 'publish' to 'expired'
    if ($post->post_type == 'property' && $old_status == 'publish' && $new_status == 'expired') {
        // Your code to handle the status change
        // For example, you might want to trigger the `handle_sync_expire_properties` function

        // Example: Call the function to handle this specific property
        $output = PlatformCompliance_property('CancelAd', $post->ID);
        // Decode the response
        $response = json_decode($output, true);

        if ( isset($response['success']) && $response['success'] === true ) {
            wp_update_post(['ID' => $post->ID, 'post_status' => 'canceld']);
        }
        
        // Or log the status change
        error_log("Property ID {$post->ID} changed from {$old_status} to {$new_status}");
    }
}
add_action('transition_post_status', 'handle_property_status_change', 10, 3);


function PlatformCompliance_property($operationType = 'UpdateAd', $prop_id = '')
{
 
        global $current_user;
        if( empty($prop_id ) ) {
            $prop_id = intval($_POST['propID']);
        }
        $serializedData = $_POST['formData']; // 'formData' is the key name sent from AJAX
        // Process the serialized form data
        parse_str($serializedData, $formDataArray);
        $userID       = get_current_user_id();
 
        $AdLinkInPlatform = get_the_permalink($prop_id); 

        $advertisement_response = get_post_meta($prop_id, 'advertisement_response', true);

        if( empty($advertisement_response) ){
            $msg =  json_encode(['success' => false, 'reason' => 'لم يتم التعديل من خلال الربط التقني المحدث'] );
            return $msg;
        }

        // جلب القيم من المصفوفة
        $advertiserId    = $advertisement_response['advertiserId'];
        $adLicenseNumber = $advertisement_response['adLicenseNumber'];
        $deedNumber      = $advertisement_response['deedNumber'];
        $advertiserName  = $advertisement_response['advertiserName'];
        // Remove any non-numeric characters from the phone number
        $phoneNumber                        = preg_replace('/\D/', '', $advertisement_response['phoneNumber']);
        $brokerageAndMarketingLicenseNumber = $advertisement_response['brokerageAndMarketingLicenseNumber'] ?? '';
        $isConstrained                      = $advertisement_response['isConstrained'] ?? '';
        $isPawned                           = $advertisement_response['isPawned'] ?? '';
        $isHalted                           = $advertisement_response['isHalted'] ?? '';
        $isTestment                         = $advertisement_response['isTestment'] ?? '';
        $rerConstraints                     = $advertisement_response['rerConstraints'] ?? '';
        $streetWidth                        = $advertisement_response['streetWidth'] ?? '';
        $propertyArea                       = $advertisement_response['propertyArea'] ?? '';
        $propertyPrice                      = $advertisement_response['propertyPrice'] ?? '';
        $landTotalPrice                     = $advertisement_response['landTotalPrice'] ?? '';
        $landTotalAnnualRent                = $advertisement_response['landTotalAnnualRent'] ?? '';
        $propertyType                       = $advertisement_response['propertyType'] ?? '';
        $propertyAge                        = $advertisement_response['propertyAge'] ?? '';
        $advertisementType                  = $advertisement_response['advertisementType'] ?? '';
        $creationDate                       = $advertisement_response['creationDate'] ?? '';
        $endDate                            = $advertisement_response['endDate'] ?? '';
        $adLicenseUrl                       = $advertisement_response['adLicenseUrl'] ?? '';
        $adSource                           = $advertisement_response['adSource'] ?? '';
        $titleDeedTypeName                  = $advertisement_response['titleDeedTypeName'] ?? '';
        $locationDescriptionOnMOJDeed       = $advertisement_response['locationDescriptionOnMOJDeed'] ?? '';
        $notes                              = $advertisement_response['notes'] ?? '';
        $channels                           = $advertisement_response['channels'] ?? '';
        
        $guaranteesAndTheirDuration = $advertisement_response['guaranteesAndTheirDuration'];
        // جلب القيم من المصفوفة المضمنة (borders)
        $borders = $advertisement_response['borders'];

        // Mapping between Arabic property utility names and their codes
        $propertyUtilitiesMapping = array(
            'كهرباء' => 'Electricity',
            'مياه' => 'Waters',
            'صرف صحي' => 'Sanitation',
            'لايوجد خدمات' => 'NoServices',
            'هاتف' => 'FixedPhone',
            'الياف ضوئية' => 'FibreOptics',
            'تصريف الفيضانات' => 'FloodDrainage'
        );

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];
      
		$current_property_price = $advertisement_response['propertyPrice'];
        $new_property_price = intval($formDataArray['prop_price']);
      
        $property_status = get_term_by('id', $formDataArray['prop_status'][0], 'property_status');
        $property_statusName = $property_status->name ?? null;
      
      	// Check if the values have changed
        if ($property_statusName == $advertisement_response['advertisementType'] && $current_property_price == $new_property_price) {
            $msg =  json_encode(['success' => true, 'reason' => 'لا حاجة لارسال الاعلان للهيئة العقارية . جاري تحديث الاعلان !']);
            return $msg;
        }
      
        $property_statusName = $advertisementTypeMapping[$property_statusName] ?? 'Sell';

        $user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
        $display_name           =   get_the_author_meta( 'aqar_display_name' , $userID );
        if( empty($display_name) ) {
            $display_name = $current_user->display_name;
        }
        if( empty($user_title) ){
            $user_title = $display_name;
        }
        $advertiserMobile = get_the_author_meta( 'fave_author_mobile' , $userID );
        $pattern = '/^\+\s*966/';
        // Remove +966 if it exists at the start
        $cleanedPhoneNumber = preg_replace($pattern, '', $advertiserMobile);

        // Add leading zero if it doesn't already start with one
        if (substr($cleanedPhoneNumber, 0, 1) !== '0') {
            $cleanedPhoneNumber = '0' . $cleanedPhoneNumber;
        }
        // prr($advertisement_response);
        $adLinkInPlatform = esc_url(get_the_permalink($prop_id));

        if( !empty($isConstrained) || !empty($isPawned) || !empty($isHalted) || !empty($isTestment) || !empty($rerConstraints) ) {
            $constraints = "True";
        }else {
            $constraints = "False";

        }

        // جلب القيم من المصفوفة المضمنة (location)
        $region           = $advertisement_response['location']['region'] ?? 'null';
        $regionCode       = $advertisement_response['location']['regionCode'] ?? 'null';
        $city             = $advertisement_response['location']['city'] ?? 'null';
        $cityCode         = $advertisement_response['location']['cityCode'] ?? 'null';
        $district         = $advertisement_response['location']['district'] ?? 'null';
        $districtCode     = $advertisement_response['location']['districtCode'] ?? 'null';
        $street           = $advertisement_response['location']['street'] ?? 'null';
        $postalCode       = $advertisement_response['location']['postalCode'] ?? 'null';
        $buildingNumber   = $advertisement_response['location']['buildingNumber'] ?? 'null';
        $additionalNumber = $advertisement_response['location']['additionalNumber'] ?? 'null';
        $longitude        = $advertisement_response['location']['longitude'] ?? 'null';
        $latitude         = $advertisement_response['location']['latitude'] ?? 'null';
        $operationReason  = !empty(get_post_meta( $prop_id, 'adverst_update_reason', true ))  ? get_post_meta( $prop_id, 'adverst_update_reason', true ) :  "Other";

        $property_type = get_term_by( 'id', $formDataArray['prop_type'][0], 'property_type' );
        $property_typeName = $property_type->name ?? null;
        
        $mappingPropertyTypes = [
            'أرض' => 'Land',
            'ارض' => 'Land',
            'دور' => 'Floor',
            'شقة' => 'Apartment',
            'فيلا' => 'Villa',
            'شقَّة صغيرة (استوديو)' => 'Studio',
            'غرفة' => 'Room',
            'استراحة' => 'RestHouse',
            'مجمع' => 'Compound',
            'برج' => 'Tower',
            'معرض' => 'Exhibition',
            'مكتب' => 'Office',
            'مستودع' => 'Warehouses',
            'كشك' => 'Booth',
            'سينما' => 'Cinema',
            'فندق' => 'Hotel',
            'مواقف سيارات' => 'CarParking',
            'ورشة' => 'RepairShop',
            'صراف' => 'Teller',
            'مصنع' => 'Factory',
            'مدرسة' => 'School',
            'مستشفى، مركز صحي' => 'HospitalOrHealthCenter',
            'محطة كهرباء' => 'ElectricityStation',
            'برج اتصالات' => 'TelecomTower',
            'محطة' => 'Station',
            'مزرعة' => 'Farm',
            'عمارة' => 'Building'
        ];


        $property_typeName = isset($mappingPropertyTypes[$property_typeName]) ? $mappingPropertyTypes[$property_typeName] : 'Land';

        $mappedUtilities = array();
        foreach ($advertisement_response['propertyUtilities'] as $utility) {
            if (isset($propertyUtilitiesMapping[$utility])) {
                $mappedUtilities[] = $propertyUtilitiesMapping[$utility];
            } else {
                // If the Arabic utility name doesn't exist in the mapping
                $mappedUtilities[] = 'NoServices';
            }
        }

        // Function to add double quotes around each item
        $quotedUtilities = array_map(function($item) {
            return '"' . $item . '"';
        }, $mappedUtilities);

        // Implode the array to create a string
        $implodedUtilities = implode(', ', $quotedUtilities);

        $advertisementTypeMapping = [
            'إيجار' => 'Rent',
            'بيع' => 'Sell',
        ];

        $property_status     = get_term_by( 'id', $formDataArray['prop_status'][0], 'property_status' );
        $property_statusName = $property_status->name ?? null;
        $property_statusName = isset($advertisementTypeMapping[$property_statusName]) ? $advertisementTypeMapping[$property_statusName] : 'Sell';
        

        $advertisement_request = '{
            "adLicenseNumber": "'.$adLicenseNumber.'",
            "adLinkInPlatform": "'.$adLinkInPlatform.'",
            "adSource": "REGA",
            "adType": "'.$property_statusName.'",
            "advertiserId": "'.$advertiserId.'",
            "advertiserMobile": "'.$cleanedPhoneNumber.'",
            "advertiserName": "'.$user_title.'",
            "brokerageAndMarketingLicenseNumber": "'.$brokerageAndMarketingLicenseNumber.'",
            "channels": [
                "LicensedPlatform"
            ],
            "constraints": "'.$constraints.'",
            "creationDate": "'.$creationDate.'",
            "endDate": "'.$endDate.'",
            "nationalAddress": {
                "additionalNo": '.$additionalNumber.',
                "adMapLatitude": '.$latitude.',
                "adMapLongitude": '.$longitude.',
                "buildingNo": '.$buildingNumber.',
                "city": "'.$city.'",
                "district": "'.$district.'",
                "postalCode": '. $postalCode .',
                "region": "'.$region.'",
                "streetName": "'.$street.'"
            },
            "operationReason": "'.$operationReason.'",
            "operationType": "'.$operationType.'",
            "platformId": "'.get_option( '_platformid' ).'",
            "platformOwnerId": "'.get_option( '_platformownerid' ).'",
            "price": '.intval($formDataArray['prop_price']).',
            "propertyArea": '.$propertyArea.',
            "propertyType": "'.$property_typeName.'",
            "propertyUsage": [
                "Commercial"
            ],
            "propertyUtilities": ['. $implodedUtilities .'],
            "qrCode": "",
            "titleDeedNumber": "'.$deedNumber.'",
            "titleDeedType": "ElectronicDeed",
        }';

        require_once AG_DIR . 'module/class-rega-module.php';

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->PlatformCompliance($advertisement_request);
        $response = json_decode( $response );
        //prr($response);wp_die();
        // echo json_encode( $advertisement_request, JSON_PRETTY_PRINT);

        // if( !aqar_can_edit($prop_id) ){
        //     echo json_encode(['success' => false, 'reason' => 'التعديل من خلال صفحة كل العقارات الخاصة بكم'] );
        //     wp_die();
        // }

        if( isset($response->Body) && $response->Body->result->response === true ){
            update_post_meta($prop_id, 'adverst_can_edit', 0);  
            update_post_meta($prop_id, 'advertisement_response', $advertisement_response );
            $msg =  json_encode(['success' => true, 'reason' => 'تم ارسال التعديل بنجاح الي الهيئة العقارية']);
            return $msg;
        } else {
            if( isset($response->httpCode)  ) {
                $msg =  json_encode(['success' => false, 'reason' => $response->httpMessage . ' ' . $response->moreInformation] );
                return $msg; 
            }elseif( isset($response->Body->result->message) ){    
                // prr($response);
                $msg =  json_encode(['success' => false, 'reason' => $response->Body->result->message] );
                return $msg; 
            }
        }
    }

function AQAR_sync_advertisement($post_id) {

    if( !empty($post_id) ) {

        $advertiserId = get_post_meta($post_id, 'advertiserId', true);
        $adLicenseNumber = get_post_meta($post_id, 'adLicenseNumber', true);
        
        require_once(AG_DIR . 'module/class-rega-module.php');
        $RegaMoudle = new RegaMoudle();
        $response = $RegaMoudle->sysnc_AdvertisementValidator($adLicenseNumber, $advertiserId);
        $response = json_decode($response);
        
        if( isset($response->Body->result->advertisement) ) {
            $data = $response->Body->result->advertisement;
            $advertisement_response = json_decode(json_encode($data), true);
            /**
             * update response
             *---------------------------------------------------------------------*/ 
            update_post_meta( $post_id, 'advertisement_response', $advertisement_response );
        }   
    }  
}

function handle_contract_property_request() {
    if (!isset($_POST['post_id']) || !is_user_logged_in()) {
        wp_send_json_error();
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();
    $post = get_post($post_id);

    if ($post->post_author != $user_id || $post->post_type != 'property_request') {
        wp_send_json_error();
    }

    // تحديث حالة المنشور إلى 'تم التعاقد'
    wp_update_post(array(
        'ID' => $post_id,
        'post_status' => 'contracted' // يمكنك تحديد حالة مخصصة إذا كنت قد أضفتها
    ));

    wp_send_json_success();
}
add_action('wp_ajax_contract_property_request', 'handle_contract_property_request');

function handle_delete_property_request() {
    if (!isset($_POST['post_id']) || !is_user_logged_in()) {
        wp_send_json_error();
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();
    $post = get_post($post_id);

    if ($post->post_author != $user_id || $post->post_type != 'property_request') {
        wp_send_json_error();
    }

    // تحديث حالة المنشور إلى 'canceled'
    wp_update_post(array(
        'ID' => $post_id,
        'post_status' => 'canceled'
    ));

    wp_send_json_success();
}
add_action('wp_ajax_delete_property_request', 'handle_delete_property_request');


// إضافة حالة منشور جديدة
function add_contracted_post_status() {
    register_post_status('contracted', array(
        'label'                     => _x('Contracted', 'post'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Contracted <span class="count">(%s)</span>', 'Contracted <span class="count">(%s)</span>'),
    ));
}
add_action('init', 'add_contracted_post_status');

// إضافة الحالة الجديدة إلى القائمة المنسدلة لتصفية المنشورات في لوحة التحكم
function append_post_status_list() {
    global $post;
    $complete = '';
    $label = '';

    if ($post->post_type == 'property_request') {
        if ($post->post_status == 'contracted') {
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> Contracted</span>';
        }
        echo '
        <script>
        jQuery(document).ready(function($){
            $("select#post_status").append("<option value=\"contracted\"' . $complete . '>Contracted</option>");
            $(".misc-pub-section label").append("' . $label . '");
        });
        </script>
        ';
    }
}
add_action('admin_footer-post.php', 'append_post_status_list');

// إضافة عمود جديد في جدول المنشورات
function add_custom_columns($columns) {
    $columns['post_status'] = __('Status', 'your-text-domain');
    return $columns;
}
add_filter('manage_property_request_posts_columns', 'add_custom_columns');

// عرض محتوى العمود الجديد
function custom_column_content($column_name, $post_id) {
    if ($column_name == 'post_status') {
        $post_status = get_post_status($post_id);
        if ($post_status == 'contracted') {
            echo '<span style="background-color: #28a745; color: #fff; padding: 5px 10px; border-radius: 3px;">تم التعاقد</span>';
        } else {
            echo ucfirst($post_status);
        }
    }
}
add_action('manage_property_request_posts_custom_column', 'custom_column_content', 10, 2);

// جعل العمود الجديد قابلاً للفرز
function custom_column_sortable($columns) {
    $columns['post_status'] = 'post_status';
    return $columns;
}
add_filter('manage_edit-property_request_sortable_columns', 'custom_column_sortable');