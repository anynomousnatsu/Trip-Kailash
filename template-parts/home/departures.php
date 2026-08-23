<?php
/**
 * Which year to walk it
 *
 * The commercial centre of the homepage, and the section the customer
 * research rebuilt.
 *
 * 2026 was the Tibetan Fire Horse Year, the rarest window in the sixty year
 * cycle, and every competitor sold it hard. Almost nobody will say the honest
 * thing afterwards, which is that the year the crowds have gone is the better
 * year to walk the circle. That is a position competitors cannot copy without
 * contradicting their own 2026 marketing.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * The time-boxed band.
 *
 * Two conditions, both required. The operator has to confirm a departure can
 * genuinely still run, AND the season has to still be open. The date check is
 * here so the band takes itself down rather than depending on someone
 * remembering: an urgency banner that outlives its own deadline is worse than
 * never having run one, because it tells every returning visitor that nothing
 * on the site is maintained.
 */
$tk_season_closes = get_theme_mod('tk_horse_year_closes', '2026-09-30');
$tk_can_still_run = (bool) get_theme_mod('tk_horse_year_departure_open', false);
$tk_show_urgency  = $tk_can_still_run
    && current_time('Y-m-d') <= $tk_season_closes;
?>

<section class="tk-section tk-departures" id="departures" aria-labelledby="tk-departures-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Departures', 'trip-kailash'),
			__('Which year to walk it', 'trip-kailash'),
			array('id' => 'tk-departures-title')
		);
		?>

		<?php if ($tk_show_urgency) : ?>
			<p class="tk-urgency tk-rv">
				<?php
				printf(
					/* translators: %s: the date the season closes. */
					esc_html__('The Fire Horse Year closes on %s. If you can travel before then, tell us today and we will tell you honestly whether the permit can still be issued in time.', 'trip-kailash'),
					esc_html(date_i18n('j F', strtotime($tk_season_closes)))
				);
				?>
			</p>
		<?php endif; ?>

		<div class="tk-departures__grid">

			<div class="tk-departures__col tk-rv">
				<h3 class="tk-departures__title"><?php esc_html_e('The Horse Year, and why 2027 is the better year', 'trip-kailash'); ?></h3>
				<p><?php esc_html_e('2026 was the Fire Horse Year, the rarest window in the sixty year cycle, and tens of thousands walked the kora. The trail showed it. Permits were slow, lodges were full, and guides were looking after thirty people at a time.', 'trip-kailash'); ?></p>
				<p><?php esc_html_e('2027 is quiet. The mountain is the same mountain. If what you came for is the circle rather than the crowd, this is the better year to walk it.', 'trip-kailash'); ?></p>
				<p class="tk-actions">
					<a class="tk-btn-ghost" href="<?php echo esc_url(home_url('/book-yatra')); ?>"><?php esc_html_e('Join the 2027 departure list', 'trip-kailash'); ?></a>
				</p>
			</div>

			<div class="tk-departures__col tk-rv">
				<h3 class="tk-departures__title"><?php esc_html_e('Nepal yatras run on your dates', 'trip-kailash'); ?></h3>
				<p><?php esc_html_e('Muktinath, Gosaikunda, Haleshi and the Shiva parikrama are arranged around you. Your family, your muhurta, your festival. We need three weeks notice for permits and lodges.', 'trip-kailash'); ?></p>
				<p><?php esc_html_e('Janai Purnima at Gosaikunda and Shivaratri fill first, because everyone wants the same night.', 'trip-kailash'); ?></p>
				<p class="tk-actions">
					<a class="tk-btn-ghost" href="<?php echo esc_url(home_url('/book-yatra')); ?>"><?php esc_html_e('Request your dates', 'trip-kailash'); ?></a>
				</p>
			</div>
		</div>

		<?php get_template_part('template-parts/home/kora-ring'); ?>
	</div>
</section>
