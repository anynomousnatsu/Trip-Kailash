<?php
/**
 * REST API Endpoints
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register REST API routes
 */
function trip_kailash_register_rest_routes() {
    register_rest_route( 'tripkailash/v1', '/package/(?P<id>\d+)', array(
        'methods'             => 'GET',
        'callback'            => 'trip_kailash_get_package_details',
        'permission_callback' => '__return_true',
        'args'                => array(
            'id' => array(
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ) );
}
add_action( 'rest_api_init', 'trip_kailash_register_rest_routes' );

/**
 * Get package details via REST API
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function trip_kailash_get_package_details( $request ) {
    $package_id = $request->get_param( 'id' );
    
    // Get the post
    $package = get_post( $package_id );
    
    /*
     * SECURITY: this endpoint is public (permission_callback => __return_true),
     * so it must only ever expose published, unprotected content.
     *
     * It previously checked post_type but not post_status, which meant anyone
     * could walk IDs against /wp-json/tripkailash/v1/package/{id} and read
     * draft, pending and private packages -- full body content, pricing and
     * meta -- before they were ever published. Password-protected packages
     * leaked their contents the same way.
     */
    if ( ! $package || 'pilgrimage_package' !== $package->post_type ) {
        return new WP_Error(
            'invalid_package',
            esc_html__( 'Package not found.', 'trip-kailash' ),
            array( 'status' => 404 )
        );
    }

    if ( 'publish' !== $package->post_status || ! empty( $package->post_password ) ) {
        // Same 404 as a missing package, so the response cannot be used to
        // confirm that an unpublished package exists at a given ID.
        return new WP_Error(
            'invalid_package',
            esc_html__( 'Package not found.', 'trip-kailash' ),
            array( 'status' => 404 )
        );
    }
    
    // Get featured image
    $featured_image = get_the_post_thumbnail_url( $package_id, 'full' );
    
    // Get all meta fields
    $meta = array(
        'trip_length'       => get_post_meta( $package_id, 'trip_length', true ),
        'deity'             => get_post_meta( $package_id, 'deity', true ),
        'has_lodge'         => (bool) get_post_meta( $package_id, 'has_lodge', true ),
        'has_helicopter'    => (bool) get_post_meta( $package_id, 'has_helicopter', true ),
        'includes_meals'    => (bool) get_post_meta( $package_id, 'includes_meals', true ),
        'includes_rituals'  => (bool) get_post_meta( $package_id, 'includes_rituals', true ),
        'difficulty'        => get_post_meta( $package_id, 'difficulty', true ),
        'best_months'       => get_post_meta( $package_id, 'best_months', true ),
        'price_from'        => (int) get_post_meta( $package_id, 'price_from', true ),
        'key_stops'         => get_post_meta( $package_id, 'key_stops', true ),
    );
    
    // Prepare response
    $response = array(
        'id'             => $package_id,
        'title'          => get_the_title( $package_id ),
        'content'        => apply_filters( 'the_content', $package->post_content ),
        'excerpt'        => get_the_excerpt( $package_id ),
        'featured_image' => $featured_image,
        'meta'           => $meta,
    );
    
    return rest_ensure_response( $response );
}
