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
        // Faces first: the @font-face block must be parsed before any rule
        // that asks for one, or the first paint uses the fallback stack.
        'trip-kailash-fonts'      => array( 'fonts.css', array() ),
        'trip-kailash-variables'  => array( 'variables.css', array( 'trip-kailash-fonts' ) ),
        'trip-kailash-base'       => array( 'base.css', array( 'trip-kailash-variables' ) ),
        'trip-kailash-motion'     => array( 'motion.css', array( 'trip-kailash-base' ) ),
        'trip-kailash-components' => array( 'components.css', array( 'trip-kailash-motion' ) ),
        'trip-kailash-header'     => array( 'header.css', array( 'trip-kailash-components' ) ),
        'trip-kailash-footer'     => array( 'footer.css', array( 'trip-kailash-header' ) ),
        // Elementor overrides must win the cascade, so they load last.
        'trip-kailash-elementor'  => array( 'elementor-overrides.css', array( 'trip-kailash-footer' ) ),
        'trip-kailash-seo'        => array( 'seo-components.css', array( 'trip-kailash-elementor' ) ),
    );

    /*
     * Make the Elementor override sheet load AFTER Elementor's own CSS.
     *
     * Elementor prints frontend.min.css after every theme stylesheet, so a
     * rule like `.elementor img { height: auto }` beat the theme's equally
     * specific `.tk-package-card__image img { height: 100% }` purely on
     * source order. The file whose entire purpose is overriding Elementor
     * was losing every tie. Declaring the dependency lets WordPress order
     * it correctly instead of escalating specificity forever.
     *
     * Guarded on the handle actually being registered: wp_enqueue_style()
     * silently drops a stylesheet whose dependency does not exist, which
     * would take the overrides off the page entirely.
     */
    if ( wp_style_is( 'elementor-frontend', 'registered' ) ) {
        $styles['trip-kailash-elementor'][1][] = 'elementor-frontend';
    }

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

    /*
     * Homepage styles, on the homepage only.
     *
     * Loaded after the Elementor overrides so the front page can win without
     * escalating specificity, and conditionally because the pinned gallery
     * and kora ring rules are dead weight on every other page.
     */
    if ( is_front_page() ) {
        $home_deps = array( 'trip-kailash-seo' );

        wp_enqueue_style(
            'trip-kailash-home',
            TRIP_KAILASH_URI . '/assets/css/home.css',
            $home_deps,
            TRIP_KAILASH_VERSION,
            'all'
        );

        /*
         * The scrub engine. It exits immediately unless the hero markup says
         * a clip is actually configured, so it costs nothing on a homepage
         * that is running the still.
         */
        // The pinned temple gallery.
        wp_enqueue_script(
            'trip-kailash-parikrama',
            TRIP_KAILASH_URI . '/assets/js/parikrama.js',
            array(),
            TRIP_KAILASH_VERSION,
            true
        );

        // The one interactive moment.
        wp_enqueue_script(
            'trip-kailash-kora',
            TRIP_KAILASH_URI . '/assets/js/kora-ring.js',
            array(),
            TRIP_KAILASH_VERSION,
            true
        );

        // The signature line, which also navigates.
        wp_enqueue_script(
            'trip-kailash-parikrama-line',
            TRIP_KAILASH_URI . '/assets/js/parikrama-line.js',
            array(),
            TRIP_KAILASH_VERSION,
            true
        );
    }

    /*
     * Package page assets, on single packages only. The template renders
     * entirely from fields, so none of this is needed anywhere else.
     */
    /* The catalogue: the archive, its taxonomy listings, and the Sacred Paths
       page, which all render the same cards. */
    if ( is_post_type_archive( 'pilgrimage_package' )
        || is_tax( array( 'tradition', 'region', 'style', 'deity' ) )
        || is_page_template( 'page-sacred-paths.php' ) ) {
        wp_enqueue_style(
            'trip-kailash-catalogue',
            TRIP_KAILASH_URI . '/assets/css/catalogue.css',
            array( 'trip-kailash-seo' ),
            TRIP_KAILASH_VERSION,
            'all'
        );
    }

    if ( is_singular( 'pilgrimage_package' ) ) {
        wp_enqueue_style(
            'trip-kailash-package',
            TRIP_KAILASH_URI . '/assets/css/package.css',
            array( 'trip-kailash-seo' ),
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

    // Reveal and motion driver. Deferred: it only reads the DOM and arms
    // observers, so nothing it does needs to block parsing.
    wp_enqueue_script(
        'trip-kailash-reveal',
        TRIP_KAILASH_URI . '/assets/js/reveal.js',
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

    if ( is_singular( 'pilgrimage_package' ) ) {
        wp_enqueue_script(
            'trip-kailash-package',
            TRIP_KAILASH_URI . '/assets/js/package.js',
            array( 'trip-kailash-main' ),
            TRIP_KAILASH_VERSION,
            true
        );
    }

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
 * Preload the two faces every page actually paints with.
 *
 * A self-hosted font is only discovered after the browser has downloaded and
 * parsed fonts.css, which puts the request a full round trip later than it
 * needs to be. Preloading the two latin subsets moves them onto the wire
 * immediately, which is where the swap-in flash goes away.
 *
 * Only these two. The latin-ext and Devanagari subsets are scoped by
 * unicode-range and most pages never need them, so preloading those would
 * spend bandwidth on files that go unused.
 */
function trip_kailash_preload_fonts() {
    $fonts = array( 'cinzel-latin.woff2', 'karla-latin.woff2' );

    foreach ( $fonts as $font ) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "
",
            esc_url( TRIP_KAILASH_URI . '/assets/fonts/' . $font )
        );
    }
}
add_action( 'wp_head', 'trip_kailash_preload_fonts', 1 );

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
