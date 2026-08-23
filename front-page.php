<?php
/**
 * The homepage
 *
 * One continuous scroll-driven journey, built from the design package in
 * docs/design-package.md. The premise it serves is parikrama: you do not climb
 * what is sacred, you walk around it. So the page is a circle, the catalogue
 * is organised as circles, and the one interactive moment is a circle the
 * visitor walks with their own hand.
 *
 * This file is deliberately a running order and nothing else. Every section is
 * a template part, so any one of them can be reworked without opening the
 * others, and none of them is large enough to get lost in.
 *
 * Section order is not arbitrary, and two placements do real work:
 *
 *   The tradition doors sit ABOVE the packages. They reframe what the visitor
 *   thinks they are shopping for before a price appears. Once someone is
 *   comparing $350 to $1,000 the pilgrimage framing is gone and the site is
 *   competing on cost with all of Thamel.
 *
 *   The money explainer sits BEFORE the lineage section. For an international
 *   wire transfer to Nepal, uncertainty about money is a sharper objection
 *   than uncertainty about quality.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="tk-home" tabindex="-1">

	<?php
	/* The hero. Scrub-driven where the hardware and the visitor's settings
	   allow it, a designed still everywhere else. Never a fallback apology. */
	get_template_part('template-parts/home/hero');

	/* Which tradition are you travelling in? Three drawn doors. */
	get_template_part('template-parts/home/doors');

	/* The parikrama: the pinned temple gallery, and the signature moment.
	   Kailash sits last, because you arrive at the mountain. */
	get_template_part('template-parts/home/parikrama');

	/* Which year to walk it, and the kora ring. */
	get_template_part('template-parts/home/departures');

	/* How a place is confirmed. Money before quality, deliberately. */
	get_template_part('template-parts/home/confirm');

	/* Check us before you pay us. The strongest section on the page, and the
	   one no competitor will copy. */
	get_template_part('template-parts/home/verify');

	/* Who takes you. Lineage, because there are no testimonials yet and we do
	   not invent them or borrow the parent company's. */
	get_template_part('template-parts/home/lineage');

	/* Before you go. The guide cards, and the whole SEO surface. */
	get_template_part('template-parts/home/guides');
	?>

</main>

<?php
get_footer();
