<div class="modal-header-ajax">
    <span style="color: #bdb290;font-size: 12px;">انشاء حساب</span>
    <h5 style="margin-bottom: 40px;">مرحبا بك عميلنا العزيز</h5>
</div>
<form>
    <input type="hidden" id="first_name" name="first_name" value="">
    <input type="hidden" id="last_name" name="last_name" value="">
    <input type="hidden" id="role" name="role" value="">
    <input type="hidden" id="transId" name="transId" value="">
    <div class="register-form row">
        <div class="form-group col-md-12 mb-3 col-xs-12">
            <div class="form-group-field username-field">
                <input class="form-control" name="full_name" type="text"
                    placeholder="<?php esc_html_e('full Name','houzez'); ?>" readonly />
            </div><!-- input-group -->
        </div><!-- form-group -->
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label><?php esc_html_e('رقم الهوية','houzez');?></label>
                <input type="text" name="id_number" value="" class="form-control"
                    placeholder="<?php esc_html_e('يرجي ادخال رقم الهوية','houzez');?>" readonly>
            </div>
        </div>

        <div class="form-group col-sm-6 col-xs-12 mb-3">
            <div class="form-group">
                <label for="username"><?php esc_html_e('Username','houzez'); ?></label>
                <input class="form-control" name="username" type="text"
                    placeholder="<?php esc_html_e('Username','houzez'); ?>" />
            </div><!-- input-group -->
        </div><!-- form-group -->


        <div class="form-group col-sm-6 col-xs-12 mb-3">
            <div class="form-group">
                <label for="useremail"><?php esc_html_e('Email','houzez'); ?></label>
                <input class="form-control" name="useremail" type="email"
                    placeholder="<?php esc_html_e('Email','houzez'); ?>" />
            </div><!-- input-group -->
        </div><!-- form-group -->
        <?php if( houzez_option('register_mobile', 0) == 1 ) { ?>
        <div class="form-group col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="phone_number"><?php esc_html_e('Phone','houzez'); ?></label>
                <input class="form-control" name="phone_number" type="number"
                    placeholder="<?php esc_html_e('Phone','houzez'); ?>" />
            </div><!-- input-group -->
        </div><!-- form-group -->
        <?php } ?>

        <?php if( houzez_option('enable_password') == 'yes' ) { ?>
        <div class="form-group col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="register_pass"><?php esc_html_e('Password','houzez'); ?></label>
                <input class="form-control" name="register_pass" placeholder="<?php esc_html_e('Password','houzez'); ?>"
                    type="password" />
            </div><!-- input-group -->
        </div><!-- form-group -->
        <div class="form-group col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="register_pass_retype"><?php esc_html_e('Retype Password','houzez'); ?></label>
                <input class="form-control" name="register_pass_retype"
                    placeholder="<?php esc_html_e('Retype Password','houzez'); ?>" type="password" />
            </div><!-- input-group -->
        </div><!-- form-group -->
        <?php } ?>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label
                    for="brokerage_license_number"><?php esc_html_e('رقم رخصة الوساطة العقارية ( فال )','houzez');?></label>
                <input type="text" name="brokerage_license_number" value="" class="form-control"
                    placeholder="<?php esc_html_e('يرجي ادخال رقم رخصة الوساطة العقارية','houzez');?>">
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="license_expiration_date"><?php esc_html_e('تاريخ انتهاء الرخصة	','houzez');?></label>
                <input type="date" name="license_expiration_date" value="" class="form-control" placeholder="">
            </div>
        </div>
    </div><!-- login-form-wrap -->

    <div class="form-tools">
        <label class="control control--checkbox">
            <input name="term_condition" type="checkbox">
            <?php echo sprintf( __( 'I agree with your <a target="_blank" href="%s">Terms & Conditions</a>', 'houzez' ), 
            get_permalink(houzez_option('login_terms_condition') )); ?>
            <span class="control__indicator"></span>
        </label>
    </div><!-- form-tools -->

    <?php if(houzez_option('agent_forms_terms')) { ?>
    <div class="form-tools">
        <label class="control control--checkbox">
            <input name="privacy_policy" type="checkbox"> <?php echo houzez_option('agent_forms_terms_text'); ?>
            <span class="control__indicator"></span>
        </label>
    </div><!-- form-tools -->
    <?php } ?>

    <?php get_template_part('template-parts/google', 'reCaptcha'); ?>

    <?php wp_nonce_field( 'houzez_register_nonce', 'houzez_register_security' ); ?>
    <input type="hidden" name="action" value="ag_houzez_register" id="register_action">
    <button id="houzez-register-btn" type="submit" class="btn btn-primary btn-full-width">
        <?php get_template_part('template-parts/loader'); ?>
        <?php esc_html_e('Register','houzez');?>
    </button>
</form>