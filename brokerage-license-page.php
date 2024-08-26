<?php
/**
 * Template Name: Brokerage License 
 */
global $post, $page_bg;
$sticky_sidebar = houzez_option('sticky_sidebar');
$sidebar_meta = houzez_get_sidebar_meta($post->ID);
$page_bg      = 'page-content-wrap';
$userID       = get_current_user_id();
$id_number    = get_the_author_meta( 'aqar_author_id_number' , $userID );
$user_role    = houzez_user_role_by_user_id( $userID );
?>
<?php get_header(); ?>
<section class="page-wrap">
    <div class="container">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb');  ?>
            <div class="d-flex align-items-center">
                <?php //get_template_part('template-parts/page/page-title');  ?>
            </div><!-- d-flex -->
        </div><!-- page-title-wrap -->
        <div class="row">
            <div class="col-lg-10 col-md-12 m-auto">
                <div class="article-wrap">
                    <style>
                    /* Maintain the same border style */
                    form {
                        padding: 15px;
                        margin: 20px;
                    }

                    input[type=radio],
                    input[type=checkbox] {
                        margin-right: -1.25rem;
                    }

                    /* Customize form layout for RTL languages like Arabic */
                    body {
                        direction: rtl;
                    }

                    .input-group-text {
                        width: 140px !important;
                        text-align: right;
                        justify-content: start;
                        background: #ededed;
                        color: #000;
                        font-weight: 500;
                    }

                    .form-group {
                        margin-bottom: 15px;
                    }

                    h3 {
                        color: #007bff;
                        font-size: 17px;
                        margin: 1rem 0;
                    }

                    h4 {
                        font-size: 15px;
                        color: #4CAF50;
                        margin: 1rem 0;
                    }
                    </style>
                    <?php if( is_user_logged_in() && $user_role == "houzez_owner" || $user_role == "administrator" ) { ?>
                    <div class="brokerage-header text-center">
                        <h3>لإنشاء عقد تسويق وإصدار رخصة الاعلان يرجى إدخال البيانات التالية</h3>
                    </div>
                    <?php } ?>
                    <?php if( $user_role == "houzez_owner" || $user_role == "administrator" || intval($userID) === 1401 ) { ?>
                    <form id="contract-form">
                        <!-- بيانات المالك -->
                        <h3>بيانات المالك</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">رقم هوية المالك</span>
                                    </div>
                                    <input type="number" class="form-control" id="owner-id" name="owner-id" value="<?php echo $id_number; ?>" required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">تاريخ الميلاد</span>
                                    </div>
                                    <input type="date" class="form-control" id="owner-birth" name="owner-birth"
                                        required>
                                </div>
                            </div>


                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">نوع الهوية</span>
                                    </div>
                                    <select class="form-control" id="id-type" name="id-type" required>
                                        <option value="national-id">هوية وطنية</option>
                                        <option value="residence-permit">إقامة</option>
                                        <option value="commercial-record">سجل تجاري</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">رقم الجوال</span>
                                    </div>
                                    <input type="text" class="form-control" id="owner-mobile" name="owner-mobile"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- بيانات العقار -->
                        <h3>بيانات العقار</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">نوع وثيقة الملكية</span>
                                    </div>
                                    <select class="form-control" id="property-document" name="property-document"
                                        required>
                                        <option value="net-deed">صك الكتروني</option>
                                        <option value="paper-deed">صك ورقي</option>
                                        <option value="property-deed">صك عقار مع حصر ورثة</option>
                                        <option value="inheritance-certificate">حجة استحكام</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">رقم وثيقة الملكية</span>
                                    </div>
                                    <input type="text" class="form-control" id="document-number" name="document-number"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">نوع العقار</span>
                                    </div>
                                    <input type="text" class="form-control" id="property-type" name="property-type"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">مساحة العقار</span>
                                    </div>
                                    <input type="text" class="form-control" id="property-area" name="property-area"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">المدينة</span>
                                    </div>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الحي</span>
                                    </div>
                                    <input type="text" class="form-control" id="neighborhood" name="neighborhood"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">رقم القطعة</span>
                                    </div>
                                    <input type="text" class="form-control" id="parcel-number" name="parcel-number"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">السعر المطلوب</span>
                                    </div>
                                    <input type="text" class="form-control" id="price" name="price" required>
                                </div>
                            </div>
                        </div>
                        <h3>العنوان الوطني للعقار</h3>
                        <div class="form-row mb-5">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الشارع</span>
                                    </div>
                                    <input type="text" class="form-control" id="street" name="street"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الرمز البريدي</span>
                                    </div>
                                    <input type="text" class="form-control" id="postalCode" name="postalCode"
                                        required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">رقم المبني</span>
                                    </div>
                                    <input type="text" class="form-control" id="buildingNumber" name="buildingNumber"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الرمز الاضافي</span>
                                    </div>
                                    <input type="text" class="form-control" id="additionalNumber" name="additionalNumber"
                                        required>
                                </div>
                            </div>
                        </div>
                        <h4>ملاحظة : رسوم عقد التسويق ورخصة الاعلان 200 ريال سعودي</h4>
                        <!-- ملاحظة وزر الإرسال -->
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="approval-checkbox" required>
                                <label class="form-check-label" for="approval-checkbox">أوافق على الشروط
                                    والأحكام.</label>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" id="submit-button"><span
                                class="btn-loader houzez-loader-js"></span> إنشاء عقد التسويق ورخصة الاعلان</button>
                    </form>
                    <!-- Additional content to be shown after successful form submission -->
                    <div id="success-content" style="display: none;">
                        <p>تم استلام طلبك بنجاح. شكرًا لثقتكم!</p>
                    </div>
                    <script>
                    jQuery(document).ready(function($) {
                        $("#submit-button").click(function() {
                            var formData = $("#contract-form").serialize();
                            var currnt = $(this);

                            // Check if all required fields are filled
                            if (validateForm()) {
                                $.ajax({
                                    type: "POST",
                                    url: ajax_aqar.ajaxurl,
                                    data: {
                                        action: "handle_contract_submission",
                                        form_data: formData,
                                    },
                                    beforeSend: function() {
                                        currnt.find('.houzez-loader-js').addClass('loader-show');
                                    },
                                    complete: function() {
                                        currnt.find('.houzez-loader-js').removeClass('loader-show');
                                    },
                                    success: function(response) {
                                        currnt.find('.houzez-loader-js').removeClass('loader-show');

                                        if(response.success) {
                                            // Redirect to the checkout page
                                            window.location.href = response.data.redirect;
                                        } else {
                                            console.error("Error:", response.data.message);
                                            alert("There was an error: " + response.data.message);
                                        }
                                    },
                                    error: function(error) {
                                        console.error("Error submitting form:", error);
                                        alert("There was an error submitting the form. Please try again.");
                                    }
                                });
                            }
                        });

                        

                        function validateForm() {
                            var isValid = true;
                            // Remove existing error messages
                            $("#contract-form .error-message").remove();

                            // Check each required field
                            $("#contract-form [required]").each(function() {
                                if ($(this).attr('type') === 'checkbox') {
                                    if (!$(this).is(':checked')) {
                                        isValid = false;
                                        var errorMessage = getErrorMessage($(this).attr('id'));
                                        $(this).parent().parent().append('<div class="error-message w-100" style="color: red;font-size: 12px;background: #ff020214;padding: 0px 15px;border-radius: 4px;margin-top: 5px;">' + errorMessage + '</div>');
                                    }
                                } else {
                                    if (!$(this).val()) {
                                        isValid = false;
                                        var errorMessage = getErrorMessage($(this).attr('id'));
                                        $(this).after('<div class="error-message w-100" style="color: red;font-size: 12px;background: #ff020214;padding: 0px 15px;border-radius: 4px;margin-top: 5px;">' + errorMessage + '</div>');
                                    }
                                }
                            });
                            return isValid;
                        }

                        function getErrorMessage(fieldId) {
                            var messages = {
                                "owner-id": "يرجى إدخال رقم هوية المالك",
                                "owner-birth": "يرجى إدخال تاريخ الميلاد",
                                "id-type": "يرجى اختيار نوع الهوية",
                                "owner-mobile": "يرجى إدخال رقم الجوال",
                                "property-document": "يرجى اختيار نوع وثيقة الملكية",
                                "document-number": "يرجى إدخال رقم وثيقة الملكية",
                                "property-type": "يرجى إدخال نوع العقار",
                                "property-area": "يرجى إدخال مساحة العقار",
                                "city": "يرجى إدخال المدينة",
                                "neighborhood": "يرجى إدخال الحي",
                                "parcel-number": "يرجى إدخال رقم القطعة",
                                "price": "يرجى إدخال السعر المطلوب",
                                "street": "يرجى إدخال الشارع",
                                "postalCode": "يرجى إدخال الرمز البريدي",
                                "buildingNumber": "يرجى إدخال رقم المبنى",
                                "additionalNumber": "يرجى إدخال الرقم الإضافي",
                                "approval-checkbox": "يرجى الموافقة على الشروط والأحكام"
                            };
                            return messages[fieldId] || "هذا الحقل مطلوب";
                        }
                        
                    });

                    </script>
                    <?php } else {  ?>
                        <div class="brokerage-header text-center">
                            <div class="dashboard-content-block-wrap">
                                <div class="submit-login-required" style="padding-bottom: 9rem; padding-top: 12rem;">
                                <?php if( ! is_user_logged_in() ) { ?>
                                    <?php echo ' لإنشاء عقد تسویق یرجى تسجیل الدخول'; ?>
                                    <?php if( houzez_option('header_login') != 0 ) { ?>
                                    <span class="login-link"><a href="#" data-toggle="modal" data-target="#login-register-form"><?php esc_html_e('Login', 'houzez'); ?></a></span> 
                                    <?php } ?>

                                    <?php if( houzez_option('header_register') != 0 ) { ?>
                                    - 
                                    <span class="register-link"><a href="#" data-toggle="modal" data-target="#login-register-form"><?php esc_html_e('Register', 'houzez'); ?></a></span> 
                                    <?php } ?>
                                        
                                <?php } else {  ?>  
                                    <div style="border: 1px solid #1f3864;padding-top: 20px;background-color: #e5fde1;margin-bottom: 20px;border-radius: 10px;">
                                        <p>إنشاء عقد التسويق متاح حاليا للملاك فقط .</p>
                                    </div>
                                <?php }  ?>     
                                </div>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- bt-content-wrap -->
        </div><!-- row -->
    </div><!-- container -->
</section><!-- listing-wrap -->
<?php get_footer(); ?>