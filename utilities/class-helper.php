<?php
/**
 * Helper.
 * 
 * @since   1.0.0
 * @author  Built Mighty
 */
class builtpassHelper {

    /**
     * Mask email address.
     * 
     * Mask an email address for security purposes.
     * 
     * @since   1.0.0
     */
    public function mask_email( $email ) {

        // Split the email.
        list( $local, $domain ) = explode( '@', $email );

        // Apply mask to local.
        $local = $this->mask( $local );

        // Split domain.
        $domain = explode( '.', $domain );

        // Apply mask to domain.
        $domain = $this->mask( $domain[0] ) . '.' . $domain[1];
        
        // Return.
        return implode( '@', [ $local, $domain ] );

    }

    /**
     * Mask a string.
     * 
     * Obfuscate part of a string with asterisks, for security purposes.
     * 
     * @since   1.0.0
     */
    public function mask( $string, $visible = 3 ) {

        // Get string length.
        $length = strlen( $string );

        // Check length vs visible.
        if( $length <= $visible ) return $string;

        // Calculate how many characters to mask.
        $count = $length - $visible;

        // Mask.
        $string = substr( $string, 0, $visible ) . str_repeat( '*', $count );

        // Return.
        return $string;

    }

    /**
     * Redirect URL.
     * 
     * Redirect to a URL.
     */
    public function redirect_url() {

        // Check if WooCommerce is active.
        return ( class_exists( 'WooCommerce' ) ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : home_url();

    }

}
