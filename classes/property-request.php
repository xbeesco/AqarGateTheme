<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class PropertyRequest {

    public function __construct() {
        add_action('init', array($this, 'create_post_type'));
        add_action('carbon_fields_register_fields', array($this, 'add_custom_fields'));
        add_action('after_setup_theme', array($this, 'crb_load'));
        add_action('after_setup_theme', array($this,'create_custom_table'));
        add_action('wp_ajax_handle_property_request_submission', array($this, 'handle_property_request_submission'));
    }

    public function create_post_type() {
        register_post_type('property_request',
            array(
                'labels'      => array(
                    'name'          => __('طلبات العقارات'),
                    'singular_name' => __('طلب عقار'),
                ),
                'public'        => true,
                'has_archive'   => true,
                'rewrite'       => array('slug' => 'property-request'),
                'supports'      => array('title', 'editor'),
                'menu_position' => 5,
                'show_in_rest'  => true,
            )
        );
    }

    public function add_custom_fields() {
        Container::make('post_meta', 'تفاصيل الطلب')
            ->where('post_type', '=', 'property_request')
            ->add_fields(array(
                Field::make('select', 'property_request', 'نوع الطلب')
                    ->add_options(array(
                        'sell' => 'شراء',
                        'rent' => 'إيجار',
                    ))->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'prop_type', 'نوع العقار')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('select', 'prop_age', 'عمر العقار')
                    ->add_options(array(
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
                        'more-than-ten' => 'اكثر من عشر سنوات',
                    ))->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'state', 'المنطقة')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'city', 'المدينة')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'area', 'الحي')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'land_area', 'مساحة الأرض')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('text', 'price', 'السعر')->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('select', 'payment_method', 'طريقة الدفع')
                    ->add_options(array(
                        'cash' => 'نقدا',
                        'mortgage' => 'تمويل عقاري',
                    ))->set_width(50)->set_datastore(new CustomTableDatastore()),
                Field::make('textarea', 'more_info', 'تفاصيل أخرى')->set_width(100)->set_datastore(new CustomTableDatastore()),
                Field::make('hidden', 'user_id', '')->set_datastore(new CustomTableDatastore()),
            ));
    }

    public function crb_load() {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function handle_property_request_submission() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'property_requests';

        $form_data = isset($_POST['form_data']) ? urldecode($_POST['form_data']) : '';
        mb_parse_str($form_data, $unserialized_data);

        $property_request = sanitize_text_field($unserialized_data['property-request']);
        $prop_type_id = intval($unserialized_data['prop_type']);
        $prop_age = sanitize_text_field($unserialized_data['prop_age']);
        $country = sanitize_text_field($unserialized_data['country']);
        $administrative_area_level_1 = sanitize_text_field($unserialized_data['administrative_area_level_1']);
        $locality = sanitize_text_field($unserialized_data['locality']);
        $neighborhood = sanitize_text_field($unserialized_data['neighborhood']);
        $land_area = sanitize_text_field($unserialized_data['land-area']);
        $price = sanitize_text_field($unserialized_data['price']);
        $payment_method = sanitize_text_field($unserialized_data['payment-method']);
        $more_info = sanitize_textarea_field($unserialized_data['more-info']);
        $user_id = sanitize_text_field($unserialized_data['user-id']);

        $user_property_request = houzez_get_template_link_2('template/user_property_request.php');

        // Get property type name from term ID
        $prop_type_term = get_term($prop_type_id);
        $prop_type_name = $prop_type_term ? $prop_type_term->name : '';

        // Remove numbers from administrative area level 1
        $state = preg_replace('/[0-9]+/', '', $administrative_area_level_1);
        $state = str_replace('-', ' ', $state);

        $city = preg_replace('/[0-9]+/', '', $locality);
        $city = str_replace('-', ' ', $city);

        $property_request_name = ($property_request === 'sell') ? 'شراء' : 'للايجار';
        // Construct post title
        $post_title = "طلب $prop_type_name / $property_request_name / $state";

        if( isset($unserialized_data['action']) && $unserialized_data['action'] === 'edit') {
            $post_id = $unserialized_data['post_id'] ;

            $data = [
                'property_request' => $property_request,
                'prop_type' => $prop_type_name,
                'prop_age' => $prop_age,
                'state' => $administrative_area_level_1,
                'city' => $locality,
                'area' => $neighborhood,
                'land_area' => $land_area,
                'price' => $price,
                'payment_method' => $payment_method,
                'more_info' => $more_info,
                'user_id' => $user_id,
            ];
            $where = ['post_id' => $post_id];
            
            $wpdb->update($table_name, $data, $where);

            wp_send_json(["sucsses" => true, "html" => ' عزيزنا العميل تم تعديل طلبكم . وسيتم استقبال جميع العروض بواسطة طرق الاتصال في حسابكم.', 'redirect' => $user_property_request]);
            wp_die();
            
        } else {
            
            $post_id = wp_insert_post([
                'post_type' => 'property_request',
                'post_title' => $post_title,
                'post_status' => 'publish',
                'post_author' => $user_id,
            ]);
    
            if ( $post_id ) {
                // carbon_set_post_meta($post_id, 'property_request', $property_request);
                // carbon_set_post_meta($post_id, 'prop_type', $prop_type_name);
                // carbon_set_post_meta($post_id, 'prop_age', $prop_age);
                // carbon_set_post_meta($post_id, 'land_area', $land_area);
                // carbon_set_post_meta($post_id, 'price', $price);
                // carbon_set_post_meta($post_id, 'payment_method', $payment_method);
                // carbon_set_post_meta($post_id, 'more_info', $more_info);
                // carbon_set_post_meta($post_id, 'user_id', $user_id);
                // carbon_set_post_meta($post_id, 'state', $state);
                // carbon_set_post_meta($post_id, 'city', $city);
                // carbon_set_post_meta($post_id, 'area', $neighborhood);
    
                // Insert into custom table
                $wpdb->insert($table_name, array(
                    'post_id' => $post_id,
                    'property_request' => $property_request,
                    'prop_type' => $prop_type_name,
                    'prop_age' => $prop_age,
                    'state' => $administrative_area_level_1,
                    'city' => $locality,
                    'area' => $neighborhood,
                    'land_area' => $land_area,
                    'price' => $price,
                    'payment_method' => $payment_method,
                    'more_info' => $more_info,
                    'user_id' => $user_id
                ));
                wp_send_json(["sucsses" => true, "html" => ' عزيزنا العميل تم نشر طلبكم . وسيتم استقبال جميع العروض بواسطة طرق الاتصال في حسابكم.', 'redirect' => $user_property_request]);
                wp_die();
            } else {
                wp_send_json(["sucsses" => false, "html" =>'حدث خطأ أثناء إرسال الطلب']);
                wp_die();
            }
        }
    }

    function create_custom_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'property_requests';
        $charset_collate = $wpdb->get_charset_collate();
    
        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                post_id mediumint(9) NOT NULL,
                property_request varchar(255) NOT NULL,
                prop_type varchar(255) NOT NULL,
                prop_age varchar(255) NOT NULL,
                state varchar(255) NOT NULL,
                city varchar(255) NOT NULL,
                area varchar(255) NOT NULL,
                land_area varchar(255) NOT NULL,
                price varchar(255) NOT NULL,
                payment_method varchar(255) NOT NULL,
                more_info text NOT NULL,
                user_id mediumint(9) NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}

new PropertyRequest();