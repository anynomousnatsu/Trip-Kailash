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

/**
 * The drawn mark for one tradition.
 *
 * Four genuinely different figures, not one icon recoloured. A torana is an
 * arch you walk through, a dharma wheel is a circle you go around, a shaligram
 * is a faceted stone you hold, and a yantra is a diagram you sit in front of.
 * Recolouring a single glyph four times would say these are four flavours of
 * the same thing, which is the one claim this site must not make.
 *
 * Each draws its stroke on scroll: the outer figure first, the inner one
 * behind it, so the mark assembles rather than appearing.
 *
 * @param string $slug Tradition term slug.
 * @return void
 */
function tk_tradition_geometry($slug)
{
    $marks = array(

        // Torana: the arched temple door frame you pass through.
        'shaiva' =>
            '<path class="tk-mark__a" d="M9 86 L9 42 A29 29 0 0 1 67 42 L67 86" />' .
            '<path class="tk-mark__b" d="M20 86 L20 44 A18 18 0 0 1 56 44 L56 86" />' .
            '<path class="tk-mark__a" d="M29 27 L47 27" />' .
            '<circle class="tk-mark__dot" cx="38" cy="15" r="4.5" />',

        // Dharma wheel: the circle walked, with its eight spokes.
        'buddhist' =>
            '<circle class="tk-mark__a" cx="38" cy="45" r="29" />' .
            '<circle class="tk-mark__b" cx="38" cy="45" r="18" />' .
            '<path class="tk-mark__a" d="M9 45 L67 45 M38 16 L38 74 M17 24 L59 66 M59 24 L17 66" />' .
            '<circle class="tk-mark__dot" cx="38" cy="45" r="5.5" />',

        // Shaligram: the faceted ammonite stone from the Kali Gandaki.
        'vaishnava' =>
            '<path class="tk-mark__a" d="M38 13 L61 31 L52 68 L24 68 L15 31 Z" />' .
            '<path class="tk-mark__b" d="M38 26 L50 36 L45 60 L31 60 L26 36 Z" />' .
            '<path class="tk-mark__a" d="M21 78 L55 78" />' .
            '<circle class="tk-mark__dot" cx="38" cy="44" r="4.5" />',

        // Yantra: the downward triangle of Shakti, seated in its circle.
        'shakta' =>
            '<circle class="tk-mark__a" cx="38" cy="44" r="29" />' .
            '<path class="tk-mark__a" d="M13 32 L63 32 L38 73 Z" />' .
            '<path class="tk-mark__b" d="M23 40 L53 40 L38 64 Z" />' .
            '<circle class="tk-mark__dot" cx="38" cy="47" r="4.5" />',
    );

    if (!isset($marks[$slug])) {
        return;
    }

    printf(
        '<svg class="tk-mark" viewBox="0 0 76 88" width="76" height="88" fill="none" aria-hidden="true" focusable="false">%s</svg>',
        $marks[$slug]
    );
}
