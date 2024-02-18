<?php
/**
 * Template Name: Brokerage License 
 */
global $post, $page_bg;
$sticky_sidebar = houzez_option('sticky_sidebar');
$sidebar_meta = houzez_get_sidebar_meta($post->ID);
$page_bg = 'page-content-wrap';
$userID = get_current_user_id();
$id_number  =  get_the_author_meta( 'aqar_author_id_number' , $userID );
$user_role = houzez_user_role_by_user_id( $userID );
?>
<?php get_header(); ?>
<section class="page-wrap">
    <div class="container">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb');  ?>
            <div class="d-flex align-items-center">
                <?php get_template_part('template-parts/page/page-title');  ?>
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
                        width: 120px !important;
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
                    <div class="brokerage-header text-center">
                        <h3>لإنشاء عقد تسويق وإصدار رخصة الاعلان يرجى إدخال البيانات التالية</h3>
                    </div>
                    <?php if( $user_role == "houzez_owner" || $user_role == "administrator" ) { ?>
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
                                        currnt.find('.houzez-loader-js').addClass(
                                        'loader-show');
                                    },
                                    complete: function() {
                                        currnt.find('.houzez-loader-js').removeClass(
                                            'loader-show');
                                    },
                                    success: function(response) {

                                    currnt.find('.houzez-loader-js').removeClass('loader-show');

                                    console.log(response.data.data);
                                    if( response.success  ) {
                                        // If the email is sent successfully, show a popup window
                                        // Fade out the form with a duration of 500 milliseconds
                                        $("#contract-form").fadeOut(500, function () {
                                            // Callback function after the fade out is complete
                                            // This is where you can perform additional actions if needed
                                            // Show the success content with a fade in effect
                                            $("#success-content").html(response.data.html);
                                            $("#success-content").fadeIn(500);
                                        });
                                    } else {
                                        $("#contract-form").fadeOut(500, function () {
                                            // Callback function after the fade out is complete
                                            // This is where you can perform additional actions if needed
                                            // Show the success content with a fade in effect
                                            $("#success-content").html(response.data.html);
                                            $("#success-content").fadeIn(500);
                                        });
                                    }  

                                    },
                                    error: function(error) {
                                        console.error("Error submitting form:", error);
                                        // Handle error if needed
                                    }
                                });
                            }
                        });

                        function validateForm() {
                            var isValid = true;
                            // Check each required field
                            $("#contract-form [required]").each(function () {
                                if (!$(this).val()) {
                                    isValid = false;
                                    alert("يرجى ملء جميع الحقول المطلوبة.");
                                    return false; // Break out of the loop if any required field is empty
                                }
                            });

                            return isValid;
                        }
                    });
                    </script>
                    <?php } else {  ?>
                        <div class="brokerage-header text-center">
                            <p>سجل دخول ليتم ارسال الطلب</p>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- bt-content-wrap -->
        </div><!-- row -->
    </div><!-- container -->
</section><!-- listing-wrap -->
<?php get_footer(); ?>