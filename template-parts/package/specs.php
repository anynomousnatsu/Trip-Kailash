<?php
/**
 * The spec strip
 *
 * Every value here comes from a field. None of it is typed into a layout,
 * which is the fix for the drift on the live site: a package saying 12 days
 * in its title and 14 in its spec block, or a helicopter ride described as a
 * moderate trek.
 *
 * Rows with no value are not rendered. A spec strip that prints "Altitude:"
 * with nothing after it reads as broken, and a first-time visitor cannot tell
 * the difference between an empty field and a broken page.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id = get_the_ID();

$tk_specs = array();

if (tk_package_has('duration_days', $tk_id)) {
    $tk_days = (int) tk_package('duration_days', $tk_id);
    $tk_nights = (int) tk_package('duration_nights', $tk_id);

    $tk_specs[] = array(
        'label' => __('Duration', 'trip-kailash'),
        'value' => $tk_nights
            ? sprintf(
                /* translators: 1: number of days, 2: number of nights. */
                __('%1$d days, %2$d nights', 'trip-kailash'),
                $tk_days,
                $tk_nights
            )
            : tk_plural($tk_days, __('day', 'trip-kailash'), __('days', 'trip-kailash')),
    );
}

if (tk_package_has('max_altitude_m', $tk_id)) {
    $tk_specs[] = array(
        'label' => __('Maximum altitude', 'trip-kailash'),
        'value' => number_format_i18n((float) tk_package('max_altitude_m', $tk_id)) . ' m',
    );
}

$tk_grading = tk_package_grading($tk_id);

if ($tk_grading) {
    $tk_specs[] = array(
        'label' => __('Grading', 'trip-kailash'),
        'value' => $tk_grading['label'],
        'token' => $tk_grading['token'],
    );
}

if (tk_package_has('group_size_min', $tk_id) || tk_package_has('group_size_max', $tk_id)) {
    $tk_min = (int) tk_package('group_size_min', $tk_id);
    $tk_max = (int) tk_package('group_size_max', $tk_id);

    $tk_specs[] = array(
        'label' => __('Group size', 'trip-kailash'),
        'value' => ($tk_min && $tk_max) ? $tk_min . ' to ' . $tk_max : (string) max($tk_min, $tk_max),
    );
}

foreach (array(
    'accommodation'  => __('Accommodation', 'trip-kailash'),
    'transportation' => __('Transport', 'trip-kailash'),
    'meals_included' => __('Meals', 'trip-kailash'),
    'start_point'    => __('Starts at', 'trip-kailash'),
    'end_point'      => __('Ends at', 'trip-kailash'),
) as $tk_key => $tk_label) {
    if (tk_package_has($tk_key, $tk_id)) {
        $tk_specs[] = array('label' => $tk_label, 'value' => tk_package($tk_key, $tk_id));
    }
}

if (empty($tk_specs)) {
    return;
}
?>

<section class="tk-specs" aria-label="<?php esc_attr_e('Trip facts', 'trip-kailash'); ?>">
	<div class="tk-wrap">
		<dl class="tk-specs__grid">
			<?php foreach ($tk_specs as $tk_spec) : ?>
				<div class="tk-specs__item">
					<dt class="tk-specs__label"><?php echo esc_html($tk_spec['label']); ?></dt>
					<dd class="tk-specs__value tk-num"
						<?php if (!empty($tk_spec['token'])) : ?>
						style="--tk-grade-token: <?php echo esc_attr($tk_spec['token']); ?>"
						<?php endif; ?>>
						<?php if (!empty($tk_spec['token'])) : ?>
							<span class="tk-grade-dot" aria-hidden="true"></span>
						<?php endif; ?>
						<?php echo esc_html($tk_spec['value']); ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
