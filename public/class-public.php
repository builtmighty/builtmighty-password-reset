<?php
/**
 * Public.
 * 
 * @since   1.0.0
 */
class builtpassPublic {

    /**
     * Plugin name.
     */
    private $plugin_name;

    /**
     * Plugin version.
     */
    private $plugin_version;

    /**
     * Construct.
     * 
     * @since   1.0.0
     * @param   string      $plugin_name        The name of the plugin.
     * @param   string      $plugin_version     The version of the plugin.
     */
    public function __construct( $plugin_name, $version ) {

        // Set plugin name.
        $this->plugin_name = $plugin_name;

        // Set plugin version.
        $this->plugin_version = $version;

    }

    /**
     * On user creation.
     * 
     * Set the required key on user creation, so they aren't prompted to reset.
     * 
     * @since   1.0.0
     */
    public function set_key( $user_id ) {

        // Set.
        update_user_meta( $user_id, '_builtpass_bulk_reset', time() );
        update_user_meta( $user_id, '_builtpass_reset', time() );

    }

    /**
     * On login, check if bulk reset.
     * 
     * @since   1.0.0
     */
    public function login_reset( $user_login, $user ) {

        // Check if user has reset password.
        if( ! $this->bulk_reset_needed( $user ) ) return;

        // Logout the user.
        wp_logout();

        // Redirect.
        wp_redirect( site_url( '/?password-reset-required=true&user=' . $user->ID ) );

        // Send password reset email.
        $this->password_reset_email( $user );

        exit;

    }

    /**
     * Redirect, until done, for timed reset.
     * 
     * Redirect to password reset page until password is reset.
     * 
     * @since   1.0.0
     */
    public function redirect_timed_reset() {

        // Check if user is logged in.
        if( ! is_user_logged_in() ) return;

        // Get user.
        $user = wp_get_current_user();
 
        // Check if user has reset password.
        if( ! $this->timed_reset_needed( $user ) ) return;

        // Check if URL is password reset page.
        if( isset( $_GET['reset-password'] ) ) return;

        // Check if logging out.
        if( isset( $_GET['action'] ) && $_GET['action'] == 'logout' ) return;

        // Redirect to password reset page.
        wp_redirect( home_url( '/?reset-password=true' ) );
        exit;
        
    }

    /**
     * Reset password template.
     * 
     * Load the password reset template.
     * 
     * @since   1.0.0
     */
    public function reset_password_template( $template ) {

        // Check if password reset is requested.
        if( is_user_logged_in() && isset( $_GET['reset-password'] ) ) {

            // Load password reset template.
            $template = BUILTPASS_PATH . 'public/views/reset-password.php';

        } elseif( ! is_user_logged_in() && isset( $_GET['reset-password'] ) && isset( $_GET['key'] ) ) {

            // Load password reset template.
            $template = BUILTPASS_PATH . 'public/views/reset-password-link.php';

        } elseif( ! is_user_logged_in() && isset( $_GET['password-reset-required'] ) ) {

            // Load password reset notice template.
            $template = BUILTPASS_PATH . 'public/views/reset-password-notice.php';

        } elseif( is_user_logged_in() && isset( $_GET['reset-password-clear'] ) ) {

            // Set user ID.
            $user_id = ( is_user_logged_in() ) ? get_current_user_id() : $_GET['user'];

            // Clear user meta for testing.
            delete_user_meta( $user_id, '_builtpass_reset' );
            delete_user_meta( $user_id, '_builtpass_bulk_reset' );

        }

        // Return template.
        return $template;

    }

    /** 
     * Check for bulk reset.
     * 
     * Check if password reset is requested.
     * 
     * @since   1.0.0
     */
    public function bulk_reset_needed( $user ) {

        // Check if enabled.
        if( is_null( get_option( 'builtpass_bulk_reset' ) ) || empty( get_option( 'builtpass_bulk_reset' ) ) ) return false;

        // Get valid roles.
        $valid_roles = array_keys( get_option( 'builtpass_bulk_reset' ) );

        // Check if user has a valid reset role.
        if( ! array_intersect( (array)$user->roles, (array)$valid_roles ) ) return false;

        // Check for valid meta.
        if( empty( get_user_meta( $user->ID, '_builtpass_bulk_reset', true ) ) ) return true;

        // Set matching role time.
        $role   = array_intersect( (array)$user->roles, (array)$valid_roles );
        $times  = (array)get_option( 'builtpass_bulk_reset' );
        $time   = get_user_meta( $user->ID, '_builtpass_bulk_reset', true );

        // Check if time is valid.
        if( $time < $times[$role[0]] ) return true;

        // Return false.
        return false;

    }

    /**
     * Check for timed reset.
     * 
     * Check if password reset is requested.
     * 
     * @since   1.0.0
     */
    public function timed_reset_needed( $user ) {

        // Check if enabled.
        if( is_null( get_option( 'builtpass_timed_reset' ) ) || empty( get_option( 'builtpass_timed_reset' ) ) ) return false;

        // Check if disabled.
        if( get_option( 'builtpass_timed_reset' ) == 'disabled' ) return false;

        // Check if a role is set.
        if( is_null( get_option( 'builtpass_timed_roles' ) ) || empty( get_option( 'builtpass_timed_roles' ) ) ) return false;

        // Check if user has a valid reset role.
        if( ! array_intersect( (array)$user->roles, (array)get_option( 'builtpass_timed_roles' ) ) ) return false;

        // Check if user meta is empty.
        if( empty( get_user_meta( $user->ID, '_builtpass_reset', true ) ) ) return true;

        // Get type.
        $type = str_replace( 'require_', '', get_option( 'builtpass_timed_reset' ) );

        // Get user meta date.
        $date = strtotime( '+' . $type . ' days', get_user_meta( $user->ID, '_builtpass_reset', true ) );

        // Check if within days.
        if( $date <= time() ) return true;

        // Return false.
        return false;

    }

    /**
     * Clear user cache on logout.
     * 
     * @since   1.0.0
     */
    public function logout( $user_id ) {

        // Clear user cache.
        wp_cache_delete( $user_id, 'users' );

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

        // Compose.
        $mail->send( $email, $subject, $heading, $body );

    }

}