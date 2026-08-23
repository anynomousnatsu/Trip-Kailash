<?php
/**
 * Questions pilgrims ask
 *
 * Registered with the schema builder as well as rendered, so the answers can
 * appear in search results. Built on details and summary, so it needs no
 * JavaScript and the browser handles keyboard support.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$tk_faqs = tk_package('faq', get_the_ID());

if (empty($tk_faqs)) {
    return;
}

/* Hand the same questions to the schema output, so what a search engine sees
   and what a visitor reads cannot drift apart. */
if (function_exists('tk_register_page_faqs')) {
    $tk_pairs = array();

    foreach ($tk_faqs as $tk_faq) {
        if (!empty($tk_faq['question']) && !empty($tk_faq['answer'])) {
            $tk_pairs[] = array(
                'question' => $tk_faq['question'],
                'answer'   => $tk_faq['answer'],
            );
        }
    }

    if ($tk_pairs) {
        tk_register_page_faqs($tk_pairs);
    }
}
?>

<section class="tk-pk-section" id="faq" aria-labelledby="tk-faq-title">
	<?php
	tk_section_head(
		__('Questions', 'trip-kailash'),
		__('What pilgrims ask us', 'trip-kailash'),
		array('level' => 'h2', 'id' => 'tk-faq-title')
	);
	?>

	<div class="tk-faq">
		<?php foreach ($tk_faqs as $tk_faq) :
			if (empty($tk_faq['question'])) {
				continue;
			}
			?>
			<details class="tk-faq__item">
				<summary class="tk-faq__q"><?php echo esc_html($tk_faq['question']); ?></summary>
				<div class="tk-faq__a"><p><?php echo esc_html($tk_faq['answer']); ?></p></div>
			</details>
		<?php endforeach; ?>
	</div>
</section>
