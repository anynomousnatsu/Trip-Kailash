<?php
/**
 * Schema Markup Module - JSON-LD Structured Data
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Organization schema for site-wide use
 *
 * @return array Organization schema
 */
function tk_get_organization_schema()
{
    $logo_id = get_theme_mod('custom_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';

    return array(
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'logo' => $logo_url ?: TRIP_KAILASH_URI . '/assets/images/logo.png',
        'description' => get_bloginfo('description'),
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'telephone' => '+91-XXXXXXXXXX',
            'contactType' => 'customer service',
            'availableLanguage' => array('English', 'Hindi'),
        ),
        'sameAs' => array(
            'https://www.facebook.com/tripkailash',
            'https://www.instagram.com/tripkailash',
            'https://www.youtube.com/tripkailash',
        ),
        'areaServed' => array(
            array('@type' => 'Country', 'name' => 'India'),
            array('@type' => 'Country', 'name' => 'Nepal'),
            array('@type' => 'Country', 'name' => 'Tibet'),
        ),
    );
}

/**
 * Get LocalBusiness schema for homepage
 *
 * @return array LocalBusiness schema
 */
function tk_get_local_business_schema()
{
    return array(
        '@type' => 'TravelAgency',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'description' => 'Spiritual pilgrimage travel agency specializing in Kailash Mansarovar, Char Dham, and sacred journeys.',
        'priceRange' => '$$$',
        'address' => array(
            '@type' => 'PostalAddress',
            'addressCountry' => 'IN',
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => '28.6139',
            'longitude' => '77.2090',
        ),
        'openingHoursSpecification' => array(
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
            'opens' => '09:00',
            'closes' => '18:00',
        ),
    );
}

/**
 * Get TravelAction schema for pilgrimage package
 *
 * @param WP_Post $post Package post object
 * @return array TravelAction schema
 */
function tk_get_travel_action_schema($post)
{
    $trip_length = get_post_meta($post->ID, 'trip_length', true);
    $price_from = get_post_meta($post->ID, 'price_from', true);
    $key_stops = get_post_meta($post->ID, 'key_stops', true);
    $difficulty = get_post_meta($post->ID, 'difficulty', true);

    // Parse duration to ISO 8601 format
    $duration = 'P7D'; // Default 7 days
    if ($trip_length && preg_match('/(\d+)\s*nights?/i', $trip_length, $matches)) {
        $days = intval($matches[1]) + 1;
        $duration = 'P' . $days . 'D';
    }

    // Get destination from key stops
    $destination = 'Kailash Mansarovar';
    if (is_array($key_stops) && !empty($key_stops)) {
        $destination = end($key_stops);
    }

    // Get image URL with fallback to default
    $image_url = get_the_post_thumbnail_url($post, 'large');
    if (!$image_url) {
        $image_url = TRIP_KAILASH_URI . '/assets/images/og_default.jpg';
    }

    return array(
        '@type' => 'TravelAction',
        'name' => $post->post_title,
        'description' => tk_generate_meta_description($post),
        'url' => get_permalink($post),
        'image' => $image_url,
        'toLocation' => array(
            '@type' => 'Place',
            'name' => $destination,
            'address' => array(
                '@type' => 'PostalAddress',
                'addressCountry' => 'CN',
                'addressRegion' => 'Tibet',
            ),
        ),
        'duration' => $duration,
    );
}

/**
 * Get Product schema for pilgrimage package
 *
 * @param WP_Post $post Package post object
 * @return array Product schema
 */
function tk_get_product_schema($post)
{
    $pkg_info = function_exists('tk_get_package_info') ? tk_get_package_info() : array();

    /*
     * The field is the source of truth. tk_get_package_info() is filled in by
     * the old Elementor widgets, which are being retired.
     */
    $price_from = function_exists('tk_package') ? tk_package('price_from', $post->ID) : '';

    if ('' === $price_from || null === $price_from) {
        $price_from = !empty($pkg_info['price_from']) ? $pkg_info['price_from'] : get_post_meta($post->ID, 'price_from', true);
    }
    $trip_length = !empty($pkg_info['duration']) ? $pkg_info['duration'] : get_post_meta($post->ID, 'trip_length', true);

    // Build extended description for Schema (richer than meta tag)
    $description = tk_generate_meta_description($post);

    // Append Inclusions
    if (!empty($pkg_info['inclusions']) && is_array($pkg_info['inclusions'])) {
        $inclusions_str = implode(', ', array_slice($pkg_info['inclusions'], 0, 5));
        $description .= ' Includes: ' . $inclusions_str . '.';
    }

    // Append Itinerary Highlights
    if (!empty($pkg_info['itinerary']) && is_array($pkg_info['itinerary'])) {
        $highlights = [];
        foreach (array_slice($pkg_info['itinerary'], 0, 3) as $day) {
            $highlights[] = $day['title'];
        }
        $description .= ' Highlights: ' . implode(', ', $highlights) . '.';
    }

    // Get image URL with fallback to default
    $image_url = get_the_post_thumbnail_url($post, 'large');
    if (!$image_url) {
        $image_url = TRIP_KAILASH_URI . '/assets/images/og_default.jpg';
    }

    return array(
        '@type' => 'Product',
        'name' => $post->post_title,
        'description' => $description,
        'url' => get_permalink($post),
        'image' => $image_url,
        'brand' => array(
            '@type' => 'Brand',
            'name' => get_bloginfo('name'),
        ),
        'offers' => array(
            '@type' => 'Offer',
            'price' => $price_from,
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'validFrom' => date('Y-m-d'),
            'priceValidUntil' => date('Y-12-31'),
            'url' => get_permalink($post),
        ),
    );
}

/**
 * Get Person schema for guide
 *
 * @param WP_Post $post Guide post object
 * @return array Person schema
 */
function tk_get_person_schema($post)
{
    $years = get_post_meta($post->ID, 'years_of_experience', true);
    $bio = get_post_meta($post->ID, 'short_bio', true);

    // Get image URL with fallback
    $image_url = get_the_post_thumbnail_url($post, 'medium');
    if (!$image_url) {
        $image_url = TRIP_KAILASH_URI . '/assets/images/og_default.jpg';
    }

    return array(
        '@type' => 'Person',
        'name' => $post->post_title,
        'jobTitle' => 'Pilgrimage Guide',
        'description' => $bio ?: wp_trim_words($post->post_content, 30),
        'image' => $image_url,
        'url' => get_permalink($post),
        'worksFor' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
        ),
    );
}

/**
 * Get LodgingBusiness schema for lodge
 *
 * @param WP_Post $post Lodge post object
 * @return array LodgingBusiness schema
 */
function tk_get_lodging_schema($post)
{
    $location = get_post_meta($post->ID, 'location', true);
    $amenities = get_post_meta($post->ID, 'amenities', true);

    // Get image URL with fallback
    $image_url = get_the_post_thumbnail_url($post, 'large');
    if (!$image_url) {
        $image_url = TRIP_KAILASH_URI . '/assets/images/og_default.jpg';
    }

    $schema = array(
        '@type' => 'LodgingBusiness',
        'name' => $post->post_title,
        'description' => wp_trim_words($post->post_content, 30),
        'image' => $image_url,
        'url' => get_permalink($post),
        'address' => array(
            '@type' => 'PostalAddress',
            'addressLocality' => $location ?: 'Kathmandu',
            'addressCountry' => 'NP',
        ),
    );

    if (is_array($amenities) && !empty($amenities)) {
        $schema['amenityFeature'] = array_map(function ($amenity) {
            return array(
                '@type' => 'LocationFeatureSpecification',
                'name' => $amenity,
                'value' => true,
            );
        }, $amenities);
    }

    return $schema;
}


/**
 * Get FAQPage schema from FAQ content
 *
 * @param array $faqs Array of FAQ items with 'question' and 'answer' keys
 * @return array FAQPage schema
 */
function tk_get_faq_schema($faqs)
{
    if (empty($faqs) || !is_array($faqs)) {
        return array();
    }

    $main_entity = array();

    foreach ($faqs as $faq) {
        if (!empty($faq['question']) && !empty($faq['answer'])) {
            $main_entity[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($faq['answer']),
                ),
            );
        }
    }

    if (empty($main_entity)) {
        return array();
    }

    return array(
        '@type' => 'FAQPage',
        'mainEntity' => $main_entity,
    );
}

/**
 * Get BreadcrumbList schema
 *
 * @return array BreadcrumbList schema
 */
function tk_get_breadcrumb_schema()
{
    $breadcrumbs = array();
    $position = 1;

    // Home
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Home',
        'item' => home_url('/'),
    );

    if (is_singular('pilgrimage_package')) {
        $post = get_post();
        $deity_terms = get_the_terms($post->ID, 'deity');

        // Deity archive
        if ($deity_terms && !is_wp_error($deity_terms)) {
            $deity = $deity_terms[0];
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $deity->name,
                'item' => get_term_link($deity),
            );
        }

        // Current package
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $post->post_title,
            'item' => get_permalink($post),
        );
    } elseif (is_singular('guide')) {
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Guides',
            'item' => get_post_type_archive_link('guide'),
        );
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    } elseif (is_singular('lodge')) {
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Lodges',
            'item' => get_post_type_archive_link('lodge'),
        );
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    } elseif (is_tax('deity')) {
        $term = get_queried_object();
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $term->name . ' Pilgrimages',
            'item' => get_term_link($term),
        );
    }

    return array(
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    );
}

/**
 * Get WebPage schema for current page
 *
 * @return array WebPage schema
 */
function tk_get_webpage_schema()
{
    $post = get_post();

    return array(
        '@type' => 'WebPage',
        'name' => tk_generate_meta_title($post),
        'description' => tk_generate_meta_description($post),
        'url' => tk_get_canonical_url(),
        'isPartOf' => array(
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
        ),
        'inLanguage' => get_locale(),
        'datePublished' => $post ? get_the_date('c', $post) : '',
        'dateModified' => $post ? get_the_modified_date('c', $post) : '',
    );
}

/**
 * Build complete schema for current page
 *
 * @return array Complete schema array
 */
function tk_build_page_schema()
{
    $schema = array(
        '@context' => 'https://schema.org',
        '@graph' => array(),
    );

    // Always include Organization
    $schema['@graph'][] = tk_get_organization_schema();

    // Always include WebPage
    $schema['@graph'][] = tk_get_webpage_schema();

    // Always include Breadcrumbs
    $schema['@graph'][] = tk_get_breadcrumb_schema();

    // Homepage specific
    if (is_front_page() || is_home()) {
        $schema['@graph'][] = tk_get_local_business_schema();
    }

    // Package specific
    if (is_singular('pilgrimage_package')) {
        $post = get_post();
        $schema['@graph'][] = tk_get_travel_action_schema($post);
        $schema['@graph'][] = tk_get_product_schema($post);

        /* TouristTrip is the type that actually describes what is being sold
           here. Product alone described a fourteen day pilgrimage the same way
           it would describe a kettle. */
        $trip = tk_get_tourist_trip_schema($post);

        if (!empty($trip)) {
            $schema['@graph'][] = $trip;
        }

        // Check for FAQ content in post meta AND registered page FAQs
        $meta_faqs = get_post_meta($post->ID, 'faqs', true);
        if (!is_array($meta_faqs)) {
            $meta_faqs = array();
        }

        $page_faqs = tk_get_page_faqs();
        $all_faqs = array_merge($meta_faqs, $page_faqs);

        if (!empty($all_faqs)) {
            $faq_schema = tk_get_faq_schema($all_faqs);
            if (!empty($faq_schema)) {
                $schema['@graph'][] = $faq_schema;
            }
        }
    }

    // Guide specific
    if (is_singular('guide')) {
        $schema['@graph'][] = tk_get_person_schema(get_post());
    }

    // Lodge specific
    if (is_singular('lodge')) {
        $schema['@graph'][] = tk_get_lodging_schema(get_post());
    }

    return $schema;
}

/**
 * Output JSON-LD schema in footer
 */
function tk_output_schema_json_ld()
{
    $schema = tk_build_page_schema();

    if (!empty($schema['@graph'])) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}
add_action('wp_footer', 'tk_output_schema_json_ld', 100);

/**
 * Helper function to add FAQ schema from Elementor widget
 *
 * @param array $faqs FAQ array from widget
 */
function tk_register_page_faqs($faqs)
{
    global $tk_page_faqs;

    if (!isset($tk_page_faqs)) {
        $tk_page_faqs = array();
    }

    if (is_array($faqs)) {
        $tk_page_faqs = array_merge($tk_page_faqs, $faqs);
    }
}

/**
 * Get registered page FAQs
 *
 * @return array FAQs
 */
function tk_get_page_faqs()
{
    global $tk_page_faqs;
    return $tk_page_faqs ?: array();
}

/**
 * Helper function to add package info from Elementor widget
 *
 * @param array $info Package info array
 */
function tk_register_package_info($info)
{
    global $tk_package_info;
    if (!is_array($tk_package_info)) {
        $tk_package_info = array();
    }
    $tk_package_info = array_merge($tk_package_info, $info);
}

/**
 * Get registered package info
 *
 * @return array Package info
 */
function tk_get_package_info()
{
    global $tk_package_info;
    return $tk_package_info ?: array();
}

/**
 * TouristTrip schema, generated entirely from the package fields.
 *
 * TouristTrip is the type that actually describes what this business sells.
 * Product was carrying the whole job before, which meant a fourteen day
 * pilgrimage over a 5,630 m pass was described to search engines the same way
 * a kettle is.
 *
 * Everything here comes from a field. Nothing is defaulted, invented or
 * padded: a property with no value is omitted rather than guessed at, because
 * structured data is a claim made to a machine that will repeat it.
 *
 * @param WP_Post $post
 * @return array
 */
function tk_get_tourist_trip_schema($post)
{
    if (!function_exists('tk_package')) {
        return array();
    }

    $id = $post->ID;

    $schema = array(
        '@type' => 'TouristTrip',
        'name'  => $post->post_title,
        'url'   => get_permalink($post),
    );

    $pitch = tk_package('short_pitch', $id);

    if ($pitch) {
        $schema['description'] = wp_strip_all_tags($pitch);
    }

    $image = get_the_post_thumbnail_url($post, 'large');

    if ($image) {
        $schema['image'] = $image;
    }

    /* ISO 8601 duration. P14D is what a machine understands by fourteen days. */
    $days = (int) tk_package('duration_days', $id);

    if ($days > 0) {
        $schema['itinerary'] = array('@type' => 'ItemList', 'numberOfItems' => $days);
        $schema['duration']  = 'P' . $days . 'D';
    }

    $itinerary = tk_package('itinerary', $id);

    if (!empty($itinerary)) {
        $elements = array();
        $position = 0;

        foreach ($itinerary as $day) {
            if (empty($day['title'])) {
                continue;
            }

            $position++;
            $element = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $day['title'],
            );

            if (!empty($day['overnight'])) {
                $element['item'] = array(
                    '@type' => 'Place',
                    'name'  => $day['overnight'],
                );
            }

            $elements[] = $element;
        }

        if ($elements) {
            $schema['itinerary'] = array(
                '@type'           => 'ItemList',
                'numberOfItems'   => count($elements),
                'itemListElement' => $elements,
            );
        }
    }

    /* Group size, when both ends are known. */
    $min = (int) tk_package('group_size_min', $id);
    $max = (int) tk_package('group_size_max', $id);

    if ($min > 0 || $max > 0) {
        $audience = array('@type' => 'QuantitativeValue');

        if ($min > 0) {
            $audience['minValue'] = $min;
        }

        if ($max > 0) {
            $audience['maxValue'] = $max;
        }

        $schema['maximumAttendeeCapacity'] = $max > 0 ? $max : null;
        $schema['audience'] = array('@type' => 'Audience', 'audienceType' => 'Pilgrims');

        if (null === $schema['maximumAttendeeCapacity']) {
            unset($schema['maximumAttendeeCapacity']);
        }
    }

    /* The offer, and only when there is a real price to put in it. */
    $price = tk_package('price_from', $id);

    if ('' !== $price && null !== $price && (float) $price > 0) {
        $schema['offers'] = array(
            '@type'         => 'Offer',
            'price'         => (float) $price,
            'priceCurrency' => 'USD',
            'availability'  => 'https://schema.org/InStock',
            'url'           => get_permalink($post),
        );
    }

    /* Where it goes, from the region taxonomy. */
    $regions = get_the_terms($id, 'region');

    if ($regions && !is_wp_error($regions)) {
        $places = array();

        foreach ($regions as $region) {
            $places[] = array('@type' => 'Place', 'name' => $region->name);
        }

        $schema['touristType'] = wp_list_pluck((array) get_the_terms($id, 'tradition') ?: array(), 'name');
        $schema['subjectOf']   = $places;

        if (empty($schema['touristType'])) {
            unset($schema['touristType']);
        }
    }

    $schema['provider'] = array(
        '@type' => 'TravelAgency',
        'name'  => get_bloginfo('name'),
        'url'   => home_url('/'),
    );

    return $schema;
}
