<?php
/**
 * Permits, fitness and packing
 *
 * The three lists that decide whether someone can actually come, grouped
 * together because they are read together. Fitness leads, because the
 * research is emphatic that pilgrims prepare the spiritual side for months
 * and treat the altitude as an afterthought, and that is precisely why it
 * catches people out.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id = get_the_ID();

$tk_blocks = array(
    array(
        'key'   => 'fitness_notes',
        'title' => __('Whether you can do it', 'trip-kailash'),
        'lines' => tk_package_lines('fitness_notes', $tk_id),
    ),
    array(
        'key'   => 'permits_required',
        'title' => __('Permits you will need', 'trip-kailash'),
        'lines' => tk_package_lines('permits_required', $tk_id),
    ),
    array(
        'key'   => 'packing_list',
        'title' => __('What to bring', 'trip-kailash'),
        'lines' => tk_package_lines('packing_list', $tk_id),
    ),
);

$tk_blocks = array_values(array_filter($tk_blocks, function ($block) {
    return !empty($block['lines']);
}));

if (empty($tk_blocks)) {
    return;
}
?>

<section class="tk-pk-section" id="preparation" aria-labelledby="tk-prep-title">
	<?php
	tk_section_head(
		__('Before you go', 'trip-kailash'),
		__('What to sort out first', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-prep-title')
	);
	?>

	<div class="tk-prep">
		<?php foreach ($tk_blocks as $tk_block) : ?>
			<div class="tk-prep__block">
				<h3 class="tk-prep__head"><?php echo esc_html($tk_block['title']); ?></h3>
				<ul class="tk-prep__list">
					<?php foreach ($tk_block['lines'] as $tk_line) : ?>
						<li><?php echo esc_html($tk_line); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
</section>
