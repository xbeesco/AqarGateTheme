<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Rega_Log_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
            'singular' => 'log',
            'plural'   => 'logs',
            'ajax'     => false
        ) );
    }

    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'ad_license_number':
                
                if ( is_numeric($item['ad_license_number']) && $item['ad_license_number'] > 0 ) {
                     return '<a href="'.get_edit_post_link($item['ad_license_number']).'" target="_blank">#'.$item['ad_license_number'].'</a>';
                }
                return esc_html($item['ad_license_number']);
                
            case 'user_id':
                $user_info = get_userdata($item['user_id']);
                return $user_info ? '<a href="'.get_edit_user_link($item['user_id']).'" target="_blank">'.$user_info->display_name.'</a>' : 'Guest';
            case 'status':
                $status = $item['status'];
                $color = ($status === 'Success') ? '#4CAF50' : '#F44336';
                return sprintf('<span style="color: #fff; background: %s; padding: 3px 8px; border-radius: 3px;">%s</span>', $color, esc_html($status));         
            case 'created_at':
                return $item['created_at'];
            case 'operation':
                // Now stored as human readable text
                return esc_html($item['operation']);
                
            case 'message':
                 return $item['message'] ? esc_html($item['message']) : '-';
                 
            case 'actions':
                return sprintf('<button class="button view-details-btn" data-log-id="%d">Details</button>', $item['id']);
            default:
                return print_r( $item, true );
        }
    }

    // Columns In Admin GUI
    public function get_columns() {
        return array(
            'cb'                => '<input type="checkbox" />',
            'created_at'        => 'Date',
            'operation'         => 'Operation',
            'user_id'           => 'User',
            'ad_license_number' => 'Ad License Number',
            'status'            => 'Status',
            'message'           => 'Message',
            'actions'           => 'Details',
        );
    }
    
    // Handle Delete Action in admin dashboard in Rega logs table
    public function process_bulk_action() {
        // Check if delete button was clicked
        if ( ! isset( $_POST['delete_logs'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! isset( $_POST['rega_logs_delete_nonce'] ) || 
             ! wp_verify_nonce( $_POST['rega_logs_delete_nonce'], 'rega_logs_delete_action' ) ) {
            return;
        }

        if ( empty( $_POST['log'] ) || ! is_array( $_POST['log'] ) ) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rega_log';

        $ids = array_map( 'intval', $_POST['log'] );
        $ids = array_filter( $ids );

        if ( empty( $ids ) ) {
            return;
        }

        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE id IN ($placeholders)",
                $ids
            )
        );
    }
    
    public function column_cb( $item ) {
        return sprintf('<input type="checkbox" name="log[]" value="%s" />', $item['id']);
    }

    public function get_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rega_log';
        
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'created_at';
        $order = (isset($_GET['order'])) ? $_GET['order'] : 'DESC';

        // Filters
        $where = "WHERE 1=1";
        if (!empty($_GET['filter_status'])) {
            $where .= $wpdb->prepare(" AND status = %s", $_GET['filter_status']);
        }
        if (!empty($_GET['filter_date'])) {
            $where .= $wpdb->prepare(" AND DATE(created_at) = %s", $_GET['filter_date']);
        }


        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name $where");
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $data = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT $offset, $per_page", ARRAY_A);

        return $data;
    }

    public function prepare_items() {
        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        // Handle bulk actions (e.g. delete)
        $this->process_bulk_action();
        $this->items = $this->get_data();
    }
    
    public function extra_tablenav( $which ) {
        if ( $which == "top" ){
            ?>
            <div class="alignleft actions">
                <select name="filter_status">
                    <option value="">All Statuses</option>
                    <option value="Success" <?php selected( isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'Success' ); ?>>Success</option>
                    <option value="Failed" <?php selected( isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'Failed' ); ?>>Failed</option>
                </select>
                <input type="date" name="filter_date" value="<?php echo isset($_GET['filter_date']) ? esc_attr($_GET['filter_date']) : ''; ?>">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
                <input type="submit" name="delete_logs" id="delete-selected-logs" class="button button-secondary" value="Delete Selected" style="margin-left: 10px;">
            </div>
            <?php
        }
    }
}

function display_rega_log_page() {
    $list_table = new Rega_Log_List_Table();
    $list_table->prepare_items();
    ?>
    <div class="wrap">
        <h1>REGA API Logs</h1>
        <form method="post">
            <?php wp_nonce_field( 'rega_logs_delete_action', 'rega_logs_delete_nonce' ); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
            <?php $list_table->display(); ?>
        </form>
    </div>

    <!-- Modal for Details -->
    <div id="log-details-modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 800px; border-radius: 5px; position: relative;">
            <span class="close-modal" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Log Details</h2>
            <div id="modal-content">
                <h3>Request</h3>
                <button class="button copy-btn" data-target="req-code">Copy Request</button>
                <pre id="req-code" style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 200px;"></pre>
                
                <h3>Response</h3>
                <button class="button copy-btn" data-target="res-code">Copy Response</button>
                <pre id="res-code" style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 200px;"></pre>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($){
        // View Details
        $('.view-details-btn').click(function(e){
            e.preventDefault();
            var logId = $(this).data('log-id');
            
             // Fetch details via AJAX
            $.post(ajaxurl, {
                action: 'get_rega_log_details',
                log_id: logId
            }, function(response) {
                if(response.success) {
                    var data = response.data;
                    
                    // Parse Details JSON
                    var detailsObj = {};
                    try {
                        detailsObj = JSON.parse(data.details);
                    } catch(e){
                         console.error('Failed to parse details', e);
                    }

                    var reqJSON = detailsObj.request ? JSON.stringify(detailsObj.request, null, 4) : JSON.stringify(data, null, 4);
                    var resJSON = detailsObj.response ? JSON.stringify(detailsObj.response, null, 4) : (detailsObj.request ? '' : 'No response data'); 

                    $('#req-code').text(reqJSON);
                    $('#res-code').text(resJSON);
                    $('#log-details-modal').show();
                } else {
                    alert('Error loading details');
                }
            });
        });

        // Close Modal
        $('.close-modal').click(function(){
            $('#log-details-modal').hide();
        });
        
        // Close on click outside
        $(window).click(function(e) {
            if ($(e.target).is('#log-details-modal')) {
                $('#log-details-modal').hide();
            }
        });

        // Copy Button
        $('.copy-btn').click(function(e){
            e.preventDefault();
            var targetId = $(this).data('target');
            var text = $('#' + targetId).text();
            
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        });
    });
    </script>
    <?php
}

function add_rega_log_menu() {
    add_submenu_page(
        'crb_carbon_fields_container_ag_settings.php',
        'REGA Logs',    // Page title
        'REGA Logs',    // Menu title
        'manage_options',
        'rega-log',
        'display_rega_log_page'
    );
}
add_action('admin_menu', 'add_rega_log_menu', 99);

function get_rega_log_details_ajax() {
    global $wpdb;
    $log_id = intval($_POST['log_id']);
    $table_name = $wpdb->prefix . 'rega_log';
    $log = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $log_id), ARRAY_A);
    
    if($log) {
        wp_send_json_success($log);
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_get_rega_log_details', 'get_rega_log_details_ajax');
