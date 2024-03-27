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
     * @since   1.0.0
     */
    public function create_user_key( $user_id ) {

        // Create the key.
        $key = $this->create_key();

        // Save the key.
        update_user_meta( $user_id, '_builtpass_key', $key );

        // Return.
        return $key;

    }
    
    /**
     * Create key.
     * 
     * @since   1.0.0
     */
    public function create_key() {

        // Create key.
        $key = base64_encode( wp_generate_password( 32, false ) . ':' . time() );

        // Return key.
        return $key;

    }

    /**
     * Get key.
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
     * Validate key.
     * 
     * @since   1.0.0
     */
    public function validate( $key, $user_id ) {

        // Decrypt key.
        $post_key = $this->decrypt( $key );

        // Check if key expired within 24 hours.
        if( $post_key[1] < strtotime( '-1 day' ) ) {

            // Remove key.
            $this->expire( $user_id );
            return false;

        }
        
        // Get user key.
        $user_key = $this->decrypt( $this->get_key( $user_id ) );

        // Compare keys.
        if( $user_key[0] !== $post_key[0] ) return false;

        // Keys are valid.
        return true;

    }

    /**
     * Decrypt key.
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
     * @since   1.0.0
     */
    public function expire( $user_id ) {

        // Delete key from user.
        delete_user_meta( $user_id, '_builtpass_key' );

    }

}