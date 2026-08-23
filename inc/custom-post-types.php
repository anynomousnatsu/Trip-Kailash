<?php
/**
 * Register Custom Post Types
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types
 */
function trip_kailash_register_post_types()
{
    // Register Pilgrimage Package CPT
    register_post_type('pilgrimage_package', array(
        'labels' => array(
            'name' => esc_html__('Pilgrimage Packages', 'trip-kailash'),
            'singular_name' => esc_html__('Pilgrimage Package', 'trip-kailash'),
            'add_new' => esc_html__('Add New', 'trip-kailash'),
            'add_new_item' => esc_html__('Add New Package', 'trip-kailash'),
            'edit_item' => esc_html__('Edit Package', 'trip-kailash'),
            'new_item' => esc_html__('New Package', 'trip-kailash'),
            'view_item' => esc_html__('View Package', 'trip-kailash'),
            'search_items' => esc_html__('Search Packages', 'trip-kailash'),
            'not_found' => esc_html__('No packages found', 'trip-kailash'),
            'not_found_in_trash' => esc_html__('No packages found in trash', 'trip-kailash'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'packages'),
        'menu_icon' => 'dashicons-palmtree',
    ));

    // Register Guide CPT
    register_post_type('guide', array(
        'labels' => array(
            'name' => esc_html__('Guides', 'trip-kailash'),
            'singular_name' => esc_html__('Guide', 'trip-kailash'),
            'add_new' => esc_html__('Add New', 'trip-kailash'),
            'add_new_item' => esc_html__('Add New Guide', 'trip-kailash'),
            'edit_item' => esc_html__('Edit Guide', 'trip-kailash'),
            'new_item' => esc_html__('New Guide', 'trip-kailash'),
            'view_item' => esc_html__('View Guide', 'trip-kailash'),
            'search_items' => esc_html__('Search Guides', 'trip-kailash'),
            'not_found' => esc_html__('No guides found', 'trip-kailash'),
            'not_found_in_trash' => esc_html__('No guides found in trash', 'trip-kailash'),
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-groups',
    ));

    // Register Lodge CPT
    register_post_type('lodge', array(
        'labels' => array(
            'name' => esc_html__('Lodges', 'trip-kailash'),
            'singular_name' => esc_html__('Lodge', 'trip-kailash'),
            'add_new' => esc_html__('Add New', 'trip-kailash'),
            'add_new_item' => esc_html__('Add New Lodge', 'trip-kailash'),
            'edit_item' => esc_html__('Edit Lodge', 'trip-kailash'),
            'new_item' => esc_html__('New Lodge', 'trip-kailash'),
            'view_item' => esc_html__('View Lodge', 'trip-kailash'),
            'search_items' => esc_html__('Search Lodges', 'trip-kailash'),
            'not_found' => esc_html__('No lodges found', 'trip-kailash'),
            'not_found_in_trash' => esc_html__('No lodges found in trash', 'trip-kailash'),
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-admin-home',
    ));
}
add_action('init', 'trip_kailash_register_post_types');

/**
 * Register custom meta fields for pilgrimage packages
 */
function trip_kailash_register_package_meta()
{
    // String fields
    register_post_meta('pilgrimage_package', 'trip_length', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));

    register_post_meta('pilgrimage_package', 'deity', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));

    register_post_meta('pilgrimage_package', 'difficulty', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));

    register_post_meta('pilgrimage_package', 'best_months', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));

    // Boolean fields
    register_post_meta('pilgrimage_package', 'has_lodge', array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => true,
        'default' => false,
    ));

    register_post_meta('pilgrimage_package', 'has_helicopter', array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => true,
        'default' => false,
    ));

    register_post_meta('pilgrimage_package', 'includes_meals', array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => true,
        'default' => false,
    ));

    register_post_meta('pilgrimage_package', 'includes_rituals', array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => true,
        'default' => false,
    ));

    // Integer field
    register_post_meta('pilgrimage_package', 'price_from', array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => true,
        'default' => 0,
    ));

    // Array field (serialized)
    register_post_meta('pilgrimage_package', 'key_stops', array(
        'type' => 'array',
        'single' => true,
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string',
                ),
            ),
        ),
        'default' => array(),
    ));
}
add_action('init', 'trip_kailash_register_package_meta');

/**
 * Add meta box for pilgrimage package details
 */
function trip_kailash_add_package_meta_box()
{
    add_meta_box(
        'trip_kailash_package_details',
        esc_html__('Package Details', 'trip-kailash'),
        'trip_kailash_render_package_meta_box',
        'pilgrimage_package',
        'normal',
        'high'
    );
}
/*
 * Retired in favour of inc/package-admin.php, which generates its panels from
 * tk_package_field_schema().
 *
 * Unhooking the save handler is the part that matters. Both systems own
 * price_from and best_months, and they disagree about the shape: the old one
 * writes best_months as a text string, the new one as a list of month
 * integers. Left hooked, whichever ran last would quietly corrupt the other's
 * data on every update.
 *
 * The functions stay defined so nothing that calls them fatals.
 */
// add_action('add_meta_boxes', 'trip_kailash_add_package_meta_box');

/**
 * Render the package details meta box
 */
function trip_kailash_render_package_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('trip_kailash_save_package_meta', 'trip_kailash_package_meta_nonce');

    // Get current values
    $trip_length = get_post_meta($post->ID, 'trip_length', true);
    $deity = get_post_meta($post->ID, 'deity', true);
    $difficulty = get_post_meta($post->ID, 'difficulty', true);
    $best_months = get_post_meta($post->ID, 'best_months', true);
    $has_lodge = get_post_meta($post->ID, 'has_lodge', true);
    $has_helicopter = get_post_meta($post->ID, 'has_helicopter', true);
    $includes_meals = get_post_meta($post->ID, 'includes_meals', true);
    $includes_rituals = get_post_meta($post->ID, 'includes_rituals', true);
    $price_from = get_post_meta($post->ID, 'price_from', true);
    $key_stops = get_post_meta($post->ID, 'key_stops', true);

    if (!is_array($key_stops)) {
        $key_stops = array();
    }
    ?>
    <style>
        .tk-meta-field {
            margin-bottom: 20px;
        }

        .tk-meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tk-meta-field input[type="text"],
        .tk-meta-field input[type="number"],
        .tk-meta-field select {
            width: 100%;
            max-width: 400px;
        }

        .tk-meta-field input[type="checkbox"] {
            margin-right: 5px;
        }

        .tk-key-stops {
            margin-top: 10px;
        }

        .tk-key-stop-item {
            margin-bottom: 10px;
        }

        .tk-key-stop-item input {
            width: 100%;
            max-width: 400px;
        }

        .tk-add-stop {
            margin-top: 10px;
        }
    </style>

    <div class="tk-meta-field">
        <label for="trip_length"><?php esc_html_e('Trip Length', 'trip-kailash'); ?></label>
        <input type="text" id="trip_length" name="trip_length" value="<?php echo esc_attr($trip_length); ?>"
            placeholder="e.g., 7 nights / 8 days">
        <p class="description">
            <?php esc_html_e('Enter the duration of the trip (e.g., "7 nights / 8 days")', 'trip-kailash'); ?></p>
    </div>

    <div class="tk-meta-field">
        <label for="deity"><?php esc_html_e('Deity', 'trip-kailash'); ?></label>
        <select id="deity" name="deity">
            <option value=""><?php esc_html_e('Select Deity', 'trip-kailash'); ?></option>
            <option value="shiva" <?php selected($deity, 'shiva'); ?>><?php esc_html_e('Shiva', 'trip-kailash'); ?>
            </option>
            <option value="vishnu" <?php selected($deity, 'vishnu'); ?>><?php esc_html_e('Vishnu', 'trip-kailash'); ?>
            </option>
            <option value="devi" <?php selected($deity, 'devi'); ?>><?php esc_html_e('Devi', 'trip-kailash'); ?>
            </option>
        </select>
    </div>

    <div class="tk-meta-field">
        <label for="difficulty"><?php esc_html_e('Difficulty', 'trip-kailash'); ?></label>
        <select id="difficulty" name="difficulty">
            <option value=""><?php esc_html_e('Select Difficulty', 'trip-kailash'); ?></option>
            <option value="Easy" <?php selected($difficulty, 'Easy'); ?>><?php esc_html_e('Easy', 'trip-kailash'); ?>
            </option>
            <option value="Moderate" <?php selected($difficulty, 'Moderate'); ?>>
                <?php esc_html_e('Moderate', 'trip-kailash'); ?></option>
            <option value="Challenging" <?php selected($difficulty, 'Challenging'); ?>>
                <?php esc_html_e('Challenging', 'trip-kailash'); ?></option>
        </select>
    </div>

    <div class="tk-meta-field">
        <label for="best_months"><?php esc_html_e('Best Months', 'trip-kailash'); ?></label>
        <input type="text" id="best_months" name="best_months" value="<?php echo esc_attr($best_months); ?>"
            placeholder="e.g., March-May, September-November">
        <p class="description"><?php esc_html_e('Enter the best months to visit', 'trip-kailash'); ?></p>
    </div>

    <div class="tk-meta-field">
        <label for="price_from"><?php esc_html_e('Price From ($)', 'trip-kailash'); ?></label>
        <input type="number" id="price_from" name="price_from" value="<?php echo esc_attr($price_from); ?>" min="0"
            step="1">
        <p class="description"><?php esc_html_e('Enter the starting price in USD', 'trip-kailash'); ?></p>
    </div>

    <div class="tk-meta-field">
        <label>
            <input type="checkbox" name="has_lodge" value="1" <?php checked($has_lodge, true); ?>>
            <?php esc_html_e('Has Lodge', 'trip-kailash'); ?>
        </label>
    </div>

    <div class="tk-meta-field">
        <label>
            <input type="checkbox" name="has_helicopter" value="1" <?php checked($has_helicopter, true); ?>>
            <?php esc_html_e('Has Helicopter', 'trip-kailash'); ?>
        </label>
    </div>

    <div class="tk-meta-field">
        <label>
            <input type="checkbox" name="includes_meals" value="1" <?php checked($includes_meals, true); ?>>
            <?php esc_html_e('Includes Meals', 'trip-kailash'); ?>
        </label>
    </div>

    <div class="tk-meta-field">
        <label>
            <input type="checkbox" name="includes_rituals" value="1" <?php checked($includes_rituals, true); ?>>
            <?php esc_html_e('Includes Rituals', 'trip-kailash'); ?>
        </label>
    </div>

    <div class="tk-meta-field">
        <label><?php esc_html_e('Key Stops', 'trip-kailash'); ?></label>
        <div class="tk-key-stops" id="tk-key-stops">
            <?php
            if (!empty($key_stops)) {
                foreach ($key_stops as $index => $stop) {
                    ?>
                    <div class="tk-key-stop-item">
                        <input type="text" name="key_stops[]" value="<?php echo esc_attr($stop); ?>"
                            placeholder="<?php esc_attr_e('Enter stop name', 'trip-kailash'); ?>">
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="tk-key-stop-item">
                    <input type="text" name="key_stops[]" value=""
                        placeholder="<?php esc_attr_e('Enter stop name', 'trip-kailash'); ?>">
                </div>
                <?php
            }
            ?>
        </div>
        <button type="button" class="button tk-add-stop"
            id="tk-add-stop"><?php esc_html_e('Add Stop', 'trip-kailash'); ?></button>
        <p class="description"><?php esc_html_e('Add key stops for this pilgrimage package', 'trip-kailash'); ?></p>
    </div>

    <script>
        (function () {
            document.getElementById('tk-add-stop').addEventListener('click', function () {
                var container = document.getElementById('tk-key-stops');
                var newStop = document.createElement('div');
                newStop.className = 'tk-key-stop-item';
                newStop.innerHTML = '<input type="text" name="key_stops[]" value="" placeholder="<?php esc_attr_e('Enter stop name', 'trip-kailash'); ?>">';
                container.appendChild(newStop);
            });
        })();
    </script>
    <?php
}

/**
 * Save package meta box data
 */
function trip_kailash_save_package_meta($post_id)
{
    // Check nonce
    if (
        !isset($_POST['trip_kailash_package_meta_nonce']) ||
        !wp_verify_nonce($_POST['trip_kailash_package_meta_nonce'], 'trip_kailash_save_package_meta')
    ) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save string fields
    if (isset($_POST['trip_length'])) {
        update_post_meta($post_id, 'trip_length', sanitize_text_field($_POST['trip_length']));
    }

    if (isset($_POST['deity'])) {
        update_post_meta($post_id, 'deity', sanitize_text_field($_POST['deity']));
    }

    if (isset($_POST['difficulty'])) {
        update_post_meta($post_id, 'difficulty', sanitize_text_field($_POST['difficulty']));
    }

    if (isset($_POST['best_months'])) {
        update_post_meta($post_id, 'best_months', sanitize_text_field($_POST['best_months']));
    }

    // Save boolean fields
    update_post_meta($post_id, 'has_lodge', isset($_POST['has_lodge']) ? true : false);
    update_post_meta($post_id, 'has_helicopter', isset($_POST['has_helicopter']) ? true : false);
    update_post_meta($post_id, 'includes_meals', isset($_POST['includes_meals']) ? true : false);
    update_post_meta($post_id, 'includes_rituals', isset($_POST['includes_rituals']) ? true : false);

    // Save integer field
    if (isset($_POST['price_from'])) {
        update_post_meta($post_id, 'price_from', absint($_POST['price_from']));
    }

    // Save array field (key stops)
    if (isset($_POST['key_stops']) && is_array($_POST['key_stops'])) {
        $key_stops = array_filter(array_map('sanitize_text_field', $_POST['key_stops']));
        update_post_meta($post_id, 'key_stops', $key_stops);
    } else {
        update_post_meta($post_id, 'key_stops', array());
    }
}
/*
 * Retired in favour of inc/package-admin.php, which generates its panels from
 * tk_package_field_schema().
 *
 * Unhooking the save handler is the part that matters. Both systems own
 * price_from and best_months, and they disagree about the shape: the old one
 * writes best_months as a text string, the new one as a list of month
 * integers. Left hooked, whichever ran last would quietly corrupt the other's
 * data on every update.
 *
 * The functions stay defined so nothing that calls them fatals.
 */
// add_action('save_post_pilgrimage_package', 'trip_kailash_save_package_meta');

/**
 * Register custom meta fields for guides
 */
function trip_kailash_register_guide_meta()
{
    // Integer field
    register_post_meta('guide', 'years_of_experience', array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => true,
        'default' => 0,
    ));

    // Text field
    register_post_meta('guide', 'short_bio', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));
}
add_action('init', 'trip_kailash_register_guide_meta');

/**
 * Register custom meta fields for lodges
 */
function trip_kailash_register_lodge_meta()
{
    // String field
    register_post_meta('lodge', 'location', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'default' => '',
    ));

    // Array field (serialized)
    register_post_meta('lodge', 'amenities', array(
        'type' => 'array',
        'single' => true,
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string',
                ),
            ),
        ),
        'default' => array(),
    ));
}
add_action('init', 'trip_kailash_register_lodge_meta');

/**
 * Add meta box for guide details
 */
function trip_kailash_add_guide_meta_box()
{
    add_meta_box(
        'trip_kailash_guide_details',
        esc_html__('Guide Details', 'trip-kailash'),
        'trip_kailash_render_guide_meta_box',
        'guide',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'trip_kailash_add_guide_meta_box');

/**
 * Render the guide details meta box
 */
function trip_kailash_render_guide_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('trip_kailash_save_guide_meta', 'trip_kailash_guide_meta_nonce');

    // Get current values
    $years_of_experience = get_post_meta($post->ID, 'years_of_experience', true);
    $short_bio = get_post_meta($post->ID, 'short_bio', true);
    ?>
    <style>
        .tk-meta-field {
            margin-bottom: 20px;
        }

        .tk-meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tk-meta-field input[type="number"] {
            width: 100%;
            max-width: 200px;
        }

        .tk-meta-field textarea {
            width: 100%;
            max-width: 600px;
            height: 100px;
        }
    </style>

    <div class="tk-meta-field">
        <label for="years_of_experience"><?php esc_html_e('Years of Experience', 'trip-kailash'); ?></label>
        <input type="number" id="years_of_experience" name="years_of_experience"
            value="<?php echo esc_attr($years_of_experience); ?>" min="0" step="1">
        <p class="description"><?php esc_html_e('Enter the number of years of guiding experience', 'trip-kailash'); ?></p>
    </div>

    <div class="tk-meta-field">
        <label for="short_bio"><?php esc_html_e('Short Bio', 'trip-kailash'); ?></label>
        <textarea id="short_bio" name="short_bio" rows="5"><?php echo esc_textarea($short_bio); ?></textarea>
        <p class="description"><?php esc_html_e('Enter a brief biography for the guide', 'trip-kailash'); ?></p>
    </div>
    <?php
}

/**
 * Save guide meta box data
 */
function trip_kailash_save_guide_meta($post_id)
{
    // Check nonce
    if (
        !isset($_POST['trip_kailash_guide_meta_nonce']) ||
        !wp_verify_nonce($_POST['trip_kailash_guide_meta_nonce'], 'trip_kailash_save_guide_meta')
    ) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save integer field
    if (isset($_POST['years_of_experience'])) {
        update_post_meta($post_id, 'years_of_experience', absint($_POST['years_of_experience']));
    }

    // Save text field
    if (isset($_POST['short_bio'])) {
        update_post_meta($post_id, 'short_bio', sanitize_textarea_field($_POST['short_bio']));
    }
}
add_action('save_post_guide', 'trip_kailash_save_guide_meta');

/**
 * Add meta box for lodge details
 */
function trip_kailash_add_lodge_meta_box()
{
    add_meta_box(
        'trip_kailash_lodge_details',
        esc_html__('Lodge Details', 'trip-kailash'),
        'trip_kailash_render_lodge_meta_box',
        'lodge',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'trip_kailash_add_lodge_meta_box');

/**
 * Render the lodge details meta box
 */
function trip_kailash_render_lodge_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('trip_kailash_save_lodge_meta', 'trip_kailash_lodge_meta_nonce');

    // Get current values
    $location = get_post_meta($post->ID, 'location', true);
    $amenities = get_post_meta($post->ID, 'amenities', true);

    if (!is_array($amenities)) {
        $amenities = array();
    }
    ?>
    <style>
        .tk-meta-field {
            margin-bottom: 20px;
        }

        .tk-meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tk-meta-field input[type="text"] {
            width: 100%;
            max-width: 400px;
        }

        .tk-amenities {
            margin-top: 10px;
        }

        .tk-amenity-item {
            margin-bottom: 10px;
        }

        .tk-amenity-item input {
            width: 100%;
            max-width: 400px;
        }

        .tk-add-amenity {
            margin-top: 10px;
        }
    </style>

    <div class="tk-meta-field">
        <label for="location"><?php esc_html_e('Location', 'trip-kailash'); ?></label>
        <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>"
            placeholder="e.g., Kathmandu, Nepal">
        <p class="description"><?php esc_html_e('Enter the location of the lodge', 'trip-kailash'); ?></p>
    </div>

    <div class="tk-meta-field">
        <label><?php esc_html_e('Amenities', 'trip-kailash'); ?></label>
        <div class="tk-amenities" id="tk-amenities">
            <?php
            if (!empty($amenities)) {
                foreach ($amenities as $index => $amenity) {
                    ?>
                    <div class="tk-amenity-item">
                        <input type="text" name="amenities[]" value="<?php echo esc_attr($amenity); ?>"
                            placeholder="<?php esc_attr_e('Enter amenity name', 'trip-kailash'); ?>">
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="tk-amenity-item">
                    <input type="text" name="amenities[]" value=""
                        placeholder="<?php esc_attr_e('Enter amenity name', 'trip-kailash'); ?>">
                </div>
                <?php
            }
            ?>
        </div>
        <button type="button" class="button tk-add-amenity"
            id="tk-add-amenity"><?php esc_html_e('Add Amenity', 'trip-kailash'); ?></button>
        <p class="description">
            <?php esc_html_e('Add amenities available at this lodge (e.g., Hot Water, Puja Room, WiFi)', 'trip-kailash'); ?>
        </p>
    </div>

    <script>
        (function () {
            document.getElementById('tk-add-amenity').addEventListener('click', function () {
                var container = document.getElementById('tk-amenities');
                var newAmenity = document.createElement('div');
                newAmenity.className = 'tk-amenity-item';
                newAmenity.innerHTML = '<input type="text" name="amenities[]" value="" placeholder="<?php esc_attr_e('Enter amenity name', 'trip-kailash'); ?>">';
                container.appendChild(newAmenity);
            });
        })();
    </script>
    <?php
}

/**
 * Save lodge meta box data
 */
function trip_kailash_save_lodge_meta($post_id)
{
    // Check nonce
    if (
        !isset($_POST['trip_kailash_lodge_meta_nonce']) ||
        !wp_verify_nonce($_POST['trip_kailash_lodge_meta_nonce'], 'trip_kailash_save_lodge_meta')
    ) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save string field
    if (isset($_POST['location'])) {
        update_post_meta($post_id, 'location', sanitize_text_field($_POST['location']));
    }

    // Save array field (amenities)
    if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
        $amenities = array_filter(array_map('sanitize_text_field', $_POST['amenities']));
        update_post_meta($post_id, 'amenities', $amenities);
    } else {
        update_post_meta($post_id, 'amenities', array());
    }
}
add_action('save_post_lodge', 'trip_kailash_save_lodge_meta');
