<?php
global $is_multi_steps, $hide_prop_fields;
$houzez_map_system = houzez_get_map_system();
$default_lat = houzez_option('map_default_lat', 25.686540);
$default_long = houzez_option('map_default_long', -80.431345);
if (houzez_edit_property()) {
    $lat_lng = houzez_get_field_meta('property_location');
    $lat_lng = explode(",", $lat_lng);

    if(!empty($lat_lng[0])) {
    	$default_lat = $lat_lng[0];
    	$default_long = $lat_lng[1];
    }
    
}
?>
<div id="location" class="dashboard-content-block-wrap">
	<div class="col-md-12 col-sm-12">
		<div class="row">
		  	<div class="col-md-4 col-sm-12">
				<?php 
				$state = '';
				if (houzez_edit_property()) {
					global $property_data;

					$state = houzez_get_post_term_slug($property_data->ID, 'property_state');
				}
				?>
				<div class="form-group">
					<label for="administrative_area_level_1"><?php echo houzez_option('cl_state', 'County/State'); ?><span class="required-field">*</span></label>
						<select name="location[region]" data-state="<?php echo urldecode($state); ?>" data-target="houzezThirdList" <?php houzez_required_field_2('state'); ?> id="countyState" class="houzezSelectFilter houzezSecondList selectpicker form-control bs-select-hidden" data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true" required>
							<?php
							if (houzez_edit_property()) {
								global $property_data;
								houzez_taxonomy_edit_hirarchical_options_for_search( $property_data->ID, 'property_state');

							} else {
							
							echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';               
							$property_state_terms = get_terms (
								array(
									"property_state"
								),
								array(
									'orderby' => 'name',
									'order' => 'ASC',
									'hide_empty' => false,
									'parent' => 0
								)
							);

							houzez_hirarchical_options( 'property_state', $property_state_terms, -1);
							}
							?>
						</select>
				</div>
			</div>
			
			
			<div class="col-md-4 col-sm-12">
			<?php 
				global $required_fields; 
				$city = '';
				if (houzez_edit_property()) {
					global $property_data;

					$city = houzez_get_post_term_slug($property_data->ID, 'property_city');
				}
				?>
				<div class="form-group">
					<label for="location[city]"><?php echo houzez_option( 'cl_city', 'City' ); ?><span class="required-field">*</span></label>
						<select name="location[city]" id="city" data-city="<?php echo urldecode($city); ?>" data-target="houzezFourthList" required class="houzezSelectFilter houzezThirdList selectpicker form-control bs-select-hidden"  data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true">
							<?php
							if (houzez_edit_property()) {
								global $property_data;
								houzez_taxonomy_edit_hirarchical_options_for_search( $property_data->ID, 'property_city');

							} else {
							
							echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';                
							$property_city_terms = get_terms (
								array(
									"property_city"
								),
								array(
									'orderby' => 'name',
									'order' => 'ASC',
									'hide_empty' => false,
									'parent' => 0
								)
							);

							houzez_hirarchical_options( 'property_city', $property_city_terms, -1);
							}
							?>
						</select>
				</div>
			</div>
			
			
			<div class="col-md-4 col-sm-12">
			<?php
				$area = '';
				if (houzez_edit_property()) {
					global $property_data;

					$area = houzez_get_post_term_slug($property_data->ID, 'property_area');
				}
				?>
				<div class="form-group">
					<label for="location[district]"><?php echo houzez_option( 'cl_area', 'Area' ); ?><span class="required-field">*</span></label>
						<select name="location[district]" data-area="<?php echo urldecode($area); ?>" data-size="5" id="neighborhood" required class=" houzezSelectFilter houzezFourthList selectpicker form-control bs-select-hidden" data-live-search="true" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}">
							<?php
							if (houzez_edit_property()) {
								global $property_data;
								houzez_taxonomy_edit_hirarchical_options_for_search( $property_data->ID, 'property_area');

							} else {
							
							echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';                  
							$property_area_terms = get_terms (
								array(
									"property_area"
								),
								array(
									'orderby' => 'name',
									'order' => 'ASC',
									'hide_empty' => false,
									'parent' => 0
								)
							);

							houzez_hirarchical_options( 'property_area', $property_area_terms, -1);
							}
							?>
						</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="location[street]">الشارع</label>
					<input type="text" class="form-control" name="location[street]" id="street" value="" placeholder="">          
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="location[postalCode]">الرمز البريدي</label>
					<input type="text" class="form-control" name="location[postalCode]" id="postalCode" value="" placeholder="">          
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="location[buildingNumber]">رقم المبني</label>
					<input type="text" class="form-control" name="location[buildingNumber]" id="buildingNumber" value="" placeholder="">          
				<p>يرجي ادخال 4 ارقام </p>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="location[additionalNumber]">الرقم الاضافي</label>
					<input type="text" class="form-control" name="location[additionalNumber]" id="additionalNumber" value="" placeholder="">          
				<p>يرجي ادخال 4 ارقام </p>
				</div>
			</div>
		</div><!-- row -->			
	</div><!-- dashboard-content-block -->
	<div class="col-md-12 col-sm-12">
		<h4><?php echo houzez_option('cls_map', 'Map'); ?></h4>
		<div class="row">
			<div class="col-md-12 col-sm-12 submit-property-map-area">
				<h5 class="mt-3 mb-t text-center text-danger">نامل مطابقة الموقع أدناه مع الموقع المذكور في وصف عنوان العقار المكتوب</h5>
				<div class="form-group dashboard-map-field">
					<label><?php echo houzez_option('cl_drag_drop_text', 'Drag and drop the pin on map to find exact location'); ?></label>

					<div class="map-wrap">
						<div class="map_canvas" id="map_canvas" data-add-lat="<?php echo esc_attr($default_lat); ?>" data-add-long="<?php echo esc_attr($default_long); ?>">
	                    </div>
	                </div>
				</div>
				<button id="find_coordinates" type="button" class="btn btn-primary btn-full-width"><?php echo houzez_option('cl_ppbtn', 'Place the pin in address above'); ?></button>
				<a id="reset" href="#" style="display:none;"><?php esc_html_e('Reset Marker', 'houzez');?></a>
			</div><!-- col-md-6 col-sm-12 -->
			
			<div class="col-md-12 col-sm-12 submit-lat-long">
				<div class="row">
					<div class="form-group col-md-6 col-sm-12">
						<label for="location[latitude]"><?php echo houzez_option( 'cl_latitude', 'Latitude' ); ?><span class="required-field">*</span></label>
						<input class="form-control" id="latitude" name="location[latitude]" value="" placeholder="<?php echo houzez_option('cl_latitude_plac', 'Enter address latitude'); ?>" type="text" required>
					</div>
					
					<div class="form-group col-md-6 col-sm-12">
						<label for="location[longitude]"><?php echo houzez_option( 'cl_longitude', 'Longitude' ); ?><span class="required-field">*</span></label>
						<input class="form-control" id="longitude" name="location[longitude]" value="" placeholder="<?php echo houzez_option('cl_longitude_plac', 'Enter address longitude'); ?>" type="text" required>
					</div>
				</div>
			</div>

		</div><!-- row -->			
	</div><!-- dashboard-content-block -->
</div><!-- dashboard-content-block-wrap -->

