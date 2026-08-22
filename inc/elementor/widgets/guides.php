<?php
/**
 * Guides Widget
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Guides extends Widget_Base {

    public function get_name() {
        return 'tk-guides';
    }

    public function get_title() {
        return __('Guides', 'trip-kailash');
    }

    public function get_icon() {
        return 'eicon-person';
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
                'default' => __('Our Expert Guides', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'number_of_guides',
            [
                'label' => __('Number of Guides', 'trip-kailash'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 1,
                'max' => 12,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'guide',
            'posts_per_page' => $settings['number_of_guides'],
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query($args);

        ?>
        <section class="tk-guides">
            <?php if ($settings['section_title']): ?>
                <h2 class="tk-section-title"><?php echo esc_html($settings['section_title']); ?></h2>
            <?php endif; ?>
            
            <div class="tk-guides-grid tk-grid-4">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $guide_id = get_the_ID();
                        $this->render_guide_card($guide_id);
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>' . __('No guides found.', 'trip-kailash') . '</p>';
                }
                ?>
            </div>
        </section>
        <?php
    }

    private function render_guide_card($guide_id) {
        $years_experience = get_post_meta($guide_id, 'years_of_experience', true);
        $short_bio = get_post_meta($guide_id, 'short_bio', true);
        
        $photo = get_the_post_thumbnail_url($guide_id, 'medium');
        if (!$photo) {
            $photo = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }

        ?>
        <div class="tk-guide">
            <div class="tk-guide__photo">
                <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
            </div>
            <div class="tk-guide__content">
                <h3 class="tk-guide__name"><?php echo esc_html(get_the_title()); ?></h3>
                <?php if ($years_experience): ?>
                    <p class="tk-guide__experience">
                        <?php printf(__('%d years experience', 'trip-kailash'), intval($years_experience)); ?>
                    </p>
                <?php endif; ?>
                <?php if ($short_bio): ?>
                    <p class="tk-guide__bio"><?php echo esc_html($short_bio); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
