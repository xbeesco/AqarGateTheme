<?php
    $file = AG_DIR.'sa-data/property_provinces.csv';
    $property_provinces = csv_to_array($file);
    
    // 1- insert province to property tax .
    foreach ( $property_provinces as $province ) {
            // prr($mdaKey);wp_die();
            $nameAr = $mdaKey['nameAr'];
            $provinc_id = $mdaKey['id'];  
            $inserted_term =  wp_insert_term($nameAr, 'property_state');
            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
            }
            // var_dump($new_term_id);wp_die();
            $houzez_meta['parent_country'] = 'saudi-arabia';
            $houzez_meta['provinceId'] = $provinc_id;
    
            update_option('_houzez_property_state_'.$new_term_id, $houzez_meta);
    }  