<?php 
$userID                   = get_current_user_id();
$id_number                = get_the_author_meta( 'aqar_author_id_number' , $userID );
$brokerage_license_number = get_the_author_meta( 'brokerage_license_number' , $userID );
$license_expiration_date  = get_the_author_meta( 'license_expiration_date' , $userID );
$validateDateTime         = strtotime($license_expiration_date);
$license_expiration_date  = (!empty($license_expiration_date) && $validateDateTime ) ? date( "Y-m-d", strtotime( $license_expiration_date ) ) : '';
$user_email               = get_the_author_meta( 'user_email' , $userID );
$user_mobile              =   get_the_author_meta( 'fave_author_mobile' , $userID );
?>
<div id="hz-register-messages" class="hz-social-messages"></div>
<div id="register-screen-1">
    <form id="register-screen-form">
        <input type="hidden" name="user-id" value="<?php echo $userID; ?>">
        <div class="modal-header-ajax text-center">
            <span class="text-center" style="color: #bdb290;font-size: 12px;">الربط بنفاذ</span>
            <h5 style="margin-bottom: 40px;">اكمل الربط بنفاذ للوصول الي حسابك</h5>
        </div>
        <style> 
        .control__indicator_img {
            position: absolute;
            top: 2px;
            right: 0;
            height: 50px;
            width: 50px;
            background: #fff;
        }

        .control.img {
            padding-right: 70px;
        }

        .control input:checked~.control__indicator_img {
            border: 2px solid #ffffff;
            border-radius: 50%;
            box-shadow: 1px 1px 16px 1px #00000045;
        }
        div#time-model {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100%;
            background: #ffffffbf;
        }
        .warp {
            margin: 33% 33%;
            text-align: center;
            background: #bdb290;
            padding: 1rem;
            border-radius: 6px;
        }
        div#timer {
            font-size: 50px;
            margin: 30px 0;
        }
        .login-register-form .modal-dialog {
            max-width: 555px !important;
        }
        .sync__loader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            bottom: 0;
            text-align: center;
            align-items: center;
            background: #ffffffbf;
            display: none;
        }
        svg#loader-1 {
            margin: 25%;
        }
        </style>
        <fieldset id="aqar_author_type_id">
            <div class="form-control-wrap d-flex flex-column">
                <label class="control control--radio">
                    <input type="radio" name="aqar_author_type_id" value="1" checked>
                    <h5>حساب أفراد</h5>
                    <span class="radio-sub" style="color: #bdb290;font-size: 12px;">التسجيل كمسوق عقاري فرد</span>
                    <span class="control__indicator"></span>
                </label>
                <label class="control control--radio">
                    <input type="radio" name="aqar_author_type_id" value="2">
                    <h5>حساب منشأة عقارية</h5>
                    <span class="radio-sub" style="color: #bdb290;font-size: 12px;">التسجيل للشركات والؤسسات
                        العقارية</span>
                    <span class="control__indicator"></span>
                </label>
                <a href="#" id="nic" class="control img control--radio">
                    <h5>التوثيق عن طريق نفاذ الوطني</h5>
                    <span class="radio-sub" style="color: #bdb290;font-size: 12px;"> التسجيل عن طريق الدخول بحساب النفاذ
                        الوطني الموحد </span>
                    <img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) .'assets/img/NIC-logo.png'; ?>"
                        alt="NIC" class="control__indicator_img">
                </a>
                <div class="form-group" id="nafath_id"> 
                    <label id="change-lable" for=""> رقم الهوية </label>
                    <input name="id" id="id" type="text" class="form-control" value="<?php echo esc_attr( $id_number );?>" placeholder="">
                </div>
            </div>
            <button id="next-register-btn" type="submit" class="btn btn-primary btn-full-width">
                <?php get_template_part('template-parts/loader'); ?>
                <?php esc_html_e('Next','houzez');?>
            </button>
        </fieldset>
        <div class="sync__loader">
            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="80px" height="80px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
           <path opacity="0.2" fill="#312d24" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
             s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
             c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
           <path fill="#bdb290" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
             C22.32,8.481,24.301,9.057,26.013,10.047z">
             <animateTransform attributeType="xml"
               attributeName="transform"
               type="rotate"
               from="0 20 20"
               to="360 20 20"
               dur="0.5s"
               repeatCount="indefinite"/>
             </path>
           </svg>
        </div>
    </form>
    <div id="time-model" class="time-model" style="display: none;">
        <div class="warp">
            <div id="id-number">
                <span>رقم التاكيد</span>
                <span id="nafathNumber">00</span>
            </div>
            <div id="timer">60</div>
        </div>
    </div> 
</div><!-- /end /register-screen-1 -->