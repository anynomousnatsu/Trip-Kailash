<?php
/**
 * A single pilgrimage package
 *
 * Generated entirely from the field schema. Nothing on this page is typed
 * into a layout, which is what stops a package saying 12 days in its title
 * and 14 in its spec block, and what makes adding an eighth package a data
 * entry job rather than a page rebuild.
 *
 * The running order answers a nervous buyer's questions in the order they
 * actually ask them: what is this and what does it cost, what happens each
 * day, what is and is not included, what could stop me going, and only then
 * the details. The reserve panel stays with them the whole way down.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();

    $tk_id = get_the_ID();
    ?>

	<main id="main" class="tk-package" tabindex="-1">

		<?php get_template_part('template-parts/package/hero'); ?>
		<?php get_template_part('template-parts/package/specs'); ?>

		<div class="tk-package__body">
			<div class="tk-wrap tk-package__grid">

				<div class="tk-package__main">
					<?php
					get_template_part('template-parts/package/overview');
					get_template_part('template-parts/package/itinerary');
					get_template_part('template-parts/package/inclusions');
					get_template_part('template-parts/package/season');
					get_template_part('template-parts/package/preparation');
					get_template_part('template-parts/package/faq');
					?>
				</div>

				<aside class="tk-package__aside" aria-label="<?php esc_attr_e('Reserve your place', 'trip-kailash'); ?>">
					<?php get_template_part('template-parts/package/reserve'); ?>
				</aside>

			</div>
		</div>

	</main>

	<?php
endwhile;

get_footer();
