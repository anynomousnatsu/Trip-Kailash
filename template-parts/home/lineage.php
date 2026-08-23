<?php
/**
 * Who takes you
 *
 * There are no Trip Kailash testimonials, and we do not invent them or paste
 * the parent company's on. Those people did not travel with Trip Kailash, and
 * a reader who checks will feel misled, which costs more than the empty
 * section saved.
 *
 * Lineage is the honest version of the same claim, and it happens to answer
 * the question a nervous buyer is actually asking: who rescues me at 4,600
 * metres, and will this company still exist next year.
 *
 * Every number here comes from the Customizer and is simply omitted when
 * unset. A credential row showing dashes where figures belong is worse than
 * no credential row: it advertises that nobody finished the page.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_creds = array(
    'tk_years_operating' => __('years operating', 'trip-kailash'),
    'tk_travellers'      => __('travellers hosted', 'trip-kailash'),
    'tk_guides'          => __('licensed guides', 'trip-kailash'),
);

$tk_shown = array();

foreach ($tk_creds as $tk_mod => $tk_label) {
    $tk_value = trim((string) get_theme_mod($tk_mod, ''));

    if ('' !== $tk_value) {
        $tk_shown[$tk_value] = $tk_label;
    }
}

$tk_portrait  = (int) get_theme_mod('tk_founder_portrait_id', 0);
$tk_founder   = trim((string) get_theme_mod('tk_founder_name', ''));
$tk_trekmania = trim((string) get_theme_mod('tk_trekmania_reviews_url', ''));
?>

<section class="tk-section tk-lineage" id="who" aria-labelledby="tk-lineage-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Who takes you', 'trip-kailash'),
			__('Second generation on these trails', 'trip-kailash'),
			array('id' => 'tk-lineage-title')
		);
		?>

		<div class="tk-lineage__grid">

			<div class="tk-lineage__portrait tk-rv tk-rv--left">
				<?php if ($tk_portrait) : ?>
					<?php
					echo wp_get_attachment_image($tk_portrait, 'large', false, array(
						'class'   => 'tk-lineage__img',
						'loading' => 'lazy',
						'alt'     => $tk_founder
							? sprintf(
								/* translators: %s: founder name. */
								esc_attr__('%s, founder of Trip Kailash, on the trail', 'trip-kailash'),
								$tk_founder
							)
							: esc_attr__('The founder of Trip Kailash, on the trail', 'trip-kailash'),
					));
					?>
				<?php else : ?>
					<span class="tk-lineage__placeholder" aria-hidden="true"></span>
				<?php endif; ?>
			</div>

			<div class="tk-lineage__body tk-rv tk-rv--right">
				<blockquote class="tk-lineage__quote">
					<p><?php esc_html_e('My father built Trekmania to take people up mountains. I built Trip Kailash to take them to temples.', 'trip-kailash'); ?></p>
					<?php if ($tk_founder) : ?>
						<cite class="tk-lineage__cite"><?php echo esc_html($tk_founder); ?></cite>
					<?php endif; ?>
				</blockquote>

				<p><?php esc_html_e('Trip Kailash is the pilgrimage arm of Trekmania Nepal. The same guides, the same permits desk, the same emergency evacuation protocol, applied to journeys where the destination is a shrine rather than a summit.', 'trip-kailash'); ?></p>

				<?php if ($tk_trekmania) : ?>
					<p>
						<a class="tk-lineage__link" href="<?php echo esc_url($tk_trekmania); ?>" rel="noopener" target="_blank">
							<?php esc_html_e('Read Trekmania Nepal reviews', 'trip-kailash'); ?>
							<span class="tk-sr-only"><?php esc_html_e('(reviews for our parent company, opens in a new tab)', 'trip-kailash'); ?></span>
						</a>
					</p>
				<?php endif; ?>

				<?php if ($tk_shown) : ?>
					<dl class="tk-creds">
						<?php foreach ($tk_shown as $tk_value => $tk_label) : ?>
							<div class="tk-creds__item">
								<dt class="tk-creds__value tk-num"><?php echo esc_html($tk_value); ?></dt>
								<dd class="tk-creds__label"><?php echo esc_html($tk_label); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
