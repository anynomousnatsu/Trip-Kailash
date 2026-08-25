<?php
/**
 * Customizer settings
 *
 * Everything the redesigned templates read that is not package data. These are
 * facts about the business rather than content, which is why they live here
 * and not in a post.
 *
 * The pattern throughout the templates is that an unset value is OMITTED, not
 * printed as a placeholder. A site whose central trust argument is to go and
 * check our registration cannot ship a page with a bracket where a number
 * belongs, so leaving a field here empty is always safe.
 *
 * @package TripKailash
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the panel, its sections and every control.
 *
 * @param WP_Customize_Manager $wp_customize
 * @return void
 */
function tk_customize_register($wp_customize)
{
    $wp_customize->add_panel('tk_panel', array(
        'title'       => __('Trip Kailash', 'trip-kailash'),
        'description' => __('The details the site needs about the business. Anything left empty is hidden rather than shown as a placeholder.', 'trip-kailash'),
        'priority'    => 20,
    ));

    /* ---- Credentials -------------------------------------------------- */

    $wp_customize->add_section('tk_credentials', array(
        'title'       => __('Credentials', 'trip-kailash'),
        'panel'       => 'tk_panel',
        'description' => __('These drive the Check us before you pay us section, the booking panel and the footer. Fraud is the number one objection in this category, and both Nepali registers are public, so the site invites verification rather than asking to be believed.', 'trip-kailash'),
    ));

    $tk_text_fields = array(
        'tk_taan_number' => array(
            'section' => 'tk_credentials',
            'label'   => __('TAAN membership number', 'trip-kailash'),
            'help'    => __('Searchable by anyone at taan.org.np.', 'trip-kailash'),
        ),
        'tk_ntb_number' => array(
            'section' => 'tk_credentials',
            'label'   => __('Nepal Tourism Board licence number', 'trip-kailash'),
        ),
        'tk_company_reg' => array(
            'section' => 'tk_credentials',
            'label'   => __('Company registration number', 'trip-kailash'),
        ),
        'tk_office_address' => array(
            'section' => 'tk_credentials',
            'label'   => __('Office address', 'trip-kailash'),
            'help'    => __('A real address, because a contact form alone is what a buyer is taught to distrust.', 'trip-kailash'),
        ),
        'tk_whatsapp_number' => array(
            'section' => 'tk_credentials',
            'label'   => __('WhatsApp number', 'trip-kailash'),
            'help'    => __('With country code. Shows a bar on phones only.', 'trip-kailash'),
        ),

        /* ---- Lineage --------------------------------------------------- */

        'tk_founder_name' => array(
            'section' => 'tk_lineage',
            'label'   => __('Founder name', 'trip-kailash'),
            'help'    => __('For a $2,500 wire transfer, one named and photographed person does more than fifty anonymous five-stars.', 'trip-kailash'),
        ),
        'tk_trekmania_reviews_url' => array(
            'section' => 'tk_lineage',
            'label'   => __('Trekmania reviews link', 'trip-kailash'),
            'help'    => __('Labelled on the page as the parent company reviews. We do not present them as ours.', 'trip-kailash'),
        ),
        'tk_years_operating' => array(
            'section' => 'tk_lineage',
            'label'   => __('Years operating', 'trip-kailash'),
        ),
        'tk_travellers' => array(
            'section' => 'tk_lineage',
            'label'   => __('Travellers hosted', 'trip-kailash'),
        ),
        'tk_guides' => array(
            'section' => 'tk_lineage',
            'label'   => __('Licensed guides', 'trip-kailash'),
        ),
    );

    $wp_customize->add_section('tk_lineage', array(
        'title'       => __('Who takes you', 'trip-kailash'),
        'panel'       => 'tk_panel',
        'description' => __('There are no Trip Kailash testimonials yet and we do not invent them or borrow the parent company. Lineage answers the question a nervous buyer is actually asking.', 'trip-kailash'),
    ));

    foreach ($tk_text_fields as $tk_id => $tk_field) {
        $wp_customize->add_setting($tk_id, array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control($tk_id, array(
            'label'       => $tk_field['label'],
            'section'     => $tk_field['section'],
            'type'        => 'text',
            'description' => isset($tk_field['help']) ? $tk_field['help'] : '',
        ));
    }
}
add_action('customize_register', 'tk_customize_register');

/**
 * The remaining settings: the hero, the booking policy and the two flags that
 * hold back claims nobody has confirmed yet.
 *
 * @param WP_Customize_Manager $wp_customize
 * @return void
 */
function tk_customize_register_more($wp_customize)
{
    /* ---- The hero ----------------------------------------------------- */

    $wp_customize->add_section('tk_hero', array(
        'title'       => __('Homepage hero', 'trip-kailash'),
        'panel'       => 'tk_panel',
        'description' => __('One photograph, the claim, and one way in. Until an image is set the hero paints a sunset in the brand colours, so the layout is judged now and the photograph drops in later without anything moving.', 'trip-kailash'),
    ));

    $wp_customize->add_setting('tk_hero_poster_id', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'tk_hero_poster_id', array(
        'label'       => __('Hero image', 'trip-kailash'),
        'description' => __('The hero photograph. Landscape, and composed so the subject sits right of centre, since the words occupy the left.', 'trip-kailash'),
        'section'     => 'tk_hero',
        'mime_type'   => 'image',
    )));

    /*
     * The caption under the hero. It names what is in the photograph, so it
     * has to change when the photograph does, which is why it is a setting
     * rather than a line of markup somebody has to remember to go and edit.
     */
    $wp_customize->add_setting('tk_hero_viewing', array(
        'default'           => "Lord Shiva's home",
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_hero_viewing', array(
        'label'       => __('Currently viewing', 'trip-kailash'),
        'description' => __('Names the place in the hero photograph. Shown as "Currently viewing: ...". Leave empty to hide the line.', 'trip-kailash'),
        'section'     => 'tk_hero',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('tk_founder_portrait_id', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'tk_founder_portrait_id', array(
        'label'     => __('Founder portrait', 'trip-kailash'),
        'section'   => 'tk_lineage',
        'mime_type' => 'image',
    )));

    /* ---- Booking ------------------------------------------------------- */

    $wp_customize->add_section('tk_booking', array(
        'title' => __('Booking', 'trip-kailash'),
        'panel' => 'tk_panel',
    ));

    $wp_customize->add_setting('tk_cancellation_policy', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('tk_cancellation_policy', array(
        'label'       => __('Cancellation policy, in plain language', 'trip-kailash'),
        'description' => __('Say plainly when a deposit stops being refundable. Buyers are braced for vagueness here, so being specific is the advantage.', 'trip-kailash'),
        'section'     => 'tk_booking',
        'type'        => 'textarea',
    ));

    /* ---- Claims held back ---------------------------------------------
       Two switches, both off by default, both guarding a claim the site
       should not make until someone has confirmed it. Defaulting either to on
       would put an unverified statement on the page the first time the theme
       is activated.
       -------------------------------------------------------------------- */

    $wp_customize->add_section('tk_claims', array(
        'title'       => __('Claims to confirm', 'trip-kailash'),
        'panel'       => 'tk_panel',
        'description' => __('Both of these are switched off until someone confirms them. Nothing on the page depends on them being on.', 'trip-kailash'),
    ));

    $wp_customize->add_setting('tk_merit_figure_confirmed', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('tk_merit_figure_confirmed', array(
        'label'       => __('The Horse Year merit figure is confirmed', 'trip-kailash'),
        'description' => __('Sources give a kora walked in a Horse Year as worth thirteen ordinary ones, some say twelve. Switch this on only once you know which figure your tradition uses. Until then the hero and the kora ring say something specific and true instead.', 'trip-kailash'),
        'section'     => 'tk_claims',
        'type'        => 'checkbox',
    ));

    $wp_customize->add_setting('tk_horse_year_departure_open', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('tk_horse_year_departure_open', array(
        'label'       => __('A Kailash departure can still run this season', 'trip-kailash'),
        'description' => __('Shows the time-boxed band on the homepage. It also takes itself down when the season closes, so it can never outlive its own deadline.', 'trip-kailash'),
        'section'     => 'tk_claims',
        'type'        => 'checkbox',
    ));

    $wp_customize->add_setting('tk_horse_year_closes', array(
        'default'           => '2026-09-30',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tk_horse_year_closes', array(
        'label'       => __('Season closes on', 'trip-kailash'),
        'description' => __('Format YYYY-MM-DD.', 'trip-kailash'),
        'section'     => 'tk_claims',
        'type'        => 'text',
    ));
}
add_action('customize_register', 'tk_customize_register_more');
