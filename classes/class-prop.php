<?php 
class AG_Prop
{
    public function __construct(){
        // Add 'not_authorized' post status.
        add_action( 'init', array( $this, 'not_authorized_status' ) , 9999);

        // if the user not  authorized change change the post status to authorization_pending
        add_action( 'houzez_after_property_submit',array( $this, 'prop_authorization_pending' ), 9999, 1 );
        add_action( 'houzez_after_property_update', array( $this, 'prop_authorization_pending' ), 9999, 1 );
    }

    public function prop_authorization_pending( $prop_id ){
        include_once ( AG_DIR.'classes/class-rega.php' );
        if( REGA::is_valid_ad( $prop_id, get_current_user_id()) !== true  ){
            $post = array( 'ID' => $prop_id, 'post_status' => 'draft' );
            wp_update_post($post);
        } else {
            $post = array( 'ID' => $prop_id, 'post_status' => 'publish' );
            wp_update_post($post);
        }
    }

    public function not_authorized_status(){
        register_post_status( 'not_authorized', array(
            'label'                     => _x( 'Not Authorized', 'property' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Not Authorized <span class="count">(%s)</span>', 'Not Authorized <span class="count">(%s)</span>' ),
        ) );
    }
    
}