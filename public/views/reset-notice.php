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

// Wrapper start.
do_action( 'builtpass_notice_start' );

// Helper.
$helper = new builtpassHelper();

// Reset message. ?>
<div class="builtpass-notice-info">
    <p>Please check your email. A password reset link has been sent to <?php echo $helper->mask_email( $user->user_email ); ?>.</p>
</div><?php

// Output content.
if( ! empty( get_option( 'builtpass_bulk_page' ) ) ) {

    // Output. ?>
    <div class="builtpass-notice-content">
        <?php echo wpautop( get_option( 'builtpass_bulk_page' ) ); ?>
    </div><?php 

}

// Output custom notice.
do_action( 'builtpass_reset_notice', $_GET['user'] );

// Wrapper end.
do_action( 'builtpass_notice_end' );

// Footer.
get_footer();