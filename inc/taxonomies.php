<?php
/**
 * Register Custom Taxonomies
 *
 * @package TripKailash
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Wrapper function for theme activation
 */
function trip_kailash_register_taxonomies() {
    tk_register_deity_taxonomy();
}

/**
 * Register deity taxonomy
 */
function tk_register_deity_taxonomy() {
    $labels = array(
        'name'                       => _x('Deities', 'Taxonomy General Name', 'trip-kailash'),
        'singular_name'              => _x('Deity', 'Taxonomy Singular Name', 'trip-kailash'),
        'menu_name'                  => __('Deities', 'trip-kailash'),
        'all_items'                  => __('All Deities', 'trip-kailash'),
        'parent_item'                => __('Parent Deity', 'trip-kailash'),
        'parent_item_colon'          => __('Parent Deity:', 'trip-kailash'),
        'new_item_name'              => __('New Deity Name', 'trip-kailash'),
        'add_new_item'               => __('Add New Deity', 'trip-kailash'),
        'edit_item'                  => __('Edit Deity', 'trip-kailash'),
        'update_item'                => __('Update Deity', 'trip-kailash'),
        'view_item'                  => __('View Deity', 'trip-kailash'),
        'separate_items_with_commas' => __('Separate deities with commas', 'trip-kailash'),
        'add_or_remove_items'        => __('Add or remove deities', 'trip-kailash'),
        'choose_from_most_used'      => __('Choose from the most used', 'trip-kailash'),
        'popular_items'              => __('Popular Deities', 'trip-kailash'),
        'search_items'               => __('Search Deities', 'trip-kailash'),
        'not_found'                  => __('Not Found', 'trip-kailash'),
        'no_terms'                   => __('No deities', 'trip-kailash'),
        'items_list'                 => __('Deities list', 'trip-kailash'),
        'items_list_navigation'      => __('Deities list navigation', 'trip-kailash'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
        'rewrite'                    => array('slug' => 'deity'),
    );

    register_taxonomy('deity', array('pilgrimage_package'), $args);
}
add_action('init', 'tk_register_deity_taxonomy', 0);

/**
 * Create default deity terms
 */
function tk_create_default_deity_terms() {
    // Check if terms already exist to avoid duplicates
    $existing_terms = get_terms(array(
        'taxonomy'   => 'deity',
        'hide_empty' => false,
    ));

    // Only create terms if none exist
    if (empty($existing_terms) || is_wp_error($existing_terms)) {
        $default_terms = array(
            'shiva'  => 'Shiva',
            'vishnu' => 'Vishnu',
            'devi'   => 'Devi',
        );

        foreach ($default_terms as $slug => $name) {
            if (!term_exists($slug, 'deity')) {
                wp_insert_term(
                    $name,
                    'deity',
                    array(
                        'slug' => $slug,
                    )
                );
            }
        }
    }
}
// Superseded by tk_seed_catalogue_terms(), which seeds every taxonomy once
// behind an option flag instead of querying the database on every request.
// The function is left in place because a theme this old may have callers.

/**
 * Build a full WordPress label set from one singular and one plural name.
 *
 * Three taxonomies below need the same twenty labels each. Writing them out
 * three times is how they drift apart.
 *
 * @param string $singular Singular display name, e.g. "Tradition".
 * @param string $plural   Plural display name, e.g. "Traditions".
 * @return array
 */
function tk_taxonomy_labels($singular, $plural)
{
    return array(
        'name'                       => $plural,
        'singular_name'              => $singular,
        'menu_name'                  => $plural,
        'all_items'                  => sprintf(__('All %s', 'trip-kailash'), $plural),
        'new_item_name'              => sprintf(__('New %s', 'trip-kailash'), $singular),
        'add_new_item'               => sprintf(__('Add New %s', 'trip-kailash'), $singular),
        'edit_item'                  => sprintf(__('Edit %s', 'trip-kailash'), $singular),
        'update_item'                => sprintf(__('Update %s', 'trip-kailash'), $singular),
        'view_item'                  => sprintf(__('View %s', 'trip-kailash'), $singular),
        'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'trip-kailash'), strtolower($plural)),
        'add_or_remove_items'        => sprintf(__('Add or remove %s', 'trip-kailash'), strtolower($plural)),
        'choose_from_most_used'      => __('Choose from the most used', 'trip-kailash'),
        'popular_items'              => sprintf(__('Popular %s', 'trip-kailash'), $plural),
        'search_items'               => sprintf(__('Search %s', 'trip-kailash'), $plural),
        'not_found'                  => __('Not found', 'trip-kailash'),
        'no_terms'                   => sprintf(__('No %s', 'trip-kailash'), strtolower($plural)),
        'items_list'                 => sprintf(__('%s list', 'trip-kailash'), $plural),
        'items_list_navigation'      => sprintf(__('%s list navigation', 'trip-kailash'), $plural),
    );
}

/**
 * The catalogue taxonomies.
 *
 * Organising by which deity you are going to see is how a pilgrim actually
 * thinks, and no competitor does it: every Kathmandu operator sells Muktinath
 * as a trek. This is what gives the Sacred Paths page a reason to exist.
 *
 * Tradition is deliberately NOT hierarchical, because it is multi-select.
 * Several sites belong to more than one faith at once: Kailash is sacred to
 * four, and Haleshi Maratika is both a Shaiva and a Nyingma site. A radio
 * button would force a lie about the place.
 */
function tk_register_catalogue_taxonomies()
{
    $taxonomies = array(
        'tradition' => array(
            'singular' => __('Tradition', 'trip-kailash'),
            'plural'   => __('Traditions', 'trip-kailash'),
            'slug'     => 'tradition',
        ),
        'region' => array(
            'singular' => __('Region', 'trip-kailash'),
            'plural'   => __('Regions', 'trip-kailash'),
            'slug'     => 'region',
        ),
        'style' => array(
            'singular' => __('Style', 'trip-kailash'),
            'plural'   => __('Styles', 'trip-kailash'),
            'slug'     => 'journey-style',
        ),
    );

    foreach ($taxonomies as $taxonomy => $config) {
        register_taxonomy($taxonomy, array('pilgrimage_package'), array(
            'labels'            => tk_taxonomy_labels($config['singular'], $config['plural']),
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => $config['slug']),
        ));
    }
}
add_action('init', 'tk_register_catalogue_taxonomies', 0);

/**
 * Seed the catalogue terms, once.
 *
 * Guarded by an option rather than by a term lookup. tk_create_default_deity_terms
 * below ran a get_terms() query on every single front-end request forever, to
 * answer a question whose answer stops changing after the first time. This
 * costs one autoloaded option read instead.
 *
 * Bumping TK_TAXONOMY_SEED_VERSION re-runs the seed, which is how new terms
 * get added later without anyone touching the database by hand.
 */
define('TK_TAXONOMY_SEED_VERSION', 2);

function tk_seed_catalogue_terms()
{
    if ((int) get_option('tk_taxonomy_seed_version') >= TK_TAXONOMY_SEED_VERSION) {
        return;
    }

    $seed = array(
        // Shakta is here because the site already runs a Devi track and the
        // catalogue is gaining Pathibhara and Manakamana, both Devi sites. The
        // brief listed five traditions and missed it.
        //
        // The Shaiva weighting that falls out of the seven live packages is a
        // positioning asset, not something to apologise for: Nepal's Shiva
        // pilgrimage specialists is sharper than pretending to cover everything.
        'tradition' => array(
            'shaiva'    => 'Shaiva',
            'vaishnava' => 'Vaishnava',
            'shakta'    => 'Shakta',
            'buddhist'  => 'Buddhist',
            'jain'      => 'Jain',
            'bon'       => 'Bön',
        ),
        'region' => array(
            'nepal'       => 'Nepal',
            'tibet'       => 'Tibet',
            'uttarakhand' => 'Uttarakhand',
        ),
        'style' => array(
            'trek'       => 'Trek',
            'overland'   => 'Overland',
            'helicopter' => 'Helicopter',
            'mixed'      => 'Mixed',
        ),
        'deity' => array(
            'shiva'  => 'Shiva',
            'vishnu' => 'Vishnu',
            'devi'   => 'Devi',
        ),
    );

    foreach ($seed as $taxonomy => $terms) {
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, $taxonomy)) {
                wp_insert_term($name, $taxonomy, array('slug' => $slug));
            }
        }
    }

    update_option('tk_taxonomy_seed_version', TK_TAXONOMY_SEED_VERSION);
}
add_action('init', 'tk_seed_catalogue_terms', 5);
