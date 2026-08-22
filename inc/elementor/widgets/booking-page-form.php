<?php

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Booking Page Detailed Enquiry Form Widget
 *
 * Two-column layout: highlights on the left, full booking enquiry form on the right.
 * Uses existing Trip Kailash form styling and AJAX contact handler.
 */
class Booking_Page_Form extends Widget_Base
{

    public function get_name()
    {
        return 'tk-booking-page-form';
    }

    public function get_title()
    {
        return __('Booking Page – Enquiry Form', 'trip-kailash');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['trip-kailash'];
    }

    /**
     * Get list of countries
     */
    private function get_countries()
    {
        return [
            'Afghanistan',
            'Albania',
            'Algeria',
            'Andorra',
            'Angola',
            'Antigua and Barbuda',
            'Argentina',
            'Armenia',
            'Australia',
            'Austria',
            'Azerbaijan',
            'Bahamas',
            'Bahrain',
            'Bangladesh',
            'Barbados',
            'Belarus',
            'Belgium',
            'Belize',
            'Benin',
            'Bhutan',
            'Bolivia',
            'Bosnia and Herzegovina',
            'Botswana',
            'Brazil',
            'Brunei',
            'Bulgaria',
            'Burkina Faso',
            'Burundi',
            'Cambodia',
            'Cameroon',
            'Canada',
            'Cape Verde',
            'Central African Republic',
            'Chad',
            'Chile',
            'China',
            'Colombia',
            'Comoros',
            'Congo',
            'Costa Rica',
            'Croatia',
            'Cuba',
            'Cyprus',
            'Czech Republic',
            'Denmark',
            'Djibouti',
            'Dominica',
            'Dominican Republic',
            'East Timor',
            'Ecuador',
            'Egypt',
            'El Salvador',
            'Equatorial Guinea',
            'Eritrea',
            'Estonia',
            'Ethiopia',
            'Fiji',
            'Finland',
            'France',
            'Gabon',
            'Gambia',
            'Georgia',
            'Germany',
            'Ghana',
            'Greece',
            'Grenada',
            'Guatemala',
            'Guinea',
            'Guinea-Bissau',
            'Guyana',
            'Haiti',
            'Honduras',
            'Hungary',
            'Iceland',
            'India',
            'Indonesia',
            'Iran',
            'Iraq',
            'Ireland',
            'Israel',
            'Italy',
            'Ivory Coast',
            'Jamaica',
            'Japan',
            'Jordan',
            'Kazakhstan',
            'Kenya',
            'Kiribati',
            'North Korea',
            'South Korea',
            'Kuwait',
            'Kyrgyzstan',
            'Laos',
            'Latvia',
            'Lebanon',
            'Lesotho',
            'Liberia',
            'Libya',
            'Liechtenstein',
            'Lithuania',
            'Luxembourg',
            'Macedonia',
            'Madagascar',
            'Malawi',
            'Malaysia',
            'Maldives',
            'Mali',
            'Malta',
            'Marshall Islands',
            'Mauritania',
            'Mauritius',
            'Mexico',
            'Micronesia',
            'Moldova',
            'Monaco',
            'Mongolia',
            'Montenegro',
            'Morocco',
            'Mozambique',
            'Myanmar',
            'Namibia',
            'Nauru',
            'Nepal',
            'Netherlands',
            'New Zealand',
            'Nicaragua',
            'Niger',
            'Nigeria',
            'Norway',
            'Oman',
            'Pakistan',
            'Palau',
            'Panama',
            'Papua New Guinea',
            'Paraguay',
            'Peru',
            'Philippines',
            'Poland',
            'Portugal',
            'Qatar',
            'Romania',
            'Russia',
            'Rwanda',
            'Saint Kitts and Nevis',
            'Saint Lucia',
            'Saint Vincent and the Grenadines',
            'Samoa',
            'San Marino',
            'Sao Tome and Principe',
            'Saudi Arabia',
            'Senegal',
            'Serbia',
            'Seychelles',
            'Sierra Leone',
            'Singapore',
            'Slovakia',
            'Slovenia',
            'Solomon Islands',
            'Somalia',
            'South Africa',
            'South Sudan',
            'Spain',
            'Sri Lanka',
            'Sudan',
            'Suriname',
            'Swaziland',
            'Sweden',
            'Switzerland',
            'Syria',
            'Taiwan',
            'Tajikistan',
            'Tanzania',
            'Thailand',
            'Togo',
            'Tonga',
            'Trinidad and Tobago',
            'Tunisia',
            'Turkey',
            'Turkmenistan',
            'Tuvalu',
            'Uganda',
            'Ukraine',
            'United Arab Emirates',
            'United Kingdom',
            'United States',
            'Uruguay',
            'Uzbekistan',
            'Vanuatu',
            'Vatican City',
            'Venezuela',
            'Vietnam',
            'Yemen',
            'Zambia',
            'Zimbabwe'
        ];
    }

    /**
     * Get list of country codes
     */
    private function get_country_codes()
    {
        return [
            '+93' => 'Afghanistan (+93)',
            '+355' => 'Albania (+355)',
            '+213' => 'Algeria (+213)',
            '+376' => 'Andorra (+376)',
            '+244' => 'Angola (+244)',
            '+1-268' => 'Antigua and Barbuda (+1-268)',
            '+54' => 'Argentina (+54)',
            '+374' => 'Armenia (+374)',
            '+61' => 'Australia (+61)',
            '+43' => 'Austria (+43)',
            '+994' => 'Azerbaijan (+994)',
            '+1-242' => 'Bahamas (+1-242)',
            '+973' => 'Bahrain (+973)',
            '+880' => 'Bangladesh (+880)',
            '+1-246' => 'Barbados (+1-246)',
            '+375' => 'Belarus (+375)',
            '+32' => 'Belgium (+32)',
            '+501' => 'Belize (+501)',
            '+229' => 'Benin (+229)',
            '+975' => 'Bhutan (+975)',
            '+591' => 'Bolivia (+591)',
            '+387' => 'Bosnia and Herzegovina (+387)',
            '+267' => 'Botswana (+267)',
            '+55' => 'Brazil (+55)',
            '+673' => 'Brunei (+673)',
            '+359' => 'Bulgaria (+359)',
            '+226' => 'Burkina Faso (+226)',
            '+257' => 'Burundi (+257)',
            '+855' => 'Cambodia (+855)',
            '+237' => 'Cameroon (+237)',
            '+1' => 'Canada (+1)',
            '+238' => 'Cape Verde (+238)',
            '+236' => 'Central African Republic (+236)',
            '+235' => 'Chad (+235)',
            '+56' => 'Chile (+56)',
            '+86' => 'China (+86)',
            '+57' => 'Colombia (+57)',
            '+269' => 'Comoros (+269)',
            '+242' => 'Congo (+242)',
            '+506' => 'Costa Rica (+506)',
            '+385' => 'Croatia (+385)',
            '+53' => 'Cuba (+53)',
            '+357' => 'Cyprus (+357)',
            '+420' => 'Czech Republic (+420)',
            '+45' => 'Denmark (+45)',
            '+253' => 'Djibouti (+253)',
            '+1-767' => 'Dominica (+1-767)',
            '+1-809' => 'Dominican Republic (+1-809)',
            '+670' => 'East Timor (+670)',
            '+593' => 'Ecuador (+593)',
            '+20' => 'Egypt (+20)',
            '+503' => 'El Salvador (+503)',
            '+240' => 'Equatorial Guinea (+240)',
            '+291' => 'Eritrea (+291)',
            '+372' => 'Estonia (+372)',
            '+251' => 'Ethiopia (+251)',
            '+679' => 'Fiji (+679)',
            '+358' => 'Finland (+358)',
            '+33' => 'France (+33)',
            '+241' => 'Gabon (+241)',
            '+220' => 'Gambia (+220)',
            '+995' => 'Georgia (+995)',
            '+49' => 'Germany (+49)',
            '+233' => 'Ghana (+233)',
            '+30' => 'Greece (+30)',
            '+1-473' => 'Grenada (+1-473)',
            '+502' => 'Guatemala (+502)',
            '+224' => 'Guinea (+224)',
            '+245' => 'Guinea-Bissau (+245)',
            '+592' => 'Guyana (+592)',
            '+509' => 'Haiti (+509)',
            '+504' => 'Honduras (+504)',
            '+36' => 'Hungary (+36)',
            '+354' => 'Iceland (+354)',
            '+91' => 'India (+91)',
            '+62' => 'Indonesia (+62)',
            '+98' => 'Iran (+98)',
            '+964' => 'Iraq (+964)',
            '+353' => 'Ireland (+353)',
            '+972' => 'Israel (+972)',
            '+39' => 'Italy (+39)',
            '+225' => 'Ivory Coast (+225)',
            '+1-876' => 'Jamaica (+1-876)',
            '+81' => 'Japan (+81)',
            '+962' => 'Jordan (+962)',
            '+7' => 'Kazakhstan (+7)',
            '+254' => 'Kenya (+254)',
            '+686' => 'Kiribati (+686)',
            '+850' => 'North Korea (+850)',
            '+82' => 'South Korea (+82)',
            '+965' => 'Kuwait (+965)',
            '+996' => 'Kyrgyzstan (+996)',
            '+856' => 'Laos (+856)',
            '+371' => 'Latvia (+371)',
            '+961' => 'Lebanon (+961)',
            '+266' => 'Lesotho (+266)',
            '+231' => 'Liberia (+231)',
            '+218' => 'Libya (+218)',
            '+423' => 'Liechtenstein (+423)',
            '+370' => 'Lithuania (+370)',
            '+352' => 'Luxembourg (+352)',
            '+389' => 'Macedonia (+389)',
            '+261' => 'Madagascar (+261)',
            '+265' => 'Malawi (+265)',
            '+60' => 'Malaysia (+60)',
            '+960' => 'Maldives (+960)',
            '+223' => 'Mali (+223)',
            '+356' => 'Malta (+356)',
            '+692' => 'Marshall Islands (+692)',
            '+222' => 'Mauritania (+222)',
            '+230' => 'Mauritius (+230)',
            '+52' => 'Mexico (+52)',
            '+691' => 'Micronesia (+691)',
            '+373' => 'Moldova (+373)',
            '+377' => 'Monaco (+377)',
            '+976' => 'Mongolia (+976)',
            '+382' => 'Montenegro (+382)',
            '+212' => 'Morocco (+212)',
            '+258' => 'Mozambique (+258)',
            '+95' => 'Myanmar (+95)',
            '+264' => 'Namibia (+264)',
            '+674' => 'Nauru (+674)',
            '+977' => 'Nepal (+977)',
            '+31' => 'Netherlands (+31)',
            '+64' => 'New Zealand (+64)',
            '+505' => 'Nicaragua (+505)',
            '+227' => 'Niger (+227)',
            '+234' => 'Nigeria (+234)',
            '+47' => 'Norway (+47)',
            '+968' => 'Oman (+968)',
            '+92' => 'Pakistan (+92)',
            '+680' => 'Palau (+680)',
            '+507' => 'Panama (+507)',
            '+675' => 'Papua New Guinea (+675)',
            '+595' => 'Paraguay (+595)',
            '+51' => 'Peru (+51)',
            '+63' => 'Philippines (+63)',
            '+48' => 'Poland (+48)',
            '+351' => 'Portugal (+351)',
            '+974' => 'Qatar (+974)',
            '+40' => 'Romania (+40)',
            '+7' => 'Russia (+7)',
            '+250' => 'Rwanda (+250)',
            '+1-869' => 'Saint Kitts and Nevis (+1-869)',
            '+1-758' => 'Saint Lucia (+1-758)',
            '+1-784' => 'Saint Vincent and the Grenadines (+1-784)',
            '+685' => 'Samoa (+685)',
            '+378' => 'San Marino (+378)',
            '+239' => 'Sao Tome and Principe (+239)',
            '+966' => 'Saudi Arabia (+966)',
            '+221' => 'Senegal (+221)',
            '+381' => 'Serbia (+381)',
            '+248' => 'Seychelles (+248)',
            '+232' => 'Sierra Leone (+232)',
            '+65' => 'Singapore (+65)',
            '+421' => 'Slovakia (+421)',
            '+386' => 'Slovenia (+386)',
            '+677' => 'Solomon Islands (+677)',
            '+252' => 'Somalia (+252)',
            '+27' => 'South Africa (+27)',
            '+211' => 'South Sudan (+211)',
            '+34' => 'Spain (+34)',
            '+94' => 'Sri Lanka (+94)',
            '+249' => 'Sudan (+249)',
            '+597' => 'Suriname (+597)',
            '+268' => 'Swaziland (+268)',
            '+46' => 'Sweden (+46)',
            '+41' => 'Switzerland (+41)',
            '+963' => 'Syria (+963)',
            '+886' => 'Taiwan (+886)',
            '+992' => 'Tajikistan (+992)',
            '+255' => 'Tanzania (+255)',
            '+66' => 'Thailand (+66)',
            '+228' => 'Togo (+228)',
            '+676' => 'Tonga (+676)',
            '+1-868' => 'Trinidad and Tobago (+1-868)',
            '+216' => 'Tunisia (+216)',
            '+90' => 'Turkey (+90)',
            '+993' => 'Turkmenistan (+993)',
            '+688' => 'Tuvalu (+688)',
            '+256' => 'Uganda (+256)',
            '+380' => 'Ukraine (+380)',
            '+971' => 'United Arab Emirates (+971)',
            '+44' => 'United Kingdom (+44)',
            '+1' => 'United States (+1)',
            '+598' => 'Uruguay (+598)',
            '+998' => 'Uzbekistan (+998)',
            '+678' => 'Vanuatu (+678)',
            '+379' => 'Vatican City (+379)',
            '+58' => 'Venezuela (+58)',
            '+84' => 'Vietnam (+84)',
            '+967' => 'Yemen (+967)',
            '+260' => 'Zambia (+260)',
            '+263' => 'Zimbabwe (+263)',
        ];
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
                'default' => __('Complete Your Booking Request', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'section_subtitle',
            [
                'label' => __('Section Subtitle', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => __('Share a few details about your group and ideal travel dates. Our Trip Kailash specialists will send you a customised Yatra plan.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'highlights_title',
            [
                'label' => __('Highlights Title', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Why book with Trip Kailash', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'highlights_text',
            [
                'label' => __('Highlights (one per line)', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 4,
                'default' => "Dedicated Kailash pilgrimage specialists\nAll permits, logistics & rituals handled\nComfortable lodges and experienced guides\nFlexible dates for private & small groups",
                'description' => __('Each line will be shown as a separate bullet point.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'submit_button_text',
            [
                'label' => __('Submit Button Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Send Booking Request', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'success_message',
            [
                'label' => __('Success Message', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => __('Thank you. Our team will review your request and reply with a detailed itinerary and quote.', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'email_recipient',
            [
                'label' => __('Email Recipient', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => get_option('admin_email'),
                'description' => __('Address that will receive booking enquiries from this widget.', 'trip-kailash'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Prepare highlights list
        $highlights = [];
        if (!empty($settings['highlights_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $settings['highlights_text']);
            foreach ($lines as $line) {
                $line = trim($line);
                if ('' !== $line) {
                    $highlights[] = $line;
                }
            }
        }

        // Get packages for dropdown
        // Cached id => title map; see tk_get_package_options() in inc/helpers.php.
        $package_options = tk_get_package_options();
        ?>
        <section class="tk-booking-detail">
            <div class="tk-container">
                <div class="tk-booking-detail__inner">
                    <div class="tk-booking-detail__info">
                        <?php if (!empty($settings['section_title'])): ?>
                            <h2 class="tk-booking-detail__title"><?php echo esc_html($settings['section_title']); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($settings['section_subtitle'])): ?>
                            <p class="tk-booking-detail__subtitle"><?php echo esc_html($settings['section_subtitle']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($settings['highlights_title']) || !empty($highlights)): ?>
                            <div class="tk-booking-detail__highlights">
                                <?php if (!empty($settings['highlights_title'])): ?>
                                    <h3 class="tk-booking-detail__highlights-title">
                                        <?php echo esc_html($settings['highlights_title']); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if (!empty($highlights)): ?>
                                    <ul class="tk-booking-detail__highlights-list">
                                        <?php foreach ($highlights as $item): ?>
                                            <li><?php echo esc_html($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tk-booking-detail__form">
                        <form class="tk-form tk-contact-form tk-booking-detail-form" method="post">
                            <?php wp_nonce_field('tk_contact_form', 'tk_contact_nonce'); ?>
                            <input type="hidden" name="action" value="tk_submit_contact_form">
                            <input type="hidden" name="email_recipient"
                                value="<?php echo esc_attr($settings['email_recipient']); ?>">
                            <input type="hidden" name="success_message"
                                value="<?php echo esc_attr($settings['success_message']); ?>">
                            <input type="hidden" name="form_context" value="booking_detail">

                            <!-- Honeypot spam trap - hidden from users, bots will fill it -->
                            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                <input type="text" name="tk_website_url" value="" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="tk-booking-detail-form__grid">
                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-full-name">
                                        <?php esc_html_e('Full Name', 'trip-kailash'); ?>
                                    </label>
                                    <input type="text" id="tk-booking-full-name" name="name" class="tk-form-input" required
                                        placeholder="<?php esc_attr_e('Full Name', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-email-detail">
                                        <?php esc_html_e('Email Address', 'trip-kailash'); ?>
                                    </label>
                                    <input type="email" id="tk-booking-email-detail" name="email" class="tk-form-input" required
                                        placeholder="<?php esc_attr_e('Email Address', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-phone">
                                        <?php esc_html_e('Phone / WhatsApp', 'trip-kailash'); ?>
                                    </label>
                                    <div class="tk-phone-input-wrapper">
                                        <div class="tk-country-code-wrapper">
                                            <input type="text" name="phone_country_code"
                                                class="tk-form-input tk-phone-country-code" value="+1" placeholder="+1"
                                                data-country-indicator="phone">
                                            <span class="tk-country-indicator" id="phone-country-indicator"></span>
                                        </div>
                                        <input type="tel" id="tk-booking-phone" name="phone"
                                            class="tk-form-input tk-phone-number"
                                            placeholder="<?php esc_attr_e('Phone Number', 'trip-kailash'); ?>">
                                    </div>
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-emergency-contact">
                                        <?php esc_html_e('Emergency Contact Number', 'trip-kailash'); ?>
                                    </label>
                                    <div class="tk-phone-input-wrapper">
                                        <div class="tk-country-code-wrapper">
                                            <input type="text" name="emergency_country_code"
                                                class="tk-form-input tk-phone-country-code" value="+1" placeholder="+1"
                                                data-country-indicator="emergency">
                                            <span class="tk-country-indicator" id="emergency-country-indicator"></span>
                                        </div>
                                        <input type="tel" id="tk-booking-emergency-contact" name="emergency_contact"
                                            class="tk-form-input tk-phone-number"
                                            placeholder="<?php esc_attr_e('Emergency Number', 'trip-kailash'); ?>">
                                    </div>
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-country">
                                        <?php esc_html_e('Country of Residence', 'trip-kailash'); ?>
                                    </label>
                                    <select id="tk-booking-country" name="country" class="tk-form-select">
                                        <option value=""><?php esc_html_e('Select Country', 'trip-kailash'); ?></option>
                                        <?php foreach ($this->get_countries() as $country): ?>
                                            <option value="<?php echo esc_attr($country); ?>">
                                                <?php echo esc_html($country); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-passport">
                                        <?php esc_html_e('Passport Number', 'trip-kailash'); ?>
                                    </label>
                                    <input type="text" id="tk-booking-passport" name="passport_number" class="tk-form-input"
                                        placeholder="<?php esc_attr_e('Passport Number', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-package-detail">
                                        <?php esc_html_e('Preferred Yatra / Package', 'trip-kailash'); ?>
                                    </label>
                                    <select id="tk-booking-package-detail" name="package_interest" class="tk-form-select">
                                        <option value=""><?php esc_html_e('I am exploring options', 'trip-kailash'); ?>
                                        </option>
                                        <?php foreach ($package_options as $package_title): ?>
                                            <option value="<?php echo esc_attr($package_title); ?>">
                                                <?php echo esc_html($package_title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-month">
                                        <?php esc_html_e('Preferred Travel Month / Season', 'trip-kailash'); ?>
                                    </label>
                                    <input type="text" id="tk-booking-month" name="travel_month" class="tk-form-input"
                                        placeholder="<?php esc_attr_e('Preferred Travel Month / Season', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-dates-detail">
                                        <?php esc_html_e('Approx. Travel Dates', 'trip-kailash'); ?>
                                    </label>
                                    <input type="text" id="tk-booking-dates-detail" name="travel_dates" class="tk-form-input"
                                        placeholder="<?php esc_attr_e('e.g. 15–28 May 2025', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-travellers-detail">
                                        <?php esc_html_e('Number of Travellers', 'trip-kailash'); ?>
                                    </label>
                                    <input type="number" id="tk-booking-travellers-detail" name="group_size"
                                        class="tk-form-input" min="1" value="2"
                                        placeholder="<?php esc_attr_e('Number of Travellers', 'trip-kailash'); ?>">
                                </div>

                                <div class="tk-form-group">
                                    <label class="tk-form-label" for="tk-booking-room-type">
                                        <?php esc_html_e('Room Type Preference', 'trip-kailash'); ?>
                                    </label>
                                    <select id="tk-booking-room-type" name="room_type" class="tk-form-select">
                                        <option value=""><?php esc_html_e('Standard twin / double', 'trip-kailash'); ?>
                                        </option>
                                        <option value="single"><?php esc_html_e('Single room', 'trip-kailash'); ?></option>
                                        <option value="triple"><?php esc_html_e('Triple room', 'trip-kailash'); ?></option>
                                        <option value="custom"><?php esc_html_e('Mixed / custom', 'trip-kailash'); ?></option>
                                    </select>
                                </div>

                                <div class="tk-form-group tk-booking-detail-form__full">
                                    <label class="tk-form-label" for="tk-booking-message-detail">
                                        <?php esc_html_e('Special Requests / Additional Details', 'trip-kailash'); ?>
                                    </label>
                                    <textarea id="tk-booking-message-detail" name="message" class="tk-form-textarea" rows="4"
                                        placeholder="<?php esc_attr_e('Share any health considerations, rituals you wish to include, or flexibility with dates.', 'trip-kailash'); ?>"></textarea>
                                </div>
                            </div>

                            <div class="tk-booking-detail-form__submit">
                                <button type="submit" class="tk-btn tk-btn-gold">
                                    <?php echo esc_html($settings['submit_button_text']); ?>
                                </button>
                            </div>
                        </form>

                        <div class="tk-form-message" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            .tk-booking-detail {
                padding: var(--tk-space-lg, 89px) 0;
                background-color: var(--tk-bg-main, #F5F2ED);
            }

            .tk-booking-detail__inner {
                max-width: var(--tk-max-width, 1180px);
                margin: 0 auto;
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.3fr);
                gap: var(--tk-space-md, 42px);
                align-items: flex-start;
            }

            .tk-booking-detail__info {
                padding-right: var(--tk-space-xs, 20px);
                max-width: 440px;
            }

            .tk-booking-detail__title {
                font-family: var(--tk-font-heading, serif);
                font-size: clamp(28px, 3.2vw, 36px);
                font-weight: 400;
                margin: 0 0 10px 0;
                color: var(--tk-text-main, #2C2B28);
            }

            .tk-booking-detail__subtitle {
                font-family: var(--tk-font-body, sans-serif);
                font-size: var(--tk-font-size-body, 18px);
                line-height: 1.6;
                color: rgba(44, 43, 40, 0.8);
                margin: 0 0 var(--tk-space-xs, 20px) 0;
                max-width: 36rem;
            }

            .tk-booking-detail__highlights {
                margin-top: var(--tk-space-xs, 18px);
                padding-top: 14px;
                border-top: 1px solid rgba(44, 43, 40, 0.08);
            }

            .tk-booking-detail__highlights-title {
                font-family: var(--tk-font-heading, serif);
                font-size: var(--tk-font-size-h4, 20px);
                margin: 0 0 12px 0;
                color: var(--tk-text-main, #2C2B28);
            }

            .tk-booking-detail__highlights-list {
                list-style: none;
                padding: 0;
                margin: 0;
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                row-gap: 8px;
            }

            .tk-booking-detail__highlights-list li {
                position: relative;
                padding-left: 26px;
                margin-bottom: 0;
                font-family: var(--tk-font-body, sans-serif);
                font-size: var(--tk-font-size-small, 16px);
                color: rgba(44, 43, 40, 0.9);
            }

            .tk-booking-detail__highlights-list li::before {
                content: '\2713';
                position: absolute;
                left: 0;
                top: 0;
                color: var(--tk-gold, #B8860B);
                font-weight: 600;
            }

            .tk-booking-detail__form {
                background-color: #FFFFFF;
                border-radius: var(--tk-border-radius, 14px);
                box-shadow: var(--tk-shadow-card, 0 18px 40px rgba(0, 0, 0, 0.15));
                padding: 22px 26px 24px;
            }

            .tk-booking-detail-form__grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px 16px;
            }

            .tk-booking-detail-form__full {
                grid-column: 1 / -1;
            }

            .tk-booking-detail-form__submit {
                margin-top: var(--tk-space-xs, 18px);
                text-align: right;
            }

            .tk-booking-detail-form__submit .tk-btn {
                min-width: 220px;
            }

            /* Compact form sizing only for booking detail form */
            .tk-booking-detail__form .tk-form-label {
                margin-bottom: 4px;
                font-size: 13px;
            }

            .tk-booking-detail__form .tk-form-group {
                margin-bottom: 8px;
            }

            .tk-booking-detail__form .tk-form-group:last-of-type {
                margin-bottom: 0;
            }

            .tk-booking-detail__form .tk-form-input,
            .tk-booking-detail__form .tk-form-select {
                padding: 8px 14px;
                font-size: var(--tk-font-size-small, 15px);
                border-width: 1px;
                border-radius: 8px;
                border-color: rgba(44, 43, 40, 0.18);
            }

            .tk-booking-detail__form .tk-form-textarea {
                padding: 10px 14px;
                min-height: 96px;
                font-size: var(--tk-font-size-small, 15px);
                border-width: 1px;
                border-radius: 8px;
                border-color: rgba(44, 43, 40, 0.18);
            }

            .tk-booking-detail__form .tk-btn {
                padding: 10px 28px;
            }

            /* Phone input with country code */
            .tk-phone-input-wrapper {
                display: flex;
                gap: 8px;
            }

            .tk-country-code-wrapper {
                position: relative;
                flex: 0 0 70px;
                min-width: 70px;
            }

            .tk-phone-country-code {
                width: 100%;
                padding: 8px 6px;
                text-align: center;
            }

            .tk-country-indicator {
                position: absolute;
                bottom: -18px;
                left: 0;
                font-size: 11px;
                color: var(--tk-gold, #B8860B);
                white-space: nowrap;
                pointer-events: none;
            }

            .tk-phone-number {
                flex: 1;
                min-width: 0;
            }

            @media (max-width: 1024px) {
                .tk-booking-detail {
                    padding: var(--tk-space-md, 64px) 0;
                }

                .tk-booking-detail__inner {
                    grid-template-columns: 1fr;
                    max-width: 640px;
                }

                .tk-booking-detail__info {
                    padding-right: 0;
                    margin-bottom: var(--tk-space-sm, 24px);
                }

                .tk-booking-detail__form {
                    margin: 0 auto;
                }
            }

            @media (max-width: 768px) {
                .tk-booking-detail {
                    padding: var(--tk-space-md, 40px) 0;
                }

                .tk-booking-detail__inner {
                    max-width: 100%;
                }

                .tk-booking-detail .tk-container {
                    padding-left: var(--tk-space-xs, 12px);
                    padding-right: var(--tk-space-xs, 12px);
                }

                .tk-booking-detail__info {
                    text-align: center;
                }

                .tk-booking-detail__form {
                    padding: 8px 0 14px;
                    max-width: 100%;
                    background-color: transparent;
                    box-shadow: none;
                }

                .tk-booking-detail-form__grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 6px 10px;
                }

                .tk-booking-detail-form__submit {
                    margin-top: var(--tk-space-xs, 14px);
                    text-align: center;
                }

                .tk-booking-detail-form__submit .tk-btn {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .tk-booking-detail-form__grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 6px 8px;
                }

                .tk-booking-detail__form .tk-form-label {
                    display: none;
                }
            }
        </style>

        <script>
            (function () {
                const countryCodeMap = <?php echo json_encode($this->get_country_codes()); ?>;

                function verifyCountryCode(input) {
                    const code = input.value.trim();
                    const indicatorId = input.getAttribute('data-country-indicator') + '-country-indicator';
                    const indicator = document.getElementById(indicatorId);

                    if (!indicator) return;

                    if (code && countryCodeMap[code]) {
                        const countryName = countryCodeMap[code].replace(/\s*\([^)]*\)/, '');
                        indicator.textContent = countryName;
                        indicator.style.display = 'block';
                        input.style.borderColor = 'var(--tk-gold, #B8860B)';
                    } else if (code) {
                        indicator.textContent = 'Invalid code';
                        indicator.style.display = 'block';
                        indicator.style.color = '#d32f2f';
                        input.style.borderColor = '#d32f2f';
                    } else {
                        indicator.textContent = '';
                        indicator.style.display = 'none';
                        input.style.borderColor = 'rgba(44, 43, 40, 0.18)';
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const countryCodeInputs = document.querySelectorAll('.tk-phone-country-code');

                    countryCodeInputs.forEach(function (input) {
                        // Verify on page load
                        verifyCountryCode(input);

                        // Verify on input
                        input.addEventListener('input', function () {
                            verifyCountryCode(this);
                        });

                        // Verify on blur
                        input.addEventListener('blur', function () {
                            verifyCountryCode(this);
                        });
                    });
                });
            })();
        </script>
        <?php
    }
}
