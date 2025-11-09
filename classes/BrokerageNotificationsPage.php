<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('BrokerageNotificationsPage')) {

    class BrokerageNotificationsPage
    {

        protected $dublicate;
        private $per_page = 10;

        public function __construct()
        {
            add_action('admin_menu', [$this, 'register_menu']);
            $this->dublicate = true;
        }

        public function register_menu()
        {
            add_menu_page(
                'Brokerage Notifications',
                'Brokerage Notifications',
                'manage_options',
                'brokerage-notifications',
                [$this, 'render_page'],
                'dashicons-bell',
                26
            );
        }

        public function render_page()
        {
            $notifications = get_option('brokerage_notifications');
            if (!is_array($notifications)) {
                echo '<div class="wrap"><h1>Brokerage Notifications</h1><p>No data found.</p></div>';
                return;
            }

            if (!$this->dublicate) {
                // ✅ Filter: keep only latest for each AdlicenseNumber
                $latest_notifications = [];
                foreach ($notifications as $item) {
                    $body = $item['NotificationBody'];
                    $license = $body['AdlicenseNumber'] ?? null;
                    $date = strtotime($body['ActionDateTime'] ?? '0000-00-00');

                    if ($license) {
                        if (!isset($latest_notifications[$license]) || $date > strtotime($latest_notifications[$license]['NotificationBody']['ActionDateTime'])) {
                            $latest_notifications[$license] = $item;
                        }
                    }
                }
                $notifications = array_values($latest_notifications);
            }

            // Handle search
            $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

            if ($search_query) {
                $notifications = array_filter($notifications, function ($item) use ($search_query) {
                    $post = get_post($item['property_id']);
                    return $post && stripos($post->post_title, $search_query) !== false;
                });
            }

            // Pagination
            $total_items = count($notifications);
            $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            $offset = ($paged - 1) * $this->per_page;
            $paged_items = array_slice($notifications, $offset, $this->per_page);

            $total_pages = ceil($total_items / $this->per_page);

            // Output
            echo '<div class="wrap">';
            echo '<h1>Brokerage Notifications</h1>';

            // Search form
            echo '<form method="get" action="">';
            echo '<input type="hidden" name="page" value="brokerage-notifications" />';
            echo '<input type="search" name="s" value="' . esc_attr($search_query) . '" placeholder="Search by post title..." />';
            submit_button('Search', '', '', false);
            echo '</form><br>';

            // Table
            if (!empty($paged_items)) {
                echo '<table class="widefat fixed striped">';
                echo '<thead><tr>
                        <th>Property ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>License Number</th>
                        <th>Status Code</th>
                        <th>Status (AR)</th>
                        <th>Action Date</th>
                        <th>Reason</th>
                    </tr></thead><tbody>';

                foreach ($paged_items as $item) {
                    $body = $item['NotificationBody'];
                    $property_id = $item['property_id'] ?? 0;
                    if ($property_id === 0) {
                        $property_id = $this->get_property_id_by_adLicenseNumber($body['AdlicenseNumber']);
                    }
                    if (!$property_id) {
                        continue; // Skip if no property ID found
                    }
                    $post = get_post($property_id);

                    $post_title = $post ? esc_html(get_the_title($post)) : '<em>Not found</em>';
                    $post_status = $post ? esc_html($post->post_status) : '-';
                    $edit_link = $post ? get_edit_post_link($post->ID) : '';

                    echo '<tr>';
                    echo '<td>' . esc_html($property_id) . '</td>';
                    echo '<td>';
                    echo $edit_link ? '<a href="' . esc_url($edit_link) . '">' . $post_title . '</a>' : $post_title;
                    echo '</td>';
                    echo '<td>' . $post_status . '</td>';
                    echo '<td>' . esc_html($body['AdlicenseNumber'] ?? '') . '</td>';
                    echo '<td>' . esc_html($body['AdlicenseStatusCode'] ?? '') . '</td>';
                    echo '<td>' . esc_html($body['AdlicenseStatusNameAR'] ?? '') . '</td>';
                    echo '<td>' . esc_html($body['ActionDateTime'] ?? '') . '</td>';
                    echo '<td>' . (isset($body['ActionReason ']) ? $body['ActionReason '] : 'OwnerDesire') . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';

                // Pagination links
                echo '<div class="tablenav"><div class="tablenav-pages">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    $link = add_query_arg([
                        'page' => 'brokerage-notifications',
                        'paged' => $i,
                        's' => $search_query,
                    ]);
                    $class = ($i == $paged) ? ' class="current-page"' : '';
                    echo '<a' . $class . ' href="' . esc_url($link) . '">' . $i . '</a> ';
                }
                echo '</div></div>';

            } else {
                echo '<p>No notifications found.</p>';
            }

            echo '</div>';
        }

        /* ------------ // Get the property ID using the AdlicenseNumber ------------ */
        public function get_property_id_by_adLicenseNumber($adLicenseNumber)
        {
            if (is_numeric($adLicenseNumber)) {
                global $wpdb;

                $post_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
                    'adLicenseNumber',
                    $adLicenseNumber
                ));

                if ($post_id) {
                    return $post_id;
                } else {
                    return null;
                }

            }

            return null;
        }
    }

    new BrokerageNotificationsPage();
}

