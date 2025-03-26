<?php
/**
 * Process.
 * 
 * @since   1.0.0
 */
class builtpassProcess {

    /**
     * Run.
     * 
     * @since   1.0.0
     */
    public function run( $post, $data ) {

        // Check if set. 
        if( ! isset( $post['action'] ) || $post['action'] !== 'builtpass_reset_password' ) return;

        // Help.
        $help = new builtpassHelper();

        // Check nonce.
        $data = $this->check_nonce( $post, $data );

        // Sanitize.
        $post = $this->sanitize( $post );
        
        // Check match.
        $data = $this->check_match( $post, $data );

        // Check strength.
        $data = $this->check_strength( $post, $data );

        // Check for message.
        if( ! empty( $data['message'] ) ) {

            // Display message.
            echo '<div class="builtpass-error">' . $data['message'] . '</div>';

        } else {

            // Save password.
            $this->save_password( $post );

        }

        // Check for redirect.
        if( $data['redirect'] ) {

            // Redirect.
            wp_redirect( $help->redirect_url() );
            exit;

        }

    }

    /**
     * Reset.
     * 
     * @since   1.0.0
     */
    public function reset( $post, $data ) {

        // Check if set. 
        if( ! isset( $post['action'] ) || $post['action'] !== 'builtpass_reset_expired' ) return;

        // Check nonce.
        if( ! wp_verify_nonce( $post['nonce'], 'builtpass_reset_expired' ) ) {

            // Update.
            $data['message'] = 'There was an error resetting password.';

            // Redirect.
            $data['redirect'] = false;

        }

        // Sanitize.
        $post = $this->sanitize( $post );

        // Check email.
        if( ! $this->check_email( $post['email'], $post['user_id'] ) ) {

            // Update.
            $data['message'] = 'The email address is incorrect.';

            // Redirect.
            $data['redirect'] = false;

        }

        // Check for message.
        if( ! empty( $data['message'] ) ) {

            // Display message.
            echo '<div class="builtpass-error">' . $data['message'] . '</div>';

        } else {

            // Get user.
            $user = get_user_by( 'id', $post['user_id'] );

            // Mail.
            $mail = new builtpassMail();
            $mail->password_reset_email( $user );

            // Redirect.
            wp_redirect( site_url( '/?password-reset-required=true&user=' . $user->ID ) );
            exit;

        }

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
     * Sanitize.
     * 
     * @since   1.0.0
     */
    public function sanitize( $post ) {

        // Sanitize post.
        foreach( $post as $key => $value ) {

            // Sanitize.
            $post[$key] = sanitize_text_field( $value );

        }

        // Return post.
        return $post;

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
     * Check email.
     * 
     * @since   1.0.0
     */
    public function check_email( $email, $user_id ) {

        // Get user.
        $user = get_user_by( 'id', $user_id );

        // Check if email matches user email.
        if( $email != $user->user_email ) {

            // Return false.
            return false;

        }

        // Return true.
        return true;
        
    }

    /**
     * Save password.
     * 
     * @since   1.0.0
     */
    public function save_password( $post ) {

        // Keys.
        $keys = new builtpassKeys();

        // Update user meta.
        update_user_meta( $post['user_id'], '_builtpass_reset', time() );
        update_user_meta( $post['user_id'], '_builtpass_bulk_reset', time() );

        // Expire key.
        $keys->expire( $post['user_id'] );

        // Update the users password if the action has not been triggered by the WooCommerce reset password form.
        if ( ! did_action('woocommerce_customer_reset_password') )
            wp_set_password( $post['password'], $post['user_id'] );

        // Clear the user cache.
        wp_cache_delete( $post['user_id'], 'users' );

        // Re-authenticate the user programmatically.
        wp_set_current_user( $post['user_id'] );
        wp_set_auth_cookie( $post['user_id'] );

    }

}