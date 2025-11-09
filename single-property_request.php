<?php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        if ( get_post_status() == 'publish' ) :
            global $wpdb;
            $table_name = $wpdb->prefix . 'property_requests';
            $post_id = get_the_ID();
            $user_id = get_current_user_id();
            $post_author = intval(get_post_field( 'post_author', $post_id  ));
            // استرداد تفاصيل الطلب من الجدول المخصص
            $request = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE post_id = %d",
                $post_id
            ));
            $is_edit = false;
            if( (isset($_GET['edit']) && $_GET['edit'] === 'true') && $user_id === $post_author) {
                $is_edit = true;
            }
            if ($request) :
                // ترجمات "نوع الطلب" و "طريقة الدفع"
                $request_types = array(
                    'sell' => 'شراء',
                    'rent' => 'إيجار'
                );

                $payment_methods = array(
                    'cash' => 'نقداً',
                    'finance' => 'تمويل عقاري'
                );
                
                $property_ages = array(
                    'new' => 'جديد',
                    'less-year' => 'اقل من سنة',
                    'year' => 'سنة',
                    'two-year' => 'سنتين',
                    'three-year' => 'ثلاث سنوات',
                    'four-year' => 'اربع سنوات',
                    'five-year' => 'خمس سنوات',
                    'six-year' => 'ست سنوات',
                    'seven-year' => 'سبع سنوات',
                    'eight-year' => 'ثمان سنوات',
                    'nine-year' => 'تسع سنوات',
                    'ten-year' => 'عشر سنوات',
                    'more-than-ten' => 'اكثر من عشر سنوات'
                );
                
?>


<style>
.dashboard-content {
    padding: 20px;
}

.details-table {
    width: 100%;
    border-collapse: separate;
}

.details-table th,
.details-table td {
    border: 1px solid #d1d1d1;
    padding: 8px;
    text-align: right;
}

.details-table th {
    background-color: #f1f1f1;
    font-weight: bold;
}

/* .details-table tr:nth-child(even) {
    background-color: #d3e3ff;
}

.details-table tr:hover {
    background-color: #d3e3ff;
} */

.details-table .section-header {
    background-color: #1f3864;
    color: #fff;
    font-weight: bold;
    text-align: center;
}
h2.section-head {
    display: inline-block;
    color: #007bff;
    padding: 10px 10px;
    margin: 20px 0;
    font-size: 21px;
}
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
<?php if( $is_edit ) { ?>
<div class="dashboard-content container">
<div class="brokerage-header text-center">
                        <h3>تعديل طلب عقار : <?php echo $request->post_id; ?></h3>
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
                                        <?php
                                        $selected_property_request = $request->property_request ?? '';
                                        ?>

                                        <div class="form-control d-flex">
                                            <label class="control control--radio mr-3">
                                                <input type="radio" name="property-request" value="sell" <?php echo ($selected_property_request === 'sell') ? 'checked' : ''; ?>> شراء
                                                <span class="control__indicator"></span>
                                            </label>
                                            <label class="control control--radio">
                                                <input type="radio" name="property-request" value="rent" <?php echo ($selected_property_request === 'rent') ? 'checked' : ''; ?>> ايجار
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
                                    <select name="prop_type" data-size="5" <?php houzez_required_field_2('prop_type'); ?> id="prop_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_select', 'Select'); ?>" data-selected-text-format="count > 2" data-live-search="true" data-actions-box="true" <?php houzez_multiselect(houzez_option('ams_type', 0)); ?> data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-count-selected-text="{0} <?php echo houzez_option('cl_prop_types', 'Types'); ?>">
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
                                        $taxonomy_terms_ids = ag_get_term_id_by_name($request->prop_type, 'property_type');
                                        houzez_get_taxonomies_for_edit_listing_multivalue_child( 'property_type', $property_types_terms, [$taxonomy_terms_ids] );

                                        ?>

                                    </select><!-- selectpicker -->
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">عمر العقار</span>
                                    </div>
                                    <?php $selected_value = $request->prop_age ?? ''; ?>
                                    <select name="prop_age" data-size="5" <?php houzez_required_field_2('prop_type'); ?> id="prop_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_select', 'Select'); ?>" data-selected-text-format="count > 2" data-live-search="true" data-actions-box="true" <?php houzez_multiselect(houzez_option('ams_type', 0)); ?> data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-count-selected-text="{0} <?php echo houzez_option('cl_prop_types', 'Types'); ?>">
                                    <option value="new" <?php echo ($selected_value == 'new') ? 'selected' : ''; ?>>جديد</option>
                                    <option value="less-year" <?php echo ($selected_value == 'less-year') ? 'selected' : ''; ?>>اقل من سنة</option>
                                    <option value="year" <?php echo ($selected_value == 'year') ? 'selected' : ''; ?>>سنة</option>
                                    <option value="two-year" <?php echo ($selected_value == 'two-year') ? 'selected' : ''; ?>>سنتين</option>
                                    <option value="three-year" <?php echo ($selected_value == 'three-year') ? 'selected' : ''; ?>>ثلاث سنوات</option>
                                    <option value="four-year" <?php echo ($selected_value == 'four-year') ? 'selected' : ''; ?>>اربع سنوات</option>
                                    <option value="five-year" <?php echo ($selected_value == 'five-year') ? 'selected' : ''; ?>>خمس سنوات</option>
                                    <option value="six-year" <?php echo ($selected_value == 'six-year') ? 'selected' : ''; ?>>ست سنوات</option>
                                    <option value="seven-year" <?php echo ($selected_value == 'seven-year') ? 'selected' : ''; ?>>سبع سنوات</option>
                                    <option value="eight-year" <?php echo ($selected_value == 'eight-year') ? 'selected' : ''; ?>>ثمان سنوات</option>
                                    <option value="nine-year" <?php echo ($selected_value == 'nine-year') ? 'selected' : ''; ?>>تسع سنوات</option>
                                    <option value="ten-year" <?php echo ($selected_value == 'ten-year') ? 'selected' : ''; ?>>عشر سنوات</option>
                                    <option value="more-than-ten" <?php echo ($selected_value == 'more-than-ten') ? 'selected' : ''; ?>>اكثر من عشر سنوات</option>

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
                                    <select name="country" id="country" data-country="<?php echo urldecode($country); ?>" data-target="houzezSecondList" <?php houzez_required_field_2('country'); ?> class="houzezSelectFilter houzezFirstList selectpicker form-control bs-select-hidden" data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true">
                                        <?php
                                        
                                        echo '<option value="">'.houzez_option('cl_none', 'None').'</option>';
                                                    
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
                                        houzez_taxonomy_hirarchical_options_for_search( 'property_country', $property_country_terms, 'saudi-arabia' );
                                        // houzez_hirarchical_options( 'property_country', $property_country_terms, -1);
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">المنطقة</span>
                                    </div>
                                    <select name="administrative_area_level_1" data-state="<?php echo urldecode($state); ?>" data-target="houzezThirdList" <?php houzez_required_field_2('state'); ?> id="countyState" class="houzezSelectFilter houzezSecondList selectpicker form-control bs-select-hidden" data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true">
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
                                        
                                        houzez_taxonomy_hirarchical_options_for_search( 'property_state', $property_state_terms, lowercase_rawurlencode($request->state) );
                                        // houzez_hirarchical_options( 'property_state', $property_state_terms, -1);
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
                                    <select name="locality" id="city" data-city="<?php echo urldecode($city); ?>" data-target="houzezFourthList" <?php houzez_required_field_2('city'); ?> class="houzezSelectFilter houzezThirdList selectpicker form-control bs-select-hidden"  data-size="5" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}" data-live-search="true">
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
                                        houzez_taxonomy_hirarchical_options_for_search( 'property_city', $property_city_terms, lowercase_rawurlencode($request->city) );
                                        // houzez_hirarchical_options( 'property_city', $property_city_terms, -1);   
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">الحي</span>
                                    </div>
                                    <select name="neighborhood" data-area="<?php echo urldecode($area); ?>" data-size="5" id="neighborhood" <?php houzez_required_field_2('area'); ?> class=" houzezSelectFilter houzezFourthList selectpicker form-control bs-select-hidden" data-live-search="true" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}">
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
                                        houzez_taxonomy_hirarchical_options_for_search( 'property_area', $property_area_terms, lowercase_rawurlencode($request->area) );
                                        // houzez_hirarchical_options( 'property_area', $property_area_terms, -1);
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
                                    <input type="text" class="form-control" id="land-area" name="land-area" value="<?php echo $request->land_area ?? ''; ?>"
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
                                    <input type="number" class="form-control" id="price" name="price" value="<?php echo $request->price ?? ''; ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                            <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">طريقة الدفع</span>
                                        </div>
                                        <?php
                                        $selected_payment_method = isset($request->payment_method) ? $request->payment_method : '';
                                        ?>

                                        <div class="form-control d-flex">
                                            <label class="control control--radio mr-3">
                                                <input type="radio" name="payment-method" value="cash" <?php echo ($selected_payment_method == 'cash') ? 'checked' : ''; ?>> نقدا
                                                <span class="control__indicator"></span>
                                            </label>
                                            <label class="control control--radio">
                                                <input type="radio" name="payment-method" value="finance" <?php echo ($selected_payment_method == 'finance') ? 'checked' : ''; ?>> تمويل عقاري
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
                                    <textarea class="form-control" name="more-info" id="more-info" cols="30" rows="5"><?php echo $request->more_info ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user-id" value="<?php echo get_current_user_id(); ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="post_id" value="<?php echo $request->post_id ; ?>">
                        <div class="submit-wrap text-center mt-5">
                            <button type="button" class="btn btn-primary" id="submit-button"><span
                                    class="btn-loader houzez-loader-js"></span>تعديل الطلب</button>
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
</div>
<?php } else { ?>
<div class="dashboard-content container mt-5 mb-5 pb-5">
    <h1>تفاصيل الطلب رقم <?php echo esc_html($request->post_id); ?></h1>
    <h2 class="section-head">بيانات العقار</h2>
    <table class="details-table">
        <thead>
        </thead>
        <tbody>
            <tr>
                <th>رقم الطلب</th>
                <td><?php echo esc_html($request->post_id); ?></td>
                <th>نوع الطلب</th>
                <td><?php echo esc_html($request_types[$request->property_request]); ?></td>
                <th>نوع العقار</th>
                <td><?php echo esc_html($request->prop_type); ?></td>
            </tr>
            <tr>
                <th>المنطقة</th>
                <td><?php echo esc_html($request->state); ?></td>
                <th>المدينة</th>
                <td><?php echo esc_html($request->city); ?></td>
                <th>الحي</th>
                <td><?php echo esc_html($request->area); ?></td>
            </tr>
            <tr>
                <th>المساحة</th>
                <td><?php echo esc_html($request->land_area); ?></td>
                <th>السعر</th>
                <td><?php echo esc_html(is_numeric($request->price) ? number_format($request->price) : $request->price); ?></td>
                <th>عمر العقار</th>
                <td><?php echo esc_html($property_ages[$request->prop_age]); ?></td>
            </tr>
            <tr>
                <th colspan="1">طريقة الدفع</th>
                <td colspan="5"><?php echo esc_html($payment_methods[$request->payment_method]); ?></td>
            </tr>
            <tr>
                <th colspan="1">تفاصيل أخرى</th>
                <td colspan="5"><?php echo esc_html($request->more_info); ?></td>
            </tr>
        </tbody>
    </table>
    <?php if( can_view_property_req_info() ) { ?>
    <h2 class="section-head">بيانات طالب العقار</h2>
    <table class="details-table text-center">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>رقم الجوال</th>
                <th>رقم الواتساب</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $user_id = get_post_field('post_author', get_the_ID());
            $user_info = get_userdata(get_post_field('post_author', get_the_ID()));
            ?>
            <tr>
                <td><?php echo esc_html($user_info->display_name); ?></td>
                <td><?php echo esc_html($user_info->user_email); ?></td>               
                <td dir="ltr"><?php echo esc_html(get_user_meta($user_id, 'fave_author_mobile', true)); ?></td>                
                <td><?php echo esc_html(get_user_meta($user_id, 'fave_author_whatsapp', true)); ?></td>
            </tr>
        </tbody>
    </table>
    <?php } ?>
</div>
<?php } ?>
<?php
            else :
                echo '<div class="brokerage-header text-center h-100">
    <div class="dashboard-content container text-center mt-5 pt-5 mb-5 pb-5">
        <div class="dashboard-content-block-wrap">
            <div class="submit-login-required" style="padding-bottom: 12rem;"><h4>عزیزنا العمیل .. بیانات ھذا الطلب غیر متاحة حیث تم التعاقد وإنھاء الطلب. </h4></div>
        </div><!-- dashboard-content-block-wrap -->
    </div>
</div>';
            endif;
        else :
            echo '<div class="brokerage-header text-center h-100">
    <div class="dashboard-content container text-center mt-5 pt-5 mb-5 pb-5">
        <div class="dashboard-content-block-wrap">
            <div class="submit-login-required" style="padding-bottom: 12rem;"><h4>عزیزنا العمیل .. بیانات ھذا الطلب غیر متاحة حیث تم التعاقد وإنھاء الطلب. </h4></div>
        </div><!-- dashboard-content-block-wrap -->
    </div>
</div>';
        endif;
    endwhile;
else :
    echo '<div class="brokerage-header text-center h-100">
    <div class="dashboard-content container text-center mt-5 pt-5 mb-5 pb-5">
        <div class="dashboard-content-block-wrap">
            <div class="submit-login-required" style="padding-bottom: 12rem;"><h4>عزیزنا العمیل .. بیانات ھذا الطلب غیر متاحة حیث تم التعاقد وإنھاء الطلب. </h4></div>
        </div><!-- dashboard-content-block-wrap -->
    </div>
</div>';
endif;

get_footer();