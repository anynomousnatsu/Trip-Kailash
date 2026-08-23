<?php
/**
 * Check us before you pay us
 *
 * The highest-value section the customer research produced, and the one no
 * competitor will copy.
 *
 * Fraud is the number one objection in this category and it is earned: an
 * operator in Hyderabad took over fifteen lakh rupees from seven pilgrims and
 * never ran their trip, and fake credentials circulate by email. The advice
 * written for buyers is explicit that a properly registered agency ACTIVELY
 * ENCOURAGES verification rather than asking you to take its word.
 *
 * Both Nepali registers are public. So this section does not display numbers,
 * it hands over the means to check them.
 *
 * Rows with no number are not rendered. If nothing at all is set, the section
 * does not appear: a verification table full of placeholders would do more
 * damage than having no verification table, because the one thing this
 * section cannot survive is a number that does not check out.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_rows = array(
    array(
        'what'  => __('TAAN membership', 'trip-kailash'),
        'value' => trim((string) get_theme_mod('tk_taan_number', '')),
        'where' => __('Searchable by name or membership number at taan.org.np', 'trip-kailash'),
        'url'   => 'https://www.taan.org.np/members',
    ),
    array(
        'what'  => __('Nepal Tourism Board licence', 'trip-kailash'),
        'value' => trim((string) get_theme_mod('tk_ntb_number', '')),
        'where' => __('Nepal Tourism Board, Kathmandu', 'trip-kailash'),
        'url'   => 'https://ntb.gov.np/',
    ),
    array(
        'what'  => __('Company registration', 'trip-kailash'),
        'value' => trim((string) get_theme_mod('tk_company_reg', '')),
        'where' => __('Office of the Company Registrar', 'trip-kailash'),
        'url'   => '',
    ),
);

$tk_rows = array_values(array_filter($tk_rows, function ($row) {
    return '' !== $row['value'];
}));

$tk_office = trim((string) get_theme_mod('tk_office_address', ''));

if (empty($tk_rows) && '' === $tk_office) {
    return;
}
?>

<section class="tk-section tk-verify" id="verify" aria-labelledby="tk-verify-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Before you send money', 'trip-kailash'),
			__('Check us before you pay us', 'trip-kailash'),
			array('id' => 'tk-verify-title')
		);
		?>

		<div class="tk-verify__lede tk-rv">
			<p><?php esc_html_e('You are about to wire money to a company in another country, for a trip you cannot inspect, on the strength of a website. You should be suspicious. People in this business have taken deposits and disappeared.', 'trip-kailash'); ?></p>
			<p><?php esc_html_e('So here is everything you need to check us, and the places to check it. An operator who will not point you at these is not one you should pay.', 'trip-kailash'); ?></p>
		</div>

		<?php if ($tk_rows) : ?>
			<ul class="tk-verify__list tk-stagger">
				<?php foreach ($tk_rows as $tk_row) : ?>
					<li class="tk-verify__row tk-rv">
						<span class="tk-verify__what"><?php echo esc_html($tk_row['what']); ?></span>
						<span class="tk-verify__value tk-num"><?php echo esc_html($tk_row['value']); ?></span>
						<span class="tk-verify__where">
							<?php if ($tk_row['url']) : ?>
								<a href="<?php echo esc_url($tk_row['url']); ?>" rel="noopener" target="_blank">
									<?php echo esc_html($tk_row['where']); ?>
									<span class="tk-sr-only"><?php esc_html_e('(opens in a new tab)', 'trip-kailash'); ?></span>
								</a>
							<?php else : ?>
								<?php echo esc_html($tk_row['where']); ?>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ($tk_office) : ?>
			<p class="tk-verify__office tk-rv">
				<?php esc_html_e('Our office, which is a real address and not a contact form:', 'trip-kailash'); ?>
				<strong><?php echo esc_html($tk_office); ?></strong>
			</p>
		<?php endif; ?>

		<p class="tk-verify__close tk-rv">
			<?php esc_html_e('If anything here does not check out, do not travel with us. Tell us what you found and we will fix it.', 'trip-kailash'); ?>
		</p>
	</div>
</section>
