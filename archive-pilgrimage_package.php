<?php
/**
 * The catalogue
 *
 * This template existed before, in templates/, which is a directory the
 * WordPress template hierarchy never looks in. It also called a template part
 * that was never written. So the package archive has been falling through to
 * index.php the whole time, which prints the_content() with no title, no
 * width and no wrapper.
 *
 * Filtering is by tradition, driven by the taxonomy, and works with no
 * JavaScript at all: every filter is a real link to a real URL, which means it
 * is shareable, bookmarkable, crawlable and works on a bad connection.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$tk_active = isset($_GET['tradition']) ? sanitize_key(wp_unslash($_GET['tradition'])) : '';
?>

<main id="main" class="tk-catalogue" tabindex="-1">

	<header class="tk-catalogue__head">
		<div class="tk-wrap">
			<?php
			tk_section_head(
				__('Every circle we walk', 'trip-kailash'),
				__('Sacred yatras of Nepal and Tibet', 'trip-kailash'),
				array('level' => 'h1', 'align' => 'center', 'id' => 'tk-catalogue-title')
			);
			?>
		</div>
	</header>

	<div class="tk-wrap">
		<?php get_template_part('template-parts/catalogue/filters', null, array('active' => $tk_active)); ?>

		<?php if (have_posts()) : ?>
			<div class="tk-cards tk-stagger">
				<?php
				while (have_posts()) :
					the_post();
					get_template_part('template-parts/content', 'package-card');
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(array(
				'mid_size'  => 1,
				'prev_text' => __('Previous', 'trip-kailash'),
				'next_text' => __('Next', 'trip-kailash'),
			));
			?>

		<?php else : ?>
			<p class="tk-catalogue__empty">
				<?php esc_html_e('No yatras match that path yet. Tell us where you want to go and we will arrange it.', 'trip-kailash'); ?>
			</p>
			<p class="tk-actions">
				<a class="tk-btn" href="<?php echo esc_url(home_url('/book-yatra')); ?>"><?php esc_html_e('Ask us', 'trip-kailash'); ?></a>
			</p>
		<?php endif; ?>
	</div>

</main>

<?php
get_footer();
