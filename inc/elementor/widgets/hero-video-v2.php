<?php
/**
 * Hero Video Widget (New)
 *
 * Clean implementation using existing Trip Kailash hero styles.
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Hero_Video_V2 extends Widget_Base
{

    public function get_name()
    {
        return 'tk-hero-video-v2';
    }

    public function get_title()
    {
        return __('Hero Video (New)', 'trip-kailash');
    }

    public function get_icon()
    {
        return 'eicon-video-camera';
    }

    public function get_categories()
    {
        return ['trip-kailash'];
    }

    protected function register_controls()
    {
        // Content section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_source',
            [
                'label' => __('Video Source', 'trip-kailash'),
                'type' => Controls_Manager::MEDIA,
                'media_types' => ['video'],
                'default' => ['url' => ''],
                'description' => __('Export at 1920x1080 or smaller. A 4K hero clip is tens of megabytes and is downloaded in full before the page can paint.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'video_poster',
            [
                'label' => __('Poster Image', 'trip-kailash'),
                'type' => Controls_Manager::MEDIA,
                'media_types' => ['image'],
                'default' => ['url' => ''],
                'description' => __('Shown immediately while the video loads, and shown instead of the video on phones and data-saver connections. Use the first frame of the clip.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'show_trishul',
            [
                'label' => __('Show Trishul Icon', 'trip-kailash'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'trip-kailash'),
                'label_off' => __('Hide', 'trip-kailash'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Walk the Path of Gods', 'trip-kailash'),
                'placeholder' => __('Enter your heading', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'subheading',
            [
                'label' => __('Subheading', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Experience sacred pilgrimages to the most revered temples and holy sites', 'trip-kailash'),
                'rows' => 3,
            ]
        );

        $this->end_controls_section();

        // CTA buttons
        $this->start_controls_section(
            'cta_buttons_section',
            [
                'label' => __('CTA Buttons', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'primary_button_text',
            [
                'label' => __('Primary Button Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Begin Your Yatra', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'primary_button_link',
            [
                'label' => __('Primary Button Link', 'trip-kailash'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'trip-kailash'),
                'default' => ['url' => '#'],
            ]
        );

        $this->add_control(
            'secondary_button_text',
            [
                'label' => __('Secondary Button Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Speak with Tirtha Purohit', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'secondary_button_link',
            [
                'label' => __('Secondary Button Link', 'trip-kailash'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'trip-kailash'),
                'default' => ['url' => '#'],
            ]
        );

        $this->end_controls_section();

        // Scroll section
        $this->start_controls_section(
            'scroll_section',
            [
                'label' => __('Scroll Indicator', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_scroll_indicator',
            [
                'label' => __('Show Scroll Indicator', 'trip-kailash'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'trip-kailash'),
                'label_off' => __('Hide', 'trip-kailash'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'scroll_text',
            [
                'label' => __('Scroll Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('DISCOVER SACRED JOURNEYS', 'trip-kailash'),
                'condition' => ['show_scroll_indicator' => 'yes'],
            ]
        );

        $this->end_controls_section();

        // Style section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => __('Overlay Opacity', 'trip-kailash'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => ['min' => 0, 'max' => 100, 'step' => 5],
                ],
                'default' => ['unit' => '%', 'size' => 40],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $video_url = '';
        if (!empty($settings['video_source']) && is_array($settings['video_source']) && !empty($settings['video_source']['url'])) {
            $video_url = esc_url($settings['video_source']['url']);
        }

        $poster_url = '';
        if (!empty($settings['video_poster']) && is_array($settings['video_poster']) && !empty($settings['video_poster']['url'])) {
            $poster_url = esc_url($settings['video_poster']['url']);
        }

        $show_trishul = !empty($settings['show_trishul']) && $settings['show_trishul'] === 'yes';
        $heading = !empty($settings['heading']) ? $settings['heading'] : '';
        $subheading = !empty($settings['subheading']) ? $settings['subheading'] : '';

        $primary_btn_text = !empty($settings['primary_button_text']) ? $settings['primary_button_text'] : '';
        $primary_btn_link = '#';
        if (!empty($settings['primary_button_link']) && is_array($settings['primary_button_link']) && !empty($settings['primary_button_link']['url'])) {
            $primary_btn_link = esc_url($settings['primary_button_link']['url']);
        }

        $secondary_btn_text = !empty($settings['secondary_button_text']) ? $settings['secondary_button_text'] : '';
        $secondary_btn_link = '#';
        if (!empty($settings['secondary_button_link']) && is_array($settings['secondary_button_link']) && !empty($settings['secondary_button_link']['url'])) {
            $secondary_btn_link = esc_url($settings['secondary_button_link']['url']);
        }

        $show_scroll = !empty($settings['show_scroll_indicator']) && $settings['show_scroll_indicator'] === 'yes';
        $scroll_text = !empty($settings['scroll_text']) ? $settings['scroll_text'] : '';

        $overlay_opacity = 40.0;
        if (isset($settings['overlay_opacity']) && is_array($settings['overlay_opacity']) && isset($settings['overlay_opacity']['size']) && is_numeric($settings['overlay_opacity']['size'])) {
            $overlay_opacity = max(0, min(100, (float) $settings['overlay_opacity']['size']));
        }

        ?>
        <section class="tk-hero-video">
            <?php if ($video_url): ?>
                <?php
                /*
                 * The source is deliberately NOT rendered as a src attribute.
                 *
                 * autoplay makes the browser ignore preload hints and fetch the
                 * whole file before first paint. The hero clip on this site is
                 * a 3840x2160 mp4 of roughly 27MB, which is over 90% of the
                 * homepage's total weight and is downloaded in full on a phone
                 * over mobile data before anything is visible.
                 *
                 * assets/js/video-controls.js attaches the source only when the
                 * viewport is wide enough for the detail to matter and the
                 * visitor has not asked to save data. Everyone else gets the
                 * poster image, which is the same first frame at a fraction of
                 * the bytes.
                 */
                ?>
                <video class="tk-hero-video__video" muted loop playsinline preload="none"
                    data-tk-video-src="<?php echo esc_url($video_url); ?>"
                    <?php if ($poster_url): ?>poster="<?php echo esc_url($poster_url); ?>" <?php endif; ?>></video>
            <?php elseif ($poster_url): ?>
                <img class="tk-hero-video__video" src="<?php echo esc_url($poster_url); ?>" alt=""
                    fetchpriority="high" decoding="async">
            <?php endif; ?>

            <div class="tk-hero-overlay" style="--overlay-opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;">
                <div class="tk-hero-content">
                    <?php if ($show_trishul): ?>
                        <div class="tk-hero-icon">
                            <svg width="40" height="60" viewBox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 2L20 58M20 2L12 10M20 2L28 10M8 12C8 12 12 8 20 8C28 8 32 12 32 12M20 8L20 2"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    <?php endif; ?>

                    <?php if ($heading): ?>
                        <h1 class="tk-hero-heading"><?php echo esc_html($heading); ?></h1>
                    <?php endif; ?>

                    <?php if ($subheading): ?>
                        <p class="tk-hero-subheading"><?php echo esc_html($subheading); ?></p>
                    <?php endif; ?>

                    <div class="tk-hero-buttons">
                        <?php if ($primary_btn_text): ?>
                            <a href="<?php echo $primary_btn_link; ?>"
                                class="tk-btn tk-btn-primary"><?php echo esc_html($primary_btn_text); ?></a>
                        <?php endif; ?>

                        <?php if ($secondary_btn_text): ?>
                            <a href="<?php echo $secondary_btn_link; ?>"
                                class="tk-btn tk-btn-secondary"><?php echo esc_html($secondary_btn_text); ?></a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($show_scroll && $scroll_text): ?>
                    <div class="tk-hero-scroll">
                        <span class="tk-hero-scroll-text"><?php echo esc_html($scroll_text); ?></span>
                        <svg class="tk-hero-scroll-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tk-hero-wave">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                    <path d="M0,64 C240,96 480,96 720,64 C960,32 1200,32 1440,64 L1440,120 L0,120 Z" fill="#f5f2ed" />
                </svg>
            </div>
        </section>
        <?php
    }
}
