<?php
/**
 * How a place is confirmed
 *
 * Placed before the lineage section on purpose. For an international wire
 * transfer to Nepal, uncertainty about money is a sharper objection than
 * uncertainty about quality, and the research is blunt about why: an operator
 * in Hyderabad took over fifteen lakh rupees from seven pilgrims and never ran
 * their trip. Someone reading this page is braced for vagueness about money.
 *
 * So the three steps say exactly what happens and exactly when anything is
 * paid, in that order.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_steps = array(
    array(
        'title' => __('You enquire', 'trip-kailash'),
        'body'  => __('Tell us the yatra, your dates and how many are travelling. No payment, no card details.', 'trip-kailash'),
    ),
    array(
        'title' => __('We confirm and invoice', 'trip-kailash'),
        'body'  => __('We check lodges, permits and guides, then send written confirmation with a full itinerary and an invoice.', 'trip-kailash'),
    ),
    array(
        'title' => __('You pay 30%', 'trip-kailash'),
        'body'  => __('A thirty percent deposit holds your place. The balance is due on arrival in Kathmandu, not before.', 'trip-kailash'),
    ),
);
?>

<section class="tk-section tk-confirm" id="confirm" aria-labelledby="tk-confirm-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Booking', 'trip-kailash'),
			__('How a place is confirmed', 'trip-kailash'),
			array('id' => 'tk-confirm-title')
		);
		?>

		<ol class="tk-steps tk-stagger">
			<?php foreach ($tk_steps as $tk_step) : ?>
				<li class="tk-step tk-rv">
					<h3 class="tk-step__title"><?php echo esc_html($tk_step['title']); ?></h3>
					<p class="tk-step__body"><?php echo esc_html($tk_step['body']); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
