<?php

/**
 * AqarGateApi
 */
class AqarGateApi {

    /**
	 * data routes
	 *
	 * @var array
	 */
	protected $routes = array(
		'property_post' => array(
			'path'                => '/properties/(?P<id>[\d]+)',
			'callback'            => 'get_property',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'property_search_pram' => array(
			'path'                => '/properties',
			'callback'            => 'get_properties',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'property_search_pram' => array(
			'path'                => '/user-properties',
			'callback'            => 'get_user_properties',
			'permission_callback' => 'user_properties_allow_access',
			'methods'             => 'GET',
		),
        'property_field' => array(
			'path'                => '/properties/main-fields',
			'callback'            => 'prop_type_fields',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'property_field_builder' => array(
			'path'                => '/properties/extra-fields',
			'callback'            => 'prop_type_fields_builder',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'property_category' => array(
			'path'                => '/properties/category',
			'callback'            => 'property_category',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'submit_property' => array(
			'path'                => '/properties/submit',
			'callback'            => 'submit_property',
			'permission_callback' => 'create_item_permissions_check',
			'methods'             => 'POST',
		),
        'delete_property' => array(
			'path'                => '/properties/delete/(?P<id>[\d]+)',
			'callback'            => 'delete_property',
			'permission_callback' => 'delete_item_permissions_check',
			'methods'             => WP_REST_Server::DELETABLE,
            'args'                => 'delete_property_args_schema'
		),
        'agency_post' => array(
			'path'                => '/agency/(?P<id>[\d]+)',
			'callback'            => 'get_agency',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),	
        'all_agency_posts' => array(
			'path'                => '/agency',
			'callback'            => 'get_agencies',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'property_state' => array(
			'path'                => '/state',
			'callback'            => 'get_state',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),	
        'property_city' => array(
			'path'                => '/city',
			'callback'            => 'get_cites',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),		
        'property_area' => array(
			'path'                => '/area',
			'callback'            => 'get_area',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'aqargate_login' => array(
			'path'                => '/login',
			'callback'            => 'login',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),		
        'aqargate_signup' => array(
			'path'                => '/signup',
			'callback'            => 'signup',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'aqargate_membership' => array(
			'path'                => '/membership',
			'callback'            => 'membership',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'favorite_properties' => array(
			'path'                => '/favorite_properties',
			'callback'            => 'favorite_properties',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'favorite_properties_add' => array(
			'path'                => '/favorite_properties/add',
			'callback'            => 'favorite_properties_add',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'favorite_properties_remove' => array(
			'path'                => '/favorite_properties/remove',
			'callback'            => 'favorite_properties_remove',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'aqargate_conversations' => array(
			'path'                => '/conversations',
			'callback'            => 'conversations',
			'permission_callback' => 'allow_access',
			'methods'             => 'GET',
		),
        'new_conversation' => array(
			'path'                => '/conversations/new',
			'callback'            => 'new_conversation',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'aqargate_conversations_messages' => array(
			'path'                => '/conversations/messages',
			'callback'            => 'conversations_messages',
			'permission_callback' => 'allow_access',
			'methods'             => WP_REST_Server::READABLE,
		),
        'new_conversations_messages' => array(
			'path'                => '/conversations/messages/new',
			'callback'            => 'new_conversation_message',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'aqargate_profile' => array(
			'path'                => '/profile/fields',
			'callback'            => 'profile_fields',
			'permission_callback' => 'allow_access',
			'methods'             => WP_REST_Server::READABLE,
		),
        'aqargate_profile_update' => array(
			'path'                => '/profile/update',
			'callback'            => 'profile_update',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		),
        'aqargate_role' => array(
			'path'                => '/user/role',
			'callback'            => 'user_role',
			'permission_callback' => 'allow_access',
			'methods'             => WP_REST_Server::READABLE,
		),
        'aqargate_info' => array(
			'path'                => '/siteinfo',
			'callback'            => 'siteinfo',
			'permission_callback' => 'allow_access',
			'methods'             => WP_REST_Server::READABLE,
		),
        
	);	

    /**
	 * Version of the API
	 *
	 * @see set_version()
	 * @see get_version()
	 * @var string
	 */
	protected $version = '1';

	/**
	 * Vendor slug for the API
	 *
	 * @see set_vendor()
	 * @see get_vendor()
	 * @var string
	 */
	protected $vendor = 'aqargate';
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes'], 15 );
        add_action( 'rest_api_init', [ $this, 'load_controllers' ] ) ;
        add_action( 'saved_term', [ $this, 'tax_last_update' ], 20, 4);
    }
    
        
    /**
     * load_controllers
     *
     * @return void
     */
    public function load_controllers()
    {
        include_once ( 'api-fields-controller.php' );
        include_once ( 'api-prop-controller.php' );
        include_once ( 'api-agency-controller.php' );
        include_once ( 'api-register-controller.php' );
        include_once ( 'api-membership-controller.php' );
    }

    /**
	 * Set version
	 *
	 * @param string $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

	/**
	 * Return version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Set vendor
	 *
	 * @param string $vendor
	 */
	public function set_vendor( $vendor ) {
		$this->vendor = $vendor;
	}

	/**
	 * Return vendor
	 *
	 * @return string
	 */
	public function get_vendor() {
		return $this->vendor;
	}

	/**
	 * Allow access to an endpoint
	 *
	 * @return bool
	 */
	public function allow_access() {
		return true;
	}

    /**
	 * Set routes
	 *
	 * @param array $routes
	 */
	public function set_routes( $routes ) {
		$this->routes = $routes;
	}

	/**
	 * Return routes
	 *
	 * @return array
	 */
	public function get_routes() {
		return $this->routes;
	}

    /**
	 * Register custom routes
	 *
	 * @see  register_route()
	 */
	public function register_routes() {
		foreach ( $this->routes as $route ) {
			$this->register_route( $route );
		}
	}

	/**
	 * Register a custom REST route
	 *
	 * @param  array $route
	 */
	protected function register_route( $route ) {
		register_rest_route($this->get_vendor() . '/v' . $this->get_version(), $route['path'], array(
			'methods'             => $route['methods'],
            'permission_callback' => array( $this, $route['permission_callback'] ),
			'callback'            => array( $this, $route['callback'] ),
			'args'                => isset( $route['args'] ) ? call_user_func( array( $this, $route['args'] ) ) : array(),
		) );
	}


    
    /**
     * error_response
     *
     * @param  mixed $error_code
     * @param  mixed $error_message
     * @return void
     */
    public function error_response( $error_code, $error_message = '' )
    {
        $msgs = array(
            '2000' => 'No Properties Found',
            '2001' => 'No Correct data collection provided',
            '2002' => 'No Correct property id',
        );

        return array(
            'error_code' => $error_code,
            'message' => empty( $error_message ) ? $msgs[$error_code] :  $error_message,
        );

    }
    
    /**
     * response
     *
     * @param  mixed $date
     * @return void
     */
    public function response( $date )
    {
        return new WP_REST_Response(
            array( 'data' => $date )
        );
    }

    
    /**
     * prop_type_fields
     *
     * @param  mixed $data
     * @return void
     */
    public function prop_type_fields( WP_REST_Request $request )
    {
        $fields = ag_get_property_fields();

        // $prop_type_enabled_fields = carbon_field_value(); 


        // foreach ($prop_type_enabled_fields as $key => $value) {
            
        // }
        $response_data = $fields;

        return $this->response( $response_data );

    }

    /**
     * prop_type_fields_builder
     *
     * @param  mixed $data
     * @return void
     */
    public function prop_type_fields_builder( WP_REST_Request $request )
    {
        if( !isset( $_GET['tax_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing Parameter(s) tax_id'  )
            );
        }
        $fields = ag_get_property_fields_builder();

        $response_data = $fields;

        return $this->response( $response_data );

    }


    
   
    /**
     * data_collections
     *
     * @return array
     */
    public function data_collections()
    {
        return array('list', 'popup-search', 'search', 'property');
    }
    
    /**
     * is_property
     *
     * @param  mixed $prop_id
     * @return true/false
     */
    public function is_property( $prop_id = null )
    {
        global $post;   
        $post = $prop_id;
        setup_postdata( $post ); 
          if(  get_post_type( get_the_ID()  ) === 'property' ) { $is_prop = true; }
          else { $is_prop = false; }
        wp_reset_postdata();
        return $is_prop;
    }    
    /**
     * is_agency
     *
     * @param  mixed $agency_id
     * @return true/false
     */
    public function is_agency( $agency_id = null )
    {
        global $post;   
        $post = $agency_id ;
        setup_postdata( $post ); 
          if(  get_post_type( get_the_ID()  ) === 'houzez_agency' ) { $is_agency = true; } 
          else {  $is_agency = false; }
        wp_reset_postdata();
        return $is_agency;
    }

     /**
	 * get_property
	 *
	 * @param  array $data
	 * @return array
	 */
	public function get_property( WP_REST_Request $data ) {
     
        if( !isset( $data['data_collection'] ) ||  !in_array( $data['data_collection'] ,  $this->data_collections() )  ){
            return $this->error_response ( '2001');
        }

        if( !isset( $data['id'] ) ){
            return $this->error_response ( '2000');
        }
        
        if(  $this->is_property( $data['id'] ) === false ){
            return $this->error_response ( '2002');
        }

        $response_data = get_prop_data( $data['id'] , $data['data_collection']  );

        return $this->response($response_data);
	}

    
   
    /**
	 * get_property
	 *
	 * @param  array $data
	 * @return array
	 */
	public function get_properties( WP_REST_Request $data ) {

        if(!isset( $data['data_collection'] ) ||  !in_array( $data['data_collection'] ,  $this->data_collections() )  ){
            $this->error_response( '2001' );
        }
       
        $properties_data = [];

        $number_of_prop = isset($_GET['limit']) ? ( $_GET['limit'] ) : 8;
		if(!$number_of_prop){
		    $number_of_prop = 8;
		}

        $paged = isset($_GET['paged']) ? ($_GET['paged']) : '1';

        $search_qry = array(
            'post_type' => 'property',
            'posts_per_page' => $number_of_prop,
            'paged' => $paged,
            'post_status' => 'publish'
        );

       if( isset($data['me']) && $data['me'] === 'yes' ){
           $current_user_id = get_current_user_id();
           if( !empty( $current_user_id ) ){
              $search_qry['author'] = $current_user_id ;
           }else {
            return new WP_Error(
                'jwt_auth_no_auth_header',
                'Authorization header not found.',
                array(
                    'status' => 403,
                )
            );
           }
       }
                
        $_tax_query = Array();

        $_tax_query['relation'] = 'OR';

        if( !empty( $_GET['area'] ) && isset($_GET['area'])){
            $_tax_query[] = array(
                'taxonomy' => 'property_area',
                'field' => 'slug',
                'terms' => $_GET['area']
            );
        }       

        if( !empty( $_GET['city'] ) && isset($_GET['city'])){
            $_tax_query[] = array(
                'taxonomy' => 'property_city',
                'field' => 'slug',
                'terms' => $_GET['city']
            );
        }

        if( !empty( $_GET['state'] ) && isset($_GET['state'])){
            $_tax_query[] = array(
                'taxonomy' => 'property_state',
                'field' => 'slug',
                'terms' => $_GET['state']
            );
        }     

        $tax_query[] = $_tax_query;
        $tax_count = count($tax_query);
        $tax_query['relation'] = 'AND';
        if ($tax_count > 0) {
            $search_qry['tax_query'] = $tax_query;
        }
        $meta_query = [];
        // $meta_query[] = houzez_search_min_max_price($meta_query);
        // $meta_query[] = houzez_search_min_max_area($meta_query);
        // $meta_query[] = houzez_search_custom_fields($meta_query);
        // $meta_query[] = houzez_keyword_meta_address($meta_query);
        // $meta_query[] = houzez_search_bedrooms($meta_query);
        // $meta_query[] = houzez_search_rooms($meta_query);
        // $meta_query[] = houzez_search_year_built($meta_query);
        // $meta_query[] = houzez_search_garage($meta_query);
        // $meta_query[] = houzez_search_bathrooms($meta_query);
        // $meta_query[] = houzez_search_property_id($meta_query);
        // $meta_query[] = houzez_search_currency($meta_query);
        $meta_query = apply_filters( 'houzez_meta_search_filter', $meta_query );
        
        $meta_count = count($meta_query);
        if ($meta_count > 0 || !empty($keyword_array)) {
            $search_qry['meta_query'] = array(
                array(
                    'relation' => 'AND',
                    $meta_query
                ),
            );
        }

        $search_qry['fields'] = 'ids';

		$props = get_posts( $search_qry );

        
        if( count( $props ) == 0 ){
            return $this->error_response( '2000' );
        }

        foreach ((array)$props as $prop_id) {
            $properties_data[] = get_prop_data( $prop_id , $data['data_collection']  );
        }

        $defaults = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
            'paged' => $paged,
            'post_status' => 'publish'
        );

       /**======================================================== */
       // Get pagination to work for get_posts() in WordPress .
       /**======================================================== */
        $parsed_args = wp_parse_args( $search_qry, $defaults );
	    $results = get_posts( $parsed_args );

        $published_prop_count = '';	
        $count_prop = count( $results );
        if ( $count_prop ) {
           $published_prop_count = ceil( $count_prop / $number_of_prop );
        }
        wp_reset_postdata();
       /**======================================================== */


        return $this->response( 
            [ 
                'count_pages'     => $count_prop, 
                'current_page'    => (int) $paged, 
                'properties_data' => $properties_data  
            ]
        );
	}

     /**
	 * get_user_properties
	 *
	 * @param  array $data
	 * @return array
	 */
	public function get_user_properties( $data ) {

        if(!isset( $data['data_collection'] ) ||  !in_array( $data['data_collection'] ,  $this->data_collections() )  ){
            $this->error_response( '2001' );
        }
       
        $properties_data = [];

        $number_of_prop = isset($_GET['limit']) ? ( $_GET['limit'] ) : 8 ;
		if(!$number_of_prop){
		    $number_of_prop = 8;
		}

        $paged = isset($_GET['paged']) ? ($_GET['paged']) : '1';

        $search_qry = array(
            'post_type' => 'property',
            'posts_per_page' => $number_of_prop,
            'paged' => $paged,
            'post_status' => 'publish'
        );

        $current_user_id = get_current_user_id();

        if( !empty( $current_user_id ) ){
            $search_qry['author'] = $current_user_id ;
        } 
        else{
            return new WP_Error(
                'jwt_auth_no_auth_header',
                'Authorization header not found.',
                array(
                    'status' => 403,
                )
            );
        }
       
        $search_qry['fields'] = 'ids';

		$props = get_posts( $search_qry );

        
        if( count( $props ) == 0 ){
            return $this->error_response( '2000' );
        }

        foreach ((array)$props as $prop_id) {
            $properties_data[] = get_prop_data( $prop_id , $data['data_collection']  );
        }

        $defaults = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
            'paged' => $paged,
            'post_status' => 'publish'
        );

       /**======================================================== */
       // Get pagination to work for get_posts() in WordPress .
       /**======================================================== */
        $parsed_args = wp_parse_args( $search_qry, $defaults );
	    $results = get_posts( $parsed_args );

        $published_prop_count = '';	
        $count_prop = count( $results );
        if ( $count_prop ) {
           $published_prop_count = ceil( $count_prop / $number_of_prop );
        }
        wp_reset_postdata();
       /**======================================================== */


        return $this->response( 
            [ 
                'count_pages'     => $count_prop, 
                'current_page'    => (int) $paged, 
                'properties_data' => $properties_data  
            ]
        );
	}
    
    /**
     * user_properties_allow_access
     *
     * @param  mixed $request
     * @return void
     */
    public function user_properties_allow_access( $request ){
        
        if( !is_user_logged_in() || !houzez_check_role()  ){
            return new WP_Error(
                'jwt_auth_no_auth_header',
                'Authorization header not found.',
                array(
                    'status' => 403,
                )
            );
        }
        
        return true;  
    }

    /**
     * get_agency
     *
     * @param  mixed $request
     * @return void
     */
    public function get_agencies( WP_REST_Request $request )
    {

        
        $agency_qry = array(
            'post_type' => 'houzez_agency',
            'posts_per_page' => -1
        );

        /* Keyword Based Search */
        if( isset ( $request['agency_name'] ) ) {
            $keyword = trim( $request['agency_name'] );
            $keyword = sanitize_text_field($keyword);
            if ( ! empty( $keyword ) ) {
                $agency_qry['s'] = $keyword;
            }
        }
        
        $agency_qry['fields'] = 'ids';
        $agency = get_posts( $agency_qry );

        if( count( $agency ) == 0 ){
            return $this->error_response( '2000' );
        }

        foreach ( (array)$agency as $agency_id ) {
            $agency_data[] = ag_get_agency_data( $agency_id );
        }

        return $this->response( $agency_data );
    }
    
    /**
     * get_agency
     *
     * @param  mixed $request
     * @return void
     */
    public function get_agency( WP_REST_Request $request )
    {
        if( !isset( $request['id'] ) ){
            return $this->error_response ( '2000');
        }

        if(  $this->is_agency( $request['id'] ) === false ){
            return $this->error_response ( '2002');
        }

        if( isset( $request['data_collection'] ) && !in_array( $request['data_collection'] ,  $this->data_collections() )  ){
            return $this->error_response ( '2001' );
        }
  
        if( isset( $request['data_collection'] ) ){

            $agency_agents = Houzez_Query::get_agency_agents_ids();
            $loop_get_agent_properties_ids = Houzez_Query::loop_get_agent_properties_ids($agency_agents);
            $loop_agency_properties_ids = Houzez_Query::loop_agency_properties_ids();
            $properties_ids = array_merge($loop_get_agent_properties_ids, $loop_agency_properties_ids);

            if(empty($properties_ids)) {
                $agency_qry = Houzez_Query::loop_agency_properties();
                $agency_total_listing = Houzez_Query::loop_agency_properties_count();
            } else {
                $agency_qry = Houzez_Query::loop_properties_by_ids($properties_ids);
                $agency_total_listing = Houzez_Query::loop_properties_by_ids_for_count($properties_ids);
           }

            
           if( count($properties_ids) > 0 ) { 
            foreach ((array)$properties_ids as $prop_id) {
                $properties_data[] = get_prop_data( $prop_id , $request['data_collection'] );
            }
            return $this->response( $properties_data );
          }
          else{
            return $this->error_response ( '2000');
          }
    
            
        }

        $agency_data = ag_get_agency_data( $request['id'] );

        return $this->response( $agency_data );
    }
        
    /**
     * get_state
     *
     * @param  mixed $request
     * @return void
     */
    public function get_state( WP_REST_Request $request )
    {
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
        $searched_term = isset( $request[ 'country' ] ) ? $request[ 'country' ] : -1 ;
        $property_state = ag_hirarchical_options( 'property_state', $property_state_terms, $searched_term );

        if( count( $property_state ) == 0 ) {
            return $this->error_response( '2007' , ' No state found .' );
        }

        $prop_state = [
            [
                'id'          => 'administrative_area_level_1',
                'field_id'    => 'administrative_area_level_1',
                'type'        => 'select',
                'label'       => houzez_option('cl_state', 'County/State').houzez_required_field('state'),
                'placeholder' => '',
                'options'     => $property_state,
                'required'    => 1,
            ],
        ];

        return $this->response( $prop_state );
    }
    
    /**
     * get_cites
     *
     * @param  mixed $request
     * @return array/cites
     */
    public function get_cites( WP_REST_Request $request )
    {
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
        $searched_term = isset( $request[ 'state' ] ) ? $request[ 'state' ] : -1 ;
        $property_city = ag_hirarchical_options( 'property_city', $property_city_terms, $searched_term);

        if( count( $property_city ) == 0 ) {
            return $this->error_response( '2007' , ' No city found .' );
        }

        $prop_city = [
            [
                'id'          => 'city',
                'field_id'    => 'locality',
                'type'        => 'select',
                'label'       => houzez_option( 'cl_city', 'City' ).houzez_required_field('city'),
                'placeholder' => '',
                'options'     => $property_city,
                'required'    => 1,
            ],
        ];

        return $this->response( $prop_city ); 
    }
    
    /**
     * get_area
     *
     * @param  mixed $request
     * @return void
     */
    public function get_area( WP_REST_Request $request )
    {
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
        $searched_term = isset( $request[ 'city' ] ) ? $request[ 'city' ] : -1 ;
        $property_area = ag_hirarchical_options( 'property_area', $property_area_terms, $searched_term);

        if( count( $property_area ) == 0 ) {
            return $this->error_response( '2007' , ' No area found .' );
        }

        $prop_area = [
            [
                'id'          => 'neighborhood',
                'field_id'    => 'neighborhood',
                'type'        => 'select',
                'label'       => houzez_option( 'cl_area', 'Area' ).houzez_required_field('area'),
                'placeholder' => '',
                'options'     => $property_area,
                'required'    => 1,
            ],
        ];

        return $this->response( $prop_area ); 
    }
    
    /**
     * submit_property
     *
     * @param  mixed $request
     * @return void
     */
    public function submit_property( WP_REST_Request $request )
    {
        
        if( !isset( $request['prop_title'] ) ){
            return $this->error_response ( '2000', 'missing parameter prop_title');
        }
        $new_property = ag_submit_property( $request );

        $new_prop_api_url = rest_url( $this->get_vendor() . '/v' . $this->get_version() . '/properties' . '/' . $new_property.'/?data_collection=property'  );

        return $this->response( $new_prop_api_url ); 
    }
    
    /**
     * delete_property
     *
     * @param  mixed $request
     * @return void
     */
    public function delete_property( WP_REST_Request $request )
    {
        if( !isset( $request['id'] ) ) {
            return $this->error_response ( 
                'rest_invalide_id',
                __( 'Missing Property parameter : [ id ]' )
            ); 
        }

        if(  $this->is_property( $request['id'] ) === false ){
            return $this->error_response ( 
                'rest_invalide_id',
                __( 'Invalide Property Id' )
            );
        }
        $id = $request['id'];
		$force = (bool) $request['force'];
        $post = get_post( (int) $id );

        // If we're forcing, then delete permanently.
		if ( $force ) {

            $result   = wp_delete_post( $id, true );
			$response = new WP_REST_Response();

            return $this->response( [ 
                'deleted'  => true ,
                'prop_id'  => $id
                ] );

        } else {

            // Otherwise, only trash if we haven't already.
			if ( 'trash' === $post->post_status ) {
				return $this->error_response(
					'rest_already_trashed',
					__( 'The post has already been deleted.' )
				);
			}
            // (Note that internally this falls through to `wp_delete_post()`
			// if the Trash is disabled.)
			$result   = wp_trash_post( $id );
			$post     = get_post( $id );

        }

        if ( ! $result ) {
			return $this->error_response(
				'rest_cannot_delete',
				__( 'The post cannot be deleted.' )
			);
		}

        return $this->response( [ 
            'trashed'  => true ,
            'prop_id'  => $id
            ] );
    }

    /**
	 * Checks if a given request has access to create a post.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {

        global $post;

		if ( ! empty( $request['prop_id'] ) && isset( $request['action'] ) && $request['action'] == 'add_property') {
			return new WP_Error(
				'rest_post_exists',
				__( 'Cannot create existing post.' ),
				array( 'status' => 400 )
			);
		}

		$post_type = get_post_type_object( 'property' );

        if( ! empty( $request['prop_id']  ) && $this->is_property( $request['prop_id'] ) ) {
            $post = get_post( $request['prop_id'] );

            if( get_current_user_id() !== (int) $post->post_author ){
                return new WP_Error(
                    'rest_cannot_edit_others',
                    __( 'Sorry, you are not allowed to [create/update]  property as this user.' ),
                    array( 'status' => rest_authorization_required_code() )
                );
            }
            
        }

		if ( !is_user_logged_in() || ! houzez_check_role() ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to [create/update]  property as this user.' ),
				array( 'status' => rest_authorization_required_code()  )
			);
		}

		return true;
	}

    /**
	 * Checks if a given request has access to delete a post.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {

		$post = $this->is_property( $request['id'] );

        $error = new WP_Error(
			'rest_post_invalid_id',
			__( 'Invalid Property ID.' ),
			array( 'status' => 404 )
		);

		if ( ! $post ) {
			return $error;
		}

        $post = get_post( $request['id'] );

        if ( !is_user_logged_in()  ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not logged in.' ),
				array( 'status' => rest_authorization_required_code()  )
			);
		}

		if ( $post &&  get_current_user_id() !== (int) $post->post_author ) {
			return new WP_Error(
				'rest_cannot_delete',
				__( 'Sorry, you are not allowed to delete this property.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

        
    /**
     * delete_property_args_schema
     *
     * @return void
     */
    public function delete_property_args_schema()
    {
        return array(
            'force' => array(
                'type'        => 'boolean',
                'default'     => false,
                'description' => __( 'Whether to bypass Trash and force deletion.' ),
            ),
        );
    }
    /**
     * login
     *
     * @param  mixed $request
     * @return user/id
     */
    public function login( WP_REST_Request $request ){
        $creds = array();
        if( !isset ( $request["username"] ) ) {
            return $this->error_response(
                'rest_invalid_param',
                __( 'Missing parameter(s) : username.' )
            );
        }
        if( !isset ( $request["password"] ) ) {
            return $this->error_response(
                'rest_invalid_param',
                __( 'Missing parameter(s) : password.' )
            );
        }
        $creds['user_login'] = $request["username"];
        $creds['user_password'] =  $request["password"];
        $creds['remember'] = true;
        $user = wp_signon( $creds, false );
    
        if ( is_wp_error($user) ) {
            return $this->error_response(
                'login error',
                __( $user->get_error_message() )
            );
        }

        $user_token = aqargate_token_after_register( $request["username"] ,  $request["password"] );

        return $this->response( $user_token );
    }
    
    /**
     * signup
     *
     * @param  mixed $request
     * @return user/id
     */
    public function signup( WP_REST_Request $request )
    {
        if ( ! empty( $request['id'] ) ) {
			return $this->error_response(
				'rest_user_exists',
				__( 'Cannot create existing user.' )
			);
		}

        $allow_signup_parameters = [
            'username',
            'first_name',
            'last_name',
            'useremail',
            'phone_number',
            'register_pass',
            'register_pass_retype',
            'role',
            'term_condition',
            'privacy_policy'
        ];

        foreach ( $allow_signup_parameters as $parameter) {
            if( ! isset( $request[$parameter] ) ) {
                $error[] = $parameter ;
                
            }  
        }

        if( ! empty( $error ) ) {
            return $this->error_response(
                'rest_invalid_param',
                __( 'Missing parameter(s) : [ ' . implode( ", ", $error ) . ' ]'  )
            );
        }

        $allowed_role = [
            'houzez_agent',
            'houzez_agency',
            'houzez_owner',
            'houzez_buyer',
            'houzez_seller',
            'houzez_manager'
        ];

        if( empty ( $request[ 'role' ] ) ) {
            return $this->error_response(
                'rest_invalid_role',
                __( 'Missing Role : [ Name ]'  )
            ); 
        }

        if( ! empty ( $request[ 'role' ] )  ){
            foreach ( $allowed_role as $role) {
                if( !in_array( $request[ 'role' ], $allowed_role ) ) {
                    return $this->error_response(
                        'rest_invalid_role',
                        __( 'Invalid Given Role : [ ' . $request[ 'role' ] . ' ]'  )
                    );                    
                }  
            }
        }

        $response = ag_register( $request );
        
        if( isset( $response[ 'error_code' ] ) ) {
            return $response;
        }
        
        return $this->response( $response );
    }
    
    /**
     * membership
     *
     * @param  mixed $request
     * @return void
     */
    public function membership( WP_REST_Request $request ){

        if( isset( $_GET['user_id'] ) && !is_numeric( $_GET['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        if( isset( $_GET['user_id'] ) && !empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ){
            $user_id = intval( $_GET['user_id'] );
            $response = ag_user_membership( $user_id );
        } else {
            $response = ag_membership_type();
        }


        return $this->response( $response );
    }

     
     /**
      * favorite_properties
      *
      * @param  mixed $request
      * @return void
      */
     public function favorite_properties( WP_REST_Request $request ){

        if( isset( $_GET['user_id'] ) && !is_numeric( $_GET['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        if( isset( $_GET['user_id'] ) && !empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ){
            $userID  = intval( $_GET['user_id'] );
            $fav_ids = 'houzez_favorites-'.$userID;
            $fav_ids = get_option( $fav_ids );
        } 

        if( empty( $fav_ids ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __("You don't have any favorite listings yet!", 'houzez')
            );
        }
        $search_qry = array('post_type' => 'property', 'post__in' => $fav_ids, 'numberposts' => -1 );
        $search_qry['fields'] = 'ids';

		$fav_props = get_posts( $search_qry );

        if( count( $fav_props ) == 0 ){
            return $this->error_response(
                'rest_invalid_data',
                __("You don't have any favorite listings yet!", 'houzez')
            );
        }

        foreach ((array)$fav_props as $prop_id) {
            $response[] = get_prop_data( $prop_id , $_GET['data_collection'] );
        }
        wp_reset_postdata();

        return $this->response( $response );
     }
     
     /**
      * favorite_properties_add
      *
      * @param  mixed $request
      * @return void
      */
     public function favorite_properties_add( WP_REST_Request $request ){

        if( isset( $_GET['user_id'] ) && !is_numeric( $_GET['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        if( isset( $_GET['prop_id'] ) && !is_numeric( $_GET['prop_id'] ) && $this->is_property( $_GET['prop_id'] )){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid Property ID data'  )
            );
        }

        if( isset( $_GET['user_id'] ) && !empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ){
            $userID  = intval( $_GET['user_id'] );
            $fav_option = 'houzez_favorites-'.$userID;
            $property_id = intval( $_GET['prop_id'] );
            $current_prop_fav = get_option( 'houzez_favorites-'.$userID );

            // Check if empty or not
            if( empty( $current_prop_fav ) ) {
                $prop_fav = array();
                $prop_fav['1'] = $property_id;
                update_option( $fav_option, $prop_fav );
            } else {
                if(  ! in_array ( $property_id, $current_prop_fav )  ) {
                    $current_prop_fav[] = $property_id;
                    update_option( $fav_option,  $current_prop_fav );
                } 
            }
        } 

        return $this->response( $current_prop_fav );
     }
     
     /**
      * favorite_properties_remove
      *
      * @param  mixed $data
      * @return void
      */
     public function favorite_properties_remove( $data ){
        if( isset( $_GET['user_id'] ) && !is_numeric( $_GET['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        if( isset( $_GET['prop_id'] ) && !is_numeric( $_GET['prop_id'] ) && $this->is_property( $_GET['prop_id'] )){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid Property ID data'  )
            );
        }

        if( isset( $_GET['user_id'] ) && !empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ){
            $userID  = intval( $_GET['user_id'] );
            $fav_option = 'houzez_favorites-'.$userID;
            $property_id = intval( $_GET['prop_id'] );
            $current_prop_fav = get_option( 'houzez_favorites-'.$userID );

            // Check if empty or not
            if( empty( $current_prop_fav ) ) {
                $prop_fav = array();
                $prop_fav['1'] = $property_id;
                update_option( $fav_option, $prop_fav );
            } else {
                
                $key = array_search( $property_id, $current_prop_fav );

                if( $key != false ) {
                    unset( $current_prop_fav[$key] );
                }
                update_option( $fav_option, $current_prop_fav );

                
            }
        } 

        return $this->response( $current_prop_fav );
        
     }
     
     /**
      * conversations
      *
      * @param  mixed $request
      * @return void
      */
     public function conversations( $data ){

        if( isset( $data['user_id'] ) && !is_numeric( $data['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        global $wpdb, $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;
    
        if( isset( $data['user_id'] ) && !empty( $data['user_id'] ) ) {
            $userID = $data['user_id'];
        }

        $tabel = $wpdb->prefix . 'houzez_threads';
        $thread_id = isset( $data['thread_id'] ) ? $data['thread_id']  : '';
        $current_user_id = $userID;

        $conversations = $wpdb->get_results(
            "
            SELECT * 
            FROM $tabel
            WHERE sender_id = $current_user_id OR receiver_id = $current_user_id 
            "
        );

        if( empty( $conversations ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'no conversations found'  )
            );
        }

        $conversation_id = [];

        foreach( (array)$conversations as $conversation ) {    

            $conversation_id[] = $this->get_all_conversation( $user_id,  $conversation ) ;
        }

        $response['conversations'] =  $conversation_id;

        return $this->response( $response );
     }

     
     /**
      * conversations_messages
      *
      * @param  mixed $request
      * @return void
      */
     public function conversations_messages( $data ){

        if( isset( $_GET['thread_id'] ) && !is_numeric( $_GET['thread_id'] ) && empty( $_GET['thread_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid conversations ID data'  )
            );
        }

        if( isset( $_GET['user_id'] ) && !is_numeric( $_GET['user_id'] ) ){
            return $this->error_response(
                'rest_invalid_data',
                __( 'Invalid User ID data'  )
            );
        }

        global $wpdb, $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;
    
        if( isset( $data['user_id'] ) && !empty( $data['user_id'] ) ) {
            $userID = $data['user_id'];
        }
        
        $thread_id = isset( $_GET['thread_id'] ) ? $_GET['thread_id']  : '';
        $prop_author_id = $userID;

        $tabel = $wpdb->prefix . 'houzez_threads';
        $conversation = $wpdb->get_row(
            "
            SELECT * 
            FROM $tabel
            WHERE id = $thread_id
            AND (sender_id = $prop_author_id OR receiver_id = $prop_author_id)
            "
        );

        $tabel = $wpdb->prefix . 'houzez_thread_messages';
        $conversations_messages = $wpdb->get_results(
            "
            SELECT * 
            FROM $tabel
            WHERE thread_id = $thread_id
            ORDER BY id DESC
            "
        );

        if( empty( $conversations_messages ) || empty( $conversation ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'no conversations found'  )
            );
        }

        $thread_author = $conversation->sender_id;

        if ( $thread_author == $prop_author_id ) {
            $thread_author = $conversation->receiver_id;
        } 
        $response['id'] = $conversation->id;
        $response['prop_id']   = $conversation->property_id;
        $response['conversation_title']   = get_the_title( $conversation->property_id );
        $response['conversation_author'] = $thread_author;

        $thread_author_first_name  =  get_the_author_meta( 'first_name', $thread_author );
        $response['conversation_author_first_name'] = $thread_author_first_name;
        $thread_author_last_name  =  get_the_author_meta( 'last_name', $thread_author );
        $response['conversation_author_last_name'] = $thread_author_last_name;
        $thread_author_display_name = get_the_author_meta( 'display_name', $thread_author );
        $response['conversation_author_display_name'] = $thread_author_display_name;
        if( !empty($thread_author_first_name) && !empty($thread_author_last_name) ) {
            $response['conversation_author_display_name'] = $thread_author_first_name.' '.$thread_author_last_name;
        }

        $user_custom_picture =  get_the_author_meta( 'fave_author_custom_picture' , $thread_author );
        $response['user_custom_picture'] = $user_custom_picture;
        if ( empty( $user_custom_picture )) {
            $user_custom_picture = get_template_directory_uri().'/img/profile-avatar.png';
            $response['user_custom_picture'] = $user_custom_picture;
        }

        $response['is_user_online'] = houzez_is_user_online( $thread_author );
        
        $messages_data = [];
        foreach ( $conversations_messages as $message ) { 

			$message_class = 'msg-me';
			$message_author = $message->created_by;
			$message_author_name = ucfirst( $thread_author_display_name );
			$message_author_picture =  get_the_author_meta( 'fave_author_custom_picture' , $message_author );
            
			if ( $message_author == $current_user_id ) {
				$message_author_name = esc_html__( 'Me', 'houzez' );
				$message_class = '';
			}

			if ( empty( $message_author_picture )) {
				$message_author_picture = get_template_directory_uri().'/img/profile-avatar.png';
			}

            $messages_data[] = [
                'id'                     => $message->id,
                'message_author'         => $message_author,
                'message_author_name'    => $message_author_name,
                'message_author_picture' => $message_author_picture,
                'message_content'        => $message->message,
                'message_attachments'    => $message->attachments,    
            ];
        }
        $response['messages_data'] = $messages_data;

        return $this->response( $response );
     }
     
     /**
      * new_conversation
      *
      * @param  mixed $data
      * @return void
      */
     public function new_conversation( $data ){
        
        if( !isset( $_POST['prop_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing Property ID data'  )
            );
        }
        if( !isset( $_POST['message'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing message data'  )
            );
        }

        if( isset( $_POST['user_id'] ) && !is_numeric( $_POST['user_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing User ID data' )
            );
        }
        global $wpdb, $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;
    
        if( isset( $data['user_id'] ) && !empty( $data['user_id'] ) ) {
            $userID = $data['user_id'];
        }
         
		if (   isset( $_POST['prop_id'] ) && !empty( $_POST['prop_id'] ) && isset( $_POST['message'] ) && !empty( $_POST['message'] ) ) {
            $message_attachments = Array ();
			$message   = $_POST['message'];
            $sender_id = $userID;
            
			if ( isset( $_POST['propperty_image_ids'] ) && sizeof( $_POST['propperty_image_ids'] ) != 0 ) {
				$message_attachments = $_POST['propperty_image_ids'];
			}
			$message_attachments = serialize( $message_attachments );
            $thread_id   = $this->add_new_conversation( $data );
            $message_id  = $this->send_message( $sender_id, $thread_id, $message, $message_attachments );
			
            
            if( $message_id < 0 ){
               return $this->error_response(                   
                        '403',
                        __("Some errors occurred! Please try again.", 'houzez')                   
               );
            }

            return $this->response( $message_id );

        }
        
     }
 
     /**
      * add_new_conversation
      *
      * @param  mixed $data
      * @return void
      */
     public function add_new_conversation( $data ){

        global $wpdb, $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;
    
        if( isset( $data['user_id'] ) && !empty( $data['user_id'] ) ) {
            $userID = $data['user_id'];
        }

		$sender_id   = $userID ;
		$property_id = $data['prop_id'];
		$receiver_id = get_post_field( 'post_author', $property_id );
		$table_name  = $wpdb->prefix . 'houzez_threads';
		$agent_display_option = get_post_meta( $property_id, 'fave_agent_display_option', true );
		$prop_agent_display = get_post_meta( $property_id, 'fave_agents', true );
		if( $prop_agent_display != '-1' && $agent_display_option == 'agent_info' ) {
			$prop_agent_id = get_post_meta( $property_id, 'fave_agents', true );
			$agent_user_id = get_post_meta( $prop_agent_id, 'houzez_user_meta_id', true );
			if ( !empty( $agent_user_id ) && $agent_user_id != 0 ) {
				$receiver_id = $agent_user_id;
			}
		} elseif( $agent_display_option == 'agency_info' ) {
			$prop_agent_id = get_post_meta( $property_id, 'fave_property_agency', true );
			$agent_user_id = get_post_meta( $prop_agent_id, 'houzez_user_meta_id', true );
			if ( !empty( $agent_user_id ) && $agent_user_id != 0 ) {
				$receiver_id = $agent_user_id;
			}
		}

		$id = $wpdb->insert(
			$table_name,
			array(
				'sender_id' => $sender_id,
				'receiver_id' => $receiver_id,
				'property_id' => $property_id,
				'time'	=> current_time( 'mysql' )
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s'
			)
		);

		return $wpdb->insert_id;
       
    }
     
     /**
      * send_message
      *
      * @param  mixed $sender_id
      * @param  mixed $thread_id
      * @param  mixed $message
      * @param  mixed $message_attachments
      * @return void
      */
     public function send_message( $sender_id, $thread_id, $message, $attachments ){
        global $wpdb, $current_user;

		if ( is_array( $attachments ) ) {
			$attachments = serialize( $attachments );
		}

		$created_by =  $sender_id;
		$table_name = $wpdb->prefix . 'houzez_thread_messages';

		$message = stripslashes($message);
		$message = htmlentities($message);

		$message_id = $wpdb->insert(
			$table_name,
			array(
				'created_by' => $created_by,
				'thread_id' => $thread_id,
				'message' => $message,
				'attachments' => $attachments,
				'time' => current_time( 'mysql' )
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s'
			)
		);

		$tabel = $wpdb->prefix . 'houzez_threads';
		$wpdb->update(
			$tabel,
			array(  'seen' => 0 ),
			array( 'id' => $thread_id ),
			array( '%d' ),
			array( '%d' )
		);

		$message_query = "SELECT * FROM $tabel WHERE id = $thread_id";
		$houzez_thread = $wpdb->get_row( $message_query );
		$receiver_id = $houzez_thread->sender_id;

		if ( $receiver_id == $created_by ) {
			$receiver_id = $houzez_thread->receiver_id;
		}

		$receiver_data = get_user_by( 'id', $receiver_id );

		apply_filters( 'houzez_message_email_notification', $thread_id, $message, $receiver_data->user_email, $created_by );

		return $wpdb->insert_id;
     }
     
     /**
      * new_conversation_message
      *
      * @param  mixed $data
      * @return void
      */
     public function new_conversation_message( $data ){
        
        if( !isset( $_POST['conversation_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing Conversation ID data [ conversation_id ]'  )
            );
        }
        if( !isset( $_POST['message'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing message data'  )
            );
        }

        if( isset( $_POST['user_id'] ) && !is_numeric( $_POST['user_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing User ID data' )
            );
        }

        global $wpdb, $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;
    
        if( isset( $data['user_id'] ) && !empty( $data['user_id'] ) ) {
            $userID = $data['user_id'];
        }
         
		if ( isset( $_POST['conversation_id'] ) && !empty( $_POST['conversation_id'] ) && isset( $_POST['message'] ) && !empty( $_POST['message'] ) ) {
            $message_attachments = Array ();
			$message   = $_POST['message'];
            $sender_id = $userID;

			if ( isset( $_POST['propperty_image_ids'] ) && sizeof( $_POST['propperty_image_ids'] ) != 0 ) {
				$message_attachments = $_POST['propperty_image_ids'];
			}
			$message_attachments = serialize( $message_attachments );
            $thread_id   = intval( $_POST['conversation_id'] );
            $message_id  = $this->send_message( $sender_id, $thread_id, $message, $message_attachments );
			
            
            if( $message_id < 0 ){
               return $this->error_response(                   
                        '403',
                        __("Some errors occurred! Please try again.", 'houzez')                   
               );
            }

            return $this->response( $message_id );
        }
    }
    
    /**
     * get_all_conversation
     *
     * @param  mixed $user_id
     * @param  mixed $conversation
     * @return void
     */
    public function get_all_conversation( $user_id,  $conversation ){

        global $wpdb, $userID;
        $sender_id = $conversation->sender_id;
        $receiver_id = $conversation->receiver_id;
        $conversation_class = 'msg-unread table-new';
        $tabel = $wpdb->prefix . 'houzez_thread_messages';
        $sender_id = $conversation->sender_id;
        $thread_id = $conversation->id;

        $last_message = $wpdb->get_row(
            "SELECT *
                FROM $tabel
                WHERE thread_id = $thread_id
                ORDER BY id DESC"
        );

        $user_custom_picture =  get_the_author_meta( 'fave_author_custom_picture' , $sender_id );
        $url_query = array( 'thread_id' => $thread_id, 'seen' => true );

        if ( $last_message->created_by == $userID || $conversation->seen ) {
            $conversation_class = '';
            unset( $url_query['seen'] );
        }

        if ( empty( $user_custom_picture )) {
            $user_custom_picture = get_template_directory_uri().'/img/profile-avatar.png';
        }

        $conversation_link = houzez_get_template_link_2('template/user_dashboard_messages.php');
        $conversation_link = add_query_arg( $url_query, $thread_link );

        $sender_first_name  =  get_the_author_meta( 'first_name', $sender_id );
        $sender_last_name  =  get_the_author_meta( 'last_name', $sender_id );
        $sender_display_name = get_the_author_meta( 'display_name', $sender_id );
        if( !empty($sender_first_name) && !empty($sender_last_name) ) {
            $sender_display_name = $sender_first_name.' '.$sender_last_name;
        }

        $last_sender_first_name  =  get_the_author_meta( 'first_name', $last_message->created_by );
        $last_sender_last_name  =  get_the_author_meta( 'last_name', $last_message->created_by );
        $last_sender_display_name = get_the_author_meta( 'display_name', $last_message->created_by );
        if( !empty($last_sender_first_name) && !empty($last_sender_last_name) ) {
            $last_sender_display_name = $last_sender_first_name.' '.$last_sender_last_name;
        }
        
        $response['id'] = $thread_id ;
        $response['user_custom_picture'] = $user_custom_picture ;
        $response['from'] = ucfirst( $sender_display_name );
        $response['property_id'] = $conversation->property_id;
        $response['property_title'] = get_the_title( $conversation->property_id ) ;
        $response['last_message'] = ucfirst( $last_sender_display_name ).': ' .$last_message->message;
        $response['date'] = date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $last_message->time ) ); 

        return $response;

    }

    public function profile_fields( $data ){

        if( isset( $data['user_id'] ) && empty( $data['user_id'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing User ID data' )
            );
        }

        global $current_user, $houzez_local;
        $userID = get_current_user_id();

        if( isset( $data['user_id'] )  && !empty( $data['user_id'] )) {
            $userID = $data['user_id'];
        }
       
        $username               =   get_the_author_meta( 'user_login' , $userID );
        $user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
        $first_name             =   get_the_author_meta( 'first_name' , $userID );
        $last_name              =   get_the_author_meta( 'last_name' , $userID );
        $user_email             =   get_the_author_meta( 'user_email' , $userID );
        $user_mobile            =   get_the_author_meta( 'fave_author_mobile' , $userID );
        $user_whatsapp          =   get_the_author_meta( 'fave_author_whatsapp' , $userID );
        $user_phone             =   get_the_author_meta( 'fave_author_phone' , $userID );
        $description            =   get_the_author_meta( 'description' , $userID );
        $userlangs              =   get_the_author_meta( 'fave_author_language' , $userID );
        $user_company           =   get_the_author_meta( 'fave_author_company' , $userID );
        $tax_number             =   get_the_author_meta( 'fave_author_tax_no' , $userID );
        $fax_number             =   get_the_author_meta( 'fave_author_fax' , $userID );
        $user_address           =   get_the_author_meta( 'fave_author_address' , $userID );
        $service_areas          =   get_the_author_meta( 'fave_author_service_areas' , $userID );
        $specialties            =   get_the_author_meta( 'fave_author_specialties' , $userID );
        $license                =   get_the_author_meta( 'fave_author_license' , $userID );
        $gdpr_agreement         =   get_the_author_meta( 'gdpr_agreement' , $userID );
        $id_number              =   get_the_author_meta( 'aqar_author_id_number' , $userID );
        $ad_number              =   get_the_author_meta( 'aqar_author_ad_number' , $userID );
        $type_id                =   get_the_author_meta( 'aqar_author_type_id' , $userID );
       
       if( houzez_is_agency() ) {
           $title_position_lable = esc_html__('Agency Name','houzez');
           $about_lable = esc_html__( 'About Agency', 'houzez' );
       } else {
           $title_position_lable =  esc_html__('Title / Position','houzez');
           $about_lable = esc_html__( 'About me', 'houzez' );
       }
       
       $profile_fields[] = [
            'id'          => 'username',
            'field_id'    => 'username',
            'type'        => 'text',
            'label'       => __('Username','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $username ),
            'disabled'    => 1,
        ];

       $profile_fields[] = [
            'id'          => 'useremail',
            'field_id'    => 'useremail',
            'type'        => 'text',
            'label'       => __('Email','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_email ),
            'disabled'    => 0,
        ];

        if( !houzez_is_agency() ):
            $profile_fields[] = [
                'id'          => 'firstname',
                'field_id'    => 'firstname',
                'type'        => 'text',
                'label'       => __('First Name','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => esc_attr( $first_name ),
                'disabled'    => 0,
            ];
            $profile_fields[] = [
                'id'          => 'lastname',
                'field_id'    => 'lastname',
                'type'        => 'text',
                'label'       => __('Last Name','houzez'),
                'placeholder' => '',
                'options'     => '',
                'value'       => esc_attr( $last_name ),
                'disabled'    => 0,
            ];
        endif;

        $profile_fields[] = [
            'id'          => 'title',
            'field_id'    => 'title',
            'type'        => 'text',
            'label'       => esc_attr($title_position_lable),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_title ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'license',
            'field_id'    => 'license',
            'type'        => 'text',
            'label'       => __('License', 'houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $license ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'usermobile',
            'field_id'    => 'usermobile',
            'type'        => 'text',
            'label'       => __('Mobile','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_mobile ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'whatsapp',
            'field_id'    => 'whatsapp',
            'type'        => 'text',
            'label'       => __('Whatsapp','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_whatsapp ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'id_number',
            'field_id'    => 'id_number',
            'type'        => 'text',
            'label'       => __('رقم الهوية / أو السجل التجاري','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $id_number ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'ad_number',
            'field_id'    => 'ad_number',
            'type'        => 'text',
            'label'       => __('رقم المعلن','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $ad_number ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'aqar_author_type_id',
            'field_id'    => 'aqar_author_type_id',
            'type'        => 'select',
            'label'       => __('نوع المعلن','houzez'),
            'placeholder' => '',
            'options'     => [
                ['id' => '1', 'value' => 'مواطن'],
                ['id' => '2', 'value' => 'مقيم'],
                ['id' => '3', 'value' => 'منشأة'],
            ],
            'value'       => esc_attr( $type_id ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'tax_number',
            'field_id'    => 'tax_number',
            'type'        => 'text',
            'label'       => __('Tax Number','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $tax_number ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'userphone',
            'field_id'    => 'userphone',
            'type'        => 'text',
            'label'       => __('Phone','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_phone ),
            'disabled'    => 0,
        ];
        if( !houzez_is_agency() ):
        $profile_fields[] = [
            'id'          => 'user_company',
            'field_id'    => 'user_company',
            'type'        => 'text',
            'label'       => __('Company Name','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_company ),
            'disabled'    => 0,
        ];
        endif;
        $profile_fields[] = [
            'id'          => 'user_address',
            'field_id'    => 'user_address',
            'type'        => 'text',
            'label'       => __('Address','houzez'),
            'placeholder' => '',
            'options'     => '',
            'value'       => esc_attr( $user_address ),
            'disabled'    => 0,
        ];
        $profile_fields[] = [
            'id'          => 'bio',
            'field_id'    => 'bio',
            'type'        => 'textarea',
            'label'       => $about_lable,
            'placeholder' => '',
            'options'     => '',
            'value'       => $description,
            'disabled'    => 0,
        ];

        

       return $this->response( $profile_fields );
    }
     
    
    /**
     * profile_update
     *
     * @param  mixed $data
     * @return void
     */
    public function profile_update ( $data ){

        // if( !isset( $data['user_id'] ) ) {
        //     return $this->error_response(
        //         'rest_invalid_data',
        //         __( 'Missing User ID data' )
        //     );
        // }

        // if( isset( $data['user_id'] )  && empty( $data['user_id'] )) {
        //     return $this->error_response(
        //         'rest_invalid_data',
        //         __( 'Missing User ID data' )
        //     );
        // }
        
        $update_profile = api_update_profile( $data );

        return $this->response( $update_profile );

    }
    
    /**
     * user_role
     *
     * @param  mixed $data
     * @return void
     */
    public function user_role( $data ){

        $user_role = [];

        if( $show_hide_roles['agent'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_agent', 'name' => houzez_option('agent_role')];
        }
        if( $show_hide_roles['agency'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_agency', 'name' => houzez_option('agency_role')];
        }
        if( $show_hide_roles['owner'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_owner', 'name' => houzez_option('owner_role')];
        }
        if( $show_hide_roles['buyer'] != 1 ) {
            $user_role[] = [ 'id' => 'houzez_buyer', 'name' => houzez_option('buyer_role')];
        }
        
        return $this->response( $user_role );
    }
    
    /**
     * siteinfo
     *
     * @param  mixed $data
     * @return void
     */
    public function siteinfo( $data )
    {
        $response = [];
        
        $response['name'] = get_bloginfo( 'name' );
        $response['description'] = get_bloginfo( 'description' );
        $response['timezone_string'] = get_option( 'timezone_string' );
        $response['gmt_offset']  = get_option( 'gmt_offset' );
        $response['logo'] = carbon_get_theme_option( 'ag_logo' );
        $response['reload_gif'] = carbon_get_theme_option( 'ag_reload_gif' );
        $response['json'] = carbon_get_theme_option( 'ag_json' );

        return $this->response( $response );
    }
    
    /**
     * tax_last_update
     *
     * @param  mixed $term_id
     * @param  mixed $tt_id
     * @param  mixed $taxonomy
     * @param  mixed $update
     * @return void
     */
    public function tax_last_update( $term_id, $tt_id, $taxonomy, $update ){       
        $time = date("F d, Y h:i:s A");
        update_option( $taxonomy .'_tax_last_update', $time, true );
    }
    
    /**
     * property_category
     *
     * @param  mixed $data
     * @return void
     */
    public function property_category( $data ) {

        if( !isset( $_GET['category_slug'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Missing paramter(s) category_slug' )
            );
        }
        if( isset( $_GET['category_slug'] )  && empty( $_GET['category_slug'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Empty paramter(s) category_slug' )
            );
        }
        if( isset( $_GET['category_slug'] )  && !is_taxonomy( $_GET['category_slug'] ) ) {
            return $this->error_response(
                'rest_invalid_data',
                __( 'Error Category [ ' . $_GET['category_slug'] . ' ] [ Not Found ]' )
            );
        }

        if( isset( $_GET['category_slug'] ) && !empty( $_GET['category_slug'] ) && is_taxonomy( $_GET['category_slug'] ) ){
            
            if( isset( $_GET['level'] ) && !empty( $_GET['level']  ) && $_GET['level'] === '1' ) {
                $child_terms = true;
            } else {
                $child_terms = false;
            }

            $property_terms = get_terms ( array( 'taxonomy' => $_GET['category_slug'],'hide_empty' => false ) );
        
            $response = ag_get_taxonomies_with_id_value( $_GET['category_slug'], $property_terms, -1, $child_terms);

            return $this->response( $response );
        }
    }
}


new AqarGateApi();