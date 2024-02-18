<?php
// Step 2: Define a class for your custom list table
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Custom_List_Table extends WP_List_Table {
    // Define necessary properties and methods for your custom list table
    // Refer to the WP_List_Table documentation for more details:
    // https://developer.wordpress.org/reference/classes/wp_list_table/
    
    public function __construct() {
        parent::__construct( array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false
        ) );
    }
    
    public function column_default( $item, $column_name ) {
        // Implement logic to display the columns for your table
        switch ( $column_name ) {
            case 'id':
            case 'transId':
            case 'cardId':
                return '<strong>' . $item[ $column_name ] . '</strong>';
            case 'status':
                $status = $item[ $column_name ];
                $bg_color = '';
    
                if ( $status === 'COMPLETED' ) {
                    $bg_color = '#8BC34A';
                    $color = '#fff';

                } elseif ( $status === 'PENDING' ) {
                    $bg_color = '#ffc107';
                    $color = '#fff';
                }elseif( $status === 'REJECTED' ) {
                    $bg_color = '#E91E63';
                    $color = '#fff';
                }
    
                return sprintf(
                    '<span style="border-radius: 4px;background-color: %s; color: %s; padding: 1px 5px 2px ;">%s</span>',
                    $bg_color,
                    $color,
                    $status
                );
            case 'date_created':
            case 'date_updated':
        
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); // Fallback output for unknown columns
        }
    }
    
    public function column_cb( $item ) {
        // Implement logic for the checkbox column
        return sprintf(
            '<input type="checkbox" name="item[]" value="%s" />',
            $item['id']
        );
    }
    
    public function get_columns() {
        // Define the columns for your table
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'id'           => 'ID',
            'cardId'       => 'cardId',
            'transId'      => 'transId',
            'status'       => 'status',
            'date_created' => 'date created',
            'date_updated' => 'date updated',

        );
        
        return $columns;
    }
    
    public function get_data() {
        // Retrieve data for your table from the custom table in the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'nafath_callback';
        $data = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A );
        
        return $data;
    }
    
    public function prepare_items() {
        // Prepare the items and pagination for the table
        $data = $this->get_data();
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        $per_page = 20; // Number of items to display per page
        $current_page = $this->get_pagenum();
        $total_items = count( $data );
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );
        
        $this->items = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
    }

    /**
     * Get sortable columns
     * @return array
     */
    function get_sortable_columns(){
        $s_columns = array (
            'transId' => [ 'transId', true], 
            'status'  => [ 'status', true],
        );
        return $s_columns;
    }
}

// Step 4: Create a function to display your custom admin page

function display_custom_admin_page() {
    $list_table = new Custom_List_Table();
    $list_table->prepare_items();
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Nafath Process', 'aqar-gate' ); ?></h1>
        
        <!-- Display your custom table here -->
        <form method="post">
            <?php $list_table->display(); ?>
        </form>
    </div>
    <?php
}

// Step 5: Hook into the WordPress admin menu to add your custom page
function add_custom_admin_page() {
    add_menu_page(
        'Nafath Process',
        'Nafath Process',
        'manage_options',
        'custom-admin-page',
        'display_custom_admin_page',
        'dashicons-admin-generic',
        30
    );
}

add_action( 'admin_menu', 'add_custom_admin_page' );