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
     * Redirect, until done.
     * 
     * Redirect to password reset page until password is reset.
     * 
     * @since   1.0.0
     */
    public function redirect_reset_pass() {

        // Check if user is logged in.
        if( ! is_user_logged_in() ) return;
 
        // Check if user has reset password.
        if( ! $this->reset_needed( get_current_user_id() ) ) return;

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

        } elseif( is_user_logged_in() && isset( $_GET['reset-password-clear'] ) ) {

            // Clear user meta for testing.
            delete_user_meta( get_current_user_id(), '_builtpass_reset' );

        }

        // Return template.
        return $template;

    }

    /**
     * Check for reset.
     * 
     * Check if password reset is requested.
     * 
     * @since   1.0.0
     */
    public function reset_needed( $user_id ) {

        // Check type.
        if( is_null( get_option( 'builtpass_reset_password' ) ) || empty( get_option( 'builtpass_reset_password' ) ) ) return false;

        // Get type.
        $type = str_replace( 'require_', '', get_option( 'builtpass_reset_password' ) );

        // Check if once and check if reset is needed.
        if( $type == 'once' && empty( get_user_meta( $user_id, '_builtpass_reset', true ) ) ) return true;

        // Check if within days.
        if( strtotime( get_user_meta( $user_id, '_builtpass_reset', true ) ) > strtotime( '-' . (float)$days . ' days' ) ) return true;

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

}