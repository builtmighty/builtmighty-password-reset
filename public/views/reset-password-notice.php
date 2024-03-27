<?php
/**
 * Password reset form.
 * 
 * @since   1.0.0
 */

// Check if user is set 
if( ! isset( $_GET['user'] ) ) {
    
    // Redirect.
    wp_redirect( home_url() );
    exit;

}

// Header.
get_header();

// Output.
do_action( 'builtpass_reset_notice', $_GET['user'] );

// Footer.
get_footer();