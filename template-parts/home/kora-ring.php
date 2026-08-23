<?php
/**
 * The kora ring: the one interactive moment
 *
 * The visitor holds, and walks the 52 kilometre circle with their own hand.
 * The altitude readout climbs to the Dolma La at 5,630 m and comes back down,
 * the day markers light in sequence, and closing the circle earns the payoff.
 *
 * This is the premise performed rather than stated. The site's whole argument
 * is that you do not climb what is sacred, you walk around it, and here the
 * visitor does exactly that before being told what it means.
 *
 * Three things keep it feeling designed rather than gimmicky: progress builds
 * only while they hold, releasing early eases back down instead of snapping
 * to zero, and completing it reveals something real.
 *
 * Accessible by design. A hold is a pointer gesture, so keyboard and
 * assistive-technology users complete the circle with one press instead: the
 * control is a real button, and the payoff is the same. Reduced motion gets
 * the completed state with no interaction required at all.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * The real profile of the kora, in walking order. These are the altitudes a
 * pilgrim actually crosses, which is the point: the readout is not decoration.
 */
$tk_markers = array(
    array('label' => __('Darchen', 'trip-kailash'), 'note' => __('Start', 'trip-kailash'), 'alt' => 4675),
    array('label' => __('Dirapuk', 'trip-kailash'), 'note' => __('Day 1', 'trip-kailash'), 'alt' => 4910),
    array('label' => __('Dolma La', 'trip-kailash'), 'note' => __('Day 2', 'trip-kailash'), 'alt' => 5630),
    array('label' => __('Zutulpuk', 'trip-kailash'), 'note' => __('Day 2', 'trip-kailash'), 'alt' => 4790),
    array('label' => __('Darchen', 'trip-kailash'), 'note' => __('Day 3, closed', 'trip-kailash'), 'alt' => 4675),
);

/*
 * The payoff. The merit figure is blocked on design-package section 10 item 2:
 * sources give it as thirteen, some as twelve. Until the operator confirms
 * which figure their own tradition uses, the completed state says something
 * true and specific instead of guessing at a religious claim.
 */
$tk_merit_confirmed = (bool) get_theme_mod('tk_merit_figure_confirmed', false);

$tk_payoff = $tk_merit_confirmed
    ? __('You have closed the circle. Walked in a Horse Year it counts thirteen times. The last was 2026. The next is 2038.', 'trip-kailash')
    : __('You have closed the circle. Fifty-two kilometres, three days, and one pass at 5,630 metres.', 'trip-kailash');
?>

<div class="tk-kora" id="tk-kora"
	data-markers="<?php echo esc_attr(wp_json_encode($tk_markers)); ?>">

	<button type="button" class="tk-kora__control" aria-describedby="tk-kora-readout">
		<svg class="tk-kora__ring" viewBox="0 0 240 240" aria-hidden="true" focusable="false">
			<circle class="tk-kora__groove" cx="120" cy="120" r="92" />
			<circle class="tk-kora__path" cx="120" cy="120" r="92" />
			<g class="tk-kora__marks"></g>
		</svg>

		<span class="tk-kora__centre">
			<span class="tk-kora__alt tk-num" data-role="altitude">4,675</span>
			<span class="tk-kora__unit"><?php esc_html_e('metres', 'trip-kailash'); ?></span>
			<span class="tk-kora__place" data-role="place"><?php esc_html_e('Darchen', 'trip-kailash'); ?></span>
		</span>

		<span class="tk-sr-only"><?php esc_html_e('Walk the kora. Hold to travel the circle, or press to complete it.', 'trip-kailash'); ?></span>
	</button>

	<p class="tk-kora__readout" id="tk-kora-readout" role="status" data-role="readout"
		data-idle="<?php echo esc_attr__('Hold to walk the kora', 'trip-kailash'); ?>"
		data-pass="<?php echo esc_attr__('Dolma La. 5,630 m. The highest point of the circle.', 'trip-kailash'); ?>"
		data-done="<?php echo esc_attr($tk_payoff); ?>">
		<?php esc_html_e('Hold to walk the kora', 'trip-kailash'); ?>
	</p>
</div>
