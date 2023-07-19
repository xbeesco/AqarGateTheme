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
if( houzez_is_agency() ) {
    $title_position_lable = esc_html__('Agency Name','houzez');
    $about_lable = esc_html__( 'About Agency', 'houzez' );
} else {
    $title_position_lable =  esc_html__('Title / Position','houzez');
    $about_lable = esc_html__( 'About me', 'houzez' );
}

?>
<h2><?php esc_html_e('نبذه  عن الوسيط','houzez');?></h2>
<div class="dashboard-content-block" style="background: #f1f1f1;">
    <div class="row">
        <div class="col-md-3 col-sm-12">         
            <a href="<?php echo esc_url($packages_page_link); ?>" target="_blank" class="btn btn-warning mb-4 text-center w-100" style="display:none;">   
                شراء عضوية
            </a>
            <?php get_template_part('template-parts/dashboard/profile/photo'); ?>
        </div><!-- col-md-3 col-sm-12 -->

        <div class="col-md-9 col-sm-12">
            <div class="row">
            <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="about"><?php echo esc_attr($about_lable); ?></label>
                        <?php
                        $editor_id = 'about';
                        $settings = array(
                            // 'media_buttons' => false,
                            // 'textarea_rows' => 6,
                            // 'tinymce' => true,
                            // 'quicktags' => true
                        );
                        $args = array(
                            'media_buttons' => false,
                            'tinymce'       => array(
                                'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            ),
                        );
                        if ( !empty($description) ) {
                            wp_editor($description, $editor_id, $args);
                        } else {
                            wp_editor('', $editor_id, $args);
                        }
                        ?>
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