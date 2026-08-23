<?php
/**
 * Before you go
 *
 * This is what the guide post type is for, and it is the entire SEO surface.
 * "Muktinath yatra best time" and "Kailash permit requirements" outrank any
 * package name in search volume, and those searches feed the package pages.
 *
 * Real guides are used when they exist. The four below are the fallback, and
 * their subjects come straight out of the customer research rather than from
 * guessing: altitude handled badly, booking for a parent, permits and the
 * exact day money stops being refundable, and which year to walk the kora.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_guides = get_posts(array(
    'post_type'        => 'guide',
    'post_status'      => 'publish',
    'numberposts'      => 4,
    'suppress_filters' => false,
));

$tk_cards = array();

foreach ($tk_guides as $tk_guide) {
    $tk_cards[] = array(
        'title' => get_the_title($tk_guide),
        'body'  => get_the_excerpt($tk_guide),
        'url'   => get_permalink($tk_guide),
    );
}

/*
 * The guide post type is empty today. Rather than render nothing, or render
 * four dead links, the fallback cards point at the enquiry form: someone who
 * wants to know what altitude does to the body can simply ask us.
 */
if (empty($tk_cards)) {
    $tk_ask = home_url('/contact-us');

    $tk_cards = array(
        array(
            'title' => __('Altitude, honestly', 'trip-kailash'),
            'body'  => __('Most pilgrims prepare the spiritual side for months and treat the altitude as an afterthought. That is exactly why it catches people out.', 'trip-kailash'),
            'url'   => $tk_ask,
        ),
        array(
            'title' => __('Booking for your parents', 'trip-kailash'),
            'body'  => __('What the road journey actually involves, when the helicopter is the right answer rather than the easy one, and what to ask a doctor first.', 'trip-kailash'),
            'url'   => $tk_ask,
        ),
        array(
            'title' => __('Kailash permits explained', 'trip-kailash'),
            'body'  => __('Why Tibet permits take six weeks, what documents you need, and the exact day your money stops being refundable.', 'trip-kailash'),
            'url'   => $tk_ask,
        ),
        array(
            'title' => __('Which year to walk the kora', 'trip-kailash'),
            'body'  => __('Horse Years, Saga Dawa, and why the most auspicious year is not always the best year to travel.', 'trip-kailash'),
            'url'   => $tk_ask,
        ),
    );
}
?>

<section class="tk-section tk-guides" id="before" aria-labelledby="tk-guides-title">
	<div class="tk-wrap">
		<?php
		tk_section_head(
			__('Before you go', 'trip-kailash'),
			__('What pilgrims ask us first', 'trip-kailash'),
			array('id' => 'tk-guides-title')
		);
		?>

		<div class="tk-guide-cards tk-stagger">
			<?php foreach ($tk_cards as $tk_card) : ?>
				<a class="tk-guide-card tk-rv" href="<?php echo esc_url($tk_card['url']); ?>">
					<h3 class="tk-guide-card__title"><?php echo esc_html($tk_card['title']); ?></h3>
					<p class="tk-guide-card__body"><?php echo esc_html(wp_strip_all_tags($tk_card['body'])); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
