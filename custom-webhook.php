<?php
/**
 * Plugin Name: Custom Webhook
 * Description: Allows admins to set a custom webhook URL to send notifications when posts are published.
 * Version: 1.0
 * Author: Tamjid
 * Author URI: https://github.com/tee-jaay
 */
// Function to send the webhook request
function send_custom_webhook($post_id) {
    $post_status = get_post_status($post_id);

    // Check if the post is transitioning to the 'publish' status
    if ($post_status === 'publish') {
        $webhook_url = get_option('custom_webhook_url'); // Get the webhook URL from the database

        // Prepare the webhook payload
        $payload = array(
            'post_id' => $post_id,
            // Include any additional data you want to send in the payload
        );

        // Set the webhook headers
        $headers = array(
            'Content-Type' => 'application/json',
        );

        // Send the webhook request
        wp_remote_post($webhook_url, array(
            'method'  => 'POST',
            'headers' => $headers,
            'body'    => json_encode($payload),
        ));
    }
}

// Add the action to hook into the 'publish_post' event
add_action('publish_post', 'send_custom_webhook');

// Function to add the settings page to the WordPress menu
function custom_webhook_add_settings_page() {
    add_menu_page(
        'Custom Webhook Settings',
        'Custom Webhook',
        'manage_options',
        'custom-webhook-settings',
        'custom_webhook_settings_page',
        'dashicons-admin-generic',
        50
    );
}
add_action('admin_menu', 'custom_webhook_add_settings_page');

// Function to display the settings page
function custom_webhook_settings_page() {
    // Check if the user is an administrator
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }

    // Get the webhook URL from the database
    $webhook_url = get_option('custom_webhook_url');

    // Handle the form submission
    if (isset($_POST['submit'])) {
        // Update the webhook URL in the database
        update_option('custom_webhook_url', $_POST['webhook_url']);

        // Display a success message
        echo '<div class="notice notice-success"><p>Webhook URL updated successfully.</p></div>';
    }
    ?>

    <form method="post">
        <label for="webhook_url">Webhook URL:</label>
        <input type="text" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>">
        <input type="submit" name="submit" value="Update">
    </form>

<?php
}
// function send_custom_webhook($post_id) {
//     $post_status = get_post_status($post_id);

//     // Check if the post is transitioning to the 'publish' status
//     if ($post_status === 'publish') {
//         $webhook_url = 'http://192.168.10.77:3000/api/blog/rss'; 

//         // Prepare the webhook payload
//         $payload = array(
//             'post_id' => $post_id,
//             // Include any additional data you want to send in the payload
//         );

//         // Set the webhook headers
//         $headers = array(
//             'Content-Type' => 'application/json',
//         );

//         // Send the webhook request
//         wp_remote_post($webhook_url, array(
//             'method'  => 'POST',
//             'headers' => $headers,
//             'body'    => json_encode($payload),
//         ));
//     }
// }

// add_action('publish_post', 'send_custom_webhook');