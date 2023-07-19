<?php
    $property_area = csv_to_array($file);
    // 3- insert province to property tax .
        $property_cities = get_terms( array(
            'taxonomy' => 'property_city',
            'hide_empty' => false,
        ) );

        foreach ( $property_area as $mdaKey ) {
            // prr($mdaKey);wp_die();
            $cityId = $mdaKey['CITY_ID'];
            $nameAr = $mdaKey['DISTRICTNAME_AR'];
            $_id    = $mdaKey['DISTRICT_ID'];
            $property_area_slug  = $nameAr.'-'.$_id;


                foreach( $property_cities as $_term ){
                    $slug = $_term->slug;
                    $city_Id = get_option( '_houzez_property_city_'.$_term->term_id, true ); 
                    if( $cityId == $city_Id['CITY_ID'] ){
                        $houzez_meta['parent_city'] = $slug;
                    }
                 }
            $inserted_term =  wp_insert_term($nameAr, 'property_area', [
                'slug' => $property_area_slug,
            ]);

            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
                update_term_meta( $new_term_id, 'term_from_file', 'NEW' );
                update_term_meta( $new_term_id, 'DISTRICT_ID', $_id );

            }
            // var_dump($new_term_id);wp_die();
            $houzez_meta['DISTRICT_ID'] = $_id;
            
            update_option( '_houzez_property_area_'.$new_term_id, $houzez_meta );
        }    