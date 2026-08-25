<?php
/**
 * Homepage hero
 *
 * The photograph carries it. Two columns, both sitting on the vertical
 * centre: the claim and one action on the left, the tour finder on the
 * right, barely there against the sky.
 *
 * Words come first in the markup because they come first on the screen and
 * first in the argument. The finder is a convenience and reads after it.
 *
 * Everything is set in the four brand colours. Sandal for the words, brass
 * for the action, bark for the glass. The hero is where a palette either
 * holds or stops being one, so nothing here introduces a fifth.
 *
 * @package TripKailash
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_image_id = (int) get_theme_mod('tk_hero_poster_id', 0);
$tk_viewing  = trim((string) get_theme_mod('tk_hero_viewing', __("Lord Shiva's home", 'trip-kailash')));
?>

<section class="tk-hero" id="hero" aria-labelledby="tk-hero-title">

	<div class="tk-hero__media" aria-hidden="true">
		<?php if ($tk_image_id) : ?>
			<?php
			echo wp_get_attachment_image($tk_image_id, 'full', false, array(
				'class'         => 'tk-hero__img',
				'fetchpriority' => 'high',
				'decoding'      => 'async',
				'alt'           => '',
			));
			?>
		<?php else : ?>
			<span class="tk-hero__placeholder"></span>
		<?php endif; ?>
	</div>

	<div class="tk-hero__scrim" aria-hidden="true"></div>

	<div class="tk-wrap tk-hero__inner">

		<div class="tk-hero__lede">
			<h1 class="tk-hero__title" id="tk-hero-title">
				<?php esc_html_e('Walk the sacred paths of Nepal with people who know the rituals', 'trip-kailash'); ?>
			</h1>

			<p class="tk-hero__sub">
				<?php esc_html_e('We arrange tours to sacred Hindu and Buddhist locations while walking the rituals of those places, yoga and retreats. Eleven percent of every tour\'s earnings goes to feeding and helping the ones in need, with full transparency for you to be a part of it.', 'trip-kailash'); ?>
			</p>

			<p class="tk-hero__actions">
				<a class="tk-hero__cta" href="<?php echo esc_url(get_post_type_archive_link('pilgrimage_package')); ?>">
					<?php esc_html_e('Divya Yatras', 'trip-kailash'); ?>
				</a>
			</p>

			<?php if ($tk_viewing) : ?>
				<p class="tk-hero__viewing">
					<span class="tk-hero__viewing-dot" aria-hidden="true"></span>
					<?php
					printf(
						/* translators: %s: the place shown in the hero photograph. */
						esc_html__('Currently viewing: %s', 'trip-kailash'),
						esc_html($tk_viewing)
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<form class="tk-finder" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<label class="tk-finder__label" for="tk-finder-input">
				<?php esc_html_e('Quick Tour Finder', 'trip-kailash'); ?>
			</label>

			<div class="tk-finder__bar">
				<input type="hidden" name="post_type" value="pilgrimage_package">
				<input class="tk-finder__input" id="tk-finder-input" type="search" name="s"
					placeholder="<?php esc_attr_e('Muktinath, Gosaikunda', 'trip-kailash'); ?>"
					autocomplete="off">

				<?php /* Icon only. A word here would make the bar a form to fill in
				         rather than a thing to type into and go. */ ?>
				<button class="tk-finder__go" type="submit">
					<span class="tk-sr-only"><?php esc_html_e('Search yatras', 'trip-kailash'); ?></span>
					<svg viewBox="0 0 20 20" width="20" height="20" fill="none" aria-hidden="true" focusable="false">
						<circle cx="8.5" cy="8.5" r="5.75" stroke="currentColor" stroke-width="1.9" />
						<path d="M12.8 12.8 L17.2 17.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
					</svg>
				</button>
			</div>
		</form>
	</div>
</section>
