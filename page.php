<?php
/**
 * Template for static pages
 *
 * Pages previously fell through to index.php, which prints the_content()
 * with no title, no width constraint and no styling wrapper, so anything
 * that was not an Elementor layout rendered full-bleed and unstyled.
 *
 * Elementor-built pages keep rendering exactly as before: when Elementor
 * owns the document it takes over the content, and the title band below is
 * suppressed so the page's own hero is the first thing on screen.
 *
 * @package TripKailash
 * @since 1.0.3
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$tk_is_elementor_page = false;

if (class_exists('\Elementor\Plugin') && is_singular()) {
    $tk_is_elementor_page = \Elementor\Plugin::$instance->documents
        ->get(get_the_ID())
        ->is_built_with_elementor();
}
?>

<main id="main" class="site-main tk-page">
    <?php if (!$tk_is_elementor_page) : ?>
        <header class="tk-page__banner">
            <div class="tk-container">
                <h1 class="tk-page__title"><?php the_title(); ?></h1>
            </div>
        </header>
    <?php endif; ?>

    <?php
    while (have_posts()) :
        the_post();

        if ($tk_is_elementor_page) {
            the_content();
        } else {
            ?>
            <div class="tk-page__body">
                <div class="tk-container tk-container--reading">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php
        }
    endwhile;
    ?>
</main>

<?php
get_footer();
