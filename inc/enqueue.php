<?php
/**
 * Asset Enqueuing
 *
 * Handles enqueuing of CSS and JavaScript files for the Trip Kailash theme.
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 */
function trip_kailash_enqueue_assets() {
    /*
     * Stylesheets are registered individually rather than chained through
     * @import in main.css. @import forces the browser to discover each file
     * only after the previous one has parsed, producing a serial
     * render-blocking waterfall. Separate handles let them download in
     * parallel while the dependency chain preserves cascade order.
     */
    $styles = array(
        'trip-kailash-variables'  => array( 'variables.css', array() ),
        'trip-kailash-base'       => array( 'base.css', array( 'trip-kailash-variables' ) ),
        'trip-kailash-components' => array( 'components.css', array( 'trip-kailash-base' ) ),
        'trip-kailash-header'     => array( 'header.css', array( 'trip-kailash-components' ) ),
        'trip-kailash-footer'     => array( 'footer.css', array( 'trip-kailash-header' ) ),
        // Elementor overrides must win the cascade, so they load last.
        'trip-kailash-elementor'  => array( 'elementor-overrides.css', array( 'trip-kailash-footer' ) ),
        'trip-kailash-seo'        => array( 'seo-components.css', array( 'trip-kailash-elementor' ) ),
    );

    foreach ( $styles as $handle => $style ) {
        list( $file, $deps ) = $style;
        wp_enqueue_style(
            $handle,
            TRIP_KAILASH_URI . '/assets/css/' . $file,
            $deps,
            TRIP_KAILASH_VERSION,
            'all'
        );
    }

    // Enqueue JavaScript files
    // Overlay script for package details
    wp_enqueue_script(
        'trip-kailash-overlay',
        TRIP_KAILASH_URI . '/assets/js/overlay.js',
        array(),
        TRIP_KAILASH_VERSION,
        true
    );

    // Mobile navigation script
    wp_enqueue_script(
        'trip-kailash-mobile-nav',
        TRIP_KAILASH_URI . '/assets/js/mobile-nav.js',
        array(),
        TRIP_KAILASH_VERSION,
        true
    );

    // Header navigation script
    wp_enqueue_script(
        'trip-kailash-header',
        TRIP_KAILASH_URI . '/assets/js/header.js',
        array(),
        TRIP_KAILASH_VERSION,
        true
    );

    // Video controls script
    wp_enqueue_script(
        'trip-kailash-video-controls',
        TRIP_KAILASH_URI . '/assets/js/video-controls.js',
        array(),
        TRIP_KAILASH_VERSION,
        true
    );

    // Main JavaScript file (depends on other scripts)
    wp_enqueue_script(
        'trip-kailash-main',
        TRIP_KAILASH_URI . '/assets/js/main.js',
        array( 'trip-kailash-overlay', 'trip-kailash-mobile-nav', 'trip-kailash-video-controls' ),
        TRIP_KAILASH_VERSION,
        true
    );

    // Pass PHP data to JavaScript
    wp_localize_script(
        'trip-kailash-main',
        'tripKailashData',
        array(
            'restUrl'       => esc_url_raw( rest_url( 'tripkailash/v1/' ) ),
            'restNonce'     => wp_create_nonce( 'wp_rest' ),
            'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
            'ajaxNonce'     => wp_create_nonce( 'trip_kailash_ajax' ),
            'themeUrl'      => TRIP_KAILASH_URI,
            'isElementor'   => did_action( 'elementor/loaded' ) ? true : false,
        )
    );
}
add_action( 'wp_enqueue_scripts', 'trip_kailash_enqueue_assets' );

/**
 * Enqueue admin styles and scripts
 */
function trip_kailash_enqueue_admin_assets( $hook ) {
    // Only load on post edit screens
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }

    // Check if we're editing a pilgrimage package, guide, or lodge
    global $post;
    if ( ! $post ) {
        return;
    }

    $allowed_post_types = array( 'pilgrimage_package', 'guide', 'lodge' );
    if ( ! in_array( $post->post_type, $allowed_post_types, true ) ) {
        return;
    }

    // Enqueue admin-specific styles if needed in the future
    // wp_enqueue_style( 'trip-kailash-admin', TRIP_KAILASH_URI . '/assets/css/admin.css', array(), TRIP_KAILASH_VERSION );
}
add_action( 'admin_enqueue_scripts', 'trip_kailash_enqueue_admin_assets' );
