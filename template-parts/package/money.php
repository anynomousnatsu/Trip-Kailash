<?php
/**
 * The money explainer and the trust cluster
 *
 * Sits directly under the enquiry form, because that is where doubt peaks.
 * Someone has just typed their name and email into a page belonging to a
 * company in another country, and the next thought is what happens to my
 * money. Answering it three lines later is too late.
 *
 * Numbers read from the Customizer and are omitted when unset. A trust block
 * showing a bracket where a licence number belongs does the opposite of its
 * job.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_deposit = (int) tk_package('deposit_percent', get_the_ID());
$tk_deposit = $tk_deposit ? $tk_deposit : 30;

$tk_credentials = array(
    __('TAAN membership', 'trip-kailash')  => trim((string) get_theme_mod('tk_taan_number', '')),
    __('Tourism licence', 'trip-kailash')  => trim((string) get_theme_mod('tk_ntb_number', '')),
    __('Company reg.', 'trip-kailash')     => trim((string) get_theme_mod('tk_company_reg', '')),
);

$tk_credentials = array_filter($tk_credentials);
$tk_cancellation = trim((string) get_theme_mod('tk_cancellation_policy', ''));
?>

<div class="tk-money">

	<h2 class="tk-money__head"><?php esc_html_e('How a place is confirmed', 'trip-kailash'); ?></h2>

	<ol class="tk-money__steps">
		<li>
			<strong><?php esc_html_e('You enquire', 'trip-kailash'); ?></strong>
			<?php esc_html_e('No payment, no card details.', 'trip-kailash'); ?>
		</li>
		<li>
			<strong><?php esc_html_e('We confirm and invoice', 'trip-kailash'); ?></strong>
			<?php esc_html_e('Written confirmation with a full itinerary, from a company registered in Nepal.', 'trip-kailash'); ?>
		</li>
		<li>
			<strong>
				<?php
				printf(
					/* translators: %d: deposit percentage. */
					esc_html__('You pay %d%%', 'trip-kailash'),
					$tk_deposit
				);
				?>
			</strong>
			<?php esc_html_e('The balance is due on arrival in Kathmandu, not before.', 'trip-kailash'); ?>
		</li>
	</ol>

	<?php if ($tk_cancellation) : ?>
		<p class="tk-money__policy"><?php echo esc_html($tk_cancellation); ?></p>
	<?php endif; ?>

	<?php if ($tk_credentials) : ?>
		<div class="tk-money__trust">
			<p class="tk-money__trust-head"><?php esc_html_e('Check us before you pay us', 'trip-kailash'); ?></p>
			<dl class="tk-money__creds">
				<?php foreach ($tk_credentials as $tk_label => $tk_value) : ?>
					<div class="tk-money__cred">
						<dt><?php echo esc_html($tk_label); ?></dt>
						<dd class="tk-num"><?php echo esc_html($tk_value); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
			<p class="tk-money__verify">
				<a href="<?php echo esc_url(home_url('/#verify')); ?>"><?php esc_html_e('Where to verify each of these', 'trip-kailash'); ?></a>
			</p>
		</div>
	<?php endif; ?>
</div>
