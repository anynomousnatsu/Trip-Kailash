<?php
/**
 * Package hero
 *
 * One image, the name, and the one line that says what the place is. No
 * carousel: carousels delay the largest paint, nobody sees slide three, and
 * they signal indecision about which photograph is the good one.
 *
 * The focal point comes from the hero_focal field, which exists because a
 * cover-cropped hero puts the shrine off frame on wide or short screens.
 * Being able to say "keep Muktinath in shot" without re-cropping the file is
 * the difference between a usable photograph and an unusable one.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id    = get_the_ID();
$tk_focal = tk_package('hero_focal', $tk_id);
$tk_pitch = tk_package('short_pitch', $tk_id);
$tk_terms = get_the_terms($tk_id, 'tradition');
?>

<header class="tk-package-hero">

	<div class="tk-package-hero__media" aria-hidden="true">
		<?php if (has_post_thumbnail($tk_id)) : ?>
			<?php
			the_post_thumbnail('full', array(
				'class'         => 'tk-package-hero__img',
				'style'         => 'object-position:' . esc_attr($tk_focal),
				'fetchpriority' => 'high',
				'alt'           => '',
			));
			?>
		<?php else : ?>
			<span class="tk-package-hero__placeholder"></span>
		<?php endif; ?>
	</div>

	<div class="tk-package-hero__scrim" aria-hidden="true"></div>

	<div class="tk-wrap tk-package-hero__inner">

		<?php if ($tk_terms && !is_wp_error($tk_terms)) : ?>
			<p class="tk-eyebrow tk-package-hero__traditions">
				<?php echo esc_html(implode(' · ', wp_list_pluck($tk_terms, 'name'))); ?>
			</p>
		<?php endif; ?>

		<h1 class="tk-package-hero__title"><?php the_title(); ?></h1>

		<?php if ($tk_pitch) : ?>
			<p class="tk-package-hero__pitch"><?php echo esc_html($tk_pitch); ?></p>
		<?php endif; ?>

		<p class="tk-package-hero__jump">
			<a class="tk-btn" href="#reserve"><?php esc_html_e('Reserve your place', 'trip-kailash'); ?></a>
		</p>
	</div>
</header>
