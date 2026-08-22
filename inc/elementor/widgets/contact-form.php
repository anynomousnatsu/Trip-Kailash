<?php
/**
 * Contact Form Widget
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Contact_Form extends Widget_Base
{

    public function get_name()
    {
        return 'tk-contact-form';
    }

    public function get_title()
    {
        return __('Contact Form', 'trip-kailash');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['trip-kailash'];
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
            'form_title',
            [
                'label' => __('Form Title', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Get in Touch', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'submit_button_text',
            [
                'label' => __('Submit Button Text', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Send Message', 'trip-kailash'),
            ]
        );

        $this->add_control(
            'success_message',
            [
                'label' => __('Success Message', 'trip-kailash'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Thank you for your message. We will get back to you soon!', 'trip-kailash'),
                'rows' => 3,
            ]
        );

        $this->add_control(
            'email_recipient',
            [
                'label' => __('Email Recipient (configured in theme)', 'trip-kailash'),
                'type' => Controls_Manager::TEXT,
                'default' => get_option('admin_email'),
                'description' => __('Enquiries are sent to the address configured in the theme (TK_FORM_RECIPIENT in wp-config.php, or the tk_form_recipient option). This field is no longer used: taking the recipient from the page let anyone submit their own destination address and use the site as a mail relay.', 'trip-kailash'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Get all packages for dropdown
        // Cached id => title map; see tk_get_package_options() in inc/helpers.php.
        $package_options = tk_get_package_options();

        ?>
        <section class="tk-contact-form-widget">
            <?php if ($settings['form_title']): ?>
                <h2 class="tk-section-title"><?php echo esc_html($settings['form_title']); ?></h2>
            <?php endif; ?>

            <form class="tk-form tk-contact-form" id="tk-contact-form" method="post" action="">
                <?php wp_nonce_field('tk_contact_form', 'tk_contact_nonce'); ?>

                <input type="hidden" name="action" value="tk_submit_contact_form">
                <input type="hidden" name="success_message" value="<?php echo esc_attr($settings['success_message']); ?>">

                <!-- Honeypot spam trap - hidden from users, bots will fill it -->
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                    <input type="text" name="tk_website_url" value="" tabindex="-1" autocomplete="off">
                </div>

                <div class="tk-form-group">
                    <input type="text" name="name" class="tk-form-input" placeholder="<?php _e('Your Name', 'trip-kailash'); ?>"
                        required>
                </div>

                <div class="tk-form-group">
                    <input type="email" name="email" class="tk-form-input"
                        placeholder="<?php _e('Your Email', 'trip-kailash'); ?>" required>
                </div>

                <div class="tk-form-group">
                    <select name="package_interest" id="tk-package-interest" class="tk-form-select">
                        <option value=""><?php _e('Select Package (Optional)', 'trip-kailash'); ?></option>
                        <?php foreach ($package_options as $package_title): ?>
                            <option value="<?php echo esc_attr($package_title); ?>">
                                <?php echo esc_html($package_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tk-form-group">
                    <input type="text" name="travel_month" class="tk-form-input"
                        placeholder="<?php _e('Preferred Travel Month', 'trip-kailash'); ?>">
                </div>

                <div class="tk-form-group">
                    <input type="number" name="group_size" class="tk-form-input"
                        placeholder="<?php _e('Group Size', 'trip-kailash'); ?>" min="1">
                </div>

                <div class="tk-form-group">
                    <textarea name="message" class="tk-form-textarea" placeholder="<?php _e('Your Message', 'trip-kailash'); ?>"
                        rows="5"></textarea>
                </div>

                <button type="submit" class="tk-btn tk-btn-gold">
                    <?php echo esc_html($settings['submit_button_text']); ?>
                </button>
            </form>

            <div class="tk-form-message" style="display:none;"></div>
        </section>
        <?php
    }
}
