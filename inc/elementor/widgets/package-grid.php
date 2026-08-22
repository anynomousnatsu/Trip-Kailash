<?php
/**
 * Package Grid Widget
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Package_Grid extends Widget_Base {

    public function get_name() {
        return 'tk-package-grid';
    }

    public function get_title() {
        return __('Package Grid', 'trip-kailash');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
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
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'trip-kailash'),
                'type' => Controls_Manager::NUMBER,
                'default' => 9,
                'min' => 1,
                'max' => 24,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'trip-kailash'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '2' => __('2 Columns', 'trip-kailash'),
                    '3' => __('3 Columns', 'trip-kailash'),
                    '4' => __('4 Columns', 'trip-kailash'),
                ],
                'default' => '3',
            ]
        );

        $this->add_control(
            'enable_ajax',
            [
                'label' => __('Enable AJAX Filtering', 'trip-kailash'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'trip-kailash'),
                'label_off' => __('No', 'trip-kailash'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'pilgrimage_package',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => 'date',
            'order' => 'DESC',
        ];

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

        $grid_class = 'tk-grid-' . $settings['columns'];
        $ajax_enabled = $settings['enable_ajax'] === 'yes' ? 'true' : 'false';

        ?>
        <section class="tk-package-grid-widget" 
                 data-deity="<?php echo esc_attr($settings['deity_filter']); ?>"
                 data-posts-per-page="<?php echo esc_attr($settings['posts_per_page']); ?>"
                 data-ajax-enabled="<?php echo esc_attr($ajax_enabled); ?>">
            
            <div class="tk-packages-grid <?php echo esc_attr($grid_class); ?>">
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

        $featured_image = get_the_post_thumbnail_url($package_id, 'large');
        if (!$featured_image) {
            $featured_image = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }

        if (!is_array($key_stops)) {
            $key_stops = [];
        }

        ?>
        <article class="tk-package-card tk-package-card--with-stops" data-package-id="<?php echo esc_attr($package_id); ?>" data-package-url="<?php echo esc_url(get_permalink($package_id)); ?>">
            <div class="tk-package-card__image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
            </div>
            <div class="tk-package-card__content">
                <h3 class="tk-package-card__title"><?php echo esc_html(get_the_title()); ?></h3>
                <?php if ($trip_length): ?>
                    <p class="tk-package-card__duration"><?php echo esc_html($trip_length); ?></p>
                <?php endif; ?>
                <div class="tk-package-card__icons">
                    <span class="tk-icon <?php echo $has_lodge ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🛏️</span>
                    <span class="tk-icon <?php echo $has_helicopter ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🚁</span>
                    <span class="tk-icon <?php echo $includes_meals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🍛</span>
                    <span class="tk-icon <?php echo $includes_rituals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🙏</span>
                </div>
                <?php if (!empty($key_stops)): ?>
                    <div class="tk-package-card__key-stops">
                        <h4><?php _e('Key Stops', 'trip-kailash'); ?></h4>
                        <ul>
                            <?php foreach (array_slice($key_stops, 0, 3) as $stop): ?>
                                <li><?php echo esc_html($stop); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
