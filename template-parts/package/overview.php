<?php
/**
 * The narrative overview
 *
 * Renders nothing at all when there is nothing to say. The live pages carry a
 * grey block with no content in it under the Tour Overview heading, which
 * reads as a broken page rather than as an empty one, and it was flagged in
 * review for exactly that reason.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_overview = tk_package_overview(get_the_ID());

if ('' === trim($tk_overview)) {
    return;
}
?>

<section class="tk-pk-section" id="overview" aria-labelledby="tk-overview-title">
	<?php
	tk_section_head(
		__('The journey', 'trip-kailash'),
		__('What this yatra is', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-overview-title')
	);
	?>
	<div class="tk-prose"><?php echo wp_kses_post($tk_overview); ?></div>
</section>
