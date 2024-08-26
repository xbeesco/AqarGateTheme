<?php
/**
 * Template Name: Property request 
 */
global $post, $page_bg;
$sticky_sidebar = houzez_option('sticky_sidebar');
$sidebar_meta = houzez_get_sidebar_meta($post->ID);
$page_bg = 'page-content-wrap';
$userID = get_current_user_id();
$id_number  =  get_the_author_meta( 'aqar_author_id_number' , $userID );
$user_role = houzez_user_role_by_user_id( $userID );
$select_packages_link = houzez_get_template_link('template/template-packages.php'); 
?>
<?php get_header(); ?>
<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">
        <div class="d-flex align-items-center">
            <div class="dashboard-header-left flex-grow-1">
                <h1><?php the_title(); ?></h1>         
            </div><!-- dashboard-header-left -->

            <div class="dashboard-header-right">
                
            </div><!-- dashboard-header-right -->
        </div><!-- d-flex -->
    </div><!-- dashboard-header-wrap -->
</header><!-- .header-main-wrap -->
<section class="dashboard-content-wrap">
    <div class="dashboard-content-inner-wrap mt-5 pt-5 mb-5 pb-5">
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
                        background: #1f3864;
                        color: #ffffff;
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
                    .submit-wrap .btn-primary{
                        background: #1f3864;
                        border: 4px solid #fff;
                        box-shadow: 0px 0px 7px 3px #0000002b;
                        border-radius: 4px;
                    }

                    .btn-loader:after {
                        border: 2px solid #fff;
                        border-color: #fff transparent;
                    }
                    </style>
                    <?php if( is_user_logged_in() ) { ?>
                    <div class="brokerage-header text-center">
                        <h3>لإنشاء طلب عقاري  يرجى إدخال البيانات التالية</h3>
                    </div>
                    <form id="contract-form">
                        <!-- بيانات المالك -->
                        <h3>تفاصيل الطلب</h3>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">نوع الطلب</span>
                                        </div>
                                        <div class="form-control d-flex">
                                            <label class="control control--radio mr-3">
                                                <input type="radio" name="property-request"
                                                    value="sell" checked>شراء
                                                <span class="control__indicator"></span>
                                            </label>
                                            <label class="control control--radio">
                                                <input type="radio" name="property-request"
                                                    value="rent">ايجار
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">نوع العقار</span>
                                    </div>
                                    <select name="prop_type" data-size="5" <?php houzez_required_field_2('prop_type'); ?> id="prop_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_select', 'Select'); ?>" data-selected-text-format="count > 2" data-live-search="true" data-actions-box="true" <?php houzez_multiselect(houzez_option('ams_type', 0)); ?> data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-count-selected-text="{0} <?php echo houzez_option('cl_prop_types', 'Types'); ?>" <?php echo $disabled; ?>>
                                        <?php
                                                            
                                        $property_types_terms = get_terms (
                                            array(
                                                "property_type"
                                            ),
                                            array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            )
                                        );

                                        houzez_get_taxonomies_with_id_value( 'property_type', $property_types_terms, -1);
                                        
                                        ?>

                                    </select><!-- selectpicker -->
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">عمر العقار</span>
                                    </div>
                                    <select name="prop_age" data-size="5" <?php houzez_required_field_2('prop_type'); ?> id="prop_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_select', 'Select'); ?>" data-selected-text-format="count > 2" data-live-search="true" data-actions-box="true" <?php houzez_multiselect(houzez_option('ams_type', 0)); ?> data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-count-selected-text="{0} <?php echo houzez_option('cl_prop_types', 'Types'); ?>" <?php echo $disabled; ?>>
                                    <option value="new">جديد</option>
                                    <option value="less-year">اقل من سنة</option>
                                    <option value="year">سنة</option>
                                    <option value="two-year">سنتين</option>
                                    <option value="three-year">ثلاث سنوات</option>
                                    <option value="four-year">اربع سنوات</option>
                                    <option value="five-year">خمس سنوات</option>
                                    <option value="six-year">ست سنوات</option>
                                    <option value="seven-year">سبع سنوات</option>
                                    <option value="eight-year">ثمان سنوات</option>
                                    <option value="nine-year">تسع سنوات</option>
                                    <option value="ten-year">عشر سنوات</option>
                                    <option value="more-than-ten">اكثر من عشر سنوات</option>

                                    </select><!-- selectpicker -->
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الدولة</span>
                                    </div>
                                    <select name="country" id="country" data-country="<?php echo urldecode($country); ?>" data-target="houzezSecondList" <?php houzez_required_field_2('country'); ?> class="houzezSelectFilter houzezFirstList selectpicker form-control bs-select-hidden" data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true" <?php echo $disabled; ?>>
                                        <?php
                                        
                                        echo '<option value="" selected="selected">'.houzez_option('cl_none', 'None').'</option>';
                                                    
                                        $property_country_terms = get_terms (
                                            array(
                                                "property_country"
                                            ),
                                            array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            )
                                        );

                                        houzez_hirarchical_options( 'property_country', $property_country_terms, -1);
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">المنطقة</span>
                                    </div>
                                    <select name="administrative_area_level_1" data-state="<?php echo urldecode($state); ?>" data-target="houzezThirdList" <?php houzez_required_field_2('state'); ?> id="countyState" class="houzezSelectFilter houzezSecondList selectpicker form-control bs-select-hidden" data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true" <?php echo $disabled; ?>>
                                        <?php
                                        echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';               
                                        $property_state_terms = get_terms (
                                            array(
                                                "property_state"
                                            ),
                                            array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            )
                                        );

                                        houzez_hirarchical_options( 'property_state', $property_state_terms, -1);
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">المدينة</span>
                                    </div>
                                    <select name="locality" id="city" data-city="<?php echo urldecode($city); ?>" data-target="houzezFourthList" <?php houzez_required_field_2('city'); ?> class="houzezSelectFilter houzezThirdList selectpicker form-control bs-select-hidden"  data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true" <?php echo $disabled; ?>>
                                        <?php
                                        echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';                
                                        $property_city_terms = get_terms (
                                            array(
                                                "property_city"
                                            ),
                                            array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            )
                                        );

                                        houzez_hirarchical_options( 'property_city', $property_city_terms, -1);   
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الحي</span>
                                    </div>
                                    <select name="neighborhood" data-area="<?php echo urldecode($area); ?>" data-size="5" id="neighborhood" <?php houzez_required_field_2('area'); ?> class=" houzezSelectFilter houzezFourthList selectpicker form-control bs-select-hidden" data-live-search="true" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" <?php echo $disabled; ?>>
                                        <?php                      
                                        echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';                  
                                        $property_area_terms = get_terms (
                                            array(
                                                "property_area"
                                            ),
                                            array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => 0
                                            )
                                        );

                                        houzez_hirarchical_options( 'property_area', $property_area_terms, -1);
                                        ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">المساحة</span>
                                    </div>
                                    <input type="text" class="form-control" id="land-area" name="land-area"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">السعر</span>
                                    </div>
                                    <input type="number" class="form-control" id="price" name="price"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                            <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">طريقة الدفع</span>
                                        </div>
                                        <div class="form-control d-flex">
                                            <label class="control control--radio mr-3">
                                                <input type="radio" name="payment-method"
                                                    value="cash" checked>نقدا
                                                <span class="control__indicator"></span>
                                            </label>
                                            <label class="control control--radio">
                                                <input type="radio" name="payment-method"
                                                    value="finance">تمويل عقاري
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">تفاصيل اخري</span>
                                    </div>
                                    <textarea class="form-control" name="more-info" id="more-info" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user-id" value="<?php echo get_current_user_id(); ?>">
                        <div class="submit-wrap text-center mt-5">
                            <button type="button" class="btn btn-primary" id="submit-button"><span
                                    class="btn-loader houzez-loader-js"></span>نشر الطلب</button>
                        </div>
                    </form>
                    <!-- Additional content to be shown after successful form submission -->
                    <div id="success-content" style="display: none;"></div>
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
                                        action: "handle_property_request_submission",
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
                                    if( response.sucsses === true  ) {
                                        console.log(response);

                                        bootbox.confirm({
                                            message: "<p><strong>"+response.html+"</strong></p>",
                                            buttons: {
                                                confirm: {
                                                label: 'كل الطلبات',
                                                className: 'btn-primary'
                                                },
                                                cancel: {
                                                label: 'اضافة طلب اخر',
                                                className: 'btn-secondary'
                                                }
                                            },
                                            callback: function (result) {
                                                if(result) {
                                                    // Redirect to all user property page
                                                    window.location.href = response.redirect;
                                                } else {
                                                    // Reload the current page
                                                    location.reload();
                                                }
                                            }
                                        });
                                    } else {
                                        $("#contract-form").fadeOut(500, function () {
                                            // Callback function after the fade out is complete
                                            // This is where you can perform additional actions if needed
                                            // Show the success content with a fade in effect
                                            $("#success-content").html(response.html);
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
                                    console.log($(this));
                                    alert("يرجى ملء جميع الحقول المطلوبة.");
                                    return false; // Break out of the loop if any required field is empty
                                }
                            });

                            return isValid;
                        }
                    });
                    </script>
                    
                    <?php } else {  ?>
                        <?php get_template_part('template-parts/dashboard/submit/partials/login-required-property'); ?>
                    <?php } ?>
    </div><!-- container -->
</section><!-- listing-wrap -->
<section class="dashboard-side-wrap">
    <?php get_template_part('template-parts/dashboard/side-wrap'); ?>
</section>
<?php get_footer(); ?>