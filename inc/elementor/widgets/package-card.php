<?php
/**
 * Package Card Widget
 * 
 * Displays a single package card with image, title, duration, amenity icons,
 * and key stops that reveal on hover.
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Package_Card extends Widget_Base {

    public function get_name() {
        return 'tk-package-card';
    }

    public function get_title() {
        return __('Package Card', 'trip-kailash');
    }

    public function get_icon() {
        return 'eicon-post-content';
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

        // Cached id => title map; see tk_get_package_options() in inc/helpers.php.
        $package_options = tk_get_package_options();

        $this->add_control(
            'package_id',
            [
                'label' => __('Select Package', 'trip-kailash'),
                'type' => Controls_Manager::SELECT,
                'options' => $package_options,
                'default' => !empty($package_options) ? array_key_first($package_options) : '',
            ]
        );

        $this->add_control(
            'show_key_stops',
            [
                'label' => __('Show Key Stops on Hover', 'trip-kailash'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'trip-kailash'),
                'label_off' => __('No', 'trip-kailash'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $package_id = $settings['package_id'];
        $show_key_stops = $settings['show_key_stops'] === 'yes';

        if (empty($package_id)) {
            echo '<p>' . __('Please select a package.', 'trip-kailash') . '</p>';
            return;
        }

        $package = get_post($package_id);
        if (!$package || $package->post_type !== 'pilgrimage_package') {
            echo '<p>' . __('Invalid package selected.', 'trip-kailash') . '</p>';
            return;
        }

        // Get package meta
        $trip_length = get_post_meta($package_id, 'trip_length', true);
        $has_lodge = get_post_meta($package_id, 'has_lodge', true);
        $has_helicopter = get_post_meta($package_id, 'has_helicopter', true);
        $includes_meals = get_post_meta($package_id, 'includes_meals', true);
        $includes_rituals = get_post_meta($package_id, 'includes_rituals', true);
        $key_stops = get_post_meta($package_id, 'key_stops', true);

        // Get featured image
        $featured_image = get_the_post_thumbnail_url($package_id, 'large');
        if (!$featured_image) {
            $featured_image = get_template_directory_uri() . '/assets/images/placeholder.jpg';
        }

        // Ensure key_stops is an array
        if (!is_array($key_stops)) {
            $key_stops = [];
        }

        ?>
        <article class="tk-package-card <?php echo $show_key_stops ? 'tk-package-card--with-stops' : ''; ?>" data-package-id="<?php echo esc_attr($package_id); ?>" data-package-url="<?php echo esc_url(get_permalink($package_id)); ?>">
            <div class="tk-package-card__image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($package->post_title); ?>" loading="lazy">
            </div>
            <div class="tk-package-card__content">
                <h3 class="tk-package-card__title"><?php echo esc_html($package->post_title); ?></h3>
                <?php if ($trip_length): ?>
                    <p class="tk-package-card__duration"><?php echo esc_html($trip_length); ?></p>
                <?php endif; ?>
                <div class="tk-package-card__icons">
                    <span class="tk-icon tk-icon--lodge <?php echo $has_lodge ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $has_lodge ? __('Lodge Included', 'trip-kailash') : __('No Lodge', 'trip-kailash'); ?>">
                        🛏️
                    </span>
                    <span class="tk-icon tk-icon--helicopter <?php echo $has_helicopter ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $has_helicopter ? __('Helicopter Available', 'trip-kailash') : __('No Helicopter', 'trip-kailash'); ?>">
                        🚁
                    </span>
                    <span class="tk-icon tk-icon--meals <?php echo $includes_meals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $includes_meals ? __('Meals Included', 'trip-kailash') : __('No Meals', 'trip-kailash'); ?>">
                        🍛
                    </span>
                    <span class="tk-icon tk-icon--rituals <?php echo $includes_rituals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>" title="<?php echo $includes_rituals ? __('Rituals Included', 'trip-kailash') : __('No Rituals', 'trip-kailash'); ?>">
                        🙏
                    </span>
                </div>
                <?php if ($show_key_stops && !empty($key_stops)): ?>
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
