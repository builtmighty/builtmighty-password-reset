<?php
/**
 * Keys.
 * 
 * @since   1.0.0
 * @author  Built Mighty
 */
class builtpassKeys {

    /**
     * Create user key.
     * 
     * @param int $user_id - The user ID.
     * 
     * @return string - The key.
     * 
     * @since   1.0.0
     * 
     * @modified 1.3.0 - Added timestamp.
     */
    public function create_user_key( $user_id ) {

        // Create the key.
        $key = $this->create_key( $user_id );

        // Save the key.
        update_user_meta( $user_id, '_builtpass_key', $key );
        update_user_meta( $user_id, '_builtpass_key_timestamp', time() );

        // Return.
        return $key;

    }
    
    /**
     * Create key.
     * 
     * @param int $user_id - The user ID.
     * 
     * @return string - The key.
     * 
     * @since   1.0.0
     * 
     * @modified 1.3.0 - use get_password_reset_key.
     */
    public function create_key( $user_id ) {

        // Create key.
        // $key = base64_encode( wp_generate_password( 32, false ) . ':' . time() );

        // Get the user object.
        $user = get_user_by( 'id', $user_id );

        // Generate the WordPress reset key.
        $key = get_password_reset_key( $user );

        // Return key.
        return $key;

    }

    /**
     * Get key.
     * 
     * @param int $user_id - The user ID.
     * 
     * @return string - The key.
     * 
     * @since   1.0.0
     */
    public function get_key( $user_id ) {

        // Check for key.
        if( empty( get_user_meta( $user_id, '_builtpass_key', true ) ) ) return false;

        // Return key.
        return get_user_meta( $user_id, '_builtpass_key', true );

    }

    /**
     * Get login.
     * 
     * @param  int $user_id - The user ID.
     * 
     * @return string - The user login.
     * 
     * @since   1.3.0
     */
    public function get_login( $user_id ) {
        $userdata = get_userdata( absint( $user_id ) );
        $login    = $userdata ? $userdata->user_login : '';

        // Return login.
        return $login;
    }

    /**
     * Check password reset key.
     * 
     * @param  string $key - The key to check.
     * 
     * @param  int $user_id - The user ID.
     * 
     * @since   1.3.0
     */
    public function check_password_reset_key( $key, $user_id ) {
        $login = $this->get_login( $user_id );

        return check_password_reset_key( $key, $login );
    }

    /**
     * Validate key.
     * 
     * @param  string $post_key - The key from the post.
     * @param  int $user_id - The user ID.
     * 
     * @return bool - Whether the key is valid.
     * 
     * @since   1.0.0
     * 
     * @modified 1.3.0 - Added timestamp and use check_password_reset_key.
     */
    public function validate( $post_key, $user_id ) {

        // Get key timestamp.
        $timestamp  = get_user_meta( $user_id, '_builtpass_key_timestamp', true );

        // Check if key expired within 24 hours.
        if( $timestamp < strtotime( '-1 day' ) ) {

            // Remove key.
            $this->expire( $user_id );
            return false;

        }

        // Get user key.
        // $user_key = $this->decrypt( $this->get_key( $user_id ) );
        $user_key = $this->get_key( $user_id );

        // Ensure Password Reset Key is valid
        if( ! $this->check_password_reset_key( $post_key, $user_id ) ) return false;

        // Check if key expired within 24 hours.
        if( $timestamp < strtotime( '-1 day' ) ) {

            // Remove key.
            $this->expire( $user_id );
            return false;

        }

        // Compare keys.
        if( $user_key !== $post_key ) return false;

        // Keys are valid.
        return true;

    }

    /**
     * Decrypt key.
     * 
     * @param  string $key - The key to decrypt.
     * 
     * @return array - The decrypted key.
     * 
     * @since   1.0.0
     */
    public function decrypt( $key ) {

        // Return.
        return explode( ':', base64_decode( $key ) );        

    }

    /**
     * Expire key.
     * 
     * @param  int $user_id - The user ID.
     * 
     * @return void
     * 
     * @since   1.0.0
     */
    public function expire( $user_id ) {

        // Delete key from user.
        delete_user_meta( $user_id, '_builtpass_key' );

        // Delete key timestamp.
        delete_user_meta( $user_id, '_builtpass_key_timestamp' );

    }

}