<?php

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

class Pilgrimage_Overview extends Widget_Base
{

    public function get_name()
    {
        return 'tk-pilgrimage-overview';
    }

    public function get_title()
    {
        return __('Pilgrimage Overview', 'trip-kailash');
    }

    public function get_icon()
    {
        return 'eicon-info-circle';
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
            'section_title',
            [
                'label' => __('Section Title', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Overview', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => __('Description', 'trip-kailash'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Stats Cards Section
        $this->start_controls_section(
            'stats_section',
            [
                'label' => __('Stats Cards', 'trip-kailash'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'label',
            [
                'label' => __('Label', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Duration', 'trip-kailash'),
            ]
        );

        $repeater->add_control(
            'value',
            [
                'label' => __('Value', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('10 Days', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'stats',
            [
                'label' => __('Stats Cards', 'trip-kailash'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'label' => __('Duration', 'trip-kailash'),
                        'value' => __('10 Days', 'trip-kailash'),
                    ],
                    [
                        'label' => __('Difficulty', 'trip-kailash'),
                        'value' => __('Moderate', 'trip-kailash'),
                    ],
                    [
                        'label' => __('Altitude', 'trip-kailash'),
                        'value' => __('4,800m', 'trip-kailash'),
                    ],
                    [
                        'label' => __('Group Size', 'trip-kailash'),
                        'value' => __('2-15', 'trip-kailash'),
                    ],
                ],
                'title_field' => '{{{ label }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        ?>
        <section class="tk-pilgrimage-overview">
            <div class="tk-overview-box" data-version="v2">
                <?php if (!empty($settings['section_title'])): ?>
                    <div class="tk-overview-header">
                        <h2 class="tk-pilgrimage-overview__title"><?php echo esc_html($settings['section_title']); ?></h2>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['description'])): ?>
                    <div class="tk-overview-content" data-overview-content>
                        <div class="tk-overview-content-wrapper">
                            <div class="tk-pilgrimage-overview__description tk-overview-collapsible">
                                <?php echo wp_kses_post($settings['description']); ?>
                            </div>
                            <div class="tk-overview-fade"></div>
                        </div>
                        <button class="tk-overview-see-more" type="button">
                            <span class="tk-overview-see-more__text">See More</span>
                            <svg class="tk-overview-see-more__icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>

                <?php
                /*
                 * Drop blank repeater rows before deciding whether to render.
                 *
                 * !empty($settings['stats']) is true for a repeater holding a
                 * single row with an empty label and an empty value, which is
                 * what an untouched default leaves behind. That rendered a
                 * stat card with a background, padding and no content: the
                 * empty grey slab under the Tour Overview.
                 */
                $stats = array();

                if (!empty($settings['stats']) && is_array($settings['stats'])) {
                    foreach ($settings['stats'] as $stat) {
                        $label = isset($stat['label']) ? trim($stat['label']) : '';
                        $value = isset($stat['value']) ? trim($stat['value']) : '';

                        if ('' !== $label || '' !== $value) {
                            $stats[] = array('label' => $label, 'value' => $value);
                        }
                    }
                }
                ?>
                <?php if (!empty($stats)): ?>
                    <div class="tk-overview-stats-section">
                        <div class="tk-pilgrimage-overview__stats">
                            <?php foreach ($stats as $stat): ?>
                                <div class="tk-stat-card">
                                    <?php if ('' !== $stat['label']): ?>
                                        <div class="tk-stat-card__label"><?php echo esc_html($stat['label']); ?></div>
                                    <?php endif; ?>
                                    <?php if ('' !== $stat['value']): ?>
                                        <div class="tk-stat-card__value"><?php echo esc_html($stat['value']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <style>
            /* Overview Widget CSS v2.0 - Updated 2025-11-20 */
            /* Override Elementor widget padding */
            .elementor-widget-tk-pilgrimage-overview .elementor-widget-container {
                padding: 0 !important;
            }

            .tk-pilgrimage-overview {
                padding: 10px 0 10px 0;
                background: var(--tk-bg-main, #F5F2ED);
            }

            .tk-overview-box {
                max-width: var(--tk-max-width);
                margin-left: auto;
                margin-right: auto;
                background: #FFFFFF;
                border-radius: var(--tk-border-radius, 10px);
                box-shadow: var(--tk-shadow-card, 0 4px 20px rgba(44, 43, 40, 0.08));
                padding: 0 !important;
                overflow: hidden;
            }

            /* Header Section */
            .tk-overview-header {
                padding: 30px 55px 24px 55px !important;
                margin: 0 !important;
            }

            .tk-pilgrimage-overview__title {
                font-family: var(--tk-font-heading, serif);
                font-size: var(--tk-font-size-h2, 36px);
                color: var(--tk-text-main, #2C2B28);
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1.1 !important;
                font-weight: 400;
            }

            /* Content Section */
            .tk-overview-content {
                padding: 0 55px 34px !important;
                margin: 0 !important;
            }

            .tk-pilgrimage-overview__description {
                font-family: var(--tk-font-body, sans-serif);
                font-size: var(--tk-font-size-body, 18px);
                color: var(--tk-text-main, #2C2B28);
                line-height: 1.8;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Collapsible Content */
            .tk-overview-content-wrapper {
                position: relative;
                overflow: hidden;
            }

            .tk-overview-collapsible {
                overflow: hidden;
                transition: max-height 0.5s ease-in-out;
            }

            .tk-overview-fade {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 100px;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
                pointer-events: none;
                transition: opacity 0.3s ease, visibility 0.3s ease;
                z-index: 1;
            }

            .tk-overview-content.is-expanded .tk-overview-fade {
                opacity: 0;
                visibility: hidden;
            }

            .tk-overview-content.is-expanded .tk-overview-content-wrapper {
                overflow: visible;
            }

            .tk-overview-see-more {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-top: 20px;
                padding: 0;
                background: transparent;
                border: none;
                font-family: var(--tk-font-body, sans-serif);
                font-size: var(--tk-font-size-body, 18px);
                font-weight: 600;
                color: var(--tk-gold, #B8860B);
                cursor: pointer;
                transition: color var(--tk-transition-fast, 0.2s ease);
                position: relative;
                z-index: 2;
            }

            .tk-overview-see-more:hover {
                color: #9A7209;
            }

            .tk-overview-see-more__icon {
                transition: transform var(--tk-transition-fast, 0.2s ease);
            }

            .tk-overview-see-more.is-expanded .tk-overview-see-more__icon {
                transform: rotate(180deg);
            }

            /* Stats Section */
            .tk-overview-stats-section {
                padding: 0 55px 40px !important;
                margin: 0 !important;
            }

            .tk-pilgrimage-overview__stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
                margin: 0 !important;
                padding: 0 !important;
            }

            .tk-stat-card {
                background: var(--tk-bg-main, #F5F2ED);
                padding: var(--tk-space-sm, 34px);
                border-radius: var(--tk-border-radius, 10px);
                text-align: center;
                transition: transform var(--tk-transition-normal, 0.3s ease),
                    background-color var(--tk-transition-normal, 0.3s ease);
                margin: 0 !important;
            }

            .tk-stat-card:hover {
                transform: translateY(-2px);
                background-color: rgba(184, 134, 11, 0.08);
            }

            .tk-stat-card__label {
                font-family: var(--tk-font-body, sans-serif);
                font-size: var(--tk-font-size-small, 16px);
                color: var(--tk-gold, #B8860B);
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: var(--tk-space-xs, 21px);
                font-weight: 600;
            }

            .tk-stat-card__value {
                font-family: var(--tk-font-heading, serif);
                font-size: var(--tk-font-size-h3, 24px);
                color: var(--tk-text-main, #2C2B28);
                font-weight: 400;
            }

            @media (max-width: 768px) {
                .tk-pilgrimage-overview {
                    padding: 0 0 var(--tk-space-md, 55px) 0;
                }

                .tk-overview-header {
                    padding: 20px 34px 20px 34px !important;
                }

                .tk-pilgrimage-overview__title {
                    padding-top: 0 !important;
                }

                .tk-overview-content {
                    padding: 0 34px 28px !important;
                }

                .tk-overview-stats-section {
                    padding: 0 34px 28px !important;
                }

                .tk-pilgrimage-overview__stats {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                }
            }
        </style>
        <?php
    }
}
