<?php
/**
 * The hero
 *
 * A tall pinned region containing a sticky full-viewport stage. Scroll
 * progress through the pinned region maps 0 to 1 and drives the video's time,
 * so the page settles exactly as the footage reaches its composed ending.
 *
 * Five conditions send a visitor to the designed still instead: phones,
 * portrait tablets, coarse-pointer portrait, landscape phones, and reduced
 * motion. Those five live in CSS and in assets/js/hero-scrub.js and must match
 * character for character, or one side loads assets the other side hides.
 *
 * The still is not a fallback apology. It is a composed layout over the
 * ending frame, and it is what most of this audience sees, because most of
 * this traffic is Indian and Nepali mobile.
 *
 * With no clip set the hero renders as the still for everyone, which is the
 * honest state until footage exists rather than a broken video player.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_video_id  = (int) get_theme_mod('tk_hero_video_id', 0);
$tk_poster_id = (int) get_theme_mod('tk_hero_poster_id', 0);

$tk_video_url  = $tk_video_id ? wp_get_attachment_url($tk_video_id) : '';
$tk_poster_url = $tk_poster_id ? wp_get_attachment_image_url($tk_poster_id, 'full') : '';

// The loading ring needs a real total when the host omits Content-Length.
$tk_video_bytes = 0;
if ($tk_video_id) {
    $tk_path = get_attached_file($tk_video_id);
    if ($tk_path && file_exists($tk_path)) {
        $tk_video_bytes = (int) filesize($tk_path);
    }
}

$tk_has_scrub = ($tk_video_url && $tk_poster_url);

/*
 * The licence line. Registration numbers come from the Customizer, and an
 * unset one is simply omitted. A site whose central trust argument is
 * "check our numbers" cannot print a placeholder where a number belongs.
 */
$tk_reg = trim((string) get_theme_mod('tk_company_reg', ''));

$tk_classes = 'tk-hero' . ($tk_has_scrub ? ' tk-hero--scrub' : ' tk-hero--still-only');
?>

<section class="<?php echo esc_attr($tk_classes); ?>" id="hero"
	<?php if ($tk_has_scrub) : ?>
	data-video="<?php echo esc_url($tk_video_url); ?>"
	data-poster="<?php echo esc_url($tk_poster_url); ?>"
	data-bytes="<?php echo esc_attr($tk_video_bytes); ?>"
	<?php endif; ?>>

	<h1 class="tk-sr-only">Walk the sacred paths of Nepal with people who know the rituals</h1>

	<div class="tk-hero__pin">
		<div class="tk-hero__stage">

			<?php if ($tk_poster_url) : ?>
				<div class="tk-hero__poster" aria-hidden="true"
					style="background-image:url('<?php echo esc_url($tk_poster_url); ?>')"></div>
			<?php endif; ?>

			<?php if ($tk_has_scrub) : ?>
				<?php /* Decorative: no controls, out of the tab order, hidden from
				         screen readers, so keyboard and assistive users land on the
				         captions and the actions instead of on a video element. */ ?>
				<video class="tk-hero__video" preload="none" muted playsinline
					aria-hidden="true" tabindex="-1"></video>

				<svg class="tk-hero__ring" viewBox="0 0 48 48" aria-hidden="true">
					<circle cx="24" cy="24" r="20" fill="none" stroke="currentColor"
						stroke-width="3" stroke-dasharray="126"
						style="stroke-dashoffset:var(--tk-ring, 126)" />
				</svg>
			<?php endif; ?>

			<?php /* The global base scrim. Always on, so no frame is ever raw
			         behind the page. Per-band scrims ride on top of this. */ ?>
			<div class="tk-hero__scrim" aria-hidden="true"></div>

			<?php if ($tk_has_scrub) : ?>
			<div class="tk-hero__bands">

				<?php /* Band ranges are starting points from the design package,
				         validated later by the flick test at 120, 240 and 360px
				         wheel steps. Copy ships verbatim. */ ?>

				<div class="tk-band" aria-hidden="true" data-from="0" data-to="0.16" data-entrance="rise">
					<p class="tk-band__line">Some mountains are climbed. This one is walked around.</p>
				</div>

				<div class="tk-band" aria-hidden="true" data-from="0.20" data-to="0.42" data-entrance="drift">
					<p class="tk-band__line">Fifty-two kilometres. One circle. Four faiths walking the same line.</p>
				</div>

				<?php
				/*
				 * Blocked on design-package section 10, item 2: sources give the
				 * Horse Year multiplier as thirteen, some as twelve. This line
				 * does not ship until the operator confirms which figure their
				 * own tradition uses. Getting religious detail wrong on a
				 * pilgrimage site is worse than saying nothing at all.
				 */
				$tk_merit_confirmed = (bool) get_theme_mod('tk_merit_figure_confirmed', false);
				?>
				<?php if ($tk_merit_confirmed) : ?>
				<div class="tk-band" aria-hidden="true" data-from="0.46" data-to="0.68" data-entrance="focus">
					<p class="tk-band__line" data-text="Walked in a Horse Year, this circle counts thirteen times.">Walked in a Horse Year, this circle counts thirteen times.</p>
				</div>
				<?php endif; ?>

				<div class="tk-band tk-band--settle" data-from="0.74" data-to="1" data-entrance="settle">
					<p class="tk-band__head">Walk the sacred paths of Nepal with people who know the rituals</p>
					<p class="tk-band__sub">Muktinath, Gosaikunda, Haleshi and Kailash, led by guides who
						understand what you have come to do, not only how to get you there.</p>
					<div class="tk-actions">
						<a class="tk-btn" href="#paths">Find your yatra</a>
						<a class="tk-btn-ghost" href="#departures">Kailash, and which year to walk it</a>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<?php
	/*
	 * The still. Shown to everyone the five gates catch, and to everyone when
	 * no clip is set. This carries the real heading and the real actions, so
	 * the page is complete and has a working call to action even if the video
	 * never loads or was never added.
	 */
	?>
	<div class="tk-hero__still">
		<div class="tk-wrap">
			<p class="tk-eyebrow">Pilgrimage in Nepal and Tibet</p>
			<p class="tk-hero__head">Walk the sacred paths of Nepal with people who know the rituals</p>
			<p class="tk-hero__sub">Muktinath, Gosaikunda, Haleshi and Kailash, led by guides who
				understand what you have come to do, not only how to get you there.</p>
			<div class="tk-actions">
				<a class="tk-btn" href="#paths">Find your yatra</a>
				<a class="tk-btn-ghost" href="#departures">Kailash, and which year to walk it</a>
			</div>
		</div>
	</div>

	<div class="tk-wrap">
		<p class="tk-hero__licence">
			<?php
			$tk_line = 'Licensed by the Government of Nepal.';

			if ($tk_reg) {
				$tk_line .= ' Reg. ' . $tk_reg . '.';
			}

			$tk_line .= ' The pilgrimage arm of Trekmania Nepal.';

			echo esc_html($tk_line);
			?>
		</p>
	</div>
</section>
