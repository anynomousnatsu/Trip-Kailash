<?php
/**
 * Template tags
 *
 * Small output helpers shared across the redesigned templates. Their job is to
 * make the design system hard to break by accident: a section heading is not
 * three separate elements someone can forget to pair, it is one call.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * A section heading: tracked eyebrow, display heading, brass rule.
 *
 * The rule is the one ornament in the system, a hairline with a small rotated
 * square at its left end echoing metal inlay on a temple door frame. It draws
 * itself on scroll, and it appears under every section heading, which is what
 * makes the page read as one continuous walk rather than as stacked sections.
 *
 * @param string $eyebrow Small tracked label above the heading.
 * @param string $heading The heading itself.
 * @param array  $args    level: heading tag, defaults to h2.
 *                        align: 'center' to centre the block.
 *                        id: optional id on the heading, for aria-labelledby.
 * @return void
 */
function tk_section_head($eyebrow, $heading, $args = array())
{
    $args = wp_parse_args($args, array(
        'level' => 'h2',
        'align' => '',
        'id'    => '',
    ));

    $level = in_array($args['level'], array('h1', 'h2', 'h3'), true) ? $args['level'] : 'h2';
    $class = 'tk-sechead' . ('center' === $args['align'] ? ' tk-sechead--center' : '');

    echo '<div class="' . esc_attr($class) . '">';

    if ($eyebrow) {
        echo '<p class="tk-eyebrow tk-rv">' . esc_html($eyebrow) . '</p>';
    }

    printf(
        '<%1$s class="tk-sechead__title tk-rv"%2$s>%3$s</%1$s>',
        $level,
        $args['id'] ? ' id="' . esc_attr($args['id']) . '"' : '',
        esc_html($heading)
    );

    echo '<div class="tk-rule" aria-hidden="true"></div>';
    echo '</div>';
}

/**
 * Count the packages in one tradition.
 *
 * The tradition doors carry a count each, and typing those in is how they go
 * stale the first time a package is added. Cached for an hour because it runs
 * three times on every homepage load and the answer changes on the order of
 * once a season.
 *
 * @param string $slug Tradition term slug.
 * @return int
 */
function tk_tradition_count($slug)
{
    $counts = get_transient('tk_tradition_counts');

    if (!is_array($counts)) {
        $counts = array();
        $terms  = get_terms(array(
            'taxonomy'   => 'tradition',
            'hide_empty' => false,
        ));

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $counts[$term->slug] = (int) $term->count;
            }
        }

        set_transient('tk_tradition_counts', $counts, HOUR_IN_SECONDS);
    }

    return isset($counts[$slug]) ? $counts[$slug] : 0;
}

/**
 * Drop the cached counts whenever a package's traditions change.
 *
 * Without this the doors keep showing yesterday's numbers for an hour after
 * someone publishes, which is exactly when they look at the site to check.
 *
 * @param int $post_id
 * @return void
 */
function tk_flush_tradition_counts($post_id = 0)
{
    if ($post_id && 'pilgrimage_package' !== get_post_type($post_id)) {
        return;
    }

    delete_transient('tk_tradition_counts');
}
add_action('save_post_pilgrimage_package', 'tk_flush_tradition_counts');
add_action('deleted_post', 'tk_flush_tradition_counts');
add_action('set_object_terms', 'tk_flush_tradition_counts');

/**
 * Plural that reads like a person wrote it.
 *
 * @param int    $count
 * @param string $singular
 * @param string $plural
 * @return string
 */
function tk_plural($count, $singular, $plural)
{
    return sprintf('%d %s', $count, 1 === (int) $count ? $singular : $plural);
}
