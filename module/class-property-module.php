<?php

class PropertyMoudle{

    public function __construct() {
        
        /* ---------------------- init_actions && init_includes --------------------- */
        $this->init_actions();
        $this->init_includes();
    }
    
    /**
     * init_actions
     *
     * @return void
     */
    public function init_actions() {
        
        /* ------------------ action for display aqar isvalid form ------------------ */
        if( get_option( '_aq_show_api' ) == 'yes' ) {
            add_action( 'aqar_isvalid', array($this, 'aqar_isvalid'), 20, 1 );
        }
        
        /* ----------------------- Ajax add property function ----------------------- */
        add_action( 'wp_ajax_nopriv_aqar_isvalid_api', array( $this,'aqar_isvalid_api' ));
        add_action( 'wp_ajax_aqar_isvalid_api', array( $this,'aqar_isvalid_api' ));

        /* ----------------------- Ajax get property info function ----------------------- */
        add_action( 'wp_ajax_nopriv_get_ad_info', array( $this,'get_ad_info' ));
        add_action( 'wp_ajax_get_ad_info', array( $this,'get_ad_info' ));

    }
    
    /**
     * init_includes
     *
     * @return void
     */
    public function init_includes() { }    
    
    /**
     * aqar_isvalid
     *
     * @param  mixed $userID
     * @return void
     */
    public function aqar_isvalid($userID)
    {
        $cancel_link = houzez_dashboard_listings();
        if( !is_user_logged_in() ) {
            $cancel_link = home_url('/');  
        }
        $allowed_html = array(
            'i' => array(
                'class' => array()
            ),
            'strong' => array(),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array()
            )
        );

        $userID    = get_current_user_id();
        $id_number = get_the_author_meta( 'aqar_author_id_number' , $userID );
        $type_id   = get_the_author_meta( 'aqar_author_type_id' , $userID );
        $formTitle = get_option( '_form_head', 'اكمل البينات المطلوبة للتأكد من معلومات الاعلان' );
        if( empty($formTitle) ) {
            $formTitle = 'اكمل البينات المطلوبة للتأكد من معلومات الاعلان' ;
        }
        ?>
        <div class="container">
            <style>
            #submit_property_form,
            #save_as_draft {
                display: none;
            }
            .error{
                color: red;
            }
            </style>
            <form autocomplete="off" id="submit_aqar_isvalid_form" name="new_post" method="post" action="#" enctype="multipart/form-data"
              class="add-frontend-property">
              <div class="w-100 text-center mb-5">
                    <h2><?php echo $formTitle; ?></h2>
                </div>
                <hr>
                <div id="errors-messages" class="validate-errors alert alert-danger houzez-hidden" role="alert">
                    <strong id="messages"></strong> 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="success-messages" class="validate-success alert alert-success houzez-hidden" role="alert">
                    <strong id="messages"></strong> 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="isvalid-step" class="dashboard-content-block-wrap mt-5 row">
                    <div class="form-group col-md-8 col-sm-12">
                        <label for="adLicenseNumber"> * رقم ترخيص الاعلان</label>
                        <input class="form-control" id="adLicenseNumber" required name="adLicenseNumber" value="" placeholder="" type="text">
                    </div>
                
                    <div class="text-center mb-2 col-md-4 col-sm-12" style="margin-top: 35px;">
                        <button id="get_ad_info" type="submit" class="btn houzez-submit-js btn-success">
                                <span class="btn-loader houzez-loader-js"></span>تأكيد المعلومات         
                        </button>
                    </div>
                </div>

                <div id="property-info" class="w-100" style="display: none;">
                    <div class="w-100 validate-success alert alert-success" id="property-info-text"></div>
                </div>
                <?php wp_nonce_field('aqar_isvalid_api', 'aqar_isvalid_api'); ?>
                <!-- <input type="hidden" name="action" value="aqar_isvalid_api"/> -->
                <div class="d-flex justify-content-between add-new-listing-bottom-nav-wrap">
                <a href="<?php echo esc_url($cancel_link ); ?>" class="btn-cancel btn btn-primary-outlined">
                    <?php echo houzez_option('fal_cancel', esc_html__('Cancel', 'houzez')); ?>    
                </a>
                <button id="check_aqar_isvalid" type="submit" class="btn houzez-submit-js btn-primary houzez-hidden">
                        <?php get_template_part('template-parts/loader'); ?>
                        <?php echo houzez_option('fal_submit_property', esc_html__('Submit Property', 'houzez') ); ?>        
                </button>
                </div>
            </form>
            <script>
                jQuery(document).ready(function($){	

                    $('#check_aqar_isvalid').prop('disabled', true);
                    $('#check_aqar_isvalid').on('click', function(e){
                        e.preventDefault();
                        var currnt = $(this);
                        var form  = currnt.parents('form');
                        // introduce the validation rules to the form! 
                        aqar_isvalid(currnt);    
                    }); 
                    
                    $('#get_ad_info').on('click', function(e){
                        e.preventDefault();
                        var currnt = $(this);
                        var form  = currnt.parents('form');
                        // introduce the validation rules to the form! 
                        get_ad_info(currnt);    
                    })

                    var aqar_isvalid = function( currnt ) {
                        var $Message = $('#errors-messages');
                        var $success_messages = $('#success-messages');
                        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                        var formValue=new FormData(document.getElementById('submit_aqar_isvalid_form')); 
                        formValue.append("action", "aqar_isvalid_api"); 
                        $.ajax({
                            type: 'post',
                            url: ajaxurl,
                            dataType: 'json',
                            cache: false,
                            processData: false, 
                            contentType: false, 
                            data: formValue,
                            beforeSend: function( ) {
                                form_valid($('#submit_aqar_isvalid_form'));
                                currnt.find('.houzez-loader-js').addClass('loader-show');
                                $Message.addClass('houzez-hidden');
                                $success_messages.addClass('houzez-hidden');
                            },
                            complete: function(){
                                currnt.find('.houzez-loader-js').removeClass('loader-show');
                            },
                            success: function( response ) {
                                if( response.success ) {
                                    $success_messages.removeClass('houzez-hidden');
                                    $success_messages.find('#messages').empty().append(response.reason);
                                    window.setTimeout( function(){
                                    window.location = response.redir;
                                    }, 1000 );               
                                } else {
                                    $Message.removeClass('houzez-hidden');
                                    $Message.find('#messages').empty().append(response.reason);
                                }
                                currnt.find('.houzez-loader-js').removeClass('loader-show');
                            },
                            error: function(xhr, status, error) {
                                var err = eval("(" + xhr.responseText + ")");
                                console.log(err.Message);
                            }
                        });
                    
                    } // end aqar_isvalid

                    var get_ad_info = function( currnt ) {
                        var $Message = $('#errors-messages');
                        var $success_messages = $('#success-messages');
                        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                        var formData=new FormData(document.getElementById('submit_aqar_isvalid_form')); 
                        formData.append("action", "get_ad_info");  

                        $.ajax({
                            type: 'post',
                            url: ajaxurl,
                            dataType: 'json',
                            cache: false,
                            processData: false, 
                            contentType: false, 
                            data: formData,
                            beforeSend: function( ) {
                                form_valid($('#submit_aqar_isvalid_form'));
                                currnt.find('.houzez-loader-js').addClass('loader-show');
                                $Message.addClass('houzez-hidden');
                                $success_messages.addClass('houzez-hidden');
                                $("#property-info").hide();
                            },
                            complete: function(){
                                currnt.find('.houzez-loader-js').removeClass('loader-show');
                            },
                            success: function( response ) {
                                if( response.success ) {
                                    $('#check_aqar_isvalid').removeClass('houzez-hidden');
                                    $('#check_aqar_isvalid').prop('disabled', false);
                                    $("#property-info").show();
                                    $('html, body').animate({
                                        scrollTop: $("#property-info-text").offset().top
                                    }, 1000);
                                    $('#property-info-text').empty().append(response.reason);              
                                } else {
                                    $Message.removeClass('houzez-hidden');
                                    $Message.find('#messages').empty().append(response.reason);
                                    document.getElementById('property-info-text').value = response.reason;              
                                }
                                currnt.find('.houzez-loader-js').removeClass('loader-show');
                            },
                            error: function(xhr, status, error) {
                                var err = eval("(" + xhr.responseText + ")");
                                console.log(err.Message);
                            }
                        });
                    
                    } // get_ad_info
                });

                let form_valid = function(form){
                    form.validate({
                            rules:{
                                adLicenseNumber:{
                                    required: true,
                                    minlength: 4,
                                }
                            },
                            messages:{
                                adLicenseNumber:{
                                    required: " *  رقم ترخيص الاعلان مطلوب",
                                    minlength: "يجب ألا يقل رقم ترخيص الإعلان عن 5 أرقام",
                                }
                            }
                        });
                }
                
            
            </script>
        </div>
        <?php     
    }

    
    public function aqar_isvalid_api()
    {
        $nonce = isset($_POST['aqar_isvalid_api']) ? $_POST['aqar_isvalid_api'] : '';
        if ( ! wp_verify_nonce( $nonce, 'aqar_isvalid_api' ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Security check failed!', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        $adLicenseNumber = isset($_POST['adLicenseNumber']) ? $_POST['adLicenseNumber'] : '';
        $advertiserId    = isset($_POST['advertiserId']) ? $_POST['advertiserId'] : '';
        $idType          = isset($_POST['idType']) ? $_POST['idType'] : 1 ;

        if ( empty( $adLicenseNumber ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'رقم ترخيص الإعلان  مطلوب', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        // if ( empty( $advertiserId ) ) {
        //     $ajax_response = array( 'success' => false , 'reason' => esc_html__( ' رقم هوية المعلن  مطلوب', 'houzez' ) );
        //     echo wp_send_json( $ajax_response );
        //     wp_die();
        // }

        require_once ( AG_DIR . 'module/class-rega-module.php' );
        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->AdvertisementValidator($adLicenseNumber);
        $response = json_decode( $response );

        if( $response->Body->result->isValid == true ){
            
            // في حالة رقم العلن موجود بالفعل اظهار رسالة بذلك
            $repeat_prop = get_option( '_repeat_prop' );
            if( $repeat_prop != 'yes' ) {
                $deedNumber = $response->Body->result->advertisement->deedNumber;
                $search_deedNumber_property = $this->search_deedNumber_property($deedNumber);
    
                if( $search_deedNumber_property ) {
                    $ajax_response = array( 'success' => false , 'reason' => 'رقم الاعلان موجود بالفعل !' );
                    echo wp_send_json( $ajax_response );
                    wp_die(); 
                }
                
            }

            // اضافة الاعلان
            $property_id = $this->add_property_advertisement($response->Body->result->advertisement);
            if( $property_id > 0 ) {
                $submit_link   = houzez_dashboard_add_listing();
                $edit_link     = add_query_arg( 'edit_property', $property_id, $submit_link );
                $ajax_response = array( 
                    'success' => true,
                    'reason'  => 'تم نشر الاعلان بنجاح -> ' . $property_id . ' سوف يتم اعادة توجيهك الي الاعلان ... ',
                    'redir'   => $edit_link
                );
                echo wp_send_json( $ajax_response );
                wp_die();
            } else {
                $ajax_response = array( 'success' => false , 'reason' => 'لم يتم اضافة الاعلان !');
                echo wp_send_json( $ajax_response );
                wp_die();
            }
            
        }else{
            $ajax_response = array( 
                'success' => false , 
                'reason' => 'هنالك مشكلة في الاتصال مع هيئة العقار'
            );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        
    }

    public function get_ad_info()
    {
        $nonce = isset($_POST['aqar_isvalid_api']) ? $_POST['aqar_isvalid_api'] : '';
        if ( ! wp_verify_nonce( $nonce, 'aqar_isvalid_api' ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Security check failed!', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        $adLicenseNumber = isset($_POST['adLicenseNumber']) ? $_POST['adLicenseNumber'] : '';
        $advertiserId    = isset($_POST['advertiserId']) ? $_POST['advertiserId'] : '';
        $idType          = isset($_POST['idType']) ? $_POST['idType'] : 1 ;


        if ( empty( $adLicenseNumber ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'رقم ترخيص الإعلان  مطلوب', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }


        require_once ( AG_DIR . 'module/class-rega-module.php' );

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->AdvertisementValidator( $adLicenseNumber );
        $response = json_decode( $response );
        
        // الكلمة هنا موجودة
        // var_export($response);
         
  
        if( $response->Header->Status->Code != 200  ) {  
            $msg = 'هنالك مشكلة في الاتصال مع هيئة العقار';
            if( isset($response->Body->error->message) ) {
                $msg = $response->Body->error->message;
            } 
            $ajax_response = array( 'success' => false , 'reason' => $msg );
            echo wp_send_json( $ajax_response );
            wp_die();
        }else{
            $translate = [
                'advertiserId' => 'المعلن',
                'adLicenseNumber' => 'رقم الترخيص',
                'deedNumber' => 'رقم الاعلان',
                'advertiserName' => 'اسم المعلن',
                'phoneNumber' => 'رقم التليفون',
                'brokerageAndMarketingLicenseNumber' => 'الوساطة والتسويق',
                'isConstrained' => 'مقيد',
                'isPawned' => 'مرهونة',
                'streetWidth' => 'عرض الشارع',
                'propertyArea' => 'منطقة',
                'propertyPrice' => 'أسعار العقارات',
                'numberOfRooms' => 'عدد الغرف',
                'propertyType' => 'نوع الملكية',
                'propertyAge' => 'العمر',
                'advertisementType' => 'نوع الإعلان',
                'location' => 'موقع',
                'region' => 'منطقة',
                'city' => 'مدينة',
                'district' => 'الحي',
                'buildingNumber' => 'رقم المبنى',
                'longitude' => 'خط الطول',
                'latitude' => 'خط العرض',
                "regionCode" => "كود المنطقة",
                "cityCode" => "كود المدينة",
                "districtCode" => "كود الحي",
                "street" => "الشارع",
                "postalCode" => "الرمز البريدي",
                "buildingNumber" => "رقم المبني",
                "additionalNumber" => "رقم اضافي",
                'propertyFace' => 'واجهة العقار',
                'planNumber' => 'رقم المخطط',
                'obligationsOnTheProperty' => 'الالتزامات على الممتلكات',
                'guaranteesAndTheirDuration' => 'الضمانات والمدة الزمنية',
                'theBordersAndLengthsOfTheProperty' => 'حدود الملكية وأطوالها',
                'complianceWithTheSaudiBuildingCode' => 'الامتثال لكود البناء السعودي',
                'propertyUsages' => 'استخدام العقار',
                'propertyUtilities' => 'خدمات العقار',
                'channels' => 'قنوات الاعلان ',
                'creationDate' => 'تاريخ إنشاء ترخيص الاعلان ',
                'endDate' => 'تاريخ انتهاء ترخيص الاعلان',
                'qrCodeUrl' => 'رابط رمز الاستجابة السريع',
            ];
            $data = [];
            if( isset($response->Body->result->advertisement) ) {

                // في حالة رقم العلن موجود بالفعل اظهار رسالة بذلك
                $repeat_prop = get_option( '_repeat_prop' );
                if( $repeat_prop != 'yes' ) {
                    $deedNumber = $response->Body->result->advertisement->deedNumber;
                    $search_deedNumber_property = $this->search_deedNumber_property($deedNumber);
        
                    if( $search_deedNumber_property ) {
                        $ajax_response = array( 'success' => false , 'reason' => 'رقم الاعلان موجود بالفعل !' );
                        echo wp_send_json( $ajax_response );
                        wp_die(); 
                    }    
                }
                
                foreach( $response->Body->result->advertisement as $key => $advertisement ) {
                    if( $key == 'location' 
                      ){
                        foreach ($advertisement as $k => $v) {
                            $data[]= $translate[$k] . ' : '  . $v . '<br>';
                        }
                    }else if (
                        $key == 'propertyUsages' ||
                        $key == 'propertyUtilities' ||
                        $key == 'channels'
                    ){ 
                        $data[]= $translate[$key] . ' : '  . $advertisement[0] . '<br>';
                    }else{
                        $data[]= $translate[$key] . ' : '  . $advertisement . '<br>';
                    }
                }
            }
            $ajax_response = array( 'success' => true , 'reason' => $data );
            echo wp_send_json( $ajax_response );
            wp_die();
        }
    }

    public function add_property_advertisement($data)
    {
        $prop_id  = '';
        $userID = get_current_user_id();
        $listings_admin_approved = houzez_option('listings_admin_approved');
        $edit_listings_admin_approved = houzez_option('edit_listings_admin_approved');
        $enable_paid_submission = houzez_option('enable_paid_submission');
        $new_property = [];
        $new_property['post_type'] = 'property';
        $new_property['post_author'] = $userID;

        // Title (the post_title could be empty if you use post_name instead);
        $new_property['post_title'] = '';
        
        // $new_property['post_name'] = isset($data->deedNumber) ? 'property-' . $data->deedNumber : 'new-property';
 
        if( houzez_is_admin() ) {
            $new_property['post_status'] = 'draft';
        } else {
            if( $listings_admin_approved != 'yes' && ( $enable_paid_submission == 'no' || $enable_paid_submission == 'free_paid_listing' || $enable_paid_submission == 'membership' ) ) {
                if( $user_submit_has_no_membership == 'yes' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'draft';
                }
            } else {
                if( $user_submit_has_no_membership == 'yes' && $enable_paid_submission = 'membership' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'pending';
                }
            }
        }


            /*
            * Filter submission arguments before insert into database.
            */
            
            $new_property = apply_filters( 'houzez_before_submit_property', $new_property );

            add_filter( 'wp_insert_post_empty_content', '__return_false' );

            $prop_id = wp_insert_post( $new_property );

            if( $prop_id > 0 ) {
                $submitted_successfully = true;
                if( $enable_paid_submission == 'membership'){ // update package status
                    houzez_update_package_listings( $userID );
                }
            }
  
            // Add price post meta
            if( isset( $data->propertyPrice ) ) {
                update_post_meta( $prop_id, 'fave_property_price', $data->propertyPrice  );
            }

            // Add property type
            if( isset( $data->propertyType ) && ( $data->propertyType != '' ) ) {
                $type = $data->propertyType;
                wp_set_object_terms( $prop_id, $type, 'property_type' );
            } else {
                wp_set_object_terms( $prop_id, '', 'property_type' );
            }

            // Add property status
            if( isset( $data->advertisementType ) && ( $data->advertisementType != '' ) ) {
                $prop_status = $data->advertisementType;
                wp_set_object_terms( $prop_id, $prop_status, 'property_status' );
            } else {
                wp_set_object_terms( $prop_id, '', 'property_status' );
            }

            // Postal Code
            if( isset( $data->location->postalCode ) ) {
                update_post_meta( $prop_id, 'fave_property_zip', $data->location->postalCode );
            }

            

            if( isset($data->streetWidth) ) {
                update_post_meta( $prop_id, 'fave_d8b9d8b1d8b6-d8a7d984d8b4d8a7d8b1d8b9', $data->streetWidth );
            }

            //property-age

            if( isset( $data->propertyAge ) ) {
                update_post_meta( $prop_id, 'fave_property_year', $data->propertyAge );

            }
            
            if( isset($data->numberOfRooms) ) {
                update_post_meta( $prop_id, 'fave_property_rooms', $data->numberOfRooms );
            }

            $state_id = [];
            // Add property state
            if( isset( $data->location->region ) ) {
                $property_state = $data->location->region;
                $state_id = wp_set_object_terms( $prop_id, $property_state, 'property_state' );
            }

            $city_id = [];
            // Add property city
            if( isset( $data->location->city ) ) {
                $property_city = $data->location->city;
                $city_id = wp_set_object_terms( $prop_id, $property_city, 'property_city' );
                $term_object = get_term( $state_id[0] );
                $parent_state = $term_object->slug;
                $houzez_meta = array();
                $houzez_meta['parent_state'] = $parent_state;
                if( !empty( $city_id) && !empty($houzez_meta['parent_state'])  ) {
                    update_option('_houzez_property_city_' . $city_id[0], $houzez_meta);
                }
            }
  

            $area_id = [];
            // Add property area
            if( isset( $data->location->district ) ) {
                $property_area = sanitize_text_field( $data->location->district );
                $area_id = wp_set_object_terms( $prop_id, $property_area, 'property_area' );
                $term_object = get_term( $city_id[0] );
                $parent_city = $term_object->slug;
                $houzez_meta = array();
                $houzez_meta['parent_city'] = $parent_city;
                if( !empty( $area_id) && !empty($houzez_meta['parent_city'])  ) {
                    update_option('_houzez_property_area_' . $area_id[0], $houzez_meta);
                }
            }

            

            
           

            
            //prop_size 
            if( isset( $data->propertyArea ) ) {
                update_post_meta( $prop_id, 'fave_property_size', $data->propertyArea );
            }

            if( isset( $data->planNumber ) ){
                update_post_meta( $prop_id, 'fave_d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1', $data->planNumber );
            }
             
            // سعر متر البيع
            //d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9
            if( isset( $data->propertyPrice ) ){
                update_post_meta( $prop_id, 'fave_d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9', $data->propertyPrice );
            }
            
            // obligationsOnTheProperty
            if( isset( $data->obligationsOnTheProperty ) ){
                update_post_meta( $prop_id, 'fave_d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985', $data->obligationsOnTheProperty );
            }

            // Address
            if( isset( $data->location->street ) || isset( $data->location->buildingNumber ) ) {
                $adress = $data->location->region .' ,'.$data->location->city . ' ,' . $data->location->district . ' ,' . $data->location->street . ' ,' .$data->location->buildingNumber; 
                update_post_meta( $prop_id, 'fave_property_map_address', $adress );
                update_post_meta( $prop_id, 'fave_property_address', $adress );
            }

            // Land Area Size
            if( isset( $data->propertyArea ) ) {
                update_post_meta( $prop_id, 'fave_property_land', $data->propertyArea );
            }
            
            if( isset($data->propertyFace) ) {
                update_post_meta( $prop_id, 'fave_d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1', $data->propertyFace );
            }
            
            // lat & long
            if( ( isset($data->location->latitude) && !empty($data->location->latitude) ) && (  isset($data->location->longitude) && !empty($data->location->latitude)  ) ) {
                $lat = $data->location->latitude;
                $lng = sanitize_text_field( $data->location->longitude );
                $streetView = '';
                $lat_lng = $lat.','.$lng;

                update_post_meta( $prop_id, 'houzez_geolocation_lat', $lat );
                update_post_meta( $prop_id, 'houzez_geolocation_long', $lng );
                update_post_meta( $prop_id, 'fave_property_location', $lat_lng );
                update_post_meta( $prop_id, 'fave_property_map', '1' );
                update_post_meta( $prop_id, 'fave_property_map_street_view', $streetView );
            }

            update_post_meta( $prop_id, 'advertiserId', $data->advertiserId );
            update_post_meta( $prop_id, 'adLicenseNumber', $data->adLicenseNumber );
            update_post_meta( $prop_id, 'deedNumber', $data->deedNumber );
            update_post_meta( $prop_id, 'TitleDeed', $data->deedNumber ); 
 
            /*---------------------------------------------------------------------------------*
            * Save expiration meta 
            *----------------------------------------------------------------------------------*/
            update_post_meta( $prop_id, 'creationDate', $data->creationDate );
            update_post_meta( $prop_id, 'endDate', $data->endDate );
            update_post_meta( $prop_id, 'houzez_manual_expire', 1 );

            $options = array();
            $timestamp = get_gmt_from_date($data->endDate,'U');

            // Schedule/Update Expiration
            $options['id'] = $prop_id;

            _houzezScheduleExpiratorEvent( $prop_id, $timestamp, $options );
            /*---------------------------------------------------------------------------------*
            * End expiration meta
            *----------------------------------------------------------------------------------*/

            if( isset( $data->obligationsOnTheProperty ) ) {
                update_post_meta( $prop_id, 'fave_d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8a7d984d8a3d8aed8b1d989-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1', $data->obligationsOnTheProperty  );
            }
            if( isset( $data->guaranteesAndTheirDuration ) ) {
                update_post_meta( $prop_id, 'fave_d8a7d984d8b6d985d8a7d986d8a7d8aa-d988d985d8afd8aad987d8a7', $data->guaranteesAndTheirDuration );
            }
            if( isset( $data->theBordersAndLengthsOfTheProperty ) ) {
               
            }
            if( isset( $data->complianceWithTheSaudiBuildingCode ) ) {
                update_post_meta( $prop_id, 'fave_d985d8b7d8a7d8a8d982d8a9-d983d988d8af-d8a7d984d8a8d986d8a7d8a1-d8a7d984d8b3d8b9d988d8afd98a', $data->complianceWithTheSaudiBuildingCode );
            }
            if( isset( $data->complianceWithTheSaudiBuildingCode ) ) {
                update_post_meta( $prop_id, 'fave_d985d8b7d8a7d8a8d982d8a9-d983d988d8af-d8a7d984d8a8d986d8a7d8a1-d8a7d984d8b3d8b9d988d8afd98a', $data->complianceWithTheSaudiBuildingCode );
            }
            if( isset( $data->propertyUtilities ) ) {
                if( is_array($data->propertyUtilities) ) {
                    foreach( $data->propertyUtilities as $propertyUtiliti ) {
                        update_post_meta( $prop_id, 'fave_d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1', $propertyUtiliti );
                    }
                }
            }
            if( isset( $data->channels ) ) {
                if( is_array($data->channels) ) {
                    foreach( $data->channels as $channel ) {
                        update_post_meta( $prop_id, 'fave_d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986', $channel );
                    }
                }
            }

             //prop_labels
             if( isset( $data->propertyUsages ) ) {
                if( is_array($data->propertyUsages) ) {
                    foreach( $data->propertyUsages as $propertyUsage ) {
                        wp_set_object_terms( $prop_id, $propertyUsage, 'property_label' );
                    }
                }
            }
            

        return $prop_id;
    }
    
    /**
     * search_deedNumber_property
     *
     * @param  mixed $deedNumber
     * @return void
     */
    public function search_deedNumber_property($deedNumber)
    {
        global $wpdb;
        if( empty($deedNumber) ) {
            return false;
        }
        
        $hasDeedNumber = $wpdb->get_results("select * from $wpdb->postmeta where meta_key='deedNumber' and meta_value='$deedNumber'");

        if ( count($hasDeedNumber) == 0 ) {
            return false;
        }

        return true;

    }
}
new PropertyMoudle();