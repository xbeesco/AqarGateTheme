<?php
/**
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 *   1.0 - Edit meta field
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 */
if ( !function_exists( 'aqar_property_area_edit_meta_fields' ) ) :
    function aqar_property_area_edit_meta_fields( $term ) {
        // get meta data value
	    $DISTRICT_ID = get_term_meta( $term->term_id, 'DISTRICT_ID', true );
        ?>

        <tr class="form-field" style="background-color: #fff;border: 1px solid #e5e5e5;">
            <th scope="row" valign="top" style="color: blueviolet; padding-left:10px;"><label><?php _e( 'DISTRICT_ID', 'houzez' ); ?></label></th>
            <td>
            <input name="DISTRICT_ID" id="DISTRICT_ID" type="text" value="<?php echo esc_attr( $DISTRICT_ID ) ?>" />
                <p class="description" style="color: brown;"><?php _e( 'Change Area DISTRICT_ID?', 'houzez' ); ?></p>
            </td>
        </tr>

        <?php
    }
endif;

add_action( 'property_area_edit_form_fields', 'aqar_property_area_edit_meta_fields', 10, 2 );

/**
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 *   1.1 - save meta field
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 */
function property_area_save_term_fields( $term_id ) {
	
	update_term_meta(
		$term_id,
		'DISTRICT_ID',
		sanitize_text_field( $_POST[ 'DISTRICT_ID' ] )
	);	
}
add_action( 'created_property_area', 'property_area_save_term_fields' );
add_action( 'edited_property_area', 'property_area_save_term_fields' );
/**
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 *   2.0 - Edit meta field
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 */
if ( !function_exists( 'aqar_property_city_edit_meta_fields' ) ) :
    function aqar_property_city_edit_meta_fields( $term ) {
        // get meta data value
	    $CITY_ID = get_term_meta( $term->term_id, 'CITY_ID', true );
        ?>

        <tr class="form-field" style="background-color: #fff;border: 1px solid #e5e5e5;">
            <th scope="row" valign="top" style="color: blueviolet; padding-left:10px;"><label><?php _e( 'CITY_ID', 'houzez' ); ?></label></th>
            <td>
            <input name="CITY_ID" id="CITY_ID" type="text" value="<?php echo esc_attr( $CITY_ID ) ?>" />
                <p class="description" style="color: brown;"><?php _e( 'Change Area CITY_ID?', 'houzez' ); ?></p>
            </td>
        </tr>

        <?php
    }
endif;

add_action( 'property_city_edit_form_fields', 'aqar_property_city_edit_meta_fields', 10, 2 );
/**
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 *   2.1 - save meta field
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 */
function property_city_save_term_fields( $term_id ) {
	
	update_term_meta(
		$term_id,
		'CITY_ID',
		sanitize_text_field( $_POST[ 'CITY_ID' ] )
	);	
}
add_action( 'created_property_city', 'property_city_save_term_fields' );
add_action( 'edited_property_city', 'property_city_save_term_fields' );
/**
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 *   3.0 - Edit meta field
 *   ----------------------------------------------------------------------------------------------------------------------------------------------------
 */
if ( !function_exists( 'aqar_property_state_edit_meta_fields' ) ) :
    function aqar_property_state_edit_meta_fields( $term ) {
        // get meta data value
	    $REGION_ID = get_term_meta( $term->term_id, 'REGION_ID', true );
        ?>

        <tr class="form-field" style="background-color: #fff;border: 1px solid #e5e5e5;">
            <th scope="row" valign="top" style="color: blueviolet; padding-left:10px;"><label><?php _e( 'REGION_ID', 'houzez' ); ?></label></th>
            <td>
            <input name="REGION_ID" id="REGION_ID" type="text" value="<?php echo esc_attr( $REGION_ID ) ?>" />
                <p class="description" style="color: brown;"><?php _e( 'Change Area REGION_ID?', 'houzez' ); ?></p>
            </td>
        </tr>

        <?php
    }
endif;

add_action( 'property_state_edit_form_fields', 'aqar_property_state_edit_meta_fields', 10, 2 );
/**
*  ----------------------------------------------------------------------------------------------------------------------------------------------------
*   3.1 - save meta field
*  ----------------------------------------------------------------------------------------------------------------------------------------------------
*/
function property_state_save_term_fields( $term_id ) {
	
	update_term_meta(
		$term_id,
		'REGION_ID',
		sanitize_text_field( $_POST[ 'REGION_ID' ] )
	);	
}
add_action( 'created_property_state', 'property_state_save_term_fields' );
add_action( 'edited_property_state', 'property_state_save_term_fields' );
