<?php
/**
 * Reserve your place
 *
 * Not a cart, and it never pretends to be one. This business closes by
 * enquiry, confirmation, invoice, then a thirty percent deposit, so the panel
 * ends in a conversation. A checkout button that leads to an enquiry form is
 * the kind of small lie that costs a booking at exactly the wrong moment.
 *
 * The order is deliberate and comes straight from the review notes: pricing
 * first, including the group tiers, because that is what the sidebar was
 * asked to carry instead of a table of contents. Then the dates, then who is
 * coming, then the money explainer and the credentials, because doubt peaks
 * right at the point of handing over details.
 *
 * It posts to the existing AJAX handler, which already carries the nonce, the
 * honeypot and the rate limiting, rather than adding a second way in.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_id = get_the_ID();

$tk_price      = tk_package('price_from', $tk_id);
$tk_note       = tk_package('price_note', $tk_id);
$tk_deposit    = (int) tk_package('deposit_percent', $tk_id);
$tk_tiers      = tk_package('group_pricing', $tk_id);
$tk_type       = tk_package('departure_type', $tk_id);
$tk_departures = tk_package('fixed_departures', $tk_id);
$tk_lead       = tk_package('lead_time_note', $tk_id);
$tk_max        = (int) tk_package('group_size_max', $tk_id);
$tk_uid        = 'pk' . $tk_id;
?>

<div class="tk-reserve" id="reserve">

	<?php if (tk_package_has('price_from', $tk_id)) : ?>
		<div class="tk-reserve__price">
			<p class="tk-reserve__from"><?php esc_html_e('From', 'trip-kailash'); ?></p>
			<p class="tk-reserve__amount tk-num">
				<span class="tk-reserve__currency">$</span><?php echo esc_html(number_format_i18n((float) $tk_price)); ?>
			</p>
			<?php if ($tk_note) : ?>
				<p class="tk-reserve__note"><?php echo esc_html($tk_note); ?></p>
			<?php endif; ?>
		</div>

		<?php if (!empty($tk_tiers)) : ?>
			<div class="tk-reserve__tiers">
				<p class="tk-reserve__tiers-head"><?php esc_html_e('The more of you there are, the less it costs each', 'trip-kailash'); ?></p>
				<ul class="tk-reserve__tier-list">
					<?php foreach ($tk_tiers as $tk_tier) :
						if (empty($tk_tier['min_pax']) || empty($tk_tier['price'])) {
							continue;
						}
						?>
						<li class="tk-reserve__tier">
							<span class="tk-num"><?php echo esc_html(sprintf(__('%d or more', 'trip-kailash'), (int) $tk_tier['min_pax'])); ?></span>
							<span class="tk-num"><?php echo esc_html('$' . number_format_i18n((float) $tk_tier['price']) . ' ' . __('each', 'trip-kailash')); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<form class="tk-reserve__form" method="post" data-tk-enquiry>
		<?php wp_nonce_field('tk_contact_form', 'tk_contact_nonce'); ?>
		<input type="hidden" name="action" value="tk_submit_contact_form">
		<input type="hidden" name="form_context" value="package">
		<input type="hidden" name="package_interest" value="<?php echo esc_attr(get_the_title($tk_id)); ?>">

		<?php /* Honeypot. Reachable by a bot filling every field, out of the way
		         of anyone using a keyboard or a screen reader. */ ?>
		<p class="tk-hp" aria-hidden="true">
			<label for="tk-url-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('Leave this empty', 'trip-kailash'); ?></label>
			<input id="tk-url-<?php echo esc_attr($tk_uid); ?>" type="text" name="tk_website_url" tabindex="-1" autocomplete="off">
		</p>

		<?php if ('fixed' === $tk_type && !empty($tk_departures)) : ?>
			<p class="tk-field">
				<label for="tk-dep-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('Which departure', 'trip-kailash'); ?></label>
				<select id="tk-dep-<?php echo esc_attr($tk_uid); ?>" name="travel_dates">
					<?php foreach ($tk_departures as $tk_departure) :
						if (empty($tk_departure['date'])) {
							continue;
						}
						$tk_left  = isset($tk_departure['seats_left']) ? (int) $tk_departure['seats_left'] : -1;
						$tk_label = $tk_departure['date'];

						if ($tk_left > 0) {
							$tk_label .= sprintf(__(' (%d places left)', 'trip-kailash'), $tk_left);
						} elseif (0 === $tk_left) {
							$tk_label .= __(' (full)', 'trip-kailash');
						}
						?>
						<option value="<?php echo esc_attr($tk_departure['date']); ?>"<?php echo 0 === $tk_left ? ' disabled' : ''; ?>>
							<?php echo esc_html($tk_label); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php else : ?>
			<p class="tk-field">
				<label for="tk-when-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('When would you like to travel', 'trip-kailash'); ?></label>
				<input id="tk-when-<?php echo esc_attr($tk_uid); ?>" type="text" name="travel_dates"
					placeholder="<?php esc_attr_e('A month, a festival, or a date', 'trip-kailash'); ?>">
				<?php if ($tk_lead) : ?>
					<span class="tk-field__help"><?php echo esc_html($tk_lead); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<p class="tk-field">
			<label for="tk-pax-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('How many pilgrims', 'trip-kailash'); ?></label>
			<input id="tk-pax-<?php echo esc_attr($tk_uid); ?>" type="number" name="group_size" min="1" value="2"
				<?php echo $tk_max ? 'max="' . esc_attr($tk_max) . '"' : ''; ?>
				inputmode="numeric" data-tk-pax
				data-base-price="<?php echo esc_attr((float) $tk_price); ?>"
				data-tiers="<?php echo esc_attr(wp_json_encode(array_values((array) $tk_tiers))); ?>">
		</p>

		<?php if (tk_package_has('price_from', $tk_id)) : ?>
			<p class="tk-reserve__total" data-tk-total role="status"
				data-deposit="<?php echo esc_attr($tk_deposit); ?>"
				data-label-total="<?php echo esc_attr__('Estimated total', 'trip-kailash'); ?>"
				data-label-deposit="<?php echo esc_attr__('deposit to hold your places', 'trip-kailash'); ?>"></p>
		<?php endif; ?>

		<p class="tk-field">
			<label for="tk-name-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('Your name', 'trip-kailash'); ?></label>
			<input id="tk-name-<?php echo esc_attr($tk_uid); ?>" type="text" name="name" autocomplete="name" required>
		</p>

		<p class="tk-field">
			<label for="tk-email-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('Email', 'trip-kailash'); ?></label>
			<input id="tk-email-<?php echo esc_attr($tk_uid); ?>" type="email" name="email" autocomplete="email" required>
		</p>

		<p class="tk-field">
			<label for="tk-phone-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('WhatsApp or phone', 'trip-kailash'); ?></label>
			<input id="tk-phone-<?php echo esc_attr($tk_uid); ?>" type="tel" name="phone" autocomplete="tel">
		</p>

		<p class="tk-field">
			<label for="tk-msg-<?php echo esc_attr($tk_uid); ?>"><?php esc_html_e('Anything we should know', 'trip-kailash'); ?></label>
			<textarea id="tk-msg-<?php echo esc_attr($tk_uid); ?>" name="message" rows="3"
				placeholder="<?php esc_attr_e('Anyone over 65, or managing a health condition?', 'trip-kailash'); ?>"></textarea>
		</p>

		<button type="submit" class="tk-btn tk-reserve__submit"><?php esc_html_e('Send enquiry', 'trip-kailash'); ?></button>

		<p class="tk-reserve__reassure">
			<?php esc_html_e('Nothing is charged now, and no place is held until you say yes to the invoice.', 'trip-kailash'); ?>
		</p>

		<p class="tk-reserve__result" data-tk-result role="status" aria-live="polite"></p>
	</form>

	<?php get_template_part('template-parts/package/money'); ?>
</div>
