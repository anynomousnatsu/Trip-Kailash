<?php
/**
 * Package field admin UI
 *
 * Every panel below is generated from tk_package_field_schema(). Nothing here
 * knows the name of a single field, which is the point: adding a field to the
 * schema makes it appear here, in the REST API and in the templates at once,
 * with no chance of the four drifting apart.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The four panels, in the order someone filling in a package works.
 *
 * @return array
 */
function tk_package_admin_groups()
{
    return array(
        'facts'      => __('Trip facts', 'trip-kailash'),
        'pricing'    => __('Pricing', 'trip-kailash'),
        'departures' => __('Departures', 'trip-kailash'),
        'content'    => __('Itinerary and content', 'trip-kailash'),
    );
}

/**
 * Register one meta box per group.
 */
function tk_add_package_meta_boxes()
{
    foreach (tk_package_admin_groups() as $group => $title) {
        add_meta_box(
            'tk_package_' . $group,
            $title,
            'tk_render_package_meta_box',
            'pilgrimage_package',
            'normal',
            'high',
            array('group' => $group)
        );
    }
}
add_action('add_meta_boxes', 'tk_add_package_meta_boxes');

/**
 * Render one panel.
 *
 * @param WP_Post $post
 * @param array   $box
 */
function tk_render_package_meta_box($post, $box)
{
    $group = $box['args']['group'];

    // One nonce for the whole screen, printed by the first panel only.
    static $nonce_printed = false;
    if (!$nonce_printed) {
        wp_nonce_field('tk_save_package_fields', 'tk_package_fields_nonce');
        $nonce_printed = true;
    }

    echo '<div class="tk-fields">';

    foreach (tk_package_fields_in_group($group) as $key => $field) {
        tk_render_package_field($key, $field, $post->ID);
    }

    echo '</div>';
}

/**
 * Render one field.
 *
 * @param string $key
 * @param array  $field
 * @param int    $post_id
 */
function tk_render_package_field($key, $field, $post_id)
{
    $value = tk_package($key, $post_id);
    $id    = 'tk-field-' . $key;

    echo '<p class="tk-field tk-field--' . esc_attr($field['type']) . '">';
    printf('<label for="%s"><strong>%s</strong></label>', esc_attr($id), esc_html($field['label']));

    switch ($field['type']) {
        case 'textarea':
            printf(
                '<textarea id="%s" name="tk_fields[%s]" rows="5" class="widefat">%s</textarea>',
                esc_attr($id),
                esc_attr($key),
                esc_textarea((string) $value)
            );
            break;

        case 'number':
            printf(
                '<input type="number" step="any" id="%s" name="tk_fields[%s]" value="%s" class="small-text">',
                esc_attr($id),
                esc_attr($key),
                esc_attr((string) $value)
            );
            break;

        case 'select':
            printf('<select id="%s" name="tk_fields[%s]">', esc_attr($id), esc_attr($key));
            foreach ($field['options'] as $option_value => $option_label) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($option_value),
                    selected($value, $option_value, false),
                    esc_html($option_label)
                );
            }
            echo '</select>';
            break;

        case 'months':
            tk_render_months_field($key, (array) $value);
            break;

        case 'repeater':
            tk_render_repeater_field($key, $field, (array) $value);
            break;

        case 'text':
        default:
            printf(
                '<input type="text" id="%s" name="tk_fields[%s]" value="%s" class="widefat">',
                esc_attr($id),
                esc_attr($key),
                esc_attr((string) $value)
            );
            break;
    }

    if (!empty($field['help'])) {
        printf('<span class="description">%s</span>', esc_html($field['help']));
    }

    echo '</p>';
}

/**
 * The twelve-month bar, as checkboxes.
 *
 * @param string $key
 * @param array  $selected Month numbers, 1 to 12.
 */
function tk_render_months_field($key, $selected)
{
    $selected = array_map('intval', $selected);

    echo '<span class="tk-months">';

    for ($month = 1; $month <= 12; $month++) {
        printf(
            '<label class="tk-month"><input type="checkbox" name="tk_fields[%s][]" value="%d"%s> %s</label>',
            esc_attr($key),
            $month,
            checked(in_array($month, $selected, true), true, false),
            esc_html(date_i18n('M', mktime(0, 0, 0, $month, 1)))
        );
    }

    echo '</span>';
}

/**
 * A repeater: existing rows, a hidden template row, and an add button.
 *
 * The template row carries __i__ where the index belongs and is disabled, so
 * the browser never submits it. Forgetting to disable it is how a phantom
 * blank row gets saved on every single update.
 *
 * @param string $key
 * @param array  $field
 * @param array  $rows
 */
function tk_render_repeater_field($key, $field, $rows)
{
    printf('<span class="tk-repeater" data-field="%s">', esc_attr($key));
    echo '<span class="tk-repeater__rows">';

    foreach (array_values($rows) as $index => $row) {
        tk_render_repeater_row($key, $field, $row, (string) $index, false);
    }

    echo '</span>';

    echo '<script type="text/html" class="tk-repeater__template">';
    tk_render_repeater_row($key, $field, array(), '__i__', true);
    echo '</script>';

    printf(
        '<button type="button" class="button tk-repeater__add">%s</button>',
        esc_html(sprintf(__('Add %s', 'trip-kailash'), strtolower($field['label'])))
    );

    echo '</span>';
}

/**
 * One repeater row.
 *
 * @param string $key
 * @param array  $field
 * @param array  $row
 * @param string $index
 * @param bool   $is_template
 */
function tk_render_repeater_row($key, $field, $row, $index, $is_template)
{
    echo '<span class="tk-repeater__row">';

    foreach ($field['fields'] as $sub_key => $sub) {
        $value = isset($row[$sub_key]) ? $row[$sub_key] : '';
        $name  = sprintf('tk_fields[%s][%s][%s]', $key, $index, $sub_key);

        echo '<label class="tk-repeater__cell">';
        printf('<span>%s</span>', esc_html($sub['label']));

        if ('textarea' === $sub['type']) {
            printf(
                '<textarea name="%s" rows="2"%s>%s</textarea>',
                esc_attr($name),
                $is_template ? ' disabled' : '',
                esc_textarea((string) $value)
            );
        } else {
            printf(
                '<input type="%s"%s name="%s" value="%s"%s>',
                'number' === $sub['type'] ? 'number' : 'text',
                'number' === $sub['type'] ? ' step="any"' : '',
                esc_attr($name),
                esc_attr((string) $value),
                $is_template ? ' disabled' : ''
            );
        }

        echo '</label>';
    }

    printf(
        '<button type="button" class="button-link tk-repeater__remove" aria-label="%s">&times;</button>',
        esc_attr__('Remove this row', 'trip-kailash')
    );

    echo '</span>';
}

/**
 * Sanitise one scalar value against its schema type.
 *
 * @param mixed $value
 * @param array $field
 * @return mixed
 */
function tk_sanitize_package_value($value, $field)
{
    switch ($field['type']) {
        case 'number':
            return '' === trim((string) $value) ? '' : (float) $value;

        case 'textarea':
            return sanitize_textarea_field((string) $value);

        case 'select':
            $value = sanitize_text_field((string) $value);
            return isset($field['options'][$value]) ? $value : (isset($field['default']) ? $field['default'] : '');

        default:
            return sanitize_text_field((string) $value);
    }
}

/**
 * Save every field on the screen.
 *
 * @param int $post_id
 */
function tk_save_package_fields($post_id)
{
    if (
        !isset($_POST['tk_package_fields_nonce'])
        || !wp_verify_nonce(sanitize_key($_POST['tk_package_fields_nonce']), 'tk_save_package_fields')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised per field below.
    $submitted = isset($_POST['tk_fields']) && is_array($_POST['tk_fields']) ? wp_unslash($_POST['tk_fields']) : array();

    foreach (tk_package_field_schema() as $key => $field) {

        /* Months: a list of integers, and an unchecked bar posts nothing at
           all, so a missing key has to clear the field rather than skip it. */
        if ('months' === $field['type']) {
            $months = isset($submitted[$key]) ? (array) $submitted[$key] : array();
            $months = array_values(array_unique(array_filter(array_map('intval', $months), function ($m) {
                return $m >= 1 && $m <= 12;
            })));
            sort($months);
            update_post_meta($post_id, $key, $months);
            continue;
        }

        /* Repeaters: reindex, and drop rows the editor left completely blank
           rather than saving a run of empty itinerary days. */
        if ('repeater' === $field['type']) {
            $rows = isset($submitted[$key]) ? (array) $submitted[$key] : array();
            $clean = array();

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $clean_row = array();
                $has_value = false;

                foreach ($field['fields'] as $sub_key => $sub) {
                    $raw = isset($row[$sub_key]) ? $row[$sub_key] : '';
                    $clean_row[$sub_key] = tk_sanitize_package_value($raw, $sub);

                    if ('' !== trim((string) $clean_row[$sub_key])) {
                        $has_value = true;
                    }
                }

                if ($has_value) {
                    $clean[] = $clean_row;
                }
            }

            update_post_meta($post_id, $key, $clean);
            continue;
        }

        if (!isset($submitted[$key])) {
            continue;
        }

        update_post_meta($post_id, $key, tk_sanitize_package_value($submitted[$key], $field));
    }
}
add_action('save_post_pilgrimage_package', 'tk_save_package_fields');

/**
 * Styles and the repeater script, on the package edit screen only.
 *
 * @param string $hook
 */
function tk_enqueue_package_admin_assets($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || 'pilgrimage_package' !== $screen->post_type) {
        return;
    }

    wp_enqueue_style(
        'trip-kailash-package-admin',
        TRIP_KAILASH_URI . '/assets/css/package-admin.css',
        array(),
        TRIP_KAILASH_VERSION
    );

    wp_enqueue_script(
        'trip-kailash-package-admin',
        TRIP_KAILASH_URI . '/assets/js/package-admin.js',
        array(),
        TRIP_KAILASH_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'tk_enqueue_package_admin_assets');
