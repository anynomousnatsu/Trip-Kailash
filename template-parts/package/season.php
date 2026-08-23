<?php
/**
 * The twelve-month bar
 *
 * When to come, at a glance. A pilgrim planning around a festival or a
 * muhurta is choosing a month before anything else, and a sentence saying
 * "March to May and September to November" makes them do the work of turning
 * prose back into a calendar.
 *
 * Colour is not the only signal: the good months are also labelled and
 * announced, so the bar works for a colour-blind reader and a screen reader
 * both.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_months = array_map('intval', (array) tk_package('best_months', get_the_ID()));

if (empty($tk_months)) {
    return;
}

$tk_names = array();

for ($tk_m = 1; $tk_m <= 12; $tk_m++) {
    $tk_names[$tk_m] = date_i18n('M', mktime(0, 0, 0, $tk_m, 1));
}

$tk_good = array();

foreach ($tk_months as $tk_m) {
    if (isset($tk_names[$tk_m])) {
        $tk_good[] = date_i18n('F', mktime(0, 0, 0, $tk_m, 1));
    }
}
?>

<section class="tk-pk-section" id="season" aria-labelledby="tk-season-title">
	<?php
	tk_section_head(
		__('When to come', 'trip-kailash'),
		__('The season for this yatra', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-season-title')
	);
	?>

	<ul class="tk-season" role="list">
		<?php foreach ($tk_names as $tk_number => $tk_name) :
			$tk_is_good = in_array($tk_number, $tk_months, true);
			?>
			<li class="tk-season__month<?php echo $tk_is_good ? ' is-good' : ''; ?>">
				<span class="tk-season__bar" aria-hidden="true"></span>
				<span class="tk-season__name"><?php echo esc_html($tk_name); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>

	<p class="tk-season__note">
		<?php
		printf(
			/* translators: %s: a list of month names. */
			esc_html__('Best months: %s.', 'trip-kailash'),
			esc_html(implode(', ', $tk_good))
		);
		?>
	</p>
</section>
