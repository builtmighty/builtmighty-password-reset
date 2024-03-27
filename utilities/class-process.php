<?php
/**
 * Process.
 * 
 * @since   1.0.0
 */
class builtpassProcess {

    /**
     * Process.
     * 
     * @since   1.0.0
     */
    public function process( $post, $data ) {

        

    }

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

        // Update the users password.
        wp_set_password( $post['password'], $post['user_id'] );

        // Clear the user cache.
        wp_cache_delete( $post['user_id'], 'users' );

        // Re-authenticate the user programmatically.
        wp_set_current_user( $post['user_id'] );
        wp_set_auth_cookie( $post['user_id'] );

        // Update user meta.
        update_user_meta( $post['user_id'], '_builtpass_reset', time() );

    }

}