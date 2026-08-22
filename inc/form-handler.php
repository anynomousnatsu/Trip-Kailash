<?php
/**
 * Contact Form Handler
 * Handles AJAX form submissions and sends emails via wp_mail()
 *
 * @package TripKailash
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Resolve where enquiry email is sent.
 *
 * SECURITY: this used to read $_POST['email_recipient'], which was rendered
 * into the public form as a hidden input. Anyone could POST an arbitrary
 * address and have the site mail it on their behalf -- an open relay running
 * on the site's own domain and SMTP reputation, which is how a domain ends up
 * on spam blacklists. The nonce was no defence: nonces are embedded in the
 * public page HTML, so an attacker just loads a page to obtain a valid one.
 *
 * The destination is now server-side only and can never be influenced by the
 * request. Override it by defining TK_FORM_RECIPIENT in wp-config.php, by
 * setting the tk_form_recipient option, or via the filter below.
 *
 * @return string Validated recipient address.
 */
function tk_get_form_recipient()
{
    if (defined('TK_FORM_RECIPIENT') && is_email(TK_FORM_RECIPIENT)) {
        $recipient = TK_FORM_RECIPIENT;
    } else {
        $recipient = get_option('tk_form_recipient', '');
    }

    if (!is_email($recipient)) {
        $recipient = get_option('admin_email');
    }

    /**
     * Filter the enquiry recipient.
     *
     * Anything hooked here must not read from $_POST, $_GET or $_REQUEST.
     *
     * @param string $recipient Recipient address.
     */
    $recipient = apply_filters('tk_form_recipient', $recipient);

    return is_email($recipient) ? $recipient : get_option('admin_email');
}

/**
 * Handle contact form AJAX submission
 */
function tk_handle_contact_form()
{
    // Verify nonce for security
    if (!isset($_POST['tk_contact_nonce']) || !wp_verify_nonce($_POST['tk_contact_nonce'], 'tk_contact_form')) {
        wp_send_json_error(array(
            'message' => __('Security verification failed. Please refresh the page and try again.', 'trip-kailash')
        ));
        return;
    }

    // Honeypot spam check - if this field is filled, it's a bot
    if (!empty($_POST['tk_website_url'])) {
        // Silently pretend success to fool bots
        wp_send_json_success(array(
            'message' => __('Thank you for your message!', 'trip-kailash')
        ));
        return;
    }

    // Sanitize basic form data
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    // Resolved server-side only. See tk_get_form_recipient() below for why.
    $recipient = tk_get_form_recipient();
    $form_context = isset($_POST['form_context']) ? sanitize_text_field($_POST['form_context']) : 'contact';

    // Sanitize booking-specific fields
    $phone_code = isset($_POST['phone_country_code']) ? sanitize_text_field($_POST['phone_country_code']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $emergency_code = isset($_POST['emergency_country_code']) ? sanitize_text_field($_POST['emergency_country_code']) : '';
    $emergency_contact = isset($_POST['emergency_contact']) ? sanitize_text_field($_POST['emergency_contact']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $passport = isset($_POST['passport_number']) ? sanitize_text_field($_POST['passport_number']) : '';
    $package = isset($_POST['package_interest']) ? sanitize_text_field($_POST['package_interest']) : '';
    $travel_month = isset($_POST['travel_month']) ? sanitize_text_field($_POST['travel_month']) : '';
    $travel_dates = isset($_POST['travel_dates']) ? sanitize_text_field($_POST['travel_dates']) : '';
    $travel_start = isset($_POST['travel_start']) ? sanitize_text_field($_POST['travel_start']) : '';
    $travel_end = isset($_POST['travel_end']) ? sanitize_text_field($_POST['travel_end']) : '';
    $group_size = isset($_POST['group_size']) ? absint($_POST['group_size']) : '';
    $room_type = isset($_POST['room_type']) ? sanitize_text_field($_POST['room_type']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    // Validate required fields
    if (empty($name)) {
        wp_send_json_error(array(
            'message' => __('Please enter your name.', 'trip-kailash')
        ));
        return;
    }

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'trip-kailash')
        ));
        return;
    }

    // Validate recipient email
    if (empty($recipient) || !is_email($recipient)) {
        $recipient = get_option('admin_email');
    }

    // Build email subject
    $site_name = get_bloginfo('name');

    if ($form_context === 'booking_detail' || $form_context === 'booking_search') {
        $subject = sprintf('[%s] Booking Request from %s', $site_name, $name);
    } else {
        $subject = sprintf('[%s] New Inquiry from %s', $site_name, $name);
    }

    // Build HTML email body for better formatting
    $body = tk_build_email_body($form_context, array(
        'name' => $name,
        'email' => $email,
        'phone_code' => $phone_code,
        'phone' => $phone,
        'emergency_code' => $emergency_code,
        'emergency_contact' => $emergency_contact,
        'country' => $country,
        'passport' => $passport,
        'package' => $package,
        'travel_month' => $travel_month,
        'travel_dates' => $travel_dates,
        'travel_start' => $travel_start,
        'travel_end' => $travel_end,
        'group_size' => $group_size,
        'room_type' => $room_type,
        'message' => $message,
    ));

    // Email headers - HTML format
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    // Send email using wp_mail (WP Mail SMTP will intercept this)
    $sent = wp_mail($recipient, $subject, $body, $headers);

    // Only log failures. The previous version wrote the recipient, subject and
    // form context to the PHP error log on every successful submission, which
    // put enquirer names and email addresses into a file that is often world
    // readable on shared hosting and is not covered by any retention policy.
    if (!$sent) {
        error_log('Trip Kailash Form: wp_mail() failed for context ' . $form_context);
    }

    if ($sent) {
        do_action('tk_contact_form_sent', array(
            'name' => $name,
            'email' => $email,
            'package' => $package,
        ));

        wp_send_json_success(array(
            'message' => __('Thank you for your message. We will get back to you soon!', 'trip-kailash'),
        ));
    } else {
        global $phpmailer;
        $mail_error = '';
        if (isset($phpmailer) && is_object($phpmailer) && isset($phpmailer->ErrorInfo)) {
            $mail_error = $phpmailer->ErrorInfo;
        }

        error_log('Trip Kailash: Failed to send contact form email to ' . $recipient);
        error_log('Trip Kailash: Mail error: ' . $mail_error);

        wp_send_json_error(array(
            'message' => __('Sorry, there was an error sending your message. Please try again or contact us directly.', 'trip-kailash')
        ));
    }
}

/**
 * Build a nicely formatted HTML email body
 */
function tk_build_email_body($form_context, $data)
{
    $is_booking = ($form_context === 'booking_detail' || $form_context === 'booking_search');

    // Start building HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #B8860B, #DAA520); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 25px; border: 1px solid #ddd; }
            .section { margin-bottom: 20px; }
            .section-title { font-size: 14px; color: #888; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            .field { margin-bottom: 12px; }
            .field-label { font-weight: bold; color: #555; display: inline-block; min-width: 150px; }
            .field-value { color: #333; }
            .message-box { background: white; padding: 15px; border-left: 4px solid #B8860B; margin-top: 10px; }
            .footer { background: #333; color: #999; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
            .highlight { background: #FFF8DC; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>' . ($is_booking ? '🙏 New Booking Request' : '✉️ New Inquiry') . '</h1>
        </div>
        <div class="content">';

    // Contact Information Section
    $html .= '<div class="section">
        <div class="section-title">Contact Information</div>
        <div class="field">
            <span class="field-label">Name:</span>
            <span class="field-value">' . esc_html($data['name']) . '</span>
        </div>
        <div class="field">
            <span class="field-label">Email:</span>
            <span class="field-value"><a href="mailto:' . esc_attr($data['email']) . '">' . esc_html($data['email']) . '</a></span>
        </div>';

    if (!empty($data['phone'])) {
        $full_phone = $data['phone_code'] . ' ' . $data['phone'];
        $html .= '<div class="field">
            <span class="field-label">Phone / WhatsApp:</span>
            <span class="field-value">' . esc_html($full_phone) . '</span>
        </div>';
    }

    if (!empty($data['emergency_contact'])) {
        $full_emergency = $data['emergency_code'] . ' ' . $data['emergency_contact'];
        $html .= '<div class="field">
            <span class="field-label">Emergency Contact:</span>
            <span class="field-value">' . esc_html($full_emergency) . '</span>
        </div>';
    }

    if (!empty($data['country'])) {
        $html .= '<div class="field">
            <span class="field-label">Country:</span>
            <span class="field-value">' . esc_html($data['country']) . '</span>
        </div>';
    }

    if (!empty($data['passport'])) {
        $html .= '<div class="field">
            <span class="field-label">Passport Number:</span>
            <span class="field-value">' . esc_html($data['passport']) . '</span>
        </div>';
    }

    $html .= '</div>'; // End Contact Section

    // Travel Details Section
    $has_travel_details = !empty($data['package']) || !empty($data['travel_month']) ||
        !empty($data['travel_dates']) || !empty($data['travel_start']) ||
        !empty($data['group_size']) || !empty($data['room_type']);

    if ($has_travel_details) {
        $html .= '<div class="section">
            <div class="section-title">Travel Details</div>';

        if (!empty($data['package'])) {
            $html .= '<div class="field highlight">
                <span class="field-label">Preferred Package:</span>
                <span class="field-value"><strong>' . esc_html($data['package']) . '</strong></span>
            </div>';
        }

        if (!empty($data['travel_month'])) {
            $html .= '<div class="field">
                <span class="field-label">Travel Month:</span>
                <span class="field-value">' . esc_html($data['travel_month']) . '</span>
            </div>';
        }

        if (!empty($data['travel_dates'])) {
            $html .= '<div class="field">
                <span class="field-label">Travel Dates:</span>
                <span class="field-value">' . esc_html($data['travel_dates']) . '</span>
            </div>';
        }

        if (!empty($data['travel_start']) || !empty($data['travel_end'])) {
            $dates = $data['travel_start'];
            if (!empty($data['travel_end'])) {
                $dates .= ' → ' . $data['travel_end'];
            }
            $html .= '<div class="field">
                <span class="field-label">Travel Dates:</span>
                <span class="field-value">' . esc_html($dates) . '</span>
            </div>';
        }

        if (!empty($data['group_size'])) {
            $html .= '<div class="field">
                <span class="field-label">Number of Travellers:</span>
                <span class="field-value">' . esc_html($data['group_size']) . ' person(s)</span>
            </div>';
        }

        if (!empty($data['room_type'])) {
            $room_labels = array(
                'single' => 'Single Room',
                'triple' => 'Triple Room',
                'custom' => 'Mixed / Custom',
            );
            $room_display = isset($room_labels[$data['room_type']]) ? $room_labels[$data['room_type']] : 'Standard Twin / Double';
            $html .= '<div class="field">
                <span class="field-label">Room Preference:</span>
                <span class="field-value">' . esc_html($room_display) . '</span>
            </div>';
        }

        $html .= '</div>'; // End Travel Section
    }

    // Message Section
    $html .= '<div class="section">
        <div class="section-title">Message / Special Requests</div>
        <div class="message-box">' .
        (!empty($data['message']) ? nl2br(esc_html($data['message'])) : '<em>No message provided</em>') .
        '</div>
    </div>';

    // Footer
    $html .= '</div>
        <div class="footer">
            Sent from <a href="' . home_url() . '" style="color: #B8860B;">' . get_bloginfo('name') . '</a><br>
            ' . wp_date('F j, Y \a\t g:i a') . '
        </div>
    </body>
    </html>';

    return $html;
}

// Register AJAX handlers for both logged-in and logged-out users
add_action('wp_ajax_tk_submit_contact_form', 'tk_handle_contact_form');
add_action('wp_ajax_nopriv_tk_submit_contact_form', 'tk_handle_contact_form');

/**
 * Add rate limiting to prevent form abuse
 */
function tk_check_form_rate_limit()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'tk_form_limit_' . md5($ip);
    $submissions = get_transient($transient_key);

    // Limit to 50 submissions per hour (increased for testing, reduce to 5-10 in production)
    if ($submissions >= 50) {
        wp_send_json_error(array(
            'message' => __('Too many submissions. Please try again later.', 'trip-kailash')
        ));
        exit;
    }

    // Increment counter (expires in 1 hour)
    set_transient($transient_key, ($submissions ? $submissions + 1 : 1), HOUR_IN_SECONDS);
}
add_action('wp_ajax_tk_submit_contact_form', 'tk_check_form_rate_limit', 5);
add_action('wp_ajax_nopriv_tk_submit_contact_form', 'tk_check_form_rate_limit', 5);
