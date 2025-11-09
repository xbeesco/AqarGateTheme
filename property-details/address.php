<?php
global $post;

$google_map_address     = get_post_meta($post->ID, 'fave_property_location', true);
$google_map_lat         = get_post_meta($post->ID, 'houzez_geolocation_lat', true);
$google_map_lng         = get_post_meta($post->ID, 'houzez_geolocation_long', true);
$google_map_address_url = "https://maps.google.com/?q=".$google_map_lat.','.$google_map_lng;

// $google_map_address = houzez_get_listing_data('property_map_address');
// $google_map_address_url = "http://maps.google.com/?q=".$google_map_address;
$advertisement_response = get_post_meta( get_the_ID(), 'advertisement_response', true );
if (is_string($advertisement_response)) {
    $advertisement_response = json_decode($advertisement_response, true);
} elseif ($advertisement_response instanceof stdClass) {
    $advertisement_response = (array) $advertisement_response;  // Convert stdClass to array
}
?>
<div class="property-address-wrap property-section-wrap" id="property-address-wrap">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('sps_address', 'Address'); ?></h2>
			<?php if( !empty($google_map_address) ) { ?>
			<a class="btn btn-primary btn-slim" href="<?php echo esc_url($google_map_address_url); ?>" target="_blank"><i class="houzez-icon icon-maps mr-1"></i> <?php echo houzez_option('spl_ogm', 'Open on Google Maps' ); ?></a>
			<?php } ?>

		</div><!-- block-title-wrap -->
		<div class="block-content-wrap">
		
			<p><strong class="text-primary">وصف موقع العقار حسب الصك : </strong><?php echo $advertisement_response['locationDescriptionOnMOJDeed'] ?? ''; ?></p>
			<ul class="<?php echo houzez_option('prop_address_cols', 'list-2-cols'); ?> list-unstyled">
				<?php get_template_part('property-details/partials/address-data'); ?>
			</ul>	
		</div><!-- block-content-wrap -->
		<div style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 15px; margin: 20px 0;">
			<p style="color: #856404; margin: 0; text-align: center; font-size: 16px;">
				<i class="fa fa-info-circle" style="margin-left: 10px;"></i>
				<strong>نأمل مطابقة الموقع في أدناه مع الموقع المذكور في وصف موقع العقار المكتوب</strong>
			</p>
		</div>
		<?php if(houzez_map_in_section() && houzez_get_listing_data('property_map')) { ?>
		<div id="houzez-single-listing-map" class="block-map-wrap">
		</div><!-- block-map-wrap -->
		<?php } ?>

	</div><!-- block-wrap -->
</div><!-- property-address-wrap -->
<?php
// Hook لإضافة زر الدفع بعد الخريطة والعنوان
do_action('houzez_single_property_after_address');
?>