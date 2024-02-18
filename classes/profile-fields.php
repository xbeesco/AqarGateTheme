<?php 
$type_id =   get_the_author_meta( 'aqar_author_type_id' , $user->ID ); 
?>
<style>
    .profile-wrap{
        background: #fff;
        padding: 1rem;
        border-collapse: unset;
        border: 1px solid #ebeaea;
        box-shadow: 0px 0px 2px 0px #00000014;
        margin: 1rem 0 ;
    }
</style>
<div class="profile-wrap">
    <h2><?php echo esc_html__('Aqar Gate User Custom Profile Fields', 'ag'); ?></h2>
    <table class="form-table">
        <tbody>
            <tr class="user-aqar_author_id_number-wrap">
                <th><label for="aqar_author_id_number"><?php echo esc_html__('رقم الهوية', 'houzez'); ?></label></th>
                <td><input type="text" name="aqar_author_id_number" id="aqar_author_id_number"
                        value="<?php echo get_the_author_meta('aqar_author_id_number', $user->ID); ?>" class="regular-text">
                </td>
            </tr>
            <?php if( $user->roles[0] === 'houzez_agency' ) { ?>
            <tr>
                <th><label for="aqar_author_unified_number"><?php esc_html_e('الرقم الموحد للمنشأة ( 700 )','houzez');?></label></th>
                <td><input type="text" name="aqar_author_unified_number" id="aqar_author_unified_number"
                        value="<?php echo get_the_author_meta('aqar_author_unified_number', $user->ID); ?>" class="regular-text">
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th><label for="brokerage_license_number"><?php esc_html_e('رقم رخصة الوساطة ( فال )','houzez');?></label></th>
                <td><input type="text" name="brokerage_license_number" id="brokerage_license_number"
                        value="<?php echo get_the_author_meta('brokerage_license_number', $user->ID); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="license_expiration_date"><?php esc_html_e('تاريخ انتهاء الرخصة	','houzez');?></label></th>
                <td><input type="date" name="license_expiration_date" id="license_expiration_date"
                        value="<?php echo get_the_author_meta('license_expiration_date', $user->ID); ?>" class="regular-text">
                </td>
            </tr> 
            <tr>
                <th class="form-group">
                    <label for="aqar_author_type_id">
                        <?php esc_html_e('نوع المعلن','houzez');?>
                    </label>
                </th><!-- form-group -->
                <td>
                    <select name="aqar_author_type_id" data-size="5" id="aqar_author_type_id"
                        class="selectpicker form-control regular-text" title="يرجى الاختيار">
                        <option value="" disabled selected>يرجى الاختيار</option>
                        <option <?php echo selected($type_id, '1', false); ?> value="1">مسوق عقاري / مالك</option>
                                <option <?php echo selected($type_id, '2', false); ?> value="2">شركة / مؤسسة / مكتب عقاري</option>
                    </select>
                </td>
    
            </tr>
        </tbody>
    </table>
</div>