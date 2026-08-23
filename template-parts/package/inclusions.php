<?php
/**
 * What is and is not included
 *
 * Explicitly separated, and shown side by side. A merged list, or an
 * exclusions list buried below the fold, is where trust goes on a page
 * asking for an international wire transfer. The exclusions column is the
 * one a careful buyer reads first, so it is given equal weight rather than
 * being treated as the small print.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id = get_the_ID();
$tk_in = tk_package_lines('inclusions', $tk_id);
$tk_out = tk_package_lines('exclusions', $tk_id);

if (empty($tk_in) && empty($tk_out)) {
    return;
}
?>

<section class="tk-pk-section" id="inclusions" aria-labelledby="tk-inclusions-title">
	<?php
	tk_section_head(
		__('The price', 'trip-kailash'),
		__('What is and is not included', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-inclusions-title')
	);
	?>

	<div class="tk-inclusions">
		<?php if ($tk_in) : ?>
			<div class="tk-inclusions__col tk-inclusions__col--in">
				<h3 class="tk-inclusions__head"><?php esc_html_e('Included', 'trip-kailash'); ?></h3>
				<ul class="tk-inclusions__list">
					<?php foreach ($tk_in as $tk_line) : ?>
						<li><?php echo esc_html($tk_line); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ($tk_out) : ?>
			<div class="tk-inclusions__col tk-inclusions__col--out">
				<h3 class="tk-inclusions__head"><?php esc_html_e('Not included', 'trip-kailash'); ?></h3>
				<ul class="tk-inclusions__list">
					<?php foreach ($tk_out as $tk_line) : ?>
						<li><?php echo esc_html($tk_line); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
