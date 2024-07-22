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
