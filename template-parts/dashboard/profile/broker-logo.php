<?php
global $houzez_local;
$userID = get_current_user_id();

$user_custom_logo    =   get_the_author_meta( 'fave_author_custom_logo' , $userID );
$author_logo_id      =   get_the_author_meta( 'fave_author_logo_id' , $userID );
$user_default_currency  =   get_the_author_meta( 'fave_author_currency' , $userID );
if($user_custom_logo =='' ) {
    $user_custom_logo = HOUZEZ_IMAGE. 'profile-avatar.png';
}
?>

<div id="aqar_profile_logo" class="profile-image">
<?php
if( !empty( $author_logo_id ) ) {
    $author_logo_id = intval( $author_logo_id );
    if ( $author_logo_id ) {
        echo wp_get_attachment_image( $author_logo_id, 'large', "", array( "class" => "img-fluid" ) );
        echo '<input type="hidden" class="profile-logo-id" id="profile-logo-id" name="profile-logo-id" value="' . esc_attr( $author_logo_id ).'"/>';
    }
} else {
    print '<img class="img-fluid" id="profile-image" src="'.esc_url( $user_custom_logo ).'" alt="user image" >';
}
?>
</div>
<button id="select_user_profile_logo" type="button" class="btn btn-primary btn-full-width mt-3">
	<?php echo esc_html__('تحديث الشعار', 'houzez'); ?>
</button>
<small class="form-text text-muted text-center"><?php echo esc_html__('Minimum size 300 x 300 px', 'houzez'); ?></small>
<div id="upload_errors"></div>