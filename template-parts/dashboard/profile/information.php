<?php
$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
$is_worldWide = false;
if( isset( $details->country ) && $details->country != 'SA' ){
    $is_worldWide = true;
}

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
$roles = array('houzez_agent' => 'houzez_agent', 'houzez_agency' => 'houzez_agency', 'houzez_owner' => 'houzez_owner');
$class = 'style="display:none;"';
$readonly = '';
$is_verify = get_user_meta( $userID, 'aqar_phone_confirm', true );
if( $is_verify ) {
    $readonly = 'readonly';
}
/*
if( ! $is_worldWide ) {
    $class = 'style="display:none;"';
    foreach ( $roles as $role ) { 
        if( in_array($role, $current_user->roles ) ) {
            if( empty( $brokerage_license_number ) ){
                echo 'يرجي تعبئة الخانات التالية : رقم رخصة الوساطة العقارية ';
                echo '<br>';
            }
            if( empty( $id_number ) ){
                echo 'يرجي تعبئة الخانات التالية : رقم الهوية ';
                echo '<br>';
            }
            if( empty( $tax_number ) ){
                echo 'يرجي تعبئة الخانات التالية : رقم السجل التجاري';
                echo '<br>';
            }
        }
    }
}
*/

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
          <a href="<?php echo esc_url($packages_page_link); ?>" target="_blank" class="btn btn-warning mb-4 text-center w-100" style="display:none;">   
            شراء عضوية
        </a>
            <?php get_template_part('template-parts/dashboard/profile/photo'); ?>
        </div><!-- col-md-3 col-sm-12 -->

        <div class="col-md-9 col-sm-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="username"><?php esc_html_e('Username','houzez');?></label>
                        <input disabled type="text" name="username" class="form-control"
                            value="<?php echo esc_attr( $username );?>">
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="useremail"><?php esc_html_e('Email','houzez');?></label>
                        <input type="text" name="useremail" class="form-control"
                            value="<?php echo esc_attr( $user_email );?>" readonly>
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

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="display_name"><?php esc_html_e('Select Your Public Name', 'houzez'); ?></label>
                        <select name="display_name" class="selectpicker form-control" id="display_name"
                            data-live-search="false">
                            <?php
                                $public_display = array();
                                $public_display['display_username']  = $current_user->user_login;
                                $public_display['display_nickname']  = $current_user->nickname;
                                
                                if(!empty($current_user->first_name)) {
                                    $public_display['display_firstname'] = $current_user->first_name;
                                }
                                
                                if(!empty($current_user->last_name)) {
                                    $public_display['display_lastname'] = $current_user->last_name;
                                }
                                
                                if(!empty($current_user->first_name) && !empty($current_user->last_name) ) {
                                    $public_display['display_firstlast'] = $current_user->first_name . ' ' . $current_user->last_name;
                                    $public_display['display_lastfirst'] = $current_user->last_name . ' ' . $current_user->first_name;
                                }
                                
                                if(!in_array( $current_user->display_name, $public_display)) {
                                    $public_display = array( 'display_displayname' => $current_user->display_name ) + $public_display;
                                    $public_display = array_map( 'trim', $public_display );
                                    $public_display = array_unique( $public_display );
                                }

                                foreach ($public_display as $id => $item) {
                            ?>
                            <option id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($item); ?>"
                                <?php selected( $current_user->display_name, $item ); ?>><?php echo esc_attr($item); ?>
                            </option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <?php if(houzez_not_buyer()) { ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="title"><?php echo esc_attr($title_position_lable); ?></label>
                        <input type="text" name="title" value="<?php echo esc_attr( $user_title );?>"
                            class="form-control"
                            placeholder="<?php esc_html_e('Enter your title or position','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="license"><?php esc_html_e('License','houzez');?></label>
                        <input type="text" name="license" value="<?php echo esc_attr( $license );?>"
                            class="form-control" placeholder="<?php esc_html_e('Enter your license','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="usermobile"><?php esc_html_e('Mobile','houzez');?></label>
                        <input type="text" name="usermobile" class="form-control"
                            value="<?php echo esc_attr( $user_mobile );?>"
                            placeholder="<?php esc_html_e('Enter your mobile phone number','houzez');?>" <?php echo $readonly; ?>>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12" style="display: none;">
                    <div class="form-group">
                        <label for="whatsapp"><?php esc_html_e('WhatsApp','houzez');?></label>
                        <input type="text" name="whatsapp" class="form-control"
                            value="<?php echo esc_attr( $user_whatsapp );?>"
                            placeholder="<?php esc_html_e('Enter your whatsapp number with country code','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="tax_number"><?php esc_html_e('Tax Number','houzez');?></label>
                        <input type="text" name="tax_number" value="<?php echo esc_attr( $tax_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('Enter your tax number','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12" style="display:none;">
                    <div class="form-group">
                        <label for="type_id">
                            <?php esc_html_e('نوع المعلن','houzez');?>
                        </label>
                        <select name="aqar_author_type_id" data-size="5" id="aqar_author_type_id"
                            class="selectpicker form-control" title="يرجى الاختيار">
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
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم الهوية','houzez');?>">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="brokerage_license_number"><?php esc_html_e('رقم رخصة الوساطة العقارية','houzez');?></label>
                        <input type="text" name="brokerage_license_number" value="<?php echo esc_attr( $brokerage_license_number );?>"
                            class="form-control" placeholder="<?php esc_html_e('يرجي ادخال رقم رخصة الوساطة العقارية','houzez');?>">
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
                        <label for="userphone"><?php esc_html_e('Phone','houzez');?></label>
                        <input type="text" name="userphone" class="form-control"
                            value="<?php echo esc_attr( $user_phone );?>"
                            placeholder="<?php esc_html_e('Enter your phone number','houzez');?>" readonly>
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
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="user_company"><?php esc_html_e('Company Name','houzez');?></label>
                        <input type="text" name="user_company"
                            placeholder="<?php esc_html_e('Enter your company name','houzez');?>" class="form-control"
                            value="<?php echo esc_attr( $user_company );?>">
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-sm-6 col-xs-12">
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
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="about"><?php echo esc_attr($about_lable); ?></label>
                        <?php
                        $editor_id = 'about';
                        $settings = array(
                            'media_buttons' => false,
                            'textarea_rows' => 6,
                        );
                        if ( !empty($description) ) {
                            wp_editor($description, $editor_id, $settings);
                        } else {
                            wp_editor('', $editor_id, $settings);
                        }
                        ?>
                    </div>
                </div>
            </div><!-- row -->
            <button class="houzez_update_profile btn btn-success">
                <?php get_template_part('template-parts/loader'); ?>
                <?php esc_html_e('Update Profile', 'houzez'); ?>
            </button><br />
            <div class="notify"></div>

        </div><!-- col-md-9 col-sm-12 -->
    </div><!-- row -->
</div><!-- dashboard-content-block -->