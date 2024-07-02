<?php 
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
?>
<div class="aqar-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
    <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>"
        width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>

    <form id="aqargate-functionality-form" class="form-inline">
        <h4 class="m-2">اضافة المناطق والمدن والاحياء</h4>
        <p>يتم الاضافة بالترتيب السليم المناطق ثم المدن ثم الاحياء</p>
        <div class="form-group m-5 row align-items-center">
            <div class="col-md-5">
                <select name="location_type" id="location_type" class="form-control mr-2">
                    <option value="REGION">Region</option>
                    <option value="CITY">City</option>
                    <option value="DISTRICT">District</option>
                </select>
            </div>
            <div class="col-md-5">
                <select name="start_file" id="start_file" class="form-control mr-2">
                    <!-- Options will be populated dynamically based on the location type -->
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary mb-2">اضافة</button>
            </div>
        </div>
        <div id="loading" style="display: none;">
            <div class="svg-loader">
                <svg class="svg-container" height="50" width="50" viewBox="0 0 100 100">
                    <circle class="loader-svg bg" cx="50" cy="50" r="45"></circle>
                    <circle class="loader-svg animate" cx="50" cy="50" r="45"></circle>
                </svg>
            </div>
        </div>
        <div class="progress mt-3">
            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </form>
    <div id="response" class="mt-3"><pre></pre></div>
</div>
