<?php
/**
 * Pilgrimage package fields
 *
 * The change everything else in the redesign depends on. Every spec on a page
 * used to be typed into an Elementor widget as layout text, which meant
 * nothing could be filtered, sorted, compared or turned into schema, and each
 * new package was a page rebuild. It also let the data drift: one package
 * said 12 days in its title and 14 in its spec block, and a helicopter ride
 * was described as a moderate trek.
 *
 * ONE schema below drives everything: field registration, the admin UI, the
 * REST shape, and the templates. Adding a field means adding a row here.
 * Nothing is defined in two places, so nothing can disagree with itself.
 *
 * Native post meta rather than ACF, so the theme carries no plugin dependency.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The field schema.
 *
 * type      one of: text, textarea, number, select, checkbox, months, repeater
 * label     admin label
 * help      one line under the field, for the person entering data
 * default   used when the field has never been set
 * options   for select, as value => label
 * fields    for repeater, a nested schema of the same shape
 * group     which admin panel it appears in
 *
 * @return array
 */
function tk_package_field_schema()
{
    static $schema = null;

    if (null !== $schema) {
        return $schema;
    }

    $schema = array(

        /* ---- Pricing --------------------------------------------------- */

        'price_from' => array(
            'group' => 'pricing',
            'type'  => 'number',
            'label' => __('Price from (USD)', 'trip-kailash'),
            'help'  => __('Base price per person. Always stored in USD; the site converts for display.', 'trip-kailash'),
        ),
        'price_note' => array(
            'group'   => 'pricing',
            'type'    => 'text',
            'label'   => __('Price note', 'trip-kailash'),
            'help'    => __('Shown next to the price, e.g. "per person, twin sharing".', 'trip-kailash'),
            'default' => 'per person',
        ),
        'deposit_percent' => array(
            'group'   => 'pricing',
            'type'    => 'number',
            'label'   => __('Deposit percent', 'trip-kailash'),
            'help'    => __('The share paid to hold a place. The balance is due on arrival in Kathmandu.', 'trip-kailash'),
            'default' => 30,
        ),
        'group_pricing' => array(
            'group'  => 'pricing',
            'type'   => 'repeater',
            'label'  => __('Group pricing', 'trip-kailash'),
            'help'   => __('Per-person price at each group size. Leave empty if the price does not change.', 'trip-kailash'),
            'fields' => array(
                'min_pax' => array('type' => 'number', 'label' => __('From this many pilgrims', 'trip-kailash')),
                'price'   => array('type' => 'number', 'label' => __('Price each (USD)', 'trip-kailash')),
            ),
        ),

        /* ---- Trip facts ------------------------------------------------- */

        'duration_days' => array(
            'group' => 'facts',
            'type'  => 'number',
            'label' => __('Duration, days', 'trip-kailash'),
            'help'  => __('This drives the title, the card, the spec strip and the schema. Set it once here.', 'trip-kailash'),
        ),
        'duration_nights' => array(
            'group' => 'facts',
            'type'  => 'number',
            'label' => __('Duration, nights', 'trip-kailash'),
        ),
        'max_altitude_m' => array(
            'group' => 'facts',
            'type'  => 'number',
            'label' => __('Maximum altitude (m)', 'trip-kailash'),
        ),
        'grading' => array(
            'group'   => 'facts',
            'type'    => 'select',
            'label'   => __('Grading', 'trip-kailash'),
            'help'    => __('Be honest. A 52 km kora over a 5,630 m pass is not Easy.', 'trip-kailash'),
            'options' => array(
                'easy'        => __('Easy', 'trip-kailash'),
                'moderate'    => __('Moderate', 'trip-kailash'),
                'challenging' => __('Challenging', 'trip-kailash'),
            ),
            'default' => 'moderate',
        ),
        'group_size_min' => array(
            'group' => 'facts',
            'type'  => 'number',
            'label' => __('Group size, minimum', 'trip-kailash'),
        ),
        'group_size_max' => array(
            'group' => 'facts',
            'type'  => 'number',
            'label' => __('Group size, maximum', 'trip-kailash'),
        ),
        'accommodation' => array(
            'group' => 'facts',
            'type'  => 'text',
            'label' => __('Accommodation', 'trip-kailash'),
            'help'  => __('e.g. "Hotel and tea-house".', 'trip-kailash'),
        ),
        'transportation' => array(
            'group' => 'facts',
            'type'  => 'text',
            'label' => __('Transportation', 'trip-kailash'),
        ),
        'meals_included' => array(
            'group' => 'facts',
            'type'  => 'text',
            'label' => __('Meals included', 'trip-kailash'),
        ),
        'start_point' => array(
            'group' => 'facts',
            'type'  => 'text',
            'label' => __('Starts at', 'trip-kailash'),
        ),
        'end_point' => array(
            'group' => 'facts',
            'type'  => 'text',
            'label' => __('Ends at', 'trip-kailash'),
        ),

        /* ---- Departures -------------------------------------------------
           One field switches the entire booking module. Kailash is
           fixed-departure because Tibet permits are issued in batches;
           everything in Nepal is arranged around the traveller's own dates,
           their family, their muhurta, their festival.
           ------------------------------------------------------------------ */

        'departure_type' => array(
            'group'   => 'departures',
            'type'    => 'select',
            'label'   => __('Departure type', 'trip-kailash'),
            'help'    => __('Fixed shows a date list. On request shows a date picker and the lead-time note.', 'trip-kailash'),
            'options' => array(
                'on_request' => __('On request, the traveller picks dates', 'trip-kailash'),
                'fixed'      => __('Fixed departures on set dates', 'trip-kailash'),
            ),
            'default' => 'on_request',
        ),
        'lead_time_note' => array(
            'group' => 'departures',
            'type'  => 'text',
            'label' => __('Lead time note', 'trip-kailash'),
            'help'  => __('On-request trips only, e.g. "three weeks for permits and lodges".', 'trip-kailash'),
        ),
        'best_months' => array(
            'group' => 'departures',
            'type'  => 'months',
            'label' => __('Best months', 'trip-kailash'),
            'help'  => __('Renders as a twelve-month bar on the package page.', 'trip-kailash'),
        ),
        'fixed_departures' => array(
            'group'  => 'departures',
            'type'   => 'repeater',
            'label'  => __('Fixed departures', 'trip-kailash'),
            'help'   => __('Real dates only. An invented departure date is the fastest way to lose a booking that was already yours.', 'trip-kailash'),
            'fields' => array(
                'date'        => array('type' => 'text', 'label' => __('Date', 'trip-kailash')),
                'seats_total' => array('type' => 'number', 'label' => __('Seats', 'trip-kailash')),
                'seats_left'  => array('type' => 'number', 'label' => __('Seats left', 'trip-kailash')),
                'status'      => array('type' => 'text', 'label' => __('Status', 'trip-kailash')),
            ),
        ),

        /* ---- Content ---------------------------------------------------- */

        'short_pitch' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('Short pitch', 'trip-kailash'),
            'help'  => __('One line, used on every card. Make it about the place rather than the logistics; every competitor leads with logistics.', 'trip-kailash'),
        ),
        'hero_focal' => array(
            'group'   => 'content',
            'type'    => 'text',
            'label'   => __('Hero image focal point', 'trip-kailash'),
            'help'    => __('Two percentages, e.g. "50% 35%". Moves the crop so the shrine stays in frame when the hero is cropped on wide or short screens.', 'trip-kailash'),
            'default' => '50% 50%',
        ),
        'permits_required' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('Permits required', 'trip-kailash'),
            'help'  => __('One per line.', 'trip-kailash'),
        ),
        'fitness_notes' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('Fitness notes', 'trip-kailash'),
            'help'  => __('Who should not come, said plainly. Pilgrims prepare the spiritual side for months and treat the altitude as an afterthought.', 'trip-kailash'),
        ),
        'packing_list' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('Packing list', 'trip-kailash'),
            'help'  => __('One per line.', 'trip-kailash'),
        ),
        'inclusions' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('What is included', 'trip-kailash'),
            'help'  => __('One per line. Kept explicitly separate from exclusions, because a merged list is where trust goes.', 'trip-kailash'),
        ),
        'exclusions' => array(
            'group' => 'content',
            'type'  => 'textarea',
            'label' => __('What is not included', 'trip-kailash'),
            'help'  => __('One per line.', 'trip-kailash'),
        ),

        'itinerary' => array(
            'group'  => 'content',
            'type'   => 'repeater',
            'label'  => __('Day by day', 'trip-kailash'),
            'help'   => __('Altitude and overnight matter more than prose here. They are what a nervous buyer reads.', 'trip-kailash'),
            'fields' => array(
                'day'       => array('type' => 'number', 'label' => __('Day', 'trip-kailash')),
                'title'     => array('type' => 'text', 'label' => __('Title', 'trip-kailash')),
                'body'      => array('type' => 'textarea', 'label' => __('What happens', 'trip-kailash')),
                'altitude'  => array('type' => 'number', 'label' => __('Altitude (m)', 'trip-kailash')),
                'overnight' => array('type' => 'text', 'label' => __('Overnight at', 'trip-kailash')),
                'meals'     => array('type' => 'text', 'label' => __('Meals', 'trip-kailash')),
                'hours'     => array('type' => 'text', 'label' => __('Drive or walk hours', 'trip-kailash')),
            ),
        ),
        'faq' => array(
            'group'  => 'content',
            'type'   => 'repeater',
            'label'  => __('Questions pilgrims ask', 'trip-kailash'),
            'help'   => __('Answer the objection that is actually raised, not the one that is comfortable to answer.', 'trip-kailash'),
            'fields' => array(
                'question' => array('type' => 'text', 'label' => __('Question', 'trip-kailash')),
                'answer'   => array('type' => 'textarea', 'label' => __('Answer', 'trip-kailash')),
            ),
        ),
    );

    return $schema;
}

/**
 * Fields belonging to one admin panel, in schema order.
 *
 * @param string $group
 * @return array
 */
function tk_package_fields_in_group($group)
{
    return array_filter(
        tk_package_field_schema(),
        function ($field) use ($group) {
            return isset($field['group']) && $group === $field['group'];
        }
    );
}

/**
 * The REST schema for one field.
 *
 * Repeaters and month lists are arrays, so they need their item shape spelled
 * out or WordPress refuses to expose them over REST at all.
 *
 * @param array $field
 * @return array
 */
function tk_package_field_rest_schema($field)
{
    if ('months' === $field['type']) {
        return array(
            'type'  => 'array',
            'items' => array('type' => 'integer'),
        );
    }

    if ('repeater' === $field['type']) {
        $properties = array();

        foreach ($field['fields'] as $key => $sub) {
            $properties[$key] = array(
                'type' => 'number' === $sub['type'] ? 'number' : 'string',
            );
        }

        return array(
            'type'  => 'array',
            'items' => array(
                'type'                 => 'object',
                'properties'           => $properties,
                'additionalProperties' => false,
            ),
        );
    }

    return array('type' => 'number' === $field['type'] ? 'number' : 'string');
}

/**
 * Register every field as post meta.
 *
 * Registered rather than free-floating, so the REST API and the block editor
 * both see them and so anything reading a package gets a predictable shape.
 */
function tk_register_package_fields()
{
    foreach (tk_package_field_schema() as $key => $field) {
        $is_array = in_array($field['type'], array('repeater', 'months'), true);

        register_post_meta('pilgrimage_package', $key, array(
            'type'         => $is_array ? 'array' : ('number' === $field['type'] ? 'number' : 'string'),
            'single'       => true,
            'show_in_rest' => array('schema' => tk_package_field_rest_schema($field)),
            'default'      => isset($field['default']) ? $field['default'] : ($is_array ? array() : ''),
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ));
    }
}
add_action('init', 'tk_register_package_fields');

/**
 * Read one package field, with its schema default.
 *
 * The single accessor every template uses. get_post_meta() returns an empty
 * string for a field that was never set, which then prints as a blank spec
 * row; this returns the schema default instead, so a package that has not
 * been filled in yet degrades to something sensible rather than to a hole.
 *
 * @param string   $key     Field key from the schema.
 * @param int|null $post_id Defaults to the current post.
 * @return mixed
 */
function tk_package($key, $post_id = null)
{
    $schema = tk_package_field_schema();

    if (!isset($schema[$key])) {
        return null;
    }

    $post_id = $post_id ? $post_id : get_the_ID();
    $value   = get_post_meta($post_id, $key, true);
    $field   = $schema[$key];
    $default = isset($field['default']) ? $field['default'] : null;

    if (in_array($field['type'], array('repeater', 'months'), true)) {
        return is_array($value) && $value ? $value : array();
    }

    if ('' === $value || null === $value) {
        return null === $default ? '' : $default;
    }

    return $value;
}

/**
 * Whether a field has a real value worth rendering.
 *
 * Guards every spec row and section. A package page that prints "Altitude: "
 * with nothing after it reads as broken, and an empty section reads as a hole
 * to a first-time visitor.
 *
 * @param string   $key
 * @param int|null $post_id
 * @return bool
 */
function tk_package_has($key, $post_id = null)
{
    $value = tk_package($key, $post_id);

    if (is_array($value)) {
        return !empty($value);
    }

    return '' !== trim((string) $value);
}

/**
 * Split a one-per-line textarea field into a clean list.
 *
 * @param string   $key
 * @param int|null $post_id
 * @return array
 */
function tk_package_lines($key, $post_id = null)
{
    $raw = (string) tk_package($key, $post_id);

    if ('' === trim($raw)) {
        return array();
    }

    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
}

/**
 * The grading label and its token, as a pair.
 *
 * Colour always travels with the word. A coloured dot alone tells a
 * colour-blind reader nothing, and grading is exactly the field where that
 * matters: it is the one a buyer uses to decide whether they can go.
 *
 * @param int|null $post_id
 * @return array{key:string,label:string,token:string}|null
 */
function tk_package_grading($post_id = null)
{
    $key    = tk_package('grading', $post_id);
    $schema = tk_package_field_schema();

    if (!$key || !isset($schema['grading']['options'][$key])) {
        return null;
    }

    $tokens = array(
        'easy'        => 'var(--grade-easy)',
        'moderate'    => 'var(--grade-moderate)',
        'challenging' => 'var(--grade-hard)',
    );

    return array(
        'key'   => $key,
        'label' => $schema['grading']['options'][$key],
        'token' => isset($tokens[$key]) ? $tokens[$key] : 'var(--grade-moderate)',
    );
}
