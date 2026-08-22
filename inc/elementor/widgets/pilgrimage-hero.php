<?php

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Pilgrimage_Hero extends Widget_Base
{

    public function get_name()
    {
        return 'tk-pilgrimage-hero';
    }

    public function get_title()
    {
        return __('Pilgrimage Package Hero', 'trip-kailash');
    }

    public function get_icon()
    {
        return 'eicon-featured-image';
    }

    public function get_categories()
    {
        return ['trip-kailash'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Leave empty to use post title', 'trip-kailash'),
                'dynamic' => ['active' => true],
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => __('Subtitle', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => '',
                'placeholder' => __('Enter subtitle text', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'background_image',
            [
                'label' => __('Background Image', 'trip-kailash'),
                'type' => Controls_Manager::MEDIA,
                'default' => [],
                'description' => __('Leave empty to use featured image', 'trip-kailash'),
            ]
        );

        /*
         * The hero crops a wide photo into a short band with object-fit:cover,
         * which keeps the centre and throws away the top and bottom. On the
         * Muktinath page that discarded the temple itself. These two controls
         * move the kept region instead of the image, so the subject can be
         * brought into frame without re-cropping the file.
         */
        $this->add_control(
            'focal_x',
            [
                'label' => __('Image Focus: Horizontal (%)', 'trip-kailash'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['%' => ['min' => 0, 'max' => 100]],
                'default' => ['size' => 50, 'unit' => '%'],
                'description' => __('0 = show the left edge, 100 = show the right edge.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'focal_y',
            [
                'label' => __('Image Focus: Vertical (%)', 'trip-kailash'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['%' => ['min' => 0, 'max' => 100]],
                'default' => ['size' => 50, 'unit' => '%'],
                'description' => __('0 = show the top of the photo, 100 = show the bottom. Lower this to bring a temple or peak into frame.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => __('Overlay Opacity (%)', 'trip-kailash'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 40,
                    'unit' => '%',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $post_id = get_the_ID();

        // Get title
        $title = !empty($settings['title']) ? $settings['title'] : get_the_title($post_id);

        // Get subtitle
        $subtitle = $settings['subtitle'];

        // Get background image
        $bg_image = '';
        if (!empty($settings['background_image']['url'])) {
            $bg_image = $settings['background_image']['url'];
        } elseif ($post_id) {
            $bg_image = get_the_post_thumbnail_url($post_id, 'full');
        }

        // Get overlay opacity (will be used for gradient intensity)
        $overlay_opacity = $settings['overlay_opacity']['size'] / 100;

        // Focal point, clamped so a stray value cannot produce invalid CSS.
        $focal_x = isset($settings['focal_x']['size']) && is_numeric($settings['focal_x']['size'])
            ? max(0, min(100, (float) $settings['focal_x']['size']))
            : 50;
        $focal_y = isset($settings['focal_y']['size']) && is_numeric($settings['focal_y']['size'])
            ? max(0, min(100, (float) $settings['focal_y']['size']))
            : 50;

        ?>
        <section class="tk-pilgrimage-hero">
            <?php if ($bg_image): ?>
                <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>" class="tk-pilgrimage-hero__bg"
                    style="object-position: <?php echo esc_attr($focal_x . '% ' . $focal_y . '%'); ?>;"
                    fetchpriority="high" loading="eager">
            <?php endif; ?>
            <div class="tk-pilgrimage-hero__overlay" style="opacity: <?php echo esc_attr($overlay_opacity * 1.5); ?>;"></div>
            <div class="tk-pilgrimage-hero__content">
                <div class="tk-container">
                    <?php if ($title): ?>
                        <h1 class="tk-pilgrimage-hero__title"><?php echo esc_html($title); ?></h1>
                    <?php endif; ?>

                    <?php if ($subtitle): ?>
                        <p class="tk-pilgrimage-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <style>
            .tk-pilgrimage-hero {
                position: relative;
                min-height: 500px;
                display: flex;
                align-items: flex-end;
                overflow: hidden;
                /* Removed background-image styles */
            }

            /*
             * Scoped through the parent so this beats Elementor's
             * `.elementor img { height: auto }`, which is printed after the
             * theme's CSS and otherwise wins the tie, collapsing the hero
             * image to its intrinsic height instead of filling the band.
             */
            .tk-pilgrimage-hero .tk-pilgrimage-hero__bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                z-index: 0;
            }

            .tk-pilgrimage-hero__overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 70%, rgba(0, 0, 0, 0.6) 100%);
                z-index: 1;
            }

            .tk-pilgrimage-hero__content {
                position: relative;
                z-index: 2;
                width: 100%;
                color: #FFFFFF;
                padding: 40px 60px;
                text-align: left;
            }

            .tk-container {
                max-width: 1200px;
                margin: 0;
            }

            .tk-pilgrimage-hero__title {
                font-family: var(--tk-font-heading, sans-serif);
                font-size: 48px;
                font-weight: 700;
                margin: 0 0 12px 0;
                line-height: 1.2;
                color: #FFFFFF;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
                max-width: 900px;
            }

            .tk-pilgrimage-hero__subtitle {
                font-family: var(--tk-font-body, sans-serif);
                font-size: 16px;
                font-weight: 400;
                margin: 0;
                max-width: 700px;
                line-height: 1.6;
                color: #FFFFFF;
                text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
                opacity: 0.95;
            }

            @media (max-width: 1024px) {
                .tk-pilgrimage-hero__content {
                    padding: 30px 40px;
                }

                .tk-pilgrimage-hero__title {
                    font-size: 36px;
                }

                .tk-pilgrimage-hero__subtitle {
                    font-size: 15px;
                }
            }

            @media (max-width: 768px) {
                .tk-pilgrimage-hero {
                    min-height: 400px;
                }

                .tk-pilgrimage-hero__content {
                    padding: 24px 24px 32px;
                }

                .tk-pilgrimage-hero__title {
                    font-size: 28px;
                    margin-bottom: 8px;
                }

                .tk-pilgrimage-hero__subtitle {
                    font-size: 14px;
                }
            }
        </style>
        <?php
    }
}
