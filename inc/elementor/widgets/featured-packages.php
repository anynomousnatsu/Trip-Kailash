<?php
/**
 * Featured Packages Widget
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Featured_Packages extends Widget_Base {

    public function get_name() {
        return 'tk-featured-packages';
    }

    public function get_title() {
        return __('Featured Packages', 'trip-kailash');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['trip-kailash'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label' => __('Section Title', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Featured Packages', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'section_subtitle',
            [
                'label' => __('Section Subtitle', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'rows' => 3,
            ]
        );

        $this->add_control(
            'number_of_packages',
            [
                'label' => __('Number of Packages', 'trip-kailash'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 12,
            ]
        );

        $deity_options = [
            'all'   => __('All', 'trip-kailash'),
            'shiva' => __('Shiva', 'trip-kailash'),
            'vishnu' => __('Vishnu', 'trip-kailash'),
            'devi' => __('Devi', 'trip-kailash'),
        ];

        $deity_terms = get_terms([
            'taxonomy'   => 'deity',
            'hide_empty' => false,
        ]);

        if (!is_wp_error($deity_terms) && !empty($deity_terms)) {
            $deity_options = [
                'all' => __('All', 'trip-kailash'),
            ];
            foreach ($deity_terms as $term) {
                $deity_options[$term->slug] = $term->name;
            }
        }

        $this->add_control(
            'deity_filter',
            [
                'label' => __('Filter by Deity', 'trip-kailash'),
                'type' => Controls_Manager::SELECT,
                'options' => $deity_options,
                'default' => 'all',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order By', 'trip-kailash'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'date' => __('Date', 'trip-kailash'),
                    'title' => __('Title', 'trip-kailash'),
                    'meta_value_num' => __('Price', 'trip-kailash'),
                ],
                'default' => 'date',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'trip-kailash'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'DESC' => __('Descending', 'trip-kailash'),
                    'ASC' => __('Ascending', 'trip-kailash'),
                ],
                'default' => 'DESC',
            ]
        );

        $this->add_control(
            'view_all_link_text',
            [
                'label' => __('View All Link Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View All Sacred Journeys', 'trip-kailash'),
                'placeholder' => __('Enter link text', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'view_all_custom_url',
            [
                'label' => __('View All Custom URL', 'trip-kailash'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'trip-kailash'),
                'description' => __('Leave empty to use the default pilgrimage packages archive page', 'trip-kailash'),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'pilgrimage_package',
            'posts_per_page' => $settings['number_of_packages'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
        ];

        if ($settings['order_by'] === 'meta_value_num') {
            $args['meta_key'] = 'price_from';
        }

        if ($settings['deity_filter'] !== 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'deity',
                    'field' => 'slug',
                    'terms' => $settings['deity_filter'],
                ],
            ];
        }

        $query = new \WP_Query($args);

        ?>
        <section class="tk-deity-section tk-featured-packages">
            <?php if (!empty($settings['section_title']) || !empty($settings['section_subtitle'])) : ?>
                <div class="tk-deity-section__header">
                    <?php if (!empty($settings['section_title'])) : ?>
                        <h2 class="tk-section-title"><?php echo esc_html($settings['section_title']); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($settings['section_subtitle'])) : ?>
                        <p class="tk-section-subtitle"><?php echo esc_html($settings['section_subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="tk-packages-grid tk-grid-3">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $package_id = get_the_ID();
                        $this->render_package_card($package_id);
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>' . __('No packages found.', 'trip-kailash') . '</p>';
                }
                ?>
            </div>

            <div class="tk-featured-packages__view-all">
                <?php
                // Get custom URL settings
                $view_all_url = !empty($settings['view_all_custom_url']['url']) 
                    ? $settings['view_all_custom_url']['url'] 
                    : get_post_type_archive_link('pilgrimage_package');
                
                $view_all_text = !empty($settings['view_all_link_text']) 
                    ? $settings['view_all_link_text'] 
                    : __('View All Sacred Journeys', 'trip-kailash');
                
                $target = !empty($settings['view_all_custom_url']['is_external']) ? ' target="_blank"' : '';
                $nofollow = !empty($settings['view_all_custom_url']['nofollow']) ? ' rel="nofollow"' : '';
                ?>
                <a href="<?php echo esc_url($view_all_url); ?>" class="tk-featured-packages__view-all-link"<?php echo $target . $nofollow; ?>>
                    <?php echo esc_html($view_all_text); ?>
                </a>
            </div>
        </section>
        <?php
    }

    private function render_package_card($package_id) {
        $trip_length = get_post_meta($package_id, 'trip_length', true);
        $has_lodge = get_post_meta($package_id, 'has_lodge', true);
        $has_helicopter = get_post_meta($package_id, 'has_helicopter', true);
        $includes_meals = get_post_meta($package_id, 'includes_meals', true);
        $includes_rituals = get_post_meta($package_id, 'includes_rituals', true);
        $key_stops = get_post_meta($package_id, 'key_stops', true);
        $difficulty = get_post_meta($package_id, 'difficulty', true);
        $best_months = get_post_meta($package_id, 'best_months', true);
        $price_from = (int) get_post_meta($package_id, 'price_from', true);

        $featured_image = get_the_post_thumbnail_url($package_id, 'large');
        if (!$featured_image) {
            $featured_image = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }

        if (!is_array($key_stops)) {
            $key_stops = [];
        }

        $difficulty_slug = '';
        if (!empty($difficulty)) {
            $difficulty_slug = strtolower(preg_replace('/[^a-z]+/', '-', $difficulty));
        }

        $difficulty_class = $difficulty_slug ? ' tk-package-card--difficulty-' . $difficulty_slug : '';
        $has_price = $price_from > 0;

        ?>
        <article class="tk-package-card tk-package-card--featured<?php echo esc_attr($difficulty_class); ?>" data-package-id="<?php echo esc_attr($package_id); ?>" data-package-url="<?php echo esc_url(get_permalink($package_id)); ?>">
            <div class="tk-package-card__image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                <?php if ($difficulty): ?>
                    <div class="tk-package-card__badge-wrapper">
                        <span class="tk-package-card__badge tk-package-card__badge--difficulty tk-package-card__badge--<?php echo esc_attr($difficulty_slug); ?>">
                            <?php echo esc_html($difficulty); ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div class="tk-package-card__image-overlay">
                    <h3 class="tk-package-card__title"><?php echo esc_html(get_the_title()); ?></h3>
                    <?php if ($trip_length): ?>
                        <p class="tk-package-card__duration"><?php echo esc_html($trip_length); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tk-package-card__content tk-package-card__content--featured">
                <div class="tk-package-card__icons">
                    <span class="tk-icon <?php echo $has_lodge ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $has_lodge ? __('Lodge Included', 'trip-kailash') : __('No Lodge', 'trip-kailash'); ?>">🛏️</span>
                    <span class="tk-icon <?php echo $has_helicopter ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $has_helicopter ? __('Helicopter Available', 'trip-kailash') : __('No Helicopter', 'trip-kailash'); ?>">🚁</span>
                    <span class="tk-icon <?php echo $includes_meals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $includes_meals ? __('Meals Included', 'trip-kailash') : __('No Meals', 'trip-kailash'); ?>">🍛</span>
                    <span class="tk-icon <?php echo $includes_rituals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $includes_rituals ? __('Rituals Included', 'trip-kailash') : __('No Rituals', 'trip-kailash'); ?>">🙏</span>
                </div>
                <?php if ($has_price): ?>
                    <div class="tk-package-card__price-row">
                        <span class="tk-package-card__price-label"><?php esc_html_e('From', 'trip-kailash'); ?></span>
                        <span class="tk-package-card__price">$<?php echo esc_html(number_format_i18n($price_from)); ?></span>
                        <span class="tk-package-card__price-note"><?php esc_html_e('per person', 'trip-kailash'); ?></span>
                    </div>
                <?php endif; ?>
                <div class="tk-package-card__cta-row">
                    <span class="tk-package-card__cta-text"><?php esc_html_e('Touch for Details', 'trip-kailash'); ?></span>
                </div>
            </div>
        </article>
        <?php
    }
}
