<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
class PushNotification
{

    private $username; // Replace with your username
    private $password; // Replace with your password

    private $allowed_ips = array('95.177.165.178', '95.177.213.114', '95.177.162.19'); // Add allowed IPs here

    private $prod = false; // Set to true for production

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /* --------------------- // Register the REST API route --------------------- */
    public function register_routes()
    {
        // prod
        register_rest_route('Brokerage', '/PushNotification', array(
            'methods' => ['POST', 'PUT'],
            'callback' => array($this, 'receive_push_notification'),
            'permission_callback' => array($this, 'check_authentication'),
        ));

        $this->username = get_option('_push_notification_user');
        $this->password = get_option('_push_notification_pass');

    }
    /* ---------------------- // Check Basic Authentication --------------------- */
    public function check_authentication()
    {

        //? Check for Authorization header in both possible places
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '');
        list($type, $credentials) = explode(' ', $auth_header, 2) + array('', '');

        if (strtolower($type) != 'basic') {
            return new WP_Error('unauthorized', 'Invalid authentication type : ' . $type, array('status' => 401));
        }

        $decoded_credentials = base64_decode($credentials);
        list($username, $password) = explode(':', $decoded_credentials, 2) + array('', '');

        if ($username === $this->username && $password === $this->password) {
            return true;
        } else {
            return new WP_Error('unauthorized', 'Invalid credentials', array('status' => 401));
        }
    }



    /* --------------------- // Handle the push notification -------------------- */
    public function receive_push_notification(WP_REST_Request $request)
    {
        $log_data = [
            'status' => 'success',
            'headers' => $request->get_headers(),
            'query_params' => $request->get_query_params(),
            'body_params' => $request->get_json_params(),
            'route_params' => $request->get_params(),
            'raw_body' => $request->get_body(),
            'auth_user' => $_SERVER['PHP_AUTH_USER'] ?? null,
        ];

        // Create the logger instance
        $logger = new WC_Logger();

        // Log the data as a string
        $logger->add('PushNotification', 'LOG : ' . print_r($log_data, true));


        // Get all the data from the request body
        $notification_body = $request->get_json_params();

        if (empty($notification_body)) {
            return new WP_Error('no_data', 'Invalid or empty notification Body data', array('status' => 400));
        }


        // Extract the AdlicenseNumber from the NotificationBody
        $adLicenseNumber = $notification_body['NotificationBody']['AdlicenseNumber'] ?? null;

        if (empty($adLicenseNumber)) {
            return new WP_Error('adLicenseNumber-ERROR', 'Invalid or empty adLicenseNumber Body data', array('status' => 400));
        }

        // if the adLicenseNumber is test one 123456789, then return a test response
        if ($adLicenseNumber === '7994567891') {
            return rest_ensure_response(array(
                'IsReceived' => true,
                'Response' => 'Notification received and saved successfully',
                'adLicenseNumber' => $adLicenseNumber,
                'ResponseTime' => date('Y-m-d\TH:i:s'),
            ));
        }

        $saved = false;
        if ($adLicenseNumber) {
            $postStatus = 'draft';
            // Get the property ID using the AdlicenseNumber
            $property_id = $this->get_property_id_by_adLicenseNumber($adLicenseNumber);
            $delete = false;
            if ($property_id) {
                // Optionally, you could include the property ID in the response or log it
                $notification_body['property_id'] = $property_id;
                // change the property status [draft - delete]
                if (!$delete) {
                    // Set post to draft
                    wp_update_post(array(
                        'ID' => $property_id,
                        'post_status' => $postStatus,
                    ));
                } elseif ($delete) {
                    // Delete the post
                    wp_delete_post($property_id, true);
                }
                update_post_meta($property_id, 'notification_body', $notification_body);
                $saved = true;
            }
            // Retrieve existing notifications
            $existing_notifications = get_option('brokerage_notifications', array());
            if (!is_array($existing_notifications)) {
                $existing_notifications = array();
            }
            // Add the new notification to the array
            $existing_notifications[] = $notification_body;

            // Save the updated array back to the database
            update_option('brokerage_notifications', $existing_notifications);
        }


        if ($saved) {
            return rest_ensure_response(array(
                'IsReceived' => true,
                'Response' => 'Notification received and saved successfully',
                'adLicenseNumber' => $adLicenseNumber,
                'ResponseTime' => date('Y-m-d\TH:i:s'),
                //'PropertyID' => $property_id
            ));
        } else {
            return new WP_REST_Response(
                array(
                    'IsReceived' => true,
                    'Response' => 'Business Error, This ad ' . $adLicenseNumber . ' is not available on our platform',
                    'adLicenseNumber' => $adLicenseNumber,
                    'ResponseTime' => date('Y-m-d\TH:i:s'),
                ),
                500
            );
        }
    }

    /* ------------ // Get the property ID using the AdlicenseNumber ------------ */
    public function get_property_id_by_adLicenseNumber($adLicenseNumber)
    {
        if (is_numeric($adLicenseNumber)) {
            global $wpdb;

            $post_id = $wpdb->get_var($wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
                'adLicenseNumber',
                $adLicenseNumber
            ));

            if ($post_id) {
                return $post_id;
            } else {
                 return null;
            }

        }

        return null;
    }

}

new PushNotification();

