<?php
/**
 * XML Sitemap Generator
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Disable WordPress core sitemaps (we use our own custom sitemaps)
 * This prevents empty/default sitemaps like wp-sitemap-posts-post-1.xml
 */
add_filter('wp_sitemaps_enabled', '__return_false');

/**
 * Register sitemap rewrite rules
 */
function tk_sitemap_rewrite_rules()
{
    add_rewrite_rule('^sitemap\.xml$', 'index.php?tk_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-pages\.xml$', 'index.php?tk_sitemap=pages', 'top');
    add_rewrite_rule('^sitemap-packages\.xml$', 'index.php?tk_sitemap=packages', 'top');
    add_rewrite_rule('^sitemap-guides\.xml$', 'index.php?tk_sitemap=guides', 'top');
    add_rewrite_rule('^sitemap-lodges\.xml$', 'index.php?tk_sitemap=lodges', 'top');
    add_rewrite_rule('^sitemap-deities\.xml$', 'index.php?tk_sitemap=deities', 'top');
}
add_action('init', 'tk_sitemap_rewrite_rules');

/**
 * Register sitemap query var
 */
function tk_sitemap_query_vars($vars)
{
    $vars[] = 'tk_sitemap';
    return $vars;
}
add_filter('query_vars', 'tk_sitemap_query_vars');

/**
 * Handle sitemap requests
 */
function tk_sitemap_template_redirect()
{
    $sitemap_type = get_query_var('tk_sitemap');

    if (!$sitemap_type) {
        return;
    }

    $builders = array(
        'index'    => 'tk_output_sitemap_index',
        'pages'    => 'tk_output_pages_sitemap',
        'packages' => 'tk_output_packages_sitemap',
        'guides'   => 'tk_output_guides_sitemap',
        'lodges'   => 'tk_output_lodges_sitemap',
        'deities'  => 'tk_output_deities_sitemap',
    );

    if (!isset($builders[$sitemap_type])) {
        status_header(404);
        exit;
    }

    // Set XML content type
    header('Content-Type: application/xml; charset=UTF-8');
    // Note: Sitemaps don't need indexing themselves - they're for search engines to discover your content
    // Removed X-Robots-Tag: noindex to avoid GSC warnings when inspecting sitemap URLs

    /*
     * Sitemaps are rebuilt from unbounded queries -- every published page,
     * package, guide and lodge. Rendering that on every crawler hit is
     * wasteful, and crawlers hit sitemaps often. Cache the finished XML and
     * rebuild only when content changes (see tk_flush_sitemap_cache) or when
     * the cache expires.
     */
    $cache_key = TK_SITEMAP_CACHE_PREFIX . $sitemap_type;
    $xml = get_transient($cache_key);

    if (!is_string($xml) || '' === $xml) {
        ob_start();
        call_user_func($builders[$sitemap_type]);
        $xml = ob_get_clean();

        if ('' !== $xml) {
            set_transient($cache_key, $xml, TK_SITEMAP_CACHE_TTL);
        }
    }

    header('Cache-Control: public, max-age=' . TK_SITEMAP_CACHE_TTL);
    echo $xml;

    exit;
}
add_action('template_redirect', 'tk_sitemap_template_redirect');

/**
 * Transient key prefix and lifetime for cached sitemap XML.
 */
const TK_SITEMAP_CACHE_PREFIX = 'tk_sitemap_xml_';
const TK_SITEMAP_CACHE_TTL = 6 * HOUR_IN_SECONDS;

/**
 * Drop every cached sitemap when indexable content changes.
 *
 * @param int $post_id Post ID being written.
 * @return void
 */
function tk_flush_sitemap_cache($post_id = 0)
{
    if ($post_id && wp_is_post_revision($post_id)) {
        return;
    }

    foreach (array('index', 'pages', 'packages', 'guides', 'lodges', 'deities') as $type) {
        delete_transient(TK_SITEMAP_CACHE_PREFIX . $type);
    }
}
add_action('save_post', 'tk_flush_sitemap_cache');
add_action('trashed_post', 'tk_flush_sitemap_cache');
add_action('untrashed_post', 'tk_flush_sitemap_cache');
add_action('deleted_post', 'tk_flush_sitemap_cache');
add_action('edited_deity', 'tk_flush_sitemap_cache');
add_action('created_deity', 'tk_flush_sitemap_cache');
add_action('delete_deity', 'tk_flush_sitemap_cache');

/**
 * Check if a post type has published posts
 *
 * @param string $post_type Post type to check
 * @return bool True if has published posts
 */
function tk_has_published_posts($post_type)
{
    $count = wp_count_posts($post_type);
    return isset($count->publish) && $count->publish > 0;
}

/**
 * Check if a taxonomy has terms
 *
 * @param string $taxonomy Taxonomy to check
 * @return bool True if has terms
 */
function tk_has_taxonomy_terms($taxonomy)
{
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'number' => 1,
    ));
    return !is_wp_error($terms) && !empty($terms);
}

/**
 * Output sitemap index
 */
function tk_output_sitemap_index()
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Pages sitemap (always include - we at least have homepage)
    $xml .= '<sitemap>' . "\n";
    $xml .= '<loc>' . esc_url(home_url('/sitemap-pages.xml')) . '</loc>' . "\n";
    $xml .= '<lastmod>' . esc_html(tk_get_latest_post_date('page')) . '</lastmod>' . "\n";
    $xml .= '</sitemap>' . "\n";

    // Packages sitemap (only if has published packages)
    if (tk_has_published_posts('pilgrimage_package')) {
        $xml .= '<sitemap>' . "\n";
        $xml .= '<loc>' . esc_url(home_url('/sitemap-packages.xml')) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(tk_get_latest_post_date('pilgrimage_package')) . '</lastmod>' . "\n";
        $xml .= '</sitemap>' . "\n";
    }

    // Guides sitemap (only if has published guides)
    if (tk_has_published_posts('guide')) {
        $xml .= '<sitemap>' . "\n";
        $xml .= '<loc>' . esc_url(home_url('/sitemap-guides.xml')) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(tk_get_latest_post_date('guide')) . '</lastmod>' . "\n";
        $xml .= '</sitemap>' . "\n";
    }

    // Lodges sitemap (only if has published lodges)
    if (tk_has_published_posts('lodge')) {
        $xml .= '<sitemap>' . "\n";
        $xml .= '<loc>' . esc_url(home_url('/sitemap-lodges.xml')) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(tk_get_latest_post_date('lodge')) . '</lastmod>' . "\n";
        $xml .= '</sitemap>' . "\n";
    }

    // Deities sitemap (only if has deity terms)
    if (tk_has_taxonomy_terms('deity')) {
        $xml .= '<sitemap>' . "\n";
        $xml .= '<loc>' . esc_url(home_url('/sitemap-deities.xml')) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(date('c')) . '</lastmod>' . "\n";
        $xml .= '</sitemap>' . "\n";
    }

    $xml .= '</sitemapindex>';

    echo $xml;
}

/**
 * Output pages sitemap
 */
function tk_output_pages_sitemap()
{
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    $xml .= '<url>' . "\n";
    $xml .= '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
    $xml .= '<lastmod>' . esc_html(date('c')) . '</lastmod>' . "\n";
    $xml .= '<changefreq>daily</changefreq>' . "\n";
    $xml .= '<priority>1.0</priority>' . "\n";
    $xml .= '</url>' . "\n";

    // Pages
    foreach ($pages as $page) {
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . esc_url(get_permalink($page)) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(get_the_modified_date('c', $page)) . '</lastmod>' . "\n";
        $xml .= '<changefreq>weekly</changefreq>' . "\n";
        $xml .= '<priority>0.8</priority>' . "\n";
        $xml .= '</url>' . "\n";
    }

    $xml .= '</urlset>';

    echo $xml;
}

/**
 * Output packages sitemap
 */
function tk_output_packages_sitemap()
{
    $packages = get_posts(array(
        'post_type' => 'pilgrimage_package',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

    foreach ($packages as $package) {
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . esc_url(get_permalink($package)) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(get_the_modified_date('c', $package)) . '</lastmod>' . "\n";
        $xml .= '<changefreq>weekly</changefreq>' . "\n";
        $xml .= '<priority>0.9</priority>' . "\n";

        if (has_post_thumbnail($package)) {
            $xml .= '<image:image>' . "\n";
            $xml .= '<image:loc>' . esc_url(get_the_post_thumbnail_url($package, 'large')) . '</image:loc>' . "\n";
            $xml .= '<image:title>' . esc_html($package->post_title) . '</image:title>' . "\n";
            $xml .= '</image:image>' . "\n";
        }

        $xml .= '</url>' . "\n";
    }

    $xml .= '</urlset>';

    echo $xml;
}

/**
 * Output guides sitemap
 */
function tk_output_guides_sitemap()
{
    $guides = get_posts(array(
        'post_type' => 'guide',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($guides as $guide) {
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . esc_url(get_permalink($guide)) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(get_the_modified_date('c', $guide)) . '</lastmod>' . "\n";
        $xml .= '<changefreq>monthly</changefreq>' . "\n";
        $xml .= '<priority>0.6</priority>' . "\n";
        $xml .= '</url>' . "\n";
    }

    $xml .= '</urlset>';

    echo $xml;
}

/**
 * Output lodges sitemap
 */
function tk_output_lodges_sitemap()
{
    $lodges = get_posts(array(
        'post_type' => 'lodge',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($lodges as $lodge) {
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . esc_url(get_permalink($lodge)) . '</loc>' . "\n";
        $xml .= '<lastmod>' . esc_html(get_the_modified_date('c', $lodge)) . '</lastmod>' . "\n";
        $xml .= '<changefreq>monthly</changefreq>' . "\n";
        $xml .= '<priority>0.6</priority>' . "\n";
        $xml .= '</url>' . "\n";
    }

    $xml .= '</urlset>';

    echo $xml;
}

/**
 * Output deities sitemap
 */
function tk_output_deities_sitemap()
{
    $deities = get_terms(array(
        'taxonomy' => 'deity',
        'hide_empty' => false,
    ));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    if (!is_wp_error($deities)) {
        foreach ($deities as $deity) {
            $term_link = get_term_link($deity);
            if (!is_wp_error($term_link)) {
                $xml .= '<url>' . "\n";
                $xml .= '<loc>' . esc_url($term_link) . '</loc>' . "\n";
                $xml .= '<changefreq>weekly</changefreq>' . "\n";
                $xml .= '<priority>0.8</priority>' . "\n";
                $xml .= '</url>' . "\n";
            }
        }
    }

    $xml .= '</urlset>';

    echo $xml;
}

/**
 * Get latest post date for a post type
 *
 * @param string $post_type Post type
 * @return string ISO 8601 date
 */
function tk_get_latest_post_date($post_type)
{
    $latest = get_posts(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'modified',
        'order' => 'DESC',
    ));

    if (!empty($latest)) {
        return get_the_modified_date('c', $latest[0]);
    }

    return date('c');
}

/**
 * Ping search engines when content is updated
 *
 * @param int $post_id Post ID
 */
function tk_ping_search_engines($post_id)
{
    // Only ping for published posts
    if (get_post_status($post_id) !== 'publish') {
        return;
    }

    // Only ping for our custom post types
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, array('page', 'pilgrimage_package', 'guide', 'lodge'))) {
        return;
    }

    // Don't ping on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    /*
     * Google retired https://www.google.com/ping?sitemap= in June 2023; it
     * now returns 404, so the request that used to live here accomplished
     * nothing but an outbound HTTP call on every save. The comment beside it
     * also claimed Google forwards to Bing via IndexNow, which is not true --
     * IndexNow is a separate protocol Google does not participate in.
     *
     * Sitemaps are discovered from robots.txt (see tk_robots_txt below) and
     * from Search Console. Nothing needs to be pinged.
     *
     * If you do want push notification, submit to IndexNow directly at
     * https://api.indexnow.org/indexnow with a key file hosted on the domain.
     */
    do_action('tk_content_published', $post_id, $post_type);
}
add_action('save_post', 'tk_ping_search_engines');

/**
 * Flush rewrite rules on theme activation
 * Note: This is called from functions.php via after_switch_theme hook
 */
function tk_sitemap_flush_rules()
{
    tk_sitemap_rewrite_rules();
    flush_rewrite_rules();
}



/**
 * Custom robots.txt output
 *
 * @param string $output Default robots.txt output
 * @param bool $public Whether the site is public
 * @return string Modified robots.txt output
 */
function tk_robots_txt($output, $public)
{
    if (!$public) {
        return $output;
    }

    $robots = "# Trip Kailash Robots.txt\n";
    $robots .= "# Generated by Trip Kailash Theme\n\n";

    // Allow all crawlers
    $robots .= "User-agent: *\n";

    // Allow crawling of public content
    $robots .= "Allow: /\n";
    $robots .= "Allow: /packages/\n";
    $robots .= "Allow: /deity/\n";

    // Block admin and sensitive areas
    $robots .= "Disallow: /wp-admin/\n";
    $robots .= "Allow: /wp-admin/admin-ajax.php\n";
    $robots .= "Disallow: /wp-includes/\n";
    $robots .= "Disallow: /wp-content/plugins/\n";
    $robots .= "Disallow: /wp-content/cache/\n";
    $robots .= "Disallow: /wp-content/themes/*/inc/\n";
    $robots .= "Disallow: /trackback/\n";
    $robots .= "Disallow: /feed/\n";
    $robots .= "Disallow: /comments/\n";
    $robots .= "Disallow: /?s=\n";
    $robots .= "Disallow: /search/\n";

    // Crawl delay for politeness
    $robots .= "\nCrawl-delay: 1\n";

    // Sitemap location
    $robots .= "\n# Sitemap\n";
    $robots .= "Sitemap: " . home_url('/sitemap.xml') . "\n";

    // Google specific
    $robots .= "\n# Google\n";
    $robots .= "User-agent: Googlebot\n";
    $robots .= "Allow: /\n";

    // Bing specific
    $robots .= "\n# Bing\n";
    $robots .= "User-agent: Bingbot\n";
    $robots .= "Allow: /\n";

    // AI crawlers (for GEO)
    $robots .= "\n# AI Crawlers\n";
    $robots .= "User-agent: GPTBot\n";
    $robots .= "Allow: /\n";
    $robots .= "User-agent: ChatGPT-User\n";
    $robots .= "Allow: /\n";
    $robots .= "User-agent: Google-Extended\n";
    $robots .= "Allow: /\n";
    $robots .= "User-agent: PerplexityBot\n";
    $robots .= "Allow: /\n";
    $robots .= "User-agent: ClaudeBot\n";
    $robots .= "Allow: /\n";

    return $robots;
}
add_filter('robots_txt', 'tk_robots_txt', 10, 2);
