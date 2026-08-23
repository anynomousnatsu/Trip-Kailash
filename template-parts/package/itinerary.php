<?php
/**
 * Day by day
 *
 * Altitude and overnight location on every day, because those are the two
 * numbers a nervous buyer actually reads. The research is blunt about it:
 * pilgrims who skip acclimatisation are the ones turned back below the pass,
 * so a day list that hides where you sleep and how high is hiding the thing
 * that decides whether the trip works.
 *
 * Built on details and summary, so it works with no JavaScript at all and
 * keyboard support is the browser's rather than ours.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_days = tk_package('itinerary', get_the_ID());

if (empty($tk_days)) {
    return;
}
?>

<section class="tk-pk-section" id="itinerary" aria-labelledby="tk-itinerary-title">
	<?php
	tk_section_head(
		__('Day by day', 'trip-kailash'),
		__('How the journey runs', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-itinerary-title')
	);
	?>

	<ol class="tk-itinerary">
		<?php foreach ($tk_days as $tk_index => $tk_day) :
			$tk_number = !empty($tk_day['day']) ? (int) $tk_day['day'] : $tk_index + 1;
			?>
			<li class="tk-itinerary__day">
				<details<?php echo 0 === $tk_index ? ' open' : ''; ?>>
					<summary class="tk-itinerary__summary">
						<span class="tk-itinerary__num tk-num"><?php echo esc_html(sprintf(__('Day %d', 'trip-kailash'), $tk_number)); ?></span>
						<span class="tk-itinerary__title"><?php echo esc_html($tk_day['title']); ?></span>
						<?php if (!empty($tk_day['altitude'])) : ?>
							<span class="tk-itinerary__alt tk-num"><?php echo esc_html(number_format_i18n((float) $tk_day['altitude'])); ?> m</span>
						<?php endif; ?>
					</summary>

					<div class="tk-itinerary__detail">
						<?php if (!empty($tk_day['body'])) : ?>
							<p class="tk-itinerary__body"><?php echo esc_html($tk_day['body']); ?></p>
						<?php endif; ?>

						<dl class="tk-itinerary__facts">
							<?php
							$tk_facts = array(
								__('Overnight', 'trip-kailash')    => !empty($tk_day['overnight']) ? $tk_day['overnight'] : '',
								__('Meals', 'trip-kailash')        => !empty($tk_day['meals']) ? $tk_day['meals'] : '',
								__('On the move', 'trip-kailash')  => !empty($tk_day['hours']) ? $tk_day['hours'] : '',
							);

							foreach ($tk_facts as $tk_label => $tk_value) :
								if ('' === trim((string) $tk_value)) {
									continue;
								}
								?>
								<div class="tk-itinerary__fact">
									<dt><?php echo esc_html($tk_label); ?></dt>
									<dd><?php echo esc_html($tk_value); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				</details>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
