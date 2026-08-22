<?php
/**
 * Shared Query Helpers
 *
 * Small cached accessors for data that several widgets and templates need.
 * Keeping them here means one query per request instead of one per widget.
 *
 * @package TripKailash
 * @since 1.0.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Transient key holding the id => title map of published packages.
 */
const TK_PACKAGE_OPTIONS_TRANSIENT = 'tk_package_options';

/**
 * Get published pilgrimage packages as an id => title map.
 *
 * Several widgets need exactly this to populate a <select>. They each used
 * to run get_posts() with posts_per_page => -1, which hydrates a full
 * WP_Post for every package and primes the meta and term caches for all of
 * them -- to read one column. Two of those callers ran on every front-end
 * render.
 *
 * This queries only the columns needed, caches the result in a transient,
 * and memoises it for the rest of the request.
 *
 * @return array<int, string> Package ID => package title.
 */
function tk_get_package_options()
{
    static $options = null;

    if (null !== $options) {
        return $options;
    }

    $cached = get_transient(TK_PACKAGE_OPTIONS_TRANSIENT);

    if (is_array($cached)) {
        $options = $cached;
        return $options;
    }

    $packages = get_posts(array(
        'post_type'              => 'pilgrimage_package',
        'post_status'            => 'publish',
        'posts_per_page'         => 200,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        // We only read ID and title, so skip the expensive cache priming
        // and the SQL_CALC_FOUND_ROWS pagination count.
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    $options = array();

    foreach ($packages as $package) {
        $options[$package->ID] = $package->post_title;
    }

    set_transient(TK_PACKAGE_OPTIONS_TRANSIENT, $options, HOUR_IN_SECONDS * 12);

    return $options;
}

/**
 * Invalidate the package options cache whenever a package changes.
 *
 * @param int $post_id Post ID being written.
 * @return void
 */
function tk_flush_package_options_cache($post_id)
{
    if ('pilgrimage_package' !== get_post_type($post_id)) {
        return;
    }

    delete_transient(TK_PACKAGE_OPTIONS_TRANSIENT);
}
add_action('save_post', 'tk_flush_package_options_cache');
add_action('trashed_post', 'tk_flush_package_options_cache');
add_action('untrashed_post', 'tk_flush_package_options_cache');
add_action('deleted_post', 'tk_flush_package_options_cache');
