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

// Get users email.
$user = get_user_by( 'id', $_GET['user'] );

// Check if user exists.
if( ! $user ) {
    
    // Redirect.
    wp_redirect( home_url() );
    exit;

}

// Header.
get_header();

// Wrapper.
do_action( 'builtpass_notice_start' );

// Output content.
if( ! empty( get_option( 'builtpass_bulk_page' ) ) ) {

    // Output. ?>
    <div class="builtpass-notice-content">
        <?php echo wpautop( get_option( 'builtpass_bulk_page' ) ); ?>
    </div><?php 

}

// Reset message. ?>
<div class="builtpass-notice-info">
    <p>A password reset email has been sent.</p>
</div><?php

// Output.
do_action( 'builtpass_reset_notice', $_GET['user'] );

// Wrapper.
do_action( 'builtpass_notice_end' );

// Footer.
get_footer();