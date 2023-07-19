<?php
// $ip = $_SERVER['REMOTE_ADDR'];
// $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
$is_worldWide = false;
// if( isset( $details->country ) && $details->country != 'SA' ){
// 	$is_worldWide = true;
// }
global $prop_meta_data, $hide_prop_fields, $required_fields, $is_multi_steps, $area_prefix_default, $area_prefix_changeable;
$area_prefix_default = houzez_option('area_prefix_default');
$area_prefix_changeable = houzez_option('area_prefix_changeable');
$auto_property_id = houzez_option('auto_property_id');

if( $area_prefix_default == 'SqFt' ) {
    $area_prefix_default = houzez_option('measurement_unit_sqft_text');
} elseif( $area_prefix_default == 'm²' ) {
    $area_prefix_default = houzez_option('measurement_unit_square_meter_text');
}

$adp_details_fields = houzez_option('adp_details_fields');
$fields_builder = $adp_details_fields['enabled'];
unset($fields_builder['placebo']);
// only work on edit property page . 
if ( isset($_GET) && !empty( $_GET['edit_property'] ) ) {
	$propertyType = wp_get_post_terms( $_GET['edit_property'], 'property_type' );
    $ag_fields = carbon_get_term_meta($propertyType[0]->term_id, 'crb_available_fields');
    if (!empty($ag_fields)) {
        $fields_builder = array_flip($ag_fields);
    }
	
}

 if( get_option( '_aq_show_api' ) == 'yes' ) {
	// unset($fields_builder['d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1']);
	// unset($fields_builder['d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9']);
	// unset($fields_builder['d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985']);
	// unset($fields_builder['area-size']);
	// unset($fields_builder['land-area']);
	// unset($fields_builder['d8b1d982d985-d8a7d984d985d8aed8b7d8b7']);

 }

if( $is_worldWide ){
	unset($fields_builder['d987d984-d98ad988d8acd8af-d8a7d984d8b1d987d986-d8a3d988-d8a7d984d982d98ad8af-d8a7d984d8b0d98a-d98ad985d986d8b9-d8a7d988-d98ad8add8af']);
	unset($fields_builder['d8a7d984d986d8b2d8a7d8b9d8a7d8aa-d8a7d984d982d8a7d8a6d985d8a9-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1']);
	unset($fields_builder['d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985']);
	unset($fields_builder['d8a7d984d985d8b9d984d988d985d8a7d8aa-d8a7d984d8aad98a-d982d8af-d8aad8a4d8abd8b1-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8b3d988d8a7d8a1']);
	unset($fields_builder['d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9']);
	unset($fields_builder['d8b1d982d985-d8b9d982d8af-d8a7d984d988d8b3d8a7d8b7d8a9-d8a7d984d8b9d982d8a7d8b1d98ad8a9']);
	unset($fields_builder['d8b1d982d985-d8a7d984d8aad981d988d98ad8b6']);
}
unset($fields_builder['d986d988d8b9-d8a7d984d8a5d8b9d984d8a7d986-d8a7d984d8b1d8a6d98ad8b3d98a']);
?>
<div id="details" class="dashboard-content-block-wrap <?php echo esc_attr($is_multi_steps);?>">
	<h2><?php echo houzez_option('cls_details', 'Details'); ?></h2>
	<div id="aqar-details" class="dashboard-content-block">
		
		<div class="row">
			<?php
			$wide_field = array(
				'd987d984-d98ad988d8acd8af-d8a7d984d8b1d987d986-d8a3d988-d8a7d984d982d98ad8af-d8a7d984d8b0d98a-d98ad985d986d8b9-d8a7d988-d98ad8add8af',
				'd8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985',
				'd8a7d984d985d8b9d984d988d985d8a7d8aa-d8a7d984d8aad98a-d982d8af-d8aad8a4d8abd8b1-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8b3d988d8a7d8a1',
				'd8a7d984d986d8b2d8a7d8b9d8a7d8aa-d8a7d984d982d8a7d8a6d985d8a9-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1',
			);
			if ($fields_builder) {
				foreach ($fields_builder as $key => $value) {
					// prr($key);
					if(in_array($key, $wide_field)){
						$class = 'col-md-12';
					}else{
						$class = 'col-md-4';
					}

					if(in_array($key, houzez_details_section_fields())) { 

						if( $key == 'property-id' ) {

							if( $auto_property_id != 1 ) {
								echo '<div class="col-md-4 col-sm-12">';
									get_template_part('template-parts/dashboard/submit/form-fields/'.$key); 
								echo '</div>';
							}

						} else {
							echo '<div class="col-md-4 col-sm-12">';
								get_template_part('template-parts/dashboard/submit/form-fields/'.$key); 
							echo '</div>';
						}
						

					} else {

						echo '<div class="'. $class .' col-sm-12">';
						    do_action('get_custom_add_listing_field', $key);
							// houzez_get_custom_add_listing_field($key);
						echo '</div>';
					}
				}
			}
			?>
			
			
		</div><!-- row -->
	</div><!-- dashboard-content-block -->

	<?php if( $hide_prop_fields['additional_details'] != 1 ) { ?>
	<h2><?php echo houzez_option('cls_additional_details', 'Additional details'); ?></h2>
	<div class="dashboard-content-block">
		<?php get_template_part('template-parts/dashboard/submit/form-fields/additional-details'); ?>
	</div><!-- dashboard-content-block -->
	<?php } ?>
</div><!-- dashboard-content-block-wrap -->
