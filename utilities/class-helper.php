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

    /**
     * Convert interval to datetime.
     * 
     * @param string $interval - The interval to convert.
     * 
     * @return int - The datetime.
     * 
     * @since   1.1.0
     */
    public static function convert_interval_to_datetime( $interval ) {
        $current_time = time();

        // Define the regex patterns for different units.
        $patterns = array(
            'months' => '/(\d+)\s*months?/',
            'years'  => '/(\d+)\s*years?/',
            'days'   => '/(\d+)\s*days?/',
        );

        // Check and convert each unit.
        foreach ( $patterns as $unit => $pattern ) :
            if ( preg_match( $pattern, str_replace('_', ' ', $interval), $matches ) ) :
                $value          = (int) $matches[1];
                $converted_time = strtotime( "$value $unit", $current_time );
            endif;
        endforeach;

        return $converted_time;
    }


}
