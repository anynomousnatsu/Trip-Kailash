<?php
/**
 * SEO Module - Meta Tags, Canonical URLs, Open Graph
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Block malware requests (saiga.php) with 410 Gone
 * This tells Google to de-index these pages immediately
 */
function tk_block_malware_requests()
{
    $request_uri = $_SERVER['REQUEST_URI'];

    if (strpos($request_uri, 'saiga.php') !== false) {
        status_header(410);
        nocache_headers();
        echo '410 Content Deleted';
        exit;
    }
}
add_action('init', 'tk_block_malware_requests');

/**
 * Enforce canonical domain (non-www)
 * Handles redirection at the theme level since .htaccess is not in git
 */
function tk_enforce_canonical_domain()
{
    // Skip for admin dashboard, standard WP cron, or XML-RPC
    if (is_admin() || defined('DOING_CRON') || defined('XMLRPC_REQUEST')) {
        return;
    }

    $site_url = get_home_url();
    $parsed_url = parse_url($site_url);

    // Safety checks
    if (!isset($parsed_url['host']) || !isset($parsed_url['scheme'])) {
        return;
    }

    $target_host = $parsed_url['host'];
    $target_scheme = $parsed_url['scheme']; // http or https

    $current_host = $_SERVER['HTTP_HOST'];
    $current_scheme = is_ssl() ? 'https' : 'http';

    // Check if current request matches target strictly (Scheme + Host)
    if ($current_host !== $target_host || $current_scheme !== $target_scheme) {
        $request_uri = $_SERVER['REQUEST_URI'];

        // Redirect to the canonical version
        wp_redirect($target_scheme . '://' . $target_host . $request_uri, 301);
        exit;
    }
}
add_action('template_redirect', 'tk_enforce_canonical_domain', 1);

/**
 * Generate optimized meta title (50-60 characters)
 *
 * @param WP_Post|null $post Post object
 * @return string Meta title
 */
function tk_generate_meta_title($post = null)
{
    if (!$post) {
        $post = get_post();
    }

    if (!$post) {
        return get_bloginfo('name');
    }

    $site_name = get_bloginfo('name');
    $title = '';

    // Package-specific title
    if ($post->post_type === 'pilgrimage_package') {
        $deity_terms = get_the_terms($post->ID, 'deity');
        $deity = $deity_terms && !is_wp_error($deity_terms) ? $deity_terms[0]->name : '';
        $title = $post->post_title;

        if ($deity) {
            $title .= ' - ' . $deity . ' Pilgrimage';
        } else {
            $title .= ' Pilgrimage';
        }
    }
    // Guide-specific title
    elseif ($post->post_type === 'guide') {
        $title = $post->post_title . ' - Expert Pilgrimage Guide';
    }
    // Lodge-specific title
    elseif ($post->post_type === 'lodge') {
        $title = $post->post_title . ' - Pilgrimage Accommodation';
    }
    // Default pages/posts
    else {
        $title = $post->post_title;
    }

    $suffix = ' | ' . $site_name;
    $full_title = $title . $suffix;

    /*
     * Keep the brand, shorten the description.
     *
     * The previous version truncated to substr($title, 0, 57) whenever the
     * combined string passed 60 characters, which discarded the site name
     * outright -- so precisely the longer, more competitive pages lost the
     * only occurrence of "Trip Kailash" in their title tag, which is the
     * string a branded search has to match. It also counted bytes rather
     * than characters, so a cut could land inside a multi-byte glyph.
     *
     * Now the suffix is reserved first and the descriptive part is trimmed
     * to fit around it, on a word boundary.
     */
    if (mb_strlen($full_title) > 60) {
        $available = 60 - mb_strlen($suffix) - 1;

        if ($available >= 20) {
            $trimmed = mb_substr($title, 0, $available);
            $last_space = mb_strrpos($trimmed, ' ');

            if ($last_space > 15) {
                $trimmed = mb_substr($trimmed, 0, $last_space);
            }

            $full_title = rtrim($trimmed, " ,.;:-") . $suffix;
        } else {
            // Site name alone is long enough that nothing sensible fits
            // beside it; lead with the page and let the brand follow.
            $full_title = mb_substr($title, 0, 57) . '...';
        }
    }

    return esc_html($full_title);
}

/**
 * Resolve the fallback Open Graph image.
 *
 * This used to point unconditionally at assets/images/og-default.jpg, which
 * has never shipped with the theme. Every share of a page without its own
 * featured image therefore advertised a 404, and Facebook, WhatsApp and X all
 * rendered the link with no preview image at all.
 *
 * Resolves against things that actually exist, in order of preference, and
 * returns an empty string rather than a broken URL if none do -- an absent
 * og:image degrades better than one that 404s.
 *
 * @return string Absolute image URL, or '' when nothing suitable exists.
 */
function tk_get_default_og_image()
{
    static $resolved = null;

    if (null !== $resolved) {
        return $resolved;
    }

    $candidates = array();

    // 1. An explicit choice, set as an option or via the filter below.
    $configured = get_option('tk_og_image', '');
    if ($configured) {
        $candidates[] = $configured;
    }

    // 2. The site's custom logo.
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $logo = wp_get_attachment_image_url($logo_id, 'full');
        if ($logo) {
            $candidates[] = $logo;
        }
    }

    // 3. The site icon, which WordPress guarantees is square and present
    //    whenever a favicon has been set.
    $icon = get_site_icon_url(512);
    if ($icon) {
        $candidates[] = $icon;
    }

    $resolved = $candidates ? $candidates[0] : '';

    /**
     * Filter the fallback Open Graph image URL.
     *
     * @param string $resolved Absolute URL, or '' when none is available.
     */
    $resolved = apply_filters('tk_default_og_image', $resolved);

    return $resolved;
}

/**
 * Generate optimized meta description (150-160 characters)
 *
 * @param WP_Post|null $post Post object
 * @return string Meta description
 */
function tk_generate_meta_description($post = null)
{
    if (!$post) {
        $post = get_post();
    }

    if (!$post) {
        return get_bloginfo('description');
    }

    $description = '';

    // Package-specific description
    if ($post->post_type === 'pilgrimage_package') {
        $pkg_info = function_exists('tk_get_package_info') ? tk_get_package_info() : array();

        $trip_length = !empty($pkg_info['duration']) ? $pkg_info['duration'] : get_post_meta($post->ID, 'trip_length', true);
        $difficulty = !empty($pkg_info['grading']) ? $pkg_info['grading'] : get_post_meta($post->ID, 'difficulty', true);
        // Price might be "Starting from $X" in widget, or int in meta. 
        // We'll trust meta for price unless widget provides clean int, but for description text widget is fine.
        $price_from = !empty($pkg_info['price_from']) ? $pkg_info['price_from'] : get_post_meta($post->ID, 'price_from', true);

        $description = 'Experience ' . $post->post_title;

        if ($trip_length) {
            $description .= ' - ' . $trip_length;
        }
        if ($difficulty) {
            $description .= '. ' . $difficulty . ' difficulty';
        }
        if ($price_from) {
            $description .= '. Starting from $' . number_format($price_from);
        }
        $description .= '. Book your spiritual journey today.';
    }
    // Guide-specific description
    elseif ($post->post_type === 'guide') {
        $years = get_post_meta($post->ID, 'years_of_experience', true);
        $bio = get_post_meta($post->ID, 'short_bio', true);

        $description = $post->post_title;
        if ($years) {
            $description .= ' - ' . $years . ' years of pilgrimage guiding experience';
        }
        if ($bio) {
            $description .= '. ' . $bio;
        }
    }
    // Lodge-specific description
    elseif ($post->post_type === 'lodge') {
        $location = get_post_meta($post->ID, 'location', true);
        $description = $post->post_title;
        if ($location) {
            $description .= ' in ' . $location;
        }
        $description .= '. Comfortable accommodation for pilgrims.';
    }
    // Default - use excerpt or content
    else {
        if ($post->post_excerpt) {
            $description = $post->post_excerpt;
        } else {
            $description = tk_extract_readable_text($post->post_content);
        }
    }

    $description = trim(preg_replace('/\s+/u', ' ', $description));

    // Never emit an empty or near-empty description; Google will invent one.
    if (mb_strlen($description) < 30) {
        $fallback = get_bloginfo('description');
        $description = $fallback
            ? $post->post_title . ' - ' . $fallback
            : $post->post_title . ' - ' . get_bloginfo('name');
    }

    /*
     * Truncate on a word boundary using multi-byte functions.
     *
     * The previous version used strlen()/substr(), which count bytes rather
     * than characters. Package titles and body copy on this site contain
     * Devanagari (for example "ॐ नमः शिवाय"), where a byte-offset cut lands
     * mid-character and emits a broken glyph into the search snippet.
     */
    if (mb_strlen($description) > 160) {
        $description = mb_substr($description, 0, 157);
        $last_space = mb_strrpos($description, ' ');

        if ($last_space > 100) {
            $description = mb_substr($description, 0, $last_space);
        }

        $description = rtrim($description, " ,.;:-") . '...';
    }

    return esc_attr($description);
}

/**
 * Extract human-readable text from post content for use in a meta description.
 *
 * wp_strip_all_tags() removes the tags but keeps whatever sat between them,
 * so a page built around a <video> element yields its fallback copy --
 * "Your browser does not support the video tag." was being served as this
 * site's homepage meta description, and as its og:description and
 * twitter:description along with it.
 *
 * Elements whose inner text is a fallback or a machine instruction, never
 * prose, are removed wholesale before the remaining tags are stripped.
 *
 * @param string $content Raw post content.
 * @return string Plain text suitable for a description.
 */
function tk_extract_readable_text($content)
{
    if (empty($content)) {
        return '';
    }

    // Elementor stores layout as shortcodes; render nothing rather than markup.
    $content = strip_shortcodes($content);

    // Drop these elements and everything inside them.
    $content = preg_replace(
        '#<(video|audio|script|style|noscript|iframe|svg|canvas)\b[^>]*>.*?</\1>#is',
        ' ',
        $content
    );

    // Self-closing or unclosed variants of the same.
    $content = preg_replace('#<(source|track|embed)\b[^>]*/?>#i', ' ', $content);

    return wp_strip_all_tags($content);
}

/**
 * Get canonical URL for current page
 *
 * @return string Canonical URL
 */
function tk_get_canonical_url()
{
    if (is_singular()) {
        return get_permalink();
    }

    if (is_home() || is_front_page()) {
        return home_url('/');
    }

    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        return get_term_link($term);
    }

    if (is_post_type_archive()) {
        return get_post_type_archive_link(get_post_type());
    }

    if (is_author()) {
        return get_author_posts_url(get_queried_object_id());
    }

    if (is_date()) {
        if (is_day()) {
            return get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
        }
        if (is_month()) {
            return get_month_link(get_query_var('year'), get_query_var('monthnum'));
        }
        return get_year_link(get_query_var('year'));
    }

    // Fallback to current URL without query strings
    global $wp;
    return home_url($wp->request);
}

/**
 * Output canonical URL meta tag
 */
function tk_output_canonical_url()
{
    $canonical = tk_get_canonical_url();
    if ($canonical) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";
    }
}

/**
 * Get Open Graph tags array
 *
 * @param WP_Post|null $post Post object
 * @return array OG tags
 */
function tk_get_og_tags($post = null)
{
    if (!$post) {
        $post = get_post();
    }

    $og = array(
        'og:site_name' => get_bloginfo('name'),
        'og:locale' => get_locale(),
    );

    /*
     * The front page is the site, not an article.
     *
     * It is a static Page here, so is_singular() matches it and it was being
     * advertised as og:type "article" titled "Home | Trip Kailash". Shares of
     * the homepage therefore looked like a blog post called "Home". Checked
     * before is_singular() so the front page never falls through to the
     * article branch.
     */
    if (is_front_page()) {
        $site_name = get_bloginfo('name');
        $tagline = get_bloginfo('description');

        $og['og:type'] = 'website';
        $og['og:title'] = $tagline ? $site_name . ' - ' . $tagline : $site_name;
        $og['og:description'] = tk_generate_meta_description($post);
        $og['og:url'] = home_url('/');
        $og['og:image'] = tk_get_default_og_image();
    } elseif (is_singular() && $post) {
        $og['og:type'] = ($post->post_type === 'pilgrimage_package') ? 'product' : 'article';
        $og['og:title'] = tk_generate_meta_title($post);
        $og['og:description'] = tk_generate_meta_description($post);
        $og['og:url'] = get_permalink($post);

        // Featured image
        if (has_post_thumbnail($post)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'large');
            if ($image) {
                $og['og:image'] = $image[0];
                $og['og:image:width'] = $image[1];
                $og['og:image:height'] = $image[2];
            }
        } else {
            // Default OG image
            $og['og:image'] = tk_get_default_og_image();
        }
    } else {
        $og['og:type'] = 'website';
        $og['og:title'] = get_bloginfo('name');
        $og['og:description'] = get_bloginfo('description');
        $og['og:url'] = home_url('/');
        $og['og:image'] = tk_get_default_og_image();
    }

    return $og;
}

/**
 * Output Open Graph meta tags
 */
function tk_output_og_tags()
{
    $og_tags = tk_get_og_tags();

    foreach ($og_tags as $property => $content) {
        if ($content) {
            echo '<meta property="' . esc_attr($property) . '" content="' . esc_attr($content) . '" />' . "\n";
        }
    }
}

/**
 * Output Twitter Card meta tags
 */
function tk_output_twitter_cards()
{
    $post = get_post();

    /*
     * Resolve the image before declaring the card type.
     *
     * twitter:image previously required a featured image, so every page
     * without one -- the homepage included -- declared summary_large_image
     * and then supplied no image, which X renders as a bare text link.
     * Falls back to the same default the Open Graph tags use, and drops to
     * the plain summary card only when no image exists anywhere.
     */
    $twitter_image = '';

    if (is_singular() && has_post_thumbnail($post)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'large');
        if ($image) {
            $twitter_image = $image[0];
        }
    }

    if (!$twitter_image) {
        $twitter_image = tk_get_default_og_image();
    }

    $card_type = $twitter_image ? 'summary_large_image' : 'summary';

    echo '<meta name="twitter:card" content="' . esc_attr($card_type) . '" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(tk_generate_meta_title($post)) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr(tk_generate_meta_description($post)) . '" />' . "\n";

    if ($twitter_image) {
        echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '" />' . "\n";
    }
}

/**
 * Output all SEO meta tags in wp_head
 */
function tk_output_seo_meta_tags()
{
    $post = get_post();

    // Meta description
    echo '<meta name="description" content="' . tk_generate_meta_description($post) . '" />' . "\n";

    // Canonical URL
    tk_output_canonical_url();

    // Open Graph tags
    tk_output_og_tags();

    // Twitter Cards
    tk_output_twitter_cards();

    /*
     * The robots directive is NOT echoed here.
     *
     * WordPress core already prints its own <meta name="robots"> via the
     * wp_robots filter, so echoing a second one produced two robots tags on
     * every page. Crawlers combine duplicates by taking the most restrictive
     * value, which makes the outcome depend on what any plugin adds later.
     * Contributing to core's single tag through the filter below keeps one
     * authoritative directive on the page. See tk_filter_robots().
     */
}
add_action('wp_head', 'tk_output_seo_meta_tags', 1);

/**
 * Contribute this theme's indexing preferences to core's single robots tag.
 *
 * @param array $robots Robots directives keyed by name.
 * @return array Modified directives.
 */
function tk_filter_robots($robots)
{
    $robots['index'] = true;
    $robots['follow'] = true;
    $robots['max-image-preview'] = 'large';
    $robots['max-snippet'] = -1;
    $robots['max-video-preview'] = -1;

    return $robots;
}
add_filter('wp_robots', 'tk_filter_robots');

/**
 * Filter document title parts for better SEO
 */
function tk_filter_document_title_parts($title)
{
    $post = get_post();

    if (is_singular() && $post) {
        if ($post->post_type === 'pilgrimage_package') {
            $deity_terms = get_the_terms($post->ID, 'deity');
            $deity = $deity_terms && !is_wp_error($deity_terms) ? $deity_terms[0]->name : '';

            if ($deity) {
                $title['title'] = $post->post_title . ' - ' . $deity . ' Pilgrimage';
            }
        }
    }

    return $title;
}
add_filter('document_title_parts', 'tk_filter_document_title_parts');


/**
 * Image SEO Optimization Functions
 */

/**
 * Generate descriptive alt text for package featured images
 *
 * @param string $alt Current alt text
 * @param int $attachment_id Attachment ID
 * @param int $post_id Post ID
 * @return string Modified alt text
 */
function tk_generate_package_image_alt($alt, $attachment_id, $post_id = null)
{
    // If alt already exists and is meaningful, keep it
    if (!empty($alt) && strlen($alt) > 10) {
        return $alt;
    }

    // Get the post this image is attached to
    if (!$post_id) {
        $post_id = get_post_field('post_parent', $attachment_id);
    }

    if (!$post_id) {
        return $alt;
    }

    $post = get_post($post_id);

    if (!$post) {
        return $alt;
    }

    // Generate alt based on post type
    if ($post->post_type === 'pilgrimage_package') {
        $deity_terms = get_the_terms($post_id, 'deity');
        $deity = $deity_terms && !is_wp_error($deity_terms) ? $deity_terms[0]->name : '';

        if ($deity) {
            return sprintf('%s - %s Pilgrimage Package', $post->post_title, $deity);
        }
        return $post->post_title . ' Pilgrimage Package';
    }

    if ($post->post_type === 'guide') {
        return $post->post_title . ' - Pilgrimage Guide';
    }

    if ($post->post_type === 'lodge') {
        $location = get_post_meta($post_id, 'location', true);
        if ($location) {
            return sprintf('%s Lodge in %s', $post->post_title, $location);
        }
        return $post->post_title . ' - Pilgrimage Accommodation';
    }

    return $alt ?: $post->post_title;
}

/**
 * Filter post thumbnail HTML to add optimized alt text
 *
 * @param string $html Thumbnail HTML
 * @param int $post_id Post ID
 * @param int $thumbnail_id Thumbnail attachment ID
 * @param string $size Image size
 * @param array $attr Image attributes
 * @return string Modified HTML
 */
function tk_filter_post_thumbnail_html($html, $post_id, $thumbnail_id, $size, $attr)
{
    if (empty($html)) {
        return $html;
    }

    $post = get_post($post_id);

    if (!$post || !in_array($post->post_type, array('pilgrimage_package', 'guide', 'lodge'))) {
        return $html;
    }

    // Generate optimized alt text
    $current_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $new_alt = tk_generate_package_image_alt($current_alt, $thumbnail_id, $post_id);

    // Replace alt attribute in HTML
    if ($new_alt !== $current_alt) {
        $html = preg_replace('/alt="[^"]*"/', 'alt="' . esc_attr($new_alt) . '"', $html);
    }

    return $html;
}
add_filter('post_thumbnail_html', 'tk_filter_post_thumbnail_html', 10, 5);

/**
 * Add lazy loading to content images
 *
 * @param string $content Post content
 * @return string Modified content
 */
function tk_add_lazy_loading_to_images($content)
{
    if (is_admin()) {
        return $content;
    }

    // Add loading="lazy" to images that don't have it
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function ($matches) {
            $img = $matches[0];

            // Skip if already has loading attribute
            if (strpos($img, 'loading=') !== false) {
                return $img;
            }

            // Skip hero images (first image in content)
            static $first_image = true;
            if ($first_image) {
                $first_image = false;
                return $img;
            }

            // Add lazy loading
            return str_replace('<img', '<img loading="lazy"', $img);
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'tk_add_lazy_loading_to_images', 15);

/**
 * Ensure images have width and height attributes
 *
 * @param array $attr Image attributes
 * @param WP_Post $attachment Attachment post object
 * @param string|array $size Image size
 * @return array Modified attributes
 */
function tk_add_image_dimensions($attr, $attachment, $size)
{
    // Skip if dimensions already set
    if (!empty($attr['width']) && !empty($attr['height'])) {
        return $attr;
    }

    // Get image dimensions
    $image = wp_get_attachment_image_src($attachment->ID, $size);

    if ($image) {
        $attr['width'] = $image[1];
        $attr['height'] = $image[2];
    }

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'tk_add_image_dimensions', 10, 3);

/**
 * Add decoding="async" to images for better performance
 *
 * @param array $attr Image attributes
 * @param WP_Post $attachment Attachment post object
 * @param string|array $size Image size
 * @return array Modified attributes
 */
function tk_add_async_decoding($attr, $attachment, $size)
{
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'tk_add_async_decoding', 10, 3);


/**
 * Mobile SEO Optimization Functions
 */

/**
 * Add mobile-specific meta tags
 */
function tk_mobile_meta_tags()
{
    // Theme color for mobile browsers
    echo '<meta name="theme-color" content="#B8860B">' . "\n";

    // Apple mobile web app capable
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

    // Format detection
    echo '<meta name="format-detection" content="telephone=yes">' . "\n";

    // Mobile app links (if applicable)
    // echo '<meta name="apple-itunes-app" content="app-id=YOUR_APP_ID">' . "\n";
}
add_action('wp_head', 'tk_mobile_meta_tags', 2);

/**
 * Add click-to-call and WhatsApp links helper
 *
 * @param string $phone Phone number
 * @param string $type Type: 'tel' or 'whatsapp'
 * @return string Formatted link
 */
function tk_get_contact_link($phone, $type = 'tel')
{
    // Clean phone number
    $clean_phone = preg_replace('/[^0-9+]/', '', $phone);

    if ($type === 'whatsapp') {
        // Remove + for WhatsApp
        $wa_phone = ltrim($clean_phone, '+');
        return 'https://wa.me/' . $wa_phone;
    }

    return 'tel:' . $clean_phone;
}

/**
 * Output mobile contact buttons
 *
 * @param string $phone Phone number
 * @param string $whatsapp_message Optional WhatsApp pre-filled message
 */
function tk_output_mobile_contact_buttons($phone, $whatsapp_message = '')
{
    $tel_link = tk_get_contact_link($phone, 'tel');
    $wa_link = tk_get_contact_link($phone, 'whatsapp');

    if ($whatsapp_message) {
        $wa_link .= '?text=' . urlencode($whatsapp_message);
    }
    ?>
    <div class="tk-mobile-contact-buttons">
        <a href="<?php echo esc_url($tel_link); ?>" class="tk-mobile-contact-btn tk-mobile-contact-btn--call"
            aria-label="<?php esc_attr_e('Call us', 'trip-kailash'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
            </svg>
            <span><?php esc_html_e('Call', 'trip-kailash'); ?></span>
        </a>
        <a href="<?php echo esc_url($wa_link); ?>" class="tk-mobile-contact-btn tk-mobile-contact-btn--whatsapp"
            target="_blank" rel="noopener" aria-label="<?php esc_attr_e('WhatsApp us', 'trip-kailash'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
            <span><?php esc_html_e('WhatsApp', 'trip-kailash'); ?></span>
        </a>
    </div>
    <?php
}

/**
 * Add structured data for mobile app (if applicable)
 */
function tk_mobile_app_schema()
{
    // Only output if app exists
    // Uncomment and configure when app is available
    /*
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'MobileApplication',
        'name' => get_bloginfo('name'),
        'operatingSystem' => 'iOS, Android',
        'applicationCategory' => 'TravelApplication',
        'offers' => array(
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    */
}
// add_action('wp_footer', 'tk_mobile_app_schema');


/**
 * Search Console Verification Functions
 */

/**
 * Output search engine verification meta tags
 * Configure these in WordPress Customizer or theme options
 */
function tk_search_console_verification()
{
    // Google Search Console verification
    $google_verification = get_theme_mod('tk_google_verification', '');
    if ($google_verification) {
        echo '<meta name="google-site-verification" content="' . esc_attr($google_verification) . '" />' . "\n";
    }

    // Bing Webmaster Tools verification
    $bing_verification = get_theme_mod('tk_bing_verification', '');
    if ($bing_verification) {
        echo '<meta name="msvalidate.01" content="' . esc_attr($bing_verification) . '" />' . "\n";
    }

    // Yandex verification (for Russian market)
    $yandex_verification = get_theme_mod('tk_yandex_verification', '');
    if ($yandex_verification) {
        echo '<meta name="yandex-verification" content="' . esc_attr($yandex_verification) . '" />' . "\n";
    }

    // Pinterest verification
    $pinterest_verification = get_theme_mod('tk_pinterest_verification', '');
    if ($pinterest_verification) {
        echo '<meta name="p:domain_verify" content="' . esc_attr($pinterest_verification) . '" />' . "\n";
    }
}
add_action('wp_head', 'tk_search_console_verification', 1);

/**
 * Add Customizer settings for verification codes
 *
 * @param WP_Customize_Manager $wp_customize Customizer object
 */
function tk_customizer_verification_settings($wp_customize)
{
    // Add SEO section
    $wp_customize->add_section('tk_seo_settings', array(
        'title' => __('SEO Settings', 'trip-kailash'),
        'priority' => 120,
    ));

    // Google verification
    $wp_customize->add_setting('tk_google_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_google_verification', array(
        'label' => __('Google Search Console Verification', 'trip-kailash'),
        'description' => __('Enter the content value from Google Search Console meta tag.', 'trip-kailash'),
        'section' => 'tk_seo_settings',
        'type' => 'text',
    ));

    // Bing verification
    $wp_customize->add_setting('tk_bing_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_bing_verification', array(
        'label' => __('Bing Webmaster Tools Verification', 'trip-kailash'),
        'description' => __('Enter the content value from Bing Webmaster Tools meta tag.', 'trip-kailash'),
        'section' => 'tk_seo_settings',
        'type' => 'text',
    ));

    // Yandex verification
    $wp_customize->add_setting('tk_yandex_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_yandex_verification', array(
        'label' => __('Yandex Verification', 'trip-kailash'),
        'description' => __('Enter the content value from Yandex Webmaster meta tag.', 'trip-kailash'),
        'section' => 'tk_seo_settings',
        'type' => 'text',
    ));

    // Pinterest verification
    $wp_customize->add_setting('tk_pinterest_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_pinterest_verification', array(
        'label' => __('Pinterest Verification', 'trip-kailash'),
        'description' => __('Enter the content value from Pinterest domain verification.', 'trip-kailash'),
        'section' => 'tk_seo_settings',
        'type' => 'text',
    ));

    // Contact phone for mobile
    $wp_customize->add_setting('tk_contact_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_contact_phone', array(
        'label' => __('Contact Phone Number', 'trip-kailash'),
        'description' => __('Phone number for click-to-call and WhatsApp (include country code).', 'trip-kailash'),
        'section' => 'tk_seo_settings',
        'type' => 'text',
    ));
}
add_action('customize_register', 'tk_customizer_verification_settings');

/**
 * Add geo meta tags for regional targeting
 */
function tk_geo_meta_tags()
{
    // Geographic targeting for India/Nepal region
    echo '<meta name="geo.region" content="IN" />' . "\n";
    echo '<meta name="geo.placename" content="India" />' . "\n";

    // ICBM coordinates (Delhi as primary location)
    echo '<meta name="ICBM" content="28.6139, 77.2090" />' . "\n";
    echo '<meta name="geo.position" content="28.6139;77.2090" />' . "\n";
}
add_action('wp_head', 'tk_geo_meta_tags', 3);
