<?php
/**
 * Template Name: Sacred Paths
 *
 * The catalogue with its doors on top.
 *
 * The tradition doors come first for the same reason they do on the homepage:
 * they reframe what the visitor thinks they are shopping for before a price
 * appears. Once someone is comparing $350 to $1,000 the pilgrimage framing is
 * gone and the site is competing on cost with all of Thamel.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$tk_active = isset($_GET['tradition']) ? sanitize_key(wp_unslash($_GET['tradition'])) : '';

$tk_args = array(
    'post_type'      => 'pilgrimage_package',
    'post_status'    => 'publish',
    'posts_per_page' => 24,
    'orderby'        => array('menu_order' => 'ASC', 'date' => 'DESC'),
);

if ($tk_active && term_exists($tk_active, 'tradition')) {
    $tk_args['tax_query'] = array(
        array(
            'taxonomy' => 'tradition',
            'field'    => 'slug',
            'terms'    => $tk_active,
        ),
    );
}

$tk_packages = new WP_Query($tk_args);
?>

<main id="main" class="tk-catalogue" tabindex="-1">

	<header class="tk-catalogue__head">
		<div class="tk-wrap">
			<?php
			tk_section_head(
				__('Every circle we walk', 'trip-kailash'),
				get_the_title(),
				array('level' => 'h1', 'align' => 'center', 'id' => 'tk-paths-title')
			);
			?>
		</div>
	</header>

	<?php get_template_part('template-parts/home/doors'); ?>

	<div class="tk-wrap tk-catalogue__list" id="yatras">
		<?php get_template_part('template-parts/catalogue/filters', null, array('active' => $tk_active)); ?>

		<?php if ($tk_packages->have_posts()) : ?>
			<div class="tk-cards tk-stagger">
				<?php
				while ($tk_packages->have_posts()) :
					$tk_packages->the_post();
					get_template_part('template-parts/content', 'package-card');
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="tk-catalogue__empty">
				<?php esc_html_e('No yatras match that path yet. Tell us where you want to go and we will arrange it.', 'trip-kailash'); ?>
			</p>
			<p class="tk-actions">
				<a class="tk-btn" href="<?php echo esc_url(home_url('/book-yatra')); ?>"><?php esc_html_e('Ask us', 'trip-kailash'); ?></a>
			</p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>

	<?php
	/* Anything the editor wrote on the page itself still renders, below the
	   catalogue, so this template does not silently swallow their content. */
	while (have_posts()) :
		the_post();
		$tk_content = get_the_content();

		if ('' !== trim($tk_content)) :
			?>
			<div class="tk-wrap tk-catalogue__prose">
				<div class="tk-prose"><?php the_content(); ?></div>
			</div>
			<?php
		endif;
	endwhile;
	?>

</main>

<?php
get_footer();
