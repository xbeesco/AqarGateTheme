<?php

class RegaLogDB {

    public function __construct() {
        add_action( 'init', [$this, 'create_rega_log_table']);
    }
/*
|--------------------------------------------------------------------------------------------
|                         Create REGA Log DataBase Table
|--------------------------------------------------------------------------------------------
*/
    public function create_rega_log_table()
    {
        global $wpdb;
    
        $wpdb->hide_errors();
    
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        
        $table_name = $wpdb->prefix.'rega_log';
        $charset_collate = $wpdb->get_charset_collate(); // utf8mb4

        // Check if table exists
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
             $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,        -- Log Number
                user_id bigint(20) NOT NULL,                    -- ID for user who performed the action
                property_id bigint(20) DEFAULT NULL,            -- Number of property
                operation varchar(100) NOT NULL,                -- Operation performed ( Verify / Create / Update / Delete )  
                status varchar(20) NOT NULL,                    -- Status: Success/Failed
                status_code int(5) DEFAULT NULL,                -- HTTP Status Code ( 200 / 400 / 500 )
                request_body longtext DEFAULT NULL,             -- Request Body
                response_body longtext DEFAULT NULL,            -- Response Body
                simple_error text DEFAULT NULL,                 -- Simple error message to normal users to understand errors
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            
            dbDelta( $sql );
        }
    }
/*
|--------------------------------------------------------------------------------------------
|                   Handel Logging Function When Forgot To Send Something
|--------------------------------------------------------------------------------------------
*/
    public function log( $data = array() )
    {
        global $wpdb;
        $table_name  = $wpdb->prefix."rega_log";
        
        $defaults = [
            'user_id'       => get_current_user_id(),
            'property_id'   => null,
            'operation'     => 'Unknown',
            'status'        => 'Failed',
            'status_code'   => 0,
            'request_body'  => '',
            'response_body' => '',
            'simple_error'  => '',
            'created_at'    => current_time('mysql'),
        ];

        $data = wp_parse_args($data, $defaults);
        
        $format = ['%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s'];
        
        return $wpdb->insert($table_name, $data, $format);
    }
}

new RegaLogDB();
