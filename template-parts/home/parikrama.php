<?php
/**
 * The parikrama: the pinned temple gallery
 *
 * The signature interaction, and the section the premise is built on. Vertical
 * scroll drives the track horizontally, and whichever temple is nearest the
 * viewport centre becomes active: full scale, in focus, and its significance
 * expands beneath it. Everything else blurs, dims and shrinks in proportion to
 * its distance from centre, so the falloff is continuous rather than a hard
 * on-off toggle.
 *
 * Copy here describes SACRED MEANING, never logistics. The lake Shiva formed
 * with his trident, not a five-day moderate trek. The facts sit in the small
 * row underneath. Every competitor leads with logistics, which is exactly why
 * this does not.
 *
 * Kailash is forced last. You arrive at the mountain; you do not start there.
 *
 * Below 900px the pinning is switched off entirely and the track becomes a
 * native horizontal swipe with scroll-snap. Pinned scroll-jacking on phones is
 * reliably awful and most of this traffic is phones.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_query = new WP_Query(array(
    'post_type'           => 'pilgrimage_package',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'orderby'             => array('menu_order' => 'ASC', 'date' => 'ASC'),
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
));

if (!$tk_query->have_posts()) {
    return;
}

$tk_packages = $tk_query->posts;
wp_reset_postdata();

/*
 * Kailash last, whatever the editor set.
 *
 * This is a design decision rather than an ordering preference, so it is
 * enforced here rather than left to menu_order where one drag would undo it.
 * The whole sequence is built to arrive at the mountain, and the flagship is
 * also the one package that has never sold: putting it first asks a stranger
 * for $2,500 before the site has earned anything.
 */
$tk_anchor = array();
$tk_rest   = array();

foreach ($tk_packages as $tk_package_post) {
    if (false !== stripos($tk_package_post->post_name, 'kailash')) {
        $tk_anchor[] = $tk_package_post;
    } else {
        $tk_rest[] = $tk_package_post;
    }
}

$tk_packages = array_merge($tk_rest, $tk_anchor);
?>

<section class="tk-parikrama tk-deep" id="parikrama" aria-labelledby="tk-parikrama-title">

	<div class="tk-parikrama__head tk-wrap">
		<?php
		tk_section_head(
			__('The circles we walk', 'trip-kailash'),
			__('Journeys we know by heart', 'trip-kailash'),
			array('align' => 'center', 'id' => 'tk-parikrama-title')
		);
		?>
	</div>

	<div class="tk-parikrama__pin" id="tk-parikrama-pin">
		<div class="tk-parikrama__stage">
			<ul class="tk-parikrama__track" id="tk-parikrama-track">

				<?php foreach ($tk_packages as $tk_package_post) :
					$tk_id    = $tk_package_post->ID;
					$tk_pitch = tk_package('short_pitch', $tk_id);
					$tk_terms = get_the_terms($tk_id, 'tradition');
					?>
					<li class="tk-temple">
						<a class="tk-temple__link" href="<?php echo esc_url(get_permalink($tk_id)); ?>">

							<span class="tk-temple__frame">
								<?php if (has_post_thumbnail($tk_id)) : ?>
									<?php
									echo get_the_post_thumbnail($tk_id, 'large', array(
										'class'   => 'tk-temple__img',
										'loading' => 'lazy',
										'alt'     => '',
										'style'   => 'object-position:' . esc_attr(tk_package('hero_focal', $tk_id)),
									));
									?>
								<?php else : ?>
									<span class="tk-temple__placeholder" aria-hidden="true"></span>
								<?php endif; ?>
							</span>

							<span class="tk-temple__name"><?php echo esc_html(get_the_title($tk_id)); ?></span>

							<?php if ($tk_terms && !is_wp_error($tk_terms)) : ?>
								<span class="tk-temple__traditions">
									<?php echo esc_html(implode(' · ', wp_list_pluck($tk_terms, 'name'))); ?>
								</span>
							<?php endif; ?>

							<?php if ($tk_pitch) : ?>
								<span class="tk-temple__meaning"><?php echo esc_html($tk_pitch); ?></span>
							<?php endif; ?>

							<span class="tk-temple__facts tk-num">
								<?php if (tk_package_has('duration_days', $tk_id)) : ?>
									<span><?php echo esc_html(tk_plural((int) tk_package('duration_days', $tk_id), __('day', 'trip-kailash'), __('days', 'trip-kailash'))); ?></span>
								<?php endif; ?>
								<?php if (tk_package_has('max_altitude_m', $tk_id)) : ?>
									<span><?php echo esc_html(number_format_i18n((float) tk_package('max_altitude_m', $tk_id))); ?> m</span>
								<?php endif; ?>
								<?php if (tk_package_has('price_from', $tk_id)) : ?>
									<span><?php printf(esc_html__('from $%s', 'trip-kailash'), esc_html(number_format_i18n((float) tk_package('price_from', $tk_id)))); ?></span>
								<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="tk-parikrama__foot tk-wrap">
		<a class="tk-btn-ghost" href="<?php echo esc_url(get_post_type_archive_link('pilgrimage_package')); ?>">
			<?php esc_html_e('See all yatras', 'trip-kailash'); ?>
		</a>
	</div>
</section>
