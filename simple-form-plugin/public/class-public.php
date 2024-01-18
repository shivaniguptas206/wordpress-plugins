<?php


add_filter('the_content', 'sfp_display_car_listing_shortcode_on_selected_page');
add_filter('the_content', 'sfp_display_car_shortcode_on_selected_page');
add_action('wp_enqueue_scripts', 'sfp_enqueue_ajax_script');
add_shortcode('car_listing', 'sfp_car_list_table');
add_shortcode('car_add', 'sfp_car_add_form');
add_action('wp_ajax_save_car_data', 'sfp_save_car_data');
add_action('wp_ajax_nopriv_save_car_data', 'sfp_save_car_data');

// Function to enable or disable the shortcode based on settings
function sfp_display_car_shortcode_on_selected_page($content) {
    $enable_frontend_form = get_option('enable_frontend_form', 0);

    // Check if the "Enable Form in Frontend" option is enabled
    if ($enable_frontend_form == 1) {
        $selected_page = get_option('selected_page', 0);

        // Check if the current page matches the selected page
        if (is_page($selected_page)) {
            // Display the [car_add] shortcode on the selected page
            $content .= do_shortcode('[car_add]');
        }
    }

    return $content;
}
// Function to enable or disable the shortcode based on settings
function sfp_display_car_listing_shortcode_on_selected_page($content) {
    $enable_frontend_form = get_option('enable_car_listing', 0);

    // Check if the "Enable Form in Frontend" option is enabled
    if ($enable_frontend_form == 1) {
        $selected_page = get_option('selected_car_list_page', 0);

        // Check if the current page matches the selected page
        if (is_page($selected_page)) {
            // Display the [car_add] shortcode on the selected page
            $content .= do_shortcode('[car_listing]');
        }
    }

    return $content;
}
//enqueue js and css
function sfp_enqueue_ajax_script() {
    wp_enqueue_script('car-add-ajax', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '1.0', true);

    $localized_data = array(
        'car_add_url' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('car-add-ajax', 'car_add_vars', $localized_data);

    // Enqueue the CSS file.
    wp_enqueue_style('custom-plugin-styles', plugin_dir_url(__FILE__) . 'css/public-styles.css');
}
// car_listing shortcode function 
function sfp_car_list_table() {
    ob_start();
    ?>

    <table class="custom-car-list-table">
        <thead>
            <tr>
                <th>Featured Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Model</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query and loop through your car posts here
            $car_posts = new WP_Query(array(
                'post_type' => 'car',
                'posts_per_page' => -1, // Display all cars
            ));

            while ($car_posts->have_posts()) : $car_posts->the_post();
                $featured_image = get_the_post_thumbnail();
                $car_name = get_the_title();
                $car_price = get_post_meta(get_the_ID(), 'car_price', true);
                $car_model = get_the_terms(get_the_ID(), 'model_taxonomy');
                $date_created = get_the_date('F j, Y');
                $delete_link = get_delete_post_link(get_the_ID());

                echo '<tr>';
                echo '<td class="custom-img">' . $featured_image . '</td>';
                echo '<td>' . $car_name . '</td>';
                echo '<td>' . $car_price . '</td>';
                echo '<td>';
                if ($car_model) {
                    foreach ($car_model as $model) {
                        echo $model->name . ', ';
                    }
                }
                echo '</td>';
                echo '<td>' . $date_created . '</td>';
                echo '<td class="custom-action-buttons">';
                echo '<a class="custom-delete-button" href="' . $delete_link . '" onclick="return confirm(\'Are you sure you want to delete this car?\');">Delete</a>';
                echo '</td>';
                echo '</tr>';
            endwhile;

            wp_reset_postdata();
            ?>
        </tbody>
    </table>

    <?php
    return ob_get_clean();
}
// car_add shortcode function 

function sfp_car_add_form() {
    ob_start();

    if (!is_user_logged_in()) {
        return 'You must be logged in to add a car.';
    }

    ?>
    <form id="car-add-form" method="post" enctype="multipart/form-data">
        <div class="custom-form-group">
            <label for="car_name">Car Name</label>
            <input type="text" id="car_name" name="car_name" required>
        </div>
        <div class="custom-form-group">
            <label for="car_description">Car Description</label>
            <textarea id="car_description" name="car_description" rows="4" required></textarea>
        </div>
        <div class="custom-form-group">
            <label for="car_price">Car Price</label>
            <input type="text" id="car_price" name="car_price" required>
        </div>
        <div class="custom-form-group">
            <label for="car_images">Car Images</label>
            <input type="file" id="car_images" name="car_images" accept="image/*" required>
        </div>
        <div class="custom-form-group">
            <label for="car_model">Car Model</label>
            <!-- Select field for existing model names -->
            <select id="car_model" name="car_model">
                <option value="custom">Custom Model</option>
                <?php
                $terms = get_terms(array('taxonomy' => 'model_taxonomy'));
                foreach ($terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                }
                ?>
            </select></br></br>
             <!-- Input field for custom model name -->
             <input type="text" id="custom_model_name" name="custom_model_name" placeholder="Enter Custom Model Name">

        </div>

        <div class="custom-form-group">
            <button type="submit">Submit</button>
        </div>
    </form>
    <div id="car-add-response"></div>
    <?php

    return ob_get_clean();
}
// car data save ajax function shortcode function 

function sfp_save_car_data() {
    $car_name = sanitize_text_field($_POST['car_name']);
    $car_description = wp_kses_post($_POST['car_description']);
    $car_price = sanitize_text_field($_POST['car_price']);
    $car_model = $_POST['car_model'];
    $car_image = sfp_upload_car_image(); // You should have your upload_car_image() function defined.

    $post_id = wp_insert_post(array(
        'post_title' => $car_name,
        'post_content' => $car_description,
        'post_status' => 'publish',
        'post_type' => 'car',
        'post_author' => get_current_user_id(),
    ));

    if (!is_wp_error($post_id)) {
        // Set the car price as post meta
        update_post_meta($post_id, 'car_price', $car_price);
        if ($car_model === 'custom') {
            // Handle custom model name
            $custom_model_name = sanitize_text_field($_POST['custom_model_name']);
            // Create a new term in the "model_taxonomy" taxonomy with $custom_model_name
            $term = wp_insert_term($custom_model_name, 'model_taxonomy');
            if (!is_wp_error($term)) {
                // The term was created successfully
                $car_model_id = $term['term_id'];
            } else {
                // Handle the error, e.g., display an error message
            }
        } else {
            // Use the selected model's ID
            $car_model_id = intval($car_model);
        }
        

        // Set the car model as a term (taxonomy)
        wp_set_object_terms($post_id, $car_model_id, 'model_taxonomy');

        // Set the car image as a featured image
        set_post_thumbnail($post_id, $car_image);

        $response = array(
            'status' => 'success',
            'message' => 'Car details saved successfully.',
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error saving car details. Please try again.',
        );
    }

    wp_send_json($response);
}
// Function to handle car image upload
function sfp_upload_car_image() {
    if (isset($_FILES['car_images']) && !empty($_FILES['car_images']['name'])) {
        $file = $_FILES['car_images'];

        $upload_overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded_file['file'])) {
            $file_name = basename($uploaded_file['file']);
            $file_type = wp_check_filetype($file_name);
            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                'post_content' => '',
                'post_status' => 'inherit',
            );

            $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                return $attachment_id;
            }
        }
    }
    return false;
}
