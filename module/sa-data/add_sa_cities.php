<?php

    $property_cities = csv_to_array(CITIES);
    $property_state = get_terms( array(
        'taxonomy'   => 'property_state',
        'hide_empty' => false,
    ) );
    // 2- insert province to property tax .
    foreach ( $property_cities as $mdaKey ) {
        // prr($mdaKey);wp_die();
        $provinceId = $mdaKey['REGION_ID'];
        $nameAr     = $mdaKey['CITYNAME_AR'];
        $_id        = $mdaKey['CITY_ID'];
        $slug       = $nameAr.'-'.$_id;

        
        foreach($property_state as $term){
            $province_Id = get_option( '_houzez_property_state_'.$term->term_id, true );
            if( $provinceId == $province_Id['REGION_ID'] ){
                $houzez_meta['parent_state'] = $term->slug;
            }   
        }

        $inserted_term =  wp_insert_term($nameAr, 'property_city', [
            'slug' => $slug,
        ]);
        
        if (is_wp_error($inserted_term)) {
            $new_term_id = $inserted_term->error_data['term_exists'];
        } else {
            $new_term_id = $inserted_term['term_id'];
            update_term_meta( $new_term_id, 'term_from_file', 'NEW' );
            update_term_meta( $new_term_id, 'CITY_ID', $_id );
        }

        // var_dump($new_term_id);wp_die();
        $houzez_meta['CITY_ID'] = $_id;

        update_option( '_houzez_property_city_'.$new_term_id, $houzez_meta );
    }     