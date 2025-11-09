<?php
// $ip = $_SERVER['REMOTE_ADDR'];
// $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
// $is_worldWide = false;
// if( isset( $details->country ) && $details->country != 'SA' ){
//     $is_worldWide = true;
// }

global $current_user, $houzez_local;
$userID = get_current_user_id();

$username               =   get_the_author_meta( 'user_login' , $userID );
$user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
$display_name           =   get_the_author_meta( 'aqar_display_name' , $userID );
if( empty($display_name) ) {
    $display_name = $current_user->display_name;
}
if( empty($user_title) ){
    $user_title = $display_name;
}
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
$unified_number         =   get_the_author_meta( 'aqar_author_unified_number' , $userID );
if( houzez_is_agency() ){
    $type_id = 2;
} 
$brokerage_license_number = get_the_author_meta( 'brokerage_license_number' , $userID );
$license_expiration_date  = get_the_author_meta( 'license_expiration_date' , $userID );
$validateDateTime = strtotime($license_expiration_date);
$license_expiration_date  = (!empty($license_expiration_date) && $validateDateTime ) ? date( "Y-m-d", strtotime( $license_expiration_date ) ) : '';

$roles = array('houzez_agent' => 'houzez_agent', 'houzez_agency' => 'houzez_agency', 'houzez_owner' => 'houzez_owner');
$class = 'style="display:none;"';
$readonly = '';
if( empty($user_email) ||
    empty($user_mobile) ||
    empty($brokerage_license_number) ||
    ( empty($unified_number ) && houzez_is_agency() ) ||
    empty($id_number ) 
  ){ 
    $is_verify = false;
    update_user_meta( $userID, 'aqar_is_verify_user', 0 );
  }else{
    $is_verify = true;
    update_user_meta( $userID, 'aqar_is_verify_user', 1 );
  }
if( $is_verify ) {
    $readonly = '';
}

if( ! houzez_is_agency() && ! houzez_is_agent() ) {
    $is_verify = true;
    update_user_meta( $userID, 'aqar_is_verify_user', 1 );
}
$is_verify = true;
    //  var_dump($userID);
    $class = 'style="display:none;"';
    $msg = [];
    foreach ( $roles as $role ) { 
        if( in_array($role, $current_user->roles ) ) {
            if( empty( $brokerage_license_number ) ){
                $msg[] = 'يرجي تعبئة الخانات التالية : رقم رخصة الوساطة العقارية <br>';
            }
            if( empty( $id_number ) ){
                $msg[] = 'يرجي تعبئة الخانات التالية : رقم الهوية <br>';
            }
            if( empty( $user_email ) ){
                $msg[] = 'يرجي تعبئة الخانات التالية :  البريد الإلكتروني <br>';
            }
            if( empty( $user_mobile ) ){
                $msg[] = 'يرجي تعبئة الخانات التالية :  رقم الجوال <br>';
            }
            if( empty( $unified_number ) ){
                $msg[] = 'يرجي تعبئة الخانات التالية : الرقم الموحد للمنشأة <br>';
            }
        }
    }



if( houzez_is_agency() ) {
    $title_position_lable = esc_html__('Agency Name','houzez');
    $about_lable = esc_html__( 'About Agency', 'houzez' );
} else {
    $title_position_lable =  esc_html__('Title / Position','houzez');
    $about_lable = esc_html__( 'About me', 'houzez' );
}

$packages_page_link = houzez_get_template_link('template/template-packages.php'); 
?>

<h2><?php esc_html_e( 'Information', 'houzez' ); ?></h2>

<div class="dashboard-content-block">
    <div class="row">
        <div class="col-md-3 col-sm-12">
          
        </div><!-- col-md-3 col-sm-12 -->

        <div class="col-md-9 col-sm-12">
        <?php if( ! $is_verify ) : ?>
        <div id="errors-messages" class="validate-errors alert alert-danger" role="alert">
            <strong id="messages">
                <?php foreach ($msg as $error) { echo $error; } ; ?>
            </strong> 
        </div>
        <?php endif; ?>
            <div class="row">
            <?php $col_class = 'col-md-6'; if( houzez_is_agency() ): $col_class = 'col-md-6'; endif; ?>
                <div class="<?php echo $col_class; ?> col-sm-12">
                    <div class="form-group">
                        <label for="username"><?php esc_html_e('Username','houzez');?></label>
                        <input disabled type="text" name="username" class="form-control"
                            value="<?php echo esc_attr( $username );?>">
                    </div>
                </div>

                

                <?php if( !houzez_is_agency() ): ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="firstname"><?php esc_html_e('First Name','houzez');?></label>
                        <input type="text" name="firstname" class="form-control"
                            value="<?php echo esc_attr( $first_name );?>"
                            placeholder="<?php esc_html_e('Enter your first name','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="lastname"><?php esc_html_e('Last Name','houzez');?></label>
                        <input type="text" name="lastname" class="form-control"
                            value="<?php echo esc_attr( $last_name );?>"
                            placeholder="<?php esc_html_e('Enter your last name','houzez');?>">
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <?php if( houzez_is_agency() ){ ?>
                        <label for="title"><?php esc_html_e('اسم المؤسسة / الشركة', 'houzez'); ?></label>
                        <?php } else { ?>
                        <label for="title"><?php esc_html_e('الاسم بالكامل', 'houzez'); ?></label>
                        <?php } ?>
                        <input type="text" name="title" class="form-control" id="title" value=" <?php echo $user_title; ?>">
                    </div>
                </div>
                
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="id_number"><?php esc_html_e('رقم الهوية','houzez');?></label>
                        <input type="text" name="id_number" value="<?php echo esc_attr( $id_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم الهوية','houzez');?>" readonly>
                    </div>
                </div>
                <?php if( houzez_is_agency() ): ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="unified_number-number"><?php esc_html_e('الرقم الموحد للمنشأة ( 700 )','houzez');?></label>
                        <input type="text" name="unified_number" value="<?php echo esc_attr( $unified_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال الرقم الموحد','houzez');?>">
                    </div>
                </div>
                <?php endif; ?>
                <?php if( houzez_is_agency() || houzez_is_agent() ): ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="type_id">
                            <?php esc_html_e('نوع الوسيط','houzez');?>
                        </label>
                        <input type="hidden" name="aqar_author_type_id" value="<?php echo $type_id; ?>">
                        <select name="aqar_author_type_id" data-size="5" id="aqar_author_type_id"
                            class="selectpicker form-control" title="يرجى الاختيار">
                            <option <?php echo selected($type_id, '1', false); ?> value="1">مسوق عقاري فرد</option>
                            <option <?php echo selected($type_id, '2', false); ?> value="2">منشأة عقارية</option>
                        </select>
                    </div><!-- form-group --> 
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="brokerage_license_number"><?php esc_html_e('رقم رخصة الوساطة العقارية ( فال )','houzez');?></label>
                        <input type="text" name="brokerage_license_number" value="<?php echo esc_attr( $brokerage_license_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم رخصة الوساطة العقارية','houzez');?>">
                    </div>
                </div> 
                <div class="col-sm-6 col-xs-12"> 
                    <div class="form-group">
                        <label for="license_expiration_date"><?php esc_html_e('تاريخ انتهاء الرخصة','houzez');?></label>
                        <input type="date" name="license_expiration_date" value="<?php echo $license_expiration_date ; ?>"
                            class="form-control" placeholder="">
                    </div> 
                </div>
                <?php endif; ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="tax_number"><?php esc_html_e('Tax Number','houzez');?></label>
                        <input type="text" name="tax_number" value="<?php echo esc_attr( $tax_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('Enter your tax number','houzez');?>">
                    </div>
                </div>
                <?php if(houzez_not_buyer()) { ?>
                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="license"><?php esc_html_e('License','houzez');?></label>
                        <input type="text" name="license" value="<?php echo esc_attr( $license );?>"
                            class="form-control" placeholder="<?php esc_html_e('Enter your license','houzez');?>">
                    </div>
                </div>
                
                
                
                

                <div class="col-sm-6 col-xs-12" style="display:none;">
                    <div class="form-group">
                        <label for="ad_number"><?php esc_html_e('رقم المعلن','houzez');?></label>
                        <input type="text" name="ad_number" value="<?php echo esc_attr( $ad_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم المعلن','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="fax_number"><?php esc_html_e('Fax Number','houzez');?></label>
                        <input type="text" name="fax_number" class="form-control"
                            value="<?php echo esc_attr( $fax_number );?>"
                            placeholder="<?php esc_html_e('Enter your fax number','houzez');?>">
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="userlangs"><?php esc_html_e('Language','houzez');?></label>
                        <input type="text" name="userlangs"
                            placeholder="<?php echo esc_html__('English, Spanish, French', 'houzez'); ?>"
                            class="form-control" value="<?php echo esc_attr( $userlangs );?>">
                    </div>
                </div>

                <?php if( !houzez_is_agency() ): ?>
                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="user_company"><?php esc_html_e('Company Name','houzez');?></label>
                        <input type="text" name="user_company"
                            placeholder="<?php esc_html_e('Enter your company name','houzez');?>" class="form-control"
                            value="<?php echo esc_attr( $user_company );?>">
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="about"><?php esc_html_e( 'Address', 'houzez' ); ?></label>
                        <input type="text" name="user_address" class="form-control"
                            value="<?php echo esc_attr( $user_address );?>"
                            placeholder="<?php esc_html_e('Enter your address','houzez');?>">
                    </div>
                </div>

                <div class="col-sm-12 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="service_areas"><?php esc_html_e( 'Service Areas', 'houzez' ); ?></label>
                        <input type="text" name="service_areas" class="form-control"
                            value="<?php echo esc_attr( $service_areas );?>"
                            placeholder="<?php esc_html_e('Enter your service areas','houzez');?>">
                    </div>
                </div>

                <div class="col-sm-12 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="specialties"><?php esc_html_e( 'Specialties', 'houzez' ); ?></label>
                        <input type="text" name="specialties" class="form-control"
                            value="<?php echo esc_attr( $specialties );?>"
                            placeholder="<?php esc_html_e('Enter your specialties','houzez');?>">
                    </div>
                </div>

                <?php } ?>
            </div><!-- row -->
            <button class="houzez_update_profile btn btn-success">
                <?php get_template_part('template-parts/loader'); ?>
                <?php esc_html_e('Update Profile', 'houzez'); ?>
            </button><br />
            <div class="notify"></div>

        </div><!-- col-md-9 col-sm-12 -->
    </div><!-- row -->
</div><!-- dashboard-content-block -->