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

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            ad_license_number varchar(255) DEFAULT NULL,
            operation varchar(255) NOT NULL,
            status varchar(50) NOT NULL,
            message text DEFAULT NULL,
            details longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        dbDelta( $sql );
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
            'ad_license_number'   => 'Not linked to a property yet',
            'operation'     => 'Unknown Operation',
            'status'        => 'Failed',
            'message'       => '',
            'details'       => '{}',
            'created_at'    => current_time('mysql'),
        ];

        $data = wp_parse_args($data, $defaults);
        
        // Prepare record for DB to avoid legacy fields issues
        $record = [
            'user_id'     => $data['user_id'],
            'ad_license_number' => $data['ad_license_number'],
            'operation'   => $data['operation'],
            'status'      => $data['status'],
            'message'     => $data['message'],
            'details'     => $data['details'],
            'created_at'  => $data['created_at']
        ];
        
        // Ensure details is a valid JSON string
        if ( is_array($record['details']) || is_object($record['details']) ) {
            $record['details'] = wp_json_encode( $record['details'] );
        }
        
        $format = ['%d', '%s', '%s', '%s', '%s', '%s', '%s'];
        
        return $wpdb->insert($table_name, $record, $format);
    }
}

new RegaLogDB();
