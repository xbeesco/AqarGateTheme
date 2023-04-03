<?php
add_action( 'init', 'get_array_property_data');
function get_array_property_data(){
    global $post, $hide_fields, $top_area, $property_layout, $map_street_view;

    $property_data= array();
    $args = array(
         'post_type'        =>  'property',
         'posts_per_page'   => -1,
         'post_status'      =>  'publish',
         'suppress_filters' => false
    );
    $prop_qry = get_posts($args); 
    if( $prop_qry && is_array($prop_qry) ){
        foreach( $prop_qry as $prop ){
            $prop_id = $prop->ID;
            $userID = $prop->post_author;
            $prop_date = $prop->post_date;
            $prop_date_modified = $prop->post_modified;
            $id_number  = get_the_author_meta( 'aqar_author_id_number' , $userID );
            $ad_number  = get_the_author_meta( 'aqar_author_ad_number', $userID );
            if(empty($ad_number)){
                $$ad_number = 0 ;
            }
            $type_id    = get_the_author_meta( 'aqar_author_type_id', $userID );
            if(!empty($type_id)){
                if( $type_id == 1 ){ $Advertiser_category = 'مواطن'; }
                if( $type_id == 2 ){ $Advertiser_category = 'مقيم'; }
                if( $type_id == 3 ){ $Advertiser_category = 'منشأة'; }
            }else{
                $Advertiser_category = 0;
            }
            
            $first_name  = get_the_author_meta( 'first_name' , $userID );
            $last_name   = get_the_author_meta( 'last_name' , $userID );
            $user_email  = get_the_author_meta( 'user_email' , $userID );
            $user_mobile = get_the_author_meta( 'fave_author_mobile' , $userID );
            $license     = get_the_author_meta( 'fave_author_license' , $userID );
            if( empty( $license ) ){
                $license = 0 ;
            } else {
                $license = preg_replace('/\D/', '', $license);
            }
            $expiration_date = get_houzez_listing_expire($prop_id);
            // prr($expiration_date);
            $fave_property_price = get_post_meta( $prop_id, 'fave_property_price', true);
            $Selling_Meter_Price = get_post_meta($prop_id, 'fave_d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9', true);
            if(empty($Selling_Meter_Price)){
                $Selling_Meter_Price = 0 ;
            }
            $The_main_type_of_ad =  get_post_meta( $prop_id, 'fave_d986d988d8b9-d8a7d984d8a5d8b9d984d8a7d986-d8a7d984d8b1d8a6d98ad8b3d98a', true);
            if(empty($The_main_type_of_ad)){
                $The_main_type_of_ad = 0 ;
            }
            $Ad_subtype =  wp_get_post_terms( $prop_id, 'property_status', array("fields" => "all"));
            if (!is_wp_error($Ad_subtype) && $Ad_subtype) {
                $Ad_subtype = $Ad_subtype[0]->name;
            }else{
                $Ad_subtype = '0';
            }
           $property_state =  wp_get_post_terms( $prop_id, 'property_state', array("fields" => "all"));
            if (!is_wp_error($property_state) && $property_state) {
                $property_state = $property_state[0]->name;
            }else{
                $property_state = '0';
            }
            $property_city =  wp_get_post_terms( $prop_id, 'property_city', array("fields" => "all"));
            if (!is_wp_error($property_city) && $property_city) {
                $property_city = $property_city[0]->name;
            }else{
                $property_city = '0';
            }
            $property_area =  wp_get_post_terms( $prop_id, 'property_area', array("fields" => "all"));
            if (!is_wp_error($property_area) && $property_area) {
                $property_area = $property_area[0]->name;
            }else{
                $property_area = '0';
            }
            $property_label =  wp_get_post_terms( $prop_id, 'property_label', array("fields" => "all"));
            if (!is_wp_error($property_label) && $property_area) {
                $property_label = isset($property_label[0]->name) ? $property_label[0]->name : '0';
            }else{
                $property_label = '0';
            }
            $property_type =  wp_get_post_terms( $prop_id, 'property_type', array("fields" => "all"));
            $Using_For = '';
            if (!is_wp_error($property_type) && $property_type) {
                if($property_type[0]->term_id == 0){
                    $Using_For = $property_type[0]->name ;
                    $property_type = $property_type[0]->name;
                } else {
                    $Using_For = get_term($property_type[0]->term_id, 'property_type');
                    $Using_For = $Using_For->name;
                    $property_type = get_term($property_type[0]->parent, 'property_type');
                    $property_type = property_exists( $property_type, 'name' ) ? $property_type->name : '0';
                }
                
            }else{
                $property_type = '0';
            }
           

            $Lattitude = get_post_meta( $prop_id, 'houzez_geolocation_lat', true );
            $Longitude = get_post_meta( $prop_id, 'houzez_geolocation_long', true );
            $Street_Name = get_post_meta( $prop_id, 'fave_property_map_address', true );
            $additional_features = get_post_meta($prop_id, 'additional_features', true);
            $prop_beds = get_post_meta( $prop_id, 'fave_property_bedrooms', true );
            $prop_baths = get_post_meta( $prop_id, 'fave_property_bathrooms', true );
            $Land_Number = get_post_meta($prop_id, 'fave_d8b1d982d985-d8a7d984d8a3d8b1d8b6', true) ;
            $Plan_Number = get_post_meta($prop_id, 'fave_d8b1d982d985-d8a7d984d985d8aed8b7d8b7', true); 
            $Number_Of_Units = get_post_meta($prop_id, 'fave_d8b9d8afd8af-d8a7d984d988d8add8afd8a7d8aa', true);
            if(empty($Land_Number) || empty($Plan_Number) || empty($Number_Of_Units)){
                $Land_Number = $Plan_Number = $Number_Of_Units = 0 ;
            }
            $prop_size = houzez_get_listing_area_size( $prop_id );
            $Rooms_Number = get_post_meta( $prop_id, 'fave_property_rooms', true );
            $Construction_Date = get_post_meta( $prop_id, 'fave_property_year', true );
            if(empty($Construction_Date)){$Construction_Date = 0 ;}
            $Street_Width = get_post_meta($prop_id, 'fave_d8b9d8b1d8b6-d8a7d984d8b4d8a7d8b1d8b9', true); 
            if(empty($Street_Width)){$Street_Width = 0 ;}
            $Property_limits_and_lenghts = get_post_meta($prop_id, 'fave_d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1', true);  
            if(empty($Property_limits_and_lenghts)) {
                $Property_limits_and_lenghts = 0;
            }     
            $Is_there_mortgage = get_post_meta($prop_id, 'fave_d987d984-d98ad988d8acd8af-d8a7d984d8b1d987d986-d8a3d988-d8a7d984d982d98ad8af-d8a7d984d8b0d98a-d98ad985d986d8b9-d8a7d988-d98ad8add8af', true); 
            $Rights_and_obligations = get_post_meta($prop_id, 'fave_d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985', true); 
            $Information_that_may_affect_the_property = get_post_meta($prop_id, 'fave_d8a7d984d985d8b9d984d988d985d8a7d8aa-d8a7d984d8aad98a-d982d8af-d8aad8a4d8abd8b1-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8b3d988d8a7d8a1', true); 
            $Property_disputes = get_post_meta($prop_id, 'fave_d8a7d984d986d8b2d8a7d8b9d8a7d8aa-d8a7d984d982d8a7d8a6d985d8a9-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1', true); 
            $Availability_of_elevators = get_post_meta($prop_id, 'fave_d8aad988d8a7d981d8b1-d8a7d984d985d8b5d8a7d8b9d8af', true); 
            $Number_of_elevators = get_post_meta($prop_id, 'fave_d8b9d8afd8af-d8a7d984d985d8b5d8a7d8b9d8af', true); 
            $Availability_of_Parking = get_post_meta($prop_id, 'fave_d8aad988d981d8b1-d8a7d984d985d988d8a7d982d981', true); 
            $Number_of_parking = get_post_meta($prop_id, 'fave_prop_garage', true); 
            if(empty($Number_of_parking) || empty($Number_of_elevators) || empty($Rooms_Number)){
                $Number_of_parking = 0 ; $Number_of_elevators = 0 ;
                $Rooms_Number = 0 ;
            }
            if(empty($Availability_of_elevators) || empty($Is_there_mortgage) || empty($Availability_of_Parking)
            || empty($Property_disputes) || empty($Information_that_may_affect_the_property )
            || empty($Rights_and_obligations) || empty($Is_there_mortgage)){
                $Availability_of_elevators = 'لا يوجد';
                $Is_there_mortgage = 'لا يوجد';
                $Availability_of_Parking = 'لا يوجد';
                $Property_disputes = 'لا يوجد';
                $Information_that_may_affect_the_property = 'لا يوجد';
                $Rights_and_obligations = 'لا يوجد'; 
                $Is_there_mortgage = 'لا يوجد'; 
            }
            $Authorization_number = get_post_meta($prop_id, 'fave_d8b1d982d985-d8a7d984d8aad981d988d98ad8b6', true); 
            if(empty($Authorization_number)){
                $Authorization_number = 'مفوض كتابيا' ;
            }
            $Real_Estate_Facade = get_post_meta($prop_id, 'fave_d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1', true); 
            if(empty($Real_Estate_Facade)){
                $Real_Estate_Facade = 0;
            }
            $Ad_description = $prop->post_content ;
            if(empty($Ad_description)){
                $Ad_description = '0';
            }else{
                $Ad_description = $Ad_description;
            }
            $total_views = intval( get_post_meta($prop_id, 'houzez_total_property_views', true) );
            $user = new WP_User($userID); 
            $user_role = houzez_user_role_by_user_id($userID);
            $Advertiser_character = '';
            if( $user_role == "houzez_agent"  ) { $Advertiser_character =  "مفوض";}
            elseif( $user_role == "houzez_agency" ) { $Advertiser_character =  "مفوض"; }
            elseif( $user_role == "houzez_owner"  ) { $Advertiser_character =  "مالك"; } 
            elseif( $user_role == "houzez_buyer"  ) { $Advertiser_character =  "مفوض"; } 
            elseif( $user_role == "houzez_seller" ) { $Advertiser_character =  "مفوض" ; }
            elseif( $user_role == "houzez_manager") { $Advertiser_character = "مفوض"; }

            $property_data[] = array(
                 'Ad_Id' => $prop_id,
                 'Advertiser_character' =>$Advertiser_character,
                 'Advertiser_name' => $first_name.' '.$last_name,
                 'Advertiser_mobile_number' => $user_mobile,
                 'The_main_type_of_ad' => $The_main_type_of_ad,
                 'Ad_description' => $Ad_description,
                 'Ad_subtype' => $Ad_subtype,
                 'Advertisement_publication_date' => $prop_date,
                 'Ad_update_date' => $prop_date_modified,
                 'Ad_expiration' => $expiration_date ,
                 'Ad_status' => $property_label,
                 'Ad_Views' => $total_views,
                 'District_Name' => $property_state,
                 'City_Name' => $property_city,
                 'Neighbourhood_Name' => $property_area,
                 'Street_Name' => $Street_Name,
                 'Longitude' => $Longitude,
                 'Lattitude' => $Lattitude,
                 'Furnished' => 'لا',
                 'Kitchen' => 'لا',
                 'Air_Condition' => 'لا',
                 'facilities' => 'لا يوجد',
                 'Using_For' => $property_type,
                 'Property_Type' => $Using_For,
                 'The_Space' => $prop_size,
                 'Land_Number' => $Land_Number,
                 'Plan_Number' => $Plan_Number,
                 'Number_Of_Units' => $Number_Of_Units,
                 'Floor_Number' => 0,
                 'Unit_Number' => 0,
                 'Rooms_Number' => isset($Rooms_Number) ? $Rooms_Number : '0',
                 'Rooms_Type' => 0,
                 'Real_Estate_Facade' => $Real_Estate_Facade,
                 'Street_Width' => $Street_Width,
                 'Construction_Date' => $Construction_Date,
                 'Rental_Price' => $fave_property_price,
                 'Selling_Price' =>  $fave_property_price ,
                 'Selling_Meter_Price' => $Selling_Meter_Price,
                 'Property_limits_and_lenghts' => $Property_limits_and_lenghts,
                 'Is there a mortgage or restriction that prevents or limits the use of the property' =>  $Is_there_mortgage ,
                 'Rights and obligations over real estate that are not documented in the real estate document' => $Rights_and_obligations ,
                 'Information that may affect the property' => $Information_that_may_affect_the_property ,
                 'Property disputes' => $Property_disputes ,
                 'Availability of elevators' =>  $Availability_of_elevators ,
                 'Number of elevators' => $Number_of_elevators ,
                 'Availability of Parking' => $Availability_of_Parking  ,
                 'Number of parking' => $Number_of_parking ,
                 'Advertiser category' => $Advertiser_category,
                 'Advertiser license number' => $ad_number ,
                 'Advertiser`s email' => $user_email,
                 'Advertiser registration number' => intval($license),
                 'Authorization number' => $Authorization_number,
         );
            
        }
        return $property_data;
    }
 }
 add_action( 'init', 'ag_html_export_field');
 function ag_html_export_field() {
    if (is_admin() && isset($_GET['page']) == 'crb_carbon_fields_container_ag_settings.php') {
        // get array of data to export .
        $get_array_property_data = get_array_property_data();
        // prr($get_array_property_data);
        $time_now = date('Ymd_his');
        $filename = "aqargate.com_". $time_now .".csv";

        if (isset($_GET['export']) && $_GET['export'] === '1') {
            $AqarGate_Export = new AqarGate_Export();
            $AqarGate_Export->array_csv_download($get_array_property_data, $filename, $delimiter=";");
            die();
        }
    }
}