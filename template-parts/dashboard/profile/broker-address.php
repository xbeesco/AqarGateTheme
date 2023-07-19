<?php
global $current_user, $houzez_local;
$userID = get_current_user_id();

$state             = get_the_author_meta( 'aqar_state' , $userID );
$city              = get_the_author_meta( 'aqar_city' , $userID );
$area              = get_the_author_meta( 'aqar_area' , $userID );
$street            = get_the_author_meta( 'aqar_street' , $userID );
$zip               = get_the_author_meta( 'aqar_zip' , $userID );
$building_number   = get_the_author_meta( 'aqar_building_number' , $userID );
$additional_number = get_the_author_meta( 'aqar_additional_number' , $userID );
$Shortcode         = get_the_author_meta( 'aqar_Shortcode' , $userID );
$user_address      =   get_the_author_meta( 'fave_author_address' , $userID );

$is_valid_ad = get_the_author_meta( 'is_valid_ad' , $userID );
$readonly = '';
if( $is_valid_ad == 'is_valid' ) {
    $readonly = 'readonly';
}
if( houzez_is_agency() ) {
    $title_position_lable = esc_html__('Agency Name','houzez');
    $about_lable = esc_html__( 'About Agency', 'houzez' );
} else {
    $title_position_lable =  esc_html__('Title / Position','houzez');
    $about_lable = esc_html__( 'About me', 'houzez' );
}

?>
<div class="dashboard-content-block" style="background: #f1f1f1;">
    <div class="row">
        <div class="col-md-3 col-sm-12">
            <h2><?php esc_html_e('عنوان الوسيط','houzez');?></h2>
        </div><!-- col-md-3 col-sm-12 -->

        <div class="col-md-9 col-sm-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'State', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_state" value="<?php echo $state; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'City', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_city" value="<?php echo $city; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'Area', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_area" value="<?php echo $area; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'Streat Address', 'houzez' ); ?></label>
                        <input class="form-control" name="user_address" value="<?php echo $user_address; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'Zip/Postal Code', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_zip" value="<?php echo $zip; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'رقم المبني', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_building_number" value="<?php echo $building_number; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'الرقم الإضافي', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_additional_number" value="<?php echo $additional_number; ?>" placeholder="" type="text">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label><?php esc_html_e( 'الرقم المختصر', 'houzez' ); ?></label>
                        <input class="form-control" name="aqar_Shortcode" value="<?php echo $Shortcode; ?>" placeholder="" type="text">
                    </div>
                </div>
            </div><!-- row -->

            <button class="houzez_update_profile btn btn-success">
                <?php get_template_part('template-parts/loader'); ?>
                <?php esc_html_e('Update Profile', 'houzez'); ?>
            </button><br/>
            <div class="notify"></div>
        </div><!-- col-md-9 col-sm-12 -->
    </div><!-- row -->
</div><!-- dashboard-content-block -->