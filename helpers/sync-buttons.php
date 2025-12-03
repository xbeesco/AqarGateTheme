<?php
/**
 * Add REGA Sync buttons in various locations
 */

// 1. Add button to WordPress Admin Bar when viewing a property
add_action("admin_bar_menu", "add_rega_sync_admin_bar_button", 100);
function add_rega_sync_admin_bar_button($wp_admin_bar) {
    if (!is_singular("property")) return;
    if (!current_user_can("manage_options")) return;
    
    $property_id = get_the_ID();
    $sync_url = admin_url("admin.php?page=single-prop-sync&id=" . $property_id);
    
    $wp_admin_bar->add_node(array(
        "id" => "rega-sync-property",
        "title" => "REGA Sync",
        "href" => $sync_url,
        "meta" => array(
            "class" => "rega-sync-admin-bar",
            "title" => "مزامنة العقار مع هيئة العقار"
        )
    ));
}

// 2. Add meta box with sync button on property edit page
add_action("add_meta_boxes", "add_rega_sync_meta_box");
function add_rega_sync_meta_box() {
    add_meta_box(
        "rega_sync_meta_box",
        "مزامنة REGA",
        "render_rega_sync_meta_box",
        "property",
        "side",
        "high"
    );
}

function render_rega_sync_meta_box($post) {
    $sync_url = admin_url("admin.php?page=single-prop-sync&id=" . $post->ID);
    $advertisement_response = get_post_meta($post->ID, "advertisement_response", true);
    $adLicenseNumber = get_post_meta($post->ID, "adLicenseNumber", true);
    
    echo "<div style=\"text-align: center; padding: 10px 0;\">";
    
    if (!empty($adLicenseNumber)) {
        echo "<p style=\"margin-bottom: 10px; color: #666;\">رقم الترخيص: <strong>" . esc_html($adLicenseNumber) . "</strong></p>";
    }
    
    if (!empty($advertisement_response)) {
        // Get sync timestamp
        $sync_time = "";
        if (isset($advertisement_response["_sync_timestamp"])) {
            $sync_time = $advertisement_response["_sync_timestamp"];
        } elseif (isset($advertisement_response["_sync_timestamp_unix"])) {
            $sync_time = date("Y-m-d H:i:s", $advertisement_response["_sync_timestamp_unix"]);
        }
        
        if (!empty($sync_time)) {
            // Format date in Arabic-friendly format
            $formatted_date = date("Y/m/d", strtotime($sync_time));
            $formatted_time = date("H:i", strtotime($sync_time));
            echo "<p style=\"margin-bottom: 10px; color: #666;\">آخر مزامنة: <strong>" . $formatted_date . "</strong> الساعة <strong>" . $formatted_time . "</strong></p>";
            
            // Show how long ago
            $time_diff = human_time_diff(strtotime($sync_time), current_time("timestamp"));
            echo "<p style=\"margin-bottom: 10px; color: #666; font-size: 12px;\">منذ " . $time_diff . "</p>";
        } else {
            echo "<p style=\"margin-bottom: 10px; color: #666;\">تمت المزامنة سابقاً</p>";
        }
    } else {
        echo "<p style=\"margin-bottom: 10px; color: #dba617;\">⚠️ لم تتم المزامنة بعد</p>";
    }
    
    echo "<a href=\"" . esc_url($sync_url) . "\" class=\"button button-primary button-large\" style=\"width: 100%;\">";
    echo "<span class=\"dashicons dashicons-update\" style=\"margin-top: 4px;\"></span> ";
    echo "مزامنة مع REGA";
    echo "</a>";
    echo "</div>";
}
