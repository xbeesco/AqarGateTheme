<?php
global $current_user, $houzez_local;
$userID = get_current_user_id();

$username               =   get_the_author_meta( 'user_login' , $userID );
$user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
$first_name             =   get_the_author_meta( 'first_name' , $userID );
$last_name              =   get_the_author_meta( 'last_name' , $userID );
$user_email             =   get_the_author_meta( 'user_email' , $userID );
$user_mobile            =   get_the_author_meta( 'fave_author_mobile' , $userID );
$user_whatsapp          =   get_the_author_meta( 'fave_author_whatsapp' , $userID );
$user_phone             =   get_the_author_meta( 'fave_author_phone' , $userID );
$description            =   get_the_author_meta( 'description' , $userID );
$userlangs              =   get_the_author_meta( 'fave_author_language' , $userID );
$user_company           =   get_the_author_meta( 'fave_author_company' , $userID );
$tax_number             =   get_the_author_meta( 'fave_author_tax_no' , $userID );
$fax_number             =   get_the_author_meta( 'fave_author_fax' , $userID );
$user_address           =   get_the_author_meta( 'fave_author_address' , $userID );
$service_areas          =   get_the_author_meta( 'fave_author_service_areas' , $userID );
$specialties            =   get_the_author_meta( 'fave_author_specialties' , $userID );
$license                =   get_the_author_meta( 'fave_author_license' , $userID );
$gdpr_agreement         =   get_the_author_meta( 'gdpr_agreement' , $userID );
$id_number              =   get_the_author_meta( 'aqar_author_id_number' , $userID );
$ad_number              =   get_the_author_meta( 'aqar_author_ad_number' , $userID );
$type_id                =   get_the_author_meta( 'aqar_author_type_id' , $userID );
$brokerage_license_number = get_the_author_meta( 'brokerage_license_number' , $userID );
$is_valid_ad = get_the_author_meta( 'is_valid_ad' , $userID );
$readonly = '';
if( $is_valid_ad == 'is_valid' ) {
    $readonly = 'readonly';
}

?>
<div class="dashboard-content-block" style="background: #f1f1f1;">
    <div class="row">
        <div class="col-md-3 col-sm-12">
            <h2><?php esc_html_e('حسابات الهيئة السعودية للعقار','houzez');?></h2>
        </div><!-- col-md-3 col-sm-12 -->

        <div class="col-md-9 col-sm-12">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="type_id">
                            <?php esc_html_e('نوع المعلن','houzez');?>
                        </label>
                        <select name="aqar_author_type_id" data-size="5" id="aqar_author_type_id"
                            class="selectpicker form-control" title="يرجى الاختيار" <?php echo $readonly; ?>>
                            <option <?php echo selected($type_id, '1', false); ?> value="1">مواطن</option>
                            <option <?php echo selected($type_id, '2', false); ?> value="2">مقيم</option>
                            <option <?php echo selected($type_id, '3', false); ?> value="3">منشأة</option>
                        </select>
                    </div><!-- form-group -->
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="id_number"><?php esc_html_e('رقم الهوية','houzez');?></label>
                        <input type="text" name="id_number" value="<?php echo esc_attr( $id_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم الهوية','houzez');?>" <?php echo $readonly; ?>>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="brokerage_license_number"><?php esc_html_e('رقم رخصة الوساطة العقارية','houzez');?></label>
                        <input type="text" name="brokerage_license_number" value="<?php echo esc_attr( $brokerage_license_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم رخصة الوساطة العقارية','houzez');?>" <?php echo $readonly; ?>>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="ad_number"><?php esc_html_e('رقم المعلن','houzez');?></label>
                        <input type="text" name="ad_number" value="<?php echo esc_attr( $ad_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم المعلن','houzez');?>" <?php echo $readonly; ?>>
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