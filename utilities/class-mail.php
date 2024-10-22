<?php
/**
 * Mail.
 * 
 * @since   1.0.0
 * @author  Built Mighty
 */
class builtpassMail {
    
    /**
     * Parameters.
     */
    public $mail;
    public $headers;

    /**
     * Construct.
     */
    public function __construct() {

        // Get mailer.
        $this->headers = [ 'Content-Type: text/html; charset=UTF-8' ];

    }

    /**
     * Send.
     */
    public function send( $email, $subject, $heading, $message, $attachment = NULL ) {

        // Get woocommerce mailer from instance
        $mailer = WC()->mailer();

        // Wrap message using woocommerce html email template
        $wrapped_message = $mailer->wrap_message($heading, $message);

        // Create new WC_Email instance
        $wc_email = new WC_Email;

        // Style the wrapped message with woocommerce inline styles
        $html_message = $wc_email->style_inline( $wrapped_message );

        // Send the email using wordpress mail function
        wp_mail( $email, $subject, $html_message, $this->headers, $attachment );

    }

    /**
     * Compose password reset email.
     * 
     * @since   1.0.0
     */
    public function password_reset_email( $user ) {

        // Classes.
        $mail = new builtpassMail();
        $keys = new builtpassKeys();

        // Create a key.
        $key = $keys->create_user_key( $user->ID );

        // Set email.
        $email      = $user->user_email;
        $subject    = get_bloginfo( 'name' ) . ' | Password Reset Required';
        $heading    = 'Reset password.';
        $body       = "Hello, " . $user->display_name . ",\n\n";

        // Get email content.
        $body       .= ( ! empty( get_option( 'builtpass_bulk_email' ) ) ) ? get_option( 'builtpass_bulk_email' ) : '';
        $body       .= "In order to keep your account safe, you'll need to reset your password. Please reset your password by clicking the link below.\n\n";
        $body       .= "<a href=\"" . site_url( '/?reset-password=true&key=' . $key . '&user=' . $user->ID ) . "\">Reset Password</a>";
        $body       .= "\n\nThank you!\n\nSincerely,\n" . get_bloginfo( 'name' ) . "\n\n";

        // Filters.
        $email      = apply_filters( 'builtpass_password_reset_email', $email, $user->ID );
        $subject    = apply_filters( 'builtpass_password_reset_subject', $subject, $user->ID );
        $heading    = apply_filters( 'builtpass_password_reset_heading', $heading, $user->ID );
        $body       = apply_filters( 'builtpass_password_reset_body', $body, $user->ID );

        // Compose.
        $mail->send( $email, $subject, $heading, $body );

    }

}