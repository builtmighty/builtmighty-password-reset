<?php
/**
 * Process.
 * 
 * @since   1.0.0
 */
class builtpassProcess {

    /**
     * Check nonce.
     * 
     * Check if nonce is valid.
     * 
     * @since   1.0.0
     */
    public function check_nonce( $post, $data ) {

        // Check nonce.
        if( ! wp_verify_nonce( $post['nonce'], 'builtpass_reset_password' ) ) {

            // Update.
            $data['message'] = 'There was an error resetting password.';

            // Redirect.
            $data['redirect'] = false;

        }

        // Return data.
        return $data;

    }

    /**
     * Check that passwords match.
     * 
     * @since   1.0.0
     */
    public function check_match( $post, $data ) {

        // Check that passwords match.
        if( $post['password'] != $post['confirm_password'] ) {

            // Update.
            $data['message'] = 'The passwords do not match.';

            // Redirect.
            $data['redirect'] = false;

        }

        // Return data.
        return $data;

    }

    /**
     * Check password strength.
     * 
     * @since   1.0.0
     */
    public function check_strength( $post, $data ) {

        // Check length.
        if( strlen( $post['password'] ) < 8 ) {

            // Update.
            $data['message'] = 'Password must be at least 8 characters long.';

            // Redirect.
            $data['redirect'] = false;

            // Return.
            return $data;

        }

        // Check for both lower and upper case letters.
        if( ! preg_match( '/[a-z]/', $post['password'] ) || ! preg_match( '/[A-Z]/', $post['password'] ) ) {

            // Update.
            $data['message'] = 'Password must contain both lower and upper case letters.';

            // Redirect.
            $data['redirect'] = false;

            // Return.
            return $data;

        }

        // Check for numbers.
        if( ! preg_match( '/[0-9]/', $post['password'] ) ) {

            // Update.
            $data['message'] = 'Password must contain at least one number.';

            // Redirect.
            $data['redirect'] = false;

            // Return.
            return $data;

        }

        // Check for special characters.
        if( ! preg_match( '/\W/', $post['password'] ) ) {

            // Update.
            $data['message'] = 'Password must contain at least one special character.';

            // Redirect.
            $data['redirect'] = false;

            // Return.
            return $data;

        }

        // Return data.
        return $data;

    }

    /**
     * Save password.
     * 
     * @since   1.0.0
     */
    public function save_password( $post ) {

        // Global.
        global $wpdb;

        // Hash password.
        $hash = wp_hash_password( $post['password'] );

        // Set data.
        error_log( 'Password: ' . print_r( $hash, true ) );
        error_log( 'User: ' . print_r( $post['user_id'], true ) );

        // Update user password.
        $status = $wpdb->update( 
            $wpdb->users,
            [ 'user_pass' => $hash ], 
            [ 'ID'        => $post['user_id'] ] 
        );

        // Get hashed user pass.
        $user_pass = $wpdb->get_var( "SELECT user_pass FROM $wpdb->users WHERE ID = " . $post['user_id'] );

        error_log( 'User Pass: ' . print_r( $user_pass, true ) );

        // Update user meta.
        update_user_meta( $post['user_id'], '_builtpass_reset', date( 'Y-m-d-H-i-s' ) );

    }

}