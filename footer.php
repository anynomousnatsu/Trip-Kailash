<?php
/**
 * The footer
 *
 * Night ground, brass links, and the credentials with an invitation to check
 * them rather than a claim to be trusted.
 *
 * Every number reads from the Customizer and is omitted when unset. The old
 * footer printed "IATA License: TK92456" on every page of the site. IATA
 * accreditation is for airline ticketing agents, which is not what this
 * business does, and on a site whose central argument is to go and verify our
 * registration, one number that does not check out discredits the rest. It is
 * not reproduced here; if it is genuine it belongs in the Customizer with the
 * others.
 *
 * @package TripKailash
 * @since 1.1.0
 */

$tk_legal = array(
    __('Company reg.', 'trip-kailash')     => trim((string) get_theme_mod('tk_company_reg', '')),
    __('Tourism licence', 'trip-kailash')  => trim((string) get_theme_mod('tk_ntb_number', '')),
    __('TAAN member', 'trip-kailash')      => trim((string) get_theme_mod('tk_taan_number', '')),
);

$tk_legal = array_filter($tk_legal);
$tk_whatsapp = trim((string) get_theme_mod('tk_whatsapp_number', ''));
?>

<footer class="tk-footer tk-deep">
	<div class="tk-wrap">

		<div class="tk-footer__grid">

			<div class="tk-footer__brand">
				<?php if (function_exists('trip_kailash_site_logo')) { trip_kailash_site_logo(); } ?>
				<p class="tk-footer__tagline">
					<?php esc_html_e('Pilgrimage journeys in Nepal and Tibet. The pilgrimage arm of Trekmania Nepal.', 'trip-kailash'); ?>
				</p>
			</div>

			<div class="tk-footer__col">
				<h2 class="tk-footer__head"><?php esc_html_e('Yatras', 'trip-kailash'); ?></h2>
				<ul class="tk-footer__list">
					<?php
					$tk_footer_packages = get_posts(array(
						'post_type'        => 'pilgrimage_package',
						'post_status'      => 'publish',
						'numberposts'      => 6,
						'orderby'          => 'menu_order',
						'order'            => 'ASC',
						'suppress_filters' => false,
					));

					foreach ($tk_footer_packages as $tk_footer_package) :
						?>
						<li><a href="<?php echo esc_url(get_permalink($tk_footer_package)); ?>"><?php echo esc_html(get_the_title($tk_footer_package)); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url(get_post_type_archive_link('pilgrimage_package')); ?>"><?php esc_html_e('All yatras', 'trip-kailash'); ?></a></li>
				</ul>
			</div>

			<div class="tk-footer__col">
				<h2 class="tk-footer__head"><?php esc_html_e('Company', 'trip-kailash'); ?></h2>
				<ul class="tk-footer__list">
					<li><a href="<?php echo esc_url(home_url('/about-us')); ?>"><?php esc_html_e('About us', 'trip-kailash'); ?></a></li>
					<li><a href="<?php echo esc_url(home_url('/contact-us')); ?>"><?php esc_html_e('Contact us', 'trip-kailash'); ?></a></li>
					<li><a href="<?php echo esc_url(home_url('/#verify')); ?>"><?php esc_html_e('Check our credentials', 'trip-kailash'); ?></a></li>
					<li><a href="<?php echo esc_url(home_url('/book-yatra')); ?>"><?php esc_html_e('Book a yatra', 'trip-kailash'); ?></a></li>
				</ul>
			</div>

			<div class="tk-footer__col">
				<h2 class="tk-footer__head"><?php esc_html_e('Contact', 'trip-kailash'); ?></h2>
				<ul class="tk-footer__list">
					<li><?php esc_html_e('Kathmandu, Nepal', 'trip-kailash'); ?></li>
					<?php if ($tk_whatsapp) : ?>
						<li><a href="<?php echo esc_url('https://wa.me/' . preg_replace('/[^0-9]/', '', $tk_whatsapp)); ?>" rel="noopener"><?php esc_html_e('WhatsApp', 'trip-kailash'); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url('mailto:' . antispambot(TK_FORM_RECIPIENT)); ?>"><?php echo esc_html(antispambot(TK_FORM_RECIPIENT)); ?></a></li>
				</ul>
			</div>
		</div>

		<div class="tk-footer__bottom">
			<p class="tk-footer__legal">
				<?php if ($tk_legal) : ?>
					<?php
					$tk_parts = array();

					foreach ($tk_legal as $tk_label => $tk_value) {
						$tk_parts[] = $tk_label . ' ' . $tk_value;
					}

					echo esc_html(implode(' · ', $tk_parts));
					?>
					<a class="tk-footer__verify" href="<?php echo esc_url(home_url('/#verify')); ?>"><?php esc_html_e('Verify all of these', 'trip-kailash'); ?></a>
				<?php endif; ?>
			</p>

			<p class="tk-footer__copy">
				<?php
				printf(
					/* translators: %s: current year. */
					esc_html__('Copyright %s Trip Kailash', 'trip-kailash'),
					esc_html(wp_date('Y'))
				);
				?>
			</p>
		</div>
	</div>
</footer>

<?php if ($tk_whatsapp) : ?>
	<?php /* Phones only. It is standard for this market and it works, but on a
	         desktop it fights the calm the rest of the page is built on. */ ?>
	<a class="tk-whatsapp-bar" rel="noopener"
		href="<?php echo esc_url('https://wa.me/' . preg_replace('/[^0-9]/', '', $tk_whatsapp)); ?>">
		<?php esc_html_e('Ask us on WhatsApp', 'trip-kailash'); ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>

</html>
