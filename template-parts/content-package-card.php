<?php
/**
 * Package card
 *
 * The old card laid a dark band across the bottom of the photograph and put
 * the title inside it. It was flagged in review on every card on the page,
 * and the reason it looked wrong is that the band had no relationship to the
 * image underneath: it cut the photograph at an arbitrary line and changed
 * height with the length of the title.
 *
 * So the photograph is now a photograph, edge to edge, and the words sit on
 * the card below it. Nothing overlaps.
 *
 * Every card is identical in structure. A card missing an image, or one with
 * an extra line where its siblings have none, reads as a mistake rather than
 * as content, so a package with no thumbnail gets a drawn placeholder in the
 * brand's own materials instead of a broken image icon.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id      = get_the_ID();
$tk_pitch   = tk_package('short_pitch', $tk_id);
$tk_grading = tk_package_grading($tk_id);
$tk_terms   = get_the_terms($tk_id, 'tradition');
?>

<article class="tk-card tk-rv">
	<a class="tk-card__link" href="<?php the_permalink(); ?>">

		<span class="tk-card__media">
			<?php if (has_post_thumbnail($tk_id)) : ?>
				<?php
				the_post_thumbnail('medium_large', array(
					'class'   => 'tk-card__img',
					'loading' => 'lazy',
					'style'   => 'object-position:' . esc_attr(tk_package('hero_focal', $tk_id)),
					'alt'     => '',
				));
				?>
			<?php else : ?>
				<span class="tk-card__placeholder" aria-hidden="true"></span>
			<?php endif; ?>

			<?php if ($tk_grading) : ?>
				<span class="tk-card__grade" style="--tk-grade-token: <?php echo esc_attr($tk_grading['token']); ?>">
					<span class="tk-grade-dot" aria-hidden="true"></span>
					<?php echo esc_html($tk_grading['label']); ?>
				</span>
			<?php endif; ?>
		</span>

		<span class="tk-card__body">
			<?php if ($tk_terms && !is_wp_error($tk_terms)) : ?>
				<span class="tk-card__traditions">
					<?php echo esc_html(implode(' · ', wp_list_pluck($tk_terms, 'name'))); ?>
				</span>
			<?php endif; ?>

			<span class="tk-card__title"><?php the_title(); ?></span>

			<?php if ($tk_pitch) : ?>
				<span class="tk-card__pitch"><?php echo esc_html($tk_pitch); ?></span>
			<?php endif; ?>

			<span class="tk-card__facts tk-num">
				<?php if (tk_package_has('duration_days', $tk_id)) : ?>
					<span><?php echo esc_html(tk_plural((int) tk_package('duration_days', $tk_id), __('day', 'trip-kailash'), __('days', 'trip-kailash'))); ?></span>
				<?php endif; ?>
				<?php if (tk_package_has('max_altitude_m', $tk_id)) : ?>
					<span><?php echo esc_html(number_format_i18n((float) tk_package('max_altitude_m', $tk_id))); ?> m</span>
				<?php endif; ?>
			</span>

			<span class="tk-card__foot">
				<?php if (tk_package_has('price_from', $tk_id)) : ?>
					<span class="tk-card__price tk-num">
						<?php
						printf(
							/* translators: %s: price. */
							esc_html__('From $%s', 'trip-kailash'),
							esc_html(number_format_i18n((float) tk_package('price_from', $tk_id)))
						);
						?>
					</span>
				<?php endif; ?>
				<span class="tk-card__more"><?php esc_html_e('See the yatra', 'trip-kailash'); ?></span>
			</span>
		</span>
	</a>
</article>
