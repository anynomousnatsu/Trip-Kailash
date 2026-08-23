<?php
/**
 * Tradition filters
 *
 * Real links to real URLs, not JavaScript tabs. That makes every filtered
 * view shareable, bookmarkable and crawlable, and it means the catalogue still
 * filters on a connection too poor to run a script, which is a real condition
 * for a lot of this audience.
 *
 * Traditions with no packages are not offered. A filter that leads to an empty
 * page is worse than one fewer filter.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_active = isset($args['active']) ? $args['active'] : '';
$tk_base   = get_post_type_archive_link('pilgrimage_package');

$tk_terms = get_terms(array(
    'taxonomy'   => 'tradition',
    'hide_empty' => true,
));

if (is_wp_error($tk_terms) || count($tk_terms) < 2) {
    return;
}
?>

<nav class="tk-filters" aria-label="<?php esc_attr_e('Filter by tradition', 'trip-kailash'); ?>">
	<a class="tk-filter<?php echo '' === $tk_active ? ' is-on' : ''; ?>"
		href="<?php echo esc_url($tk_base); ?>"
		<?php echo '' === $tk_active ? 'aria-current="true"' : ''; ?>>
		<?php esc_html_e('All yatras', 'trip-kailash'); ?>
	</a>

	<?php foreach ($tk_terms as $tk_term) : ?>
		<a class="tk-filter<?php echo $tk_active === $tk_term->slug ? ' is-on' : ''; ?>"
			href="<?php echo esc_url(add_query_arg('tradition', $tk_term->slug, $tk_base)); ?>"
			<?php echo $tk_active === $tk_term->slug ? 'aria-current="true"' : ''; ?>>
			<?php echo esc_html($tk_term->name); ?>
			<span class="tk-filter__count tk-num"><?php echo esc_html($tk_term->count); ?></span>
		</a>
	<?php endforeach; ?>
</nav>
