<?php 

class Aqar_List_user {
    public function __construct() {
        // add_filter( 'user_contactmethods', [ $this, 'new_contact_methods'], 10, 1 );
        add_filter( 'manage_users_columns', [ $this, 'new_modify_user_table'] );
        add_filter( 'manage_users_custom_column', [ $this, 'new_modify_user_table_row'], 10, 3 );
    
    }

    public function new_contact_methods( $contactmethods ) {
        $contactmethods['is_verify'] = 'Info complete';
        return $contactmethods;
    }
    
    
    public function new_modify_user_table( $column ) {
        $column['is_verify'] = 'Complete Information';
        return $column;
    }
    
    public function new_modify_user_table_row( $val, $column_name, $user_id ) {
        switch ($column_name) {
            case 'is_verify' :
                $is_verify = get_the_author_meta( 'aqar_is_verify_user', $user_id );
                if( $is_verify ) {
                    return '✅';
                }else{
                    return '❌';
                }
            default:
        }
        return $val;
    }
}
new Aqar_List_user();



