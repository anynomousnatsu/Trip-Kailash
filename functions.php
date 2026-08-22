<?php
/**
 * Trip Kailash Theme Functions
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('TRIP_KAILASH_VERSION', '1.0.4');
define('TRIP_KAILASH_DIR', get_template_directory());
define('TRIP_KAILASH_URI', get_template_directory_uri());

/*
 * Where enquiry and booking emails are delivered.
 *
 * This is deliberately server-side. The recipient used to be read from a
 * hidden field in the public form, which let anyone POST their own address
 * and have the site mail it for them. Define TK_FORM_RECIPIENT in
 * wp-config.php to override this without editing the theme.
 */
if (!defined('TK_FORM_RECIPIENT')) {
    define('TK_FORM_RECIPIENT', 'tripkailashnepal@gmail.com');
}

/**
 * Theme Setup
 */
function trip_kailash_setup()
{
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo', array(
        'height' => 80,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'trip-kailash'),
        'mobile' => esc_html__('Mobile Menu', 'trip-kailash'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1400;
    }
}
add_action('after_setup_theme', 'trip_kailash_setup');

/**
 * Include required files
 */
require_once TRIP_KAILASH_DIR . '/inc/helpers.php';
require_once TRIP_KAILASH_DIR . '/inc/enqueue.php';
require_once TRIP_KAILASH_DIR . '/inc/custom-post-types.php';
require_once TRIP_KAILASH_DIR . '/inc/taxonomies.php';
require_once TRIP_KAILASH_DIR . '/inc/rest-api.php';
require_once TRIP_KAILASH_DIR . '/inc/form-handler.php';
require_once TRIP_KAILASH_DIR . '/inc/seo.php';
require_once TRIP_KAILASH_DIR . '/inc/schema.php';
require_once TRIP_KAILASH_DIR . '/inc/sitemap.php';
require_once TRIP_KAILASH_DIR . '/inc/performance.php';
require_once TRIP_KAILASH_DIR . '/inc/geo-content.php';

// Include Elementor integration if Elementor is active
if (did_action('elementor/loaded')) {
    require_once TRIP_KAILASH_DIR . '/inc/elementor/elementor-init.php';
}

/**
 * Admin notice if Elementor is not active
 */
function trip_kailash_elementor_notice()
{
    if (!did_action('elementor/loaded')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php esc_html_e('Trip Kailash theme requires Elementor to be installed and activated.', 'trip-kailash'); ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'trip_kailash_elementor_notice');

/**
 * Flush rewrite rules on theme activation
 */
function trip_kailash_activation()
{
    trip_kailash_register_post_types();
    trip_kailash_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'trip_kailash_activation');

/**
 * Override Elementor default content width
 */
function trip_kailash_elementor_content_width()
{
    return 100;
}
add_filter('elementor/page_settings/content_width', 'trip_kailash_elementor_content_width');

/**
 * Force Elementor container to full width via CSS variables
 */
function trip_kailash_elementor_full_width_css()
{
    // Only on frontend, not in editor - check if Elementor is loaded first
    if (!class_exists('\Elementor\Plugin') || !\Elementor\Plugin::$instance || !\Elementor\Plugin::$instance->preview->is_preview_mode()) {
        ?>
        <style id="tk-elementor-full-width">
            :root {
                --e-global-container-width: 100% !important;
                --e-container-width: 100% !important;
            }

            /* Override Elementor Pro container width */
            .e-con {
                --container-max-width: 100% !important;
                max-width: 100% !important;
            }

            /* Override widget container width */
            .e-con>.e-con-inner {
                max-width: 100% !important;
            }

            /* Force sections to be full width */
            .elementor .elementor-section .elementor-container {
                max-width: 100% !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'trip_kailash_elementor_full_width_css', 999);

/**
 * Override Elementor's kit settings for container width
 */
function trip_kailash_override_kit_settings($settings)
{
    if (isset($settings['container_width'])) {
        $settings['container_width']['size'] = 100;
        $settings['container_width']['unit'] = '%';
    }
    return $settings;
}
add_filter('elementor/kit/get_settings', 'trip_kailash_override_kit_settings');

// Ensure Elementor sections always render
add_filter('elementor/frontend/section/should_render', '__return_true');


/**
 * Add body class for full-width layout and hero detection
 */
function trip_kailash_body_classes($classes)
{
    $classes[] = 'tk-full-width';

    // Check if page has hero widget
    if (function_exists('get_post_meta') && get_the_ID()) {
        $elementor_data = get_post_meta(get_the_ID(), '_elementor_data', true);
        if (!empty($elementor_data)) {
            // Check if page contains hero widgets
            if (
                strpos($elementor_data, 'trip-kailash-hero-video') !== false ||
                strpos($elementor_data, 'tk-deity-hero') !== false
            ) {
                $classes[] = 'has-hero-section';
            }
        }
    }

    return $classes;
}
add_filter('body_class', 'trip_kailash_body_classes');
/**
 * Register the theme's CPTs with Elementor.
 *
 * Only writes the option when it actually needs to change, and only in
 * contexts where Elementor reads it (admin, editor, REST). Previously this
 * ran update_option() on every front-end request.
 */
function trip_kailash_enable_elementor_for_cpts()
{
    if (!is_admin() && !wp_doing_ajax() && !(defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    $post_types = array('pilgrimage_package', 'guide', 'lodge');
    $supported = get_option('elementor_cpt_support', array('post', 'page'));

    if (!is_array($supported)) {
        $supported = array('post', 'page');
    }

    // Nothing to do if every CPT is already registered.
    if (!array_diff($post_types, $supported)) {
        return;
    }

    update_option('elementor_cpt_support', array_values(array_unique(array_merge($supported, $post_types))));
}
add_action('init', 'trip_kailash_enable_elementor_for_cpts');

function trip_kailash_site_logo()
{
    if (function_exists('the_custom_logo') && has_custom_logo()) {
        echo get_custom_logo();
    } else {
        ?>
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <span class="tk-logo-icon">↑</span>
            <span class="tk-logo-text"><?php bloginfo('name'); ?></span>
        </a>
        <?php
    }
}
