<?php
/**
 * Lodges Widget
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Lodges extends Widget_Base {

    public function get_name() {
        return 'tk-lodges';
    }

    public function get_title() {
        return __('Lodges', 'trip-kailash');
    }

    public function get_icon() {
        return 'eicon-image-box';
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
                'default' => __('Our Lodges', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'number_of_lodges',
            [
                'label' => __('Number of Lodges', 'trip-kailash'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 12,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'lodge',
            'posts_per_page' => $settings['number_of_lodges'],
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query($args);

        ?>
        <section class="tk-lodges">
            <?php if ($settings['section_title']): ?>
                <h2 class="tk-section-title"><?php echo esc_html($settings['section_title']); ?></h2>
            <?php endif; ?>
            
            <div class="tk-lodges-grid tk-grid-3">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $lodge_id = get_the_ID();
                        $this->render_lodge_card($lodge_id);
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>' . __('No lodges found.', 'trip-kailash') . '</p>';
                }
                ?>
            </div>
        </section>
        <?php
    }

    private function render_lodge_card($lodge_id) {
        $location = get_post_meta($lodge_id, 'location', true);
        $amenities = get_post_meta($lodge_id, 'amenities', true);
        
        $photo = get_the_post_thumbnail_url($lodge_id, 'large');
        if (!$photo) {
            $photo = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }

        if (!is_array($amenities)) {
            $amenities = [];
        }

        ?>
        <div class="tk-lodge">
            <div class="tk-lodge__photo">
                <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
            </div>
            <div class="tk-lodge__content">
                <h3 class="tk-lodge__name"><?php echo esc_html(get_the_title()); ?></h3>
                <?php if ($location): ?>
                    <p class="tk-lodge__location">📍 <?php echo esc_html($location); ?></p>
                <?php endif; ?>
                <?php if (!empty($amenities)): ?>
                    <ul class="tk-lodge__amenities">
                        <?php foreach ($amenities as $amenity): ?>
                            <li>✓ <?php echo esc_html($amenity); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
