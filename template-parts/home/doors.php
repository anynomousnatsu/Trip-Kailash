<?php
/**
 * Choose your path: the tradition doors
 *
 * Sits above the packages on purpose. It reframes what the visitor thinks
 * they are shopping for before a price appears, because once someone is
 * comparing $350 to $1,000 the pilgrimage framing is gone and the site is
 * competing on cost with all of Thamel.
 *
 * Driven by the tradition taxonomy rather than hand-written, so a door cannot
 * describe a catalogue that has moved on. Traditions with no packages are not
 * shown at all: an empty door reads as a hole, and offering a path that leads
 * to nothing is worse than offering one fewer.
 *
 * Each door gets its OWN geometry, drawn by hand. One icon recoloured three
 * times looks like a template, and these are three genuinely different
 * traditions.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * Copy is verbatim from docs/design-package.md section 6.2. The order is the
 * order they are offered in, which is by weight in the catalogue.
 */
$tk_doors = array(
    'shaiva' => array(
        'name'  => __('Shaiva', 'trip-kailash'),
        'deva'  => 'शैव',
        'blurb' => __('The abodes of Shiva. Kailash, Gosaikunda, Kedarnath, and the temple parikrama of the valley.', 'trip-kailash'),
    ),
    'buddhist' => array(
        'name'  => __('Buddhist', 'trip-kailash'),
        'deva'  => 'बौद्ध',
        'blurb' => __('Maratika, the Chumig Gyatsa at Muktinath, and the kora of Kailash. Where Padmasambhava walked.', 'trip-kailash'),
    ),
    'vaishnava' => array(
        'name'  => __('Vaishnava', 'trip-kailash'),
        'deva'  => 'वैष्णव',
        'blurb' => __('Muktinath. Mukti Kshetra, the place of liberation, and the shaligram beds of the Kali Gandaki.', 'trip-kailash'),
    ),
    'shakta' => array(
        'name'  => __('Shakta', 'trip-kailash'),
        'deva'  => 'शाक्त',
        'blurb' => __('Pathibhara and Manakamana. The seats of the Mother, where a wish is spoken aloud and the walk up is the asking.', 'trip-kailash'),
    ),
);

$tk_visible = array();

foreach ($tk_doors as $tk_slug => $tk_door) {
    $tk_count = tk_tradition_count($tk_slug);

    if ($tk_count < 1) {
        continue;
    }

    $tk_term = get_term_by('slug', $tk_slug, 'tradition');

    if (!$tk_term || is_wp_error($tk_term)) {
        continue;
    }

    $tk_door['count'] = $tk_count;
    $tk_door['url']   = get_term_link($tk_term);
    $tk_visible[$tk_slug] = $tk_door;
}

if (empty($tk_visible)) {
    return;
}
?>

<section class="tk-section tk-doors-section" id="paths" aria-labelledby="tk-paths-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Choose your path', 'trip-kailash'),
			__('Which tradition are you travelling in?', 'trip-kailash'),
			array('id' => 'tk-paths-title')
		);
		?>

		<div class="tk-doors tk-stagger">
			<?php foreach ($tk_visible as $tk_slug => $tk_door) : ?>
				<a class="tk-door tk-rv" href="<?php echo esc_url($tk_door['url']); ?>">
					<span class="tk-door__mark" aria-hidden="true">
						<?php tk_tradition_geometry($tk_slug); ?>
					</span>
					<span class="tk-door__name"><?php echo esc_html($tk_door['name']); ?></span>
					<span class="tk-door__deva" lang="sa"><?php echo esc_html($tk_door['deva']); ?></span>
					<span class="tk-door__blurb"><?php echo esc_html($tk_door['blurb']); ?></span>
					<span class="tk-door__count">
						<?php echo esc_html(tk_plural($tk_door['count'], __('yatra', 'trip-kailash'), __('yatras', 'trip-kailash'))); ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
