<?php

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Pilgrimage_Sidebar_Nav extends Widget_Base {

    public function get_name() {
        return 'tk-pilgrimage-sidebar-nav';
    }

    public function get_title() {
        return __( 'Pilgrimage Sticky Sidebar', 'trip-kailash' );
    }

    public function get_icon() {
        return 'eicon-navigation-menu';
    }

    public function get_categories() {
        return [ 'trip-kailash' ];
    }

    protected function register_controls() {
        // Header Section
        $this->start_controls_section(
            'header_section',
            [
                'label' => __( 'Header', 'trip-kailash' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'header_image',
            [
                'label'   => __( 'Profile Image', 'trip-kailash' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'header_title',
            [
                'label'   => __( 'Title', 'trip-kailash' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Sacred Journeys', 'trip-kailash' ),
            ]
        );

        $this->add_control(
            'header_subtitle',
            [
                'label'   => __( 'Subtitle', 'trip-kailash' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Nepal & India', 'trip-kailash' ),
            ]
        );

        $this->end_controls_section();

        // Navigation Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Navigation Items', 'trip-kailash' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'important_notice',
            [
                'type'    => Controls_Manager::RAW_HTML,
                'raw'     => '<div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-left: 4px solid #B8860B;"><strong>📌 Important:</strong><br>Use Elementor\'s "Layout" settings to create a 30/70 split:<br>1. Create a 2-column section<br>2. Set column widths to 30% and 70%<br>3. Add this widget in the left 30% column<br>4. Add all other widgets in the right 70% column</div>',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'nav_label',
            [
                'label'   => __( 'Label', 'trip-kailash' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Overview', 'trip-kailash' ),
            ]
        );

        $repeater->add_control(
            'nav_icon',
            [
                'label'   => __( 'Icon', 'trip-kailash' ),
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-info-circle',
                    'library' => 'solid',
                ],
            ]
        );

        $repeater->add_control(
            'nav_link',
            [
                'label'       => __( 'Link (Anchor ID)', 'trip-kailash' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '#overview',
                'placeholder' => '#section-id',
            ]
        );

        $this->add_control(
            'nav_items',
            [
                'label'       => __( 'Navigation Items', 'trip-kailash' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'nav_label' => __( 'Overview', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-info-circle', 'library' => 'solid'],
                        'nav_link'  => '#overview',
                    ],
                    [
                        'nav_label' => __( 'Itinerary', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-calendar-alt', 'library' => 'solid'],
                        'nav_link'  => '#itinerary',
                    ],
                    [
                        'nav_label' => __( 'Map & Route', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-map', 'library' => 'solid'],
                        'nav_link'  => '#map',
                    ],
                    [
                        'nav_label' => __( 'Accommodation', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-bed', 'library' => 'solid'],
                        'nav_link'  => '#accommodation',
                    ],
                    [
                        'nav_label' => __( 'Travel Tips', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-lightbulb', 'library' => 'solid'],
                        'nav_link'  => '#travel-tips',
                    ],
                    [
                        'nav_label' => __( 'Gallery', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-camera', 'library' => 'solid'],
                        'nav_link'  => '#gallery',
                    ],
                    [
                        'nav_label' => __( 'Booking', 'trip-kailash' ),
                        'nav_icon'  => ['value' => 'fas fa-shopping-cart', 'library' => 'solid'],
                        'nav_link'  => '#booking',
                    ],
                ],
                'title_field' => '{{{ nav_label }}}',
            ]
        );

        $this->end_controls_section();

        // CTA Button Section
        $this->start_controls_section(
            'cta_section',
            [
                'label' => __( 'Call to Action Button', 'trip-kailash' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_cta',
            [
                'label'        => __( 'Show CTA Button', 'trip-kailash' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'trip-kailash' ),
                'label_off'    => __( 'No', 'trip-kailash' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'cta_text',
            [
                'label'     => __( 'Button Text', 'trip-kailash' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => __( 'Inquire Now', 'trip-kailash' ),
                'condition' => [
                    'show_cta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'cta_link',
            [
                'label'       => __( 'Button Link', 'trip-kailash' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '#inquiry',
                'placeholder' => '#section-id or URL',
                'condition'   => [
                    'show_cta' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        ?>
        <div id="tk-sidebar-wrapper-<?php echo esc_attr( $widget_id ); ?>" class="tk-sidebar-wrapper">
            <div class="tk-sidebar-placeholder"></div>
            <div class="tk-sticky-sidebar__inner">
                
                <?php if ( ! empty( $settings['header_title'] ) || ! empty( $settings['header_image']['url'] ) ) : ?>
                    <div class="tk-sidebar-header">
                        <?php if ( ! empty( $settings['header_image']['url'] ) ) : ?>
                            <div class="tk-sidebar-header__image">
                                <img src="<?php echo esc_url( $settings['header_image']['url'] ); ?>" 
                                     alt="<?php echo esc_attr( $settings['header_title'] ); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="tk-sidebar-header__content">
                            <?php if ( ! empty( $settings['header_title'] ) ) : ?>
                                <h3 class="tk-sidebar-header__title">
                                    <?php echo esc_html( $settings['header_title'] ); ?>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $settings['header_subtitle'] ) ) : ?>
                                <p class="tk-sidebar-header__subtitle">
                                    <?php echo esc_html( $settings['header_subtitle'] ); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                /*
                 * Transparent pricing, at the top, where it is asked for.
                 *
                 * Group-tier prices already exist on these pages, but they
                 * live in the booking table far below the fold, so a visitor
                 * weighing up the trip had to hunt for the number that decides
                 * whether they read on at all. This panel surfaces the
                 * headline price from the package's own price_from meta and
                 * links down to the full tier table rather than duplicating
                 * it, so the two can never disagree.
                 */
                $tk_price_from = get_post_meta( get_the_ID(), 'price_from', true );
                $tk_price_from = is_numeric( $tk_price_from ) ? (int) $tk_price_from : 0;

                if ( $tk_price_from > 0 ) :
                    ?>
                    <div class="tk-sidebar-price">
                        <span class="tk-sidebar-price__label"><?php esc_html_e( 'Starting from', 'trip-kailash' ); ?></span>
                        <span class="tk-sidebar-price__amount">
                            <?php echo esc_html( '$' . number_format( $tk_price_from ) ); ?>
                            <span class="tk-sidebar-price__unit"><?php esc_html_e( 'per person', 'trip-kailash' ); ?></span>
                        </span>
                        <p class="tk-sidebar-price__note">
                            <?php esc_html_e( 'Per-person cost falls as the group grows.', 'trip-kailash' ); ?>
                        </p>
                        <a class="tk-sidebar-price__link" href="#booking">
                            <?php esc_html_e( 'See group pricing', 'trip-kailash' ); ?> &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $settings['nav_items'] ) ) : ?>
                    <nav class="tk-sidebar-nav">
                        <ul class="tk-sidebar-nav__list">
                            <?php foreach ( $settings['nav_items'] as $index => $item ) : ?>
                                <li class="tk-sidebar-nav__item">
                                    <a href="<?php echo esc_attr( $item['nav_link'] ); ?>" 
                                       class="tk-sidebar-nav__link"
                                       data-index="<?php echo $index; ?>">
                                        <?php if ( ! empty( $item['nav_icon']['value'] ) ) : ?>
                                            <span class="tk-sidebar-nav__icon">
                                                <?php \Elementor\Icons_Manager::render_icon( $item['nav_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="tk-sidebar-nav__label">
                                            <?php echo esc_html( $item['nav_label'] ); ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

                <?php if ( $settings['show_cta'] === 'yes' && ! empty( $settings['cta_text'] ) ) : ?>
                    <div class="tk-sidebar-cta">
                        <a href="<?php echo esc_attr( $settings['cta_link'] ); ?>" 
                           class="tk-sidebar-cta__button">
                            <?php echo esc_html( $settings['cta_text'] ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>

        <style>
        .tk-sidebar-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 1px;
        }

        .tk-sidebar-placeholder {
            display: none;
            width: 100%;
        }

        .tk-sticky-sidebar__inner {
            position: relative;
            background: #FFFFFF;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            z-index: 90;
            transition: box-shadow 0.3s ease;
        }

        .tk-sticky-sidebar__inner.is-fixed {
            position: fixed;
            z-index: 90;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .tk-sticky-sidebar__inner.is-bottom {
            position: absolute;
            z-index: 90;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        /* Header Section */
        .tk-sidebar-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid #E5E5E5;
        }

        .tk-sidebar-header__image {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            background: #F0F0F0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .tk-sidebar-header__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tk-sidebar-header__content {
            flex: 1;
        }

        .tk-sidebar-header__title {
            margin: 0 0 3px 0;
            font-size: 17px;
            font-weight: 600;
            color: #4A5568;
            line-height: 1.3;
        }

        .tk-sidebar-header__subtitle {
            margin: 0;
            font-size: 13px;
            color: #718096;
            font-weight: 400;
        }

        /* Transparent pricing panel */
        .tk-sidebar-price {
            padding: 18px 20px 16px;
            border-bottom: 1px solid rgba(26, 27, 58, 0.08);
            background:
                radial-gradient(ellipse at 50% 0%, rgba(184, 134, 11, 0.13) 0%, transparent 70%),
                transparent;
        }

        .tk-sidebar-price__label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(44, 43, 40, 0.62);
            margin-bottom: 4px;
        }

        .tk-sidebar-price__amount {
            display: block;
            font-family: var(--tk-font-heading, Georgia, serif);
            font-size: 30px;
            line-height: 1.15;
            color: var(--tk-shiva, #1A1B3A);
        }

        .tk-sidebar-price__unit {
            font-family: var(--tk-font-body, sans-serif);
            font-size: 13px;
            color: rgba(44, 43, 40, 0.6);
            margin-left: 4px;
        }

        .tk-sidebar-price__note {
            margin: 8px 0 10px;
            font-size: 13px;
            line-height: 1.45;
            color: rgba(44, 43, 40, 0.72);
        }

        .tk-sidebar-price__link {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.04em;
            /* 4.64:1 on light backgrounds; see --tk-vishnu-text. */
            color: var(--tk-vishnu-text, #C2410C);
            text-decoration: none;
        }

        .tk-sidebar-price__link:hover {
            color: #8F3D0A;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* Navigation */
        .tk-sidebar-nav {
            padding: 10px 0;
        }

        .tk-sidebar-nav__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .tk-sidebar-nav__item {
            margin: 0;
        }

        .tk-sidebar-nav__link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            font-family: var(--tk-font-body, sans-serif);
            font-size: 14px;
            color: #4A5568;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }

        .tk-sidebar-nav__link:hover {
            background-color: #FEF5E7;
            color: #E67E22;
        }

        .tk-sidebar-nav__link.active {
            background-color: #FEF5E7;
            color: #E67E22;
            font-weight: 500;
        }

        .tk-sidebar-nav__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            font-size: 16px;
            color: inherit;
            flex-shrink: 0;
        }

        .tk-sidebar-nav__icon i,
        .tk-sidebar-nav__icon svg {
            width: 18px;
            height: 18px;
        }

        .tk-sidebar-nav__label {
            flex: 1;
        }

        /* CTA Button */
        .tk-sidebar-cta {
            padding: 14px 20px 18px;
        }

        .tk-sidebar-cta__button {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #D4A574 0%, #C89658 100%);
            color: #FFFFFF;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            border-radius: 7px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(212, 165, 116, 0.3);
        }

        .tk-sidebar-cta__button:hover {
            background: linear-gradient(135deg, #C89658 0%, #B8860B 100%);
            box-shadow: 0 6px 16px rgba(212, 165, 116, 0.4);
            transform: translateY(-2px);
            color: #FFFFFF;
        }

        /* Responsive */
        /* Small laptops (14" and below - typically 800px height or less) */
        @media (max-height: 800px) {
            .tk-sidebar-header {
                padding: 14px 18px;
                gap: 12px;
            }
            
            .tk-sidebar-header__image {
                width: 50px;
                height: 50px;
            }
            
            .tk-sidebar-header__title {
                font-size: 15px;
            }
            
            .tk-sidebar-header__subtitle {
                font-size: 12px;
            }
            
            .tk-sidebar-nav {
                padding: 8px 0;
            }
            
            .tk-sidebar-nav__link {
                padding: 8px 18px;
                font-size: 13px;
                gap: 10px;
            }
            
            .tk-sidebar-nav__icon {
                width: 16px;
                font-size: 14px;
            }
            
            .tk-sidebar-nav__icon i,
            .tk-sidebar-nav__icon svg {
                width: 16px;
                height: 16px;
            }
            
            .tk-sidebar-cta {
                padding: 12px 18px 14px;
            }
            
            .tk-sidebar-cta__button {
                padding: 10px 18px;
                font-size: 13px;
            }
        }
        
        /* Very small screens (height < 700px) */
        @media (max-height: 700px) {
            .tk-sidebar-header {
                padding: 12px 16px;
                gap: 10px;
            }
            
            .tk-sidebar-header__image {
                width: 45px;
                height: 45px;
            }
            
            .tk-sidebar-header__title {
                font-size: 14px;
            }
            
            .tk-sidebar-header__subtitle {
                font-size: 11px;
            }
            
            .tk-sidebar-nav {
                padding: 6px 0;
            }
            
            .tk-sidebar-nav__link {
                padding: 7px 16px;
                font-size: 12px;
            }
            
            .tk-sidebar-cta {
                padding: 10px 16px 12px;
            }
            
            .tk-sidebar-cta__button {
                padding: 9px 16px;
                font-size: 12px;
            }
        }
        
        @media (max-width: 1024px) {
            .tk-sticky-sidebar__inner.is-fixed,
            .tk-sticky-sidebar__inner.is-bottom {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                bottom: auto !important;
                width: 100% !important;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            }
        }
        @media (max-width: 768px) {
            .tk-sidebar-wrapper {
                z-index: 4;
            }

            .tk-sticky-sidebar__inner {
                position: fixed;
                top: var(--tk-header-height);
                left: 0;
                right: 0;
                border-radius: 0;
                box-shadow: none;
                z-index: 3; /* Below header (z-index: 5) and mobile nav (z-index: 4) */
            }

            /* Hide sidebar header and CTA on mobile to create a compact tab bar */
            .tk-sidebar-header,
            .tk-sidebar-cta {
                display: none;
            }

            /* Horizontal tab bar styling */
            .tk-sidebar-nav {
                padding: 0;
                background-color: var(--tk-gold);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .tk-sidebar-nav__list {
                display: flex;
                flex-wrap: nowrap;
                gap: 0;
                min-width: max-content;
            }

            .tk-sidebar-nav__item {
                flex: 0 0 auto;
            }

            .tk-sidebar-nav__link {
                justify-content: center;
                padding: 12px 16px;
                font-size: 14px;
                color: #ffffff;
                white-space: nowrap;
            }

            .tk-sidebar-nav__link:hover,
            .tk-sidebar-nav__link.active {
                background-color: rgba(0, 0, 0, 0.12);
                color: #ffffff;
            }

            /* Hide icons on mobile for a cleaner tab look */
            .tk-sidebar-nav__icon {
                display: none;
            }
        }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('tk-sidebar-wrapper-<?php echo esc_attr( $widget_id ); ?>');
            if (!wrapper) return;

            const sidebar = wrapper.querySelector('.tk-sticky-sidebar__inner');
            const placeholder = wrapper.querySelector('.tk-sidebar-placeholder');
            
            if (!sidebar || !placeholder) return;

            // Configuration
            const CONFIG = {
                headerHeight: 72, 
                buffer: 5,
                adminBarHeight: 32,
                mobileBreakpoint: 1024
            };

            // Find the Container (The Row)
            // We need the container that wraps both the sidebar column and the content column
            let container = wrapper.closest('.elementor-container');
            
            if (!container) {
                // Support for Elementor Flexbox Containers (.e-con)
                // The immediate .e-con is likely the column. We need its parent (the row).
                const currentCon = wrapper.closest('.e-con');
                if (currentCon) {
                    // Check if parent is also a container (nested structure)
                    if (currentCon.parentElement && currentCon.parentElement.classList.contains('e-con')) {
                        container = currentCon.parentElement;
                    } else {
                        // Fallback to the current container if no parent container found
                        container = currentCon;
                    }
                }
            }

            // Fallback for older structures
            if (!container) {
                container = wrapper.closest('.elementor-row');
            }

            if (container) {
                // Ensure container is relative so we can position absolute inside it
                const style = window.getComputedStyle(container);
                if (style.position === 'static') {
                    container.style.position = 'relative';
                }
            }

            function update() {
                // Desktop-only sticky behavior; mobile (<= 768px) uses the separate
                // fixed tab bar logic and should not be touched here.
                if (window.innerWidth <= 768) {
                    return;
                }

                if (!container || window.innerWidth <= CONFIG.mobileBreakpoint) {
                    reset();
                    return;
                }

                // 1. Calculate Top Offset
                const pricingBar = document.querySelector('.tk-pricing-bar');
                // Use getBoundingClientRect for more accuracy, or fallback to offsetHeight
                const pricingBarHeight = pricingBar ? pricingBar.offsetHeight : 0;
                const hasAdminBar = document.body.classList.contains('admin-bar');
                
                // Calculate total top offset: Pricing Bar + 5px padding + buffer
                // This makes the sidebar stick right below the pricing bar with small gap for maximum viewport usage
                const topOffset = pricingBarHeight + 5 + CONFIG.buffer + (hasAdminBar ? CONFIG.adminBarHeight : 0);

                // 2. Get Dimensions
                const containerRect = container.getBoundingClientRect();
                const wrapperRect = wrapper.getBoundingClientRect();
                const sidebarHeight = sidebar.offsetHeight;
                
                // 3. Calculate Scroll State
                // We want to start sticking when the wrapper hits the topOffset
                // We use the placeholder's position if the sidebar is already fixed
                // But wrapperRect should be correct because placeholder maintains flow
                const shouldStick = wrapperRect.top <= topOffset;
                
                // 4. Calculate Stop Point
                // We stop when the bottom of the sidebar hits the bottom of the container
                // Add extra buffer to stop earlier
                const hitBottom = (topOffset + sidebarHeight) >= (containerRect.bottom - CONFIG.buffer - 50);

                if (shouldStick) {
                    // Lock width to the wrapper's width to prevent resizing when fixed
                    const width = wrapper.getBoundingClientRect().width;
                    
                    if (hitBottom) {
                        // State: Bottom (Absolute)
                        // Sticking to the bottom of the container
                        sidebar.classList.remove('is-fixed');
                        sidebar.classList.add('is-bottom');
                        
                        sidebar.style.position = 'absolute';
                        sidebar.style.top = 'auto';
                        sidebar.style.bottom = CONFIG.buffer + 'px';
                        
                        // Left needs to be relative to container
                        const leftPos = wrapperRect.left - containerRect.left;
                        sidebar.style.left = leftPos + 'px';
                        sidebar.style.width = width + 'px';
                        
                        placeholder.style.height = sidebarHeight + 'px';
                        placeholder.style.display = 'block';
                        
                    } else {
                        // State: Fixed
                        // Sticking to the viewport
                        sidebar.classList.remove('is-bottom');
                        sidebar.classList.add('is-fixed');
                        
                        sidebar.style.position = 'fixed';
                        sidebar.style.top = topOffset + 'px';
                        sidebar.style.bottom = 'auto';
                        sidebar.style.left = wrapperRect.left + 'px';
                        sidebar.style.width = width + 'px';
                        
                        placeholder.style.height = sidebarHeight + 'px';
                        placeholder.style.display = 'block';
                    }
                } else {
                    reset();
                }
            }

            function reset() {
                sidebar.classList.remove('is-fixed', 'is-bottom');
                sidebar.style.position = '';
                sidebar.style.top = '';
                sidebar.style.bottom = '';
                sidebar.style.left = '';
                sidebar.style.width = '';
                
                placeholder.style.display = 'none';
                placeholder.style.height = '0';
            }

            // Loop
            let ticking = false;
            function onScroll() {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        update();
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', () => update(), { passive: true });
            
            // Initial
            setTimeout(update, 100);
            setTimeout(update, 500); // Double check for layout shifts
            
            // Observer for content changes
            new ResizeObserver(update).observe(container);
            if (wrapper) new ResizeObserver(update).observe(wrapper);


            // Smooth Scroll & Active State Logic
            const links = document.querySelectorAll('.tk-sidebar-nav__link');

            // Keep mobile tab bar aligned when header height changes (e.g. shrink on scroll)
            const headerObserverTarget = document.querySelector('.tk-header');
            if (headerObserverTarget && window.ResizeObserver) {
                new ResizeObserver(updateMobileTop).observe(headerObserverTarget);
            }

            function updateMobileTop() {
                if (window.innerWidth <= 768) {
                    const header = document.querySelector('.tk-header');
                    if (header) {
                        const rect = header.getBoundingClientRect();
                        // Overlap the bar slightly under the header so no gap can appear
                        const top = Math.max(rect.bottom - 4, 0);
                        sidebar.style.top = top + 'px';
                    }
                } else {
                    sidebar.style.top = '';
                }
            }

            function getScrollOffset() {
                const header = document.querySelector('.tk-header');
                const headerHeight = header ? header.offsetHeight : 80;

                // Only count pricing bar when it is sticky on larger screens
                const pricingBar = document.querySelector('.tk-pricing-bar.is-sticky');
                const pricingBarHeight = pricingBar ? pricingBar.offsetHeight : 0;

                // On mobile, also offset by the horizontal tab bar height
                let mobileTabsHeight = 0;
                if (window.innerWidth <= 768) {
                    const mobileTabs = wrapper.querySelector('.tk-sticky-sidebar__inner');
                    if (mobileTabs) {
                        mobileTabsHeight = mobileTabs.offsetHeight;
                    }
                }

                updateMobileTop();

                return headerHeight + pricingBarHeight + mobileTabsHeight + 20;
            }

            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            e.preventDefault();
                            const offset = getScrollOffset();
                            
                            // Use getBoundingClientRect for accurate position relative to viewport
                            // regardless of offsetParent (Elementor nesting issues)
                            const rect = targetElement.getBoundingClientRect();
                            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                            const targetPosition = rect.top + scrollTop - offset;
                            
                            window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                        }
                    }
                });
            });

            function setActiveLink() {
                const offset = getScrollOffset() + 30;
                let currentSection = '';
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                links.forEach(link => {
                    const targetId = link.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        const section = document.querySelector(targetId);
                        if (section) {
                            const rect = section.getBoundingClientRect();
                            const sectionTop = rect.top + scrollTop - offset;
                            
                            if (scrollTop >= sectionTop) {
                                currentSection = targetId;
                            }
                        }
                    }
                });
                links.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === currentSection) {
                        link.classList.add('active');
                    }
                });
            }
            window.addEventListener('scroll', setActiveLink);
            window.addEventListener('resize', setActiveLink);
            window.addEventListener('scroll', updateMobileTop);
            window.addEventListener('resize', updateMobileTop);
            updateMobileTop();
            setActiveLink();
        });
        </script>
        <?php
    }
}
