<?php
/**
 * Password reset form.
 * 
 * @since   1.0.0
 */

// Classes.
$helper     = new builtpassHelper();
$process    = new builtpassProcess();

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

// Process.
$process->reset( $_POST, [ 'message' => '', 'redirect' => true ] );

// Header.
get_header();

// Before expired.
do_action( 'builtpass_before_expired', $user->ID );

// Reset message. ?>
<div class="builtpass-expired-info">
    <p>It appears your reset link has expired. Request a new one and have it sent to <?php echo $helper->mask_email( $user->user_email ); ?>?</p>
</div>
<form id="built-password-reset-expired" method="post">
    <input type="hidden" name="action" value="builtpass_reset_expired">
    <input type="hidden" name="user_id" value="<?php echo $_GET['user']; ?>">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'builtpass_reset_expired' ); ?>">
    <label for="email">Confirm Email Address</label>
    <input type="email" name="email" placeholder="Email">
    <button type="submit">Send Request</button>
</form><?php

// After expired.
do_action( 'builtpass_after_expired', $user->ID );

// Footer.
get_footer();