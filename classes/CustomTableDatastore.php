<?php
use Carbon_Fields\Field\Field;
use Carbon_Fields\Datastore\Datastore;
use Carbon_Fields\Datastore\Datastore_Interface;

class CustomTableDatastore extends Datastore {

    /**
     * Initialization tasks for concrete datastores.
     **/
    public function init() {}

    protected function get_key_for_field(Field $field) {
        return $field->get_base_name();
    }

    public function load(Field $field) {
        global $wpdb, $post;
        $key = $this->get_key_for_field($field);
        $post_id = $post->ID;

        // Query the custom table to get the field value
        $table_name = $wpdb->prefix . 'property_requests';
        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT $key FROM $table_name WHERE post_id = %d",
            $post_id
        ));
  
        return $value;
    }

    public function save(Field $field) {
        global $wpdb, $post;
        $key = $this->get_key_for_field($field);
        $value = $field->get_value();
        $post_id = $post->ID;

        // Insert or update the value in the custom table
        $table_name = $wpdb->prefix . 'property_requests';
        
        // تحقق مما إذا كان الصف موجودًا بالفعل
        $existing_row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE post_id = %d",
            $post_id
        ));

        if ($existing_row) {
            // تحديث الصف الموجود
            $wpdb->update(
                $table_name,
                array($key => $value),
                array('post_id' => $post_id)
            );
        } else {
            // إدراج صف جديد
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    $key => $value
                )
            );
        }
    }

    public function delete(Field $field) {
        global $wpdb;
        $key = $this->get_key_for_field($field);
        $post_id = $field->get_value();

        // Delete the value from the custom table
        $table_name = $wpdb->prefix . 'property_requests';
        $wpdb->delete($table_name, array('id' => $post_id));
    }
}