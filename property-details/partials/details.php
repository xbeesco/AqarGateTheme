<?php
global $hide_fields;
$prop_id = houzez_get_listing_data('property_id');
$prop_price = houzez_get_listing_data('property_price');
$prop_size = houzez_get_listing_data('property_size');
$land_area = houzez_get_listing_data('property_land');
$bedrooms = houzez_get_listing_data('property_bedrooms');
$rooms = houzez_get_listing_data('property_rooms');
$bathrooms = houzez_get_listing_data('property_bathrooms');
$year_built = houzez_get_listing_data('property_year');
$garage = houzez_get_listing_data('property_garage');
$property_status = houzez_taxonomy_simple('property_status');
$property_type = houzez_taxonomy_simple('property_type');
$garage_size = houzez_get_listing_data('property_garage_size');
$property_usage = houzez_taxonomy_simple('property_label');
$additional_features = get_post_meta( get_the_ID(), 'additional_features', true);
$advertiserId = get_post_meta( get_the_ID(), 'adLicenseNumber', true );
$creationDate = get_post_meta( get_the_ID(), 'creationDate', true );
$endDate = get_post_meta( get_the_ID(), 'endDate', true );
$borders = get_post_meta( get_the_ID(), 'borders', true );
$northLimitName = $borders['northLimitName'] ?? '';
$northLimitDescription = $borders['northLimitDescription'] ?? '';
$northLimitLengthChar = $borders['northLimitLengthChar'] ?? '';
$eastLimitName = $borders['eastLimitName'] ?? '';
$eastLimitDescription = $borders['eastLimitDescription'] ?? '';
$eastLimitLengthChar = $borders['eastLimitLengthChar'] ?? '';
$westLimitName = $borders['westLimitName'] ?? '';
$westLimitDescription = $borders['westLimitDescription'] ?? '';
$westLimitLengthChar = $borders['westLimitLengthChar'] ?? '';
$southLimitName = $borders['southLimitName'] ?? '';
$southLimitDescription = $borders['southLimitDescription'] ?? '';
$southLimitLengthChar = $borders['southLimitLengthChar'] ?? '';

?>
<div class="detail-wrap">
  <style>td {padding: 5px 10px;}</style>
	<table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
    <tbody>
        <?php
        if( !empty( $prop_id ) && $hide_fields['prop_id'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_prop_id', 'Property ID').':</strong></td><td style="border: 1px solid #ddd;"><span>'.houzez_propperty_id_prefix($prop_id).'</span></td></tr>';
        }
        if( !empty( $advertiserId )  ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong> رقم رخصة الاعلان :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$advertiserId.'</span></td></tr>';
        }
        if( $prop_price != "" && $hide_fields['sale_rent_price'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_price', 'Price'). ':</strong></td><td style="border: 1px solid #ddd;"><span>'.houzez_listing_price().'</span></td></tr>';
        }
        if( !empty( $prop_size ) && $hide_fields['area_size'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_prop_size', 'Property Size'). ':</strong></td><td style="border: 1px solid #ddd;"><span>'.houzez_property_size( 'after' ).'</span></td></tr>';
        }
        if( !empty( $land_area ) && $hide_fields['land_area'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_land', 'Land Area'). ':</strong></td><td style="border: 1px solid #ddd;"><span>'.houzez_property_land_area( 'after' ).'</span></td></tr>';
        }
        if( $bedrooms != "" && $hide_fields['bedrooms'] != 1 ) {
            $bedrooms_label = ($bedrooms > 1 ) ? houzez_option('spl_bedrooms', 'Bedrooms') : houzez_option('spl_bedroom', 'Bedroom');
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($bedrooms_label).':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $bedrooms ).'</span></td></tr>';
        }
        if( $rooms != "" && ( isset($hide_fields['rooms']) && $hide_fields['rooms'] != 1 ) ) {
            $rooms_label = ($rooms > 1 ) ? houzez_option('spl_rooms', 'Rooms') : houzez_option('spl_room', 'Room');
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($rooms_label).':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $rooms ).'</span></td></tr>';
        }
        if( $bathrooms != "" && $hide_fields['bathrooms'] != 1 ) {
            $bath_label = ($bathrooms > 1 ) ? houzez_option('spl_bathrooms', 'Bathrooms') : houzez_option('spl_bathroom', 'Bathroom');
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($bath_label).':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $bathrooms ).'</span></td></tr>';
        }
        if( $garage != "" && $hide_fields['garages'] != 1 ) {
            $garage_label = ($garage > 1 ) ? houzez_option('spl_garages', 'Garages') : houzez_option('spl_garage', 'Garage');
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($garage_label).':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $garage ).'</span></td></tr>';
        }
        if( !empty( $garage_size ) && $hide_fields['garages'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_garage_size', 'Garage Size').':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $garage_size ).'</span></td></tr>';
        }
        if( !empty( $year_built ) && $hide_fields['year_built'] != 1 ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_year_built', 'Year Built').':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $year_built ).'</span></td></tr>';
        }
        if( !empty( $property_type ) && ($hide_fields['prop_type']) != 1 ) {
            echo '<tr class="prop_type" style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_prop_type', 'Property Type').':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $property_type ).'</span></td></tr>';
        }
        if( !empty( $property_status ) && ($hide_fields['prop_status']) != 1 ) {
            echo '<tr class="prop_status" style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.houzez_option('spl_prop_status', 'Property Status').':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $property_status ).'</span></td></tr>';
        }
        // Property Usage (استخدام العقار)
        if( ($hide_fields['prop_label']) != 1 ) {
            $usage_value = !empty( $property_usage ) ? esc_attr( $property_usage ) : '-';
            echo '<tr class="prop_usage" style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>استخدام العقار:</strong></td><td style="border: 1px solid #ddd;"><span>'.$usage_value.'</span></td></tr>';
        }
        //Custom Fields
        if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields(); 
            if(!empty($fields_array)) {
                foreach ( $fields_array as $value ) {
                    $field_type = $value->type;
                    $meta_type = true;
                    if( $field_type == 'checkbox_list' || $field_type == 'multiselect' ) {
                        $meta_type = false;
                    }
                    $data_value = get_post_meta( get_the_ID(), 'fave_'.$value->field_id, $meta_type );
                    $field_title = houzez_wpml_translate_single_string($value->label);
                    if( $meta_type == true ) {
                        $data_value = houzez_wpml_translate_single_string($data_value);
                    } else {
                        $data_value = houzez_array_to_comma($data_value);
                    }
                    if( $field_type == "url" ) {
                        if(!empty($data_value) && $hide_fields[houzez_clean_20($value->field_id)] != 1) {
                            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($field_title).':</strong></td><td style="border: 1px solid #ddd;"><span><a href="'.esc_url($data_value).'" target="_blank">'.esc_attr( $data_value ).'</a></span></td></tr>';
                        }
                    } else {
                        if(!empty($data_value) && $hide_fields[houzez_clean_20($value->field_id)] != 1) {
                            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>'.esc_attr($field_title).':</strong></td><td style="border: 1px solid #ddd;"><span>'.esc_attr( $data_value ).'</span></td></tr>';
                        }    
                    }
                }
            }
        }
        if( !empty( $creationDate )  ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong> تاريخ إنشاء ترخيص الاعلان :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$creationDate.'</span></td></tr>';
        }
        if( !empty( $endDate )  ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong> تاريخ انتهاء ترخيص الاعلان :   </strong></td><td style="border: 1px solid #ddd;"><span>'.$endDate.'</span></td></tr>';
        }
        if( !empty( $borders )  ) {
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>  الحد الشمالي :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$northLimitName . ' / ' . $northLimitDescription . ' / ' . $northLimitLengthChar.'</span></td></tr>';
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>  الحد الشرقي :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$eastLimitName . ' / ' . $eastLimitDescription . ' / ' . $eastLimitLengthChar.'</span></td></tr>';
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>  الحد الغربي :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$westLimitName . ' / ' . $westLimitDescription . ' / ' . $westLimitLengthChar.'</span></td></tr>';
            echo '<tr style="border: 1px solid #ddd;"><td style="border: 1px solid #ddd;"><strong>  الحد الجنوبي :  </strong></td><td style="border: 1px solid #ddd;"><span>'.$southLimitName . ' / ' . $southLimitDescription . ' / ' . $southLimitLengthChar.'</span></td></tr>';
        }
        ?>
    </tbody>
</table>

</div>

<?php if( !empty( $additional_features[0]['fave_additional_feature_title'] ) && $hide_fields['additional_details'] != 1 ) { ?>
	<div class="block-title-wrap">
		<h3><?php echo houzez_option('sps_additional_details', 'Additional details'); ?></h3>
	</div><!-- block-title-wrap -->
	<ul class="list-2-cols list-unstyled">
		<?php
        foreach( $additional_features as $ad_del ):

            $feature_title = isset( $ad_del['fave_additional_feature_title'] ) ? $ad_del['fave_additional_feature_title'] : '';
            $feature_value = isset( $ad_del['fave_additional_feature_value'] ) ? $ad_del['fave_additional_feature_value'] : '';

            echo '<li><strong>'.esc_attr( $feature_title ).':</strong> <span>'.esc_attr( $feature_value ).'</span></li>';
        endforeach;
        ?>
	</ul>	
<?php } ?>